<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AccountsReceivableReportExport implements WithEvents, WithTitle
{
    private $transactions;
    private $business_name;
    private $report_name;
    private $final_totals;
    private $transactionUtil;

    /**
     * Constructor.
     * 
     * @param  array  $transactions
     * @param string $business_name
     * @param string $report_name
     * @param App\Util\TransactionUtil $transactionUtil;
     * @return void
     */
    public function __construct($transactions, $business_name, $report_name, $final_totals, $transactionUtil)
    {
    	$this->transactions = $transactions;
        $this->business_name = $business_name;
        $this->report_name = $report_name;
        $this->final_totals = $final_totals;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('cxc.cxc');
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
                
                /** Column width and font align */
                $event->sheet->horizontalAlign('A1:L2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->columnWidth('A', 15); // correlative
                $event->sheet->columnWidth('B', 12); // date
                $event->sheet->columnWidth('C', 12); // expire date
                $event->sheet->columnWidth('D', 8); // days
                $event->sheet->columnWidth('E', 15); // total
                $event->sheet->columnWidth('F', 15); // payments
                $event->sheet->columnWidth('G', 15); // 30 days
                $event->sheet->columnWidth('H', 15); // 60 days
                $event->sheet->columnWidth('I', 15); // 90 days
                $event->sheet->columnWidth('J', 15); // 120 days
                $event->sheet->columnWidth('K', 15); // more than 120 days
                $event->sheet->columnWidth('L', 15); // total
                
                /** business name */
                $event->sheet->getDelegate()->getStyle('A1:L1')->getFont()->setBold(true);
                $event->sheet->horizontalAlign('A1:L1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->setFormat('A1:L1', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
    			$event->sheet->mergeCells('A1:L1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));

                /** report name */
                $event->sheet->getDelegate()->getStyle('A2:L2')->getFont()->setBold(true);
                $event->sheet->horizontalAlign('A2:L2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->setFormat('A2:L2', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
    			$event->sheet->mergeCells('A2:L2');
                $event->sheet->setCellValue('A2', mb_strtoupper($this->report_name));

                /** table head */
                $event->sheet->getDelegate()->getStyle('A3:L3')->getFont()->setBold(true);
                $event->sheet->horizontalAlign('A3:L3', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->setCellValue('A3', mb_strtoupper(__('lang_v1.correlative')));
                $event->sheet->setCellValue('B3', mb_strtoupper(__('messages.date')));
                $event->sheet->setCellValue('C3', mb_strtoupper(__('contact.expire_date')));
                $event->sheet->setCellValue('D3', mb_strtoupper(__('lang_v1.days')));
                $event->sheet->setCellValue('E3', mb_strtoupper(__('sale.total')));
                $event->sheet->setCellValue('F3', mb_strtoupper(__('payment.payments')));
                $event->sheet->setCellValue('G3', mb_strtoupper(__('payment.30_days')));
                $event->sheet->setCellValue('H3', mb_strtoupper(__('payment.60_days')));
                $event->sheet->setCellValue('I3', mb_strtoupper(__('payment.90_days')));
                $event->sheet->setCellValue('J3', mb_strtoupper(__('payment.120_days')));
                $event->sheet->setCellValue('K3', mb_strtoupper(__('payment.more_than_120')));
                $event->sheet->setCellValue('L3', mb_strtoupper(__('sale.total')));

                /** set thead background color */
                $event->sheet->styleCells('A3:L3', ['font' => ['color' => ['argb' => 'ffffff']]]);
                $event->sheet->getDelegate()->getStyle('A3:L3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('000000');

                /** freeze first four rows */
                $event->sheet->getDelegate()->freezePane('A4');

                /** table body */
                $id = 0;
                $row = 4;
                $count = 1;
                $counter = 0;
                $total_days_30 = 0;
                $total_days_60 = 0;
                $total_days_90 = 0;
                $total_days_120 = 0;
                $total_more_than_120 = 0;
                $totals = 0;
                foreach($transactions as $t){
                    $total = $t->days_30 + $t->days_60 + $t->days_90 +$t->days_120 + $t->more_than_120;
                    $total_days_30 += $t->days_30;
                    $total_days_60 += $t->days_60;
                    $total_days_90 += $t->days_90;
                    $total_days_120 += $t->days_120;
                    $total_more_than_120 += $t->more_than_120;
                    $totals += $total;

                    if($id != $t->customer_id){
                        $counter = $transactions->where('customer_id', $t->customer_id)->count();
    			        $event->sheet->mergeCells('A'. $row .':L'. $row);
                        $event->sheet->setCellValue('A'. $row, "");
                        
                        $row ++;
                        $event->sheet->mergeCells('A'. $row . ':L'. $row);
                        $event->sheet->getDelegate()->getStyle('A'. $row)->getFont()->setBold(true);
                        $event->sheet->setCellValue('A'. $row, $t->customer_name);
                        $row ++;
                    }

                    $event->sheet->styleCells('G'. $row, ['font' => ['color' => ['argb' => '008000']]]); // set green color
                    $event->sheet->styleCells('H'. $row . ":K". $row, ['font' => ['color' => ['argb' => 'ff0000']]]); // set red color
                    $event->sheet->setCellValue('A'. $row, $t->correlative);
                    $event->sheet->setCellValue('B'. $row, $this->transactionUtil->format_date($t->transaction_date));
                    $event->sheet->setCellValue('C'. $row, $this->transactionUtil->format_date($t->expire_date));
                    $event->sheet->setCellValue('D'. $row, $t->days);
                    $event->sheet->setCellValue('E'. $row, $t->final_total);
                    $event->sheet->setCellValue('F'. $row, $t->payments > 0 ? $t->payments : 0);
                    $event->sheet->setCellValue('G'. $row, $t->days_30 > 0 ? $t->days_30 : "");
                    $event->sheet->setCellValue('H'. $row, $t->days_60 > 0 ? $t->days_60 : "");
                    $event->sheet->setCellValue('I'. $row, $t->days_90 > 0 ? $t->days_90 : "");
                    $event->sheet->setCellValue('J'. $row, $t->days_120 > 0 ? $t->days_120 : "");
                    $event->sheet->setCellValue('K'. $row, $t->more_than_120 > 0 ? $t->more_than_120 : "");
                    $event->sheet->setCellValue('L'. $row, $total);

                    if($counter == $count){
                        $row ++;
                        $event->sheet->mergeCells('A'. $row .':F'. $row);
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper(__("sale.total")));
                        $event->sheet->getDelegate()->getStyle('A'. $row .':L'. $row)->getFont()->setBold(true);
                        $event->sheet->styleCells('G'. $row, ['font' => ['color' => ['argb' => '008000']]]); // set green color
                        $event->sheet->styleCells('H'. $row . ":K". $row, ['font' => ['color' => ['argb' => 'ff0000']]]); // set red color
                        $event->sheet->setCellValue('G'. $row, $total_days_30);
                        $event->sheet->setCellValue('H'. $row, $total_days_60);
                        $event->sheet->setCellValue('I'. $row, $total_days_90);
                        $event->sheet->setCellValue('J'. $row, $total_days_120);
                        $event->sheet->setCellValue('K'. $row, $total_more_than_120);
                        $event->sheet->setCellValue('L'. $row, $totals);

                        $count = 1;
                        $total_days_30 = 0;
                        $total_days_60 = 0;
                        $total_days_90 = 0;
                        $total_days_120 = 0;
                        $total_more_than_120 = 0;
                        $totals = 0;
                    } else {
                        $count ++;
                    }

                    $row ++;
                    $id = $t->customer_id;
                }

                /** totals */
                $row ++;
                $event->sheet->mergeCells('A'. $row .':F'. $row);
                $event->sheet->setCellValue('A'. $row, mb_strtoupper(__("report.totals")));
                $event->sheet->getDelegate()->getStyle('A'. $row .':L'. $row)->getFont()->setBold(true);
                $event->sheet->styleCells('G'. $row, ['font' => ['color' => ['argb' => '008000']]]); // set green color
                $event->sheet->styleCells('H'. $row . ":K". $row, ['font' => ['color' => ['argb' => 'ff0000']]]); // set red color
                $event->sheet->setCellValue('G'. $row, $this->final_totals['days_30']);
                $event->sheet->setCellValue('H'. $row, $this->final_totals['days_60']);
                $event->sheet->setCellValue('I'. $row, $this->final_totals['days_90']);
                $event->sheet->setCellValue('J'. $row, $this->final_totals['days_120']);
                $event->sheet->setCellValue('K'. $row, $this->final_totals['more_than_120_days']);
                $event->sheet->setCellValue('L'. $row, $this->final_totals['totals']);

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->setFontFamily('A1:L'. $row, 'Calibri');
    			$event->sheet->setFontSize('A1:L'. $row, 10);
                $event->sheet->setFontSize('A1:L1', 12); // business name font size

                /** Apply column font format */
                $event->sheet->setFormat('A4:C' . $row, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('D4:D' . $row, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
                $event->sheet->setFormat('E4:L' . $row, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
            },
        ];
    }
}
