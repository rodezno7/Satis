<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class ResultStatusExport implements WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    private $header;
    private $business;
    private $ordinary_income;
    private $return_sells_q;
    private $ordinary_income_accounts;
    private $sell_cost_q;
    private $ordinary_expense;
    private $ordinary_expense_accounts;
    private $extra_income;
    private $extra_income_accounts;
    private $extra_expense;
    private $extra_expense_accounts;

    public function __construct($header, $business, $ordinary_income, $return_sells_q, $ordinary_income_accounts, $sell_cost_q, $ordinary_expense, $ordinary_expense_accounts, $extra_income, $extra_income_accounts, $extra_expense, $extra_expense_accounts)
    {
    	$this->header = $header;
    	$this->business = $business;
    	$this->ordinary_income = $ordinary_income;
    	$this->return_sells_q = $return_sells_q;
    	$this->ordinary_income_accounts = $ordinary_income_accounts;
    	$this->sell_cost_q = $sell_cost_q;
    	$this->ordinary_expense = $ordinary_expense;
    	$this->ordinary_expense_accounts = $ordinary_expense_accounts;
    	$this->extra_income = $extra_income;
    	$this->extra_income_accounts = $extra_income_accounts;
    	$this->extra_expense = $extra_expense;
    	$this->extra_expense_accounts = $extra_expense_accounts;
    }

    /**
    * @return \Illuminate\Support\Collection
    */

    public function title(): string {
        
    	return __('accounting.result_status');
    }

    public function registerEvents(): array {

    	return [            
    		AfterSheet::class    => function(AfterSheet $event) {

    			$header = $this->header;
    			$business = $this->business;
    			$ordinary_income = $this->ordinary_income;
    			$return_sells_q = $this->return_sells_q;
    			$ordinary_income_accounts = $this->ordinary_income_accounts;
    			$sell_cost_q = $this->sell_cost_q;
    			$ordinary_expense = $this->ordinary_expense;
    			$ordinary_expense_accounts = $this->ordinary_expense_accounts;
    			$extra_income = $this->extra_income;
    			$extra_income_accounts = $this->extra_income_accounts;
    			$extra_expense = $this->extra_expense;
    			$extra_expense_accounts = $this->extra_expense_accounts;

    			$event->sheet->setFontSize('A1:H1500' , 9);
                $event->sheet->setFormat('F6:F1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setFormat('H6:H1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
    			$event->sheet->columnWidth('A', 8.43);
                $event->sheet->columnWidth('B', 8.43);
                $event->sheet->columnWidth('C', 8.43);
                $event->sheet->columnWidth('D', 8.43);
                $event->sheet->columnWidth('E', 8.43);
    			$event->sheet->columnWidth('F', 14);
    			$event->sheet->columnWidth('G', 2);
    			$event->sheet->columnWidth('H', 14);

    			$event->sheet->mergeCells('A1:H1');
    			$event->sheet->mergeCells('A2:H2');
    			$event->sheet->mergeCells('A3:H3');
    			$event->sheet->mergeCells('A4:H4');
                $event->sheet->mergeCells('A5:H5');

    			$event->sheet->setBold('A1:H4');
    			$event->sheet->horizontalAlign('A1:H4' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    			$event->sheet->setCellValue('A1', mb_strtoupper($this->business->name));
    			$event->sheet->setCellValue('A2', mb_strtoupper(__('accounting.result_title')));
    			$event->sheet->setCellValue('A3', mb_strtoupper($this->header));
    			$event->sheet->setCellValue('A4', mb_strtoupper(__('accounting.accountant_report_values')));
    			
    			$cont = 5;

    			$a_position = "A".$cont."";
                $a_position = "B".$cont."";
                $a_position = "C".$cont."";
                $a_position = "D".$cont."";
                $a_position = "E".$cont."";
    			$b_position = "F".$cont."";
                $a_position = "G".$cont."";
    			$c_position = "H".$cont."";

    			if(number_format($ordinary_income, 2) != 0.00) {

    				$cont = $cont + 1;
                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    				$event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_income_ordinary')));
                    $event->sheet->setBold("A".$cont."");
    				$event->sheet->setCellValue("H".$cont."", $ordinary_income);

    				$sum_ordinary_income = 0.00;

    				foreach($ordinary_income_accounts as $item) {

    					if(number_format($item->balance, 2) != 0.00) {

    						$sum_ordinary_income = $sum_ordinary_income + $item->balance;
    						$cont = $cont + 1;
                            $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    						$event->sheet->setCellValue("A".$cont."", $item->name);
    						$event->sheet->setCellValue("F".$cont."", $item->balance);

    						if(number_format($sum_ordinary_income, 2) == number_format($ordinary_income, 2)) {

    							$event->sheet->setBorderBottom("F".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    						}
    					}
    				}
    				
    				if(number_format($return_sells_q->balance, 2) != 0.00) {
    					$cont = $cont + 1;
                        $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    					$event->sheet->setCellValue("A".$cont."", $return_sells_q->name);
    					$event->sheet->setFormat("F".$cont."", \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
    					$event->sheet->setCellValue("F".$cont."", $return_sells_q->balance);
    					$event->sheet->setBorderBottom("F".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    				}
    				$cont = $cont + 1;
    			}



    			if(number_format($sell_cost_q->balance, 2) != 0.00) {

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');    				
    				$event->sheet->setCellValue("A".$cont."", __('accounting.result_less'));
                    $event->sheet->setBold("A".$cont."");
    				$cont = $cont + 2;

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    				$event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_cost')));
                    $event->sheet->setBold("A".$cont."");
    				$event->sheet->setCellValue("H".$cont."", $sell_cost_q->balance);
    				
    				$cont = $cont + 1;
    			}

    			if(number_format(($ordinary_income - $sell_cost_q->balance), 2) != 0.00) {

    				$cont = $cont + 1;
    				$utility_gross = $ordinary_income - $sell_cost_q->balance;
                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    				$event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_utility_gross')));
                    $event->sheet->setBold("A".$cont."");
    				$event->sheet->setCellValue("H".$cont."", $utility_gross);
    				$cont = $cont + 2;
    			}
    			

    			if(number_format($ordinary_expense, 2) != 0.00) {

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    				$event->sheet->setCellValue("A".$cont."", __('accounting.result_less'));
                    $event->sheet->setBold("A".$cont."");
    				$cont = $cont + 2;
                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    				$event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_expenses_ordinary')));
                    $event->sheet->setBold("A".$cont."");
    				$event->sheet->setCellValue("H".$cont."", $ordinary_expense);

    				$sum_ordinary_expense = 0.00;

    				foreach ($ordinary_expense_accounts as $item) {

    					if(number_format($item->balance, 2) != 0.00) {

    						$cont = $cont + 1;
    						$sum_ordinary_expense = $sum_ordinary_expense + $item->balance;
                            $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    						$event->sheet->setCellValue("A".$cont."", $item->name);
    						$event->sheet->setCellValue("F".$cont."", $item->balance);

    						if(number_format($sum_ordinary_expense, 2) == number_format($ordinary_expense, 2)) {

    							$event->sheet->setBorderBottom("F".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    							$event->sheet->setBorderBottom("H".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    						}
    					}
    				}
    			}

    			if(number_format(($ordinary_income - ($sell_cost_q->balance + $ordinary_expense)), 2) != 0.00) {

    				$cont = $cont + 1;
    				$utility_operation = ($ordinary_income - ($sell_cost_q->balance + $ordinary_expense));
                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    				$event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_utility_operation')));
                    $event->sheet->setBold("A".$cont."");
    				$event->sheet->setCellValue("H".$cont."", $utility_operation);
    				$cont = $cont + 1;
    			}



    			if(number_format($extra_income, 2) != 0.00) {

    				$cont = $cont + 1;

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    				$event->sheet->setCellValue("A".$cont."", __('accounting.result_more'));
                    $event->sheet->setBold("A".$cont."");

    				$cont = $cont + 1;

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    				$event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_income_no_ordinary')));
                    $event->sheet->setBold("A".$cont."");
    				$event->sheet->setCellValue("H".$cont."", $extra_income);

    				$sum_extra_income = 0.00;

    				foreach($extra_income_accounts as $item) {

    					if(number_format($item->balance, 2) != 0.00) {

    						$sum_extra_income = $sum_extra_income + $item->balance;
    						$cont = $cont + 1;
                            $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
    						$event->sheet->setCellValue("A".$cont."", $item->name);
    						$event->sheet->setCellValue("F".$cont."", $item->balance);

    						if(number_format($sum_extra_income, 2) == number_format($extra_income, 2)) {

    							$event->sheet->setBorderBottom("F".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    						}
    					}
    				}

    				$cont = $cont + 1;
    			}

                if(number_format($extra_expense, 2) != 0.00) {

                    $cont = $cont + 1;

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
                    $event->sheet->setCellValue("A".$cont."", __('accounting.result_less'));
                    $event->sheet->setBold("A".$cont."");

                    $cont = $cont + 1;

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
                    $event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_expenses_no_ordinary')));
                    $event->sheet->setBold("A".$cont."");
                    $event->sheet->setCellValue("H".$cont."", $extra_expense);

                    $sum_extra_expense = 0.00;

                    foreach($extra_expense_accounts as $item) {

                        if(number_format($item->balance, 2) != 0.00) {

                            $sum_extra_expense = $sum_extra_expense + $item->balance;
                            $cont = $cont + 1;
                            $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
                            $event->sheet->setCellValue("A".$cont."", $item->name);
                            $event->sheet->setCellValue("F".$cont."", $item->balance);

                            if(number_format($sum_extra_expense, 2) == number_format($extra_expense, 2)) {

                                $event->sheet->setBorderBottom("F".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                            }
                        }
                    }
                    
                    $cont = $cont + 1;
                }



                if(number_format(($ordinary_income + $extra_income - ($sell_cost_q->balance + $ordinary_expense + $extra_expense)), 2) != 0.00) {

                    $utility_before = ($ordinary_income + $extra_income - ($sell_cost_q->balance + $ordinary_expense + $extra_expense));

                    $cont = $cont + 1;
                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
                    $event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_utility_exercise')));
                    $event->sheet->setBold("A".$cont."");
                    $event->sheet->setCellValue("H".$cont."", $utility_before);
                }


                /*

                if(number_format((($ordinary_income + $extra_income - ($sell_cost_q->balance + $ordinary_expense + $extra_expense)) *(0.07)), 2) != 0.00) {
                    $legal_reserve = (($ordinary_income + $extra_income - ($sell_cost_q->balance + $ordinary_expense + $extra_expense)) *(0.07));
                    $cont = $cont + 1;

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
                    $event->sheet->setCellValue("A".$cont."", __('accounting.result_less'));
                    $event->sheet->setBold("A".$cont."");

                    $cont = $cont + 1;

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
                    $event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_legal_reserve')));
                    $event->sheet->setBold("A".$cont."");
                    $event->sheet->setCellValue("H".$cont."", $legal_reserve);

                }

                $cont = $cont + 1;

                $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
                $event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_income_tax')));
                $event->sheet->setBold("A".$cont."");
                $cont = $cont + 1;


                $utility_exercise = ($ordinary_income + $extra_income - ($sell_cost_q->balance + $ordinary_expense + $extra_expense)) - (($ordinary_income + $extra_income - ($sell_cost_q->balance + $ordinary_expense + $extra_expense)) *(0.07));
                if(number_format($utility_exercise, 2) != 0.00) {
                    $cont = $cont + 1;

                    $event->sheet->mergeCells('A'.$cont.':E'.$cont.'');
                    $event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.result_utility_exercise')));
                    $event->sheet->setBold("A".$cont."");
                    $event->sheet->setCellValue("H".$cont."", $utility_exercise);
                    $event->sheet->setBorderTop("H".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);
                    $event->sheet->setBorderBottom("H".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }

                */

                $cont = $cont + 2;
                $event->sheet->setBorderBottom("A".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $event->sheet->setBorderBottom("B".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $event->sheet->setBorderBottom("C".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $event->sheet->setBorderBottom("F".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $event->sheet->setBorderBottom("G".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $event->sheet->setBorderBottom("H".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $cont = $cont + 1;
                $event->sheet->setCellValue("A".$cont."", mb_strtoupper($this->business->legal_representative));
                $event->sheet->setCellValue("F".$cont."", mb_strtoupper($this->business->accountant));
                $cont = $cont + 1;
                $event->sheet->setCellValue("A".$cont."", mb_strtoupper(__('accounting.owner')));
                $event->sheet->setCellValue("F".$cont."", mb_strtoupper(__('accounting.accountant')));

                $cont = $cont + 2;
                $event->sheet->setBorderBottom("D".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $event->sheet->setBorderBottom("E".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $event->sheet->setBorderBottom("F".$cont."", \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $cont = $cont + 1;
                $event->sheet->setCellValue("D".$cont."", mb_strtoupper($this->business->auditor));
                $cont = $cont + 1;
                $event->sheet->setCellValue("D".$cont."", mb_strtoupper(__('accounting.auditor')));
                $cont = $cont + 1;
                $event->sheet->setCellValue("D".$cont."", "".mb_strtoupper(__('accounting.inscription_number')).": ".mb_strtoupper($this->business->inscription_number_auditor)."" );


            },
        ];
    }
}
