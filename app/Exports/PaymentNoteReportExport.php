<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PaymentNoteReportExport implements FromView, WithEvents, WithTitle
{
    private $payments;
    private $size;
    private $business;

    /**
     * Constructor.
     * 
     * @param  array  $payments
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($payments, $size, $business)
    {
    	$this->payments = $payments;
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
    	return __('report.payment_note_report');
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
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    			$event->sheet->setFontFamily('A1:H15000', 'Calibri');
    			$event->sheet->setFontSize('A1:H15000', 10);

    			$event->sheet->mergeCells('A1:H1');
    			$event->sheet->mergeCells('A2:H2');

                $event->sheet->horizontalAlign('A1:H2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A4:H4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 18.46);
                $event->sheet->columnWidth('B', 14.89);
                $event->sheet->columnWidth('C', 42.46);
                $event->sheet->columnWidth('D', 14.89);
                $event->sheet->columnWidth('E', 16.89);
                $event->sheet->columnWidth('F', 16.31);
                $event->sheet->columnWidth('G', 16.31);
                $event->sheet->columnWidth('H', 16.31);

                $event->sheet->setFormat('A5:A15000', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME);
                $event->sheet->setFormat('F5:G15000', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
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
    	return view('reports.payment_note_report_excel', [
    		'payments' => $this->payments,
    		'size' => $this->size,
    		'business' => $this->business,
        ]);
    }
}
