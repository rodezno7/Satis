<?php

namespace App\Http\Controllers;

use App\HumanResourceDocuments;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;

class HumanResourceDocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if ( !auth()->user()->can('rrhh_overall_payroll.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'document_type_id'      => 'required',
            'state_id'              => 'required',
            'city_id'               => 'required',
            'number'                => 'required',
            
        ]);

        try {

            $input_details = $request->all();

            DB::beginTransaction();

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $name = time().$file->getClientOriginalName();
                Storage::disk('flags')->put($name,  \File::get($file));
                $input_details['file'] = $name;
            }



            $document = HumanResourceDocuments::create($input_details);

            DB::commit();


            $output = [
                'success' => 1,
                'msg' => __('rrhh.added_successfully')
            ];


        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('rrhh.error')
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HumanResourceDocuments  $humanResourceDocuments
     * @return \Illuminate\Http\Response
     */
    public function show(HumanResourceDocuments $humanResourceDocuments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HumanResourceDocuments  $humanResourceDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        if ( !auth()->user()->can('rrhh_overall_payroll.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $document = HumanResourceDocuments::findOrFail($id);
        $states = DB::table('states')->pluck('name', 'id');
        $cities = DB::table('cities')->where('state_id', $document->state_id)->pluck('name', 'id');
        $types = DB::table('human_resources_datas')->where('human_resources_header_id', 9)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');

        return view('rrhh.documents.edit', compact('types', 'document', 'states', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HumanResourceDocuments  $humanResourceDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HumanResourceDocuments  $humanResourceDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        if (!auth()->user()->can('rrhh_overall_payroll.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            try {

                $item = HumanResourceDocuments::findOrFail($id);
                $item->forceDelete();
                
                $output = [
                    'success' => true,
                    'msg' => __('rrhh.deleted_successfully')
                ];


            }                


            catch (\Exception $e){
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __('rrhh.error')
                ];
            }

            return $output;
        }

    }

    public function getByEmployee($id) {

        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $documents = DB::table('human_resource_documents as document')
        ->join('human_resources_datas as type', 'type.id', '=', 'document.document_type_id')
        ->join('states as state', 'state.id', '=', 'document.state_id')
        ->join('cities as city', 'city.id', '=', 'document.city_id')
        ->select('document.id as id', 'type.value as type', 'state.name as state', 'city.name as city', 'document.number as number', 'document.file as file')
        ->where('document.employee_id', $id)
        ->get();

        return view('rrhh.documents.documents', compact('documents'));
    }

    function createDocument($id) {

        if ( !auth()->user()->can('rrhh_overall_payroll.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $states = DB::table('states')->pluck('name', 'id');
        $cities = DB::table('cities')->pluck('name', 'id');
        $types = DB::table('human_resources_datas')->where('human_resources_header_id', 9)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $employee_id = $id;


        return view('rrhh.documents.create', compact('states', 'cities', 'types', 'employee_id'));
    }

    function viewFile($id) {

        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $document = HumanResourceDocuments::findOrFail($id);

        $route = 'flags/'.$document->file;
        $ext = substr($document->file, -3);


        return view('rrhh.documents.file', compact('route', 'ext'));
    }

    public function updateDocument(Request $request) {

        if ( !auth()->user()->can('rrhh_overall_payroll.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'document_type_id'      => 'required',
            'state_id'              => 'required',
            'city_id'               => 'required',
            'number'                => 'required',
            
        ]);

        try {

            $input_details = $request->all();

            DB::beginTransaction();

            $item = HumanResourceDocuments::findOrFail($request->input('document_id'));

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $name = time().$file->getClientOriginalName();
                Storage::disk('flags')->put($name,  \File::get($file));
                $input_details['file'] = $name;
            }

            $document = $item->update($input_details);

            DB::commit();


            $output = [
                'success' => 1,
                'msg' => __('rrhh.updated_successfully')
            ];


        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('rrhh.error')
            ];
        }

        return $output;
    }
}
