<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PayrollSalaryReportExport implements WithEvents, WithTitle, ShouldAutoSize
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
    	return 'Planilla de salarios';
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
                $event->sheet->columnWidth('A', 30); // employee
                $event->sheet->columnWidth('B', 8); // days
                $event->sheet->columnWidth('C', 12); // hours worked
                $event->sheet->columnWidth('D', 12); // salary
                $event->sheet->columnWidth('E', 16); // daytime overtime
                $event->sheet->columnWidth('F', 16); // night overtime
                $event->sheet->columnWidth('G', 17); // total overtime
                $event->sheet->columnWidth('H', 13); // subtotal
                $event->sheet->columnWidth('I', 9); // isss
                $event->sheet->columnWidth('J', 9); // afp
                $event->sheet->columnWidth('K', 9); // rent
                $event->sheet->columnWidth('L', 15); // other deductions
                $event->sheet->columnWidth('M', 12); // total to pay
                $event->sheet->setFormat('A7:M' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                /** Business name */
                $event->sheet->horizontalAlign('A1:M1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:M1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:M1')->getFont()->setSize(15);
    			$event->sheet->mergeCells('A1:M1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business->name));

                /** Report name */
                $event->sheet->horizontalAlign('A2:M2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:M2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2:M2')->getFont()->setSize(13);
                $event->sheet->mergeCells('A2:M2');
                $event->sheet->setCellValue('A2', mb_strtoupper($payroll->name));


                /** Type Payroll */
                $event->sheet->horizontalAlign('A3:M3', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A3:M3')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3:M3')->getFont()->setSize(13);
                $event->sheet->mergeCells('A3:M3');
                $event->sheet->setCellValue('A3', mb_strtoupper($payroll->payrollType->name));

                /** Period Payroll */
                $event->sheet->horizontalAlign('A4:M4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A4:M4')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A4:M4')->getFont()->setSize(13);
                $event->sheet->mergeCells('A4:M4');
                $event->sheet->setCellValue('A4', $this->moduleUtil->format_date($payroll->start_date). ' - '. $this->moduleUtil->format_date($payroll->end_date));


                /** table body */
                $count = 5;

                /** table head */
                $event->sheet->horizontalAlign('A' . $count . ':M' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':M' . $count)->getFont()->setBold(true);
                $event->sheet->getStyle('A'. $count.':M'. $count,  $event->sheet->getHighestRow())->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getRowDimension($count)->setRowHeight(25);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('rrhh.employee')));
                $event->sheet->setCellValue('B'.$count, mb_strtoupper(__('payroll.days')));
                $event->sheet->setCellValue('C'.$count, mb_strtoupper(__('payroll.hours')));
                $event->sheet->setCellValue('D'.$count, mb_strtoupper(__('rrhh.salary')));
                $event->sheet->setCellValue('E'.$count, mb_strtoupper(__('payroll.daytime_overtime')));
                $event->sheet->setCellValue('F'.$count, mb_strtoupper(__('payroll.night_overtime_hours')));
                $event->sheet->setCellValue('G'.$count, mb_strtoupper(__('payroll.total_hours')));
                $event->sheet->setCellValue('H'.$count, mb_strtoupper(__('payroll.subtotal')));
                $event->sheet->setCellValue('I'.$count, 'ISSS');
                $event->sheet->setCellValue('J'.$count, 'AFP');
                $event->sheet->setCellValue('K'.$count, mb_strtoupper(__('payroll.rent')));
                $event->sheet->setCellValue('L'.$count, mb_strtoupper(__('payroll.other_deductions')));
                $event->sheet->setCellValue('M'.$count, mb_strtoupper(__('payroll.total_to_pay')));

                /** table body */
                $count = $count+1;
                $payrollDetails = $this->payrollDetails;
                foreach($payrollDetails as $payrollDetail){
                    $event->sheet->horizontalAlign('A'. $count.':M'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->setCellValue('A'. $count, $payrollDetail->employee->first_name.' '.$payrollDetail->employee->last_name);
                    $event->sheet->setCellValue('B'. $count, $payrollDetail->days);
                    $event->sheet->setCellValue('C'. $count, $payrollDetail->hours);
                    $event->sheet->setCellValue('D'. $count, $this->moduleUtil->num_f($payrollDetail->salary, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('E'. $count, $this->moduleUtil->num_f($payrollDetail->daytime_overtime, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('F'. $count, $this->moduleUtil->num_f($payrollDetail->night_overtime_hours, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('G'. $count, $this->moduleUtil->num_f($payrollDetail->total_hours, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('H'. $count, $this->moduleUtil->num_f($payrollDetail->subtotal, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('I'. $count, $this->moduleUtil->num_f($payrollDetail->isss, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('J'. $count, $this->moduleUtil->num_f($payrollDetail->afp, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('K'. $count, $this->moduleUtil->num_f($payrollDetail->rent, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('L'. $count, $this->moduleUtil->num_f($payrollDetail->other_deductions, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('M'. $count, $this->moduleUtil->num_f($payrollDetail->total_to_pay, $add_symbol = true, $precision = 2));

                    $count++;
                }
            },
        ];
    }
}
