<?php

namespace App\Http\Controllers\Optics;

use App\Optics\FlowReason;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class FlowReasonController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $util;

    /**
     * Constructor
     *
     * @param \App\Utils\Util $util
     * @return void
     */
    public function __construct(Util $util)
    {
        $this->util = $util;
        $this->module_name = 'flow_reason';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('flow_reason.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $flow_reasons = FlowReason::where('business_id', $business_id)
                ->select('id', 'reason', 'description');
            
            return Datatables::of($flow_reasons)
                ->addColumn(
                    'action',
                    '@can("flow_reason.update")
                    <button data-href="{{ action(\'Optics\FlowReasonController@edit\', [$id]) }}"
                        class="btn btn-xs btn-primary edit_flow_reasons_button">
                        <i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")
                    </button>
                    &nbsp;
                    @endcan
                    @can("flow_reason.delete")
                    <button data-href="{{ action(\'Optics\FlowReasonController@destroy\', [$id]) }}"
                        class="btn btn-xs btn-danger delete_flow_reasons_button">
                        <i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")
                    </button>
                    @endcan'
                )
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('optics.flow_reason.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('flow_reason.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('optics.flow_reason.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('flow_reason.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['reason', 'description']);

            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            $flow_reason = FlowReason::create($input);

            # Store binnacle
            $user_id = $request->session()->get('user.id');

            $this->util->registerBinnacle($user_id, $this->module_name, 'create', $flow_reason);

            $output = [
                'success' => true,
                'data' => $flow_reason,
                'msg' => __('flow_reason.added_success')
            ];

        } catch (\Exception $e) {
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
     * @param  \App\FlowReason  $flowReason
     * @return \Illuminate\Http\Response
     */
    public function show(FlowReason $flowReason)
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
        if (! auth()->user()->can('flow_reason.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $flow_reason = FlowReason::findOrFail($id);

            return view('optics.flow_reason.edit')->with(compact('flow_reason'));
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
        if (! auth()->user()->can('flow_reason.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['reason', 'description']);

                $input['updated_by'] = $request->session()->get('user.id');

                $flow_reason = FlowReason::findOrFail($id);

                # Clone record before action
                $flow_reason_old = clone $flow_reason;

                $flow_reason->fill($input);
                $flow_reason->save();

                # Store binnacle
                $user_id = $request->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'update', $flow_reason_old, $flow_reason);

                $output = [
                    'success' => true,
                    'msg' => __('flow_reason.updated_success')
                ];

            } catch (\Exception $e) {
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('flow_reason.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $flow_reason = FlowReason::findOrFail($id);

                # Clone record before action
                $flow_reason_old = clone $flow_reason;

                $flow_reason->delete();

                # Store binnacle
                $user_id = request()->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'delete', $flow_reason_old);

                $output = [
                    'success' => true,
                    'msg' => __('flow_reason.deleted_success')
                ];

            } catch (\Exception $e) {
                \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }
}
