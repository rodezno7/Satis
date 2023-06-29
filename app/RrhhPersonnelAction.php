<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhPersonnelAction extends Model
{
    protected $fillable = ['description', 'start_date', 'end_date', 'effective_date', 'status', 'employee_id', 'rrhh_type_personnel_action_id'];

}
