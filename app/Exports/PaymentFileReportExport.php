<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaymentFileReportExport implements WithEvents, WithTitle, ShouldAutoSize
{
    private $payroll;
    private $payrollDetails;
    private $bank;

    /**
     * Constructor.
     * 
     * @param  array  $payroll
     * @param  array  $payrollDetails
     * @param  \App\Business  $business
     * @param  $moduleUtil
     * @return void
     */
    public function __construct($payroll, $payrollDetails, $bank)
    {
    	$this->payroll = $payroll;
        $this->bank = $bank;
        $this->payrollDetails = $payrollDetails;
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return 'Archivo de pago';
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
                $payroll = $this->payroll;
                $payrollDetails = $this->payrollDetails;
                $bank = $this->bank;

                /** General setup */
    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                /** Columns style */
                $event->sheet->columnWidth('A', 20); // count
                $event->sheet->columnWidth('B', 45); // employee
                $event->sheet->columnWidth('C', 25);
                $event->sheet->columnWidth('D', 25); // total_to_pay
                $event->sheet->columnWidth('E', 15); // code
                $event->sheet->columnWidth('F', 20); // concept

                /** table body */
                $count = 1;
                foreach($payrollDetails as $payrollDetail){
                    if($payrollDetail->employee->payment_id != null){
                        if($payrollDetail->employee->payment->value == 'Transferencia bancaria'){
                            if($payrollDetail->employee->bank_id == $bank->id){
                                $event->sheet->horizontalAlign('A'. $count.':F'. $count, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                                $event->sheet->setCellValue('A'. $count, $payrollDetail->employee->bank_account);
                                $event->sheet->setCellValue('B'. $count, $payrollDetail->employee->first_name.' '.$payrollDetail->employee->last_name);
                                $event->sheet->setCellValue('C'. $count, '');
                                $event->sheet->setCellValue('D'. $count, $payrollDetail->total_to_pay);
                                $event->sheet->setCellValue('E'. $count, $payrollDetail->employee->agent_code);
                                $event->sheet->setCellValue('F'. $count, 'Pago de '.mb_strtolower($payroll->name));
                                $count++;
                            }
                        }
                    }
                }
            },
        ];
    }
}
