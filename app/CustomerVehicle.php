<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerVehicle extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function brand()
    {
        return $this->belongsTo(\App\Brands::class, 'brand_id');
    }
}
