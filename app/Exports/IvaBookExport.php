<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class IvaBookExport implements FromView, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $size;
    private $header;
    private $header_date;
    private $business;
    private $month;
    private $year;
    private $type;
    private $lines;

    public function __construct($size, $header, $header_date, $business, $month, $year, $type, $lines)
    {
    	$this->size = $size;
    	$this->header = $header;
    	$this->header_date = $header_date;
    	$this->business = $business;
    	$this->month = $month;
    	$this->year = $year;
    	$this->type = $type;
    	$this->lines = $lines;
    }

    public function title(): string
    {
    	return __('accounting.iva_books');
    }

    public function registerEvents(): array
    {
    	return [
    		AfterSheet::class    => function(AfterSheet $event) {

    			$type = $this->type;
    			if ($type == 'sells') {
    				$event->sheet->columnWidth('A', 17);
    				$event->sheet->columnWidth('B', 11.71);
    				$event->sheet->columnWidth('C', 11.71);
    				$event->sheet->columnWidth('D', 18);
    				$event->sheet->columnWidth('E', 16);
    				$event->sheet->columnWidth('F', 16);
    				$event->sheet->columnWidth('G', 16);
    				$event->sheet->columnWidth('H', 16);
    				$event->sheet->columnWidth('I', 16);

    				$event->sheet->horizontalAlign('A1:I2' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	

    				$event->sheet->horizontalAlign('A6:I7' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    				$event->sheet->verticalAlign('A6:I7' , \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    				$event->sheet->wrapText('A6:I7');
    				$event->sheet->setFormat('E8:I1000', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
    			}

    			if ($type == 'sells_taxpayer') {
                    $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    				$event->sheet->columnWidth('A', 16);
                    $event->sheet->columnWidth('B', 16);
                    $event->sheet->columnWidth('C', 16);
                    $event->sheet->columnWidth('D', 18);
                    $event->sheet->columnWidth('E', 16);
                    $event->sheet->columnWidth('F', 40);
                    $event->sheet->columnWidth('G', 16);
                    $event->sheet->columnWidth('H', 16);
                    $event->sheet->columnWidth('I', 16);
                    $event->sheet->columnWidth('J', 16);
                    $event->sheet->columnWidth('K', 16);
                    $event->sheet->columnWidth('L', 16);
                    $event->sheet->columnWidth('M', 16);
                    $event->sheet->columnWidth('N', 16);

                    $event->sheet->horizontalAlign('A1:N2' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 

                    $event->sheet->horizontalAlign('A6:N8' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $event->sheet->verticalAlign('A6:N8' , \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $event->sheet->wrapText('A6:N8');
                    $event->sheet->setFormat('G9:N1000', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                }

                if ($type == 'purchases') {
                    $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                    $event->sheet->columnWidth('A', 16);
                    $event->sheet->columnWidth('B', 16);
                    $event->sheet->columnWidth('C', 16);
                    $event->sheet->columnWidth('D', 18);
                    $event->sheet->columnWidth('E', 16);
                    $event->sheet->columnWidth('F', 40);
                    $event->sheet->columnWidth('G', 16);
                    $event->sheet->columnWidth('H', 16);
                    $event->sheet->columnWidth('I', 16);
                    $event->sheet->columnWidth('J', 16);
                    $event->sheet->columnWidth('K', 16);
                    $event->sheet->columnWidth('L', 16);
                    $event->sheet->columnWidth('M', 16);
                    $event->sheet->columnWidth('N', 16);

                    $event->sheet->horizontalAlign('A1:N2' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 

                    $event->sheet->horizontalAlign('A6:N7' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $event->sheet->verticalAlign('A6:N7' , \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $event->sheet->wrapText('A6:N7');
                    $event->sheet->setFormat('G8:N1000', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                }

                if ($type == 'sells_exports') {
                    $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                    $event->sheet->columnWidth('A', 7);
                    $event->sheet->columnWidth('B', 14);
                    $event->sheet->columnWidth('C', 14);
                    $event->sheet->columnWidth('D', 19);
                    $event->sheet->columnWidth('E', 13);
                    $event->sheet->columnWidth('F', 39);
                    $event->sheet->columnWidth('G', 11);
                    $event->sheet->columnWidth('H', 11);

                    $event->sheet->horizontalAlign('A1:H6' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->horizontalAlign('A7:B1000' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $event->sheet->verticalAlign('A1:H6' , \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $event->sheet->wrapText('A1:H6');

                    $event->sheet->setFormat('G7:H1000', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                    
                }



            },
        ];
    }

    public function view(): View
    {
    	return view('reports.iva_excel', [
    		'size' => $this->size,
    		'header' => $this->header,
    		'header_date' => $this->header_date,
    		'business' => $this->business,
    		'month' => $this->month,
    		'year' => $this->year,
    		'type' => $this->type,
    		'lines' => $this->lines,
    	]);
    }
}
