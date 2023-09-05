<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payroll_type_id',
        'name',
        'year', 
        'month', 
        'start_date', 
        'end_date', 
        'payroll_status_id',
        'payment_period_id',  
        'business_id', 
        'deleted_at'
    ];
    
    public function payrollType(){
        return $this->belongsTo('App\PayrollType');
    }

    public function payrollStatus(){
        return $this->belongsTo('App\PayrollStatus');
    }

    public function paymentPeriod(){
        return $this->belongsTo('App\PaymentPeriod');
    }

    public function calculationType(){
        return $this->belongsTo('App\Calculation');
    }

    public function payrollDetails(){
        return $this->hasMany('App\PayrollDetail');
    }

    public function business(){
        return $this->belongsTo('App\Business');
    }
}
