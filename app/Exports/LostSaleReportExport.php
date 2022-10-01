<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LostSaleReportExport implements FromView, WithEvents, WithTitle
{
    private $quotes;
    private $size;
    private $business;

    /**
     * Constructor.
     * 
     * @param  array  $quotes
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($quotes, $size, $business)
    {
        $this->quotes = $quotes;
        $this->size = $size;
        $this->business = $business;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
        return __('quote.lost_sale_report');
    }

    /**
     * Configure events and document format.
     * 
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                $event->sheet->setFontFamily('A1:J7500', 'Calibri');
                $event->sheet->setFontSize('A1:I7500', 10);

                $event->sheet->mergeCells('A1:J1');
                $event->sheet->mergeCells('A2:J2');

                $event->sheet->horizontalAlign('A1:J2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A4:J4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 10.43);
                $event->sheet->columnWidth('B', 17.43);
                $event->sheet->columnWidth('C', 14.30);
                $event->sheet->columnWidth('D', 15.17);
                $event->sheet->columnWidth('E', 9.71);
                $event->sheet->columnWidth('F', 26);
                $event->sheet->columnWidth('G', 16.74);
                $event->sheet->columnWidth('H', 28);
                $event->sheet->columnWidth('I', 26);
                $event->sheet->columnWidth('J', 14.14);

                $event->sheet->setFormat('A5:A7500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $event->sheet->setFormat('B5:B7500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $event->sheet->setFormat('C5:C7500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $event->sheet->setFormat('J5:J7500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
            },
        ];
    }

    /**
     * Returns view where the report is built.
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        return view('reports.lost_sales_report_excel', [
            'quotes' => $this->quotes,
            'size' => $this->size,
            'business' => $this->business,
        ]);
    }
}
