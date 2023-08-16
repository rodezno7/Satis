<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SuggestedPurchaseReportExport implements WithEvents, WithTitle
{
    private $transactions;
    private $business_name;
    private $location_name;
    private $date;

    /**
     * Constructor.
     * 
     * @param collect $transactions
     * @param string $business_name
     * @param string $date;
     * 
     * @return void
     */
    public function __construct($transactions, $business_name, $location_name, $date)
    {
    	$this->transactions = $transactions;
        $this->business_name = $business_name;
        $this->location_name = $location_name;
        $this->date = $date;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.suggested_purchase_report');
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
                $event->sheet->mergeCells('A1:P1');
                $event->sheet->mergeCells('A2:P2');
                $event->sheet->mergeCells('A3:P3');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:P1', 'center');
                $event->sheet->horizontalAlign('A1:P3', "center");
                $event->sheet->setBold('A1:P3');
                $event->sheet->setFontSize('A1:P1', 14);
                $event->sheet->setFontSize('A2:P3', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper($this->location_name));
                $event->sheet->setCellValue('A3', mb_strtoupper(__('report.suggested_purchase_report')) ." ". strtoupper(__('accounting.to_date')) ." ". $this->date);

                /** Column width and font align */
                $event->sheet->columnWidth('A', 15); // sku
                $event->sheet->columnWidth('B', 40); // product
                $event->sheet->columnWidth('C', 20); // category
                $event->sheet->columnWidth('D', 20); // subcategory
                $event->sheet->columnWidth('E', 15); // brand
                $event->sheet->columnWidth('F', 12); // total
                $event->sheet->columnWidth('G', 12); // min value
                $event->sheet->columnWidth('H', 12); // max value
                $event->sheet->columnWidth('I', 12); // avg value
                $event->sheet->columnWidth('J', 12); // min stock
                $event->sheet->columnWidth('K', 12); // max stock
                $event->sheet->columnWidth('L', 10); // stock
                $event->sheet->columnWidth('M', 10); // request
                $event->sheet->columnWidth('N', 10); // amount to request
                $event->sheet->columnWidth('O', 10); // excess
                $event->sheet->columnWidth('P', 10); // move

                /** table head */
                $event->sheet->setBold('A4:P4');
                $event->sheet->horizontalAlign('A4:P4', 'center');
                $event->sheet->verticalAlign('A4:P4', 'center');
                $event->sheet->setCellValue('A4', mb_strtoupper(__('product.sku')));
                $event->sheet->setCellValue('B4', mb_strtoupper(__('product.product')));
                $event->sheet->setCellValue('C4', mb_strtoupper(__('category.category')));
                $event->sheet->setCellValue('D4', mb_strtoupper(__('product.sub_category')));
                $event->sheet->setCellValue('E4', mb_strtoupper(__('brand.brand')));
                $event->sheet->setCellValue('F4', mb_strtoupper(__('sale.total')));
                $event->sheet->setCellValue('G4', mb_strtoupper(__('report.min_val')));
                $event->sheet->setCellValue('H4', mb_strtoupper(__('report.max_val')));
                $event->sheet->setCellValue('I4', mb_strtoupper(__('report.avg_val')));
                $event->sheet->setCellValue('J4', mb_strtoupper(__('report.min_stock')));
                $event->sheet->setCellValue('K4', mb_strtoupper(__('report.max_stock')));
                $event->sheet->setCellValue('L4', mb_strtoupper(__('report.stock')));
                $event->sheet->setCellValue('M4', mb_strtoupper(__('report.request')));
                $event->sheet->setCellValue('N4', mb_strtoupper(__('report.request')));
                $event->sheet->setCellValue('O4', mb_strtoupper(__('report.excess')));
                $event->sheet->setCellValue('P4', mb_strtoupper(__('report.move')));
                
                /** table body */
                $row = 5;
                foreach ($this->transactions as $t) {
                    $request = ($t->avg_val - $t->stock) <= 0 ? 'no' : 'yes';
                    $qty_req = $request == 'yes' ? ($t->max_val - $t->stock) : 0;
                    
                    $diff_max = $t->stock - $t->max_val;
                    $excess = $diff_max <= 0 ? 'no' : 'yes';
                    $move = $diff_max <= 0 ? 0 : $diff_max;

                    $event->sheet->setCellValue('A'. $row, $t->sku);
                    $event->sheet->setCellValue('B'. $row, $t->product);
                    $event->sheet->setCellValue('C'. $row, $t->category);
                    $event->sheet->setCellValue('D'. $row, $t->sub_category);
                    $event->sheet->setCellValue('E'. $row, $t->brand);
                    $event->sheet->setCellValue('F'. $row, $t->total);
                    $event->sheet->setCellValue('G'. $row, $t->min_val);
                    $event->sheet->setCellValue('H'. $row, $t->max_val);
                    $event->sheet->setCellValue('I'. $row, $t->avg_val);
                    $event->sheet->setCellValue('J'. $row, $t->min_val);
                    $event->sheet->setCellValue('K'. $row, $t->max_val);
                    $event->sheet->setCellValue('L'. $row, $t->stock);
                    $event->sheet->setCellValue('M'. $row, mb_strtoupper(__('lang_v1.'. $request)));
                    $event->sheet->setCellValue('N'. $row, $qty_req);
                    $event->sheet->setCellValue('O'. $row, mb_strtoupper(__('lang_v1.'. $excess)));
                    $event->sheet->setCellValue('P'. $row, $move);

                    $row ++;
                }
                $row --;

                /** set font size and family, set borders */
    			$event->sheet->setFontSize('A4:P'. $row, 10);
                $event->sheet->horizontalAlign('L4:L'. $row, 'center');
                $event->sheet->horizontalAlign('N4:N'. $row, 'center');
                $event->sheet->setFormat('A1:E'. $row, '@');
                $event->sheet->setAllBorders('A4:P'. $row, 'thin');
                $event->sheet->setFontFamily('A1:P'. $row, 'Calibri');
            },
        ];
    }

}