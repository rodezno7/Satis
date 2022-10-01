<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class QuoteExport implements FromView, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $quote;
    private $lines;
    private $value_letters;
    private $legend;

    public function __construct($quote, $lines, $value_letters, $legend)
    {
    	$this->quote = $quote;
    	$this->lines = $lines;
    	$this->value_letters = $value_letters;
    	$this->legend = $legend;
    }

    public function title(): string
    {
    	return __('quote.quote');
    }

    public function registerEvents(): array
    {
    	return [            
    		AfterSheet::class    => function(AfterSheet $event) {
    			
    			$event->sheet->setFontFamily('A1:F1500', 'Calibri');
    			$event->sheet->setFontSize('A1:F1500' , 10);
    			$event->sheet->columnWidth('A', 14.43);
    			$event->sheet->columnWidth('B', 53.71);
    			$event->sheet->columnWidth('C', 14.43);
    			$event->sheet->columnWidth('D', 14.43);
    			
    		},
    	];
    }

    public function view(): View
    {
    	return view('quote.excel', [
    		'quote' => $this->quote,
    		'lines' => $this->lines,
    		'value_letters' => $this->value_letters,
    		'legend' => $this->legend,
    	]);
    }
}
