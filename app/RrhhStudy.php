<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhStudy extends Model
{
    protected $fillable = [
        'title',
        'institution',
        'year_graduation',
        'study_status',
        'status',
        'type_study_id', 
        'employee_id', 
    ];
}
