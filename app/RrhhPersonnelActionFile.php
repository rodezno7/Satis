<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhPersonnelActionFile extends Model
{
    protected $fillable = ['file', 'rrhh_personnel_action_id'];
    
    public function personnelAction(){
        return $this->belongsTo('App\RrhhPersonnelAction');
    }
}
