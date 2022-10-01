<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'short_name', 'code', 'flag', 'business_id'];

	public function state()
	{
		return $this->hasMany('App\State');
	}
	
	public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false){
		
		$all_cnt = Country::where('business_id', $business_id)->orderBy('name');
		$all_cnt = $all_cnt->pluck('name', 'id');

		if($prepend_none){
			$all_cnt = $all_cnt->prepend(__("geography.none_country"), '');
		}

		if($prepend_all){
			$all_cnt = $all_cnt->prepend(__("geography.all_your_countries"), '');
		}

		return $all_cnt;
	}
}
