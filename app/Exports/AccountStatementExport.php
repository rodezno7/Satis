<?php

namespace App\Exports;

use App\Utils\TransactionUtil;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AccountStatementExport implements WithEvents, WithTitle
{
    private $transactionUtil;
    private $lines;
    private $date;
    private $business;
    private $customer;

    /**
     * Constructor.
     * 
     * @param  \App\Utils\TransactionUtil  $transactionUtil
     * @param  array  $lines
     * @param  date  $date
     * @param  \App\Business  $business
     * @param  \App\Customer  $customer
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, $lines, $date, $business, $customer)
    {
        $this->transactionUtil = $transactionUtil;
    	$this->lines = $lines;
        $this->date = $date;
        $this->business = $business;
        $this->customer = $customer;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.account_statement');
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
                $num_rows = count($this->lines) + 15;
                $lines = $this->lines;

                // General setup
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
    			$event->sheet->setFontFamily('A1:F' . $num_rows, 'Calibri');
    			$event->sheet->setFontSize('A1:F' . $num_rows, 10);

                // Column width and font align
                $event->sheet->horizontalAlign('A1:F' . $num_rows, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 16.31);
                $event->sheet->columnWidth('B', 16.31);
                $event->sheet->columnWidth('C', 16.31);
                $event->sheet->columnWidth('D', 16.31);
                $event->sheet->columnWidth('E', 16.31);
                $event->sheet->columnWidth('F', 16.31);

                // Apply title font size and font bold
                $event->sheet->getDelegate()->getStyle('A1:F11')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A' . $num_rows . ':F' . $num_rows)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:F1')->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle('A7:F7')->getFont()->setSize(12);

                // Apply column font format
                $event->sheet->setFormat('B10:F10', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $event->sheet->setFormat('A12:A' . $num_rows, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('B12:B' . $num_rows, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $event->sheet->setFormat('C12:F' . $num_rows, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                // Header
                $event->sheet->mergeCells('B1:F1');
                $event->sheet->setCellValue('B1', mb_strtoupper($this->business->business_full_name));
    			
                $event->sheet->mergeCells('B2:F2');
                $event->sheet->setCellValue('B2', mb_strtoupper($this->business->line_of_business));

                $event->sheet->mergeCells('B3:F3');
                $event->sheet->setCellValue('B3', mb_strtoupper('NIT: ' . $this->business->nit . '   NRC: ' . $this->business->nrc));

                $event->sheet->mergeCells('B4:F4');
                $event->sheet->setCellValue('B4', mb_strtoupper($this->business->landmark . ', ' . $this->business->city . ', ' . $this->business->state . ', ' . $this->business->country));

                $event->sheet->mergeCells('B5:F5');
                $event->sheet->setCellValue('B5', mb_strtoupper('TEL: ' . $this->business->mobile));

                $event->sheet->mergeCells('A7:F7');
                $event->sheet->setCellValue('A7', mb_strtoupper(__('report.account_statement')));

                $event->sheet->mergeCells('B9:F9');
                $event->sheet->setCellValue('A9', __('contact.customer'));
                $event->sheet->setCellValue('B9', $this->customer->name);

                $event->sheet->mergeCells('B10:F10');
                $event->sheet->setCellValue('A10', __('lang_v1.date'));
                $event->sheet->setCellValue('B10', $this->transactionUtil->format_date($this->date));

                // Table head
                $event->sheet->setCellValue('A11', __('report.no_doc'));
                $event->sheet->setCellValue('B11', __('lang_v1.date'));
                $event->sheet->setCellValue('C11', __('report.debit'));
                $event->sheet->setCellValue('D11', __('report.credit'));
                $event->sheet->setCellValue('E11', __('report.document_balance'));
                $event->sheet->setCellValue('F11', __('report.total_balance'));

                // Table body
                $count = 12;
                $total_debit = 0;
                $total_credit = 0;

                foreach ($lines as $item) {
                    $event->sheet->setCellValue('A' . $count, $item['no_doc']);
                    $event->sheet->setCellValue('B' . $count, $this->transactionUtil->format_date($item['date']));
                    $event->sheet->setCellValue('C' . $count, $item['debit'] ?? 0);
                    $event->sheet->setCellValue('D' . $count, $item['credit'] ?? 0);
                    $event->sheet->setCellValue('E' . $count, $item['document_balance'] ?? 0);
                    $event->sheet->setCellValue('F' . $count, $item['total_balance'] ?? 0);

                    $count++;
                    $total_debit += $item['debit'];
                    $total_credit += $item['credit'];
                }

                // Table footer
                $event->sheet->getDelegate()->getStyle('A' . $count . ':F' . $count)->getFont()->setBold(true);
                $event->sheet->mergeCells('A' . $count . ':E' . $count);
                $event->sheet->setCellValue('A' . $count, __('sale.total'));
                $event->sheet->setCellValue('F' . $count, $total_debit - $total_credit);

                $event->sheet->setCellValue('A' . ($count + 3), __('report.authorized'));
                $event->sheet->setCellValue('E' . ($count + 3), __('contact.customer'));
            }
        ];
    }
}
