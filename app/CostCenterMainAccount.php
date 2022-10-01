<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostCenterMainAccount extends Model
{
    protected $fillable = ['cost_center_id', 'expense_account_id', 'updated_by'];

    public function cost_center(){
        return $this->belongsTo('App\CostCenter');
    }
}
