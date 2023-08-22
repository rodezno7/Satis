<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planilla extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'year', 
        'month', 
        'start_date', 
        'end_date', 
        'payment_period_id', 
        'calculation_type_id', 
        'business_id', 
        'deleted_at'
    ];
    
    public function paymentPeriod(){
        return $this->belongsTo('App\PaymentPeriod');
    }

    public function calculationType(){
        return $this->belongsTo('App\Calculation');
    }

    public function business(){
        return $this->belongsTo('App\Business');
    }
}
