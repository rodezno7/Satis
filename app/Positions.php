<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Positions extends Model
{
    use SoftDeletes;
    
    protected  $fillable = ['name', 'descriptions', 'business_id', 'created_by'];


    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {
        $all_ps = Positions::where('business_id', $business_id);
        $all_ps = $all_ps->pluck('name', 'id');

        //Prepend none
        if ($prepend_none) {
            $all_ps = $all_ps->prepend(__("employees.none_positions"), '');
        }

        //Prepend none
        if ($prepend_all) {
            $all_ps = $all_ps->prepend(__("report.all"), '');
        }
        
        return $all_ps;
    }


}
