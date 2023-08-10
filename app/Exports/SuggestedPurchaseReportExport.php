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
                $event->sheet->mergeCells('A1:O1');
                $event->sheet->mergeCells('A2:O2');
                $event->sheet->mergeCells('A3:O3');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:O1', 'center');
                $event->sheet->horizontalAlign('A1:O3', "center");
                $event->sheet->setBold('A1:O3');
                $event->sheet->setFontSize('A1:O1', 14);
                $event->sheet->setFontSize('A2:O3', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper($this->location_name));
                $event->sheet->setCellValue('A3', mb_strtoupper(__('report.suggested_purchase_report')) ." ". strtoupper(__('accounting.to_date')) ." ". $this->date);

                /** Column width and font align */
                $event->sheet->columnWidth('A', 20); // sku
                $event->sheet->columnWidth('B', 40); // product
                $event->sheet->columnWidth('C', 25); // category
                $event->sheet->columnWidth('D', 25); // subcategory
                $event->sheet->columnWidth('E', 25); // brand
                $event->sheet->columnWidth('F', 15); // min value
                $event->sheet->columnWidth('G', 15); // max value
                $event->sheet->columnWidth('H', 15); // avg value
                $event->sheet->columnWidth('I', 15); // min stock
                $event->sheet->columnWidth('J', 15); // max stock
                $event->sheet->columnWidth('K', 15); // stock
                $event->sheet->columnWidth('L', 15); // request
                $event->sheet->columnWidth('M', 15); // amount to request
                $event->sheet->columnWidth('N', 15); // excess
                $event->sheet->columnWidth('O', 15); // move

                /** table head */
                $event->sheet->setBold('A4:O4');
                $event->sheet->horizontalAlign('A4:O4', 'center');
                $event->sheet->verticalAlign('A4:O4', 'center');
                $event->sheet->setCellValue('A4', mb_strtoupper(__('product.sku')));
                $event->sheet->setCellValue('B4', mb_strtoupper(__('product.product')));
                $event->sheet->setCellValue('C4', mb_strtoupper(__('category.category')));
                $event->sheet->setCellValue('D4', mb_strtoupper(__('product.sub_category')));
                $event->sheet->setCellValue('E4', mb_strtoupper(__('brand.brand')));
                $event->sheet->setCellValue('F4', mb_strtoupper(__('report.min_val')));
                $event->sheet->setCellValue('G4', mb_strtoupper(__('report.max_val')));
                $event->sheet->setCellValue('H4', mb_strtoupper(__('report.avg_val')));
                $event->sheet->setCellValue('I4', mb_strtoupper(__('report.min_stock')));
                $event->sheet->setCellValue('J4', mb_strtoupper(__('report.max_stock')));
                $event->sheet->setCellValue('K4', mb_strtoupper(__('report.stock')));
                $event->sheet->setCellValue('L4', mb_strtoupper(__('report.request')));
                $event->sheet->setCellValue('M4', mb_strtoupper(__('report.request')));
                $event->sheet->setCellValue('N4', mb_strtoupper(__('report.excess')));
                $event->sheet->setCellValue('O4', mb_strtoupper(__('report.move')));
                
                /** table body */
                $row = 5;
                $row --;

                /** set font size and family, set borders */
    			$event->sheet->setFontSize('A4:O'. $row, 10);
                //$event->sheet->horizontalAlign('B4:I'. $row, 'right');
                //$event->sheet->setFormat('A4:A'. $row, '@');
                //$event->sheet->setFormat('G4:F'. $row, '0');
                //$event->sheet->setFormat('F4:I'. $row, '0.00');
                //$event->sheet->setFormat('K4:L'. $row, '0.00');
                $event->sheet->setAllBorders('A4:O'. $row, 'thin');
                $event->sheet->setFontFamily('A1:O'. $row, 'Calibri');
            },
        ];
    }

}