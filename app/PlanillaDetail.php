<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PlanillaDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'days', 
        'hours', 
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
        'planilla_id'
    ];
    
    public function planilla(){
        return $this->belongsTo('App\Planilla');
    }

    public function employee(){
        return $this->belongsTo('App\Employees');
    }            
}
