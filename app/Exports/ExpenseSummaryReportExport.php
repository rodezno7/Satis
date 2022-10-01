<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExpenseSummaryReportExport implements WithEvents, WithTitle
{
    private $expenses;
    private $business;
    private $location;

    /**
     * Constructor.
     * 
     * @param  array  $sales
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($expenses, $business_name, $report_name)
    {
    	$this->expenses = $expenses;
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
    	return __('report.expense_report');
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
                $items = count($this->expenses) + 4;
                $expenses = $this->expenses;

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->setFontFamily('A1:N'.$items, 'Calibri');
    			$event->sheet->setFontSize('A1:N'.$items, 10);
                
                /** Column width and font align */
                $event->sheet->horizontalAlign('A1:N2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A4:N4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->columnWidth('A', 35);
                $event->sheet->columnWidth('B', 12);
                $event->sheet->columnWidth('C', 12);
                $event->sheet->columnWidth('D', 12);
                $event->sheet->columnWidth('E', 12);
                $event->sheet->columnWidth('F', 12);
                $event->sheet->columnWidth('G', 12);
                $event->sheet->columnWidth('H', 12);
                $event->sheet->columnWidth('I', 12);
                $event->sheet->columnWidth('J', 12);
                $event->sheet->columnWidth('K', 12);
                $event->sheet->columnWidth('L', 12);
                $event->sheet->columnWidth('M', 12);
                $event->sheet->columnWidth('N', 15);

                /** Apply title Font size and font bold */
                $event->sheet->getDelegate()->getStyle('A1:N2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:N2')->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle('A4:N4')->getFont()->setBold(true);

                /** Apply column font format */
                $event->sheet->setFormat('A5:A' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('B5:N' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                /** business name */
    			$event->sheet->mergeCells('A1:N1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setFormat('A1:F2', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                /** report name */
    			$event->sheet->mergeCells('A2:N2');
                $event->sheet->setCellValue('A2', mb_strtoupper($this->report_name));
                
                /** table head */
                $event->sheet->setCellValue('A4', mb_strtoupper(__('lang_v1.description')));
                $event->sheet->setCellValue('B4', mb_strtoupper(__('report.january')));
                $event->sheet->setCellValue('C4', mb_strtoupper(__('report.february')));
                $event->sheet->setCellValue('D4', mb_strtoupper(__('report.march')));
                $event->sheet->setCellValue('E4', mb_strtoupper(__('report.april')));
                $event->sheet->setCellValue('F4', mb_strtoupper(__('report.may')));
                $event->sheet->setCellValue('G4', mb_strtoupper(__('report.jun')));
                $event->sheet->setCellValue('H4', mb_strtoupper(__('report.july')));
                $event->sheet->setCellValue('I4', mb_strtoupper(__('report.august')));
                $event->sheet->setCellValue('J4', mb_strtoupper(__('report.september')));
                $event->sheet->setCellValue('K4', mb_strtoupper(__('report.october')));
                $event->sheet->setCellValue('L4', mb_strtoupper(__('report.november')));
                $event->sheet->setCellValue('M4', mb_strtoupper(__('report.december')));
                $event->sheet->setCellValue('N4', mb_strtoupper(__('sale.total')));

                /** table body */
                $count = 5;
                $jan = 0; $feb = 0; $mar = 0; $apr = 0; $may = 0; $jun = 0; $jul = 0;
                $aug = 0; $sep = 0; $oct = 0; $nov = 0; $dec = 0; $total = 0;
                foreach($expenses as $e){
                    $event->sheet->setCellValue('A'. $count, $e->description);
                    $event->sheet->setCellValue('B'. $count, $e->jan); 
                    $event->sheet->setCellValue('C'. $count, $e->feb);
                    $event->sheet->setCellValue('D'. $count, $e->mar);
                    $event->sheet->setCellValue('E'. $count, $e->apr);
                    $event->sheet->setCellValue('F'. $count, $e->may);
                    $event->sheet->setCellValue('G'. $count, $e->jun);
                    $event->sheet->setCellValue('H'. $count, $e->jul);
                    $event->sheet->setCellValue('I'. $count, $e->aug);
                    $event->sheet->setCellValue('J'. $count, $e->sep);
                    $event->sheet->setCellValue('K'. $count, $e->oct);
                    $event->sheet->setCellValue('L'. $count, $e->nov);
                    $event->sheet->setCellValue('M'. $count, $e->dec);
                    $event->sheet->setCellValue('N'. $count, $e->total);

                    $jan += $e->jan; $feb += $e->feb; $mar += $e->mar; $apr += $e->apr;
                    $may += $e->may; $jun += $e->jun; $jul += $e->jul; $aug += $e->aug;
                    $sep += $e->sep; $oct += $e->oct; $nov += $e->nov; $dec += $e->dec;
                    $total += $e->total;
                    $count++;
                }

                /** table footer */
                $event->sheet->horizontalAlign('A' . $count . ':N' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':N' . $count)->getFont()->setBold(true);
                $event->sheet->setFormat('A' . $count, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('B' . $count . ':N' . $count, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setCellValue('A' . $count, mb_strtoupper(__('accounting.totals')));
                $event->sheet->setCellValue('B'. $count, $jan); 
                $event->sheet->setCellValue('C'. $count, $feb);
                $event->sheet->setCellValue('D'. $count, $mar);
                $event->sheet->setCellValue('E'. $count, $apr);
                $event->sheet->setCellValue('F'. $count, $may);
                $event->sheet->setCellValue('G'. $count, $jun);
                $event->sheet->setCellValue('H'. $count, $jul);
                $event->sheet->setCellValue('I'. $count, $aug);
                $event->sheet->setCellValue('J'. $count, $sep);
                $event->sheet->setCellValue('K'. $count, $oct);
                $event->sheet->setCellValue('L'. $count, $nov);
                $event->sheet->setCellValue('M'. $count, $dec);
                $event->sheet->setCellValue('N'. $count, $total);
            },
        ];
    }
}
