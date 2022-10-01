<?php

namespace App\Utils;

use App\CashRegister;
use App\Cashier;;
use App\CashRegisterTransaction;
use App\Optics\InflowOutflow;
use App\Transaction;
use App\TransactionPayment;
use DB;

class CashRegisterUtil extends Util
{
    /**
     * Returns number of opened Cash Registers for the
     * current logged in user
     *
     * @return int
     */
    public function countOpenedRegister()
    {
        $user_id = auth()->user()->id;

        $permitted_cashiers = Cashier::permittedCashiers();

        if ($permitted_cashiers != 'all') {
            $count =  CashRegister::whereIn('cashier_id', $permitted_cashiers)
                ->where('user_id', $user_id)
                ->where('status', 'open')
                ->count();

        } else {
            $count = CashRegister::where('status', 'open')
                ->where('user_id', $user_id)
                ->count();
        }

        return $count;
    }

    /**
     * Adds sell payments to currently opened cash register
     *
     * @param object/int $transaction
     * @param array $payments
     *
     * @return boolean
     */
    public function addSellPayments($transaction, $payments)
    {
        $user_id = auth()->user()->id;
        $register =  CashRegister::where('cashier_id', $transaction->cashier_id)
                    ->where('status', 'open')
                    ->first();

        $payments_formatted = [];
        foreach ($payments as $payment) {
            $payments_formatted[] = new CashRegisterTransaction([
                    'amount' => (isset($payment['is_return']) && $payment['is_return'] == 1) ? (-1*$this->num_uf($payment['amount'])) : $this->num_uf($payment['amount']),
                    'pay_method' => $payment['method'],
                    'type' => 'credit',
                    'transaction_type' => 'sell',
                    'transaction_id' => $transaction->id
                ]);
        }

        if (!empty($payments_formatted)) {
            $register->cash_register_transactions()->saveMany($payments_formatted);
        }

        return true;
    }

    /**
     * Adds credit sell payments to currently opened cash register
     *
     * @param object/int $transaction
     *
     * @return boolean
     */
    public function addCreditSellPayment($transaction, $total_paid, $final_amount) {
        $amount = $final_amount - $total_paid;

        $payment[] = array(
            'is_return' => 0,
            'amount' => $amount,
            'method' => 'credit'
        );
        return $this->addSellPayments($transaction, $payment);
    }

    /**
     * Adds sell payments to currently opened cash register
     *
     * @param object/int $transaction
     * @param array $payments
     *
     * @return boolean
     */
    public function updateSellPayments($status_before, $transaction, $payments)
    {
        $user_id = auth()->user()->id;
        $register =  CashRegister::where('user_id', $user_id)
                                ->where('status', 'open')
                                ->first();
        //If draft -> final then add all
         //If final -> draft then refund all
         //If final -> final then update payments
        if ($status_before == 'draft' && $transaction->status == 'final') {
            $this->addSellPayments($transaction, $payments);
        } else if ($status_before == 'final' && $transaction->status == 'draft') {
            $this->refundSell($transaction);
        } else if ($status_before == 'final' && $transaction->status == 'final') {
            $prev_payments = CashRegisterTransaction::where('transaction_id', $transaction->id)
                            ->select(
                                DB::raw("SUM(IF(pay_method='cash', IF(type='credit', amount, -1 * amount), 0)) as total_cash"),
                                DB::raw("SUM(IF(pay_method='card', IF(type='credit', amount, -1 * amount), 0)) as total_card"),
                                DB::raw("SUM(IF(pay_method='check', IF(type='credit', amount, -1 * amount), 0)) as total_check"),
                                DB::raw("SUM(IF(pay_method='bank_transfer', IF(type='credit', amount, -1 * amount), 0)) as total_bank_transfer")
                            )->first();
            if (!empty($prev_payments)) {
                $payment_diffs = [
                    'cash' => $prev_payments->total_cash,
                    'card' => $prev_payments->total_card,
                    'check' => $prev_payments->total_check,
                    'bank_transfer' => $prev_payments->total_bank_transfer
                ];

                foreach ($payments as $payment) {
                    if (isset($payment['is_return']) && $payment['is_return'] == 1) {
                        $payment_diffs[$payment['method']] += $this->num_uf($payment['amount']);
                    } else {
                        $payment_diffs[$payment['method']] -= $this->num_uf($payment['amount']);
                    }
                }
                $payments_formatted = [];
                foreach ($payment_diffs as $key => $value) {
                    if ($value > 0) {
                        $payments_formatted[] = new CashRegisterTransaction([
                            'amount' => $value,
                            'pay_method' => $key,
                            'type' => 'debit',
                            'transaction_type' => 'refund',
                            'transaction_id' => $transaction->id
                        ]);
                    } else if ($value < 0) {
                        $payments_formatted[] = new CashRegisterTransaction([
                            'amount' => -1 * $value,
                            'pay_method' => $key,
                            'type' => 'credit',
                            'transaction_type' => 'sell',
                            'transaction_id' => $transaction->id
                        ]);
                    }
                }
                if (!empty($payments_formatted)) {
                    $register->cash_register_transactions()->saveMany($payments_formatted);
                }
            }
        }

        return true;
    }

