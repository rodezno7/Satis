<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CatalogueImport implements ToCollection
{
    /**
    * @param Collection $collection
    */

    public $data;

    public function collection(Collection $rows)
    {
        $this->data = $rows;
    }
}
