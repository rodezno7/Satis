<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhPositionHistory extends Model
{
    protected $fillable = ['department_id', 'position1_id', 'employee_id', 'current', 'rrhh_personnel_action_id'];

    public function department() {
        return $this->belongsTo('App\RrhhData');
    }

    public function position1() {
        return $this->belongsTo('App\RrhhData');
    }

    public function employee() {
        return $this->belongsTo('App\Employees');
    }

    public function rrhhPersonnelAction() {
        return $this->belongsTo('App\RrhhPersonnelAction');
    }
}