    /**
     * Refunds all payments of a sell
     *
     * @param object/int $transaction
     *
     * @return boolean
     */
    public function refundSell($transaction)
    {
        /*$user_id = auth()->user()->id;
        $register =  CashRegister::where('user_id', $user_id)
                                ->where('status', 'open')
                                ->first();*/

        $total_payment = CashRegisterTransaction::where('transaction_id', $transaction->id)
                            ->select(
                                'cash_register_id',
                                DB::raw("SUM(IF(pay_method='cash', IF(type='credit', amount, -1 * amount), 0)) as total_cash"),
                                DB::raw("SUM(IF(pay_method='credit', IF(type='credit', amount, -1 * amount), 0)) as total_credit"),
                                DB::raw("SUM(IF(pay_method='card', IF(type='credit', amount, -1 * amount), 0)) as total_card"),
                                DB::raw("SUM(IF(pay_method='check', IF(type='credit', amount, -1 * amount), 0)) as total_check"),
                                DB::raw("SUM(IF(pay_method='bank_transfer', IF(type='credit', amount, -1 * amount), 0)) as total_bank_transfer")
                            )->first();
        $refunds = [
                    'cash' => $total_payment->total_cash,
                    'credit' => $total_payment->total_credit,
                    'card' => $total_payment->total_card,
                    'check' => $total_payment->total_check,
                    'bank_transfer' => $total_payment->total_bank_transfer
                ];
        $refund_formatted = [];
        foreach ($refunds as $key => $val) {
            if ($val > 0) {
                $refund_formatted[] = new CashRegisterTransaction([
                    'amount' => $val,
                    'pay_method' => $key,
                    'type' => 'debit',
                    'transaction_type' => 'refund',
                    'transaction_id' => $transaction->id
                ]);
            }
        }

        $user = CashRegister::where("id", $total_payment->cash_register_id)
            ->first();

        $register =  CashRegister::where('cashier_id', $transaction->cashier_id)
                            ->where('status', 'open')
                            ->first();

        if (!empty($refund_formatted)) {
            $register->cash_register_transactions()->saveMany($refund_formatted);
        }
        return true;
    }

