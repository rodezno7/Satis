<?php

use Illuminate\Database\Seeder;

class RrhhTypeWageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT INTO rrhh_type_wages (name, isss, afp, type, business_id, created_at, updated_at, deleted_at) VALUES ('Asalariado', 1, 1, 'Ley de salario', 3, '2023-07-11 14:35:05', '2023-07-11 14:35:05', NULL)");
        DB::insert("INSERT INTO rrhh_type_wages (name, isss, afp, type, business_id, created_at, updated_at, deleted_at) VALUES ('Servicio profesional', 0, 0, 'Honorario', 3, '2023-07-11 14:35:15', '2023-07-11 14:35:15', NULL)");
    }
}
