<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class LedgerReportExport implements FromView, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $accounts;
    private $lines;
    private $report_name;
    private $date_range;
    private $business_name;

    public function __construct($accounts, $lines, $report_name, $date_range, $business_name)
    {
    	$this->accounts = $accounts;
    	$this->lines = $lines;
    	$this->report_name = $report_name;
        $this->date_range = $date_range;
        $this->business_name = $business_name;
    }

    public function title(): string
    {
    	return __('accounting.ledgers_menu');
    }

    public function registerEvents(): array
    {
    	return [            
    		AfterSheet::class    => function(AfterSheet $event) {
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->setShowGridlines(false);
    			$event->sheet->setFontFamily('A1:F1500', 'Calibri');
    			$event->sheet->setFontSize('A1:F1500' , 10);

    			$event->sheet->mergeCells('A1:E1');
    			$event->sheet->mergeCells('A2:E2');
    			$event->sheet->mergeCells('A3:E3');
    			$event->sheet->mergeCells('A4:E4');
    			$event->sheet->horizontalAlign('A1:E2' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A4:E4' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 9.71);
                $event->sheet->columnWidth('B', 60);
                $event->sheet->columnWidth('C', 11.85);
                $event->sheet->columnWidth('D', 11.85);
                $event->sheet->columnWidth('E', 11.85);
                $event->sheet->setFormat('C1:E1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
            },
        ];
    }

    public function view(): View
    {
    	return view('reports.ledgers_excel', [
    		'accounts' => $this->accounts,
    		'lines' => $this->lines,
    		'report_name' => $this->report_name,
            'date_range' => $this->date_range,
            'business_name' => $this->business_name,
        ]);
    }
}
