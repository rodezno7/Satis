<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Customer;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\Util;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
// use MAIL;
// use PDF;

class MailController extends Controller
{
    /**
     * Constructor
     *
     * @param  \App\Utils\Util  $util
     * @return void
     */
    public function __construct(Util $util)
    {
        $this->util = $util;
    }

    /**
     * Send mail with customer account statement.
     * 
     * @return \Illuminate\Http\Response
     */
    public function sendAccountStatement()
    {
        try {
            $customer_id = request()->input('email_customer_id');
            $payment_status = request()->input('email_payment_status', 0);
            $start_date = request()->input('email_start_date');
            $end_date = request()->input('email_end_date');

            $customer = Customer::find($customer_id);

            $data['email'] = $customer->email;
            $data['title'] = __('report.account_statement', [
                'customer_tax' => $customer->tax_number,
                'customer' => $customer->name
            ]);
            $data['body'] = __('customer.account_statement_email', [
                // 'start_date' => $this->util->format_date($start_date),
                // 'end_date' => $this->util->format_date($end_date)
                // 'customer' => $customer->name,
                'end_date' => $this->util->format_date(Carbon::today())
            ]);
            $data['customer'] = $customer->business_name ?? $customer->name;

            $business_id = request()->session()->get('user.business_id');
            
            // Params
            $params = [
                'business_id' => $business_id,
                'customer_id' => $customer_id,
                'payment_status' => $payment_status,
                'start_date' => $start_date,
                'end_date' => $end_date
            ];

            // Lines
            $lines = $this->getLinesForAccountStatement($params);

            $date = \Carbon::now();
            $size = 8;

            $business = Business::find($business_id);

            $location = BusinessLocation::first();
            $business->landmark = $location->landmark;
            $business->city = $location->city;
            $business->state = $location->state;
            $business->mobile = $location->mobile;

            $pdf = \PDF::loadView('reports.account_statement_pdf',
                compact('lines', 'size', 'date', 'business', 'customer'));

            \Mail::send('balances_customer.email', $data, function($message) use ($data, $pdf, $customer) {
                $message->to($data['email'], $data['email'])
                    ->subject($data['title'])
                    ->attachData($pdf->output(), __('report.account_statement_head') . '-' . $customer->name . '-' . $this->util->format_date(Carbon::today()) . '.pdf');
            });

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.mail_sent_successfully')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->action('CustomerController@indexBalancesCustomer')->with('status', $output);
    }

    /**
     * Get lines for customer account statement.
     * 
     * @param  array  $params
     * @return array
     */
    public function getLinesForAccountStatement($params)
    {
        // Customer filter
        $customer_id = ! empty($params['customer_id']) ? $params['customer_id'] : 0;

        // Sales
        $sales = Transaction::join('document_types', 'transactions.document_types_id', 'document_types.id')
            ->leftJoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where('transactions.customer_id', $customer_id)
            ->where('transactions.business_id', $params['business_id'])
            ->select(
                DB::raw("CONCAT(document_types.short_name, transactions.correlative) as correlative"),
                'transactions.transaction_date',
                'transactions.final_total',
                DB::raw("IF(customers.is_default = 1, transactions.customer_name, customers.name) as customer_name"),
                'transactions.pay_term_number',
                'transactions.payment_balance'
            );

        // Sales returns
        $sales_returns = Transaction::join('document_types', 'transactions.document_types_id', 'document_types.id')
            ->join('transactions as parent_transactions', 'transactions.return_parent_id', 'parent_transactions.id')
            ->leftJoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'sell_return')
            ->where('transactions.status', 'final')
            ->where('transactions.customer_id', $customer_id)
            ->where('transactions.business_id', $params['business_id'])
            ->select(
                DB::raw("CONCAT(document_types.short_name, transactions.correlative) as correlative"),
                'transactions.transaction_date',
                'transactions.final_total',
                'parent_transactions.transaction_date',
                DB::raw("IF(customers.is_default = 1, transactions.customer_name, customers.name) as customer_name")
            );

        // Payments
        $payments = TransactionPayment::join('transactions', 'transaction_payments.transaction_id', 'transactions.id')
            ->leftJoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.customer_id', $customer_id)
            ->where('transaction_payments.business_id', $params['business_id'])
            ->select(
                'transaction_payments.payment_ref_no',
                'transaction_payments.transfer_ref_no',
                'transaction_payments.paid_on',
                'transaction_payments.amount',
                'transactions.transaction_date',
                'transactions.pay_term_number',
                DB::raw("IF(customers.is_default = 1, transactions.customer_name, customers.name) as customer_name")
            );

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $sales->whereDate('transactions.transaction_date', '>=', $params['start_date'])
                ->whereDate('transactions.transaction_date', '<=', $params['end_date']);

            $sales_returns->whereDate('parent_transactions.transaction_date', '>=', $params['start_date'])
                ->whereDate('parent_transactions.transaction_date', '<=', $params['end_date']);

            $payments->whereDate('transactions.transaction_date', '>=', $params['start_date'])
                ->whereDate('transactions.transaction_date', '<=', $params['end_date']);
        }

