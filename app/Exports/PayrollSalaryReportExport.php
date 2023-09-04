<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PayrollSalaryReportExport implements WithEvents, WithTitle, ShouldAutoSize
{
    private $planilla;
    private $planillaDetails;
    private $business;
    private $moduleUtil;

    /**
     * Constructor.
     * 
     * @param  array  $planilla
     * @param  array  $planillaDetails
     * @param  \App\Business  $business
     * @param  $moduleUtil
     * @return void
     */
    public function __construct($planilla, $planillaDetails, $business, $moduleUtil)
    {
    	$this->planilla = $planilla;
        $this->planillaDetails = $planillaDetails;
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
                $items = count($this->planillaDetails) + 4;
                $planilla = $this->planilla;

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
                $event->sheet->setCellValue('A2', mb_strtoupper($planilla->name));


                /** Type Payroll */
                $event->sheet->horizontalAlign('A3:M3', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A3:M3')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3:M3')->getFont()->setSize(13);
                $event->sheet->mergeCells('A3:M3');
                $event->sheet->setCellValue('A3', mb_strtoupper($planilla->typePlanilla->name));

                /** Period Payroll */
                $event->sheet->horizontalAlign('A4:M4', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A4:M4')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A4:M4')->getFont()->setSize(13);
                $event->sheet->mergeCells('A4:M4');
                $event->sheet->setCellValue('A4', $this->moduleUtil->format_date($planilla->start_date). ' - '. $this->moduleUtil->format_date($planilla->end_date));


                /** table body */
                $count = 5;

                /** table head */
                $event->sheet->horizontalAlign('A' . $count . ':M' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':M' . $count)->getFont()->setBold(true);
                $event->sheet->getStyle('A'. $count.':M'. $count,  $event->sheet->getHighestRow())->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getRowDimension($count)->setRowHeight(25);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('rrhh.employee')));
                $event->sheet->setCellValue('B'.$count, mb_strtoupper(__('planilla.days')));
                $event->sheet->setCellValue('C'.$count, mb_strtoupper(__('planilla.hours')));
                $event->sheet->setCellValue('D'.$count, mb_strtoupper(__('rrhh.salary')));
                $event->sheet->setCellValue('E'.$count, mb_strtoupper(__('planilla.daytime_overtime')));
                $event->sheet->setCellValue('F'.$count, mb_strtoupper(__('planilla.night_overtime_hours')));
                $event->sheet->setCellValue('G'.$count, mb_strtoupper(__('planilla.total_hours')));
                $event->sheet->setCellValue('H'.$count, mb_strtoupper(__('planilla.subtotal')));
                $event->sheet->setCellValue('I'.$count, 'ISSS');
                $event->sheet->setCellValue('J'.$count, 'AFP');
                $event->sheet->setCellValue('K'.$count, mb_strtoupper(__('planilla.rent')));
                $event->sheet->setCellValue('L'.$count, mb_strtoupper(__('planilla.other_deductions')));
                $event->sheet->setCellValue('M'.$count, mb_strtoupper(__('planilla.total_to_pay')));

                /** table body */
                $count = $count+1;
                $planillaDetails = $this->planillaDetails;
                foreach($planillaDetails as $planillaDetail){
                    $event->sheet->horizontalAlign('A'. $count.':M'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->setCellValue('A'. $count, $planillaDetail->employee->first_name.' '.$planillaDetail->employee->last_name);
                    $event->sheet->setCellValue('B'. $count, $planillaDetail->days);
                    $event->sheet->setCellValue('C'. $count, $planillaDetail->hours);
                    $event->sheet->setCellValue('D'. $count, $this->moduleUtil->num_f($planillaDetail->salary, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('E'. $count, $this->moduleUtil->num_f($planillaDetail->daytime_overtime, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('F'. $count, $this->moduleUtil->num_f($planillaDetail->night_overtime_hours, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('G'. $count, $this->moduleUtil->num_f($planillaDetail->total_hours, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('H'. $count, $this->moduleUtil->num_f($planillaDetail->subtotal, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('I'. $count, $this->moduleUtil->num_f($planillaDetail->isss, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('J'. $count, $this->moduleUtil->num_f($planillaDetail->afp, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('K'. $count, $this->moduleUtil->num_f($planillaDetail->rent, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('L'. $count, $this->moduleUtil->num_f($planillaDetail->other_deductions, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('M'. $count, $this->moduleUtil->num_f($planillaDetail->total_to_pay, $add_symbol = true, $precision = 2));

                    $count++;
                }
            },
        ];
    }
}
