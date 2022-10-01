<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class AuxiliarReportExport implements FromView, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $accounts;
    private $details;
    private $report_name;
    private $date_range;
    private $business_name;

    public function __construct($accounts, $details, $report_name, $date_range, $business_name)
    {
    	$this->accounts = $accounts;
    	$this->details = $details;
    	$this->report_name = $report_name;
    	$this->date_range = $date_range;
        $this->business_name = $business_name;
    }

    public function title(): string
    {
    	return __('accounting.auxiliars_menu');
    }

    public function registerEvents(): array
    {
    	return [            
    		AfterSheet::class    => function(AfterSheet $event) {
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->setFontFamily('A1:F1500', 'Calibri');
    			$event->sheet->setFontSize('A1:F1500' , 10);
                
                            
    			$event->sheet->columnWidth('C', 60);
    			$event->sheet->columnWidth('D', 11.85);
    			$event->sheet->columnWidth('E', 11.85);
    			$event->sheet->columnWidth('F', 11.85);
    			$event->sheet->columnWidth('A', 15.50);
    			$event->sheet->columnWidth('B', 9.15);
                $event->sheet->setFormat('D1:F1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setRowsToRepeatAtTopByStartAndEnd(1, 5);
    		},
    	];
    }

    public function view(): View {

    	return view('reports.auxiliars_excel', [
    		'accounts' => $this->accounts,
    		'details' => $this->details,
    		'report_name' => $this->report_name,
    		'date_range' => $this->date_range,
    		'business_name' => $this->business_name,
    	]);
    }
}
