<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\RrhhRequiredAction;

class RrhhRequiredActionsSeeder extends Seeder
{
    /**
     * Run the database seeds. 
     *
     * @return void
     */
    public function run()
    {
        RrhhRequiredAction::firstOrCreate([
            'name' => 'Cambiar estado de empleado (De inactivo a activo)',
        ]);
        RrhhRequiredAction::firstOrCreate([
            'name' => 'Cambiar departamento/puesto',
        ]);
        RrhhRequiredAction::firstOrCreate([
            'name' => 'Cambiar salario',
        ]);
        RrhhRequiredAction::firstOrCreate([
            'name' => 'Seleccionar un periodo en específico',
        ]);
        RrhhRequiredAction::firstOrCreate([
            'name' => 'Cambiar cuenta bancaria',
        ]);
        RrhhRequiredAction::firstOrCreate([
            'name' => 'Cambiar forma de pago',
        ]);
        RrhhRequiredAction::firstOrCreate([
            'name' => 'Seleccionar la fecha en que entra en vigor',
        ]);
        RrhhRequiredAction::firstOrCreate([
            'name' => 'Cambiar estado de empleado (De activo a inactivo)',
        ]);
    }
}
