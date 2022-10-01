<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxGroup extends Model
{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The tax rates that belongs to tax groups
     */
    public function tax_rates() {
        return $this->belongsToMany('App\TaxRate', 'tax_rate_tax_group', 'tax_group_id', 'tax_rate_id');
    }

    /**
     * Get the transactions for the tax group
     */
    public function transactions() {
        return $this->hasMany('App\Transaction', 'tax_id', 'id');
    }

    public function contacts() {
        return $this->hasMany('App\Contact');
    }
}
