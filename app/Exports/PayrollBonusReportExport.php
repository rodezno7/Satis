<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PayrollBonusReportExport implements WithEvents, WithTitle, ShouldAutoSize
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
    	return 'Planilla de aguinaldos';
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
                $event->sheet->columnWidth('B', 38); // employee
                $event->sheet->columnWidth('C', 19); // date_admission
                $event->sheet->columnWidth('D', 16); // end_date
                $event->sheet->columnWidth('E', 15); // montly_salary
                $event->sheet->columnWidth('F', 18); // days
                $event->sheet->columnWidth('G', 16); // bonus
                $event->sheet->columnWidth('H', 13); // rent
                $event->sheet->columnWidth('I', 19); // total to pay
                $event->sheet->setFormat('A7:I' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                /** Business name */
                $event->sheet->horizontalAlign('A1:I1', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:I1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:I1')->getFont()->setSize(15);
    			$event->sheet->mergeCells('A1:I1');
                $event->sheet->setCellValue('A1', mb_strtoupper($this->business->name));

                /** Report name */
                $event->sheet->horizontalAlign('A2:F2', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:I2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2:I2')->getFont()->setSize(13);
                $event->sheet->mergeCells('A2:I2');
                $event->sheet->setCellValue('A2', mb_strtoupper($payroll->name));

                /** Type Payroll */
                $event->sheet->horizontalAlign('A3:I3', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A3:I3')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3:I3')->getFont()->setSize(13);
                $event->sheet->mergeCells('A3:I3');
                $event->sheet->setCellValue('A3', mb_strtoupper($payroll->payrollType->name));

                /** Period Payroll */
                $event->sheet->horizontalAlign('A4:I4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A4:I4')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A4:I4')->getFont()->setSize(13);
                $event->sheet->mergeCells('A4:I4');
                $event->sheet->setCellValue('A4', 'Fecha de ingreso - '. $this->moduleUtil->format_date($payroll->end_date));

                /** table head */
                $count = 5;
                $event->sheet->horizontalAlign('A' . $count . ':I' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':I' . $count)->getFont()->setBold(true);
                $event->sheet->getStyle('A'. $count.':I'. $count,  $event->sheet->getHighestRow())->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getRowDimension($count)->setRowHeight(25);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('rrhh.code')));
                $event->sheet->setCellValue('B'.$count, mb_strtoupper(__('rrhh.employee')));
                $event->sheet->setCellValue('C'.$count, mb_strtoupper(__('rrhh.date_admission')));
                $event->sheet->setCellValue('D'.$count, mb_strtoupper(__('payroll.end_date')));
                $event->sheet->setCellValue('E'.$count, mb_strtoupper(__('payroll.montly_salary')));
                $event->sheet->setCellValue('F'.$count, mb_strtoupper(__('payroll.days')));
                $event->sheet->setCellValue('G'.$count, mb_strtoupper(__('payroll.bonus')));
                $event->sheet->setCellValue('H'.$count, mb_strtoupper(__('payroll.rent')));
                $event->sheet->setCellValue('I'.$count, mb_strtoupper(__('payroll.total_to_pay')));

                /** table body */
                $count = $count+1;
                $payrollDetails = $this->payrollDetails;
                $montly_salary = 0;
                $bonus = 0;
                $rent = 0;
                $total_to_pay = 0;
                foreach($payrollDetails as $payrollDetail){
                    $event->sheet->horizontalAlign('A'. $count.':I'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->setCellValue('A'. $count, $payrollDetail->employee->agent_code);
                    $event->sheet->setCellValue('B'. $count, $payrollDetail->employee->first_name.' '.$payrollDetail->employee->last_name);
                    $event->sheet->setCellValue('C'. $count, $this->moduleUtil->format_date($payrollDetail->start_date));
                    $event->sheet->setCellValue('D'. $count, $this->moduleUtil->format_date($payrollDetail->end_date));
                    $event->sheet->setCellValue('E'. $count, $this->moduleUtil->num_f($payrollDetail->montly_salary, $add_symbol = true, $precision = 2));
                    if($payrollDetail->proportional == 1){
                        $event->sheet->setCellValue('F'. $count, $payrollDetail->days.' (Proporcional)');
                    }else{
                        $event->sheet->setCellValue('F'. $count, $payrollDetail->days);
                    }
                    $event->sheet->setCellValue('G'. $count, $this->moduleUtil->num_f($payrollDetail->bonus, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('H'. $count, $this->moduleUtil->num_f($payrollDetail->rent, $add_symbol = true, $precision = 2));
                    $event->sheet->getDelegate()->getStyle('I' . $count)->getFont()->setBold(true);
                    $event->sheet->setCellValue('I'. $count, $this->moduleUtil->num_f($payrollDetail->total_to_pay, $add_symbol = true, $precision = 2));

                    $montly_salary += $payrollDetail->montly_salary;
                    $bonus += $payrollDetail->bonus;
                    $rent += $payrollDetail->rent;
                    $total_to_pay += $payrollDetail->total_to_pay;

                    $count++;
                }

                $event->sheet->horizontalAlign('A' . $count . ':I' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':I' . $count)->getFont()->setBold(true);
                $event->sheet->mergeCells('A'. $count.':D'. $count);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('payroll.totals')));
                $event->sheet->setCellValue('E'.$count, $this->moduleUtil->num_f($montly_salary, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('G'.$count, $this->moduleUtil->num_f($bonus, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('H'.$count, $this->moduleUtil->num_f($rent, $add_symbol = true, $precision = 2));
                $event->sheet->setCellValue('I'.$count, $this->moduleUtil->num_f($total_to_pay, $add_symbol = true, $precision = 2));
            },
        ];
    }
}
