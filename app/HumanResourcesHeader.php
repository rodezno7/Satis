<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HumanResourcesHeader extends Model {

    protected $fillable = ['name', 'description', 'status'];

    public function data() {

        return $this->hasMany('App\HumanResourcesData');
    }

}
