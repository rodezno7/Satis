<?php

namespace App\Optics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusLabOrder extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = [
    	'code',
        'name',
        'descripction',
        'status',
        'business_id',
        'color',
        'is_default',
        'print_order',
        'transfer_sheet',
        'second_time',
        'material_download',
        'save_and_print'
    ];
}
