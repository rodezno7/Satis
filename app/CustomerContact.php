<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'landline',
        'email',
        'cargo',
        'customer_id',
    ];
}
