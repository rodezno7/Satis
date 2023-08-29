<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhSalaryHistory extends Model
{
    protected $fillable = ['employee_id', 'previous_salary', 'new_salary', 'current', 'rrhh_personnel_action_id'];

    public function employee() {
        return $this->belongsTo('App\Employees');
    }

    public function rrhhPersonnelAction() {
        return $this->belongsTo('App\RrhhPersonnelAction');
    }
}
