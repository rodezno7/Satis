<?php

namespace App\Exports;

use App\Business;
use App\Utils\TransactionUtil;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PaymentReportExport implements WithEvents, WithTitle
{
    private $records;
    private $business;
    private $start;
    private $end;
    private $transactionUtil;

    /**
     * Constructor.
     * 
     * @param  array  $records
     * @param  string  $title
     * @param  \App\Business  $business
     * @param  string  $start
     * @param  string  $end
     * @param  \App\Utils\TransactionUtil  $transactionUtil
     * @return void
     */
    public function __construct($records, Business $business, $start, $end, TransactionUtil $transactionUtil)
    {
    	$this->records = $records;
        $this->business = $business;
        $this->start = $start;
        $this->end = $end;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.payment_report');
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
                // General settings
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                $event->sheet->columnWidth('A', 15.00);
                $event->sheet->columnWidth('B', 58.32);
                $event->sheet->columnWidth('C', 20.46);
                $event->sheet->columnWidth('D', 20.46);

                // Row number
                $row_no = 1;

                // Header
                $event->sheet->setBold('A' . $row_no . ':D' . ($row_no + 3));
                $event->sheet->wrapText('A' . $row_no . ':D' . ($row_no + 3));
                $event->sheet->horizontalAlign('A' . $row_no . ':D' . ($row_no + 3), \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->verticalAlign('A' . $row_no . ':D' . ($row_no + 3), \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $event->sheet->setCellValue('A' . $row_no, mb_strtoupper($this->business->business_full_name));
                $event->sheet->mergeCells('A' . $row_no . ':D' . $row_no);
                $row_no += 1;

                $event->sheet->setCellValue('A' . $row_no, mb_strtoupper(__('report.payment_report')));
                $event->sheet->mergeCells('A' . $row_no . ':D' . $row_no);
                $row_no += 1;

                $event->sheet->setCellValue('A' . $row_no, mb_strtoupper(__('accounting.from_date')) . ' ' . $this->transactionUtil->format_date($this->start) . ' ' . mb_strtoupper(__('accounting.to_date')) . ' ' . $this->transactionUtil->format_date($this->end));
                $event->sheet->mergeCells('A' . $row_no . ':D' . $row_no);
                $row_no += 2;

                // Body
                $total_amount = 0;
                $flag = 0;
                $i = 1;

                foreach ($this->records as $record) {
                    if (count($record['payments']) > 0) {
                        // Correlative
                        $event->sheet->setCellValue('A' . $row_no, $i);
                        
                        // Seller
                        $event->sheet->mergeCells('B' . $row_no . ':D' . $row_no);
                        $event->sheet->setCellValue('B' . $row_no, $record['seller']->first_name . ' ' . $record['seller']->last_name);

                        $event->sheet->setBold('A' . $row_no . ':D' . $row_no);
                        $event->sheet->wrapText('A' . $row_no . ':D' . $row_no);
                        $event->sheet->setAllBorders('A' . $row_no . ':D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $row_no += 1;

                        // Subheader
                        $event->sheet->setCellValue('A' . $row_no, __('report.payment_date'));
                        $event->sheet->setCellValue('B' . $row_no, __('contact.customer'));
                        $event->sheet->setCellValue('C' . $row_no, __('sale.document_no'));
                        $event->sheet->setCellValue('D' . $row_no, __('report.amount_without_vat'));

                        $event->sheet->setBold('A' . $row_no . ':D' . $row_no);
                        $event->sheet->wrapText('A' . $row_no . ':D' . $row_no);
                        $event->sheet->setAllBorders('A' . $row_no . ':D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $row_no += 1;

                        // Payments
                        $total_amount_seller = 0;

                        foreach ($record['payments'] as $payment) {
                            $event->sheet->setCellValue('A' . $row_no, $this->transactionUtil->format_date($payment->paid_on));
                            $event->sheet->setCellValue('B' . $row_no, $payment->customer_name);
                            $event->sheet->setCellValue('C' . $row_no, $payment->correlative);
                            $event->sheet->setCellValue('D' . $row_no, $payment->amount);

                            $event->sheet->wrapText('A' . $row_no . ':D' . $row_no);
                            $event->sheet->setAllBorders('A' . $row_no . ':D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                            $event->sheet->setFormat('D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                            $row_no += 1;

                            $total_amount_seller += $payment->amount;
                        }

                        // Total per seller
                        $event->sheet->mergeCells('A' . $row_no . ':C' . $row_no);
                        $event->sheet->setCellValue('A' . $row_no, __('report.total_per_seller'));

                        $event->sheet->setCellValue('D' . $row_no, $total_amount_seller);

                        $event->sheet->setBold('A' . $row_no . ':D' . $row_no);
                        $event->sheet->wrapText('A' . $row_no . ':D' . $row_no);
                        $event->sheet->setAllBorders('A' . $row_no . ':D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $event->sheet->setFormat('D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                        $row_no += 1;

                        $total_amount += $total_amount_seller;
                        $flag = 1;
                        $i++;
                    }
                }

                if ($flag) {
                    // Total general
                    $event->sheet->mergeCells('A' . $row_no . ':C' . $row_no);
                    $event->sheet->setCellValue('A' . $row_no, __('accounting.total_general'));
    
                    $event->sheet->setCellValue('D' . $row_no, $total_amount);
    
                    $event->sheet->setBold('A' . $row_no . ':D' . $row_no);
                    $event->sheet->wrapText('A' . $row_no . ':D' . $row_no);
                    $event->sheet->setAllBorders('A' . $row_no . ':D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $event->sheet->setFormat('D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                    $row_no += 1;

                } else {
                    $event->sheet->wrapText('A' . $row_no . ':D' . $row_no);
                    $event->sheet->horizontalAlign('A' . $row_no . ':D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->verticalAlign('A' . $row_no . ':D' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $event->sheet->setCellValue('A' . $row_no, __('report.no_data_available'));
                    $event->sheet->mergeCells('A' . $row_no . ':D' . $row_no);
                    $row_no += 1;
                }
            }
        ];
    }
}
