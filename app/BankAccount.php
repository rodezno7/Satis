<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = ['business_id', 'bank_id', 'catalogue_id', 'name', 'description', 'type', 'number'];

    public function bankTransaction()
    {
    	return $this->hasMany('App\bankTransaction');
    }

    public function bank()
    {
    	return $this->belongsTo('App\Bank');
    }

    public function catalogue()
    {
        return $this->belongsTo('App\Catalogue');
    }

    public function checkbook()
    {
        return $this->hasMany('App\BankCheckbook');
    }
    
}
