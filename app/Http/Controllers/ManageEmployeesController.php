<?php

namespace App\Http\Controllers;

use App\User;
use App\System;
use App\Employees;
use App\Positions;
use App\BusinessLocation;
use App\Notifications\NewNotification;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;


use Illuminate\Support\Facades\DB;
use App\Utils\ModuleUtil;
use App\Carbon;

class ManageEmployeesController extends Controller
{
    
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }
    
    // Mostrar vista para listar los empleados
    public function index(){
        if(!auth()->user()->can('employees.view') && !auth()->user()->can('employees.create')){
            abort(403, "Unauthorized action.");
        }
        $business_id = request()->session()->get('user.business_id');
        return view('manage_employees.index');
    }

    //Mostrar Lista de Empleados
    public function getEmployeesData(){

        if(!auth()->user()->can('employees.view') && !auth()->user()->can('employees.create')){
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');
        $employees = DB::table('employees')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->leftJoin('business_locations', 'employees.location_id', 'business_locations.id')
            ->select(
                'employees.id',
                DB::raw("CONCAT(COALESCE(employees.first_name, ''), ' ', COALESCE(employees.last_name, '')) as full_name"),
                'employees.email',
                'business_locations.name as location_name',
                'positions.name as position',
                'employees.hired_date',
                'employees.agent_code'
            )->whereNull('employees.deleted_at')
            ->where('employees.business_id', $business_id);
            
            return DataTables::of($employees)
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(employees.first_name, ''), ' ', COALESCE(employees.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->addColumn(
                    'action',
                    '@can("employees.update")
                    <button data-href="{{action(\'ManageEmployeesController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_employees_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("employees.delete")
                        <button data-href="{{action(\'ManageEmployeesController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_employees_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->rawColumns(['action'])
                ->toJson();
    }

    //Mostrar el formulario para registrar un nuevo Empleado
    public function create(){
        if(!auth()->user()->can('employees.create')){
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // LLenar Select de Posiciones
        $positions = Positions::forDropdown($business_id);

        // LLenar Select de Roles
        $roles = DB::table('roles')
        ->select(DB::raw("left(roles.name,LOCATE('#', roles.name) - 1) as rol, id"))
        ->where('business_id', $business_id)
        ->orderBy('roles.name', 'asc')
        ->pluck('rol', 'id');
        
        $locations = BusinessLocation::select("name", "id")
            ->pluck("name", "id");

        return view('manage_employees.create')
                    ->with(compact('positions', 'roles', 'locations'));
    }

    //Registrar el nuevo empleado en la BD
    public function store(Request $request){
        if (!auth()->user()->can('employees.create')){
            abort(403, 'Unauthorized action.');
        }


            $hasUser_mode = $request->input('chk_has_user');
            $commss_opt = $request->input('commission');

            $outpout = "";

            if($hasUser_mode == 'has_user'){
                $password_mode = $request->input('rdb_pass_mode');

                $pass = $request->input('password');
               
                try{
                    $password_mode = $request->input('rdb_pass_mode');
                    $user_details = $request->only(['first_name', 'last_name', 'username', 'email', 'location_id', 'password']);
                    $user_details['business_id'] = $request->session()->get('user.business_id');
                    $user_details['status'] = 'pending';
                    $user_details['language'] = 'es';

                    if($password_mode == 'generated'){
                        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                        $password = "";
                        for($i = 0; $i < 9; $i ++){
                            $password .= substr($str, rand(0,61), 1);
                        }
                        $user_details['password'] = bcrypt($password);
                    }else{
                        $password = $request->input('password');
                        $user_details['password'] = bcrypt($user_details['password']);
                    }

                    $ref_count = $this->moduleUtil->setAndGetReferenceCount('username');

                    if(blank($user_details['username'])){
                        $user_details['username'] = $this->moduleUtil->generateReferenceNumber('username', $ref_count);
                    }

                    $username_ext = $this->getUsernameExtension();
                    if(!empty($username_ext)){
                        $user_details['username'] .= $username_ext;
                    }

                    if($commss_opt == 'has_commission'){
                        $user_details['is_cmmsn_agnt'] = 1;
                        $user_details['cmmsn_percent'] = $request->input('commision_amount');
                    }else{
                        $user_details['is_cmmsn_agnt'] = 0;
                    }

                    $user_details = User::create($user_details);
                    $user_id = $user_details->id;
                    $role_id = $request->input('role');
                    $role = Role::findOrFail($role_id);
                    $user_details->assignRole($role->name);
                    $user_details->notify(new NewNotification($password));

                    //Crear el empleado
                    $employees = $request->only(['first_name', 'last_name', 'email', 'mobile', 'location_id', 'position_id','hired_date', 'fired_date', 'birth_date', 'short_name']);
                    $employees['business_id'] = $request->session()->get('user.business_id');
                    $employees['hired_date'] = $this->moduleUtil->uf_date($employees['hired_date']);
                    $employees['birth_date'] = $this->moduleUtil->uf_date($employees['birth_date']);
                    $employees['fired_date'] = $this->moduleUtil->uf_date($employees['fired_date']);
                    $employees['created_by'] = $request->session()->get('user.id');
                    $employees['user_id'] = $user_id;

                    if($commss_opt == 'has_commission'){
                        $str = "1234567890";
                        $agent_code = "";
                        for($i = 0; $i < 4; $i ++){
                            $agent_code .= substr($str, rand(0,10), 1);
                        }
                        $employees['agent_code'] = $agent_code;
                    }else{
                        $employees['agent_code'] = 0;
                    }


                    // return var_dump($employees);

                    $employees = Employees::create($employees);
                    
                    $outpout = ['success' => true,
                    'data' => $employees,
                    'msg' => __("employees.added_success")];

                }catch(\Exception $e)
                {
                    \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                    $outpout = ['error' => false,
                    'msg' => __("messages.something_went_wrong")];
                }
            }else{
                
                try{
                    $employees = $request->only(['first_name', 'last_name', 'email', 'mobile', 'location_id', 'position_id','hired_date', 'birth_date', 'fired_date', 'short_name']);
                    $employees['business_id'] = $request->session()->get('user.business_id');
                    $employees['created_by'] = $request->session()->get('user.id');
                    $employees['hired_date'] = $this->moduleUtil->uf_date($employees['hired_date']);
                    $employees['birth_date'] = $this->moduleUtil->uf_date($employees['birth_date']);
                    $employees['fired_date'] = $this->moduleUtil->uf_date($employees['fired_date']);
                    // \Carbon::createFromFormat( 'Y-m-d H:i:s', $request->input('hired_date'));

                    // return var_dump($employees);

                    $employees = Employees::create($employees);
                    
                    $outpout = ['success' => true,
                    'data' => $employees,
                    'msg' => __("employees.added_success")];
                }catch(\Exception $e)
                {
                    \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                    $outpout = ['success' => false,
                    'msg' => __("messages.something_went_wrong")];
                }
            }

        return $outpout;

    }

    //Mostrar el formulario para editar un Empleado
    public function edit($id){

        //Verificar si tiene permisos el usuario
        if(!auth()->user()->can('employees.update')){
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            $business_id = request()->session()->get('user.business_id');
            $employees = Employees::where('business_id', $business_id)->find($id);
            // LLenar Select de Posiciones
            $positions = Positions::forDropdown($business_id);

            $locations = BusinessLocation::select("name", "id")
                ->pluck("name", "id");

            return view('manage_employees.edit')
            ->with(compact('employees', 'positions', 'locations'));
        }

    }

    //Actualizar un empleado en la BD
    public function update(Request $request, $id){
        if (!auth()->user()->can('employees.update')) {
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            
            try{
                $input = $request->only(['first_name', 'last_name', 'email', 'mobile', 'location_id', 'position_id','hired_date', 'birth_date', 'fired_date', 'short_name', 'agent_code']);
                $business_id = $request->session()->get('user.business_id');

                $employees_edt = Employees::where('business_id', $business_id)->findOrFail($id);
                $employees_edt->first_name = $input['first_name'];
                $employees_edt->last_name = $input['last_name'];
                $employees_edt->email = $input['email'];
                $employees_edt->mobile = $input['mobile'];
                $employees_edt->location_id = $input['location_id'];
                $employees_edt->position_id = $input['position_id'];
                $employees_edt->hired_date = $this->moduleUtil->uf_date($input['hired_date']);
                $employees_edt->birth_date = $this->moduleUtil->uf_date($input['birth_date']);
                $employees_edt->fired_date = $this->moduleUtil->uf_date($input['fired_date']);
                $employees_edt->short_name = $input['short_name'];
                $employees_edt->agent_code = $input['agent_code'];
                $employees_edt->save();

                $outpout = ['success' => true,
                'data' => $employees_edt,
                'msg' => __("employees.updated_success")];

            }catch(\Exception $e){
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
    
                $outpout = ['success' => false,
                'msg' => __("messages.something_went_wrong")];
            }
            return $outpout;
        }
    }

    public function destroy($id){
        if (!auth()->user()->can('employees.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            try{
                $business_id = request()->session()->get('user.business_id');
                $employees_dstry = Employees::where('business_id', $business_id)->find($id);

                $employees_dstry->delete();
                $outpout = ['success' => true,
                'data' => $employees_dstry,
                'msg' => __("employees.deleted_success")];
            }catch(\Exception $e){
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
    
                $outpout = ['success' => false,
                'msg' => __("messages.something_went_wrong")];
            }
            return $outpout;
        }
    }

    private function getUsernameExtension()
    {
        $extension = !empty(System::getProperty('enable_business_based_username')) ? '-' .str_pad(session()->get('business.id'), 2, 0, STR_PAD_LEFT) : null;
        return $extension;
    }

    /**
     * Check if the agent code exists.
     * 
     * @return array
     */
    public function verifiedIfExistsAgentCode()
    {
        if (request()->ajax()) {
            $business_id = auth()->user()->business_id;

            if (request()->agent_code && request()->employee_id > 0) {
                $agent_code = Employees::where('id', '<>', request()->employee_id)
                    ->where('business_id', $business_id)
                    ->where('agent_code', request()->agent_code)
                    ->exists();

                if ($agent_code) {
                    $output = [
                        'success' => false,
                        'msg' => __('employees.validate_agent_code_error')
                    ];

                } else {
                    $output = [
                        'success' => true,
                        'msg' => __('employees.validate_agent_code_success')
                    ];
                }

            } else if (request()->agent_code) {
                $agent_code = Employees::where('business_id', $business_id)
                    ->where('agent_code', request()->agent_code)
                    ->exists();

                if ($agent_code) {
                    $output = [
                        'success' => false,
                        'msg' => __('employees.validate_agent_code_error')
                    ];

                } else {
                    $output = [
                        'success' => true,
                        'msg' => __('employees.validate_agent_code_success')
                    ];
                }
            }

            return $output;
        }
    }
}
