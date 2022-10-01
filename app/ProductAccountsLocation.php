<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAccountsLocation extends Model
{
    protected $fillable = ['type', 'product_id', 'location_id', 'catalogue_id'];
}
