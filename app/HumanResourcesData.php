<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HumanResourcesData extends Model {

    protected $fillable = ['code', 'short_name', 'value', 'human_resources_header_id', 'bussines_id', 'status'];

    public function header() {

        return $this->belongsTo('App\HumanResourcesHeader');
    }
    
}
