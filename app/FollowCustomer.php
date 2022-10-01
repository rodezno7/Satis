<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowCustomer extends Model
{
    protected $fillable = ['customer_id', 'contact_type', 'contact_reason_id', 'product_cat_id', 'product_not_found', 'product_not_stock', 'products_not_found_desc', 'notes', 'contact_mode_id', 'date', 'register_by'];
}
