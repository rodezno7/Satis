<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pos extends Model
{
    protected $table = 'pos';
    protected $fillable = [
        'name',
        'description',
        'bank_id',
        'business_id',
        'location_id',
        'status',
        'authorization_key',
    ];

}
