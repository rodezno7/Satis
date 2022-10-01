<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LostSale extends Model
{
    protected $fillable = [
        'reason_id',
        'quote_id',
        'user_id',
        'business_id',
        'lost_date',
        'comments',
        'created_by',
        'updated_by'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($lost_sale) {
            if (!\App::runningInConsole()) {
                $lost_sale->created_by  = auth()->user()->id;
            }
        });
        static::updating(function ($lost_sale) {
            if (!\App::runningInConsole()) {
                $lost_sale->updated_by  = auth()->user()->id;
            }
        });
    }
}
