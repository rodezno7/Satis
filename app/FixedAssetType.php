<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FixedAssetType extends Model
{
    protected $fillable = ['name', 'description', 'percentage', 'accounting_account_id', 'business_id'];

    public static function forDropdown($business_id){
        $fixed_asset_types = FixedAssetType::where('business_id', $business_id)->pluck('name', 'id');

        return $fixed_asset_types;
    }
}