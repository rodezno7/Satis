<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class ComprobationBalanceExport implements FromView, WithEvents, WithTitle
{
    private $report_name;
    private $date_range;
    private $accounts_debit;
    private $accounts_credit;
    private $account_from;
    private $account_to;
    private $business_name;
    private $enable_empty_values;

    public function __construct($report_name, $date_range, $accounts_debit, $accounts_credit, $account_from, $account_to, $business_name, $enable_empty_values)
    {
        $this->report_name = $report_name;
        $this->date_range = $date_range;
        $this->accounts_debit = $accounts_debit;
        $this->accounts_credit = $accounts_credit;
        $this->account_from = $account_from;
        $this->account_to = $account_to;
        $this->business_name = $business_name;
        $this->enable_empty_values = $enable_empty_values;
    }

    /**
    * @return \Illuminate\Support\Collection
    */

    public function title(): string
    {
        return __('accounting.comprobation_balance');
    }

    public function registerEvents(): array
    {
        return [            
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->setFontFamily('A1:F1500', 'Calibri');
                $event->sheet->columnWidth('A', 11);
                $event->sheet->columnWidth('B', 53);
                $event->sheet->columnWidth('C', 14);
                $event->sheet->columnWidth('D', 13);
                $event->sheet->columnWidth('E', 13);
                $event->sheet->columnWidth('F', 13);

                
                $event->sheet->setFormat('C6:F1500', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
                $event->sheet->setRowsToRepeatAtTopByStartAndEnd(1, 5);
                
            },
        ];
    }
    
    public function view(): View
    {
        return view('reports.balance_comprobation_excel', [
            'report_name' => $this->report_name,
            'date_range' => $this->date_range,
            'accounts_debit' => $this->accounts_debit,
            'accounts_credit' => $this->accounts_credit,
            'account_from' => $this->account_from,
            'account_to' => $this->account_to,
            'business_name' => $this->business_name,
            'enable_empty_values' => $this->enable_empty_values,
        ]);
    }
    
}
