<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DetailedCommissionsReportExport implements FromView, WithEvents, WithTitle
{
    private $commissions;
    private $size;
    private $business;

    /**
     * Constructor.
     * 
     * @param  array  $commissions
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($commissions, $size, $business)
    {
    	$this->commissions = $commissions;
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
    	return __('report.detailed_commissions_report');
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

    			$event->sheet->setFontFamily('A1:W9500', 'Calibri');
    			$event->sheet->setFontSize('A1:W9500', 10);

    			$event->sheet->mergeCells('A1:W1');
    			$event->sheet->mergeCells('A2:W2');

                $event->sheet->horizontalAlign('A1:W2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A4:W4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 6.03);
                $event->sheet->columnWidth('B', 6.03);
                $event->sheet->columnWidth('C', 6.03);
                $event->sheet->columnWidth('D', 11.74);
                $event->sheet->columnWidth('E', 14.17);
                $event->sheet->columnWidth('F', 17.46);
                $event->sheet->columnWidth('G', 16.74);
                $event->sheet->columnWidth('H', 16.74);
                $event->sheet->columnWidth('I', 34.89);
                $event->sheet->columnWidth('J', 12.17);
                $event->sheet->columnWidth('K', 12.17);
                $event->sheet->columnWidth('L', 12.17);
                $event->sheet->columnWidth('M', 12.31);
                $event->sheet->columnWidth('N', 34.89);
                $event->sheet->columnWidth('O', 13.17);
                $event->sheet->columnWidth('P', 13.03);
                $event->sheet->columnWidth('Q', 13.03);
                $event->sheet->columnWidth('R', 35.03);
                $event->sheet->columnWidth('S', 13.03);
                $event->sheet->columnWidth('T', 13.03);
                $event->sheet->columnWidth('U', 16.77);
                $event->sheet->columnWidth('V', 13.46);
                $event->sheet->columnWidth('W', 13.46);

                $event->sheet->setFormat('O5:O9500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
                $event->sheet->setFormat('P5:Q9500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setFormat('S5:T9500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
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
    	return view('reports.detailed_commissions_report_excel', [
    		'commissions' => $this->commissions,
    		'size' => $this->size,
    		'business' => $this->business,
        ]);
    }
}
