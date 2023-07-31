<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrhhData extends Model {
    use SoftDeletes;

    protected $fillable = [
        'code', 
        'short_name', 
        'value', 
        'date_required', 
        'rrhh_header_id', 
        'bussines_id', 
        'status', 
        'deleted_at'
    ];
    
    public function header() {

        return $this->belongsTo('App\RrhhHeader');
    }
    
}
