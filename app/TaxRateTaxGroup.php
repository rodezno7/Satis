<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaxRateTaxGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tax_rate_tax_group';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
