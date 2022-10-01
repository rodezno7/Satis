<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oportunity extends Model
{
    protected $table = "oportunities";
    protected $fillable = [
        'contact_type', 'contact_date',
         'contact_reason_id', 'name', 'company', 'charge', 'email', 'contacts', 'known_by', 'refered_id', 'contact_mode_id', 'social_user', 'country_id', 'state_id', 'city_id', 'product_cat_id', 'product_not_found', 'products_not_found_desc', 'created_by', 'status', 'business_id'];
}
