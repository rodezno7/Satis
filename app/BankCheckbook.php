<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankCheckbook extends Model
{
    protected $table = "bank_checkbooks";

    protected $fillable = [
        'name',
        'description',
        'serie',
        'initial_correlative',
        'final_correlative',
        'actual_correlative',
        'bank_account_id',
        'status'
    ];

    public function account()
    {
    	return $this->belongsTo('App\BankAccount');
    }
}
