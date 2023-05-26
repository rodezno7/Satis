<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PriceListsReport implements WithEvents, WithTitle
{
    private $business_name;
    private $price_lists;

    /**
     * Constructor.
     * 
     * @param string $business_name
     * @param collect $price_lists
     * 
     * @return void
     */
    public function __construct($business_name, $price_lists) {
        $this->business_name = $business_name;
        $this->price_lists = $price_lists;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.list_price_report');
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
                $event->sheet->mergeCells('A1:K1');
                $event->sheet->mergeCells('A2:K2');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:K1', 'center');
                $event->sheet->horizontalAlign('A1:K2', "center");
                $event->sheet->setBold('A1:K2');
                $event->sheet->setFontSize('A1:K1', 14);
                $event->sheet->setFontSize('A2:K2', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper(__('report.price_lists_report')));

                /** Column width and font align */
                $event->sheet->columnWidth('A', 12); // sku
                $event->sheet->columnWidth('B', 35); // product name
                $event->sheet->columnWidth('C', 15); // category
                $event->sheet->columnWidth('D', 15); // subcategory
                $event->sheet->columnWidth('E', 15); // brand
                $event->sheet->columnWidth('F', 12); // cost
                $event->sheet->columnWidth('G', 12); // price_1
                $event->sheet->columnWidth('H', 12); // price_2
                $event->sheet->columnWidth('I', 12); // price_3
                $event->sheet->columnWidth('J', 12); // stock
                $event->sheet->columnWidth('K', 10); // status

                /** table head */
                $event->sheet->setBold('A3:K3');
                $event->sheet->horizontalAlign('A3:K3', 'center');
                $event->sheet->verticalAlign('A3:K3', 'center');
                $event->sheet->setCellValue('A3', mb_strtoupper(__('product.sku')));
                $event->sheet->setCellValue('B3', mb_strtoupper(__('product.product')));
                $event->sheet->setCellValue('C3', mb_strtoupper(__('category.category')));
                $event->sheet->setCellValue('D3', mb_strtoupper(__('product.sub_category')));
                $event->sheet->setCellValue('E3', mb_strtoupper(__('brand.brand')));
                $event->sheet->setCellValue('F3', mb_strtoupper(__('product.cost')));
                $event->sheet->setCellValue('G3', 'PRECIO 1');
                $event->sheet->setCellValue('H3', 'PRECIO 2');
                $event->sheet->setCellValue('I3', 'PRECIO 3');
                $event->sheet->setCellValue('J3', mb_strtoupper(__('kardex.stock')));
                $event->sheet->setCellValue('K3', mb_strtoupper(__('product.status')));
                
                /** table body */
                $row = 4;
                foreach($this->price_lists as $pl) {
                    $event->sheet->setCellValue('A'. $row, $pl->sku);
                    $event->sheet->setCellValue('B'. $row, $pl->product);
                    $event->sheet->setCellValue('C'. $row, $pl->category);
                    $event->sheet->setCellValue('D'. $row, $pl->sub_category);
                    $event->sheet->setCellValue('E'. $row, $pl->brand);
                    $event->sheet->setCellValue('F'. $row, $pl->cost);
                    $event->sheet->setCellValue('G'. $row, $pl->price_1);
                    $event->sheet->setCellValue('H'. $row, $pl->price_2);
                    $event->sheet->setCellValue('I'. $row, $pl->price_3);
                    $event->sheet->setCellValue('J'. $row, $pl->stock);
                    $event->sheet->setCellValue('K'. $row, mb_strtoupper(__('product.status_'. $pl->status)));

                    $row ++;
                }
                $row --;

                /** set font size and family, set borders */
    			$event->sheet->setFontSize('A3:K'. $row, 10);
                $event->sheet->setFormat('A4:E'. $row, '@');
                $event->sheet->setFormat('F4:H'. $row, '$ #,##0.00_-');
                $event->sheet->setFormat('J4:J'. $row, '0.00');
                $event->sheet->setFormat('K4:K'. $row, '@');
                $event->sheet->setAllBorders('A3:K'. $row, 'thin');
                $event->sheet->setFontFamily('A1:K'. $row, 'Calibri');
            },
        ];
    }
}