<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesTrackingReportExport implements FromView, WithEvents, WithTitle
{
    private $orders;
    private $size;
    private $business;

    /**
     * Constructor.
     * 
     * @param  array  $orders
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($orders, $size, $business)
    {
    	$this->orders = $orders;
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
    	return __('report.sales_tracking_report');
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

    			$event->sheet->setFontFamily('A1:I7500', 'Calibri');
    			$event->sheet->setFontSize('A1:I7500', 10);

    			$event->sheet->mergeCells('A1:I1');
    			$event->sheet->mergeCells('A2:I2');

                $event->sheet->horizontalAlign('A1:I2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A4:I4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 9.89);
                $event->sheet->columnWidth('B', 11.89);
                $event->sheet->columnWidth('C', 16.74);
                $event->sheet->columnWidth('D', 49.31);
                $event->sheet->columnWidth('E', 15.17);
                $event->sheet->columnWidth('F', 9.89);
                $event->sheet->columnWidth('G', 15.89);
                $event->sheet->columnWidth('H', 15.89);
                $event->sheet->columnWidth('I', 49.31);

                $event->sheet->setFormat('B5:B7500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $event->sheet->setFormat('G5:G7500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setFormat('H5:H7500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
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
    	return view('reports.sales_tracking_report_excel', [
    		'orders' => $this->orders,
    		'size' => $this->size,
    		'business' => $this->business,
        ]);
    }
}
