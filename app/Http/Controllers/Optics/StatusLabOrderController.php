<?php

namespace App\Http\Controllers\Optics;

use App\Module;
use App\Permission;
use App\Optics\StatusLabOrder;
use App\Optics\StatusLabOrderStep;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StatusLabOrderController extends Controller
{
    /**
     * Constructor.
     *
     * @param  \App\Utils\Util  $util
     * @return void
     */
    public function __construct(Util $util)
    {
        $this->util = $util;
        $this->module = 'Estados de las órdenes de laboratorio';
        $this->module_name = 'status_lab_order';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('status_lab_order.view') && !auth()->user()->can('status_lab_order.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $status_lab_order = StatusLabOrder::select(['code', 'name', 'color', 'status', 'id']);
            return Datatables::of($status_lab_order)
                ->addColumn(
                    'action',
                    '@can("status_lab_order.update")
                    <button data-href="{{ action(\'Optics\StatusLabOrderController@edit\', [$id]) }}" class="btn btn-xs btn-primary edit_status_lab_orders_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                    &nbsp;
                    @endcan
                    @can("status_lab_order.delete")
                    <button data-href="{{ action(\'Optics\StatusLabOrderController@destroy\', [$id]) }}" class="btn btn-xs btn-danger delete_status_lab_orders_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
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
                ->editColumn(
                    'color',
                    '<span class="dot" style="background-color: {{ $color }}"></span>'
                )
                ->removeColumn('id')
                ->rawColumns([2, 3, 4])
                ->make(false);
        }

        return view('optics.status_lab_order.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('status_lab_order.create')) {
            abort(403, 'Unauthorized action.');
        }

        $code = $this->util->generateStatusLabOrderCode();

        $status = StatusLabOrder::select(['name', 'id'])->get();

        $status_list = [];

        if (! empty($status)) {
            foreach ($status as $item) {
                $status_list[$item->id] = $item->name;
            }
        }

        return view('optics.status_lab_order.create')->with(compact('code', 'status_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('status_lab_order.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            # Store status lab order
            $input = $request->only([
                'code',
                'name',
                'descripction',
                'status',
                'color',
                'print_order',
                'transfer_sheet',
                'save_and_print'
            ]);

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            # Make is_default unique
            $input['is_default'] = $request->input('is_default', 0);

            if ($input['is_default'] == 1) {
                $status_list = StatusLabOrder::where('is_default', 1)->get();

                foreach ($status_list as $item) {
                    $item->is_default = 0;
                    $item->save();
                }
            }

            # Make second_time unique
            $input['second_time'] = $request->input('second_time', 0);

            if ($input['second_time'] == 1) {
                $status_list = StatusLabOrder::where('second_time', 1)->get();

                foreach ($status_list as $item) {
                    $item->second_time = 0;
                    $item->save();
                }
            }

            # Make material_download unique
            $input['material_download'] = $request->input('material_download', 0);

            if ($input['material_download'] == 1) {
                $status_list = StatusLabOrder::where('material_download', 1)->get();

                foreach ($status_list as $item) {
                    $item->material_download = 0;
                    $item->save();
                }
            }

            # Make print_order unique
            $input['print_order'] = $request->input('print_order', 0);

            if ($input['print_order'] == 1) {
                $status_list = StatusLabOrder::where('print_order', 1)->get();

                foreach ($status_list as $item) {
                    $item->print_order = 0;
                    $item->save();
                }
            }

            $status_lab_order = StatusLabOrder::create($input);

            # Store binnacle
            $user_id = $request->session()->get('user.id');

            $this->util->registerBinnacle($user_id, $this->module_name, 'create', $status_lab_order);

            # Store status lab order steps
            $steps = $request->input('steps[]');

            if (! empty($steps)) {
                foreach ($steps as $item) {
                    StatusLabOrderStep::create([
                        'status_id' => $status_lab_order->id,
                        'step_id' => $item
                    ]);
                }
            }

            # Create a new permission related to the created status lab order
            $module = Module::where('name', $this->module)->first();

            if (! empty($module)) {
                $permission = Permission::where('name', 'status_lab_order.' . $status_lab_order->id)->first();

                if (empty($permission)) {
                    $permission = Permission::create([
                        'name' => 'status_lab_order.' . $status_lab_order->id,
                        'description' => $status_lab_order->name,
                        'module_id' => $module->id,
                        'guard_name' => 'web'
                    ]);

                    # Store binnacle
                    $this->util->registerBinnacle($user_id, 'permission', 'create', $permission);
                }
            }

            DB::commit();

            $output = [
                'success' => true,
                'data' => $status_lab_order,
                'msg' => __("status_lab_order.added_success")
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StatusLabOrder  $statusLabOrder
     * @return \Illuminate\Http\Response
     */
    public function show(StatusLabOrder $statusLabOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StatusLabOrder  $statusLabOrder
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('status_lab_order.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $status_lab_order = StatusLabOrder::find($id);

            $status_list = StatusLabOrder::where('id', '!=', $id)
                ->pluck('name', 'id');

            $status_selected = StatusLabOrderStep::where('status_id', $id)
                ->pluck('step_id')
                ->toArray();

            return view('optics.status_lab_order.edit')->with(compact('status_lab_order', 'status_list', 'status_selected'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StatusLabOrder  $statusLabOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('status_lab_order.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                # Update status lab order
                $input = $request->only([
                    'code',
                    'name',
                    'descripction',
                    'status',
                    'color',
                    'print_order',
                    'transfer_sheet',
                    'save_and_print'
                ]);
                
                $status_lab_order = StatusLabOrder::findOrFail($id);

                # Clone record before action
                $status_lab_order_old = clone $status_lab_order;

                # Make is_default unique
                $input['is_default'] = $request->input('is_default', 0);

                if ($input['is_default'] == 1) {
                    $input['print_order'] = 0;
                    $input['transfer_sheet'] = 0;
                    $input['second_time'] = 0;
                    $input['material_download'] = 0;

                    $status_list = StatusLabOrder::where('is_default', 1)
                        ->where('id', '!=', $id)
                        ->get();

                    foreach ($status_list as $item) {
                        $item->is_default = 0;
                        $item->save();
                    }
                }

                # Make second_time unique
                $input['second_time'] = $request->input('second_time', 0);

                if ($input['second_time'] == 1) {
                    $input['is_default'] = 0;
                    $input['print_order'] = 0;
                    $input['transfer_sheet'] = 0;
                    $input['material_download'] = 0;

                    $status_list = StatusLabOrder::where('second_time', 1)
                        ->where('id', '!=', $id)
                        ->get();

                    foreach ($status_list as $item) {
                        $item->second_time = 0;
                        $item->save();
                    }
                }

                # Make material_download unique
                $input['material_download'] = $request->input('material_download', 0);

                if ($input['material_download'] == 1) {
                    $input['is_default'] = 0;
                    $input['print_order'] = 0;
                    $input['transfer_sheet'] = 0;
                    $input['second_time'] = 0;

                    $status_list = StatusLabOrder::where('material_download', 1)
                        ->where('id', '!=', $id)
                        ->get();

                    foreach ($status_list as $item) {
                        $item->material_download = 0;
                        $item->save();
                    }
                }

                # Make print_order unique
                $input['print_order'] = $request->input('print_order', 0);

                if ($input['print_order'] == 1) {
                    $input['is_default'] = 0;
                    $input['material_download'] = 0;
                    $input['transfer_sheet'] = 0;
                    $input['second_time'] = 0;

                    $status_list = StatusLabOrder::where('print_order', 1)
                        ->where('id', '!=', $id)
                        ->get();

                    foreach ($status_list as $item) {
                        $item->print_order = 0;
                        $item->save();
                    }
                }

                $status_lab_order->fill($input);
                $status_lab_order->save();

                # Store binnacle
                $user_id = $request->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'update', $status_lab_order_old, $status_lab_order);

                # Delete status lab order steps
                DB::table('status_lab_order_steps')->where('status_id', $status_lab_order->id)->delete();

                # Store status lab order steps
                $steps = $request->input('steps');

                if (! empty($steps)) {
                    foreach ($steps as $item) {
                        StatusLabOrderStep::create([
                            'status_id' => $status_lab_order->id,
                            'step_id' => $item
                        ]);
                    }
                }

                # Update permission
                $permission = Permission::where('name', 'status_lab_order.' . $status_lab_order->id)->first();

                if (! empty($permission)) {
                    # Clone record before action
                    $permission_old = clone $permission;

                    $permission->description = $status_lab_order->name;
                    $permission->save();

                    # Store binnacle
                    $this->util->registerBinnacle($user_id, $this->module_name, 'update', $permission_old, $permission);
                }

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('status_lab_order.updated_success')
                ];
                
            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
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
     * @param  \App\StatusLabOrder  $statusLabOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('status_lab_order.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $status_lab_order = StatusLabOrder::findOrFail($id);

                # Clone record before action
                $status_lab_order_old = clone $status_lab_order;

                $status_lab_order->delete();

                # Store binnacle
                $user_id = request()->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'delete', $status_lab_order_old);

                $output = [
                    'success' => true,
                    'msg' => __("status_lab_order.deleted_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
