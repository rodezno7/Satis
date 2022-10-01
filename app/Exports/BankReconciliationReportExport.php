<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BankReconciliationReportExport implements WithEvents, WithTitle
{
    private $transactions;
    private $business_name;
    private $report_name;
    private $bank_account_name;

    /**
     * Constructor.
     * 
     * @param  array  $sales
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($transactions, $business_name, $report_name, $bank_account_name, $transactionUtil)
    {
    	$this->transactions = $transactions;
        $this->business_name = $business_name;
        $this->report_name = $report_name;
        $this->bank_account_name = $bank_account_name;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('accounting.bank_reconciliation');
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
                $items = count($this->transactions) + 5;
                $transactions = $this->transactions;

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->setFontFamily('A1:E'.$items, 'Calibri');
    			$event->sheet->setFontSize('A1:E'.$items, 10);

                /** Columns style */
                $event->sheet->columnWidth('A', 12); // date
                $event->sheet->columnWidth('B', 15); // correlative
                $event->sheet->columnWidth('C', 40); // description
                $event->sheet->columnWidth('D', 15); // system
                $event->sheet->columnWidth('E', 15); // bank
                $event->sheet->setFormat('A5:C' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('D5:E' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                /** Business name */
                $event->sheet->horizontalAlign('A1:E1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:E1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:E1')->getFont()->setSize(12);
    			$event->sheet->mergeCells('A1:E1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));

                /** Report name */
                $event->sheet->horizontalAlign('A2:E2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:E2')->getFont()->setBold(true);
    			$event->sheet->mergeCells('A2:E2');
                $event->sheet->setCellValue('A2', mb_strtoupper($this->report_name));

                /** Bank account name */
                $event->sheet->horizontalAlign('A3:E3', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A3:E3')->getFont()->setBold(true);
                $event->sheet->mergeCells('A3:E3');
                $event->sheet->mergeCells('A4:E4');
                $event->sheet->setCellValue('A3', mb_strtoupper($this->bank_account_name));

                /** table head */
                $event->sheet->horizontalAlign('A5:H5', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A5:H5')->getFont()->setBold(true);
                $event->sheet->setCellValue('A5', mb_strtoupper(__('messages.date')));
                $event->sheet->setCellValue('B5', mb_strtoupper(__('lang_v1.reference')));
                $event->sheet->setCellValue('C5', mb_strtoupper(__('accounting.description')));
                $event->sheet->setCellValue('D5', mb_strtoupper(__('business.system')));
                $event->sheet->setCellValue('E5', mb_strtoupper(__('accounting.bank')));

                /** table body */
                $count = 6; $color = '';
                $total_system = 0; $total_bank = 0;
                foreach($transactions as $t){
                    $event->sheet->setCellValue('A'. $count, $this->transactionUtil->format_date($t['transaction_date']));
                    $event->sheet->setCellValue('B'. $count, $t['reference']);
                    $event->sheet->setCellValue('C'. $count, $t['description']);
                    $event->sheet->setCellValue('D'. $count, $t['system']);
                    $event->sheet->setCellValue('E'. $count, $t['bank']);

                    /** set color for each cell by status */
                    if($t['status'] == 'red'){ $color = 'FF6666'; }
                    else if($t['status'] == 'yellow'){ $color = 'FFFF66'; }
                    else { $color = '66B266'; }

                    $event->sheet->getDelegate()->getStyle('A' . $count . ':E' . $count)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB($color);

                    $count++;
                    $total_system += $t['system'];
                    $total_bank += $t['bank'];
                }

                /** table footer */
                $event->sheet->mergeCells('A'. $count . ':C'. $count);
                $event->sheet->horizontalAlign('A' . $count . ':C' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':E' . $count)->getFont()->setBold(true);
                $event->sheet->setFormat('A' . $count, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('D' . $count . ':E' . $count, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setCellValue('A' . $count, mb_strtoupper(__('accounting.totals')));
                $event->sheet->setcellValue('D'. $count, $total_system);
                $event->sheet->setCellValue('E'. $count, $total_bank);
            },
        ];
    }
}
