<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AnnualPayrollSummaryExport implements WithEvents, WithTitle, ShouldAutoSize
{
    private $summaries;
    private $business;
    private $year;
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
    public function __construct($summaries, $business, $year, $moduleUtil)
    {
    	$this->summaries = $summaries;
        $this->business = $business;
        $this->year = $year;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return 'Resumen anual';
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
                $summaries = $this->summaries;
                $items = count($summaries) + 3;

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                /** Columns style */
                $event->sheet->columnWidth('A', 20); // code
                $event->sheet->columnWidth('B', 42); // employee
                $event->sheet->columnWidth('C', 15); // enero
                $event->sheet->columnWidth('D', 17); // febrero
                $event->sheet->columnWidth('E', 15); // marzo
                $event->sheet->columnWidth('F', 15); // abril
                $event->sheet->columnWidth('G', 15); // mayo
                $event->sheet->columnWidth('H', 15); // junio
                $event->sheet->columnWidth('I', 15); // julio
                $event->sheet->columnWidth('J', 16); // agosto
                $event->sheet->columnWidth('K', 18); // septiembre
                $event->sheet->columnWidth('L', 15); // octubre
                $event->sheet->columnWidth('M', 17); // noviembre
                $event->sheet->columnWidth('N', 17); // diciembre
                $event->sheet->columnWidth('O', 19); // total to pay
                $event->sheet->setFormat('A3:O' . $items, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

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
                $event->sheet->setCellValue('A2', mb_strtoupper(__('payroll.annual_summary').' - '.$this->year));


                /** table head */
                $count = 3;
                $event->sheet->horizontalAlign('A' . $count . ':O' . $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A' . $count . ':O' . $count)->getFont()->setBold(true);
                $event->sheet->getStyle('A'. $count.':O'. $count,  $event->sheet->getHighestRow())->getAlignment()->setWrapText(true);
                $event->sheet->setCellValue('A'.$count, mb_strtoupper(__('rrhh.code')));
                $event->sheet->setCellValue('B'.$count, mb_strtoupper(__('rrhh.employee')));
                $event->sheet->setCellValue('C'.$count, mb_strtoupper('Enero'));
                $event->sheet->setCellValue('D'.$count, mb_strtoupper('Febrero'));
                $event->sheet->setCellValue('E'.$count, mb_strtoupper('Marzo'));
                $event->sheet->setCellValue('F'.$count, mb_strtoupper('Abril'));
                $event->sheet->setCellValue('G'.$count, mb_strtoupper('Mayo'));
                $event->sheet->setCellValue('H'.$count, mb_strtoupper('Junio'));
                $event->sheet->setCellValue('I'.$count, mb_strtoupper('Julio'));
                $event->sheet->setCellValue('J'.$count, mb_strtoupper('Agosto'));
                $event->sheet->setCellValue('K'.$count, mb_strtoupper('Septiembre'));
                $event->sheet->setCellValue('L'.$count, mb_strtoupper('Octubre'));
                $event->sheet->setCellValue('M'.$count, mb_strtoupper('Noviembre'));
                $event->sheet->setCellValue('N'.$count, mb_strtoupper('Diciembre'));
                $event->sheet->setCellValue('O'.$count, mb_strtoupper('Total'));

                /** table body */
                $count = $count + 1;
                $total_to_pay = 0;
                foreach($summaries as $summary){
                    $event->sheet->horizontalAlign('A'. $count.':O'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->setCellValue('A'. $count, $summary->code);
                    $event->sheet->setCellValue('B'. $count, $summary->first_name.' '.$summary->last_name);
                    $event->sheet->setCellValue('C'. $count, $this->moduleUtil->num_f($summary->enero, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('D'. $count, $this->moduleUtil->num_f($summary->febrero, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('E'. $count, $this->moduleUtil->num_f($summary->marzo, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('F'. $count, $this->moduleUtil->num_f($summary->abril, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('G'. $count, $this->moduleUtil->num_f($summary->mayo, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('H'. $count, $this->moduleUtil->num_f($summary->junio, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('I'. $count, $this->moduleUtil->num_f($summary->julio, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('J'. $count, $this->moduleUtil->num_f($summary->agosto, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('K'. $count, $this->moduleUtil->num_f($summary->septiembre, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('L'. $count, $this->moduleUtil->num_f($summary->octubre, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('M'. $count, $this->moduleUtil->num_f($summary->noviembre, $add_symbol = true, $precision = 2));
                    $event->sheet->setCellValue('N'. $count, $this->moduleUtil->num_f($summary->diciembre, $add_symbol = true, $precision = 2));
                    $event->sheet->getDelegate()->getStyle('O' . $count)->getFont()->setBold(true);
                    $total_to_pay = $summary->enero + $summary->febrero + $summary->marzo + $summary->abril + $summary->mayo + $summary->junio + $summary->julio + $summary->agosto + $summary->septiembre + $summary->octubre + $summary->noviembre + $summary->diciembre;
                    $event->sheet->setCellValue('O'. $count, $this->moduleUtil->num_f($total_to_pay, $add_symbol = true, $precision = 2));

                    $count++;
                }
            },
        ];
    }
}
