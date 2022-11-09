<?php

namespace App\Http\Controllers;

use App\Unit;
use App\UnitGroup;
use App\UnitGroupLines;
use App\Product;
use App\Business;

use DB;
use App\Utils\ProductUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    private $productUtil;
    private $clone_product;

    public function __construct(ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;

        /** clone product config */
        $this->clone_product = config('app.clone_product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('unit.view') && !auth()->user()->can('unit.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $units = Unit::forDropdown($business_id);

        $conf_units = Business::select('enable_unit_groups')->where('id', $business_id)->first();
        $conf_units = $conf_units->enable_unit_groups;

        if (request()->ajax()) {

            $unit = Unit::where('business_id', $business_id)
            ->select(['actual_name', 'short_name', 'allow_decimal', 'id']);

            return Datatables::of($unit)
            ->addColumn(
                'action',
                '@can("unit.update")
                <button id="edit_unit" data-href="{{action(\'UnitController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_unit_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                &nbsp;
                @endcan
                @can("unit.delete")
                <button id="delete_unit" data-href="{{action(\'UnitController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_unit_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                @endcan'
            )
            ->editColumn('allow_decimal', function ($row) {
                if ($row->allow_decimal) {
                    return __('messages.yes');
                } else {
                    return __('messages.no');
                }
            })
            ->removeColumn('id')
            ->rawColumns([3])
            ->make(false);
        }
        

        return view('unit.index', compact('units', 'conf_units'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('unit.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        return view('unit.create')
        ->with(compact('quick_add'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('unit.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['actual_name', 'short_name', 'allow_decimal']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            DB::beginTransaction();

            $unit = Unit::create($input);

            /** Sync unit */
            if ($this->clone_product) {
                $this->productUtil->syncUnit($unit->id, $unit->actual_name);
            }

            DB::commit();

            $output = [
                'success' => true,
                'data' => $unit,
                'msg' => __("unit.added_success")
            ];
        } catch (\Exception $e) {
            DB::rollback();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unit = Unit::where('id', $id)->first();
        return response()->json($unit);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('unit.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $unit = Unit::where('business_id', $business_id)->find($id);

            return view('unit.edit')
            ->with(compact('unit'));
        }
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
        if (!auth()->user()->can('unit.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['actual_name', 'short_name', 'allow_decimal']);
                $business_id = $request->session()->get('user.business_id');

                DB::beginTransaction();

                $unit = Unit::where('business_id', $business_id)->findOrFail($id);
                $name = $unit->actual_name;

                $unit->actual_name = $input['actual_name'];
                $unit->short_name = $input['short_name'];
                $unit->allow_decimal = $input['allow_decimal'];
                $unit->save();

                /** Sync unit */
                if ($this->clone_product) {
                    $this->productUtil->syncUnit($unit->id, $name);
                }

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __("unit.updated_success")
                ];
            } catch (\Exception $e) {
                DB::rollback();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('unit.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try{
                $business_id = request()->user()->business_id;

                $unit = Unit::where('business_id', $business_id)->findOrFail($id);

                $groups = UnitGroup::where('unit_id', $id)->count();
                $groupLines = UnitGroupLines::where('unit_id', $id)->count();

                if(($groups > 0) || ($groupLines > 0)){
                    $output = [
                        'success' => false,
                        'msg' => __("unit.has_children")
                    ];
                }
                else{
                    $old_unit = clone $unit;

                    $unit->delete();
                    
                    /** Sync unit */
                    if ($this->clone_product) {
                        $this->productUtil->syncUnit($unit->id, "", $old_unit);
                    }

                    $output = [
                        'success' => true,
                        'msg' => __("unit.deleted_success")
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
    }
    public function getUnits(){
        if (!auth()->user()->can('unit.view') && !auth()->user()->can('unit.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $units = Unit::select('id', 'actual_name')->where('business_id', $business_id)->get();
        return $units;
    }
}
