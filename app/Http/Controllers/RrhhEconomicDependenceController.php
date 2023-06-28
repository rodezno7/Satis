<?php

namespace App\Http\Controllers;

use App\Employees;
use App\RrhhEconomicDependence;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;
use App\Utils\ModuleUtil;

class RrhhEconomicDependenceController extends Controller
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
        $business_id = request()->session()->get('user.business_id');
        $employee = Employees::where('id', $id)->where('business_id', $business_id)->first();
        $economicDependences = DB::table('rrhh_economic_dependences as economicDependence')
        ->join('rrhh_datas as type', 'type.id', '=', 'economicDependence.type_relationship_id')
        ->join('employees as employee', 'employee.id', '=', 'economicDependence.employee_id')
        ->select('economicDependence.id as id', 'type.value as type', 'economicDependence.name as name', 'economicDependence.birthdate as birthdate', 'economicDependence.phone as phone', 'economicDependence.status as status')
        ->where('economicDependence.employee_id', $employee->id)
        ->where('type.rrhh_header_id', 15)
        ->get();
        
        return view('rrhh.economic_dependences.index', compact('economicDependences', 'employee'));
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

    function createEconomicDependence($id) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.create') ) {
            abort(403, 'Unauthorized action.');
        }

        
        $business_id = request()->session()->get('user.business_id');
        $typeRelationships = DB::table('rrhh_datas')->where('rrhh_header_id', 15)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'DESC')->get();
        $employee_id = $id;

        return view('rrhh.economic_dependences.create', compact('employee_id', 'typeRelationships'));
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
        $request->validate([
            'type_relationship_id' => 'required',
            'name'                 => 'required',
            'employee_id'          => 'required',
            'phone'                => 'required',
            'birthdate'            => 'required',
        ]);

        try {
            $input_details = $request->all();
            $input_details['birthdate'] = $this->moduleUtil->uf_date($request->input('birthdate'));
            $input_details['status'] = 1;
            DB::beginTransaction();
    
            $economicDependence = RrhhEconomicDependence::create($input_details);
    
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
     * @param  \App\RrhhEconomicDependence  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhEconomicDependence $rrhhDocuments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhEconomicDependence  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.edit') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $economicDependence = RrhhEconomicDependence::findOrFail($id);
        $typeRelationships = DB::table('rrhh_datas')->where('rrhh_header_id', 15)->where('business_id', $business_id)->where('status', 1)->get();
        $employee_id = $economicDependence->employee_id;

        return view('rrhh.economic_dependences.edit', compact('typeRelationships', 'economicDependence', 'employee_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhEconomicDependence  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    public function updateEconomicDependence(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'type_relationship_id' => 'required',
            'name'                 => 'required',
            'employee_id'          => 'required',
            'phone'                => 'required',
            'birthdate'            => 'required',
            'status'               => 'required',
        ]);

        try {
            $input_details = $request->all();
            $input_details['birthdate'] = $this->moduleUtil->uf_date($request->input('birthdate'));
            if($request->input('status') == 1){
                $input_details['status'] = 1;
            }else{
                $input_details['status'] = 0;
            }
            DB::beginTransaction();
    
            $item = RrhhEconomicDependence::findOrFail($request->id);
            $economicDependence = $item->update($input_details);
    
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
     * @param  \App\RrhhEconomicDependence  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        if (!auth()->user()->can('rrhh_overall_payroll.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $item = RrhhEconomicDependence::findOrFail($id);
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
