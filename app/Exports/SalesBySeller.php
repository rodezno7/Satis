<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesBySeller implements WithEvents, WithTitle
{
    private $transactions;
    private $start_date;
    private $end_date;
    private $business_name;

    /**
     * Constructor.
     * 
     * @param  array  $transactions
     * @return void
     */
    public function __construct($transactions, $start_date, $end_date, $business_name)
    {
    	$this->transactions = $transactions;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->business_name = $business_name;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.sales_by_seller_report');
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
                $transactions = $this->transactions;

                /** General setup */
    			$event->sheet->setOrientation("landscape");

                /** Header */
                $event->sheet->mergeCells('A1:D1');
                $event->sheet->mergeCells('A2:D2');
                $event->sheet->mergeCells('A3:D3');
                $event->sheet->horizontalAlign('A1:D3', "center");
                $event->sheet->setBold('A1:D3');
                $event->sheet->setFontSize('A1:D1', 14);
                $event->sheet->setFontSize('A2:D2', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper(__('report.sales_by_seller_report')));
                $event->sheet->setCellValue('A3', mb_strtoupper(strtoupper(__('accounting.from')) ." ". $this->start_date ." ". strtoupper(__('accounting.to')) ." ". $this->end_date));

                /** Column width and font align */
                $event->sheet->columnWidth('A', 15);
                $event->sheet->columnWidth('B', 30);
                $event->sheet->columnWidth('C', 20);
                $event->sheet->columnWidth('D', 20);
                
                /** table body */
                $row = 4;
                $count = 0;
                $counter = 1;
                $location_id = null;
                $total_amount = 0;
                $total_before_tax = 0;
                $currency_symbol = session('currency')['symbol'];

                foreach($transactions as $t){
                    $total_before_tax += $t->total_before_tax;
                    $total_amount += $t->total_amount;

                    if($location_id != $t->location_id){
                        /** get register count by location */
                        $count = $transactions->where('location_id', $t->location_id)->count();

                        /** location name */
                        $event->sheet->setFontSize('A'. $row, 12);
                        $event->sheet->setBold('A'. $row . ":D". $row);
                        $event->sheet->mergeCells('A'. $row . ":D". $row);
                        $event->sheet->horizontalAlign('A'. $row . ":D". $row, 'center');
                        $event->sheet->getDelegate()->getStyle('A' . $row . ':D' . $row)
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('d3d3d3');
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper($t->location_name));

                        $row ++;

                        /** header by location */
                        $event->sheet->setBold('A'. $row . ":D". $row);
                        $event->sheet->horizontalAlign('A'. $row . ":D". $row, 'center');
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('employees.seller_code')));
                        $event->sheet->setCellValue('B'. $row, mb_strtoupper(__('employees.seller_name')));
                        $event->sheet->setCellValue('C'. $row, mb_strtoupper(__('sale.total_no_vat')));
                        $event->sheet->setCellValue('D'. $row, mb_strtoupper(__('sale.total')));

                        $row ++;
                    }

                    $event->sheet->setFormat('C'. $row, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                    $event->sheet->setFormat('D'. $row, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                    $event->sheet->horizontalAlign('A'. $row, 'left');
                    $event->sheet->horizontalAlign('C'. $row .':D'. $row, 'right');

                    $event->sheet->setCellValue('A'. $row, $t->seller_code);
                    $event->sheet->setCellValue('B'. $row, $t->seller_name);
                    $event->sheet->setCellValue('C'. $row, $currency_symbol ." ". round($t->total_before_tax, 2));
                    $event->sheet->setCellValue('D'. $row, $currency_symbol ." ". round($t->total_amount, 2));
                    
                    $row ++;

                    if ($count == $counter){
                        /** Footer by location */
                        $event->sheet->setBold('A'. $row . ":D". $row);
                        $event->sheet->mergeCells('A'. $row .":B". $row);
                        $event->sheet->horizontalAlign('A'. $row, 'center');
                        $event->sheet->horizontalAlign('C'. $row . ":D". $row, 'right');
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('report.grand_total')));
                        $event->sheet->setCellValue('C'. $row, $currency_symbol ." ". round($total_before_tax, 2));
                        $event->sheet->setCellValue('D'. $row, $currency_symbol ." ". round($total_amount, 2));

                        $row ++;
                        $event->sheet->mergeCells('A'. $row . ":D". $row);
                        $row ++;

                        $counter = 1;
                        $total_before_tax = 0;
                        $total_amount = 0;
                    } else {
                        $counter ++;
                    }

                    $location_id = $t->location_id;
                }

                /** set font size and family */
    			$event->sheet->setFontSize('A3:D'. ($row -2), 10);
                $event->sheet->setAllBorders('A1:D'. ($row -2), 'thin');
                $event->sheet->setFontFamily('A1:D'. ($row -2), 'Calibri');
            },
        ];
    }
}
