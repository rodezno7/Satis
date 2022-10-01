<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentCorrelative extends Model
{
    protected $guarded = ['id'];
    protected $table = "document_correlatives";
    protected $fillable = ['document_type_id', 'serie', 'resolution', 'initial', 'actual', 'final', 'location_id', 'business_id', 'status'];
}
