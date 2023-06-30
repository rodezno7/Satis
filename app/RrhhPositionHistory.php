<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhPositionHistory extends Model
{
    protected $fillable = ['department_id', 'position1_id', 'employee_id', 'current'];

    public function department() {
        return $this->belongsTo('App\RrhhData');
    }

    public function position1() {
        return $this->belongsTo('App\RrhhData');
    }

    public function employee() {
        return $this->belongsTo('App\Employees');
    }
}
