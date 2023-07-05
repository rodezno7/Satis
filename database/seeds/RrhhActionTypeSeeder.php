<?php

use Illuminate\Database\Seeder;

class RrhhActionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (1, 3, 1, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (1, 7, 4, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (2, 3, 2, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (2, 2, 2, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (2, 7, 4, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (3, 1, 1, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (3, 2, 1, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (3, 3, 1, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (4, 7, 4, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (4, 8, 4, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (5, 4, 3, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (6, 6, 2, NULL, NULL)");
        DB::insert("INSERT INTO rrhh_action_type (rrhh_type_personnel_action_id, rrhh_required_action_id, rrhh_class_personnel_action_id, created_at, updated_at) VALUES (7, 5, 3, NULL, NULL)");
    }
}
