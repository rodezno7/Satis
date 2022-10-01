<?php

namespace App\Optics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalLab extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'address',
        'description',
        'business_id'
    ];
}