    /**
     * Retrieves details of given rigister id else currently opened register.
     *
     * @param  int  $cashier_id
     * @param  string  $start_date
     * @param  string  $end_date
     * @param  string  $close_date
     * @return object
     */
    public function getRegisterDetails($cashier_id, $start_date, $end_date, $close_date)
    {
        $query = CashRegisterTransaction::join('transactions as t', 'cash_register_transactions.transaction_id', 't.id')
            ->join('cash_registers as cr', 'cr.id', 'cash_register_transactions.cash_register_id')
            ->whereBetween('t.transaction_date', [$start_date, $end_date])
            ->where('t.cashier_id', $cashier_id);

        $is_closed = CashRegister::where('cashier_id', $cashier_id)
            ->where('business_id', request()->session()->get('user.business_id'))
            ->where('date', $close_date)
            ->where('status', 'close')
            ->count();

        $today = \Carbon::now()->format('Y-m-d');

        if ($close_date != $today && $is_closed > 0) {
            $query = $query->whereRaw("IF(cr.status = 'close', IF(cr.date = ?, 1, 0), 0) = 1", [$close_date]);
        }
            
        $query = $query->select('cash_register_transactions.*');
                              
        $register_details = $query->select(
            DB::raw("SUM(IF(transaction_type='sell', amount, IF(transaction_type='refund', -1 * amount, 0))) as total_sale"),
            DB::raw("SUM(IF(pay_method='cash', IF(transaction_type='sell', amount, 0), 0)) as total_cash"),
            DB::raw("SUM(IF(pay_method='check', IF(transaction_type='sell', amount, 0), 0)) as total_check"),
            DB::raw("SUM(IF(pay_method='card', IF(transaction_type='sell', amount, 0), 0)) as total_card"),
            DB::raw("SUM(IF(pay_method='bank_transfer', IF(transaction_type='sell', amount, 0), 0)) as total_bank_transfer"),
            DB::raw("SUM(IF(pay_method='credit', IF(transaction_type='sell', amount, 0), 0)) as total_credit"),
            DB::raw("(SELECT SUM(crt.amount) FROM cash_register_transactions AS crt JOIN cash_registers AS cr ON crt.cash_register_id = cr.id JOIN transactions AS t ON crt.transaction_id = t.id WHERE DATE(t.transaction_date) = '$close_date' AND cr.cashier_id = $cashier_id) AS total_sell"),
            DB::raw("SUM(IF(transaction_type='sell' OR transaction_type='refund', amount, 0)) as total_sale_refund"),

            DB::raw("SUM(IF(transaction_type='refund', amount, 0)) as total_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='cash', amount, 0), 0)) as total_cash_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='check', amount, 0), 0)) as total_check_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='card', amount, 0), 0)) as total_card_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='bank_transfer', amount, 0), 0)) as total_bank_transfer_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='credit', amount, 0), 0)) as total_credit_refund")
        )->first();

        if ($close_date != $today && $is_closed > 0) {
            $register_details->total_sale_refund = 0;
        }

        return $register_details;
    }

    /**
     * Get the transaction details for a particular register
     *
     * @param $user_id int
     * @param $open_time datetime
     * @param $close_time datetime
     *
     * @return array
     */
    public function getRegisterTransactionDetails($user_id, $open_time, $close_time)
    {
        $product_details = Transaction::whereBetween('transaction_date', [$open_time, $close_time])
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->join('transaction_sell_lines AS TSL', 'transactions.id', '=', 'TSL.transaction_id')
            ->join('products AS P', 'TSL.product_id', '=', 'P.id')
            ->leftjoin('brands AS B', 'P.brand_id', '=', 'B.id')
            ->groupBy('B.id')
            ->select(
                'B.name as brand_name',
                DB::raw('SUM(TSL.quantity) as total_quantity'),
                DB::raw('SUM(TSL.unit_price_inc_tax) as total_amount')
            )
            ->orderByRaw('CASE WHEN brand_name IS NULL THEN 2 ELSE 1 END, brand_name')
            ->get();

        $transaction_details = Transaction::whereBetween('transaction_date', [$open_time, $close_time])
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->select(
                    DB::raw('SUM(tax_amount) as total_tax'),
                    DB::raw('SUM(IF(discount_type = "percentage", total_before_tax*discount_amount/100, discount_amount)) as total_discount')
                )
                ->first();

        return ['product_details' => $product_details,
                'transaction_details' => $transaction_details
            ];
    }

    /**
     * Retrieves details of given rigister id else currently opened register
     *
     * @param  int  $cashier_id
     * @param  string  $start_date
     * @param  string  $end_date
     * @return object
     */
    public function getRegisterDetailsWithPayments($cashier_id, $start_date, $end_date)
    {
        $payment_details = TransactionPayment::join('transactions as t', 't.id', 'transaction_payments.transaction_id')
            // ->join('user as u', 'u.id', 'cash_registers.user_id')
            ->whereBetween('transaction_payments.paid_on', [$start_date, $end_date])
            // ->where('transaction_payments.created_by', $user_id)
            ->where('t.cashier_id', $cashier_id)
            ->whereNotBetween('t.transaction_date', [$start_date, $end_date])
            ->whereNull('quote_id')
            ->select(
                DB::raw("SUM(IF(t.type='sell', transaction_payments.amount, IF(t.type='sell_return', -1 * transaction_payments.amount, 0))) as total_sale"),

                DB::raw("SUM(IF(transaction_payments.method='cash', IF(t.type='sell', transaction_payments.amount, 0), 0)) as total_cash"),
                DB::raw("SUM(IF(transaction_payments.method='check', IF(t.type='sell', transaction_payments.amount, 0), 0)) as total_check"),
                DB::raw("SUM(IF(transaction_payments.method='card', IF(t.type='sell', transaction_payments.amount, 0), 0)) as total_card"),
                DB::raw("SUM(IF(transaction_payments.method='bank_transfer', IF(t.type='sell', transaction_payments.amount, 0), 0)) as total_bank_transfer"),
                DB::raw("SUM(IF(transaction_payments.method='credit', IF(t.type='sell', transaction_payments.amount, 0), 0)) as total_credit"),

                DB::raw("SUM(IF(t.type='refund', transaction_payments.amount, 0)) as total_refund"),
                DB::raw("SUM(IF(t.type='refund', IF(transaction_payments.method='cash', transaction_payments.amount, 0), 0)) as total_cash_refund"),
                DB::raw("SUM(IF(t.type='refund', IF(transaction_payments.method='check', transaction_payments.amount, 0), 0)) as total_check_refund"),
                DB::raw("SUM(IF(t.type='refund', IF(transaction_payments.method='card', transaction_payments.amount, 0), 0)) as total_card_refund"),
                DB::raw("SUM(IF(t.type='refund', IF(transaction_payments.method='bank_transfer', transaction_payments.amount, 0), 0)) as total_bank_transfer_refund"),
                DB::raw("SUM(IF(t.type='refund', IF(transaction_payments.method='credit', transaction_payments.amount, 0), 0)) as total_credit_refund")
            )
            ->first();

        return $payment_details;
    }

