<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhysicalInventoryLine extends Model
{
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
        'physical_inventory_id',
        'product_id',
        'variation_id',
        'quantity',
        'stock',
        'difference',
        'price',
        'created_by',
        'updated_by'
    ];

    /**
     * Get product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }

    /**
     * Get variation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variation()
    {
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }
}
