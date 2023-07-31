<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhDocumentFile extends Model
{
    protected $fillable = [
        'rrhh_document_id',
        'file'
    ];

    public function rrhhDocument() {
        return $this->belongsTo('App\RrhhDocuments');
    }
}
