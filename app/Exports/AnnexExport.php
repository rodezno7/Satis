<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class AnnexExport extends DefaultValueBinder implements WithEvents, WithTitle, WithCustomValueBinder, WithCustomCsvSettings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $data;
    private $title;
    private $columns;

    /**
     * Constructor.
     *
     * @param  array  $data
     * @param  string  $title
     * @return void
     */
    public function __construct($data, $title)
    {
    	$this->data = $data;
        $this->title = $title;
        $this->columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    }

    /**
     * Returns document title.
     * 
     * @return string
     */
    public function title(): string
    {
    	return $this->title;
    }

    /**
     * Bind value to a cell.
     *
     * @param  Cell  $cell
     * @param  mixed  $value
     * @return bool
     */
    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), $this->columns)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // Else return default behavior
        return parent::bindValue($cell, $value);
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
                $row = 1;

                $columns = $this->columns;

                foreach ($this->data as $item) {
                    foreach ($columns as $column) {
                        if (isset($item[$column])) {
                            $event->sheet->setCellValue($column . $row, $item[$column]);

                        } else {
                            break;
                        }
                    }

                    $row++;
                }
            }
        ];
    }

    /**
     * @return array
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
}
