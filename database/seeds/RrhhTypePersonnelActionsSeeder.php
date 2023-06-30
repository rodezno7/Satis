<?php

use Illuminate\Database\Seeder;

class RrhhTypePersonnelActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT INTO rrhh_type_personnel_actions (name, required_authorization, business_id, created_at, updated_at) VALUES ('Cambio de salario', 1, 3, '2023-06-27 19:20:18', '2023-06-27 19:20:18')");
        DB::insert("INSERT INTO rrhh_type_personnel_actions (name, required_authorization, business_id, created_at, updated_at) VALUES ('Cambio de puesto', 1, 3, '2023-06-27 18:04:19', '2023-06-27 18:04:19')");
        DB::insert("INSERT INTO rrhh_type_personnel_actions (name, required_authorization, business_id, created_at, updated_at) VALUES ('Reincorporación', 0, 3, '2023-06-27 19:21:07', '2023-06-27 19:21:07')");
        DB::insert("INSERT INTO rrhh_type_personnel_actions (name, required_authorization, business_id, created_at, updated_at) VALUES ('Despido justificado', 1, 3, '2023-06-27 19:21:26', '2023-06-27 19:21:26')");
        DB::insert("INSERT INTO rrhh_type_personnel_actions (name, required_authorization, business_id, created_at, updated_at) VALUES ('Permiso', 0, 3, '2023-06-27 19:21:41', '2023-06-27 19:21:41')");
        DB::insert("INSERT INTO rrhh_type_personnel_actions (name, required_authorization, business_id, created_at, updated_at) VALUES ('Cambio de forma de pago', 0, 3, '2023-06-27 19:22:11', '2023-06-27 19:22:11')");
        DB::insert("INSERT INTO rrhh_type_personnel_actions (name, required_authorization, business_id, created_at, updated_at) VALUES ('Cambio de cuenta bancaria', 0, 3, '2023-06-27 19:22:31', '2023-06-27 19:22:31')");
    }
}