    /**
     * Get initial amount when opening cash register.
     * 
     * @param  int  $cashier_id
     * @param  string  $start_date
     * @param  string  $end_date
     * @return float
     */
    public function getInitialAmount($cashier_id, $start_date, $end_date)
    {
        $initial = CashRegister::join('cash_register_transactions as ct', 'ct.cash_register_id', 'cash_registers.id')
            ->whereBetween('cash_registers.created_at', [$start_date, $end_date])
            ->where('cash_registers.cashier_id', $cashier_id)
            ->select(DB::raw("SUM(IF(transaction_type='initial', amount, 0)) as cash_in_hand"))
            ->first();

        return $initial->cash_in_hand;
    }

    /**
     * Get inflow and outflow amount when opening cash register.
     * 
     * @param  int  $cashier_id
     * @param  string  $start_date
     * @param  string  $end_date
     * @return float
     */
    public function getInflowOutflow($cashier_id, $start_date, $end_date)
    {
        $inflow_outflow = InflowOutflow::whereBetween('created_at', [$start_date, $end_date])
            ->where('cashier_id', $cashier_id)
            ->select(
                DB::raw("SUM(IF(type='input', amount, 0)) as inflow"),
                DB::raw("SUM(IF(type='output', amount, 0)) as outflow")
            )
            ->first();

        return $inflow_outflow;
    }

