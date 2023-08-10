<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhAbsenceInability extends Model
{
    protected $fillable = [
        'type', 
        'description', 
        'start_date', 
        'end_date', 
        'amount', 
        'type_inability_id', 
        'type_absence_id', 
        'employee_id'
    ];
    
    public function typeInability() {
        return $this->belongsTo('App\RrhhData');
    }

    public function typeAbsence() {
        return $this->belongsTo('App\RrhhData');
    }
}
