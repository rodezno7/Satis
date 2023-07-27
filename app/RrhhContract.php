<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrhhContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contract_start_date', 
        'contract_end_date',
        'employee_id', 
        'rrhh_type_contract_id',
        'status',
        'contract_start_date',
        'contract_end_date',
        'employee_name',
        'employee_age',
        'employee_dni',
        'employee_tax_number',
        'employee_state',
        'employee_city',
        'employee_salary',
        'employee_department',
        'employee_position',
        'business_name',
        'business_tax_number',
        'business_state',
        'current_date_letters',
        'template',
        'deleted_at'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employees');
    }
   
    public function rrhhTypeContract()
    {
        return $this->belongsTo('App\RrhhTypeContract');
    }
}
