<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class BookTaxpayerExport implements FromView, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $lines;
    private $business;
    private $initial_month;
    private $final_month;
    private $initial_year;
    private $final_year;

    public function __construct($lines, $business, $initial_month, $final_month, $initial_year, $final_year)
    {
    	$this->lines = $lines;
    	$this->business = $business;
    	$this->initial_month = $initial_month;
    	$this->final_month = $final_month;
        $this->initial_year = $initial_year;
        $this->final_year = $final_year;
    }

    public function title(): string
    {
    	return __('accounting.book_sales_taxpayer');
    }

    public function registerEvents(): array
    {
    	return [            
    		AfterSheet::class => function(AfterSheet $event) {
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->setFontFamily('A1:R1500', 'Calibri');
    			$event->sheet->setFontSize('A1:R1500', 10);

    			$event->sheet->mergeCells('A1:R1');
    			$event->sheet->mergeCells('A2:R2');
                $event->sheet->horizontalAlign('A1:R2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    			$event->sheet->horizontalAlign('A6:R8', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 3.14);
                $event->sheet->columnWidth('B', 10.29);
                $event->sheet->columnWidth('C', 10.57);
                $event->sheet->columnWidth('D', 10.57);
                $event->sheet->columnWidth('E', 7.86);
                $event->sheet->columnWidth('F', 8.43);
                $event->sheet->columnWidth('G', 8.71);
                $event->sheet->columnWidth('H', 9.29);
                $event->sheet->columnWidth('I', 9.29);
                $event->sheet->columnWidth('J', 41.57);
                $event->sheet->columnWidth('K', 9.29);
                $event->sheet->columnWidth('L', 9.29);
                $event->sheet->columnWidth('M', 9.29);
                $event->sheet->columnWidth('N', 9.29);
                $event->sheet->columnWidth('O', 9.29);
                $event->sheet->columnWidth('P', 9.29);
                $event->sheet->columnWidth('Q', 9.29);
                $event->sheet->columnWidth('R', 9.29);

                // $event->sheet->setFormat('E9:E1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $event->sheet->setFormat('G7:I1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $event->sheet->setFormat('K9:M1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setFormat('Q9:R1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
            },
        ];
    }

    public function view(): View
    {
    	return view('reports.book_taxpayer_excel', [
    		'lines' => $this->lines,
    		'business' => $this->business,
    		'initial_month' => $this->initial_month,
    		'final_month' => $this->final_month,
            'initial_year' => $this->initial_year,
            'final_year' => $this->final_year,
        ]);
    }
}
