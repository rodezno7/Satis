<?php

use App\CalculationType;
use App\InstitutionLaw;
use App\LawDiscount;
use App\Business;
use App\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PlanillaCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $business = Business::all();

        foreach ($business as $item) {
            CalculationType::firstOrCreate([
                'name' => 'Semanal',
            ]);

            CalculationType::firstOrCreate([
                'name' => 'Quincenal',
            ]);

            CalculationType::firstOrCreate([
                'name' => 'Mensual',
            ]);

            CalculationType::firstOrCreate([
                'name' => 'Semestral',
            ]);

            CalculationType::firstOrCreate([
                'name' => 'Anual',
            ]);




            InstitutionLaw::firstOrCreate([
                'name' => 'Renta',
                'description' => 'Ministerio de hacienda',
                'employeer_number' => null,
                'business_id' => $item->id,
                'deleted_at' => null,
            ]);

            InstitutionLaw::firstOrCreate([
                'name' => 'ISSS',
                'description' => 'ISSS',
                'employeer_number' => '120321210',
                'business_id' => $item->id,
                'deleted_at' => null,
            ]);

            InstitutionLaw::firstOrCreate([
                'name' => 'AFP Crecer',
                'description' => 'AFP Crecer',
                'employeer_number' => null,
                'business_id' => $item->id,
                'deleted_at' => null,
            ]);

            InstitutionLaw::firstOrCreate([
                'name' => 'AFP Confia',
                'description' => 'AFP Confia',
                'employeer_number' => null,
                'business_id' => $item->id,
                'deleted_at' => null,
            ]);

            InstitutionLaw::firstOrCreate([
                'name' => 'Insaforp',
                'description' => 'Insaforp',
                'employeer_number' => null,
                'business_id' => $item->id,
                'deleted_at' => null,
            ]);




            LawDiscount::firstOrCreate([
                'from'=> 0.01, 
                'until' => 236, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 0, 
                'employer_value' => 0, 
                'calculation_type_id' => 1, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 236.01, 
                'until' => 447.62, 
                'base' => 236, 
                'fixed_fee' => 8.83, 
                'employee_percentage' => 10, 
                'employer_value' => 0, 
                'calculation_type_id' => 1, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 447.63, 
                'until' => 1019.05, 
                'base' => 447.62, 
                'fixed_fee' => 30, 
                'employee_percentage' => 20, 
                'employer_value' => 0, 
                'calculation_type_id' => 1, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 1019.06, 
                'until' => 25000, 
                'base' => 1019.05, 
                'fixed_fee' => 144.28, 
                'employee_percentage' => 30, 
                'employer_value' => 0, 
                'calculation_type_id' => 1, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);
        }

        $module = Module::firstOrCreate(
            ['name' => 'Planillas'],
            ['description' => 'Gestión de planillas', 'status' => 1]
        );

        Permission::firstOrCreate(
            ['name' => 'planilla_catalogues.view'],
            ['description' => 'Ver catálogos de planilla', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'planilla_catalogues.create'],
            ['description' => 'Crear catálogos de planilla', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'planilla_catalogues.update'],
            ['description' => 'Actualizar catálogos de planilla', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'planilla_catalogues.delete'],
            ['description' => 'Eliminar catálogos de planilla', 'guard_name' => 'web', 'module_id' => $module->id]
        );





        Permission::firstOrCreate(
            ['name' => 'planilla.view'],
            ['description' => 'Ver planilla', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'planilla.create'],
            ['description' => 'Crear planilla', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'planilla.update'],
            ['description' => 'Actualizar planilla', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'planilla.delete'],
            ['description' => 'Eliminar planilla', 'guard_name' => 'web', 'module_id' => $module->id]
        );
    }
}
