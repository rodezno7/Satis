<?php

use App\RrhhActionType;
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
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 1,
            'rrhh_required_action_id' => 3,
            'rrhh_class_personnel_action_id' => 1,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 1,
            'rrhh_required_action_id' => 7,
            'rrhh_class_personnel_action_id' => 4,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 2,
            'rrhh_required_action_id' => 3,
            'rrhh_class_personnel_action_id' => 2,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 2,
            'rrhh_required_action_id' => 2,
            'rrhh_class_personnel_action_id' => 2,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 2,
            'rrhh_required_action_id' => 7,
            'rrhh_class_personnel_action_id' => 4,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 3,
            'rrhh_required_action_id' => 1,
            'rrhh_class_personnel_action_id' => 1,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 3,
            'rrhh_required_action_id' => 2,
            'rrhh_class_personnel_action_id' => 1,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 3,
            'rrhh_required_action_id' => 3,
            'rrhh_class_personnel_action_id' => 1,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 4,
            'rrhh_required_action_id' => 7,
            'rrhh_class_personnel_action_id' => 4,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 4,
            'rrhh_required_action_id' => 8,
            'rrhh_class_personnel_action_id' => 4,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 5,
            'rrhh_required_action_id' => 7,
            'rrhh_class_personnel_action_id' => 4,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 6,
            'rrhh_required_action_id' => 6,
            'rrhh_class_personnel_action_id' => 2,
        ]);
        RrhhActionType::firstOrCreate([
            'rrhh_type_personnel_action_id' => 7,
            'rrhh_required_action_id' => 5,
            'rrhh_class_personnel_action_id' => 3,
        ]);
    }
}
