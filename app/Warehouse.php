<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $table ='warehouses';

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'code',
        'name',
        'business_location_id',
        'location',
        'description',
        'status',
        'business_id',
        'catalogue_id'
    ];

    /**
     * Gets the business location to which the warehouse belongs.
     *
     * @return \Illuminate\Database\Eloquent\Concerns\HasRelationships
     */
    public function business_location()
    {
        return $this->belongsTo('App\BusinessLocation');
    }

    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {
        $query = Warehouse::where('business_id', $business_id)->where('status', 'active');

        $permitted_warehouses = Warehouse::permittedWarehouses();
        if ($permitted_warehouses != 'all') {
            $query->whereIn('id', $permitted_warehouses);
        }

        $wh = $query->pluck('name', 'id');

        // Prepend none
        // if ($prepend_none) {
        //     $wh = $wh->prepend(__("lang_v1.none"), '');
        // }

        // Prepend all
        if ($prepend_all) {
            $wh = $wh->prepend(__("report.all"), '');
        }
        
        return $wh;
    }

    public function variation_location_details()
    {
        return $this->hasMany('App\VariationLocationDetails');
    }

    /**
     * Gives warehouses permitted for the logged in user
     *
     * @return string or array
     */
    public static function permittedWarehouses()
    {
        if (auth()->user()->can('access_all_warehouses')) {
            return 'all';
        } else {
            $business_id = request()->session()->get('user.business_id');
            $permitted_warehouses = [];
            $all_warehouses = Warehouse::where('business_id', $business_id)->get();
            
            foreach ($all_warehouses as $warehouse) {
                if (auth()->user()->can('warehouse.' . $warehouse->id)) {
                    $permitted_warehouses[] = $warehouse->id;
                }
            }
    
            return $permitted_warehouses;
        }
    }
}
