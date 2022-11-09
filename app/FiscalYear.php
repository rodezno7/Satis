<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    protected $fillable = ['business_id', 'year'];

    public function period()
	{
		return $this->hasMany('App\AccountingPeriod');
	}
}
