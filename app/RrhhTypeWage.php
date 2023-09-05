<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrhhTypeWage extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'isss', 'afp', 'type', 'business_id', 'deleted_at'];
    
    public function employees() {
        return $this->hasMany('App\Employees', 'type_id');
    }
}