        // Payment status filter
        if ($params['payment_status'] == 1) {
            $sales->whereIn('transactions.payment_status', ['due', 'partial']);

            $sales_returns->whereIn('parent_transactions.payment_status', ['due', 'partial']);

            $payments->whereIn('transactions.payment_status', ['due', 'partial']);
        }

        $sales = $sales->orderBy('transactions.transaction_date')->get();

        $sales_returns = $sales_returns->orderBy('transactions.transaction_date')->get();

        $payments = $payments->orderBy('transaction_payments.paid_on')->get();

        $result = collect();

        foreach ($sales as $sale) {
            $transaction_date = \Carbon::createFromFormat('Y-m-d H:i:s', $sale->transaction_date);
            $expiration_date = $transaction_date->addDays($sale->pay_term_number);

            if ($expiration_date->lt(\Carbon::now())) {
                $delay_date = $expiration_date->diffInDays(\Carbon::now());
            } else {
                $delay_date = 0;
            }

            $item = [
                'date' => $sale->transaction_date,
                'no_doc' => $sale->correlative,
                'currency' => 'usd',
                'customer' => $sale->customer_name,
                'amount' => $sale->final_total,
                'expiration' => $expiration_date->format('Y-m-d H:i:s'),
                'payment' => $sale->payment_balance,
                'balance' => $sale->final_total - $sale->payment_balance,
                'delay' => $delay_date
            ];

            $result->push($item);
        }

        foreach ($sales_returns as $sale_return) {
            $transaction_date = \Carbon::createFromFormat('Y-m-d H:i:s', $sale_return->transaction_date);
            $expiration_date = $transaction_date->addDays($sale_return->pay_term_number);

            if ($expiration_date->lt(\Carbon::now())) {
                $delay_date = $expiration_date->diffInDays(\Carbon::now());
            } else {
                $delay_date = 0;
            }

            $item = [
                'date' => $sale_return->transaction_date,
                'no_doc' => $sale_return->correlative,
                'currency' => 'usd',
                'customer' => $sale_return->customer_name,
                'amount' => $sale_return->final_total * -1,
                'expiration' => $expiration_date->format('Y-m-d H:i:s'),
                'payment' => 0,
                'balance' => $sale_return->final_total * -1,
                'delay' => $delay_date
            ];

            $result->push($item);
        }

        foreach ($payments as $payment) {
            $transaction_date = \Carbon::createFromFormat('Y-m-d H:i:s', $payment->paid_on);
            $expiration_date = $transaction_date->addDays($payment->pay_term_number);

            if ($expiration_date->lt(\Carbon::now())) {
                $delay_date = $expiration_date->diffInDays(\Carbon::now());
            } else {
                $delay_date = 0;
            }

            $item = [
                'date' => $payment->paid_on,
                'no_doc' => $payment->payment_ref_no ?? $payment->transfer_ref_no,
                'currency' => 'usd',
                'customer' => $payment->customer_name,
                'amount' => $payment->amount * -1,
                'expiration' => $expiration_date->format('Y-m-d H:i:s'),
                'payment' => 0,
                'balance' => $payment->amount * -1,
                'delay' => $delay_date
            ];

            $result->push($item);
        }

        $result = $result->sortBy('date');

        return $result;
    }
}
