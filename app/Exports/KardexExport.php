<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class KardexExport implements FromView, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $warehouse;
    private $initial_quantity;
    private $initial_cost;
    private $lines;
    private $product;
    private $size;
    private $header_date;
    private $business;

    public function __construct($warehouse, $initial_quantity, $initial_cost, $lines, $product, $size, $header_date, $business)
    {
    	$this->warehouse = $warehouse;
    	$this->initial_quantity = $initial_quantity;
    	$this->initial_cost = $initial_cost;
    	$this->lines = $lines;
    	$this->product = $product;
    	$this->size = $size;
    	$this->header_date = $header_date;
    	$this->business = $business;
    }

    public function title(): string
    {
    	return __('accounting.kardex');
    }

    public function registerEvents(): array
    {
    	return [
    		AfterSheet::class    => function(AfterSheet $event) {

    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    			$event->sheet->mergeCells('A1:G1');
    			$event->sheet->mergeCells('A2:G2');
    			$event->sheet->mergeCells('A3:G3');
    			$event->sheet->mergeCells('A4:G4');

    			$event->sheet->columnWidth('A', 5);
    			$event->sheet->columnWidth('B', 17);
    			$event->sheet->columnWidth('C', 17);
    			$event->sheet->columnWidth('D', 19);
    			$event->sheet->columnWidth('E', 17);
    			$event->sheet->columnWidth('F', 17);
    			$event->sheet->columnWidth('G', 17);
    			

    			$event->sheet->horizontalAlign('A1:G1' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 

    			$event->sheet->horizontalAlign('A2:G4' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    			$event->sheet->horizontalAlign('A6:G6' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 

    			$event->sheet->horizontalAlign('C7:C5000' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    			$event->sheet->horizontalAlign('D7:D5000' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    			$event->sheet->horizontalAlign('E7:E5000' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    			$event->sheet->horizontalAlign('F7:F5000' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    			$event->sheet->horizontalAlign('G7:G5000' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


    		},
    	];
    }

    public function view(): View
    {
    	return view('reports.kardex_excel', [
    		'warehouse' => $this->warehouse,
    		'initial_quantity' => $this->initial_quantity,
    		'initial_cost' => $this->initial_cost,
    		'lines' => $this->lines,
    		'product' => $this->product,
    		'size' => $this->size,
    		'header_date' => $this->header_date,
    		'business' => $this->business,
    	]);
    }
}
