<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalculationType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'status', 'business_id', 'deleted_at'];
    
    public function business(){
        return $this->belongsTo('App\Business');
    }
}
