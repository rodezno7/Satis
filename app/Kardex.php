<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function movement_type()
    {
        return $this->belongsTo(\App\MovementType::class, 'movement_type_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }
}
