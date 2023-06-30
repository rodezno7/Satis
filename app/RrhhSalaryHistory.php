<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhSalaryHistory extends Model
{
    protected $fillable = ['employee_id', 'salary', 'current'];

    public function employee() {
        return $this->belongsTo('App\Employees');
    }
}
