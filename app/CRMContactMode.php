<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMContactMode extends Model
{
    protected $table = "crm_contact_modes";

    protected $fillable = [
        'name',
        'description',
        'business_id',
    ];

    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false){
        $all_cm = CRMContactMode::where('business_id', $business_id)
        ->orderBy('name');
        $all_cm = $all_cm->pluck('name', 'id');

        if($prepend_none){
            $all_cm = $all_cm->prepend(__("crm.none_cm"), '');
        }

        if($prepend_all){
            $all_cm = $all_cm->prepend(__("report.all"), '');
        }

        return $all_cm;
    }
}
