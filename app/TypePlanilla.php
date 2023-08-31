<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypePlanilla extends Model
{
    protected $fillable = [
        'name',  
        'business_id'
    ];
    
    public function planillas(){
        return $this->hasMany('App\planilla');
    }
}
