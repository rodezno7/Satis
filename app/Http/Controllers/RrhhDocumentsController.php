<?php

namespace App\Http\Controllers;

use App\Employees;
use App\RrhhDocuments;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;
use App\Utils\ModuleUtil;

class RrhhDocumentsController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtil $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function getByEmployee($id) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $employee = Employees::findOrFail($id);
        $business_id = request()->session()->get('user.business_id');
        $documents = DB::table('rrhh_documents as document')
        ->join('rrhh_datas as type', 'type.id', '=', 'document.document_type_id')
        ->join('states as state', 'state.id', '=', 'document.state_id')
        ->join('cities as city', 'city.id', '=', 'document.city_id')
        ->select('document.id as id', 'type.value as type', 'state.name as state', 'city.name as city', 'document.number as number', 'document.file as file', 'document.date_expedition as date_expedition', 'document.date_expiration as date_expiration')
        ->where('document.employee_id', $employee->id)
        ->get();
        $types = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('business_id', $business_id)->where('status', 1)->get();
        
        return view('rrhh.documents.documents', compact('documents', 'employee', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() 
    {
        //
    }

    function createDocument($id) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $states = DB::table('states')->pluck('name', 'id');
        $cities = DB::table('cities')->pluck('name', 'id');
        $business_id = request()->session()->get('user.business_id');
        $types = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'DESC')->get();
        $documents = DB::table('rrhh_documents')->where('employee_id', $id)->get();

        for ($i=0; $i < count($documents); $i++) { 
            if(isset($types)){
                if(!empty($types)){
                    for ($j=0; $j < count($types); $j++) {
                        if($types[$j]->id == $documents[$i]->document_type_id){
                            $types[$j]->value = '';
                        }
                    }
                }
            }
        }
        $type_documents = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'DESC')->get();
        $employee_id = $id;

        return view('rrhh.documents.create', compact('states', 'cities', 'types', 'employee_id', 'type_documents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.create') ) {
            abort(403, 'Unauthorized action.');
        }
        if($request->document_type_id != null){
            $business_id = request()->session()->get('user.business_id');
            $type = DB::table('rrhh_datas')->where('business_id', $business_id)->where('rrhh_header_id', 9)->where('status', 1)->where('date_required', 1)->where('id', $request->document_type_id)->first();
            if($type){
                $request->validate([
                    'state_id'              => 'required',
                    'city_id'               => 'required',
                    'number'                => 'required',
                    'date_expedition'       => 'required',
                    'date_expiration'       => 'required|after:date_expedition',
                    'file'                  => 'required',
                ]);
            }else{
                $request->validate([
                    'state_id'              => 'required',
                    'city_id'               => 'required',
                    'number'                => 'required',
                    'file'                  => 'required',
                    'date_expedition'       => 'required',
                ]);
            }
        }
        else{
            $request->validate([
                'document_type_id'      => 'required',
                'state_id'              => 'required',
                'city_id'               => 'required',
                'number'                => 'required',
                'file'                  => 'required',
                'date_expedition'       => 'required',
            ]);
        }

        try {
            $input_details = $request->all();
            $input_details['date_expiration'] = $this->moduleUtil->uf_date($request->input('date_expiration'));
            $input_details['date_expedition'] = $this->moduleUtil->uf_date($request->input('date_expedition'));
            if($input_details['date_expedition'] < $input_details['date_expiration'])
            {
                DB::beginTransaction();
    
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $name = time().$file->getClientOriginalName();
                    Storage::disk('flags')->put($name,  \File::get($file));
                    $input_details['file'] = $name;
                }
    
                $document = RrhhDocuments::create($input_details);
    
                DB::commit();
    
                $output = [
                    'success' => 1,
                    'msg' => __('rrhh.added_successfully')
                ];
            }else
            {
                $output = [
                    'success' => 0,
                    'msg' => __('rrhh.message_date_valitation')
                ];
            }

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
     * @param  \App\RrhhDocuments  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhDocuments $rrhhDocuments)
    {
        //
    }

    function viewFile($id) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $document = RrhhDocuments::findOrFail($id);
        $state = DB::table('states')->where('id', $document->state_id)->first();
        $city = DB::table('cities')->where('id', $document->city_id)->first();
        $type = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('id', $document->document_type_id)->first();
        
        $route = 'flags/'.$document->file;
        $ext = substr($document->file, -3);


        return view('rrhh.documents.file', compact('route', 'ext', 'document', 'state', 'city', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhDocuments  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.edit') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $document = RrhhDocuments::findOrFail($id);
        $states = DB::table('states')->pluck('name', 'id');
        $cities = DB::table('cities')->where('state_id', $document->state_id)->pluck('name', 'id');
        $type = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('business_id', $business_id)->where('status', 1)->where('id', $document->document_type_id)->first();
        //$types = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $employee_id = $document->employee_id;

        return view('rrhh.documents.edit', compact('type', 'document', 'states', 'cities', 'employee_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhDocuments  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    public function updateDocument(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $item = RrhhDocuments::findOrFail($request->id);
        $business_id = request()->session()->get('user.business_id');
        $type = DB::table('rrhh_datas')->where('business_id', $business_id)->where('rrhh_header_id', 9)->where('status', 1)->where('date_required', 1)->where('id', $item->document_type_id)->first();
        if($type){
            $request->validate([
                'state_id'              => 'required',
                'city_id'               => 'required',
                'number'                => 'required',
                'date_expedition'       => 'required',
                'date_expiration'       => 'required',
                'file'                  => 'required',
            ]);
        }else{
            $request->validate([
                'state_id'              => 'required',
                'city_id'               => 'required',
                'number'                => 'required',
                'file'                  => 'required',
                'date_expedition'       => 'required',
            ]);
        }
        try {
            $input_details = $request->only([
                'date_expiration', 
                'date_expedition', 
                'state_id', 
                'city_id',
                'number',
                'file'
            ]);

            $input_details['date_expiration'] = $this->moduleUtil->uf_date($request->input('date_expiration'));
            $input_details['date_expedition'] = $this->moduleUtil->uf_date($request->input('date_expedition'));

            if($input_details['date_expedition'] < $input_details['date_expiration'])
            {
                DB::beginTransaction();

                $item = RrhhDocuments::findOrFail($request->id);

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
            }
            else
            {
                $output = [
                    'success' => 0,
                    'msg' => __('rrhh.message_date_valitation')
                ];
            }
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
     * Remove the specified resource from storage.
     *
     * @param  \App\RrhhDocuments  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        if (!auth()->user()->can('rrhh_overall_payroll.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $item = RrhhDocuments::findOrFail($id);
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
}
