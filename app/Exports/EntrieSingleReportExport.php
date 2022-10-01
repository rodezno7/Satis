<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class EntrieSingleReportExport implements FromView, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
	private $enable_description_line;
    private $datos;
    private $numero;
    private $business_name;

    public function __construct($enable_description_line, $datos, $numero, $business_name)
    {
		$this->enable_description_line = $enable_description_line;
    	$this->datos = $datos;
    	$this->numero = $numero;
    	$this->business_name = $business_name;
    }

    public function title(): string
    {
    	return __('accounting.entries_menu');
    }

    public function registerEvents(): array
    {
    	return [            
    		AfterSheet::class    => function(AfterSheet $event) {

    			$event->sheet->setFontFamily('A1:F5000', 'Calibri');
    			$event->sheet->setFontSize('A1:F5000' , 10);
    			$event->sheet->horizontalAlign('A1:F2' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    			$event->sheet->horizontalAlign('B1:B5000' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    			$event->sheet->setFormat('D1:F5000', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

    			$event->sheet->columnWidth('A', 14);
    			$event->sheet->columnWidth('B', 35);
    			$event->sheet->columnWidth('C', 35);
    			$event->sheet->columnWidth('D', 11.85);
    			$event->sheet->columnWidth('E', 11.85);
    			$event->sheet->columnWidth('F', 11.85);
    			
    		},
    	];
    }

    public function view(): View
    {
    	return view('reports.entrie_excel', [
			'enable_description_line' => $this->enable_description_line,
    		'datos' => $this->datos,
    		'numero' => $this->numero,
    		'business_name' => $this->business_name,
    	]);
    }
}
