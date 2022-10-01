<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AllSalesWithUtilityReportExport implements WithEvents, WithTitle
{
    private $sales;
    private $business;
    private $transactionUtil;

    /**
     * Constructor.
     * 
     * @param  array  $sales
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($sales, $business_name, $transactionUtil)
    {
    	$this->sales = $sales;
        $this->business_name = $business_name;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.all_sales_with_utility_report');
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
                $items = count($this->sales) + 4;
                $sales = $this->sales;

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->setFontFamily('A1:H'.$items, 'Calibri');
    			$event->sheet->setFontSize('A1:H'.$items, 10);

                /** Columns style */
                $event->sheet->columnWidth('A', 10); // date
                $event->sheet->columnWidth('B', 12); // correlative
                $event->sheet->columnWidth('C', 10); // doc type
                $event->sheet->columnWidth('D', 40); // customer name
                $event->sheet->columnWidth('E', 17); // pay method
                $event->sheet->columnWidth('F', 12); // cost
                $event->sheet->columnWidth('G', 12); // total
                $event->sheet->columnWidth('H', 12); // utility
                $event->sheet->setFormat('A5:E' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('F5:H' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                /** Business name */
                $event->sheet->horizontalAlign('A1:H1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:H1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:H1')->getFont()->setSize(12);
    			$event->sheet->mergeCells('A1:H1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));

                /** Report name */
                $event->sheet->horizontalAlign('A2:H2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:H2')->getFont()->setBold(true);
    			$event->sheet->mergeCells('A2:H2');
                $event->sheet->mergeCells('A3:H3');
                $event->sheet->setCellValue('A2', mb_strtoupper(__('report.all_sales_with_utility_report')));

                /** table head */
                $event->sheet->horizontalAlign('A4:H4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A4:H4')->getFont()->setBold(true);
                $event->sheet->setCellValue('A4', mb_strtoupper(__('messages.date')));
                $event->sheet->setCellValue('B4', mb_strtoupper(__('lang_v1.correlative')));
                $event->sheet->setCellValue('C4', mb_strtoupper(__('document_type.doc_type')));
                $event->sheet->setCellValue('D4', mb_strtoupper(__('sale.customer_name')));
                $event->sheet->setCellValue('E4', mb_strtoupper(__('payment.payment_method')));
                $event->sheet->setCellValue('F4', mb_strtoupper(__('sale.cost')));
                $event->sheet->setCellValue('G4', mb_strtoupper(__('sale.total')));
                $event->sheet->setCellValue('H4', mb_strtoupper(__('sale.utility')));

                /** table body */
                $count = 5;
                $cost_total = 0; $final_total = 0; $utility_total = 0;
                foreach($sales as $s){
                    $event->sheet->setCellValue('A'. $count, $this->transactionUtil->format_date($s->transaction_date));
                    $event->sheet->setCellValue('B'. $count, $s->correlative);
                    $event->sheet->setCellValue('C'. $count, $s->doc_type);
                    $event->sheet->setCellValue('D'. $count, $s->customer_name);
                    if($s->status == 'final'){
                        $event->sheet->setCellValue('E'. $count, __('payment.' . $s->payment_method));
                    } else{
                        $event->sheet->setCellValue('E'. $count, '-');
                    }
                    $event->sheet->setCellValue('F'. $count, $s->cost_total);
                    $event->sheet->setCellValue('G'. $count, $s->final_total);
                    $event->sheet->setCellValue('H'. $count, $s->utility);

                    $count++;
                    $cost_total += $s->cost_total;
                    $final_total += $s->final_total;
                    $utility_total += $s->utility;
                }

                /** table footer */
                $event->sheet->mergeCells('A'. $count . ':E'. $count);
                $event->sheet->horizontalAlign('A' . $count . ':E' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':H' . $count)->getFont()->setBold(true);
                $event->sheet->setFormat('A' . $count, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('F' . $count . ':H' . $count, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setCellValue('A' . $count, mb_strtoupper(__('accounting.totals')));
                $event->sheet->setcellValue('F'. $count, $cost_total);
                $event->sheet->setCellValue('G'. $count, $final_total);
                $event->sheet->setCellValue('H'. $count, $utility_total);
            },
        ];
    }
}
