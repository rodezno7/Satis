<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductsReportExport implements FromView, WithEvents, WithTitle
{
    private $products;
    private $size;
    private $business;

    /**
     * Constructor.
     * 
     * @param  array  $products
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($products, $size, $business)
    {
    	$this->products = $products;
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
    	return __('report.products_report');
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

    			$event->sheet->setFontFamily('A1:G7500', 'Calibri');
    			$event->sheet->setFontSize('A1:G7500', 10);

    			$event->sheet->mergeCells('A1:G1');
    			$event->sheet->mergeCells('A2:G2');

                $event->sheet->horizontalAlign('A1:G2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A4:G4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 15.17);
                $event->sheet->columnWidth('B', 74.60);
                $event->sheet->columnWidth('C', 16.46);
                $event->sheet->columnWidth('D', 16.46);
                $event->sheet->columnWidth('E', 16.46);
                $event->sheet->columnWidth('F', 16.46);
                $event->sheet->columnWidth('G', 16.46);
            }
        ];
    }

    /**
     * Returns view where the report is built.
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
    	return view('reports.products_report_excel', [
    		'products' => $this->products,
    		'size' => $this->size,
    		'business' => $this->business,
        ]);
    }
}
