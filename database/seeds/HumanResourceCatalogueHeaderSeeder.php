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

        //-------------------------------------------------------------
        HumanResourcesHeader::firstOrCreate([
            'name' => 'Estado civil',
            'description' => 'Estado civil',
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Casado/a',
            'status' => 1,
            'human_resources_header_id' => 1,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Soltero/a',
            'status' => 1,
            'human_resources_header_id' => 1,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Viudo/a',
            'status' => 1,
            'human_resources_header_id' => 1,
            'business_id' => 3
        ]);

        //-------------------------------------------------------------
        HumanResourcesHeader::firstOrCreate([
            'name' => 'Departamento',
            'description' => 'Departamento',
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Finanzas',
            'status' => 1,
            'human_resources_header_id' => 2,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Informatica',
            'status' => 1,
            'human_resources_header_id' => 2,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Contabilidad',
            'status' => 1,
            'human_resources_header_id' => 2,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Marketing',
            'status' => 1,
            'human_resources_header_id' => 2,
            'business_id' => 3
        ]);

        //-------------------------------------------------------------
        HumanResourcesHeader::firstOrCreate([
            'name' => 'Puesto',
            'description' => 'Puesto',
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Auxiliar contable',
            'status' => 1,
            'human_resources_header_id' => 3,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Contador',
            'status' => 1,
            'human_resources_header_id' => 3,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Programador',
            'status' => 1,
            'human_resources_header_id' => 3,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Secretaria',
            'status' => 1,
            'human_resources_header_id' => 3,
            'business_id' => 3
        ]);
        
        //-------------------------------------------------------------
        HumanResourcesHeader::firstOrCreate([
            'name' => 'AFPs',
            'description' => 'AFPs',
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Crecer',
            'status' => 1,
            'human_resources_header_id' => 4,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Confia',
            'status' => 1,
            'human_resources_header_id' => 4,
            'business_id' => 3
        ]);
        
        //-------------------------------------------------------------
        HumanResourcesHeader::firstOrCreate([
            'name' => 'Tipo de empleado',
            'description' => 'Tipo de empleado',
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Permanente',
            'status' => 1,
            'human_resources_header_id' => 5,
            'business_id' => 3
        ]);

        //-------------------------------------------------------------
        HumanResourcesHeader::firstOrCreate([
            'name' => 'Nacionalidad',
            'description' => 'Nacionalidad',
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Salvadoreño',
            'status' => 1,
            'human_resources_header_id' => 6,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Hondureño',
            'status' => 1,
            'human_resources_header_id' => 6,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Costarricense',
            'status' => 1,
            'human_resources_header_id' => 6,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Guatemalteco',
            'status' => 1,
            'human_resources_header_id' => 6,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Panameño',
            'status' => 1,
            'human_resources_header_id' => 6,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Cubano',
            'status' => 1,
            'human_resources_header_id' => 6,
            'business_id' => 3
        ]);

        //-------------------------------------------------------------
        HumanResourcesHeader::firstOrCreate([
            'name' => 'Profesión u oficio',
            'description' => 'Profesión u oficio',
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Licenciado',
            'status' => 1,
            'human_resources_header_id' => 7,
            'business_id' => 3
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Ingeniero',
            'status' => 1,
            'human_resources_header_id' => 7,
            'business_id' => 3
        ]);

        //-------------------------------------------------------------
        HumanResourcesHeader::firstOrCreate([
            'name' => 'Formas de pago',
            'description' => 'Formas de pago',
        ]);

        HumanResourcesData::firstOrCreate([
            'value' => 'Transferencia bancaria',
            'status' => 1,
            'human_resources_header_id' => 8,
            'business_id' => 3
        ]);
        
        //-------------------------------------------------------------
        HumanResourcesHeader::firstOrCreate([
            'name' => 'Tipos de documento',
            'description' => 'Tipos de documento',
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
