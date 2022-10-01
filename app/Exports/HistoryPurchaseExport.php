<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class HistoryPurchaseExport implements FromView, WithTitle, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $lines;
    private $business;
    private $initial_date;
    private $final_date;

    public function __construct($lines, $business, $initial_date, $final_date)
    {
        $this->lines = $lines;
        $this->business = $business;
        $this->initial_date = $initial_date;
        $this->final_date = $final_date;
    }
    public function title(): string
    {
        return __('Historial de compras');
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
                $event->sheet->setFontFamily('A1:H1', 'Calibri');
                $event->sheet->horizontalAlign('A1:H6', \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->setBold('A1:J3');
                $event->sheet->columnWidth('A', 15.50);
                $event->sheet->columnWidth('B', 35.00);
                $event->sheet->columnWidth('C', 15.50);
                $event->sheet->columnWidth('D', 50.00);
                $event->sheet->columnWidth('E', 15.50);
                $event->sheet->columnWidth('F', 15.50);
                $event->sheet->columnWidth('G', 15.50);
                $event->sheet->columnWidth('H', 16.50);
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

        return view('reports.history_purchases_excel', [
            'lines' => $this->lines,
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
