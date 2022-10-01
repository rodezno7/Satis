<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * Get the products image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            $image_url = asset('/uploads/img/' . $this->image);
        } else {
            $image_url = asset('/img/default.png');
        }
        return $image_url;
    }

    public function product_variations()
    {
        return $this->hasMany(\App\ProductVariation::class);
    }
    
    /**
     * Get the brand associated with the product.
     */
    public function brand()
    {
        return $this->belongsTo(\App\Brands::class);
    }
    
     /**
     * Get the unit associated with the product.
     */
    public function unit()
    {
        return $this->belongsTo(\App\Unit::class);
    }
    /**
     * Get category associated with the product.
     */
    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }
    /**
     * Get sub-category associated with the product.
     */
    public function sub_category()
    {
        return $this->belongsTo(\App\Category::class, 'sub_category_id', 'id');
    }
    
    /**
     * Get the brand associated with the product.
     */
    public function product_tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax', 'id');
    }

    /**
     * Get the variations associated with the product.
     */
    public function variations()
    {
        return $this->hasMany(\App\Variation::class);
    }

    /**
     * If product type is modifier get products associated with it.
     */
    public function modifier_products()
    {
        return $this->belongsToMany(\App\Product::class, 'res_product_modifier_sets', 'modifier_set_id', 'product_id');
    }

    /**
     * If product type is modifier get products associated with it.
     */
    public function modifier_sets()
    {
        return $this->belongsToMany(\App\Product::class, 'res_product_modifier_sets', 'product_id', 'modifier_set_id');
    }

    /**
     * Get the purchases associated with the product.
     */
    public function purchase_lines()
    {
        return $this->hasMany(\App\PurchaseLine::class);
    }
    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {

        $query = Product::where('business_id', $business_id)->where('clasification', '!=', 'kits');
        $all_emp = $query->select('id', 'name');
        $all_emp = $all_emp->pluck('name', 'id');

        //Prepend none
        if ($prepend_none) {
            $all_emp = $all_emp->prepend(__("lang_v1.none"), '');
        }

        //Prepend none
        if ($prepend_all) {
            $all_emp = $all_emp->prepend(__("report.all"), '');
        }
        
        return $all_emp;
    }

    public static function lastThreePurchases($id)
    {
        return Product::leftJoin('purchase_lines as pl', 'products.id', 'pl.product_id')
            ->leftJoin('transactions as t', 'pl.transaction_id', 't.id')
            ->leftJoin('contacts as c', 't.contact_id', 'c.id')
            ->where('pl.product_id', $id)
            ->where('t.type', 'purchase')
            ->select(
                'pl.created_at',
                't.transaction_date',
                'c.name',
                'pl.quantity',
                'pl.purchase_price',
                'pl.purchase_price_inc_tax',
                't.ref_no',
                't.purchase_type'
            )
            ->latest()
            ->take(3)
            ->get();
    }

    public static function getStock($id)
    {
        return Product::leftJoin('variations', 'products.id', 'variations.product_id')
            ->leftJoin('variation_location_details as vld', 'variations.id', 'vld.variation_id')
            ->where('products.id', $id)
            ->select(DB::raw('IF(vld.qty_available > 0, round(vld.qty_available, 2), 0) as stock'))
            ->first();
    }

    public function variation_location_details()
    {
        return $this->hasMany('App\VariationLocationDetails');
    }
}
