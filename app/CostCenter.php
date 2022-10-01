<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCenter extends Model
{
    use SoftDeletes;
	protected $fillable = ['name', 'description', 'created_by', 'updated_by', 'location_id', 'business_id'];

    public function cost_center_main_account()
    {
    	return $this->hasOne('App\CostCenterMainAccount');
    }

    public function cost_center_operation_account(){
        return $this->hasOne('App\CostCenterOperationAccount');
    }
}
