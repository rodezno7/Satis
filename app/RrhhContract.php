<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrhhContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_start_date', 
        'contract_end_date',
        'employee_id', 
        'rrhh_type_contract_id',
        'contract_status',
        'employee_name',
        'employee_age',
        'employee_gender',
        'employee_nationality',
        'employee_civil_status',
        'employee_profession',
        'employee_address',
        'employee_dni',
        'employee_dni_expedition_date',
        'employee_dni_expedition_place',
        'employee_tax_number',
        'employee_tax_number_approved',
        'employee_state',
        'employee_city',
        'employee_salary',
        'employee_department',
        'employee_position',
        'business_name',
        'business_legal_representative',
        'line_of_business',
        'business_address',
        'business_tax_number',
        'business_state',
        'current_date',
        'file',
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
