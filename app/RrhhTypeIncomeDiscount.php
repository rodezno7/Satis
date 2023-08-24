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
        'percentage', 
        'isss', 
        'afp', 
        'rent', 
        'status', 
        'business_id', 
        'deleted_at'
    ];

    public static $planillaColumns = [
        'Número de horas extras diurnas',
        'Número de horas extras nocturnas',
        'Comisiones',
        'Otras deducciones',
        'ISSS',
        'AFP',
        'Renta',
    ];
}
