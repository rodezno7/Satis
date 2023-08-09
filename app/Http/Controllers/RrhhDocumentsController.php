<?php

namespace App\Http\Controllers;

use App\Employees;
use App\RrhhDocuments;
use App\RrhhDocumentFile;
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
        if ( !auth()->user()->can('rrhh_document_employee.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $employee = Employees::findOrFail($id);
        $business_id = request()->session()->get('user.business_id');
        $documents = DB::table('rrhh_documents as document')
        ->join('rrhh_datas as type', 'type.id', '=', 'document.document_type_id')
        ->join('states as state', 'state.id', '=', 'document.state_id')
        ->join('cities as city', 'city.id', '=', 'document.city_id')
        ->select('document.id as id', 'type.value as type', 'state.name as state', 'city.name as city', 'document.number as number', 'document.date_expedition as date_expedition', 'document.date_expiration as date_expiration')
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
        if ( !auth()->user()->can('rrhh_document_employee.create') ) {
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
        if ( !auth()->user()->can('rrhh_document_employee.create') ) {
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
                    'date_expiration'       => 'required',
                    'files'                 => 'required',
                ]);
            }else{
                $request->validate([
                    'state_id'              => 'required',
                    'city_id'               => 'required',
                    'number'                => 'required',
                    'files'                 => 'required',
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
                'files'                 => 'required',
                'date_expedition'       => 'required',
            ]);
        }

        \Log::info($request);
        try {
            $input_details = $request->all();
            $input_details['date_expedition'] = $this->moduleUtil->uf_date($request->input('date_expedition'));
            $date_expedition = strtotime($input_details['date_expedition']);

            if($type){
                $input_details['date_expiration'] = $this->moduleUtil->uf_date($request->input('date_expiration'));
                $date_expiration = strtotime($input_details['date_expiration']);
                if($date_expedition < $date_expiration)
                {
                    DB::beginTransaction();

                    $document = RrhhDocuments::create($input_details);
                    $files = [];
                    if ($request->file('files')){
                        $business_id = request()->session()->get('user.business_id');
                        $folderName = 'business_'.$business_id;
                        foreach($request->file('files') as $file)
                        {
                            if (!Storage::disk('employee_documents')->exists($folderName)) {
                                \File::makeDirectory(public_path().'/uploads/employee_documents/'.$folderName, $mode = 0755, true, true);
                            }
                            $name = time().'_'.$file->getClientOriginalName();
                            Storage::disk('employee_documents')->put($folderName.'/'.$name,  \File::get($file));
                            $input_document['file'] = $name;
                            $input_document['rrhh_document_id'] = $document->id;
                            RrhhDocumentFile::create($input_document);
                        }
                    }

                    $output = [
                        'success' => 1,
                        'msg' => __('rrhh.added_successfully')
                    ];

                    DB::commit();
                }else
                {
                    $output = [
                        'success' => 0,
                        'msg' => __('rrhh.message_date_valitation')
                    ];
                }
            }else{
                $input_details['date_expiration'] = null;
                
                DB::beginTransaction();

                $document = RrhhDocuments::create($input_details);
                $files = [];
                if ($request->file('files')){
                    $business_id = request()->session()->get('user.business_id');
                    $folderName = 'business_'.$business_id;
                    foreach($request->file('files') as $file)
                    {
                        if (!Storage::disk('employee_documents')->exists($folderName)) {
                            \File::makeDirectory(public_path().'/uploads/employee_documents/'.$folderName, $mode = 0755, true, true);
                        }
                        $name = time().'_'.$file->getClientOriginalName();
                        Storage::disk('employee_documents')->put($folderName.'/'.$name,  \File::get($file));
                        $input_document['file'] = $name;
                        $input_document['rrhh_document_id'] = $document->id;
                        RrhhDocumentFile::create($input_document);
                    }
                }

                $output = [
                    'success' => 1,
                    'msg' => __('rrhh.added_successfully')
                ];

                DB::commit();
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

    function files($id) 
    {
        if ( !auth()->user()->can('rrhh_document_employee.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $documentsFile = RrhhDocumentFile::where('rrhh_document_id', $id)->get();
        $document = RrhhDocuments::where('id', $id)->first();
        $employee = Employees::where('id', $document->employee_id)->first();

        return view('rrhh.documents.files', compact('documentsFile', 'employee'));
    }

    function viewFile($id) 
    {
        if ( !auth()->user()->can('rrhh_document_employee.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $documentFile = RrhhDocumentFile::findOrFail($id);
        $document = RrhhDocuments::where('id', $documentFile->rrhh_document_id)->first();
        $state = DB::table('states')->where('id', $document->state_id)->first();
        $city = DB::table('cities')->where('id', $document->city_id)->first();
        $type = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('id', $document->document_type_id)->first();
        
        $business_id = request()->session()->get('user.business_id');
        $folderName = 'business_'.$business_id;
        $route = 'uploads/employee_documents/'.$folderName.'/'.$documentFile->file;
        $ext = substr($documentFile->file, -3);


        return view('rrhh.documents.view', compact('route', 'ext', 'document', 'state', 'city', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhDocuments  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        if ( !auth()->user()->can('rrhh_document_employee.edit') ) {
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
        if ( !auth()->user()->can('rrhh_document_employee.edit') ) {
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
                'files'                  => 'required',
            ]);
        }else{
            $request->validate([
                'state_id'              => 'required',
                'city_id'               => 'required',
                'number'                => 'required',
                'files'                  => 'required',
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
                //'files'
            ]);

            $input_details['date_expiration'] = $this->moduleUtil->uf_date($request->input('date_expiration'));
            $input_details['date_expedition'] = $this->moduleUtil->uf_date($request->input('date_expedition'));

            if($input_details['date_expedition'] < $input_details['date_expiration'])
            {
                DB::beginTransaction();

                $item->update($input_details);
    
                $files = [];
                if ($request->file('files')){
                    $business_id = request()->session()->get('user.business_id');
                    $folderName = 'business_'.$business_id;
                    foreach($request->file('files') as $file)
                    {
                        if (!Storage::disk('employee_documents')->exists($folderName)) {
                            \File::makeDirectory(public_path().'/uploads/employee_documents/'.$folderName, $mode = 0755, true, true);
                        }
                        $name = time().'_'.$file->getClientOriginalName();
                        Storage::disk('employee_documents')->put($folderName.'/'.$name,  \File::get($file));
                        $input_document['file'] = $name;
                        $input_document['rrhh_document_id'] = $item->id;
                        RrhhDocumentFile::create($input_document);
                    }
                }

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

        if (!auth()->user()->can('rrhh_document_employee.delete')) {
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
