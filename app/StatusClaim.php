<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusClaim extends Model
{
    protected $fillable = ['correlative', 'name', 'status', 'predecessor', 'color'];
}
