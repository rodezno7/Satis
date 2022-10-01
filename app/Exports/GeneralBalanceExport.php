<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class GeneralBalanceExport implements WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $header;
    private $accounts_debit;
    private $accounts_credit;
    private $owner;
    private $accountant;
    private $auditor;
    private $business_name;
    private $enable_foot_page;
    private $business;

    public function __construct($header, $accounts_debit, $accounts_credit, $owner, $accountant, $auditor, $business_name, $enable_foot_page, $business)
    {
    	$this->header = $header;
    	$this->accounts_debit = $accounts_debit;
    	$this->accounts_credit = $accounts_credit;
    	$this->owner = $owner;
    	$this->accountant = $accountant;
    	$this->auditor = $auditor;
    	$this->business_name = $business_name;
    	$this->enable_foot_page = $enable_foot_page;
        $this->business = $business;
    }

    /**
    * @return \Illuminate\Support\Collection
    */

    public function title(): string
    {
    	return __('accounting.general_balance');
    }

    public function registerEvents(): array
    {
    	return [            
    		AfterSheet::class    => function(AfterSheet $event) {

    			$accounts_debit = $this->accounts_debit;
    			$accounts_credit = $this->accounts_credit;
    			$owner = $this->owner;
    			$accountant = $this->accountant;
    			$auditor = $this->auditor;
    			$enable_foot_page = $this->enable_foot_page;
                $business = $this->business;

    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    			$event->sheet->setFontSize('A1:I1500' , 9);

    			$width_number = 11;
    			$width_text = 27.75;

    			$event->sheet->columnWidth('A', $width_text);
    			$event->sheet->columnWidth('B', $width_number);
    			$event->sheet->columnWidth('C', $width_number);
    			$event->sheet->columnWidth('D', $width_number);
    			$event->sheet->columnWidth('E', 0.75);
    			$event->sheet->columnWidth('F', $width_text);
    			$event->sheet->columnWidth('G', $width_number);
    			$event->sheet->columnWidth('H', $width_number);
    			$event->sheet->columnWidth('I', $width_number);

    			$event->sheet->mergeCells('A1:I1');
    			$event->sheet->mergeCells('A2:I2');
    			$event->sheet->mergeCells('A3:I3');
    			$event->sheet->mergeCells('A4:D4');
    			$event->sheet->mergeCells('F4:I4');

    			$event->sheet->horizontalAlign('A1:I4' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	

    			$event->sheet->setCellValue('A1', mb_strtoupper($this->business_name));
    			$event->sheet->setCellValue('A2', mb_strtoupper($this->header));
    			$event->sheet->setCellValue('A3', mb_strtoupper(__('accounting.accountant_report_values')));
    			$event->sheet->setCellValue('A4', mb_strtoupper(__('accounting.active_report')));
    			$event->sheet->setCellValue('F4', mb_strtoupper(__('accounting.pasive_report')));

    			$event->sheet->setBold('A1:F4');


    			$sum_level1 = 0.00;
    			$sum_level2 = 0.00;
    			$sum_debit = 0.00;
    			$cont = 5;
                $level_debit = $business->balance_debit_levels_number + 1;
    			foreach ($accounts_debit as $item) {
    				
    				$a_position = "A".$cont."";
    				$b_position = "B".$cont."";
    				$c_position = "C".$cont."";
    				$d_position = "D".$cont."";                    

    				if((number_format($item->balance, 2) != 0.00) && ($item->level <= $level_debit) && ($item->level >= 2)) {
    					$cont = $cont + 1;

    					if($item->level == 2) {
    						$account_level1 = $item->balance;
    						$sum_debit = $sum_debit + $item->balance;
    						$event->sheet->setCellValue($a_position, $item->name);
    						$event->sheet->setCellValue($d_position, $item->balance);
    						$event->sheet->setFormat($d_position, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
    						$event->sheet->setBold($a_position);
    						
    					}

                        if($business->balance_debit_levels_number > 1) {
                            if($item->level == 3) {
                                $account_level2 = $item->balance;
                                $sum_level1 = $sum_level1 + $item->balance;
                                $event->sheet->setCellValue($a_position, $item->name);
                                $event->sheet->setFormat($c_position, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                                if(number_format($account_level1, 2)  ==  number_format($sum_level1, 2)) {
                                    $account_level1 = 0.00;
                                    $sum_level1 = 0.00;
                                    $event->sheet->setCellValue($c_position, $item->balance);
                                    $event->sheet->setBorderBottom($c_position, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                                    $event->sheet->setBold($a_position);
                                }
                                else {
                                    $event->sheet->setCellValue($c_position, $item->balance);
                                    $event->sheet->setBold($a_position);
                                }
                            }
                        }




                        if($business->balance_debit_levels_number > 2) {
                            if($item->level == 4) {
                                $sum_level2 = $sum_level2 + $item->balance;
                                $event->sheet->setCellValue($a_position, $item->name);
                                $event->sheet->setFormat($b_position, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                                if(number_format($account_level2, 2) == number_format($sum_level2, 2)) {
                                    $account_level2 = 0.00;
                                    $sum_level2 = 0.00;
                                    $event->sheet->setCellValue($b_position, $item->balance);
                                    $event->sheet->setBorderBottom($b_position, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                                }
                                else {
                                    $event->sheet->setCellValue($b_position, $item->balance);
                                }
                            }
                        }
                    }
                }



                $sum_level1 = 0.00;
                $sum_level2 = 0.00;
                $sum_credit = 0.00;
                $cont2 = 5;
                $level_credit = $business->balance_credit_levels_number + 1;
                foreach ($accounts_credit as $item) {
                    $f_position = "F".$cont2."";
                    $g_position = "G".$cont2."";
                    $h_position = "H".$cont2."";
                    $i_position = "I".$cont2."";

                    if((number_format($item->balance, 2) != 0.00) && ($item->level <= $level_credit) && ($item->level >= 2)) {
                        $cont2 = $cont2 + 1;

                        if($item->level == 2) {
                            $account_level1 = $item->balance;
                            $sum_credit = $sum_credit + $item->balance;

                            $event->sheet->setCellValue($f_position, $item->name);
                            $event->sheet->setCellValue($i_position, $item->balance);
                            $event->sheet->setFormat($i_position, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                            $event->sheet->setBold($f_position);

                        }
                        


                        if($business->balance_credit_levels_number > 1) {

                            if($item->level == 3) {
                                $account_level2 = $item->balance;
                                $sum_level1 = $sum_level1 + $item->balance;
                                $event->sheet->setCellValue($f_position, $item->name);
                                $event->sheet->setFormat($h_position, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                                if(number_format($account_level1, 2)  ==  number_format($sum_level1, 2)) {
                                    $account_level1 = 0.00;
                                    $sum_level1 = 0.00;
                                    $event->sheet->setCellValue($h_position, $item->balance);
                                    $event->sheet->setBorderBottom($h_position, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                                    $event->sheet->setBold($f_position);
                                }
                                else {
                                    $event->sheet->setCellValue($h_position, $item->balance);
                                    $event->sheet->setBold($f_position);
                                }
                            }
                        }

                        




                        if($business->balance_credit_levels_number > 2) {
                            if($item->level == 4) {
                                $sum_level2 = $sum_level2 + $item->balance;
                                $event->sheet->setCellValue($f_position, $item->name);
                                $event->sheet->setFormat($g_position, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

                                if(number_format($account_level2, 2) == number_format($sum_level2, 2)) {
                                    $account_level2 = 0.00;
                                    $sum_level2 = 0.00;
                                    $event->sheet->setCellValue($g_position, $item->balance);
                                    $event->sheet->setBorderBottom($g_position, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                                }
                                else {
                                    $event->sheet->setCellValue($g_position, $item->balance);
                                }
                            }
                        }
                        
                    }
                }


                $max = max($cont, $cont2) + 1;

                $event->sheet->mergeCells('A'.$max.':C'.$max.'');
                $event->sheet->setCellValue('A'.$max.'', mb_strtoupper(__('accounting.total_active_report')));
                $event->sheet->horizontalAlign('A'.$max.'' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->setCellValue('D'.$max.'', $sum_debit);
                $event->sheet->setFormat('D'.$max.'', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setBorderTop('D'.$max.'', \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);


                $event->sheet->mergeCells('F'.$max.':H'.$max.'');
                $event->sheet->setCellValue('F'.$max.'', mb_strtoupper(__('accounting.total_pasive_report')));
                $event->sheet->horizontalAlign('F'.$max.'' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->setCellValue('I'.$max.'', $sum_credit);
                $event->sheet->setFormat('I'.$max.'', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setBorderTop('I'.$max.'', \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);

                $event->sheet->setBold('A'.$max.':I'.$max.'');


                if($enable_foot_page == 'active') {




                    $max2 = max($cont, $cont2) + 4;


                    $event->sheet->mergeCells('A'.$max2.':B'.$max2.'');
                    $event->sheet->mergeCells('C'.$max2.':F'.$max2.'');
                    $event->sheet->mergeCells('G'.$max2.':I'.$max2.'');
                    $event->sheet->horizontalAlign('A'.$max2.':I'.$max2.'' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
                    $event->sheet->setBold('A'.$max2.':I'.$max2.'');

                    $max4 = $max2 - 1;

                    $event->sheet->mergeCells('A'.$max4.':B'.$max4.'');
                    $event->sheet->mergeCells('C'.$max4.':F'.$max4.'');
                    $event->sheet->mergeCells('G'.$max4.':I'.$max4.'');

                    $event->sheet->horizontalAlign('A'.$max4.':I'.$max4.'' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
                    $event->sheet->setBold('A'.$max4.':I'.$max4.'');

                    $event->sheet->setCellValue('A'.$max4.'', '_____________________');
                    $event->sheet->setCellValue('C'.$max4.'', '_____________________');
                    $event->sheet->setCellValue('G'.$max4.'', '_____________________');

                    $event->sheet->setCellValue('A'.$max2.'', mb_strtoupper($owner));
                    $event->sheet->setCellValue('C'.$max2.'', mb_strtoupper($accountant));
                    $event->sheet->setCellValue('G'.$max2.'', mb_strtoupper($auditor));

                    $max3 = max($cont, $cont2) + 5;

                    $event->sheet->mergeCells('A'.$max3.':B'.$max3.'');
                    $event->sheet->mergeCells('C'.$max3.':F'.$max3.'');
                    $event->sheet->mergeCells('G'.$max3.':I'.$max3.'');
                    $event->sheet->horizontalAlign('A'.$max3.':I'.$max3.'' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
                    $event->sheet->setBold('A'.$max3.':I'.$max3.'');

                    $event->sheet->setCellValue('A'.$max3.'', mb_strtoupper(__('accounting.owner')));
                    $event->sheet->setCellValue('C'.$max3.'', mb_strtoupper(__('accounting.accountant')));
                    $event->sheet->setCellValue('G'.$max3.'', mb_strtoupper(__('accounting.auditor')));
                }
                else
                {
                    $max2 = max($cont, $cont2) + 4;


                    $event->sheet->mergeCells('A'.$max2.':D'.$max2.'');
                    $event->sheet->mergeCells('F'.$max2.':I'.$max2.'');
                    $event->sheet->horizontalAlign('A'.$max2.':I'.$max2.'' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 
                    $event->sheet->setBold('A'.$max2.':I'.$max2.'');

                    $max4 = $max2 - 1;

                    $event->sheet->mergeCells('A'.$max4.':D'.$max4.'');
                    $event->sheet->mergeCells('F'.$max4.':I'.$max4.'');

                    $event->sheet->horizontalAlign('A'.$max4.':I'.$max4.'' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 
                    $event->sheet->setBold('A'.$max4.':I'.$max4.'');

                    $event->sheet->setCellValue('A'.$max4.'', '_____________________');
                    $event->sheet->setCellValue('F'.$max4.'', '_____________________');


                    $event->sheet->setCellValue('A'.$max2.'', mb_strtoupper($owner));
                    $event->sheet->setCellValue('F'.$max2.'', mb_strtoupper($accountant));


                    $max3 = max($cont, $cont2) + 5;

                    $event->sheet->mergeCells('A'.$max3.':D'.$max3.'');
                    $event->sheet->mergeCells('F'.$max3.':I'.$max3.'');

                    $event->sheet->horizontalAlign('A'.$max3.':I'.$max3.'' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 
                    $event->sheet->setBold('A'.$max3.':I'.$max3.'');

                    $event->sheet->setCellValue('A'.$max3.'', mb_strtoupper(__('accounting.owner')));
                    $event->sheet->setCellValue('F'.$max3.'', mb_strtoupper(__('accounting.accountant')));

                }







            },
        ];
    }


}
