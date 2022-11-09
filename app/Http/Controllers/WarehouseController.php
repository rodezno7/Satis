<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Module;
use App\Warehouse;
use App\Permission;
use App\Catalogue;
use App\Utils\Util;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller
{
    public function __construct(Util $util)
    {
        $this->util = $util;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('warehouse.view') && !auth()->user()->can('warehouse.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $business_id = auth()->user()->business_id;

            $warehouses = Warehouse::leftJoin('business_locations as bl', 'warehouses.business_location_id', 'bl.id')
                ->where('warehouses.business_id', $business_id)
                ->select(
                    'warehouses.code',
                    'warehouses.name',
                    'bl.name as blname',
                    'warehouses.location',
                    'warehouses.status',
                    'warehouses.id'
                );

            return Datatables::of($warehouses)
                ->addColumn(
                    'action',
                    '@can("warehouse.update")
                    <button data-href="{{ action(\'WarehouseController@edit\', [$id]) }}" class="btn btn-xs btn-primary edit_warehouses_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                    &nbsp;
                    @endcan
                    @can("warehouse.delete")
                    <button data-href="{{ action(\'WarehouseController@destroy\', [$id]) }}" class="btn btn-xs btn-danger delete_warehouses_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->editColumn(
                    'status',
                    '@if($status == "active")
                    <span class="badge" style="background-color: #5cb85c;">{{ __("cashier.".$status) }}</span>
                    @else
                    <span class="badge" style="background-color: #d9534f;">{{ __("cashier.".$status) }}</span>
                    @endif'
                    )
                ->removeColumn('id')
                ->rawColumns([4, 5])
                ->make(false);
        }

        return view('warehouse.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('warehouse.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $code = $this->util->generateWarehouseCode();

        return view('warehouse.create')->with(compact('business_locations', 'code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('warehouse.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['code', 'name', 'location', 'business_location_id', 'catalogue_id', 'description', 'status']);

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $warehouse = Warehouse::create($input);

            //Create a new permission related to the created warehouse
            if (Module::where('name', 'Bodegas')->first())
            {
                $module = Module::where('name', 'Bodegas')->first();
                $permission = Permission::where('name', 'warehouse.' . $warehouse->id)->select('name')->first();
                if (empty($permission)) {
                    Permission::create([
                        'name' => 'warehouse.' . $warehouse->id,
                        'description' => 'Bodega ' . $warehouse->name,
                        'guard_name' => 'web',
                        'module_id' => $module->id,
                    ]);
                }
            }

            $output = [
                'success' => true,
                'data' => $warehouse,
                'msg' => __("warehouse.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

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
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('warehouse.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $business_locations = BusinessLocation::forDropdown($business_id);

            $warehouse = Warehouse::find($id);

            $catalogue = null;
            if($warehouse->catalogue_id){
                $catalogue = Catalogue::find($warehouse->catalogue_id);
            }

            return view('warehouse.edit')->with(compact('business_locations', 'warehouse', 'catalogue'));
        }
    }

    public function getWarehouseByLocation($id){
        $business_id = request()->session()->get('user.business_id');
        $warehouses = Warehouse::where('business_id', $business_id)
        ->where('business_location_id', $id)
        ->where('status', 'active')
        ->get();
        return response()->json($warehouses);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('warehouse.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only([
                    'code',
                    'name',
                    'location',
                    'business_location_id',
                    'catalogue_id',
                    'description',
                    'status'
                ]);

                $warehouse = Warehouse::findOrFail($id);
                $warehouse->fill($input);
                $warehouse->save();

                // Create or update permission related to the warehouse
                $module = Module::where('name', 'Bodegas')->first();

                if (! empty($module)) {
                    $permission = Permission::where('name', 'warehouse.' . $warehouse->id)->first();

                    if (empty($permission)) {
                        Permission::create([
                            'name' => 'warehouse.' . $warehouse->id,
                            'description' => 'Bodega ' . $warehouse->name,
                            'guard_name' => 'web',
                            'module_id' => $module->id,
                        ]);

                    } else {
                        $permission->description = 'Bodega ' . $warehouse->name;
                        $permission->save();
                    }
                }

                $output = [
                    'success' => true,
                    'msg' => __("warehouse.updated_success")
                ];

            } catch (\Exception $e) {
                \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('warehouse.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $warehouse = Warehouse::findOrFail($id);
                $warehouse->delete();

                $output = ['success' => true,
                    'msg' => __("warehouse.deleted_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Return location_id from a warehouse
     * @param int $warehouse_id
     * @return int
     */
    public function getLocation($warehouse_id){
        if(empty($warehouse_id)){
            return null;
        }

        $location_id = null;
        $location = Warehouse::find($warehouse_id);

        if(!empty($location)){
            $location_id = $location->business_location_id;
        } else{
            return null;
        }

        return $location_id;
    }

    /**
     * Create or update permission related to the warehouse.
     * 
     * @return string
     */
    public function createPermissions()
    {
        try {
            DB::beginTransaction();

            $module = Module::where('name', 'Bodegas')->first();

            if (! empty($module)) {
                Permission::create([
                    'name' => 'warehouse.view',
                    'description' => 'Ver bodegas',
                    'guard_name' => 'web',
                    'module_id' => $module->id,
                ]);
    
                Permission::create([
                    'name' => 'warehouse.create',
                    'description' => 'Crear bodegas',
                    'guard_name' => 'web',
                    'module_id' => $module->id,
                ]);
    
                Permission::create([
                    'name' => 'warehouse.update',
                    'description' => 'Actualizar bodegas',
                    'guard_name' => 'web',
                    'module_id' => $module->id,
                ]);
    
                Permission::create([
                    'name' => 'warehouse.delete',
                    'description' => 'Eliminar bodegas',
                    'guard_name' => 'web',
                    'module_id' => $module->id,
                ]);

                $warehouses = Warehouse::all();

                foreach ($warehouses as $warehouse) {
                    $permission = Permission::where('name', 'warehouse.' . $warehouse->id)->first();

                    if (empty($permission)) {
                        Permission::create([
                            'name' => 'warehouse.' . $warehouse->id,
                            'description' => 'Bodega ' . $warehouse->name,
                            'guard_name' => 'web',
                            'module_id' => $module->id,
                        ]);

                    } else {
                        $permission->description = 'Bodega ' . $warehouse->name;
                        $permission->save();
                    }
                }
            }

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }
}
