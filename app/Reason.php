<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $fillable = [
        'reason',
        'comments'
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
