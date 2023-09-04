<?php

use App\CalculationType;
use App\PaymentPeriod;
use App\InstitutionLaw;
use App\LawDiscount;
use App\Business;
use App\Module;
use App\PlanillaStatus;
use App\TypePlanilla;
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
            PaymentPeriod::firstOrCreate([
                'name' => 'Semanal',
                'days' => 7,
                'business_id' => $item->id,
            ]);

            PaymentPeriod::firstOrCreate([
                'name' => 'Catorcenal',
                'days' => 14,
                'business_id' => $item->id,
            ]);

            PaymentPeriod::firstOrCreate([
                'name' => 'Quincenal',
                'days' => 15,
                'business_id' => $item->id,
            ]);

            PaymentPeriod::firstOrCreate([
                'name' => 'Primera quincena',
                'days' => 15,
                'business_id' => $item->id,
            ]);

            PaymentPeriod::firstOrCreate([
                'name' => 'Segunda quincena',
                'days' => 15,
                'business_id' => $item->id,
            ]);

            PaymentPeriod::firstOrCreate([
                'name' => 'Mensual',
                'days' => 30,
                'business_id' => $item->id,
            ]);

            PaymentPeriod::firstOrCreate([
                'name' => 'Semestral',
                'days' => 365,
                'business_id' => $item->id,
            ]);

            PaymentPeriod::firstOrCreate([
                'name' => 'Anual',
                'days' => 365,
                'business_id' => $item->id,
            ]);

            

            TypePlanilla::firstOrCreate([
                'name' => 'Planilla de sueldos',
                'business_id' => $item->id,
            ]);

            TypePlanilla::firstOrCreate([
                'name' => 'Planilla de honorarios',
                'business_id' => $item->id,
            ]);

            TypePlanilla::firstOrCreate([
                'name' => 'Planilla de comisiones',
                'business_id' => $item->id,
            ]);

            TypePlanilla::firstOrCreate([
                'name' => 'Planilla de aguinaldos',
                'business_id' => $item->id,
            ]);



            PlanillaStatus::firstOrCreate([
                'name' => 'Iniciada',
                'business_id' => $item->id,
            ]);

            PlanillaStatus::firstOrCreate([
                'name' => 'Calculada',
                'business_id' => $item->id,
            ]);

            PlanillaStatus::firstOrCreate([
                'name' => 'Aprobada',
                'business_id' => $item->id,
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



            //ISSS
            //Quincenal
            LawDiscount::firstOrCreate([
                'from'=> 0, 
                'until' => 500, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 3, 
                'employer_value' => 7.5, 
                'payment_period_id' => 3, 
                'institution_law_id' => 2, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            //Mensual
            LawDiscount::firstOrCreate([
                'from'=> 0, 
                'until' => 1000, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 3, 
                'employer_value' => 7.5, 
                'payment_period_id' => 6, 
                'institution_law_id' => 2, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);


            //INSAFORP
            LawDiscount::firstOrCreate([
                'from'=> 0, 
                'until' => 200, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 0, 
                'employer_value' => 1, 
                'payment_period_id' => 3, 
                'institution_law_id' => 5, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);


            //AFP
            //Quincenal
            LawDiscount::firstOrCreate([
                'from'=> 0, 
                'until' => 3250, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 7.25, 
                'employer_value' => 7.75, 
                'payment_period_id' => 3, 
                'institution_law_id' => 3, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 0, 
                'until' => 3250, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 7.25, 
                'employer_value' => 7.75, 
                'payment_period_id' => 3, 
                'institution_law_id' => 4, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);


            //Mensual
            LawDiscount::firstOrCreate([
                'from'=> 0, 
                'until' => 6500, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 7.25, 
                'employer_value' => 7.75, 
                'payment_period_id' => 6, 
                'institution_law_id' => 3, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 0, 
                'until' => 6500, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 7.25, 
                'employer_value' => 7.75, 
                'payment_period_id' => 6, 
                'institution_law_id' => 4, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);


            //RENTA
            //------Semanal
            LawDiscount::firstOrCreate([
                'from'=> 0.01, 
                'until' => 118, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 0, 
                'employer_value' => 0, 
                'payment_period_id' => 1, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 118.01, 
                'until' => 223.81, 
                'base' => 118, 
                'fixed_fee' => 4.42, 
                'employee_percentage' => 10, 
                'employer_value' => 0, 
                'payment_period_id' => 1, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 223.82, 
                'until' => 509.52, 
                'base' => 223.81, 
                'fixed_fee' => 15, 
                'employee_percentage' => 20, 
                'employer_value' => 0, 
                'payment_period_id' => 1, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 509.53, 
                'until' => 1000000000, 
                'base' => 509.52, 
                'fixed_fee' => 72.14, 
                'employee_percentage' => 30, 
                'employer_value' => 0, 
                'payment_period_id' => 1, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            //------Quincenal
            LawDiscount::firstOrCreate([
                'from'=> 0.00, 
                'until' => 236, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 0, 
                'employer_value' => 0, 
                'payment_period_id' => 3, 
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
                'payment_period_id' => 3, 
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
                'payment_period_id' => 3, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 1019.06, 
                'until' => 1000000000, 
                'base' => 1019.05, 
                'fixed_fee' => 144.28, 
                'employee_percentage' => 30, 
                'employer_value' => 0, 
                'payment_period_id' => 3, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            //-------Mensual
            LawDiscount::firstOrCreate([
                'from'=> 0.00, 
                'until' => 472, 
                'base' => 0, 
                'fixed_fee' => 0, 
                'employee_percentage' => 0, 
                'employer_value' => 0, 
                'payment_period_id' => 6, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 472.01, 
                'until' => 895.24, 
                'base' => 472, 
                'fixed_fee' => 17.67, 
                'employee_percentage' => 10, 
                'employer_value' => 0, 
                'payment_period_id' => 6, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 895.25, 
                'until' => 2038.10, 
                'base' => 895.24, 
                'fixed_fee' => 60, 
                'employee_percentage' => 20, 
                'employer_value' => 0, 
                'payment_period_id' => 6, 
                'institution_law_id' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            LawDiscount::firstOrCreate([
                'from'=> 2038.11, 
                'until' => 1000000000, 
                'base' => 2038.10, 
                'fixed_fee' => 288.57, 
                'employee_percentage' => 30, 
                'employer_value' => 0, 
                'payment_period_id' => 6, 
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
            ['name' => 'planilla.approve'],
            ['description' => 'Aprobar planilla', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'planilla.recalculate'],
            ['description' => 'Recalcular planilla', 'guard_name' => 'web', 'module_id' => $module->id]
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
