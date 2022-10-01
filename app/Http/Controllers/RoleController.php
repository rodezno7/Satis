<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\BusinessLocation;
use App\SellingPriceGroup;
use App\Module;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Utils\ModuleUtil;

class RoleController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('roles.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $locations = BusinessLocation::where('business_id', $business_id)
        ->get();
        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
        ->get();

        $modules = Module::select('id', 'name')->where('status', 1)->orderBy('name', 'asc')->get();
        $permissions = DB::table('permissions')
        ->select('id', 'name', 'description', 'module_id')
        ->where('permissions.deleted_at', NULL)
        ->get();

        $roles = DB::table('roles')
        ->select(DB::raw("left(roles.name,LOCATE('#', roles.name) - 1) as rol, id, is_default"))
        ->where('business_id', $business_id)
        ->orderBy('roles.name', 'asc')
        ->get();        

        $module_permissions = $this->moduleUtil->getModuleData('user_permissions');
        return view('role.index', compact('locations', 'selling_price_groups', 'module_permissions', 'modules', 'permissions', 'roles'));
    }

    public function getRolesData()
    {
        if (!auth()->user()->can('roles.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $roles = Role::where('business_id', $business_id)
        ->select('name', 'id', 'is_default', 'business_id')
        ->get();
        return DataTables::of($roles)
        ->addColumn('action', function ($row) {
            if (!$row->is_default || $row->name == "Cashier#" . $row->business_id) {
                $action = '';
                if (auth()->user()->can('roles.update')) {
                    $action .= '<a href="' . action('RoleController@edit', [$row->id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a>';
                }
                return $action;
            }
            else
            {
                return '';
            }
        })
        ->editColumn('name', function($row) use ($business_id){
            $role_name = str_replace('#'. $business_id, '', $row->name);
            if (in_array($role_name, ['Admin', 'Cashier'])) {
                $role_name = __('lang_v1.' . $role_name);
            }
            return $role_name;})
        ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('roles.create')) {
            abort(403, 'Unauthorized action.');
        }

        //Get all locations
        $business_id = request()->session()->get('user.business_id');
        $locations = BusinessLocation::where('business_id', $business_id)
        ->get();

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
        ->get();

        $module_permissions = $this->moduleUtil->getModuleData('user_permissions');

        return view('role.create')
        ->with(compact('locations', 'selling_price_groups', 'module_permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('roles.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = $request->session()->get('user.business_id');
        $role_name = $request->input('name');
        $permissions = $request->input('permissions');
        try
        {
            $count = Role::where('name', $role_name . '#' . $business_id)
            ->where('business_id', $business_id)
            ->count();
            if ($count == 0) {

                $is_service_staff = 0;
                if ($request->input('is_service_staff') == 1) {
                    $is_service_staff = 1;
                }

                $role = Role::create([
                    'name' => $role_name . '#' . $business_id ,
                    'business_id' => $business_id,
                    'is_service_staff' => $is_service_staff
                ]);

                //Include location permissions
                $location_permissions = $request->input('location_permissions');
                if (!empty($location_permissions)){
                    if (!in_array('access_all_locations', $permissions) &&
                        !empty($location_permissions)){
                        foreach ($location_permissions as $location_permission){
                            $permissions[] = $location_permission;
                        }
                    }
                }
                //Include selling price group permissions
                $spg_permissions = $request->input('spg_permissions');
                if(!empty($spg_permissions)) {
                    foreach ($spg_permissions as $spg_permission) {
                        $permissions[] = $spg_permission;
                    }
                }

                if(!empty($permissions)) {
                    $role->syncPermissions($permissions);
                }
                $output = [
                    'success' => 1,
                    'msg' => __("user.role_added")
                ];
            }
            else
            {
                $output = [
                    'success' => 0,
                    'msg' => __("user.role_already_exists")
                ];

            }

        }
        catch (\Exception $e)
        {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];

        }
        return redirect('roles')->with('status', $output);
    }

    public function verifyRoleName(Request $request, $name)
    {
        if($name == '')
        {
            $resultado = 'success';
            $message = 'Se puede guardar';
        }
        else
        {
            $business_id = $request->session()->get('user.business_id');
            $count = Role::where('name', $name . '#' . $business_id)
            ->where('business_id', $business_id)
            ->count();
            if ($count == 0)
            {
                $resultado = 'success';
                $message = 'Se puede guardar';
            }
            else
            {
                $resultado = 'error';
                $message = 'No se puede guardar';
            }

        }
        $datos = array(
            'result' => $resultado,
            'message' => $message,
        );
        return $datos;

    }
    public function verifyDelete($id)
    {
        $count = DB::table('model_has_roles')
        ->where('role_id', $id)
        ->count();
        if ($count == 0)
        {
            $result = "success";
        }
        else
        {
            $result = "error";
        }
        $datos = array(
            'result' => $result,
        );
        return $datos;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('roles.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $role = Role::where('business_id', $business_id)
        ->with(['permissions'])
        ->find($id);
        $role_permissions = [];
        foreach ($role->permissions as $role_perm) {
            $role_permissions[] = $role_perm->name;
        }
        $locations = BusinessLocation::where('business_id', $business_id)
        ->get();

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
        ->get();

        $module_permissions = $this->moduleUtil->getModuleData('user_permissions');

        $modules = Module::select('id', 'name')->where('status', 1)->orderBy('name', 'asc')->get();
        $permissions = DB::table('permissions')
        ->select('id', 'name', 'description', 'module_id')
        ->where('permissions.deleted_at', NULL)
        ->get();

        return view('role.edit')
        ->with(compact('role', 'role_permissions', 'locations', 'selling_price_groups', 'module_permissions', 'modules', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('roles.update'))
        {
            abort(403, 'Unauthorized action.');
        }      
        try
        {
            DB::beginTransaction();

            $role_name = $request->input('name');
            $permissions = $request->input('permissions');
            $business_id = $request->session()->get('user.business_id');
            $count = Role::where('name', $role_name . '#' . $business_id)
            ->where('id', '!=', $id)
            ->where('business_id', $business_id)
            ->count();
            if ($count == 0)
            {
                $role = Role::findOrFail($id);
                if (!$role->is_default || $role->name == 'Cashier#' . $business_id)
                {
                    if ($role->name == 'Cashier#' . $business_id)
                    {
                        $role->is_default = 0;
                    }
                    $is_service_staff = 0;
                    if ($request->input('is_service_staff') == 1)
                    {
                        $is_service_staff = 1;
                    }
                    $role->is_service_staff = $is_service_staff;
                    $role->name = $role_name . '#' . $business_id;
                    $role->save();
                    //Include location permissions
                    $location_permissions = $request->input('location_permissions');
                    if (!empty($location_permissions)){
                        if (!in_array('access_all_locations', $permissions) &&
                            !empty($location_permissions)) {
                            foreach ($location_permissions as $location_permission) {
                                $permissions[] = $location_permission;
                            }
                        }
                    }
                    //Include selling price group permissions
                    $spg_permissions = $request->input('spg_permissions');
                    if (!empty($spg_permissions)) {
                        foreach ($spg_permissions as $spg_permission) {
                            $permissions[] = $spg_permission;
                        }
                    }
                    if (!empty($permissions)) {
                        $role->syncPermissions($permissions);
                    }
                    $output = ['success' => 1,
                    'msg' => __("user.role_updated")];
                }
                else
                {
                    $output = ['success' => 0,
                    'msg' => __("user.role_is_default")];
                }
            }
            else
            {
                $output = ['success' => 0,
                'msg' => __("user.role_already_exists")];
            }

            DB::commit();

        } catch(\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => 0,
            'msg' => __("messages.something_went_wrong")];
        }
        return redirect('roles')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('roles.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if(request()->ajax())
        {
            try
            {
                $business_id = request()->user()->business_id;
                $role = Role::findOrFail($id);
                if(!$role->is_default || $role->name == 'Cashier#' . $business_id)
                {
                    $role->delete();
                    $output = [
                        'success' => true,
                        'msg' => __("user.role_deleted")
                    ];
                }
                else
                {
                    $output = [
                        'success' => 0,
                        'msg' => __("user.role_is_default")
                    ];
                }
            }
            catch(\Exception $e)
            {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    public function getPermissionsByRoles()
    {
        $business_id = request()->session()->get('user.business_id');

        $roles = DB::table('roles')
        ->select(DB::raw("left(roles.name,LOCATE('#', roles.name) - 1) as rol, id, is_default"))
        ->where('business_id', $business_id)
        ->orderBy('roles.name', 'asc')
        ->get();

        $module_excel = DB::table('modules')
        ->join('permissions', 'permissions.module_id', '=', 'modules.id')
        ->select('modules.name', 'permissions.description', 'permissions.id as permission_id')
        ->orderBy('modules.name', 'asc')
        ->where('modules.status', 1)
        ->where('modules.deleted_at', null)
        ->where('permissions.deleted_at', null)
        ->get();

        $lines = array();
        foreach ($module_excel as $module) {
            $item = array();
            $item["modulo"] = $module->name;
            $item["permiso"] = $module->description;
            foreach ($roles as $rol) {
                $count = DB::table('role_has_permissions')
                ->where('permission_id', $module->permission_id)
                ->where('role_id', $rol->id)
                ->count();
                if($count > 0){
                    $result = "S";
                }
                else{
                    $result = "N";
                }
                if($rol->is_default == 1){
                    $result = "S";
                }
                if(($rol->rol == 'Admin') || ($rol->rol == 'Cashier')){
                    $role_name = __('lang_v1.' . $rol->rol);
                }
                else{
                    $role_name = $rol->rol;
                }
                $item[$role_name] = $result;
            }            
            array_push($lines, $item);
        }

        $elements = json_decode(json_encode ($lines), FALSE);

        return DataTables::of($elements)->toJson();
    }
}
