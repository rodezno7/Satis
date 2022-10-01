<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowOportunitiesHasProduct extends Model
{
    protected $fillable = [
        'follow_oportunitie_id',
        'variation_id',
        'quantity',
        'required_quantity'
    ];
}
