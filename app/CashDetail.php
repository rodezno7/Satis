<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashDetail extends Model
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
        'one_cent',
        'five_cents',
        'ten_cents',
        'twenty_five_cents',
        'one_dollar',
        'five_dollars',
        'ten_dollars',
        'twenty_dollars',
        'fifty_dollars',
        'one_hundred_dollars',
        'cash_register_id',
        'cashier_id',
        'business_id',
        'location_id'
    ];
}
