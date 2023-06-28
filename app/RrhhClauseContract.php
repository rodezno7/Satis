<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrhhClauseContract extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'content', 'deleted_at'];
    
}