    /**
     * Retrieves details of given rigister id else currently opened register.
     *
     * @param  int  $cashier_id
     * @param  string  $start_date
     * @param  string  $end_date
     * @param  string  $close_date
     * @return object
     */
    public function getReservationPayments($cashier_id, $start_date, $end_date, $close_date)
    {
        $query = CashRegisterTransaction::join('quotes as q', 'cash_register_transactions.quote_id', 'q.id')
            ->join('cash_registers as cr', 'cr.id', 'cash_register_transactions.cash_register_id')
            ->where('q.quote_date', $close_date)
            ->where('q.cashier_id', $cashier_id);

        $is_closed = CashRegister::where('cashier_id', $cashier_id)
            ->where('business_id', request()->session()->get('user.business_id'))
            ->where('date', $close_date)
            ->where('status', 'close')
            ->count();

        $today = \Carbon::now()->format('Y-m-d');

        if ($close_date != $today && $is_closed > 0) {
            $query = $query->whereRaw("IF(cr.status = 'close', IF(cr.date = ?, 1, 0), 0) = 1", [$close_date]);
        }
        
        $query = $query->select('cash_register_transactions.*');
                              
        $reservations = $query->select(
            DB::raw("SUM(IF(transaction_type='reservation', cash_register_transactions.amount, IF(transaction_type='refund', -1 * cash_register_transactions.amount, 0))) as total_sale"),
            DB::raw("SUM(IF(pay_method='cash', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_cash"),
            DB::raw("SUM(IF(pay_method='check', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_check"),
            DB::raw("SUM(IF(pay_method='card', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_card"),
            DB::raw("SUM(IF(pay_method='bank_transfer', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_bank_transfer"),
            DB::raw("SUM(IF(pay_method='credit', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_credit"),
            DB::raw("(SELECT SUM(crt.amount) FROM cash_register_transactions AS crt JOIN cash_registers AS cr ON crt.cash_register_id = cr.id JOIN quotes AS q ON crt.quote_id = q.id WHERE q.quote_date = '$close_date' AND cr.cashier_id = $cashier_id) AS total_reservation"),

            DB::raw("SUM(IF(transaction_type='refund', cash_register_transactions.amount, 0)) as total_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='cash', cash_register_transactions.amount, 0), 0)) as total_cash_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='check', cash_register_transactions.amount, 0), 0)) as total_check_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='card', cash_register_transactions.amount, 0), 0)) as total_card_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='bank_transfer', cash_register_transactions.amount, 0), 0)) as total_bank_transfer_refund"),
            DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='credit', cash_register_transactions.amount, 0), 0)) as total_credit_refund")
        )->first();

        return $reservations;
    }

    /**
     * Retrieves details of given rigister id else currently opened register
     *
     * @param  int  $cashier_id
     * @param  string  $start_date
     * @param  string  $end_date
     * @param  string  $close_date
     * @return object
     */
    public function getReservationPays($cashier_id, $start_date, $end_date, $close_date)
    {
        $payment_details = TransactionPayment::join('quotes as q', 'q.id', 'transaction_payments.quote_id')
            ->whereBetween('transaction_payments.paid_on', [$start_date, $end_date])
            ->where('q.cashier_id', $cashier_id)
            ->whereNotBetween('q.quote_date', [$close_date, $close_date])
            ->select(
                DB::raw("SUM(transaction_payments.amount) as total_sale"),

                DB::raw("SUM(IF(transaction_payments.method='cash', IF(q.type='reservation', transaction_payments.amount, 0), 0)) as total_cash"),
                DB::raw("SUM(IF(transaction_payments.method='check', IF(q.type='reservation', transaction_payments.amount, 0), 0)) as total_check"),
                DB::raw("SUM(IF(transaction_payments.method='card', IF(q.type='reservation', transaction_payments.amount, 0), 0)) as total_card"),
                DB::raw("SUM(IF(transaction_payments.method='bank_transfer', IF(q.type='reservation', transaction_payments.amount, 0), 0)) as total_bank_transfer"),
                DB::raw("SUM(IF(transaction_payments.method='credit', IF(q.type='reservation', transaction_payments.amount, 0), 0)) as total_credit")
            )
            ->first();

        return $payment_details;
    }

    /**
     * Get details of reservations converted to sales.
     *
     * @param  int  $cashier_id
     * @param  string  $start_date
     * @param  string  $end_date
     * @param  string  $close_date
     * @return object
     */
    public function getReservationsToSales($cashier_id, $start_date, $end_date, $close_date)
    {
        $query = CashRegisterTransaction::join('quotes as q', 'cash_register_transactions.quote_id', 'q.id')
            ->join('cash_registers as cr', 'cr.id', 'cash_register_transactions.cash_register_id')
            ->join('transactions as t', 't.id', 'q.transaction_id')
            ->whereBetween('t.transaction_date', [$start_date, $end_date])
            ->where('q.cashier_id', $cashier_id);

        // $is_closed = CashRegister::where('cashier_id', $cashier_id)
        //     ->where('business_id', request()->session()->get('user.business_id'))
        //     ->where('date', $close_date)
        //     ->where('status', 'close')
        //     ->count();

        // $today = \Carbon::now()->format('Y-m-d');

        // if ($close_date != $today && $is_closed > 0) {
        //     $query = $query->whereRaw("IF(cr.status = 'close', IF(cr.date = ?, 1, 0), 0) = 1", [$close_date]);
        // }
        
        $query = $query->select('cash_register_transactions.*');
                              
        $reservations = $query->select(
            DB::raw("SUM(IF(transaction_type='reservation', cash_register_transactions.amount, IF(transaction_type='refund', -1 * cash_register_transactions.amount, 0))) as total_sale"),
            DB::raw("SUM(IF(pay_method='cash', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_cash"),
            DB::raw("SUM(IF(pay_method='check', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_check"),
            DB::raw("SUM(IF(pay_method='card', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_card"),
            DB::raw("SUM(IF(pay_method='bank_transfer', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_bank_transfer"),
            DB::raw("SUM(IF(pay_method='credit', IF(transaction_type='reservation', cash_register_transactions.amount, 0), 0)) as total_credit"),
            DB::raw("(SELECT SUM(crt.amount) FROM cash_register_transactions AS crt JOIN cash_registers AS cr ON crt.cash_register_id = cr.id JOIN quotes AS q ON crt.quote_id = q.id WHERE q.quote_date = '$close_date' AND cr.cashier_id = $cashier_id) AS total_reservation"),

            // DB::raw("SUM(IF(transaction_type='refund', cash_register_transactions.amount, 0)) as total_refund"),
            // DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='cash', cash_register_transactions.amount, 0), 0)) as total_cash_refund"),
            // DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='check', cash_register_transactions.amount, 0), 0)) as total_check_refund"),
            // DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='card', cash_register_transactions.amount, 0), 0)) as total_card_refund"),
            // DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='bank_transfer', cash_register_transactions.amount, 0), 0)) as total_bank_transfer_refund"),
            // DB::raw("SUM(IF(transaction_type='refund', IF(pay_method='credit', cash_register_transactions.amount, 0), 0)) as total_credit_refund")
        )->first();

        return $reservations;
    }

    /**
     * Adds sell payments to currently opened cash register.
     *
     * @param  \App\Quote  $quote
     * @param  array  $payments
     * @return boolean
     */
    public function addSellPaymentsToQuotes($quote, $payments)
    {
        $register = CashRegister::where('cashier_id', $quote->cashier_id)
            ->where('status', 'open')
            ->first();

        $payments_formatted = [];

        foreach ($payments as $payment) {
            $payments_formatted[] = new CashRegisterTransaction([
                'amount' => (isset($payment['is_return']) && $payment['is_return'] == 1) ? (-1 * $this->num_uf($payment['amount'])) : $this->num_uf($payment['amount']),
                'pay_method' => $payment['method'],
                'type' => 'credit',
                'transaction_type' => 'reservation',
                'quote_id' => $quote->id
            ]);
        }

        if (! empty($payments_formatted)) {
            $register->cash_register_transactions()->saveMany($payments_formatted);
        }

        return true;
    }

    /**
     * Adds credit sell payments to currently opened cash register
     *
     * @param  \App\Quote  $quote
     *
     * @return boolean
     */
    public function addCreditSellPaymentToQuotes($quote, $total_paid, $final_amount) {
        $amount = $final_amount - $total_paid;

        $payment[] = array(
            'is_return' => 0,
            'amount' => $amount,
            'method' => 'credit'
        );
        
        return $this->addSellPaymentsToQuotes($quote, $payment);
    }

    /**
     * Refunds all payments of a sell
     *
     * @param object/int $transaction
     * @return boolean
     */
    public function refundQuote($quote)
    {
        $total_payment = CashRegisterTransaction::where('quote_id', $quote->id)
            ->select(
                'cash_register_id',
                DB::raw("SUM(IF(pay_method='cash', IF(type='credit', amount, -1 * amount), 0)) as total_cash"),
                DB::raw("SUM(IF(pay_method='credit', IF(type='credit', amount, -1 * amount), 0)) as total_credit"),
                DB::raw("SUM(IF(pay_method='card', IF(type='credit', amount, -1 * amount), 0)) as total_card"),
                DB::raw("SUM(IF(pay_method='check', IF(type='credit', amount, -1 * amount), 0)) as total_check"),
                DB::raw("SUM(IF(pay_method='bank_transfer', IF(type='credit', amount, -1 * amount), 0)) as total_bank_transfer")
            )->first();

        $refunds = [
            'cash' => $total_payment->total_cash,
            'credit' => $total_payment->total_credit,
            'card' => $total_payment->total_card,
            'check' => $total_payment->total_check,
            'bank_transfer' => $total_payment->total_bank_transfer
        ];

        $refund_formatted = [];
        
        foreach ($refunds as $key => $val) {
            if ($val > 0) {
                $refund_formatted[] = new CashRegisterTransaction([
                    'amount' => $val,
                    'pay_method' => $key,
                    'type' => 'debit',
                    'transaction_type' => 'refund',
                    'quote_id' => $quote->id
                ]);
            }
        }

        $register =  CashRegister::where('cashier_id', $quote->cashier_id)
            ->where('status', 'open')
            ->first();

        if (! empty($refund_formatted)) {
            $register->cash_register_transactions()->saveMany($refund_formatted);
        }

        return true;
    }
}
