<?php

namespace App\Http\Controllers;

use App\Module;
use App\Permission;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Validator;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('module.view')) {
            abort(403, 'Unauthorized action.');
        }
        return view('modules.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('module.create')) {
            abort(403, 'Unauthorized action.');
        }
        return view('modules.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('module.create')) {
            abort(403, 'Unauthorized action.');
        }
        try{
            $module = Module::create($request->all());
            $output = [
                'success' => true,
                'msg' => __("role.module_added")
            ];
        }
        catch (\Exception $e){
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function show(Module $module)
    {
        if (!auth()->user()->can('module.view')) {
            abort(403, 'Unauthorized action.');
        }
        return response()->json($module);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function edit(Module $module)
    {
        if (!auth()->user()->can('module.update')) {
            abort(403, 'Unauthorized action.');
        }
        return response()->json($module);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Module $module)
    {
        if (!auth()->user()->can('module.update')) {
            abort(403, 'Unauthorized action.');
        }
        try{
            $module->name = $request->input('name');
            $module->description = $request->input('description');
            $module->status = $request->input('status');
            $module->save();
            $output = [
                'success' => true,
                'msg' => __("role.module_updated")
            ];
        }
        catch (\Exception $e){
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function destroy(Module $module)
    {
        if (!auth()->user()->can('module.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try{
            $count = Permission::where('module_id', $module->id)->count();
            if($count > 0){
                $output = [
                    'success' => false,
                    'msg' => __("role.module_has_permissions")
                ];
            }
            else{
                $module->delete();
                $output = [
                    'success' => true,
                    'msg' => __("role.module_deleted")
                ];
            }
        }
        catch (\Exception $e){
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return $output;
    }

    public function getModulesData()
    {
        $modules = DB::table('modules as module')
            ->whereNull('module.deleted_at')
            ->select('module.*');

        return DataTables::of($modules)->toJson();
    }

    public function getModules()
    {
        $modules = Module::select('id', 'name')->get();
        return response()->json($modules);
    }
}
