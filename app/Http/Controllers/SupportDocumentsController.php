<?php

namespace App\Http\Controllers;

use App\SupportDocuments;
use Illuminate\Http\Request;
use DB;
use Yajra\DataTables\DataTables;

class SupportDocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('sdocs.view') && !auth()->user()->can('sdocs.create')) {
            abort(403, 'Unauthorized action.');
        }
        return view('support_documents.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('sdocs.view') && !auth()->user()->can('sdocs.create')) {
            abort(403, 'Unauthorized action.');
        }
        return view('support_documents.create');
    }

    public function getSDocsData()
    {
        if (!auth()->user()->can('sdocs.view') && !auth()->user()->can('sdocs.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $sdocs = DB::table('support_documents')
            ->select('id', 'name', 'description')
            ->where('business_id', $business_id);
        return DataTables::of($sdocs)
            ->addColumn(
                'action',
                '@can("sdocs.update")
            <button data-href="{{action(\'SupportDocumentsController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_sdocs_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                &nbsp;
            @endcan
            @can("sdocs.delete")
                <button data-href="{{action(\'SupportDocumentsController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_sdocs_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
            @endcan'
            )
            ->rawColumns([3])
            ->make(false);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('sdocs.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $sdocs = $request->only(['name', 'description']);
            $sdocs['business_id'] = $request->session()->get('user.business_id');

            if (empty($request->input('description'))) {
                $sdocs['description'] = $request->input('name');
            }

            $sdocs = SupportDocuments::create($sdocs);
            $outpout = [
                'success' => true,
                'data' => $sdocs,
                'msg' => __("lang_v1.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => $e->getMessage()];
        }

        return $outpout;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SupportDocuments  $supportDocuments
     * @return \Illuminate\Http\Response
     */
    public function show(SupportDocuments $supportDocuments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SupportDocuments  $supportDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('sdocs.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $sdocs = SupportDocuments::where('business_id', $business_id)->find($id);

            return view('support_documents.edit')
                ->with(compact('sdocs'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SupportDocuments  $supportDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('sdocs.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'description']);
                $business_id = $request->session()->get('user.business_id');

                $sdocs = SupportDocuments::where('business_id', $business_id)->findOrFail($id);
                $sdocs->name = $input['name'];
                $sdocs->description = $input['description'];
                $sdocs->save();

                $outpout = [
                    'success' => true,
                    'data' => $sdocs,
                    'msg' => __("lang_v1.updated_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $outpout = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $outpout;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SupportDocuments  $supportDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('sdocs.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $sdocs = SupportDocuments::where('business_id', $business_id)->findOrFail($id);
                $sdocs->delete();

                $outpout = [
                    'success' => true,
                    'msg' => __("lang_v1.deleted_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $outpout = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $outpout;
        }
    }
}
