<?php

namespace App\Http\Controllers\Optics;

use App\Optics\ExternalLab;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\DataTables;

class ExternalLabController extends Controller
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
        $this->module_name = 'external_lab';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('external_lab.view') && !auth()->user()->can('external_lab.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $external_lab = ExternalLab::select(['name', 'address', 'id']);

            return Datatables::of($external_lab)
                ->addColumn(
                    'action',
                    '@can("external_lab.update")
                    <button data-href="{{ action(\'Optics\ExternalLabController@edit\', [$id]) }}" class="btn btn-xs btn-primary edit_external_labs_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                    &nbsp;
                    @endcan
                    @can("external_lab.delete")
                    <button data-href="{{ action(\'Optics\ExternalLabController@destroy\', [$id]) }}" class="btn btn-xs btn-danger delete_external_labs_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('optics.external_lab.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('external_lab.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('optics.external_lab.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('external_lab.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'address', 'description']);

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $external_lab = ExternalLab::create($input);

            # Store binnacle
            $user_id = $request->session()->get('user.id');

            $this->util->registerBinnacle($user_id, $this->module_name, 'create', $external_lab);

            $output = [
                'success' => true,
                'data' => $external_lab,
                'msg' => __("external_lab.added_success")
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
     * @param  \App\ExternalLab  $externalLab
     * @return \Illuminate\Http\Response
     */
    public function show(ExternalLab $externalLab)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ExternalLab  $externalLab
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('external_lab.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $external_lab = ExternalLab::find($id);

            return view('optics.external_lab.edit')->with(compact('external_lab'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ExternalLab  $externalLab
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('external_lab.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'address', 'description']);

                $external_lab = ExternalLab::findOrFail($id);

                # Clone record before action
                $external_lab_old = clone $external_lab;

                $external_lab->fill($input);
                $external_lab->save();

                # Store binnacle
                $user_id = $request->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'update', $external_lab_old, $external_lab);

                $output = [
                    'success' => true,
                    'msg' => __("external_lab.updated_success")
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ExternalLab  $externalLab
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('external_lab.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $external_lab = ExternalLab::findOrFail($id);

                # Clone record before action
                $external_lab_old = clone $external_lab;

                $external_lab->delete();

                # Store binnacle
                $user_id = request()->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'delete', $external_lab_old);

                $output = [
                    'success' => true,
                    'msg' => __("external_lab.deleted_success")
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
