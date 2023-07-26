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
        'name_employee',
        'age_employee',
        'dni_employee',
        'tax_number_employee',
        'state_employee',
        'city_employee',
        'salary_employee',
        'department_employee',
        'position_employee',
        'name_business',
        'tax_number_business',
        'state_business',
        'current_date_letters',
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
