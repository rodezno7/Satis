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
                $event->sheet->columnWidth('A', 12); // code
                $event->sheet->columnWidth('B', 32); // employee
                $event->sheet->columnWidth('C', 12); // montly salary
                $event->sheet->columnWidth('D', 8); // days
                $event->sheet->columnWidth('E', 12); // regular salary
                $event->sheet->columnWidth('F', 16); // daytime overtime
                $event->sheet->columnWidth('G', 16); // night overtime
                $event->sheet->columnWidth('H', 13); // other income
                $event->sheet->columnWidth('I', 13); // total_income
                $event->sheet->columnWidth('J', 9); // isss
                $event->sheet->columnWidth('K', 9); // afp
                $event->sheet->columnWidth('L', 9); // rent
                $event->sheet->columnWidth('M', 15); // other deductions
                $event->sheet->columnWidth('N', 15); // total deductions
                $event->sheet->columnWidth('O', 12); // total to pay
                $event->sheet->setFormat('A7:O' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                /** Business name */
                $event->sheet->horizontalAlign('A1:O1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:O1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:O1')->getFont()->setSize(15);
    			$event->sheet->mergeCells('A1:O1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business->name));

                /** Report name */
                $event->sheet->horizontalAlign('A2:O2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:O2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2:O2')->getFont()->setSize(13);
                $event->sheet->mergeCells('A2:O2');
                $event->sheet->setCellValue('A2', mb_strtoupper($payroll->name));

                /** Type Payroll */
                $event->sheet->horizontalAlign('A3:O3', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A3:O3')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3:O3')->getFont()->setSize(13);
                $event->sheet->mergeCells('A3:O3');
                $event->sheet->setCellValue('A3', mb_strtoupper($payroll->payrollType->name));

                /** Period Payroll */
                $event->sheet->horizontalAlign('A4:O4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A4:O4')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A4:O4')->getFont()->setSize(13);
                $event->sheet->mergeCells('A4:O4');
                $event->sheet->setCellValue('A4', $this->moduleUtil->format_date($payroll->start_date). ' - '. $this->moduleUtil->format_date($payroll->end_date));

                /** table head */
                $count = 5;
                $event->sheet->horizontalAlign('A' . $count . ':O' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':O' . $count)->getFont()->setBold(true);
                $event->sheet->getStyle('A'. $count.':O'. $count,  $event->sheet->getHighestRow())->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getRowDimension($count)->setRowHeight(25);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('rrhh.code')));
                $event->sheet->setCellValue('B'.$count, mb_strtoupper(__('rrhh.employee')));
                $event->sheet->setCellValue('C'.$count, mb_strtoupper(__('payroll.montly_salary')));
                $event->sheet->setCellValue('D'.$count, mb_strtoupper(__('payroll.days')));
                $event->sheet->setCellValue('E'.$count, mb_strtoupper(__('payroll.regular_salary1')));
                $event->sheet->setCellValue('F'.$count, mb_strtoupper(__('payroll.daytime_overtime')));
                $event->sheet->setCellValue('G'.$count, mb_strtoupper(__('payroll.night_overtime_hours')));
                $event->sheet->setCellValue('H'.$count, mb_strtoupper(__('payroll.other_income')));
                $event->sheet->setCellValue('I'.$count, mb_strtoupper(__('payroll.total_income')));
                $event->sheet->setCellValue('J'.$count, 'ISSS');
                $event->sheet->setCellValue('K'.$count, 'AFP');
                $event->sheet->setCellValue('L'.$count, mb_strtoupper(__('payroll.rent')));
                $event->sheet->setCellValue('M'.$count, mb_strtoupper(__('payroll.other_deductions')));
                $event->sheet->setCellValue('N'.$count, mb_strtoupper(__('payroll.total_deductions')));
                $event->sheet->setCellValue('O'.$count, mb_strtoupper(__('payroll.total_to_pay')));

                /** table body */
                $count = $count+1;
                $payrollDetails = $this->payrollDetails;
                $regular_salary = 0;
                $daytime_overtime = 0;
                $night_overtime_hours = 0;
                $other_income = 0;
                $total_income = 0;
                $isss = 0;
                $afp = 0;
                $rent = 0;
                $other_deductions = 0;
                $total_deductions = 0;
                $total_to_pay = 0;
                foreach($payrollDetails as $payrollDetail){
                    $event->sheet->horizontalAlign('A'. $count.':O'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->setCellValue('A'. $count, $payrollDetail->employee->agent_code);
                    $event->sheet->setCellValue('B'. $count, $payrollDetail->employee->first_name.' '.$payrollDetail->employee->last_name);
                    $event->sheet->setCellValue('C'. $count, $this->moduleUtil->num_f($payrollDetail->montly_salary, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('D'. $count, $payrollDetail->days);
                    $event->sheet->setCellValue('E'. $count, $this->moduleUtil->num_f($payrollDetail->regular_salary, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('F'. $count, $this->moduleUtil->num_f($payrollDetail->daytime_overtime, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('G'. $count, $this->moduleUtil->num_f($payrollDetail->night_overtime_hours, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('H'. $count, $this->moduleUtil->num_f($payrollDetail->other_income, $add_symbol = true, $precision = 2));
                    $event->sheet->getDelegate()->getStyle('I'. $count)->getFont()->setBold(true);
                    $event->sheet->setCellValue('I'. $count, $this->moduleUtil->num_f($payrollDetail->total_income, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('J'. $count, $this->moduleUtil->num_f($payrollDetail->isss, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('K'. $count, $this->moduleUtil->num_f($payrollDetail->afp, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('L'. $count, $this->moduleUtil->num_f($payrollDetail->rent, $add_symbol = true, $precision = 2));
                    $event->sheet->getDelegate()->getStyle('M'. $count)->getFont()->setBold(true);
                    $event->sheet->setCellValue('M'. $count, $this->moduleUtil->num_f($payrollDetail->other_deductions, $add_symbol = true, $precision = 2));
                    $event->sheet->getDelegate()->getStyle('N'. $count)->getFont()->setBold(true);
                    $event->sheet->setCellValue('N'. $count, $this->moduleUtil->num_f($payrollDetail->total_deductions, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('O'. $count, $this->moduleUtil->num_f($payrollDetail->total_to_pay, $add_symbol = true, $precision = 2));

                    $regular_salary += $payrollDetail->regular_salary;
                    $daytime_overtime += $payrollDetail->daytime_overtime;
                    $night_overtime_hours += $payrollDetail->night_overtime_hours;
                    $other_income += $payrollDetail->other_income;
                    $total_income += $payrollDetail->total_income;
                    $isss += $payrollDetail->isss;
                    $afp += $payrollDetail->afp;
                    $rent += $payrollDetail->rent;
                    $other_deductions += $payrollDetail->other_deductions;
                    $total_deductions += $payrollDetail->total_deductions;
                    $total_to_pay += $payrollDetail->total_to_pay;

                    $count++;
                }

                /** table footer */
                $event->sheet->horizontalAlign('A' . $count . ':O' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':O' . $count)->getFont()->setBold(true);
                $event->sheet->mergeCells('A'. $count.':D'. $count);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('payroll.totals')));
                $event->sheet->setCellValue('E'.$count, $this->moduleUtil->num_f($regular_salary, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('F'.$count, $this->moduleUtil->num_f($daytime_overtime, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('G'.$count, $this->moduleUtil->num_f($night_overtime_hours, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('H'.$count, $this->moduleUtil->num_f($other_income, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('I'.$count, $this->moduleUtil->num_f($total_income, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('J'.$count, $this->moduleUtil->num_f($isss, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('K'.$count, $this->moduleUtil->num_f($afp, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('L'.$count, $this->moduleUtil->num_f($rent, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('M'.$count, $this->moduleUtil->num_f($other_deductions, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('N'.$count, $this->moduleUtil->num_f($total_deductions, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('O'.$count, $this->moduleUtil->num_f($total_to_pay, $add_symbol = true, $precision = 2));
            },
        ];
    }
}
