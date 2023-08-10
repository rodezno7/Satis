<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'automatic_closing',
        'exit_time',
        'business_id',
    ];

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }
}
