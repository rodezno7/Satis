<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountCard extends Model
{
    //

     protected $guarded = ['id'];

      protected $table ='discount_cards';

     protected $fillable = ['value_'];

	 public $timestamps = false;
}
