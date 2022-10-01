<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DispatchedProducts implements WithEvents, WithTitle
{
    private $products;
    private $dispatched_products;
    private $business_name;
    private $transaction_date;

    /**
     * Constructor.
     * 
     * @param collect $products
     * @param collect $dispatched_products
     * @param string $business_name
     * @param string $start_date
     * @param string $end_date
     * @return void
     * @author
     */
    public function __construct($products, $dispatched_products, $business_name, $start_date, $end_date)
    {
    	$this->products = $products;
        $this->dispatched_products = $dispatched_products;
        $this->business_name = $business_name;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.dispatched_products_report');
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
                    1 => 'C', 2 => 'D', 3 => 'E', 4 => 'F', 5 => 'G',
                    6 => 'H', 7 => 'I', 8 => 'J', 9 => 'K', 10 => 'L',
                    11 => 'M', 12 => 'N', 13 => 'O', 14 => 'P', 15 => 'Q',
                    16 => 'R', 17 => 'S', 18 => 'T', 19 => 'U', 20 => 'V',
                    21 => 'W', 22 => 'X', 23 => 'Y', 24 => 'Z' ];
                
                $tp = $this->products->count(); // products total
                $lp = $column[$tp]; // last product column
                $lc = $column[$tp + 2]; // last document column

                /** General setup */
    			$event->sheet->setOrientation("landscape");
                $event->sheet->setShowGridlines(false);

                /** Header */
                $event->sheet->mergeCells('A1:'. $lc .'1');
                $event->sheet->mergeCells('A2:'. $lc .'2');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:'. $lc .'3', 'center');
                $event->sheet->horizontalAlign('A1:'. $lc .'2', "center");
                $event->sheet->setBold('A1:'. $lc .'2');
                $event->sheet->setFontSize('A1:'. $lc .'1', 14);
                $event->sheet->setFontSize('A2:'. $lc .'2', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper(__('report.dispatched_products_report')) ." ". mb_strtoupper(strtoupper(__('accounting.from_date')) ." ". $this->start_date ." ". strtoupper(__('accounting.to_date')) ." ". $this->end_date));

                /** Columns width */
                $event->sheet->columnWidth('A', 35);
                $event->sheet->columnWidth('B', 35);
                for ($i = 0; $i < $this->products->count(); $i ++) { 
                    $event->sheet->columnWidth($column[$i +1], 10);
                }
                $event->sheet->columnWidth($column[$tp +1], 10);
                $event->sheet->columnWidth($column[$tp +2], 12);

                /** table head */
                $event->sheet->setBold('A3:'. $lc .'3');
                $event->sheet->rowHeight('3', 75);
                $event->sheet->horizontalAlign('A3:'. $lc .'3', 'center');
                $event->sheet->verticalAlign('A3:'. $lc .'3', 'center');
                $event->sheet->setCellValue('A3', mb_strtoupper(__('customer.customer')));
                $event->sheet->setCellValue('B3', mb_strtoupper(__('customer.seller')));

                for ($i = 0; $i < $this->products->count(); $i ++) { 
                    $event->sheet->setCellValue($column[$i +1] .'3', mb_strtoupper($this->products[$i]['product_name']));
                    $event->sheet->textRotation($column[$i +1] .'3', 90);
                    $event->sheet->wrapText($column[$i +1] .'3');
                }

                $event->sheet->setCellValue($column[$tp +1] .'3', mb_strtoupper(__('lang_v1.weight')));
                $event->sheet->setCellValue($column[$tp +2] .'3', mb_strtoupper(__('sale.total')));
                
                /** table body */
                $row = 4;
                $qty = 0;
                foreach ($this->products as $p) {
                    $total['product_'. $p->variation_id] = 0;
                }
                $total['weight'] = 0;
                $total['final'] = 0;

                /** products by category */
                foreach($this->dispatched_products as $dp) {
                    $event->sheet->setCellValue('A'. $row, $dp->customer_name);
                    $event->sheet->setCellValue('B'. $row, $dp->seller_name);
                    
                    for ($i = 0; $i < $this->products->count(); $i ++) {
                        $qty = $this->dispatched_products->where('customer_id', $dp->customer_id)->sum('product_'. $this->products[$i]['variation_id']);
                        $total['product_'. $this->products[$i]['variation_id']] += $qty;
                        $event->sheet->setCellValue($column[$i +1]. $row, $qty);
                    }

                    $event->sheet->setCellValue($column[$tp +1]. $row, $dp->weight_total);
                    $event->sheet->setCellValue($column[$tp +2]. $row, $dp->final_total);

                    $total['weight'] += $dp->weight_total;
                    $total['final'] += $dp->final_total;
                    $row ++;
                }
                
                /** table foot */
                $event->sheet->setBold('A'. $row .':'. $column[$tp +2]. $row);
                $event->sheet->getDelegate()
                    ->getStyle('A'. $row .':'. $column[$tp +2] . $row)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('cccccc');
                $event->sheet->horizontalAlign('A'. $row .':A'. $row, 'center');
                $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('report.grand_total')));

                for ($i = 0; $i < $this->products->count(); $i ++) { 
                    $event->sheet->setCellValue($column[$i +1]. $row, $total['product_'. $this->products[$i]['variation_id']]);
                }
                
                $event->sheet->setCellValue($column[$tp +1]. $row, $total['weight']);
                $event->sheet->setCellValue($column[$tp +2]. $row, $total['final']);

                /** set font size and family, set borders */
    			$event->sheet->setFontSize('A3:'. $lc . $row, 10);
                $event->sheet->setFormat('A3:A'. $row, '@'); // text format
                $event->sheet->setFormat('C3:'. $column[$tp +1] . $row, '#,##0.0'); // number format one decimal
                $event->sheet->setFormat($column[$tp +2]. '3:'. $column[$tp +2] . $row, '$ #,##0.00_-'); // currency format two decimals
                $event->sheet->setAllBorders('A3:'. $lc . $row, 'thin');
                $event->sheet->setFontFamily('A1:'. $lc . $row, 'Calibri');
            },
        ];
    }
}
