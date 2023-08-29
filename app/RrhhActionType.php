<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RrhhActionType extends Model
{
    protected $table = 'rrhh_action_type';
    
    protected $fillable = [
        'rrhh_type_personnel_action_id',
        'rrhh_required_action_id',
        'rrhh_class_personnel_action_id'
    ];
}
