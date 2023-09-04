<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PayrollDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'days', 
        'hours',
        'salary', 
        'commissions', 
        'number_daytime_overtime', 
        'daytime_overtime',
        'number_night_overtime_hours', 
        'night_overtime_hours', 
        'total_hours',
        'subtotal',
        'isss', 
        'afp',
        'rent', 
        'other_deductions',
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
