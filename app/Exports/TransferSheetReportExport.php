<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TransferSheetReportExport implements FromView, WithEvents, WithTitle
{
    private $lines;
    private $size;
    private $business;
    private $enable_signature_column;
    private $delivers;
    private $receives;

    /**
     * Constructor.
     * 
     * @param  array  $lines
     * @param  int  $size
     * @param  \App\Business  $business
     * @param  int  $enable_signature_column
     * @param  string  $size
     * @param  string  $size
     * @return void
     */
    public function __construct($lines, $size, $business, $enable_signature_column, $delivers, $receives)
    {
    	$this->lines = $lines;
        $this->size = $size;
        $this->business = $business;
        $this->enable_signature_column = $enable_signature_column;
        $this->delivers = $delivers;
        $this->receives = $receives;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('lab_order.transfers_sheet');
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

    			$event->sheet->setFontFamily('A1:G1500', 'Calibri');
    			$event->sheet->setFontSize('A1:G1500', 10);

                $event->sheet->horizontalAlign('A1:G2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A4:G4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 5.31);
                $event->sheet->columnWidth('B', 18.46);
                $event->sheet->columnWidth('C', 19.74);
                $event->sheet->columnWidth('D', 11.31);
                $event->sheet->columnWidth('E', 62.74);
                $event->sheet->columnWidth('F', 8.46);
                $event->sheet->columnWidth('G', 8.46);

                $event->sheet->setFormat('D5:D1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
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
    	return view('reports.transfer_sheet_excel', [
    		'lines' => $this->lines,
    		'size' => $this->size,
    		'business' => $this->business,
            'enable_signature_column' => $this->enable_signature_column,
            'delivers' => $this->delivers,
            'receives' => $this->receives,
        ]);
    }
}
