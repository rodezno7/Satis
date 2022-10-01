<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Apportionment extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'reference',
        'distributing_base',
        'vat_amount',
        'is_finished',
        'business_id',
        'apportionment_date'
    ];

    public function import_expenses()
    {
        return $this->hasMany(\App\ApportionmentHasImportExpense::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\ApportionmentHasTransaction::class);
    }
}
