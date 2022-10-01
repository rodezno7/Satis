<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApportionmentHasTransaction extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function apportionment()
    {
        return $this->belongsTo(\App\Apportionment::class, 'apportionment_id');
    }

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }
}
