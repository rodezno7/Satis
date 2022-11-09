<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductHasSuppliers extends Model
{
    protected $table = 'product_has_suppliers';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
