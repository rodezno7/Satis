<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class InputOutput implements WithEvents, WithTitle
{
    private $categories;
    private $no_categories;
    private $business_name;
    private $start_date;

    /**
     * Constructor.
     * 
     * @param collect $categories
     * @param collect $no_categories
     * @param string $business_name
     * @param string $start_date
     * @param string $end_date
     * @return void
     * @author
     */
    public function __construct($categories, $no_categories, $business_name, $start_date, $end_date)
    {
    	$this->categories = $categories;
        $this->no_categories = $no_categories;
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
    	return __('report.input_output_report');
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

                /** Header */
                $event->sheet->mergeCells('A1:L1');
                $event->sheet->mergeCells('A2:L2');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:L1', 'center');
                $event->sheet->horizontalAlign('A1:L2', "center");
                $event->sheet->setBold('A1:L2');
                $event->sheet->setFontSize('A1:L1', 14);
                $event->sheet->setFontSize('A2:L2', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper(__('report.input_output_report')) ." ". mb_strtoupper(strtoupper(__('accounting.from_date')) ." ". $this->start_date ." ". strtoupper(__('accounting.to_date')) ." ". $this->end_date));

                /** Column width and font align */
                $event->sheet->columnWidth('A', 13);
                $event->sheet->columnWidth('B', 35);
                $event->sheet->columnWidth('C', 10);
                $event->sheet->columnWidth('D', 10);
                $event->sheet->columnWidth('E', 11);
                $event->sheet->columnWidth('F', 10);
                $event->sheet->columnWidth('G', 14);
                $event->sheet->columnWidth('H', 10);
                $event->sheet->columnWidth('I', 11);
                $event->sheet->columnWidth('J', 10);
                $event->sheet->columnWidth('K', 14);
                $event->sheet->columnWidth('L', 10);

                /** table head */
                $event->sheet->setBold('A3:L4');
                $event->sheet->horizontalAlign('A3:L4', 'center');
                $event->sheet->verticalAlign('A3:L4', 'center');
                $event->sheet->mergeCells('A3:A4'); // sku
                $event->sheet->mergeCells('B3:B4'); // producto name
                $event->sheet->mergeCells('C3:C4'); // initial
                $event->sheet->mergeCells('D3:G3'); // inputs
                $event->sheet->mergeCells('H3:K3'); // inputs
                $event->sheet->mergeCells('L3:L4'); // stock
                $event->sheet->setCellValue('A3', mb_strtoupper(__('product.sku')));
                $event->sheet->setCellValue('B3', mb_strtoupper(__('product.product')));
                $event->sheet->setCellValue('C3', mb_strtoupper(__('lang_v1.initial')));
                $event->sheet->setCellValue('D3', mb_strtoupper(__('lang_v1.inputs')));
                $event->sheet->setCellValue('H3', mb_strtoupper(__('lang_v1.outputs')));
                $event->sheet->setCellValue('L3', mb_strtoupper(__('lang_v1.stock')));
                $event->sheet->setCellValue('D4', mb_strtoupper(__('purchase.purchases')));
                $event->sheet->setCellValue('E4', mb_strtoupper(__('lang_v1.transfers')));
                $event->sheet->setCellValue('F4', mb_strtoupper(__('stock_adjustment.adjustments')));
                $event->sheet->setCellValue('G4', mb_strtoupper(__('lang_v1.returns')));
                $event->sheet->setCellValue('H4', mb_strtoupper(__('sale.sells')));
                $event->sheet->setCellValue('I4', mb_strtoupper(__('lang_v1.transfers')));
                $event->sheet->setCellValue('J4', mb_strtoupper(__('stock_adjustment.adjustments')));
                $event->sheet->setCellValue('K4', mb_strtoupper(__('lang_v1.returns')));
                
                /** table body */
                $row = 5;
                $count = 0;
                $counter = 1;
                $category = null;
                
                /* category sub total variables */
                $cat_total_initial = 0; $cat_total_purchase = 0; $cat_total_in_transf = 0; $cat_total_in_adjust = 0; $cat_total_in_return = 0;
                $cat_total_sell = 0; $cat_total_out_transf = 0; $cat_total_out_adjust = 0; $cat_total_out_return = 0; $cat_total_stock = 0;

                /* total variables */
                $total_initial = 0; $total_purchase = 0; $total_in_transf = 0; $total_in_adjust = 0; $total_in_return = 0;
                $total_sell = 0; $total_out_transf = 0; $total_out_adjust = 0; $total_out_return = 0; $total_stock = 0;

                /** products by category */
                foreach($this->categories as $c) {
                    $cat_total_initial += $c->initial_inventory; $cat_total_purchase += $c->purchases; $cat_total_in_transf += $c->input_stock_adjustments;
                    $cat_total_in_adjust += $c->input_stock_adjustments; $cat_total_in_return += $c->sell_returns; $cat_total_sell += $c->sales;
                    $cat_total_out_transf += $c->sell_transfers; $cat_total_out_adjust += $c->output_stock_adjustments;
                    $cat_total_out_return += $c->purchase_returns; $cat_total_stock += $c->stock;

                    if ($c->category_id != $category) {
                        $count = $this->categories->where('category_id', $c->category_id)->count();
                    }

                    $event->sheet->horizontalAlign('A'. $row, 'left');
                    $event->sheet->setCellValue('A'. $row, $c->sku);
                    $event->sheet->setCellValue('B'. $row, $c->product_name);
                    $event->sheet->setCellValue('C'. $row, $c->initial_inventory);
                    $event->sheet->setCellValue('D'. $row, $c->purchases);
                    $event->sheet->setCellValue('E'. $row, $c->purchase_transfers);
                    $event->sheet->setCellValue('F'. $row, $c->input_stock_adjustments);
                    $event->sheet->setCellValue('G'. $row, $c->sell_returns);
                    $event->sheet->setCellValue('H'. $row, $c->sales);
                    $event->sheet->setCellValue('I'. $row, $c->sell_transfers);
                    $event->sheet->setCellValue('J'. $row, $c->output_stock_adjustments);
                    $event->sheet->setCellValue('K'. $row, $c->purchase_returns);
                    $event->sheet->setCellValue('L'. $row, $c->stock);

                    $row ++;

                    if($count == $counter) {
                        $event->sheet->mergeCells('A'. $row .':B'. $row);
                        $event->sheet->horizontalAlign('A'. $row .':B'. $row, 'center');
                        $event->sheet->setBold('A'. $row .':L'. $row);
                        $event->sheet->getDelegate()
                            ->getStyle('A'. $row .':L' . $row)
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('eeeeee');
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('sale.total') ." ". $c->category_name));
                        $event->sheet->setCellValue('C'. $row, $cat_total_initial);
                        $event->sheet->setCellValue('D'. $row, $cat_total_purchase);
                        $event->sheet->setCellValue('E'. $row, $cat_total_in_transf);
                        $event->sheet->setCellValue('F'. $row, $cat_total_in_adjust);
                        $event->sheet->setCellValue('G'. $row, $cat_total_in_return);
                        $event->sheet->setCellValue('H'. $row, $cat_total_sell);
                        $event->sheet->setCellValue('I'. $row, $cat_total_out_transf);
                        $event->sheet->setCellValue('J'. $row, $cat_total_out_adjust);
                        $event->sheet->setCellValue('K'. $row, $cat_total_out_return);
                        $event->sheet->setCellValue('L'. $row, $cat_total_stock);

                        $row ++;
                        $event->sheet->mergeCells('A'. $row .':L'. $row);
                        $row ++;

                        /* reset counter */
                        $counter = 1;

                        /* sum to totals */
                        $total_initial += $cat_total_initial; $total_purchase += $cat_total_purchase; $total_in_transf += $cat_total_in_transf;
                        $total_in_adjust += $cat_total_in_adjust; $total_in_return += $cat_total_in_return; $total_sell += $cat_total_sell;
                        $total_out_transf += $cat_total_out_transf; $total_out_adjust += $cat_total_out_adjust;
                        $total_out_return += $cat_total_out_return; $total_stock += $cat_total_stock;

                        /* reset category totals */
                        $cat_total_initial = 0; $cat_total_purchase = 0; $cat_total_in_transf = 0; $cat_total_in_adjust = 0; $cat_total_in_return =  0;
                        $cat_total_sell = 0; $cat_total_out_transf = 0; $cat_total_out_adjust = 0; $cat_total_out_return = 0; $cat_total_stock = 0;
                    } else {
                        $counter ++;
                    }

                    $category = $c->category_id;
                }

                /** No category products */
                foreach($this->no_categories as $c) {
                    $cat_total_initial += $c->initial_inventory; $cat_total_purchase += $c->purchases; $cat_total_in_transf += $c->input_stock_adjustments;
                    $cat_total_in_adjust += $c->input_stock_adjustments; $cat_total_in_return += $c->sell_returns; $cat_total_sell += $c->sales;
                    $cat_total_out_transf += $c->sell_transfers; $cat_total_out_adjust += $c->output_stock_adjustments;
                    $cat_total_out_return += $c->purchase_returns; $cat_total_stock += $c->stock;

                    if ($c->category_id != $category) {
                        $count = $this->no_categories->count();
                    }

                    $event->sheet->horizontalAlign('A'. $row, 'left');
                    $event->sheet->setCellValue('A'. $row, $c->sku);
                    $event->sheet->setCellValue('B'. $row, $c->product_name);
                    $event->sheet->setCellValue('C'. $row, $c->initial_inventory);
                    $event->sheet->setCellValue('D'. $row, $c->purchases);
                    $event->sheet->setCellValue('E'. $row, $c->purchase_transfers);
                    $event->sheet->setCellValue('F'. $row, $c->input_stock_adjustments);
                    $event->sheet->setCellValue('G'. $row, $c->sell_returns);
                    $event->sheet->setCellValue('H'. $row, $c->sales);
                    $event->sheet->setCellValue('I'. $row, $c->sell_transfers);
                    $event->sheet->setCellValue('J'. $row, $c->output_stock_adjustments);
                    $event->sheet->setCellValue('K'. $row, $c->purchase_returns);
                    $event->sheet->setCellValue('L'. $row, $c->stock);

                    $row ++;

                    if($count == $counter) {
                        $event->sheet->mergeCells('A'. $row .':B'. $row);
                        $event->sheet->horizontalAlign('A'. $row .':B'. $row, 'center');
                        $event->sheet->setBold('A'. $row .':L'. $row);
                        $event->sheet->getDelegate()
                            ->getStyle('A'. $row .':L' . $row)
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('eeeeee');
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('sale.total') ." ". __('category.no_category')));
                        $event->sheet->setCellValue('C'. $row, $cat_total_initial);
                        $event->sheet->setCellValue('D'. $row, $cat_total_purchase);
                        $event->sheet->setCellValue('E'. $row, $cat_total_in_transf);
                        $event->sheet->setCellValue('F'. $row, $cat_total_in_adjust);
                        $event->sheet->setCellValue('G'. $row, $cat_total_in_return);
                        $event->sheet->setCellValue('H'. $row, $cat_total_sell);
                        $event->sheet->setCellValue('I'. $row, $cat_total_out_transf);
                        $event->sheet->setCellValue('J'. $row, $cat_total_out_adjust);
                        $event->sheet->setCellValue('K'. $row, $cat_total_out_return);
                        $event->sheet->setCellValue('L'. $row, $cat_total_stock);

                        $row ++;
                        $event->sheet->mergeCells('A'. $row .':L'. $row);
                        $row ++;

                        /* reset counter */
                        $counter = 1;

                        /* sum to totals */
                        $total_initial += $cat_total_initial; $total_purchase += $cat_total_purchase; $total_in_transf += $cat_total_in_transf;
                        $total_in_adjust += $cat_total_in_adjust; $total_in_return += $cat_total_in_return; $total_sell += $cat_total_sell;
                        $total_out_transf += $cat_total_out_transf; $total_out_adjust += $cat_total_out_adjust;
                        $total_out_return += $cat_total_out_return; $total_stock += $cat_total_stock;

                        /* reset category totals */
                        $cat_total_initial = 0; $cat_total_purchase = 0; $cat_total_in_transf = 0; $cat_total_in_adjust = 0; $cat_total_in_return =  0;
                        $cat_total_sell = 0; $cat_total_out_transf = 0; $cat_total_out_adjust = 0; $cat_total_out_return = 0; $cat_total_stock = 0;
                    } else {
                        $counter ++;
                    }
                }
                /** table foot */
                $event->sheet->mergeCells('A'. $row .':B'. $row);
                $event->sheet->setBold('A'. $row .':L'. $row);
                $event->sheet->getDelegate()
                    ->getStyle('A'. $row .':L' . $row)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('aaaaaa');
    			$event->sheet->setFontSize('A3:L'. ($row), 10);
                $event->sheet->horizontalAlign('A'. $row .':B'. $row, 'center');
                $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('report.grand_total')));
                $event->sheet->setCellValue('C'. $row, $total_initial);
                $event->sheet->setCellValue('D'. $row, $total_purchase);
                $event->sheet->setCellValue('E'. $row, $total_in_transf);
                $event->sheet->setCellValue('F'. $row, $total_in_adjust);
                $event->sheet->setCellValue('G'. $row, $total_in_return);
                $event->sheet->setCellValue('H'. $row, $total_sell);
                $event->sheet->setCellValue('I'. $row, $total_out_transf);
                $event->sheet->setCellValue('J'. $row, $total_out_adjust);
                $event->sheet->setCellValue('K'. $row, $total_out_return);
                $event->sheet->setCellValue('L'. $row, $total_stock);

                /** set font size and family, set borders */
    			$event->sheet->setFontSize('A3:L'. ($row), 10);
                $event->sheet->setFormat('A5:B'. $row, '@');
                $event->sheet->setFormat('C5:L'. $row, '0.0');
                $event->sheet->setAllBorders('A3:L'. ($row), 'thin');
                $event->sheet->setFontFamily('A1:L'. ($row), 'Calibri');
            },
        ];
    }
}
