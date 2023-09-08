<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrhhTypeIncomeDiscount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 
        'name', 
        'payroll_column', 
        'status', 
        'business_id', 
        'deleted_at'
    ];

    public function rrhhIncomeDiscounts() {
        return $this->hasMany('App\RrhhIncomeDiscount');
    }

    public static $payrollColumns = [
        'Número de horas extras diurnas',
        'Número de horas extras nocturnas',
        'Comisiones',
        'Otros ingresos',
        'Otras deducciones',
        'Aguinaldo',
        'Vacaciones'
        // 'ISSS',
        // 'AFP',
        // 'Renta',
    ];
}