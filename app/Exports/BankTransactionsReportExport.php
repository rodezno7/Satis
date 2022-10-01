<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class BankTransactionsReportExport implements FromView, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $header1;
    private $header2;
    private $transactions_debit;
    private $transactions_credit;
    private $business_name;
    private $type_transaction;

    public function __construct($header1, $header2, $transactions_debit, $transactions_credit, $business_name, $type_transaction)
    {
    	$this->header1 = $header1;
    	$this->header2 = $header2;
    	$this->transactions_debit = $transactions_debit;
    	$this->transactions_credit = $transactions_credit;
    	$this->business_name = $business_name;
    	$this->type_transaction = $type_transaction;
    }

    public function title(): string
    {
    	return __('accounting.bank_transactions_report');
    }

    public function registerEvents(): array
    {
    	return [
    		AfterSheet::class    => function(AfterSheet $event) {

    			$event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    			$event->sheet->horizontalAlign('A1:F4' , \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    			$event->sheet->setFontFamily('A1:F1500', 'Calibri');
    			$event->sheet->setFontSize('A1:F1500' , 10);

    			$event->sheet->setFormat('F1:F1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);

    			$event->sheet->columnWidth('A', 10.14);
    			$event->sheet->columnWidth('B', 18.43);
    			$event->sheet->columnWidth('C', 40);
    			$event->sheet->columnWidth('D', 14.29);
    			$event->sheet->columnWidth('E', 16.14);
    			$event->sheet->columnWidth('F', 14.57);
    		},
    	];
    }

    public function view(): View
    {
    	return view('reports.bank_transactions_excel', [
    		'header1' => $this->header1,
    		'header2' => $this->header2,
    		'transactions_debit' => $this->transactions_debit,
    		'transactions_credit' => $this->transactions_credit,
    		'business_name' => $this->business_name,
    		'type_transaction' => $this->type_transaction,
    	]);
    }
}
