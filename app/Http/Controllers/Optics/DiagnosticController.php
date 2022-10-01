<?php

namespace App\Http\Controllers\Optics;

use App\Optics\Diagnostic;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class DiagnosticController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('diagnostic.view') && !auth()->user()->can('diagnostic.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $diagnostics = Diagnostic::where('business_id', $business_id)
                    ->select(['name', 'id']);
            
            return Datatables::of($diagnostics)
                ->addColumn(
                    'action',
                    '@can("diagnostic.update")
                    <button data-href="{{ action(\'Optics\DiagnosticController@edit\', [$id]) }}" class="btn btn-xs btn-primary edit_diagnostics_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                    &nbsp;
                    @endcan
                    @can("diagnostic.delete")
                    <button data-href="{{ action(\'Optics\DiagnosticController@destroy\', [$id]) }}" class="btn btn-xs btn-danger delete_diagnostics_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->removeColumn('id')
                ->rawColumns([1])
                ->make(false);
        }

        return view('optics.diagnostic.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('diagnostic.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('optics.diagnostic.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('diagnostic.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name']);

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $diagnostic = Diagnostic::create($input);
    
            $output = ['success' => true,
                'data' => $diagnostic,
                'msg' => __("diagnostic.added_success")
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
     * @param  \App\Diagnostic  $diagnostic
     * @return \Illuminate\Http\Response
     */
    public function show(Diagnostic $diagnostic)
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
        if (!auth()->user()->can('diagnostic.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $diagnostic = Diagnostic::find($id);

            return view('optics.diagnostic.edit')
                ->with(compact('diagnostic'));
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
        if (!auth()->user()->can('diagnostic.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name']);
                
                $diagnostic = Diagnostic::findOrFail($id);
                $diagnostic->fill($input);
                $diagnostic->save();

                $output = ['success' => true,
                    'msg' => __("diagnostic.updated_success")
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('diagnostic.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $diagnostic = Diagnostic::findOrFail($id);
                $diagnostic->delete();

                $output = ['success' => true,
                    'msg' => __("diagnostic.deleted_success")
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
