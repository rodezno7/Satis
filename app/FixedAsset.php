<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAsset extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['fixed_asset_type_id', 'code', 'name', 'description', 'type',
        'business_id', 'location_id', 'brand_id', 'year', 'model', 'initial_value', 'current_value'];
}