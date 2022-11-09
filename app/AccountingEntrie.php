<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountingEntrie extends Model
{
	protected $fillable = ['date', 'number', 'correlative', 'short_name', 'description', 'accounting_period_id', 'type_entrie_id', 'business_location_id', 'status', 'business_id'];

	public function detail()
	{
		return $this->hasMany('App\AccountingEntriesDetail');
	}

	public function bankTransaction()
	{
		return $this->hasOne('App\BankTransaction');
	}

	public function type()
    {
    	return $this->belongsTo('App\TypeEntrie', 'type_entrie_id');
    }
    public function period()
    {
    	return $this->belongsTo('App\AccountingPeriod');
    }
}
