<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class KardexReportExport implements FromView, WithEvents, WithTitle
{
    private $kardex;
    private $start;
    private $end;
    private $business;
    private $warehouse;
    private $variation;

    /**
     * Constructor.
     * 
     * @param  array  $kardex
     * @param  string  $date
     * @param  \App\Business  $business
     * @param  \App\Warehouse  $warehouse
     * @param  \App\Variation  $variation
     * @return void
     */
    public function __construct($kardex, $start, $end, $business, $warehouse, $variation)
    {
    	$this->kardex = $kardex;
        $this->start = $start;
        $this->end = $end;
        $this->business = $business;
        $this->warehouse = $warehouse;
        $this->variation = $variation;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('kardex.kardex');
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
                $view_costs = auth()->user()->can('product.view_costs');

                if ($view_costs) {
                    $letter = 'K';
                } else {
                    $letter = 'H';
                }

    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    			$event->sheet->setFontFamily('A1:K7500', 'Calibri');
    			$event->sheet->setFontSize('A1:K7500', 10);

    			$event->sheet->mergeCells('A1:' . $letter . '1');
    			$event->sheet->mergeCells('A2:' . $letter . '2');

                $event->sheet->horizontalAlign('A1:' . $letter . '2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A6:' . $letter . '6', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 18.86);
                $event->sheet->columnWidth('B', 17.00);
                $event->sheet->columnWidth('C', 8.43);
                $event->sheet->columnWidth('D', 12.86);
                $event->sheet->columnWidth('E', 17.00);
                $event->sheet->columnWidth('F', 17.00);
                $event->sheet->columnWidth('G', 17.00);
                $event->sheet->columnWidth('H', 17.00);
                $event->sheet->columnWidth('I', 17.00);
                $event->sheet->columnWidth('J', 17.00);
                $event->sheet->columnWidth('K', 17.00);

                if ($view_costs) {
                    $event->sheet->setFormat('I7:K7500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                }
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
    	return view('reports.kardex_report_excel', [
    		'kardex' => $this->kardex,
    		'start' => $this->start,
            'end' => $this->end,
    		'business' => $this->business,
            'warehouse' => $this->warehouse,
            'variation' => $this->variation,
        ]);
    }
}
