<?php

use Illuminate\Database\Seeder;

class RrhhClassPersonnelActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT INTO rrhh_class_personnel_actions (id, name) VALUES (1, 'Entrada')");
        DB::insert("INSERT INTO rrhh_class_personnel_actions (id, name) VALUES (2, 'Movimiento')");
        DB::insert("INSERT INTO rrhh_class_personnel_actions (id, name) VALUES (3, 'Interna')");
        DB::insert("INSERT INTO rrhh_class_personnel_actions (id, name) VALUES (4, 'Salida')");
    }
}
