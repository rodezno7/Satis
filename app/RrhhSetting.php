<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhSetting extends Model
{
    protected $table = 'rrhh_settings';

    protected $fillable = [
        'automatic_closing',
        'exit_time',
        'exempt_bonus',
        'business_id',
    ];

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }
}
