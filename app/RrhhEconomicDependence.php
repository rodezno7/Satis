<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrhhEconomicDependence extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'birthdate', 'phone', 'type_relationship_id', 'employee_id', 'status', 'deleted_at'];
    
}
