<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\AssignOp\Concat;
Use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employees extends Model
{
    
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of customer group for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @param $prepend_all = false (boolean)
     *
     * @return array
     */

    protected $fillable = ['first_name', 'last_name', 'email', 'mobile', 'location_id', 'position_id','hired_date', 'fired_date', 'birth_date', 'business_id', 'created_by', 'agent_code', 'user_id', 'short_name'];

    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {

        $query = Employees::where('business_id', $business_id);
        $all_emp = $query->select('id', DB::raw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) as full_name"));
        $all_emp = $all_emp->pluck('full_name', 'id');

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

    public static function SellersDropdown($business_id, $prepend_none = true)
     {

        $all_cmmsn_agnts = Employees::where('business_id', $business_id)
        ->whereNotNull('agent_code')
        ->select('id', DB::raw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) as full_name"));

        $users = $all_cmmsn_agnts->pluck('full_name', 'id');

        //Prepend none
        if ($prepend_none) {
            $users = $users->prepend(__('lang_v1.none'), '');
        }

        return $users;
    }
}
