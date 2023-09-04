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
        'planilla_column', 
        'status', 
        'business_id', 
        'deleted_at'
    ];

    public function rrhhIncomeDiscounts() {
        return $this->hasMany('App\RrhhIncomeDiscount');
    }

    public static $planillaColumns = [
        'Número de horas extras diurnas',
        'Número de horas extras nocturnas',
        'Comisiones',
        'Otras deducciones',
        'Aguinaldo',
        'Vacaciones'
        // 'ISSS',
        // 'AFP',
        // 'Renta',
    ];
}
