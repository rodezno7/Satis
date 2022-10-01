<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditDocuments extends Model
{
    protected $fillable = ['transaction_id', 'reason_id', 'register_date', 'courier_id', 'courier_date', 'business_id'];
}
