<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhHeader extends Model {

    protected $fillable = ['name', 'description', 'status'];

    public function data() {

        return $this->hasMany('App\RrhhData');
    }

}
