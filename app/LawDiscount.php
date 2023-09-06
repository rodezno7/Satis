<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LawDiscount extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'from', 
        'until', 
        'base', 
        'fixed_fee', 
        'employee_percentage', 
        'employer_value', 
        'status',
        'payment_period_id', 
        'institution_law_id', 
        'business_id', 
        'deleted_at'
    ];
    
    public function paymentPeriod(){
        return $this->belongsTo('App\PaymentPeriod');
    }
    
    public function institutionLaw(){
        return $this->belongsTo('App\InstitutionLaw');
    }

    public function business(){
        return $this->belongsTo('App\Business');
    }
}
