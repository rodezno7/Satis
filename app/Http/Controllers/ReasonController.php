<?php

namespace App\Http\Controllers;

use App\Reason;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('pos.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = auth()->user()->business_id;
        if (request()->ajax()) {
            $reasons = Reason::where('business_id', $business_id)
                ->select(['id', 'reason', 'comments']);

            return DataTables::of($reasons)
                ->addColumn(
                    'actions',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        if (auth()->user()->can('pos.update')) {
                            $html .= '<li><a href="#" data-href="' . action('ReasonController@edit', [$row->id]) . '" class="edit_reason_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }

                        if (auth()->user()->can('pos.delete')) {
                            $html .= '<li><a href="#" onclick="deleteReason(' . $row->id . ')"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->removeColumn('id')
                ->rawColumns(['actions'])
                ->toJson();
        } else {
            return view('reason_lost_sale.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('pos.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('reason_lost_sale.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('quotes.create')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate(
            [
                'reason' => 'required',
                'comments' => 'required',
            ],
            [
                'reason_id.required' => trans('La razón es requerida'),
                'comments.required' => trans('La descripción es requerida'),
            ]
        );

        try {
            DB::beginTransaction();
            $business_id = auth()->user()->business_id;
            $reason = new Reason();
            $reason->reason = $request->reason;
            $reason->comments = $request->comments;
            $reason->business_id = $business_id;
            $reason->save();
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("información guardada correctamente"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
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
        if (!auth()->user()->can('quotes.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $reason = Reason::find($id);
        return view('reason_lost_sale.edit', compact('reason'));
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
        if (!auth()->user()->can('quotes.update')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate(
            [
                'reason' => 'required',
                'comments' => 'required',
            ],
            [
                'reason_id.required' => trans('La razón es requerida'),
                'comments.required' => trans('La descripción es requerida'),
            ]
        );

        try {
            DB::beginTransaction();
            $reason = Reason::find($id);
            $reason->reason = $request->reason;
            $reason->comments = $request->comments;
            $reason->update();

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("información actualizada correctamente"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('quotes.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $reason = Reason::find($id);
            $reason->delete();

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("Eliminado correctamente"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }
}
