<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClaimTypeHasUser extends Model
{
    protected $fillable = ['claim_id', 'user_id'];
}
