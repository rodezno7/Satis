<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhPersonnelActionAuthorizer extends Model
{
    protected $fillable = ['rrhh_personnel_action_id', 'user_id'];

    public function user(){
        return $this->belongsTo(\App\User::class, 'user_id');
    }
    
    public function personnelAction(){
        return $this->belongsTo('App\RrhhPersonnelAction');
    }
}
