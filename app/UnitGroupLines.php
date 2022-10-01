<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitGroupLines extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['unit_id', 'unit_group_id', 'factor', 'default'];

    /**
     * Get unit group info
     */
    public function unitGroup()
	{
		return $this->belongsTo('App\UnitGroup');
	}

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }
}
