<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrhhTypePersonnelAction extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'required_authorization', 'apply_to_many', 'business_id'];
    
}
