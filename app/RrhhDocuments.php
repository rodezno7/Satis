<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhDocuments extends Model {

    protected $fillable = [
        'document_type_id', 
        'number', 'file', 
        'employee_id', 
        'date_expedition', 
        'date_expiration', 
        'state_id', 
        'city_id'
    ];

    public function state() {
        return $this->belongsTo('App\State');
    }

    public function city() {
        return $this->belongsTo('App\City');
    }
}
