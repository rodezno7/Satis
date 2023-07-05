<?php

use Illuminate\Database\Seeder;
use App\RrhhHeader;
use App\RrhhData;
use App\Module;
use App\Business;
use Spatie\Permission\Models\Permission;

class RrhhCatalogueHeaderSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $business = Business::all();

        $headers = ['Estado civil', 'Departamentos de empresa', 'Puesto de trabajo', 'AFPs', 'Tipo de empleado', 'Nacionalidad', 'Profesión u oficio', 'Formas de pago', 'Tipos de documento', 'Capacidades especiales', 'Clasificacion de empleados', 'Tipos de estudios', 'Tipos de ausencias', 'Tipos de incapacidades', 'Tipos de parentescos', 'Tipos de ingresos y descuentos'];
        $estadoCiviles = ['Casado(a)', 'Soltero(a)', 'Viudo(a)', 'Acompañado(a)'];
        $departamentosEmpresa = ['Finanzas', 'Informática', 'Contabilidad', 'Marketing'];
        $puestosTrabajo = ['Auxiliar contable', 'Contador', 'Programador', 'Secretaria'];
        $afps = ['Confía', 'Crecer'];
        $tiposEmpleado = ['Permanente'];
        $nacionalidades = ['Salvadoreño', 'Guatemalteco', 'Hondureño', 'Costarricense', 'Panameño'];
        $profesiones = ['Ingeniero', 'Licenciado'];
        $formasPago = ['Transferencia bancaria'];
        $tiposDocumento = ['DUI', 'NIT'];
        $capacidadesEspeciales = ['Perdida de la vista en un ojo', 'Perdida de una mano', 'Perdida de un pie', 'Perdida de audicion en un oído'];
        $clasificacionesEmpleado = ['Profesional', 'Técnico'];
        $tiposEstudio = ['Maestría', 'Universitario', 'Técnico', 'Secundaria', 'Primaria', 'Curso', 'Diplomado', 'Seminario'];
        $tiposAusencias = ['Permiso personal', 'Permiso familiar', 'Asistencia al ISSS', 'Falta', 'Incapacidad matenidad', 'Incapacidad extensión', 'Diligencia'];
        $tiposIncapacidades = ['Enfermedad o Accidente común', 'Accidente de trabajo o Enfermedad profesional', 'Incapacidad total'];
        $tiposParentescos = ['Madre', 'Padre', 'Compañero(a) de vida', 'Hijo(a)', 'Hermano(a)', 'Abuelo(a)', 'Tío(a)', 'Cuñado(a)', 'Nieto(a)', 'Sobrino(a)', 'Primo(a)'];

        foreach($headers as $header){
            RrhhHeader::firstOrCreate([
                'name' => $header,
                'description' => $header,
            ]);
        }

        $headersAll = RrhhHeader::all();
        foreach ($business as $item) {
            foreach($headersAll as $header){

                if($header->id == 1){
                    foreach($estadoCiviles as $estadoCivil){
                        RrhhData::firstOrCreate([
                            'value' => $estadoCivil,
                            'status' => 1,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 2){
                    foreach($departamentosEmpresa as $departamentoEmpresa){
                        RrhhData::firstOrCreate([
                            'value' => $departamentoEmpresa,
                            'status' => 1,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 3){
                    foreach($puestosTrabajo as $puestoTrabajo){
                        RrhhData::firstOrCreate([
                            'value' => $puestoTrabajo,
                            'status' => 1,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 4){
                    foreach($afps as $afp){
                        RrhhData::firstOrCreate([
                            'value' => $afp,
                            'status' => 1,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 5){
                    foreach($tiposEmpleado as $tipoEmpleado){
                        RrhhData::firstOrCreate([
                            'value' => $tipoEmpleado,
                            'status' => 1,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 6){
                    foreach($nacionalidades as $nacionalidad){
                        RrhhData::firstOrCreate([
                            'value' => $nacionalidad,
                            'status' => 1,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 7){
                    foreach($profesiones as $profesion){
                        RrhhData::firstOrCreate([
                            'value' => $profesion,
                            'status' => 1,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 8){
                    foreach($formasPago as $formaPago){
                        RrhhData::firstOrCreate([
                            'value' => $formaPago,
                            'status' => 1,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 9){
                    foreach($tiposDocumento as $tipoDocumento){
                        RrhhData::firstOrCreate([
                            'value' => $tipoDocumento,
                            'status' => 1,
                            'date_required' => true,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }   
                
                if($header->id == 10){
                    foreach($capacidadesEspeciales as $capacidadEspecial){
                        RrhhData::firstOrCreate([
                            'value' => $capacidadEspecial,
                            'status' => 1,
                            'date_required' => true,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 11){
                    foreach($clasificacionesEmpleado as $clasificacionEmpleado){
                        RrhhData::firstOrCreate([
                            'value' => $clasificacionEmpleado,
                            'status' => 1,
                            'date_required' => true,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 12){
                    foreach($tiposEstudio as $tipoEstudio){
                        RrhhData::firstOrCreate([
                            'value' => $tipoEstudio,
                            'status' => 1,
                            'date_required' => true,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 13){
                    foreach($tiposAusencias as $tipoAusencia){
                        RrhhData::firstOrCreate([
                            'value' => $tipoAusencia,
                            'status' => 1,
                            'date_required' => true,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 14){
                    foreach($tiposIncapacidades as $tipoIncapacidad){
                        RrhhData::firstOrCreate([
                            'value' => $tipoIncapacidad,
                            'status' => 1,
                            'date_required' => true,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }

                if($header->id == 15){
                    foreach($tiposParentescos as $tipoParentesco){
                        RrhhData::firstOrCreate([
                            'value' => $tipoParentesco,
                            'status' => 1,
                            'date_required' => true,
                            'rrhh_header_id' => $header->id,
                            'business_id' => $item->id
                        ]);
                    }
                }
            }
        }
        
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
            ['name' => 'rrhh_employees.view'],
            ['description' => 'Ver nómina', 'guard_name' => 'web', 'module_id' => $module->id]
        );
      
        Permission::firstOrCreate(
            ['name' => 'rrhh_employees.create'],
            ['description' => 'Crear nómina', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_employees.update'],
            ['description' => 'Actualizar nómina', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_employees.delete'],
            ['description' => 'Eliminar nómina', 'guard_name' => 'web', 'module_id' => $module->id]
        );


        Permission::firstOrCreate(
            ['name' => 'rrhh_document_employee.view'],
            ['description' => 'Ver documento del empleado', 'guard_name' => 'web', 'module_id' => $module->id]
        );
      
        Permission::firstOrCreate(
            ['name' => 'rrhh_document_employee.create'],
            ['description' => 'Crear documento del empleado', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_document_employee.update'],
            ['description' => 'Actualizar documento del empleado', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_document_employee.delete'],
            ['description' => 'Eliminar documento del empleado', 'guard_name' => 'web', 'module_id' => $module->id]
        );

      
        Permission::firstOrCreate(
            ['name' => 'rrhh_economic_dependence.create'],
            ['description' => 'Crear dependencia económica del empleado', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_economic_dependence.update'],
            ['description' => 'Actualizar dependencia económica del empleado', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_economic_dependence.delete'],
            ['description' => 'Eliminar dependencia económica del empleado', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_personnel_action.view'],
            ['description' => 'Ver acción de personal', 'guard_name' => 'web', 'module_id' => $module->id]
        );
      
        Permission::firstOrCreate(
            ['name' => 'rrhh_personnel_action.create'],
            ['description' => 'Crear acción de personal', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_personnel_action.update'],
            ['description' => 'Actualizar acción de personal', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_personnel_action.delete'],
            ['description' => 'Eliminar acción de personal', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_personnel_action.authorize'],
            ['description' => 'Autorizar acción de personal', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_absence_inability.view'],
            ['description' => 'Ver ausencia o incapacidad', 'guard_name' => 'web', 'module_id' => $module->id]
        );
      
        Permission::firstOrCreate(
            ['name' => 'rrhh_absence_inability.create'],
            ['description' => 'Crear ausencia o incapacidad', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_absence_inability.update'],
            ['description' => 'Actualizar ausencia o incapacidad', 'guard_name' => 'web', 'module_id' => $module->id]
        );

        Permission::firstOrCreate(
            ['name' => 'rrhh_absence_inability.delete'],
            ['description' => 'Eliminar ausencia o incapacidad', 'guard_name' => 'web', 'module_id' => $module->id]
        );
        
    }
}
