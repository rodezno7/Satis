<?php

namespace App\Optics;

use Illuminate\Database\Eloquent\Model;

class StatusLabOrderStep extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
    	'status_id',
        'step_id'
    ];

    public function step()
    {
        return $this->belongsTo(\App\Optics\StatusLabOrder::class, 'step_id');
    }
}
