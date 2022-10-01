<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;
class Brands extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'logo',
        'created_by'
    ];

    public static function brandsDropdown($business_id, $exclude_default = false, $prepend_none = true)
    {

            $all_data = Brands::where('business_id', $business_id)
                        ->select('id', DB::raw("name as customer"));

        $result = $all_data->pluck('customer', 'id');

        //Prepend none
        if ($prepend_none) {
            $result = $result->prepend(__('lang_v1.none'), '');
        }

        return $result;
    }
}
