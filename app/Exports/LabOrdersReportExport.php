<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LabOrdersReportExport implements FromView, WithEvents, WithTitle
{
    private $lab_orders;
    private $size;
    private $business;

    /**
     * Constructor.
     * 
     * @param  array  $lab_orders
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($lab_orders, $size, $business)
    {
    	$this->lab_orders = $lab_orders;
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
    	return __('report.lab_orders_report');
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

                $event->sheet->columnWidth('A', 14.03);
                $event->sheet->columnWidth('B', 16.46);
                $event->sheet->columnWidth('C', 16.46);
                $event->sheet->columnWidth('D', 36.03);
                $event->sheet->columnWidth('E', 36.03);
                $event->sheet->columnWidth('F', 15.89);
                $event->sheet->columnWidth('G', 19.31);
                $event->sheet->columnWidth('H', 19.31);

                $event->sheet->setFormat('G5:H15000', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME);
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
    	return view('reports.lab_orders_report_excel', [
    		'lab_orders' => $this->lab_orders,
    		'size' => $this->size,
    		'business' => $this->business,
        ]);
    }
}
