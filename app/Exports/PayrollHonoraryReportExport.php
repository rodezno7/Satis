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
                $event->sheet->columnWidth('A', 20); // code
                $event->sheet->columnWidth('B', 45); // employee
                $event->sheet->columnWidth('C', 25); // subtotal
                $event->sheet->columnWidth('D', 15); // rent
                $event->sheet->columnWidth('E', 20); // total to pay
                $event->sheet->setFormat('A7:E' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                /** Business name */
                $event->sheet->horizontalAlign('A1:E1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:E1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:E1')->getFont()->setSize(15);
    			$event->sheet->mergeCells('A1:E1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business->name));

                /** Report name */
                $event->sheet->horizontalAlign('A2:E2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:E2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2:E2')->getFont()->setSize(13);
                $event->sheet->mergeCells('A2:E2');
                $event->sheet->setCellValue('A2', mb_strtoupper($payroll->name));

                /** Type Payroll */
                $event->sheet->horizontalAlign('A3:E3', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A3:E3')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3:E3')->getFont()->setSize(13);
                $event->sheet->mergeCells('A3:E3');
                $event->sheet->setCellValue('A3', mb_strtoupper($payroll->payrollType->name));

                /** Period Payroll */
                $event->sheet->horizontalAlign('A4:E4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A4:E4')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A4:E4')->getFont()->setSize(13);
                $event->sheet->mergeCells('A4:E4');
                $event->sheet->setCellValue('A4', $this->moduleUtil->format_date($payroll->start_date). ' - '. $this->moduleUtil->format_date($payroll->end_date));

                /** table head */
                $count = 5;
                $event->sheet->horizontalAlign('A' . $count . ':E' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':E' . $count)->getFont()->setBold(true);
                $event->sheet->getStyle('A'. $count.':E'. $count,  $event->sheet->getHighestRow())->getAlignment()->setWrapText(true);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('rrhh.code')));
                $event->sheet->setCellValue('B'.$count, mb_strtoupper(__('rrhh.employee')));
                $event->sheet->setCellValue('C'.$count, mb_strtoupper(__('payroll.total_calculation')));
                $event->sheet->setCellValue('D'.$count, mb_strtoupper(__('payroll.rent')));
                $event->sheet->setCellValue('E'.$count, mb_strtoupper(__('payroll.total_to_pay')));

                /** table body */
                $count = $count+1;
                $payrollDetails = $this->payrollDetails;
                $regular_salary = 0;
                $rent = 0;
                $total_to_pay = 0;
                foreach($payrollDetails as $payrollDetail){
                    $event->sheet->horizontalAlign('A'. $count.':E'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->setCellValue('A'. $count, $payrollDetail->employee->agent_code);
                    $event->sheet->setCellValue('B'. $count, $payrollDetail->employee->first_name.' '.$payrollDetail->employee->last_name);
                    $event->sheet->setCellValue('C'. $count, $this->moduleUtil->num_f($payrollDetail->regular_salary, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('D'. $count, $this->moduleUtil->num_f($payrollDetail->rent, $add_symbol = true, $precision = 2));
                    $event->sheet->getDelegate()->getStyle('E' . $count)->getFont()->setBold(true);
                    $event->sheet->setCellValue('E'. $count, $this->moduleUtil->num_f($payrollDetail->total_to_pay, $add_symbol = true, $precision = 2));

                    $regular_salary += $payrollDetail->regular_salary;
                    $rent += $payrollDetail->rent;
                    $total_to_pay += $payrollDetail->total_to_pay;

                    $count++;
                }

                $event->sheet->horizontalAlign('A' . $count . ':E' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':E' . $count)->getFont()->setBold(true);
                $event->sheet->mergeCells('A'. $count.':B'. $count);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('payroll.totals')));
                $event->sheet->setCellValue('C'.$count, $this->moduleUtil->num_f($regular_salary, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('D'.$count, $this->moduleUtil->num_f($rent, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('E'.$count, $this->moduleUtil->num_f($total_to_pay, $add_symbol = true, $precision = 2));
            },
        ];
    }
}
