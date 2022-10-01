<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostCenterOperationAccount extends Model
{
    protected $fillable = [
        'cost_center_id',
        'sell_expense_account',
        'admin_expense_account',
        'finantial_expense_account',
        'non_dedu_expense_account',
        'updated_by'
    ];

    public function cost_center(){
        return $this->belongsTo('App\CostCenter');
    }
}
