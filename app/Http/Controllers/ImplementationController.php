<?php

namespace App\Http\Controllers;

use App\Business;
use App\Module;
use Illuminate\Http\Request;
use DB;

class ImplementationController extends Controller
{
    public function index() 
    {
        // if(!auth()->user()->can('business_settings.access_module')){
        //     abort(403, "Unauthorized action.");
        // }
        if(!auth()->user()->hasRole('Super Admin#' . request()->session()->get('user.business_id'))){
            abort(403, "Unauthorized action.");
        }

        $systemModules = Module::orderBy('name', 'ASC')->get();
        $avlble_modules = [];
        $enabled = [];
        foreach($systemModules as $systemModule){
            $avlble_modules[$systemModule->name] = ['name' => $systemModule->name, 'id' => $systemModule->id, 'description' => $systemModule->description, 'status' => $systemModule->status];
            if($systemModule->status == 1){
                $enabled[] = $systemModule->name;
            }
        }

        $modules = $avlble_modules;

        //dd($enabled);
        return view('implementations.index', compact('modules', 'enabled'));
    }


    public function store(Request $request){
        if(!auth()->user()->can('business_settings.access_module')){
            abort(403, "Unauthorized action.");
        }

        try {
            //dd($request->enabled_modules);
            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();

            //Enabled modules
            $enabled_modules = $request->input('enabled_modules');
            $business_details['enabled_modules'] = $enabled_modules;
            $business->fill($business_details);
            $business->save();

            $modules = Module::all();
            foreach($modules as $module){
                if(in_array($module->name, $enabled_modules)){
                    $module->status = 1;
                }else{
                    $module->status = 0;

                    $permissions = DB::table('role_has_permissions')
                        ->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
                        ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                        ->join('modules', 'modules.id', '=', 'permissions.module_id')
                        ->join('model_has_roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->join('users', 'users.id', '=', 'model_has_roles.model_id')
                        ->where('users.business_id', '=', $business_id)
                        ->where('modules.name', '=', $module->name)
                        ->where('modules.status', '=', 1)
                        ->delete();
                }
                $module->update();
            }

            DB::commit();
            
            $output = [
                'success' => 1,
                'msg' => __('business.settings_updated_module_success')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect('implementations')->with('status', $output);
    }
}
