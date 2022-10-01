<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionTaxDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_tax_details';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
