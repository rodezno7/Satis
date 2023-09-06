<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayrollType extends Model
{
    protected $fillable = [
        'name',  
        'business_id'
    ];
    
    public function payrolls(){
        return $this->hasMany('App\Payroll');
    }
}
