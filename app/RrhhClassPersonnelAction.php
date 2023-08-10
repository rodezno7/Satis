<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhClassPersonnelAction extends Model
{
    protected $fillable = [
        'name'
    ];
    
    public function rrhhClassActions() {
        return $this->hasMany('App\RrhhClassAction');
    }
}
