<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HumanResourceBanks extends Model {

    protected $fillable = ['name', 'checkformat', 'payrollformat', 'created_by', 'updated_by', 'host'];
}
