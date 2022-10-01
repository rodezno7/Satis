<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class BookFinalConsumerExport implements FromView, WithEvents, WithTitle
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
    	return __('accounting.book_sales_final_consumer');
    }

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
                $event->sheet->horizontalAlign('A7:I8', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 10.71);
                $event->sheet->columnWidth('B', 8.43);
                $event->sheet->columnWidth('C', 8.43);
                $event->sheet->columnWidth('D', 12.57);
                $event->sheet->columnWidth('E', 10.14);
                $event->sheet->columnWidth('F', 12.29);
                $event->sheet->columnWidth('G', 13.00);
                $event->sheet->columnWidth('H', 12.43);
                $event->sheet->columnWidth('I', 10.71);

                $event->sheet->setFormat('A9:A1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $event->sheet->setFormat('G9:K1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
            },
        ];
    }

    public function view(): View
    {
    	return view('reports.book_final_consumer_excel', [
    		'lines' => $this->lines,
    		'business' => $this->business,
    		'initial_month' => $this->initial_month,
    		'final_month' => $this->final_month,
            'initial_year' => $this->initial_year,
            'final_year' => $this->final_year,
        ]);
    }
}
