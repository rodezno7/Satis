<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupportDocuments extends Model
{
    protected $fillable = ['name', 'description', 'business_id'];



    public static function forDropdown($business_id, $prepend_none = true, $preprend_all = false)
    {
        $all_docs = SupportDocuments::where('business_id', $business_id);
        $all_docs = $all_docs->pluck('name', 'id');

        if ($prepend_none) {
            $all_docs = $all_docs->prepend(__('correlatives.none_correlatives'), '');
        }

        if ($preprend_all) {
            $all_docs = $all_docs->prepend(__('report.all'), '');
        }

        return $all_docs;
    }
}
