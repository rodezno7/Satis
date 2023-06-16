<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HumanResourceBanks extends Model {
    use SoftDeletes;
    protected $fillable = ['name', 'checkformat', 'payrollformat', 'created_by', 'updated_by', 'host'];
}
