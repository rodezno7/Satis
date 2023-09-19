<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PayrollDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'proportional',
        'start_date',
        'end_date',
        'days', 
        'hours',
        'montly_salary', 
        'regular_salary', 
        'commissions', 
        'extra_hours',
        'vacation',
        'bonus',
        'other_income',
        'total_income', 
        'isss', 
        'afp',
        'rent', 
        'other_deductions', 
        'total_deductions',
        'total_to_pay', 
        'employee_id', 
        'payroll_id'
    ];
    
    public function payroll(){
        return $this->belongsTo('App\Payroll');
    }

    public function employee(){
        return $this->belongsTo('App\Employees');
    }            
}
