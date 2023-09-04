<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PayrollHonoraryReportExport implements WithEvents, WithTitle, ShouldAutoSize
{
    private $payroll;
    private $payrollDetails;
    private $business;
    private $moduleUtil;

    /**
     * Constructor.
     * 
     * @param  array  $payroll
     * @param  array  $payrollDetails
     * @param  \App\Business  $business
     * @param  $moduleUtil
     * @return void
     */
    public function __construct($payroll, $payrollDetails, $business, $moduleUtil)
    {
    	$this->payroll = $payroll;
        $this->payrollDetails = $payrollDetails;
        $this->business = $business;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return 'Planilla de honorarios';
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
                $items = count($this->payrollDetails) + 4;
                $payroll = $this->payroll;

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                /** Columns style */
                $event->sheet->columnWidth('A', 45); // employee
                $event->sheet->columnWidth('B', 25); // subtotal
                $event->sheet->columnWidth('C', 15); // rent
                $event->sheet->columnWidth('D', 20); // total to pay
                $event->sheet->setFormat('A7:D' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                /** Business name */
                $event->sheet->horizontalAlign('A1:D1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:D1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:D1')->getFont()->setSize(15);
    			$event->sheet->mergeCells('A1:D1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business->name));

                /** Report name */
                $event->sheet->horizontalAlign('A2:D2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:D2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2:D2')->getFont()->setSize(13);
                $event->sheet->mergeCells('A2:D2');
                $event->sheet->setCellValue('A2', mb_strtoupper($payroll->name));


                /** Type Payroll */
                $event->sheet->horizontalAlign('A3:D3', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A3:D3')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3:D3')->getFont()->setSize(13);
                $event->sheet->mergeCells('A3:D3');
                $event->sheet->setCellValue('A3', mb_strtoupper($payroll->payrollType->name));

                /** Period Payroll */
                $event->sheet->horizontalAlign('A4:D4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A4:D4')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A4:D4')->getFont()->setSize(13);
                $event->sheet->mergeCells('A4:D4');
                $event->sheet->setCellValue('A4', $this->moduleUtil->format_date($payroll->start_date). ' - '. $this->moduleUtil->format_date($payroll->end_date));


                /** table body */
                $count = 5;

                /** table head */
                $event->sheet->horizontalAlign('A' . $count . ':D' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':D' . $count)->getFont()->setBold(true);
                $event->sheet->getStyle('A'. $count.':D'. $count,  $event->sheet->getHighestRow())->getAlignment()->setWrapText(true);
                //$event->sheet->getDelegate()->getRowDimension($count)->setRowHeight(25);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('rrhh.employee')));
                $event->sheet->setCellValue('B'.$count, mb_strtoupper(__('payroll.subtotal')));
                $event->sheet->setCellValue('C'.$count, mb_strtoupper(__('payroll.rent')));
                $event->sheet->setCellValue('D'.$count, mb_strtoupper(__('payroll.total_to_pay')));

                /** table body */
                $count = $count+1;
                $payrollDetails = $this->payrollDetails;
                foreach($payrollDetails as $payrollDetail){
                    $event->sheet->horizontalAlign('A'. $count.':D'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->setCellValue('A'. $count, $payrollDetail->employee->first_name.' '.$payrollDetail->employee->last_name);
                    $event->sheet->setCellValue('B'. $count, $this->moduleUtil->num_f($payrollDetail->subtotal, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('C'. $count, $this->moduleUtil->num_f($payrollDetail->rent, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('D'. $count, $this->moduleUtil->num_f($payrollDetail->total_to_pay, $add_symbol = true, $precision = 2));

                    $count++;
                }
            },
        ];
    }
}
