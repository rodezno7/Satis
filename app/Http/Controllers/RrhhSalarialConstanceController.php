<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use Illuminate\Http\Request;
use App\Employees;
use App\PayrollDetail;
use App\RrhhPositionHistory;
use App\RrhhSalarialConstance;
use App\RrhhSalaryHistory;
use App\Utils\EmployeeUtil;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use DB;
use DataTables;

class RrhhSalarialConstanceController extends Controller
{
    protected $moduleUtil;
    protected $employeeUtil;

    /**
     * Constructor
     *
     * @param ProductUtil $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, EmployeeUtil $employeeUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->employeeUtil = $employeeUtil;
    }

    public function getSalarialConstances(){

        if ( !auth()->user()->can('rrhh_catalogues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = RrhhSalarialConstance::where('business_id', $business_id)->where('deleted_at', null);
        
        return DataTables::of($data)->editColumn('status', function ($data) {
            if($data->status == 1){
                return __('rrhh.active');
            }else{
                return __('rrhh.inactive');
            }
        })->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('rrhh_catalogues.create')){
            abort(403, "Unauthorized action.");
        }
        return view('rrhh.catalogues.salarial_constances.create');
    }

    public function store(Request $request){
        if ( !auth()->user()->can('rrhh_catalogues.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'          => 'required',
            'editor'      => 'required',
            'margin_top'    => 'required|numeric|between:0.01,3.00',
            'margin_bottom' => 'required|numeric|between:0.01,3.00',
            'margin_left'   => 'required|numeric|between:0.01,3.00',
            'margin_right'  => 'required|numeric|between:0.01,3.00',
        ]);

        try {
            DB::beginTransaction();
            $input_details = $request->only([
                'name',
                'margin_top',
                'margin_bottom',
                'margin_left',
                'margin_right',
            ]);
            $input_details['template'] = $request->input('editor');
            $input_details['business_id'] = $request->session()->get('user.business_id');
            RrhhSalarialConstance::create($input_details);

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
                'msg' => $e->getMessage()
            ];
        }

        return redirect('rrhh-catalogues')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RrhhEconomicDependence  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!auth()->user()->can('rrhh_catalogues.view')){
            abort(403, "Unauthorized action.");
        }

        $business_id = request()->session()->get('user.business_id');
        $type = RrhhSalarialConstance::where('id', $id)->where('business_id', $business_id)->first();
        $pdf = \PDF::loadView(
            'rrhh.catalogues.salarial_constances.show', compact(['type'])
        );

        $pdf->setPaper('letter', 'portrait');
        return $pdf->stream(__('rrhh.contract') . '.pdf');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('rrhh_catalogues.update')){
            abort(403, "Unauthorized action.");
        }

        $business_id = request()->session()->get('user.business_id');
        $type = RrhhSalarialConstance::where('id', $id)->where('business_id', $business_id)->first();
        return view('rrhh.catalogues.salarial_constances.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhSalarialConstance  $rrhhTypeContract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'          => 'required',
            'editor'        => 'required',
            'margin_top'    => 'required|numeric|between:0.01,50.00',
            'margin_bottom' => 'required|numeric|between:0.01,50.00',
            'margin_left'   => 'required|numeric|between:0.01,50.00',
            'margin_right'  => 'required|numeric|between:0.01,50.00',
        ]);

        try {
            DB::beginTransaction();
            $input_details = $request->only([
                'name', 
                'margin_top',
                'margin_bottom',
                'margin_left',
                'margin_right',
                'status',
            ]);
            $input_details['template'] = $request->input('editor');
            $business_id = request()->session()->get('user.business_id');
            $type = RrhhSalarialConstance::where('id', $id)->where('business_id', $business_id)->first();
            $type->update($input_details);

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

        return redirect('rrhh-catalogues')->with('status', $output);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RrhhTypeWage  $rrhhTypeWage
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (!auth()->user()->can('rrhh_catalogues.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $count = DB::table('employees')
                ->where('bank_id', $id)               
                ->count();

                if ($count > 0) {
                    $output = [
                        'success' => false,
                        'msg' => __('rrhh.item_has_childs')
                    ];
                } else {
                    $item = RrhhSalarialConstance::findOrFail($id);
                    $item->delete();
                    $output = [
                        'success' => true,
                        'msg' => __('rrhh.deleted_successfully')
                    ];
                }               
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

    // public function salarialConstances(){
    //     if(request()->ajax()){
    //         $business_id = request()->session()->get('user.business_id');
    //         $data = DB::table('employees as e')
    //         ->select('e.id as id', 'e.agent_code', 'e.first_name', 'e.dni', 'e.email', 'e.curriculum_vitae as curriculum_vitae', 'e.status as status', DB::raw("CONCAT(e.first_name, ' ', e.last_name) as full_name"))
    //         ->where('e.business_id', $business_id)
    //         ->where('e.deleted_at', null)
    //         ->get();
            
    //         return DataTables::of($data)->editColumn('department', function ($data) {
    //             $position = RrhhPositionHistory::where('employee_id', $data->id)->where('current', 1)->first();
    //             return (!empty($position)) ? $position->newDepartment->value : __('rrhh.not_assigned');
    //         })->editColumn('position', function ($data) {
    //             $position = RrhhPositionHistory::where('employee_id', $data->id)->where('current', 1)->first();
    //             return (!empty($position)) ? $position->newPosition1->value : __('rrhh.not_assigned');
    //         })->editColumn('status', function ($data) {
    //             if($data->status == 1){
    //                 return __('rrhh.active');
    //             }else{
    //                 return __('rrhh.inactive');
    //             }
    //         })->toJson();
    //     }

    //     return view('payroll.report.salarial_constances');
    // }

    public function download(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $salarialConstance = RrhhSalarialConstance::where('status', 1)->firstOrFail();
        $business          = Business::findOrFail($business_id);
        $business_location = BusinessLocation::where('business_id', $business_id)->first();
        $employee = Employees::findOrFail($id);
        $positionHistory   = RrhhPositionHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
        $salaryHistory     = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
        

        $payrollSalary     = PayrollDetail::select('payroll_details.isss', 'payroll_details.isss', 'payroll_details.afp', 'payroll_details.rent')
            ->join('payrolls', 'payrolls.id', '=', 'payroll_details.payroll_id')
            ->join('payroll_types', 'payroll_types.id', '=', 'payrolls.payroll_type_id')
            ->where('payroll_types.name', 'Planilla de sueldos')
            ->where('payroll_details.employee_id',$employee->id)
            ->where('payroll_details.payroll_id', $payroll_id)
            ->get();

        $employee_name       = $employee->first_name . ' ' . $employee->last_name;
        $employee_salary     = ($salaryHistory != null) ? $this->moduleUtil->num_f($salaryHistory->new_salary, true) : null;
        $employee_position   = ($positionHistory != null) ? $positionHistory->newPosition1->value : null;
        $employee_hired_date = $this->employeeUtil->getDate($employee->date_admission, true);
        $business_name       = $business->name;
        $business_mobile     = ($business_location != null) ? $business_location->mobile : null;
        $business_email      = ($business_location != null) ? $business_location->email : null;
        $current_date        = $this->employeeUtil->getDate(Carbon::now(), false);
        $employee_bonus      = $payroll->

        $template = str_replace("employee_name", $employee_name, $salarialConstance->template);
        $template = str_replace("employee_salary", $employee_salary, $template);
        $template = str_replace("employee_position", $employee_position, $template);
        $template = str_replace("employee_hired_date", $employee_hired_date, $template);
        $template = str_replace("business_name", $business_name, $template);
        $template = str_replace("business_email", $business_email, $template);
        $template = str_replace("business_mobile", $business_mobile, $template);
        $template = str_replace("current_date", mb_strtolower($current_date), $template);

        $pdf = \PDF::loadView('rrhh.employees.salarial_constance', compact('salarialConstance', 'template'));

        $pdf->setPaper('letter', 'portrait');
        return $pdf->stream(__('rrhh.contract') . '.pdf');
    }
}
