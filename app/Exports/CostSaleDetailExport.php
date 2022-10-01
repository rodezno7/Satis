<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CostSaleDetailExport implements FromView, WithEvents, WithTitle
{
    private $query;
    private $business;
    private $start;
    private $end;

    /**
     * Constructor.
     * 
     * @param  array  $query
     * @param  \App\Business  $business
     * @param  string  $start
     * @param  string  $end
     * @return void
     */
    public function __construct($query, $business, $start, $end)
    {
    	$this->query = $query;
        $this->business = $business;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.cost_of_sale');
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

    			$event->sheet->setFontFamily('A1:I1500', 'Calibri');
    			$event->sheet->setFontSize('A1:I1500', 10);

    			$event->sheet->mergeCells('A1:I1');
    			$event->sheet->mergeCells('A2:I2');

                $event->sheet->horizontalAlign('A1:I2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 10.75);
                $event->sheet->columnWidth('B', 10.75);
                $event->sheet->columnWidth('C', 45.45);
                $event->sheet->columnWidth('D', 58.86);
                $event->sheet->columnWidth('E', 6.00);
                $event->sheet->columnWidth('F', 12.86);
                $event->sheet->columnWidth('G', 9.75);
                $event->sheet->columnWidth('H', 9.75);
                $event->sheet->columnWidth('I', 9.75);

                $event->sheet->setFormat('G4:I1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
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
    	return view('reports.cost_of_sale_detail_report_excel', [
    		'query' => $this->query,
    		'business' => $this->business,
    		'start' => $this->start,
            'end' => $this->end,
        ]);
    }
}
