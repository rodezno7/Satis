<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeBankTransaction extends Model
{
	protected $fillable = ['name', 'type', 'type_entrie_id', 'enable_checkbook', 'enable_headline', 'enable_date_constraint'];
	
}
