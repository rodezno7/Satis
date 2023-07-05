<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RrhhClassActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (1, 1)");
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (1, 2)");
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (1, 3)");
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (2, 3)");
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (2, 6)");
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (2, 2)");
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (3, 4)");
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (3, 5)");
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (4, 7)");
        DB::insert("INSERT INTO rrhh_class_actions (rrhh_class_personnel_action_id, rrhh_required_action_id) VALUES (4, 8)");
    }
}
