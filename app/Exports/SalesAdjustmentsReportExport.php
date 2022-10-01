<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesAdjustmentsReportExport implements FromView, WithEvents, WithTitle
{
    private $query;
    private $size;
    private $month_name;
    private $business;
    private $location;

    /**
     * Constructor.
     * 
     * @param  array  $query
     * @param  int  $size
     * @param  string  $month_name
     * @param  \App\Business  $business
     * @param  \App\BusinessLocation  $location
     * @return void
     */
    public function __construct($query, $size, $month_name, $business, $location)
    {
    	$this->query = $query;
        $this->size = $size;
        $this->month_name = $month_name;
        $this->business = $business;
        $this->location = $location;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.consumption_report');
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

    			$event->sheet->setFontFamily('A1:F1500', 'Calibri');
    			$event->sheet->setFontSize('A1:F1500', 10);

    			$event->sheet->mergeCells('A1:F1');
    			$event->sheet->mergeCells('A2:F2');

                $event->sheet->horizontalAlign('A1:F2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A6:F6', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 8.90);
                $event->sheet->columnWidth('B', 47.15);
                $event->sheet->columnWidth('C', 16.60);
                $event->sheet->columnWidth('D', 16.60);
                $event->sheet->columnWidth('E', 16.60);
                $event->sheet->columnWidth('F', 16.60);

                $event->sheet->setFormat('C6:D1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
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
    	return view('reports.sales_adjustments_report_excel', [
    		'query' => $this->query,
    		'size' => $this->size,
    		'month_name' => $this->month_name,
    		'business' => $this->business,
            'location' => $this->location,
        ]);
    }
}
