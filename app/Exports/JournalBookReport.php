<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class JournalBookReport implements WithEvents, WithTitle
{
    private $business_name;
    private $start_date;
    private $end_date;
    private $journal_book;
    private $transactionUtil;

    /**
     * Constructor.
     * 
     * @param string $business_name
     * @param string $start_date
     * @param string $end_date
     * @param collect $journal_book
     * @param \App\Utils\TransactionUtil $transactionUtil
     * 
     * @return void
     */
    public function __construct($business_name, $start_date, $end_date, $journal_book, $transactionUtil)
    {
        $this->business_name = $business_name;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->journal_book = $journal_book;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.accounting.general_journal_book');
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
    			$event->sheet->setOrientation("portrait");
                $event->sheet->setShowGridlines(false);

                /** Header */
                $event->sheet->mergeCells('A1:E1');
                $event->sheet->mergeCells('A2:E2');
                $event->sheet->mergeCells('A3:E3');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:E1', 'center');
                $event->sheet->horizontalAlign('A1:E2', 'center');
                $event->sheet->horizontalAlign('A1:E2', 'center');
                $event->sheet->setBold('A1:E3');
                $event->sheet->setFontSize('A1:E1', 14);
                $event->sheet->setFontSize('A2:E2', 12);
                $event->sheet->setFontSize('A3:E3', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper(__('accounting.general_journal_book')) ." ". strtoupper(__('accounting.from_date')) ." ". $this->start_date ." ". strtoupper(__('accounting.to_date')) ." ". $this->end_date );
                $event->sheet->setCellValue('A3', mb_strtoupper(__('accounting.accountant_report_values')));

                /** Column width and font align */
                $event->sheet->columnWidth('A', 12);
                $event->sheet->columnWidth('B', 14);
                $event->sheet->columnWidth('C', 40);
                $event->sheet->columnWidth('D', 15);
                $event->sheet->columnWidth('E', 15);

                /** table head */
                $event->sheet->setBold('A4:E4');
                $event->sheet->horizontalAlign('A3:E4', 'center');
                $event->sheet->verticalAlign('A3:E4', 'center');
                $event->sheet->setCellValue('A4', mb_strtoupper(__('accounting.correlative')));
                $event->sheet->setCellValue('B4', mb_strtoupper(__('accounting.account')));
                $event->sheet->setCellValue('C4', mb_strtoupper(__('accounting.concept')));
                $event->sheet->setCellValue('D4', mb_strtoupper(__('accounting.charges')));
                $event->sheet->setCellValue('E4', mb_strtoupper(__('accounting.payments')));
                
                /** table body */
                $row = 5;
                $day = 0;
                $total_debit = 0;
                $total_credit = 0;
                $total_total_debit = 0;
                $total_total_credit = 0;

                /** products by category */
                foreach($this->journal_book as $jb) {
                    /** Totals */
                    if ($day != $jb->day && $day != 0) {
                        $event->sheet->setBold('A'. $row .':E'. $row);
                        $event->sheet->mergeCells('A'. $row .':C'. $row);
                        $event->sheet->setFormat('A'. $row .':C'. $row, '@');
                        $event->sheet->setFormat('D'. $row .':E'. $row, '0.00');
                        $event->sheet->setBorderTop('D'. $row .':E'. $row, 'thin');
                        $event->sheet->horizontalAlign('A'. $row .':C'. $row, 'center');
                        $event->sheet->setCellValue('A'. $row, 'TOTALES');
                        $event->sheet->setCellValue('D'. $row, $total_debit);
                        $event->sheet->setCellValue('E'. $row, $total_credit);

                        $total_total_debit += $total_debit;
                        $total_total_credit += $total_credit;
                        $total_debit = 0;
                        $total_credit = 0;
                        $row += 2;
                    }

                    /** Title by day */
                    if ($day != $jb->day) {
                        $event->sheet->setBold('A'. $row .':E'. $row);
                        $event->sheet->mergeCells('A'. $row .':C'. $row);
                        $event->sheet->setFormat('A'. $row .':E'. $row, '@');
                        $event->sheet->setBorderBottom('A'. $row .':E'. $row, 'thin');
                        $event->sheet->horizontalAlign('A'. $row .':C'. $row, 'center');
                        $event->sheet->setCellValue('A'. $row, 'NO. DE COMPROBANTE CONTABLE: D '. $jb->day);

                        $event->sheet->mergeCells('D'. $row .':E'. $row);
                        $event->sheet->horizontalAlign('D'. $row .':E'. $row, 'left');
                        $event->sheet->setCellValue('D'. $row, "FECHA: ". $this->transactionUtil->format_date($jb->date));

                        $row ++;
                    }

                    /** Rows values */
                    $event->sheet->setFormat('A'. $row .':B'. $row, '@');
                    $event->sheet->setCellValue('A'. $row, $jb->correlative);
                    $event->sheet->setCellValue('B'. $row, $jb->account_code);
                    $event->sheet->setCellValue('C'. $row, $jb->account_name);

                    $event->sheet->setFormat('D'. $row .':E'. $row, '0.00');
                    $event->sheet->setCellValue('D'. $row, $jb->debit);
                    $event->sheet->setCellValue('E'. $row, $jb->credit);

                    $row ++;
                    $day = $jb->day;
                    $total_debit += $jb->debit;
                    $total_credit += $jb->credit;
                }
                
                /** Last totals */
                $event->sheet->setBold('A'. $row .':E'. $row);
                $event->sheet->mergeCells('A'. $row .':C'. $row);
                $event->sheet->setBorderTop('A'. $row .':E'. $row, 'thin');
                $event->sheet->horizontalAlign('A'. $row .':C'. $row, 'center');
                $event->sheet->setCellValue('A'. $row, 'TOTALES');
                $event->sheet->setCellValue('D'. $row, $total_debit);
                $event->sheet->setCellValue('E'. $row, $total_credit);

                $row += 2;

                /** Genetal total */
                $event->sheet->setBold('A'. $row .':E'. $row);
                $event->sheet->mergeCells('A'. $row .':C'. $row);
                $event->sheet->setBorderTop('A'. $row .':E'. $row, 'thin');
                $event->sheet->setBorderBottom('A'. $row .':E'. $row, 'thin');
                $event->sheet->horizontalAlign('A'. $row .':C'. $row, 'center');
                $event->sheet->setCellValue('A'. $row, 'TOTAL GENERAL');
                $event->sheet->setCellValue('D'. $row, $total_total_debit);
                $event->sheet->setCellValue('E'. $row, $total_total_credit);

                /** set font size and family, set borders */
    			$event->sheet->setFontSize('A4:E'. ($row), 10);
                $event->sheet->setFontFamily('A1:E'. ($row), 'Calibri');
            },
        ];
    }
}