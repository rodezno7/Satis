<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMContactReason extends Model
{
    protected $table = "crm_contact_reasons";

    protected $fillable = [
        'name',
        'description',
        'business_id',
    ];

    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false){
        $all_cr = CRMContactReason::where('business_id', $business_id)
        ->orderBy('name');
        $all_cr = $all_cr->pluck('name', 'id');

        if($prepend_none){
            $all_cr = $all_cr->prepend(__("crm.none_cr"), '');
        }

        if($prepend_all){
            $all_cr = $all_cr->prepend(__("report.all"), '');
        }

        return $all_cr;
    }
}
