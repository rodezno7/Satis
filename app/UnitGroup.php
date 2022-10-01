<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Iatstuti\Database\Support\CascadeSoftDeletes;

use App\UnitGroupLines;


class UnitGroup extends Model
{
  use SoftDeletes, CascadeSoftDeletes;
  protected $dates = ['deleted_at'];
  protected $cascadeDeletes = ['UnitGroupLines'];
  protected $fillable = ['business_id', 'unit_id', 'description'];


    /**
     * Get business info
     */
    public function business()
    {
      return $this->belongsTo('App\Business');
    }

    public function unit()
    {
      return $this->belongsTo('App\Unit');
    }

	/**
     * Get the detail lines
     */

	public function unitGroupLines()
	{
		return $this->hasMany('App\UnitGroupLines');
	}

  public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
  {

    $query = UnitGroup::where('business_id', $business_id);
    $all_emp = $query->select('id', 'description');
    $all_emp = $all_emp->pluck('description', 'id');

        //Prepend none
    if ($prepend_none) {
      $all_emp = $all_emp->prepend(__("lang_v1.none"), '');
    }

        //Prepend none
    if ($prepend_all) {
      $all_emp = $all_emp->prepend(__("report.all"), '');
    }

    return $all_emp;
  }
}
