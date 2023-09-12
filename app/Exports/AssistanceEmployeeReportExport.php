<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AssistanceEmployeeReportExport implements WithEvents, WithTitle
{
    private $assistances;
    private $assistanceSummary;
    private $business;
    private $transactionUtil;

    /**
     * Constructor.
     * 
     * @param  array  $assistance
     * @param  array  $assistanceSummary
     * @param  int  $size
     * @param  \App\Business  $business
     * @return void
     */
    public function __construct($assistances, $assistanceSummary, $business, $transactionUtil)
    {
    	$this->assistances = $assistances;
        $this->assistanceSummary = $assistanceSummary;
        $this->business = $business;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return __('rrhh.employee_assistance_report');
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
                $items = count($this->assistanceSummary) + 4;
                $assistanceSummary = $this->assistanceSummary;

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                /** Columns style */
                $event->sheet->columnWidth('A', 35); // employee
                $event->sheet->columnWidth('B', 20); // start date
                $event->sheet->columnWidth('C', 20); // end date
                $event->sheet->columnWidth('D', 23); // time worked
                $event->sheet->setFormat('A5:D' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                /** Business name */
                $event->sheet->horizontalAlign('A1:D1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:D1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:D1')->getFont()->setSize(15);
    			$event->sheet->mergeCells('A1:D1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business->name));

                /** Report name */
                $event->sheet->mergeCells('A2:D2');
                $event->sheet->horizontalAlign('A3:D3', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A3:D3')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3:D3')->getFont()->setSize(13);
                $event->sheet->mergeCells('A3:D3');
                $event->sheet->setCellValue('A3', mb_strtoupper(__('rrhh.assistance_summary')));

                /** table head */
                $event->sheet->horizontalAlign('A4:D4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A4:D4')->getFont()->setBold(true);
                $event->sheet->setCellValue('A4', mb_strtoupper(__('rrhh.employee')));
                $event->sheet->mergeCells('B4:C4');
                $event->sheet->setCellValue('B4', mb_strtoupper(__('rrhh.schedule')));
                $event->sheet->mergeCells('D4:E4');
                $event->sheet->setCellValue('D4', mb_strtoupper(__('rrhh.time_worked')));


                /** table body */
                $count = 5;
                foreach($assistanceSummary as $s){
                    $event->sheet->horizontalAlign('B'. $count.':D'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->setCellValue('A'. $count, $s->employee);
                    $event->sheet->setCellValue('B'. $count, $s->start_date);
                    $event->sheet->setCellValue('C'. $count, $s->end_date);
                    $event->sheet->mergeCells('D'.$count.':E'. $count);
                    $event->sheet->setCellValue('D'. $count, $s->time_worked);

                    $count++;
                }

                $count = $count + 1;

                /** Columns style */
                //$event->sheet->columnWidth('D', 15); // country
                $event->sheet->columnWidth('E', 23); // city
                $event->sheet->columnWidth('F', 15); // latitude
                $event->sheet->columnWidth('G', 15); // longitude
                $event->sheet->columnWidth('H', 15); // type

                $event->sheet->mergeCells('A'. $count . ':H'. $count);
                /** Report name */
                $event->sheet->horizontalAlign('A' . $count . ':H' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':H' . $count)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':H' . $count)->getFont()->setSize(13);
                $event->sheet->mergeCells('A' . $count . ':H' . $count);
                $event->sheet->setCellValue('A' . $count, mb_strtoupper(__('rrhh.employee_assistance_detail')));

                /** table head */
                $count = $count+1;
                $event->sheet->horizontalAlign('A' . $count . ':H' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':H' . $count)->getFont()->setBold(true);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('rrhh.employee')));
                $event->sheet->setCellValue('B'.$count, mb_strtoupper(__('rrhh.date')));
                $event->sheet->setCellValue('C'.$count, mb_strtoupper(__('rrhh.ip_address')));
                $event->sheet->setCellValue('D'.$count, mb_strtoupper(__('rrhh.country')));
                $event->sheet->setCellValue('E'.$count, mb_strtoupper(__('rrhh.city')));
                $event->sheet->setCellValue('F'.$count, mb_strtoupper(__('rrhh.latitude')));
                $event->sheet->setCellValue('G'.$count, mb_strtoupper(__('rrhh.longitude')));
                $event->sheet->setCellValue('H'.$count, mb_strtoupper(__('rrhh.type')));

                /** table body */
                $count = $count+1;
                $assistances = $this->assistances;
                foreach($assistances as $s){
                    $event->sheet->horizontalAlign('B'. $count.':H'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->setCellValue('A'. $count, $s->employee->first_name.' '.$s->employee->last_name);
                    $event->sheet->setCellValue('B'. $count, $this->transactionUtil->format_date($s->date).' '.$this->transactionUtil->format_time($s->time));
                    $event->sheet->setCellValue('C'. $count, $s->ip);
                    $event->sheet->setCellValue('D'. $count, $s->country);
                    $event->sheet->setCellValue('E'. $count, $s->city);
                    $event->sheet->setCellValue('F'. $count, $s->latitude);
                    $event->sheet->setCellValue('G'. $count, $s->longitude);
                    $event->sheet->setCellValue('H'. $count, $s->type);

                    $count++;
                }
            },
        ];
    }
}
