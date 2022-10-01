<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Iatstuti\Database\Support\CascadeSoftDeletes;

class Module extends Model
{
	use SoftDeletes, CascadeSoftDeletes;
	protected $dates = ['deleted_at'];
	protected $cascadeDeletes = ['permission'];
	protected $fillable = ['name', 'description'];

	public function permission()
	{
		return $this->hasMany('App\Permission');
	}
}
