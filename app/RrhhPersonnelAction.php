<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrhhPersonnelAction extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'description', 
        'start_date', 
        'end_date', 
        'effective_date', 
        'status', 
        'rrhh_type_personnel_action_id', 
        'payment_id', 
        'bank_id', 
        'bank_account', 
        'authorization_date', 
        'employee_id', 
        'user_id'
    ];

    public function employee() {
        return $this->belongsTo('App\Employees');
    }

    public function payment() {
        return $this->belongsTo('App\RrhhData');
    }

    public function bank() {
        return $this->belongsTo('App\Bank');
    }

    public function user(){
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function personnelActionAuthorizers(){
        return $this->hasMany('App\RrhhPersonnelAction');
    }
}
