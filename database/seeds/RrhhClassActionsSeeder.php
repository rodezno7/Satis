<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\RrhhClassAction;

class RrhhClassActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 1,
            'rrhh_required_action_id' => 1,
        ]);
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 1,
            'rrhh_required_action_id' => 2,
        ]);
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 1,
            'rrhh_required_action_id' => 3,
        ]);
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 2,
            'rrhh_required_action_id' => 3,
        ]);
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 2,
            'rrhh_required_action_id' => 6,
        ]);
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 2,
            'rrhh_required_action_id' => 2,
        ]);
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 3,
            'rrhh_required_action_id' => 4,
        ]);
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 3,
            'rrhh_required_action_id' => 5,
        ]);
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 4,
            'rrhh_required_action_id' => 7,
        ]);
        RrhhClassAction::firstOrCreate([
            'rrhh_class_personnel_action_id' => 4,
            'rrhh_required_action_id' => 8,
        ]);
    }
}
