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
        'Horas extras',
        'Comisiones',
        'Otros ingresos',
        'Otras deducciones',
        'Aguinaldo',
        'Vacaciones',
        'Bonificaciones',
        // 'ISSS',
        // 'AFP',
        // 'Renta',
    ];
}
