<?php

namespace App\Http\Controllers;

use App\Employees;
use App\RrhhData;
use App\RrhhAbsenceInability;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;
use App\Utils\ModuleUtil;

class RrhhAbsenceInabilityController extends Controller
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
        if ( !auth()->user()->can('rrhh_employees.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $employee = Employees::where('id', $id)->where('business_id', $business_id)->first();
        $absenceInabilities = RrhhAbsenceInability::where('employee_id', $employee->id)->get();

        return view('rrhh.absence_inabilities.index', compact('absenceInabilities', 'employee'));
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

    function createAbsenceInability($id) 
    {
        if ( !auth()->user()->can('rrhh_absence_inability.create') ) {
            abort(403, 'Unauthorized action.');
        }
 
        $business_id = request()->session()->get('user.business_id');
        $typeAbsences = RrhhData::where('rrhh_header_id', 13)->where('business_id', $business_id)->where('status', 1)->orderBy('id', 'DESC')->get();
        $typeInabilities = RrhhData::where('rrhh_header_id', 14)->where('business_id', $business_id)->where('status', 1)->orderBy('id', 'DESC')->get();
        $employee_id = $id;

        return view('rrhh.absence_inabilities.create', compact('employee_id', 'typeAbsences', 'typeInabilities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_absence_inability.create') ) {
            abort(403, 'Unauthorized action.');
        }
        $requiredStartDate = 'nullable';
        $requiredEndDate = 'nullable';
        $requiredTypeAbsence = 'nullable';
        $requiredTypeInability = 'nullable';
        $requiredAmount = 'nullable';

        if($request->input('type') == 1){
            $requiredTypeAbsence = 'required';
            $requiredAmount = 'required';
        }else{
            $requiredEndDate = 'required';
            $requiredTypeInability = 'required';
        }

        $request->validate([
            'type' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => $requiredEndDate,
            'amount' => $requiredAmount,
            'type_absence_id' => $requiredTypeAbsence,
            'type_inability_id' => $requiredTypeInability,
        ]);

        try {
            $input_details = $request->only([
                'description',
                'employee_id'
            ]);

            $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));

            if($request->input('type') == 1){
                $input_details['type'] = 'Ausencia';
                $input_details['type_absence_id'] = $request->input('type_absence_id');
                $input_details['amount'] = $request->input('amount');
            }else{
                $input_details['type'] = 'Incapacidad';
                $input_details['type_inability_id'] = $request->input('type_inability_id');
                $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));
            }
            
            DB::beginTransaction();
    
            $absenceInhability = RrhhAbsenceInability::create($input_details);
    
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
     * @param  \App\RrhhAbsenceInability  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhAbsenceInability $rrhhDocuments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhAbsenceInability  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        if ( !auth()->user()->can('rrhh_absence_inability.edit') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $absenceInability = RrhhAbsenceInability::findOrFail($id);
        $typeAbsences = RrhhData::where('rrhh_header_id', 13)->where('business_id', $business_id)->where('status', 1)->orderBy('id', 'DESC')->get();
        $typeInabilities = RrhhData::where('rrhh_header_id', 14)->where('business_id', $business_id)->where('status', 1)->orderBy('id', 'DESC')->get();
        $employee_id = $absenceInability->employee_id;

        return view('rrhh.absence_inabilities.edit', compact('absenceInability', 'typeAbsences', 'typeInabilities', 'employee_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhAbsenceInability  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    public function updateAbsenceInability(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_absence_inability.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $absenceInability = RrhhAbsenceInability::findOrFail($request->input('id'));
        $requiredStartDate = 'nullable';
        $requiredEndDate = 'nullable';
        $requiredTypeAbsence = 'nullable';
        $requiredTypeInability = 'nullable';
        $requiredAmount = 'nullable';

        if($absenceInability->type == 'Ausencia'){
            $requiredTypeAbsence = 'required';
            $requiredAmount = 'required';
        }else{
            $requiredEndDate = 'required';
            $requiredTypeInability = 'required';
        }

        $request->validate([
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => $requiredEndDate,
            'amount' => $requiredAmount,
            'type_absence_id' => $requiredTypeAbsence,
            'type_inability_id' => $requiredTypeInability,
        ]);

        try {
            $input_details = $request->only([
                'description'
            ]);

            $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));

            if($absenceInability->type == 'Ausencia'){
                $input_details['type_absence_id'] = $request->input('type_absence_id');
                $input_details['amount'] = $request->input('amount');
            }else{
                $input_details['type_inability_id'] = $request->input('type_inability_id');
                $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));
            }
            
            DB::beginTransaction();
    
            $absenceInability->update($input_details);
    
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
     * @param  \App\RrhhAbsenceInability  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) 
    {
        if (!auth()->user()->can('rrhh_absence_inability.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $item = RrhhAbsenceInability::findOrFail($id);
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
