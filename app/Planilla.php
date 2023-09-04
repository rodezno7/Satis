<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planilla extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type_planilla_id',
        'name',
        'year', 
        'month', 
        'start_date', 
        'end_date', 
        'planilla_status_id',
        'payment_period_id',  
        'business_id', 
        'deleted_at'
    ];
    
    public function typePlanilla(){
        return $this->belongsTo('App\TypePlanilla');
    }

    public function planillaStatus(){
        return $this->belongsTo('App\planillaStatus');
    }

    public function paymentPeriod(){
        return $this->belongsTo('App\PaymentPeriod');
    }

    public function calculationType(){
        return $this->belongsTo('App\Calculation');
    }

    public function planillaDetails(){
        return $this->hasMany('App\PlanillaDetail');
    }

    public function business(){
        return $this->belongsTo('App\Business');
    }
}
