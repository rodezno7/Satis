<?php

use Illuminate\Database\Seeder;
use App\RrhhClassPersonnelAction;

class RrhhClassPersonnelActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RrhhClassPersonnelAction::firstOrCreate([
            'name' => 'Entrada',
        ]);
        RrhhClassPersonnelAction::firstOrCreate([
            'name' => 'Movimiento',
        ]);
        RrhhClassPersonnelAction::firstOrCreate([
            'name' => 'Interna',
        ]);
        RrhhClassPersonnelAction::firstOrCreate([
            'name' => 'Salida',
        ]);
    }
}
