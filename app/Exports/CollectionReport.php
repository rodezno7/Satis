<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CollectionReport implements WithEvents, WithTitle
{
    private $collection_transactions;
    private $collections;
    private $business_name;
    private $start_date;
    private $end_date;
    private $transactionUtil;

    /**
     * Constructor.
     * 
     * @param collect $transaction_transactions
     * @param collect $transactions
     * @param string $business_name
     * @param string $start_date
     * @param string $end_date
     * @param App\Utils\TransactionUtil
     * 
     * @return void
     */
    public function __construct($collection_transactions, $collections, $business_name, $start_date, $end_date, $transactionUtil)
    {
        $this->collection_transactions = $collection_transactions;
    	$this->collections = $collections;
        $this->business_name = $business_name;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('cxc.collections');
    }

    /**
     * Configure events and document format.
     * 
     * @return array
     */
    public function registerEvents(): array
    {
    	return [
    		AfterSheet::class => function(AfterSheet $event) {
                /** General setup */
    			$event->sheet->setOrientation("landscape");
                $event->sheet->setShowGridlines(false);

                /** Header */
                $event->sheet->mergeCells('A1:Q1');
                $event->sheet->mergeCells('A2:Q2');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:Q1', 'center');
                $event->sheet->horizontalAlign('A1:Q2', "center");
                $event->sheet->setBold('A1:Q2');
                $event->sheet->setFontSize('A1:Q1', 14);
                $event->sheet->setFontSize('A2:Q2', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper(__('cxc.collections')) ." ". mb_strtoupper(strtoupper(__('accounting.from_date')) ." ". $this->start_date ." ". strtoupper(__('accounting.to_date')) ." ". $this->end_date));

                /** Column width and font align */
                $event->sheet->columnWidth('A', 10); // transaction date
                $event->sheet->columnWidth('B', 8); // correlative
                $event->sheet->columnWidth('C', 40); // customer
                $event->sheet->columnWidth('D', 10); // sku
                $event->sheet->columnWidth('E', 30); // product
                $event->sheet->columnWidth('F', 10); // quantity
                $event->sheet->columnWidth('G', 15); // price exc tax
                $event->sheet->columnWidth('H', 15); // price inc tax
                $event->sheet->columnWidth('I', 10); // payments
                $event->sheet->columnWidth('J', 10); // payment date
                $event->sheet->columnWidth('K', 10); // product balance
                $event->sheet->columnWidth('L', 10); // balance
                $event->sheet->columnWidth('M', 15); // payments status
                $event->sheet->columnWidth('N', 30); // seller
                $event->sheet->columnWidth('O', 20); // customer portfolio
                $event->sheet->columnWidth('P', 30); // city
                $event->sheet->columnWidth('Q', 25); // state

                /** table head */
                $event->sheet->setBold('A3:Q3');
                $event->sheet->horizontalAlign('A3:Q3', 'center');
                $event->sheet->verticalAlign('A3:Q3', 'center');
                $event->sheet->setCellValue('A3', mb_strtoupper(__('invoice.date')));
                $event->sheet->setCellValue('B3', mb_strtoupper(__('document_type.doc')));
                $event->sheet->setCellValue('C3', mb_strtoupper(__('customer.customer')));
                $event->sheet->setCellValue('D3', mb_strtoupper(__('product.sku')));
                $event->sheet->setCellValue('E3', mb_strtoupper(__('product.product')));
                $event->sheet->setCellValue('F3', mb_strtoupper(__('lang_v1.quantity')));
                $event->sheet->setCellValue('G3', mb_strtoupper(__('sale.price_exc_vat')));
                $event->sheet->setCellValue('H3', mb_strtoupper(__('sale.price_inc_vat')));
                $event->sheet->setCellValue('I3', mb_strtoupper(__('payment.payments')));
                $event->sheet->setCellValue('J3', mb_strtoupper(__('report.payment_date')));
                $event->sheet->setCellValue('K3', mb_strtoupper(__('product.product_balance')));
                $event->sheet->setCellValue('L3', mb_strtoupper(__('kardex.balance')));
                $event->sheet->setCellValue('M3', mb_strtoupper(__('sale.payment_status')));
                $event->sheet->setCellValue('N3', mb_strtoupper(__('customer.seller')));
                $event->sheet->setCellValue('O3', mb_strtoupper(__('customer.customer_portfolio')));
                $event->sheet->setCellValue('P3', mb_strtoupper(__('customer.city')));
                $event->sheet->setCellValue('Q3', mb_strtoupper(__('customer.state')));
                
                /** table body */
                $row = 4;
                $transactions = $this->collection_transactions
                    ->unique('transaction_id')
                    ->pluck('transaction_id');

                foreach($transactions as $t) {
                    $lines = $this->collection_transactions
                        ->where('transaction_id', $t)
                        ->sortByDesc('unit_price_inc_tax');

                    $collect = $this->collections
                        ->where('transaction_id', $t)
                        ->first();

                    $payments = $this->collections
                        ->where('transaction_id', $t);
                    
                    $withheld = 0;
                    if (!empty($collect->withheld)) {
                        $withheld = $collect->withheld;
                    }

                    $balance = 0;
                    if (!empty($collect->balance)) {
                        $balance = $collect->balance + $withheld;

                        $withheld = 0;
                    }

                    $remaining = 0;
                    foreach ($lines as $l) {
                        $this->setCommonValues($event, $l, $row);

                        if (($balance >= $l->unit_price_inc_tax) && ($l->unit_price_inc_tax > 0)) {
                            $event->sheet->setCellValue('K'. $row, "0");
                            
                            $balance -= $l->unit_price_inc_tax;
                            $row ++;
                            
                            \Log::info("Inside balance: correlative ". $l->correlative ." unit_price_inc ". $l->unit_price_inc_tax);
                            continue;
                        }

                        if ($balance > 0) {
                            $remaining = $l->unit_price_inc_tax - $balance;
                            $event->sheet->setCellValue('K'. $row, $remaining);

                            \Log::info("Inside remaining: correlative ". $l->correlative ." unit_price_inc ". $l->unit_price_inc_tax);

                            $balance = 0;
                        }

                        $pay_left = 0;
                        foreach ($payments as $p) {
                            $pay_left = $remaining + $p->amount;

                            if ($withheld > 0) {
                                $pay_left += $withheld;
                                $p->amount += $withheld;
                                $withheld = 0;
                            }

                            $event->sheet->setCellValue('I'. $row, $p->amount);
                            $event->sheet->setCellValue('J'. $row, $this->transactionUtil->format_date($p->transaction_date));
                            
                            if ($pay_left >= $l->unit_price_inc_tax && ($l->unit_price_inc_tax > 0)) {
                                $event->sheet->setCellValue('K'. $row, "0");

                                $pay_left -= $p->amount;
                                $row ++;

                                $this->setCommonValues($event, $l, $row);
                                continue;
                            }

                            if ($pay_left > 0) {
                                $left =
                                    ($l->unit_price_inc_tax - $pay_left) > 0.01
                                        ? ($l->unit_price_inc_tax - $pay_left) : "0";

                                $event->sheet->setCellValue('K'. $row, $left);

                                $pay_left = 0;
                            }

                            \Log::info("Inside payments: correlative ". $l->correlative ." unit_price_inc ". $l->unit_price_inc_tax ." payment_amount ". $p->amount ." date ". $this->transactionUtil->format_date($p->transaction_date));
                            $row ++;
                        }

                        $row ++;
                    }
                }
                $row --;

                /** set font size and family, set borders */
    			$event->sheet->setFontSize('A3:Q'. $row, 10);
                /*$event->sheet->horizontalAlign('B4:I'. $row, 'right');
                $event->sheet->setFormat('A4:A'. $row, '@');
                $event->sheet->setFormat('B4:C'. $row, '0.00000000');
                $event->sheet->setFormat('D4:E'. $row, 'h:mm:ss');
                $event->sheet->setFormat('F4:G'. $row, '0.00');
                $event->sheet->setFormat('H4:H'. $row, '0.000000');
                $event->sheet->setFormat('I4:I'. $row, 'h:mm:ss');*/
                $event->sheet->setAllBorders('A3:Q'. $row, 'thin');
                $event->sheet->setFontFamily('A1:Q'. $row, 'Calibri');
            },
        ];
    }

    /**
     * Set common values
     * 
     * @param Object $event
     * @param Object $record
     * @param string $row
     * 
     * @return void
     */
    private function setCommonValues($event, $record, $row) {
        $event->sheet->setCellValue('A'. $row, $this->transactionUtil->format_date($record->transaction_date));
        $event->sheet->setCellValue('B'. $row, $record->correlative);
        $event->sheet->setCellValue('C'. $row, $record->customer);
        $event->sheet->setCellValue('D'. $row, $record->sku);
        $event->sheet->setCellValue('E'. $row, $record->product);
        $event->sheet->setCellValue('F'. $row, $record->quantity);
        $event->sheet->setCellValue('G'. $row, $record->unit_price_exc_tax);
        $event->sheet->setCellValue('H'. $row, $record->unit_price_inc_tax);

        $event->sheet->setCellValue('M'. $row, mb_strtoupper(__('payment.'.$record->payment_status)));
        $event->sheet->setCellValue('N'. $row, mb_strtoupper($record->seller));
        $event->sheet->setCellValue('O'. $row, mb_strtoupper($record->portfolio));
        $event->sheet->setCellValue('P'. $row, mb_strtoupper($record->city));
        $event->sheet->setCellValue('Q'. $row, mb_strtoupper($record->state));
    }
}