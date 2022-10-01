<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ListPriceReport implements WithEvents, WithTitle
{
    private $products;
    private $business_name;
    private $list_prices;

    /**
     * Constructor.
     * 
     * @param Array $products
     * @param string $business_name
     * @param Array $list_prices
     * @return void
     * @author
     */
    public function __construct($products, $business_name, $list_prices)
    {
    	$this->products = $products;
        $this->business_name = $business_name;
        $this->list_prices = $list_prices;
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
                $column = [
                    1 => 'E', 2 => 'F', 3 => 'G', 4 => 'H', 5 => 'I',
                    6 => 'J', 7 => 'K', 8 => 'L', 9 => 'M', 10 => 'N',
                    11 => 'O', 12 => 'P', 13 => 'Q', 14 => 'R', 15 => 'S',
                    16 => 'T', 17 => 'U', 18 => 'V', 19 => 'W', 20 => 'X',
                    21 => 'Y', 21 => 'Z' ];

                $pt = count($this->list_prices);
                $lc = $column[$pt];

                /** General setup */
    			$event->sheet->setOrientation("landscape");
                $event->sheet->setShowGridlines(false);

                /** Header */
                $event->sheet->mergeCells('A1:'. $lc .'1');
                $event->sheet->mergeCells('A2:'. $lc .'2');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:I1', 'center');
                $event->sheet->horizontalAlign('A1:I2', "center");
                $event->sheet->setBold('A1:I2');
                $event->sheet->setFontSize('A1:I1', 14);
                $event->sheet->setFontSize('A2:I2', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper(__('report.list_price_report')));

                /** Column width and font align */
                $event->sheet->columnWidth('A', 10); // sku
                $event->sheet->columnWidth('B', 25); // product name
                $event->sheet->columnWidth('C', 15); // brand
                $event->sheet->columnWidth('D', 15); // category
                $event->sheet->columnWidth('E', 15); // default price
                for ($i = 0; $i < $pt; $i++) { 
                    $event->sheet->columnWidth($column[$i +1], 15);
                }

                /** table head */
                $event->sheet->setBold('A3:'. $lc .'3');
                $event->sheet->horizontalAlign('A3:'. $lc .'3', 'center');
                //$event->sheet->horizontalAlign('I3', 'left');
                $event->sheet->verticalAlign('A3:'. $lc .'3', 'center');
                $event->sheet->setCellValue('A3', mb_strtoupper(__('product.sku')));
                $event->sheet->setCellValue('B3', mb_strtoupper(__('product.product')));
                $event->sheet->setCellValue('C3', mb_strtoupper(__('brand.brand')));
                $event->sheet->setCellValue('D3', mb_strtoupper(__('category.category')));
                $event->sheet->setCellValue('E3', mb_strtoupper(__('lang_v1.default')));
                for ($i = 0; $i < $pt; $i++) {
                    $event->sheet->setCellValue($column[$i +1] .'3', $this->list_prices[$i]['name']);
                }
                
                /** table body */
                $row = 4;
                foreach($this->products as $p) {
                    $event->sheet->setCellValue('A'. $row, $p['sku']);
                    $event->sheet->setCellValue('B'. $row, $p['product_name']);
                    $event->sheet->setCellValue('C'. $row, $p['category_name']);
                    $event->sheet->setCellValue('D'. $row, $p['default_price']);
                    for ($i = 0; $i < $pt; $i++) {
                        $event->sheet->setCellValue($column[$i +1]. $row, $p[$this->list_prices[$i]['name']] > 0 ? $p[$this->list_prices[$i]['name']] : "");
                    }

                    $row ++;
                }
                $row --;

                /** set font size and family, set borders */
    			$event->sheet->setFontSize('A3:'. $lc. $row, 10);
                $event->sheet->setFormat('A4:C'. $row, '@');
                $event->sheet->setFormat('D4:'. $lc. $row, '$ #,##0.00_-'); // current format
                $event->sheet->setAllBorders('A3:'. $lc. $row, 'thin');
                $event->sheet->setFontFamily('A1:'. $lc. $row, 'Calibri');
            },
        ];
    }
}
