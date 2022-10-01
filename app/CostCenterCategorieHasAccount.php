<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostCenterCategorieHasAccount extends Model
{
    protected $fillable = ['categorie_id', 'account_id'];
}
