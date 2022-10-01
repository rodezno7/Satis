<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AllSalesReportExport implements FromView, WithEvents, WithTitle
{
    private $sales;
    private $size;
    private $business;

    /**
     * Constructor.
     * 
     * @param  array  $sales
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($sales, $size, $business)
    {
    	$this->sales = $sales;
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
    	return __('report.all_sales_report');
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
                if (config('app.business') == 'optics') {
                    $final_col = 'J';
                } else {
                    $final_col = 'K';
                }

    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    			$event->sheet->setFontFamily('A1:' . $final_col . '9500', 'Calibri');
    			$event->sheet->setFontSize('A1:' . $final_col . '9500', 10);

    			$event->sheet->mergeCells('A1:' . $final_col . '1');
    			$event->sheet->mergeCells('A2:' . $final_col . '2');

                $event->sheet->horizontalAlign('A1:' . $final_col . '2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->horizontalAlign('A4:' . $final_col . '4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->columnWidth('A', 18.46);
                $event->sheet->columnWidth('B', 14.89);
                $event->sheet->columnWidth('C', 25.74);
                
                if (config('app.business') != 'optics') {
                    $event->sheet->columnWidth('D', 16.74);
                    $event->sheet->columnWidth('E', 58.31);
                } else {
                    $event->sheet->columnWidth('D', 58.31);
                    $event->sheet->columnWidth('E', 38.31);
                }

                $event->sheet->columnWidth('F', 14.17);
                $event->sheet->columnWidth('G', 16.31);
                $event->sheet->columnWidth('H', 16.31);
                $event->sheet->columnWidth('I', 16.31);
                $event->sheet->columnWidth('J', 16.31);

                if (config('app.business') != 'optics') {
                    $event->sheet->columnWidth('K', 16.31);
                }

                $event->sheet->setFormat('A5:A9500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

                if (config('app.business') == 'optics') {
                    $event->sheet->setFormat('G5:G9500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                    $event->sheet->setFormat('I5:J9500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                } else {
                    $event->sheet->setFormat('G5:K9500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                }
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
    	return view('reports.all_sales_report_excel', [
    		'sales' => $this->sales,
    		'size' => $this->size,
    		'business' => $this->business,
        ]);
    }
}
