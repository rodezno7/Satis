<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesSummaryBySeller implements WithEvents, WithTitle
{
    private $transactions;

    /**
     * Constructor.
     * 
     * @param  array  $transactions
     * @return void
     */
    public function __construct($transactions)
    {
    	$this->transactions = $transactions;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.sales_summary_seller_report');
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
                $items = count($this->transactions) + 1;
                $transactions = $this->transactions;

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->setFontFamily('A1:N'.$items, 'Calibri');
    			$event->sheet->setFontSize('A1:N'.$items, 10);
                $event->sheet->getDelegate()->getStyle('A1:N1')->getFont()->setBold(true);

                /** Column width and font align */
                $event->sheet->horizontalAlign('A1:N1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->columnWidth('A', 12);
                $event->sheet->columnWidth('B', 20);
                $event->sheet->columnWidth('C', 10);
                $event->sheet->columnWidth('D', 10);
                $event->sheet->columnWidth('E', 10);
                $event->sheet->columnWidth('F', 8);
                $event->sheet->columnWidth('G', 10);
                $event->sheet->columnWidth('H', 10);
                $event->sheet->columnWidth('I', 10);
                $event->sheet->columnWidth('J', 25);
                $event->sheet->columnWidth('K', 15);
                $event->sheet->columnWidth('L', 10);
                $event->sheet->columnWidth('M', 10);
                $event->sheet->columnWidth('N', 10);
                
                /** Apply column font format */
                $event->sheet->setFormat('A2:E' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('F2:F' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
                $event->sheet->setFormat('G2:H' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setFormat('I2:I' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
                $event->sheet->setFormat('J2:K' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('L2:N' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                /** tablle head column name */
                $event->sheet->setCellValue('A1', 'PRODUCTO');
                $event->sheet->setCellValue('B1', 'DESCRIPCIÓN');
                $event->sheet->setCellValue('C1', 'FAMILIA');
                $event->sheet->setCellValue('D1', 'SUBFAMILIA');
                $event->sheet->setCellValue('E1', 'MARCA');
                $event->sheet->setCellValue('F1', 'CANTIDAD');
                $event->sheet->setCellValue('G1', 'PRECIO UNITARIO');
                $event->sheet->setCellValue('H1', 'VENTA TOTAL');
                $event->sheet->setCellValue('I1', 'COD. VENDEDOR');
                $event->sheet->setCellValue('J1', 'NOMBRE VENDEDOR');
                $event->sheet->setCellValue('K1', 'TIPO VENTA');
                $event->sheet->setCellValue('L1', 'COSTO');
                $event->sheet->setCellValue('M1', 'COSTO TOTAL');
                $event->sheet->setCellValue('N1', 'UTILIDAD');

                /** table body */
                $count = 2;
                foreach($transactions as $t){
                    $event->sheet->setCellValue('A' . $count, $t->sku);
                    $event->sheet->setCellValue('B' . $count, $t->product_name);
                    $event->sheet->setCellValue('C' . $count, $t->category);
                    $event->sheet->setCellValue('D' . $count, $t->sub_category);
                    $event->sheet->setCellValue('E' . $count, $t->brand);
                    $event->sheet->setCellValue('F' . $count, $t->quantity);
                    $event->sheet->setCellValue('G' . $count, $t->unit_price);
                    $event->sheet->setCellValue('H' . $count, $t->total_sale);
                    $event->sheet->setCellValue('I' . $count, $t->employee_id);
                    $event->sheet->setCellValue('J' . $count, $t->employee_name);
                    $event->sheet->setCellValue('K' . $count, __("messages." . $t->payment_condition));
                    $event->sheet->setCellValue('L' . $count, $t->cost);
                    $event->sheet->setCellValue('M' . $count, $t->total_cost);
                    $event->sheet->setCellValue('N' . $count, $t->utility);

                    $count++;
                }
            },
        ];
    }
}
