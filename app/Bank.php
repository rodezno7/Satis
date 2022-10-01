<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Iatstuti\Database\Support\CascadeSoftDeletes;

class Bank extends Model
{
	use SoftDeletes, CascadeSoftDeletes;
	protected $dates = ['deleted_at'];
	protected $cascadeDeletes = ['bankAccount'];
	protected $fillable = ['name', 'business_id', 'print_format'];

    public function bankAccount()
    {
    	return $this->hasMany('App\bankAccount');
    }
}
