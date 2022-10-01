<?php

namespace App\Http\Controllers;

use App\Cashier;
use App\BusinessLocation;
use App\Module;
use App\Utils\Util;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class CashierController extends Controller
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
        if (!auth()->user()->can('cashier.view') && !auth()->user()->can('cashier.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $cashier = Cashier::join('business_locations as bl', 'cashiers.business_location_id', 'bl.id')
                    ->select(['cashiers.code', 'cashiers.name', 'bl.name as blname', 'cashiers.status', 'cashiers.is_active', 'cashiers.id']);
            return Datatables::of($cashier)
                ->addColumn(
                    'action',
                    '@can("cashier.update")
                    <button data-href="{{ action(\'CashierController@edit\', [$id]) }}" class="btn btn-xs btn-primary edit_cashiers_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                    &nbsp;
                    @endcan
                    @can("cashier.delete")
                    <button data-href="{{ action(\'CashierController@destroy\', [$id]) }}" class="btn btn-xs btn-danger delete_cashiers_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )->editColumn('status', '{{ __("cashier." . $status) }}')
                ->editColumn('is_active', function($row){
                    if($row->is_active){
                        return __('messages.yes');
                    } else {
                        return __('messages.no');
                    }
                })
                ->removeColumn('id')
                ->rawColumns([3,4,5])
                ->make(false);
        }

        return view('cashier.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('cashier.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $code = $this->util->generateCashierCode();

        return view('cashier.create')
            ->with(compact('business_locations', 'code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('cashier.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['code', 'name', 'business_location_id', 'is_active']);
            $input['business_id'] = request()->session()->get('user.business_id');
            $input['status'] = 'close';

            $cashiers = Cashier::create($input);


            //Create a new permission related to the created cashier
            if (Module::where('name', 'Cajas')->first())
            {
                $module = Module::where('name', 'Cajas')->first();
                Permission::create([
                    'name' => 'cashier.' . $cashiers->id,
                    'description' => 'Caja ' . $cashiers->name,
                    'module_id' => $module->id,
                    ]);
            }
    
            $output = ['success' => true,
                            'data' => $cashiers,
                            'msg' => __("cashier.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cashier  $cashier
     * @return \Illuminate\Http\Response
     */
    public function show(Cashier $cashier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cashier  $cashier
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('cashier.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $business_locations = BusinessLocation::forDropdown($business_id);
            $cashier = Cashier::where('business_id', $business_id)->find($id);

            return view('cashier.edit')
                ->with(compact('business_locations', 'cashier'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cashier  $cashier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('cashier.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['code', 'name', 'business_location_id', 'is_active']);
                $business_id = $request->session()->get('user.business_id');

                $cashier = Cashier::where('business_id', $business_id)->findOrFail($id);
                $cashier->fill($input);
                $cashier->save();

                /** Check for cashier permission */
                $permission = Permission::where('name', 'cashier.' . $cashier->id)->first();
                if(!$permission){
                    $module = Module::where('name', 'Cajas')->first();

                    Permission::create([
                        'name' => 'cashier.' . $cashier->id,
                        'description' => 'Caja ' . $cashier->name,
                        'module_id' => $module->id
                    ]);
                }

                $output = ['success' => true,
                            'msg' => __("cashier.updated_success")
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Cashier  $cashier
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('cashier.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $cashier = Cashier::where('business_id', $business_id)->findOrFail($id);
                $cashier->delete();

                $output = ['success' => true,
                            'msg' => __("cashier.deleted_success")
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
}
