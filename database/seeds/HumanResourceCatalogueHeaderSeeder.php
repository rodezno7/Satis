<?php

use Illuminate\Database\Seeder;
use App\HumanResourcesHeader;
use App\HumanResourcesData;
use App\Module;
use Spatie\Permission\Models\Permission;

class HumanResourceCatalogueHeaderSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        HumanResourcesHeader::firstOrCreate([
            'name' => 'Estado civil',
            'description' => 'Estado civil',
        ]);

        HumanResourcesHeader::firstOrCreate([
            'name' => 'Departamento',
            'description' => 'Departamento',
        ]);

        HumanResourcesHeader::firstOrCreate([
            'name' => 'Puesto',
            'description' => 'Puesto',
        ]);

        HumanResourcesHeader::firstOrCreate([
            'name' => 'AFPs',
            'description' => 'AFPs',
        ]);
        
        HumanResourcesHeader::firstOrCreate([
            'name' => 'Tipo de empleado',
            'description' => 'Tipo de empleado',
        ]);

        HumanResourcesHeader::firstOrCreate([
            'name' => 'Nacionalidad',
            'description' => 'Nacionalidad',
        ]);

        HumanResourcesHeader::firstOrCreate([
            'name' => 'Profesión u oficio',
            'description' => 'Profesión u oficio',
        ]);

        HumanResourcesHeader::firstOrCreate([
            'name' => 'Formas de pago',
            'description' => 'Formas de pago',
        ]);

        HumanResourcesHeader::firstOrCreate([
            'name' => 'Tipos de documento',
            'description' => 'Tipos de documento',
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Transferencia bancaria',
            'status' => 1,
            'human_resources_header_id' => 8
        ]);

        $module = Module::firstOrCreate(
            ['name' => 'Recursos humanos'],
            ['description' => 'Gestión de recursos humanos', 'status' => 1]
        );
        
        Permission::firstOrCreate(
            ['name' => 'rrhh_catalogues.view'],
            ['description' => 'Ver catálogos', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_catalogues.create'],
            ['description' => 'Crear catálogos', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_catalogues.update'],
            ['description' => 'Actualizar catálogos', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_catalogues.delete'],
            ['description' => 'Eliminar catálogos', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_overall_payroll.view'],
            ['description' => 'Ver nómina', 'guard_name' => 'web', 'module_id' => $module->id]
        );
      
        Permission::firstOrCreate(
            ['name' => 'rrhh_overall_payroll.create'],
            ['description' => 'Crear nómina', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_overall_payroll.update'],
            ['description' => 'Actualizar nómina', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_overall_payroll.delete'],
            ['description' => 'Eliminar nómina', 'guard_name' => 'web', 'module_id' => $module->id]
        );
        
    }
}
