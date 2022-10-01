<?php

namespace App\Exports;

use App\Business;
use App\Utils\TransactionUtil;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesPerSellerReportExport implements WithEvents, WithTitle
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
    	return __('report.sales_per_seller_report');
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
                $event->sheet->columnWidth('E', 20.46);
                $event->sheet->columnWidth('F', 20.46);
                $event->sheet->columnWidth('G', 20.46);

                // Row number
                $row_no = 1;

                // Header
                $event->sheet->setBold('A' . $row_no . ':G' . ($row_no + 3));
                $event->sheet->wrapText('A' . $row_no . ':G' . ($row_no + 3));
                $event->sheet->horizontalAlign('A' . $row_no . ':G' . ($row_no + 3), \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->verticalAlign('A' . $row_no . ':G' . ($row_no + 3), \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $event->sheet->setCellValue('A' . $row_no, mb_strtoupper($this->business->business_full_name));
                $event->sheet->mergeCells('A' . $row_no . ':G' . $row_no);
                $row_no += 1;

                $event->sheet->setCellValue('A' . $row_no, mb_strtoupper(__('report.sales_per_seller_report')));
                $event->sheet->mergeCells('A' . $row_no . ':G' . $row_no);
                $row_no += 1;

                $event->sheet->setCellValue('A' . $row_no, mb_strtoupper(__('accounting.from_date')) . ' ' . $this->transactionUtil->format_date($this->start) . ' ' . mb_strtoupper(__('accounting.to_date')) . ' ' . $this->transactionUtil->format_date($this->end));
                $event->sheet->mergeCells('A' . $row_no . ':G' . $row_no);
                $row_no += 2;

                // Body
                $total_before_tax_total = 0;
                $discount_amount_total = 0;
                $tax_amount_total = 0;
                $final_total_total = 0;
                $flag = 0;
                $i = 1;

                foreach ($this->records as $record) {
                    if (count($record['sales']) > 0) {
                        // Correlative
                        $event->sheet->setCellValue('A' . $row_no, $i);
                        
                        // Seller
                        $event->sheet->mergeCells('B' . $row_no . ':G' . $row_no);
                        $event->sheet->setCellValue('B' . $row_no, $record['seller']->first_name . ' ' . $record['seller']->last_name);

                        $event->sheet->setBold('A' . $row_no . ':G' . $row_no);
                        $event->sheet->wrapText('A' . $row_no . ':G' . $row_no);
                        $event->sheet->setAllBorders('A' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $row_no += 1;

                        // Subheader
                        $event->sheet->setCellValue('A' . $row_no, __('messages.date'));
                        $event->sheet->setCellValue('B' . $row_no, __('contact.customer'));
                        $event->sheet->setCellValue('C' . $row_no, __('sale.document_no'));
                        $event->sheet->setCellValue('D' . $row_no, __('sale.subtotal'));
                        $event->sheet->setCellValue('E' . $row_no, __('sale.discount'));
                        $event->sheet->setCellValue('F' . $row_no, __('tax_rate.taxes'));
                        $event->sheet->setCellValue('G' . $row_no, __('sale.total_amount'));

                        $event->sheet->setBold('A' . $row_no . ':G' . $row_no);
                        $event->sheet->wrapText('A' . $row_no . ':G' . $row_no);
                        $event->sheet->setAllBorders('A' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $row_no += 1;

                        // Sales
                        $total_before_tax_total_seller = 0;
                        $discount_amount_total_seller = 0;
                        $tax_amount_total_seller = 0;
                        $final_total_total_seller = 0;

                        foreach ($record['sales'] as $sale) {
                            $event->sheet->setCellValue('A' . $row_no, $this->transactionUtil->format_date($sale->transaction_date));
                            $event->sheet->setCellValue('B' . $row_no, $sale->customer_name);
                            $event->sheet->setCellValue('C' . $row_no, $sale->correlative);
                            $event->sheet->setCellValue('D' . $row_no, $sale->total_before_tax);
                            $event->sheet->setCellValue('E' . $row_no, $sale->discount_amount);
                            $event->sheet->setCellValue('F' . $row_no, $sale->tax_amount);
                            $event->sheet->setCellValue('G' . $row_no, $sale->final_total);

                            $event->sheet->wrapText('A' . $row_no . ':G' . $row_no);
                            $event->sheet->setAllBorders('A' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                            $event->sheet->setFormat('D' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                            $row_no += 1;

                            $total_before_tax_total_seller += $sale->total_before_tax;
                            $discount_amount_total_seller += $sale->discount_amount;
                            $tax_amount_total_seller += $sale->tax_amount;
                            $final_total_total_seller += $sale->final_total;
                        }

                        // Total per seller
                        $event->sheet->mergeCells('A' . $row_no . ':C' . $row_no);
                        $event->sheet->setCellValue('A' . $row_no, __('report.total_per_seller'));

                        $event->sheet->setCellValue('D' . $row_no, $total_before_tax_total_seller);
                        $event->sheet->setCellValue('E' . $row_no, $discount_amount_total_seller);
                        $event->sheet->setCellValue('F' . $row_no, $tax_amount_total_seller);
                        $event->sheet->setCellValue('G' . $row_no, $final_total_total_seller);

                        $event->sheet->setBold('A' . $row_no . ':G' . $row_no);
                        $event->sheet->wrapText('A' . $row_no . ':G' . $row_no);
                        $event->sheet->setAllBorders('A' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $event->sheet->setFormat('D' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                        $row_no += 1;

                        $total_before_tax_total += $total_before_tax_total_seller;
                        $discount_amount_total += $discount_amount_total_seller;
                        $tax_amount_total += $tax_amount_total_seller;
                        $final_total_total += $final_total_total_seller;
                        $flag = 1;
                        $i++;
                    }
                }

                if ($flag) {
                    // Total general
                    $event->sheet->mergeCells('A' . $row_no . ':C' . $row_no);
                    $event->sheet->setCellValue('A' . $row_no, __('accounting.total_general'));

                    $event->sheet->setCellValue('D' . $row_no, $total_before_tax_total);
                    $event->sheet->setCellValue('E' . $row_no, $discount_amount_total);
                    $event->sheet->setCellValue('F' . $row_no, $tax_amount_total);
                    $event->sheet->setCellValue('G' . $row_no, $final_total_total);

                    $event->sheet->setBold('A' . $row_no . ':G' . $row_no);
                    $event->sheet->wrapText('A' . $row_no . ':G' . $row_no);
                    $event->sheet->setAllBorders('A' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $event->sheet->setFormat('D' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                    $row_no += 1;

                } else {
                    $event->sheet->wrapText('A' . $row_no . ':G' . $row_no);
                    $event->sheet->horizontalAlign('A' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->verticalAlign('A' . $row_no . ':G' . $row_no, \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $event->sheet->setCellValue('A' . $row_no, __('report.no_data_available'));
                    $event->sheet->mergeCells('A' . $row_no . ':G' . $row_no);
                    $row_no += 1;
                }
            }
        ];
    }
}
