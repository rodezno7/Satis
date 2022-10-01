<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteLine extends Model
{
    protected $guarded = ['id'];
    
    public function quote()
    {
        return $this->belongsTo(\App\Quote::class);
    }
    public function variation(){
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }
}
