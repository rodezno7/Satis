<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanillaStatus extends Model
{
    protected $fillable = [
        'name',  
        'business_id'
    ];
    
    public function planillas(){
        return $this->hasMany('App\planilla');
    }
}
