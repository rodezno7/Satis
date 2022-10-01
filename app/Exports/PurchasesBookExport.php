<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class PurchasesBookExport extends DefaultValueBinder implements FromView, WithEvents, WithTitle, WithCustomValueBinder
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
    	return __('accounting.purchases_book');
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() == 'E') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // Else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function registerEvents(): array
    {
    	return [            
    		AfterSheet::class => function(AfterSheet $event) {
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->setFontFamily('A1:O1500', 'Calibri');
    			$event->sheet->setFontSize('A1:O1500', 10);

    			$event->sheet->mergeCells('A1:O1');
    			$event->sheet->mergeCells('A2:O2');
                $event->sheet->horizontalAlign('A1:O2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    			$event->sheet->horizontalAlign('A5:O6', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 3.71);
                $event->sheet->columnWidth('B', 10.86);
                $event->sheet->columnWidth('C', 12.00);
                $event->sheet->columnWidth('D', 9.43);
                $event->sheet->columnWidth('E', 13.29);
                $event->sheet->columnWidth('F', 13.29);
                $event->sheet->columnWidth('G', 41.29);
                $event->sheet->columnWidth('H', 12.57);
                $event->sheet->columnWidth('I', 14.29);
                $event->sheet->columnWidth('J', 13.57);
                $event->sheet->columnWidth('K', 14.43);
                $event->sheet->columnWidth('L', 10.57);
                $event->sheet->columnWidth('M', 12.86);
                $event->sheet->columnWidth('N', 12.29);
                $event->sheet->columnWidth('O', 12.29);

                $event->sheet->setFormat('B7:B1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                $event->sheet->setFormat('C7:F1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                //$event->sheet->setFormat('C7:F1500', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $event->sheet->setFormat('H7:O1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
            },
        ];
    }

    public function view(): View
    {
    	return view('reports.purchases_book_excel', [
    		'lines' => $this->lines,
    		'business' => $this->business,
    		'initial_month' => $this->initial_month,
    		'final_month' => $this->final_month,
            'initial_year' => $this->initial_year,
            'final_year' => $this->final_year,
        ]);
    }
}
