<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionHasImportExpense extends Model
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
        'transaction_id',
        'import_expense_id',
        'amount'
    ];
}
