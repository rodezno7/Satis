<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suplies extends Model
{
    //
        protected $guarded = ['id'];

        protected $table = 'suplies';

        protected $fillable = ['suply_name','stock'];

        public $timestamps = false;
}
