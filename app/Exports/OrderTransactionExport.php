<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderTransactionExport implements FromView, WithTitle, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $lines;
    private $business;
    private $initial_date;
    private $final_date;

    public function __construct($quote_trans, $business, $initial_date, $final_date)
    {
        $this->quote_trans = $quote_trans;
        $this->business = $business;
        $this->initial_date = $initial_date;
        $this->final_date = $final_date;
    }
    public function title(): string
    {
        return __('report.dispatch_report');
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->columnWidth('A', 3);
                $event->sheet->columnWidth('B', 30);
                $event->sheet->columnWidth('C', 30);
                $event->sheet->columnWidth('D', 15);
                $event->sheet->columnWidth('E', 15);
                $event->sheet->columnWidth('F', 40);
                $event->sheet->columnWidth('G', 30);
                $event->sheet->columnWidth('H', 30);
                $event->sheet->columnWidth('I', 15);
                $event->sheet->columnWidth('J', 15);
                $event->sheet->columnWidth('K', 10);
                $event->sheet->columnWidth('L', 25);
                $event->sheet->columnWidth('M', 10);
                $event->sheet->columnWidth('N', 12);
                $event->sheet->columnWidth('O', 15);
                $event->sheet->columnWidth('P', 25);
                $event->sheet->columnWidth('Q', 25);
                $event->sheet->columnWidth('R', 25);
            },
        ];
    }
    public function view(): View
    {
        $months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));
        $initial_month = $months[($this->initial_date->format('n')) - 1];
        $final_month = $months[($this->final_date->format('n')) - 1];
        $initial_year = $this->initial_date->format('Y');
        $final_year = $this->final_date->format('Y');

        return view('reports.orders_dispatch_report_excel', [
            'quote_trans' => $this->quote_trans,
            'business' => $this->business,
            'initial_month' => $initial_month,
            'final_month' => $final_month,
            'initial_year' => $initial_year,
            'final_year' => $final_year,
            'initial_date' => $this->initial_date,
            'final_date' => $this->final_date
        ]);
    }
}
