<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RrhhRequiredActionsSeeder extends Seeder
{
    /**
     * Run the database seeds. 
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT INTO rrhh_required_actions (name) VALUES ('Cambiar estado de empleado (De inactivo a activo)')");
        DB::insert("INSERT INTO rrhh_required_actions (name) VALUES ('Cambiar departamento/puesto')");
        DB::insert("INSERT INTO rrhh_required_actions (name) VALUES ('Cambiar salario')");
        DB::insert("INSERT INTO rrhh_required_actions (name) VALUES ('Seleccionar un periodo en específico')");
        DB::insert("INSERT INTO rrhh_required_actions (name) VALUES ('Cambiar cuenta bancaria')");
        DB::insert("INSERT INTO rrhh_required_actions (name) VALUES ('Cambiar forma de pago')");
        DB::insert("INSERT INTO rrhh_required_actions (name) VALUES ('Seleccionar la fecha en que entra en vigor')");
        DB::insert("INSERT INTO rrhh_required_actions (name) VALUES ('Cambiar estado de empleado (De activo a inactivo)')");
    }
}
