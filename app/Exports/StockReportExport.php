<?php

namespace App\Exports;

use App\Business;
use App\Utils\TransactionUtil;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StockReportExport implements WithEvents, WithTitle
{
    private $products;
    private $business;
    private $start;
    private $end;
    private $product_settings;
    private $transactionUtil;

    /**
     * Constructor.
     * 
     * @param  array  $products
     * @param  \App\Business  $business
     * @param  string  $start
     * @param  string  $end
     * @param  array  $product_settings
     * @param  \App\Utils\TransactionUtil  $transactionUtil
     * @return void
     */
    public function __construct($products, Business $business, $start, $end, $product_settings, TransactionUtil $transactionUtil)
    {
    	$this->products = $products;
        $this->business = $business;
        $this->start = $start;
        $this->end = $end;
        $this->product_settings = $product_settings;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.stock_report');
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
                $items = count($this->products) + 6;
                $products = $this->products;

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->setFontFamily('A1:K'.$items, 'Calibri');
    			$event->sheet->setFontSize('A1:K'.$items, 10);
                
                /** Column width and font align */
                $event->sheet->horizontalAlign('A1:K4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A6:K6', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->columnWidth('A', 15.00); //sku
                $event->sheet->columnWidth('B', 58.32); //product
                $event->sheet->columnWidth('C', 20.46); //category
                $event->sheet->columnWidth('D', 20.46); //subcategory
                $event->sheet->columnWidth('E', 20.46); //brand
                $event->sheet->columnWidth('F', 20.46); //unit cost
                $event->sheet->columnWidth('G', 20.46); //unit price
                $event->sheet->columnWidth('H', 20.46); //stock
                $event->sheet->columnWidth('I', 20.46); //vld stock
                $event->sheet->columnWidth('J', 20.46); //total value
                $event->sheet->columnWidth('K', 20.46); //units sold

                /** Apply title Font size and font bold */
                $event->sheet->getDelegate()->getStyle('A1:J2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A6:K6')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:J1')->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle('A2:J2')->getFont()->setSize(12);

                /** Apply column font format */
                $event->sheet->setFormat('A5:E' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('F5:G' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setFormat('H5:H' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
                $event->sheet->setFormat('I5:I' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setFormat('J5:K' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

                /** business name */
    			$event->sheet->mergeCells('A1:K1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business->business_full_name));
                $event->sheet->setFormat('A1:K4', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                /** report name */
    			$event->sheet->mergeCells('A2:K2');
                $event->sheet->setCellValue('A2', mb_strtoupper(__('report.stock_report')));

                /** report name */
    			$event->sheet->mergeCells('A3:K3');
                $event->sheet->setCellValue('A3', mb_strtoupper(__('report.date_range_report', ['from' => $this->transactionUtil->format_date($this->start), 'to' => $this->transactionUtil->format_date($this->end)])));

                /** range date */
    			$event->sheet->mergeCells('A4:K4');
                $event->sheet->setCellValue('A4', mb_strtoupper(__('report.amounts_in', ['currency' => __('report.' . $this->business->currency->currency), 'code' => $this->business->currency->code])));
                
                /** table head */
                $event->sheet->setCellValue('A6', mb_strtoupper(__('product.sku')));
                $event->sheet->setCellValue('B6', mb_strtoupper(__('product.product')));
                $event->sheet->setCellValue('C6', mb_strtoupper(__('category.category')));
                $event->sheet->setCellValue('D6', mb_strtoupper(__('product.sub_category')));
                $event->sheet->setCellValue('E6', mb_strtoupper(__('brand.brand')));
                $event->sheet->setCellValue('F6', mb_strtoupper(__('product.unit_cost')));
                $event->sheet->setCellValue('G6', mb_strtoupper(__('sale.unit_price')));
                $event->sheet->setCellValue('H6', mb_strtoupper(__('report.current_stock')));
                $event->sheet->setCellValue('I6', mb_strtoupper(__('report.vld_stock')));
                $event->sheet->setCellValue('J6', mb_strtoupper(__('report.value_total')));
                $event->sheet->setCellValue('K6', mb_strtoupper(__('report.total_unit_sold')));

                /** table body */
                $count = 7;
                $total_stock = 0;
                $total_vld_stock = 0;
                $total_sold = 0;
                $total_value = 0;
                foreach($products as $p){
                    $event->sheet->setCellValue('A'. $count, $p->sku);
                    $event->sheet->setCellValue('B'. $count, $p->product);
                    $event->sheet->setCellValue('C'. $count, $p->category);
                    $event->sheet->setCellValue('D'. $count, $p->sub_category);
                    $event->sheet->setCellValue('E'. $count, $p->brand);
                    $event->sheet->setCellValue('F'. $count, $p->unit_cost);
                    $event->sheet->setCellValue('G'. $count, $p->unit_price);
                    $event->sheet->setCellValue('H'. $count, $p->stock);
                    $event->sheet->setCellValue('I'. $count, $p->vld_stock);
                    $event->sheet->setCellValue('J'. $count, $p->stock * $p->unit_cost);
                    $event->sheet->setCellValue('K'. $count, $p->total_sold);

                    $total_stock += $p->stock;
                    $total_vld_stock += $p->vld_stock;
                    $total_sold += $p->total_sold;
                    $total_value += ($p->stock * $p->unit_cost);
                    $count++;
                }

                /** table footer */
                $event->sheet->horizontalAlign('A' . $count . ':G' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':J' . $count)->getFont()->setBold(true);
                $event->sheet->setFormat('H' . $count . ':H' . $count, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
                $event->sheet->setFormat('I' . $count . ':I' . $count, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setFormat('J' . $count . ':J' . $count, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
    			$event->sheet->mergeCells('A' . $count . ':G' . $count);
                $event->sheet->setCellValue('A' . $count, mb_strtoupper(__('accounting.totals')));
                $event->sheet->setCellValue('H' . $count, $total_stock);
                $event->sheet->setCellValue('I' . $count, $total_vld_stock);
                $event->sheet->setCellValue('J' . $count, $total_value);
                $event->sheet->setCellValue('K' . $count, $total_sold);
            },
        ];
    }
}
