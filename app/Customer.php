<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
    	'name',
        'business_name',
        'email',
        'telphone',
        'dni',
        'is_taxpayer',
        'reg_number',
        'tax_number',
        'business_line',
        'business_type_id',
        'customer_portfolio_id',
        'customer_group_id',
        'address',
        'country_id',
        'state_id',
        'zone_id',
        'city_id',
        'allowed_credit',
        'opening_balance',
        'credit_limit',
        'credit_balance',
        'payment_terms_id',
        'business_id',
        'created_by',
        'contact_mode_id',
        'first_purchase_location',
        'tax_group_id',
        'latitude',
        'length',
        'selling_price_group_id',
        'is_exempt',
        'is_foreign',
        'accounting_account_id',
        'from',
        'to',
        'cost'
    ];

    public function country()
    {
        return $this->belongsTo(\App\Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(\App\State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(\App\City::class, 'city_id');
    }

    public function vehicles()
    {
        return $this->hasMany(\App\CustomerVehicle::class);
    }
}
