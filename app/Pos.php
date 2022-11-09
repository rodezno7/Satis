<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pos extends Model
{
    protected $table = 'pos';
    protected $fillable = [
        'name',
        'description',
        'bank_id',
        'business_id',
        'location_id',
        'status',
        'authorization_key',
    ];

    /**
     * 
     */
    public static function forDropdown($business_id, $prepend_none = false, $prepend_all = false) {
        $query = Pos::where('business_id', $business_id)
            ->where('status', 'active');

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('pos.location_id', $permitted_locations);
        }

        $pos = $query->pluck('name', 'id');

        //Prepend none
        if ($prepend_none) {
            $pos = $pos->prepend(__("lang_v1.none"), '');
        }

        //Prepend none
        if ($prepend_all) {
            $pos = $pos->prepend(__("report.all"), '');
        }

        return $pos;
    }
}
