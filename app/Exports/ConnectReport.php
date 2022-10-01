<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ConnectReport implements WithEvents, WithTitle
{
    private $transactions;
    private $business_name;
    private $start_date;
    private $end_date;

    /**
     * Constructor.
     * 
     * @param collect $transactions
     * @param string $business_name
     * @param string $start_date
     * @param string $end_date
     * @return void
     * @author
     */
    public function __construct($transactions, $business_name, $start_date, $end_date)
    {
    	$this->transactions = $transactions;
        $this->business_name = $business_name;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('report.connect_report');
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
                /** General setup */
    			$event->sheet->setOrientation("landscape");
                $event->sheet->setShowGridlines(false);

                /** Header */
                $event->sheet->mergeCells('A1:I1');
                $event->sheet->mergeCells('A2:I2');
                $event->sheet->rowHeight('1', 20);
                $event->sheet->verticalAlign('A1:I1', 'center');
                $event->sheet->horizontalAlign('A1:I2', "center");
                $event->sheet->setBold('A1:I2');
                $event->sheet->setFontSize('A1:I1', 14);
                $event->sheet->setFontSize('A2:I2', 12);
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
                $event->sheet->setCellValue('A2', mb_strtoupper(__('report.connect_report')) ." ". mb_strtoupper(strtoupper(__('accounting.from_date')) ." ". $this->start_date ." ". strtoupper(__('accounting.to_date')) ." ". $this->end_date));

                /** Column width and font align */
                $event->sheet->columnWidth('A', 35); // customer_name
                $event->sheet->columnWidth('B', 12); // latitude
                $event->sheet->columnWidth('C', 12); // length
                $event->sheet->columnWidth('D', 10); // from
                $event->sheet->columnWidth('E', 10); // to
                $event->sheet->columnWidth('F', 10); // cost
                $event->sheet->columnWidth('G', 10); // weight
                $event->sheet->columnWidth('H', 10); // volume
                $event->sheet->columnWidth('I', 10); // download_time

                /** table head */
                $event->sheet->setBold('A3:I3');
                $event->sheet->horizontalAlign('A3:H3', 'center');
                $event->sheet->horizontalAlign('I3', 'left');
                $event->sheet->verticalAlign('A3:I3', 'center');
                $event->sheet->setCellValue('A3', mb_strtoupper(__('customer.customer')));
                $event->sheet->setCellValue('B3', mb_strtoupper(__('customer.latitude')));
                $event->sheet->setCellValue('C3', mb_strtoupper(__('customer.length')));
                $event->sheet->setCellValue('D3', mb_strtoupper(__('accounting.from')));
                $event->sheet->setCellValue('E3', mb_strtoupper(__('accounting.to')));
                $event->sheet->setCellValue('F3', mb_strtoupper(__('product.cost')));
                $event->sheet->setCellValue('G3', mb_strtoupper(__('lang_v1.weight')));
                $event->sheet->setCellValue('H3', mb_strtoupper(__('product.volume')));
                $event->sheet->setCellValue('I3', mb_strtoupper(__('product.download_time')));
                
                /** table body */
                $row = 4;
                foreach($this->transactions as $t) {
                    $event->sheet->setCellValue('A'. $row, $t->customer_name);
                    $event->sheet->setCellValue('B'. $row, $t->latitude);
                    $event->sheet->setCellValue('C'. $row, $t->length);
                    $event->sheet->setCellValue('D'. $row, date('H:i:s', strtotime($t->from)));
                    $event->sheet->setCellValue('E'. $row, date('H:i:s', strtotime($t->to)));
                    $event->sheet->setCellValue('F'. $row, $t->cost);
                    $event->sheet->setCellValue('G'. $row, $t->weight);
                    $event->sheet->setCellValue('H'. $row, $t->volume);
                    $event->sheet->setCellValue('I'. $row, date('H:i:s', strtotime($t->download_time)));

                    $row ++;
                }
                $row --;

                /** set font size and family, set borders */
    			$event->sheet->setFontSize('A3:I'. $row, 10);
                $event->sheet->horizontalAlign('B4:I'. $row, 'right');
                $event->sheet->setFormat('A4:A'. $row, '@');
                $event->sheet->setFormat('B4:C'. $row, '0.00000000');
                $event->sheet->setFormat('D4:E'. $row, 'h:mm:ss');
                $event->sheet->setFormat('F4:G'. $row, '0.00');
                $event->sheet->setFormat('H4:H'. $row, '0.000000');
                $event->sheet->setFormat('I4:I'. $row, 'h:mm:ss');
                $event->sheet->setAllBorders('A3:I'. ($row), 'thin');
                $event->sheet->setFontFamily('A1:I'. ($row), 'Calibri');
            },
        ];
    }
}
