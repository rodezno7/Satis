<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name', 'zip_code', 'business_id', 'country_id', 'zone_id'];

	public function zone()
	{
		return $this->belongsTo('App\Zone');
	}
	
    public function country()
    {
    	return $this->belongsTo('App\Country');
	}

	public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false){
		
		$all_sts = State::where('business_id', $business_id)->orderBy('name');
		$all_sts = $all_sts->pluck('name', 'id');

		if($prepend_none){
			$all_sts = $all_sts->prepend(__("geography.none_country"), '');
		}

		if($prepend_all){
			$all_sts = $all_sts->prepend(__("geography.all_your_countries"), '');
		}

		return $all_sts;
	}

}
