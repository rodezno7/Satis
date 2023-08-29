<?php

namespace App\Http\Controllers;

use App\Employees;
use App\RrhhStudy;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;
use App\Utils\ModuleUtil;

class RrhhStudyController extends Controller
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
        if ( !auth()->user()->can('rrhh_study.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $employee = Employees::where('id', $id)->where('business_id', $business_id)->first();
        $studies = DB::table('rrhh_studies as study')
            ->join('rrhh_datas as type', 'type.id', '=', 'study.type_study_id')
            ->join('employees as employee', 'employee.id', '=', 'study.employee_id')
            ->select('study.id as id', 'type.value as type', 'study.title as title', 'study.institution as institution', 'study.year_graduation as year_graduation', 'study.study_status as study_status', 'study.status as status')
            ->where('study.employee_id', $employee->id)
            ->where('type.rrhh_header_id', 12)
            ->get();
        
        return view('rrhh.studies.index', compact('studies', 'employee'));
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

    function createStudy($id) 
    {
        if ( !auth()->user()->can('rrhh_study.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $typeStudies = DB::table('rrhh_datas')->where('rrhh_header_id', 12)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'DESC')->get();
        $employee_id = $id;

        return view('rrhh.studies.create', compact('employee_id', 'typeStudies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_study.create') ) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'type_study_id'   => 'required',
            'title'           => 'required',
            'institution'     => 'required',
            'employee_id'     => 'required',
            'study_status'    => 'required',
            'year_graduation' => 'required',
        ]);

        try {
            $input_details = $request->all();
            if($request->study_status == 'en_curso'){
                $input_details['study_status'] = 'En curso';
            }else{
                $input_details['study_status'] = 'Finalizado';
            }
            
            DB::beginTransaction();
    
            $study = RrhhStudy::create($input_details);
    
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
     * @param  \App\RrhhStudy  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhStudy $rrhhDocuments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhStudy  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        if ( !auth()->user()->can('rrhh_study.edit') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $study = RrhhStudy::findOrFail($id);
        $typeStudies = DB::table('rrhh_datas')->where('rrhh_header_id', 12)->where('business_id', $business_id)->where('status', 1)->get();
        $employee_id = $study->employee_id;

        return view('rrhh.studies.edit', compact('typeStudies', 'study', 'employee_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhStudy  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    public function updateStudy(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_study.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'type_study_id'   => 'required',
            'title'           => 'required',
            'institution'     => 'required',
            'employee_id'     => 'required',
            'study_status'    => 'required',
            'year_graduation' => 'required',
            'status'          => 'required',
        ]);

        try {
            $input_details = $request->all();
            if($request->study_status == 'en_curso'){
                $input_details['study_status'] = 'En curso';
            }else{
                $input_details['study_status'] = 'Finalizado';
            }

            if($request->input('status') == 1){
                $input_details['status'] = 1;
            }else{
                $input_details['status'] = 0;
            }
            DB::beginTransaction();
    
            $item = RrhhStudy::findOrFail($request->id);
            $study = $item->update($input_details);
    
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RrhhStudy  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        if (!auth()->user()->can('rrhh_study.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $item = RrhhStudy::findOrFail($id);
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
