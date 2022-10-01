<?php

namespace App\Optics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabOrderDetail extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = [
        'lab_order_id',
        'variation_id',
        'quantity',
        'warehouse_id',
        'location_id'
    ];

    public function variation()
    {
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }

    public function lab_order()
    {
        return $this->belongsTo(\App\Optics\LabOrder::class, 'lab_order_id');
    }

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }
}
