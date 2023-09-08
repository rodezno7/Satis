<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SaleCostProductReportExport implements WithEvents, WithTitle
{
    private $transactions;
    private $business_name;
    private $report_name;

    /**
     * Constructor.
     * 
     * @param collect $transactions
     * @param string $business_name
     * @param string $date;
     * 
     * @return void
     */
    public function __construct($transactions, $business_name, $report_name)
    {
    	$this->transactions = $transactions;
        $this->business_name = $business_name;
        $this->report_name = $report_name;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.sale_cost_product');
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
                $event->sheet->mergeCells('A1:E1');
                $event->sheet->mergeCells('A2:E2');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:E1', 'center');
                $event->sheet->horizontalAlign('A1:E2', "center");
                $event->sheet->setBold('A1:E2');
                $event->sheet->setFontSize('A1:E1', 14);
                $event->sheet->setFontSize('A2:E2', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper($this->report_name));

                /** Column width */
                $event->sheet->columnWidth('A', 15); // sku
                $event->sheet->columnWidth('B', 40); // product
                $event->sheet->columnWidth('C', 15); // sales
                $event->sheet->columnWidth('D', 15); // unit cost
                $event->sheet->columnWidth('E', 15); // total cost

                /** table head */
                $event->sheet->setBold('A3:E3');
                $event->sheet->horizontalAlign('A3:E3', 'center');
                $event->sheet->verticalAlign('A3:E3', 'center');
                $event->sheet->setCellValue('A3', mb_strtoupper(__('product.sku')));
                $event->sheet->setCellValue('B3', mb_strtoupper(__('product.product')));
                $event->sheet->setCellValue('C3', mb_strtoupper(__('sale.sells')));
                $event->sheet->setCellValue('D3', mb_strtoupper(__('sale.unit_cost')));
                $event->sheet->setCellValue('E3', mb_strtoupper(__('sale.total_cost')));
                
                /** table body */
                $row = 4;
                $total_sale = 0;
                $total_cost = 0;
                foreach ($this->transactions as $t) {
                    $event->sheet->setCellValue('A'. $row, $t->sku);
                    $event->sheet->setCellValue('B'. $row, $t->product);
                    $event->sheet->setCellValue('C'. $row, $t->quantity);
                    $event->sheet->setCellValue('D'. $row, $t->cost);
                    $event->sheet->setCellValue('E'. $row, $t->total);

                    $total_sale += $t->quantity;
                    $total_cost += $t->total;

                    $row ++;
                }
                
                /** table foot */
                $event->sheet->mergeCells('A'. $row .':B'. $row);
                $event->sheet->setBold('A'. $row .':E'. $row);
                $event->sheet->horizontalAlign('A'. $row .':B'. $row, 'center');
                $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('sale.total')));
                $event->sheet->setCellValue('C'. $row, $total_sale);
                $event->sheet->setCellValue('E'. $row, $total_cost);

                /** set font size, font family and borders */
    			$event->sheet->setFontSize('A3:E'. $row, 10);
                $event->sheet->setFormat('C3:C'. $row, '#,##0_-');
                $event->sheet->setFormat('D1:E'. $row, '$ #,##0.00_-');
                $event->sheet->setAllBorders('A3:E'. $row, 'thin');
                $event->sheet->setFontFamily('A1:E'. $row, 'Calibri');
            },
        ];
    }
}