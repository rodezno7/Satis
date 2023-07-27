<?php

namespace App\Http\Controllers;

use App\Business;
use Illuminate\Http\Request;
use App\Employees;
use App\RrhhContract;
use App\RrhhTypeContract;
use App\RrhhSalaryHistory;
use App\RrhhPositionHistory;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;
use App\Utils\ModuleUtil;
use App\Utils\EmployeeUtil;
use App\Utils\TransactionUtil;

class RrhhContractController extends Controller
{
    protected $moduleUtil;
    protected $employeeUtil;

    /**
     * Constructor
     *
     * @param ProductUtil $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, EmployeeUtil $employeeUtil, TransactionUtil $transactionUtil){
        $this->moduleUtil = $moduleUtil;
        $this->employeeUtil = $employeeUtil;
        $this->transactionUtil = $transactionUtil;
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


    public function getByEmployee($id) {
        if ( !auth()->user()->can('rrhh_contract.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $employee = Employees::where('id', $id)->where('business_id', $business_id)->first();
        $contracts = RrhhContract::join('rrhh_type_contracts as type', 'type.id', '=', 'rrhh_contracts.rrhh_type_contract_id')
        ->join('employees as employee', 'employee.id', '=', 'rrhh_contracts.employee_id')
        ->select('rrhh_contracts.id as id', 'type.name as type', 'rrhh_contracts.contract_start_date as contract_start_date', 'rrhh_contracts.contract_end_date as contract_end_date', 'rrhh_contracts.status as status')
        ->where('rrhh_contracts.employee_id', $employee->id)
        ->orderBy('id', 'DESC')
        ->get();

        $current_date = Carbon::now()->format('Y-m-d');

        foreach($contracts as $contract){
            if($contract->contract_end_date < $current_date){
                $contract->status = 0;
                $contract->update();
            }
        }
        
        return view('rrhh.contract.index', compact('contracts', 'employee'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    function create($id) {
        if ( !auth()->user()->can('rrhh_contract.create') ) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');
        $types = RrhhTypeContract::where('business_id', $business_id)->where('status', 1)->orderBy('id', 'DESC')->get();
        $employee_id = $id;

        return view('rrhh.contract.create', compact('employee_id', 'types'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if ( !auth()->user()->can('rrhh_contract.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rrhh_type_contract_id' => 'required',
            'employee_id'           => 'required',
            'contract_start_date'   => 'required',
            'contract_end_date'     => 'required',
        ]);

        try {
            $business_id      = request()->session()->get('user.business_id');
            $employee         = Employees::where('id', $request->employee_id)->where('business_id', $business_id)->first();
            $business         = Business::findOrFail($business_id);
            $positionHistory  = RrhhPositionHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
            $salaryHistory    = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
            $rrhhTypeContract = RrhhTypeContract::where('id', $request->rrhh_type_contract_id)->first();

            $input_details = $request->all();
            $input_details['contract_start_date']  = $this->moduleUtil->uf_date($request->input('contract_start_date'));
            $input_details['contract_end_date']    = $this->moduleUtil->uf_date($request->input('contract_end_date'));
            $input_details['employee_name']        = $employee->first_name.' '.$employee->last_name;
            $input_details['employee_age']         = $this->employeeUtil->getAge($employee->birth_date);
            $input_details['employee_dni']         = ($employee->dni != null)? $employee->dni : null;
            $input_details['employee_tax_number']  = ($employee->tax_number != null)? $employee->tax_number : null;
            $input_details['employee_state']       = ($employee->state_id != null)? $employee->state->name : null;
            $input_details['employee_city']        = ($employee->city_id != null)? $employee->city->name : null;
            $input_details['employee_salary']      = ($salaryHistory != null)? $salaryHistory->new_salary : null;
            $input_details['employee_department']  = ($positionHistory != null)? $positionHistory->newDepartment->value : null;
            $input_details['employee_position']    = ($positionHistory != null)? $positionHistory->newPosition1->value : null;
            $input_details['business_name']        = $business->name;
            $input_details['business_tax_number']  = ($business->tax_number != null)? $business->tax_number : null;
            $input_details['business_state']       = ($business->state_id != null)? $business->state->name : null;
            $input_details['current_date_letters'] = Carbon::now();
            $input_details['template']             = $rrhhTypeContract->template;
            
            DB::beginTransaction();
    
            $contract = RrhhContract::create($input_details);
    
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
     * @param  \App\RrhhContract  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request, $id){
        $business_id = request()->session()->get('user.business_id');
        $contract = RrhhContract::where('id', $id)->first();
        $type = RrhhTypeContract::where('id', $contract->rrhh_type_contract_id)->first();

        $contract_start_date  = $this->employeeUtil->getDateLetters($contract->contract_start_date);
        $contract_end_date    = $this->employeeUtil->getDateLetters($contract->contract_end_date);
        $employee_name        = $contract->employee_name;
        $employee_age         = $contract->employee_age;
        $employee_dni         = $this->employeeUtil->getNumberLetters($contract->employee_dni);
        $employee_tax_number  = $this->employeeUtil->getNumberLetters($contract->employee_tax_number);
        $employee_state       = $contract->employee_state;
        $employee_city        = $contract->employee_city;
        $employee_salary      = $this->transactionUtil->getAmountLetters($contract->employee_salary);
        $employee_department  = $contract->employee_department;
        $employee_position    = $contract->employee_position;
        $business_name        = $contract->business_name;
        $business_tax_number  = $this->employeeUtil->getNumberLetters($contract->business_tax_number);
        $business_state       = $contract->business_state;
        $current_date_letters = $this->employeeUtil->getDateLetters($contract->getDateLetters);

        $template = $contract->template;

        $template = str_replace("employee_name", $employee_name, $template);
        $template = str_replace("employee_age", $employee_age, $template);
        $template = str_replace("employee_dni", $employee_dni, $template);
        $template = str_replace("employee_tax_number", $employee_tax_number, $template);
        $template = str_replace("employee_state", $employee_state, $template);
        $template = str_replace("employee_city", $employee_city, $template);
        $template = str_replace("employee_salary", $employee_salary, $template);
        $template = str_replace("employee_department", $employee_department, $template);
        $template = str_replace("employee_position", $employee_position, $template);
        $template = str_replace("business_name", $business_name, $template);
        $template = str_replace("business_tax_number", $business_tax_number, $template);
        $template = str_replace("business_state", $business_state, $template);
        $template = str_replace("contract_start_date", $contract_start_date, $template);
        $template = str_replace("contract_end_date", $contract_end_date, $template);
        $template = str_replace("current_date_letters", $current_date_letters, $template);

         $pdf = \PDF::loadView('rrhh.contract.report_pdf', compact('contract', 'template'));

        $pdf->setPaper('letter', 'portrait');
        return $pdf->stream(__('rrhh.contract') . '.pdf');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\RrhhContract  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhContract $rrhhDocuments)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhContract  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if ( !auth()->user()->can('rrhh_contract.edit') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $contract = RrhhContract::findOrFail($id);
        $types = RrhhTypeContract::where('business_id', $business_id)->where('status', 1)->orderBy('id', 'DESC')->get();
        $employee_id = $contract->employee_id;

        return view('rrhh.contract.edit', compact('types', 'contract', 'employee_id'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhContract  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }


    public function updateContract(Request $request) {
        if ( !auth()->user()->can('rrhh_contract.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            //'rrhh_type_contract_id' => 'required',
            'employee_id'           => 'required',
            'contract_start_date'   => 'required',
            'contract_end_date'     => 'required',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');
            $employee = Employees::where('id', $request->employee_id)->where('business_id', $business_id)->first();
            $business = Business::findOrFail($business_id);
            $positionHistory = RrhhPositionHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
            $salaryHistory = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();

            $input_details = $request->only([
                //'rrhh_type_contract_id',
                'contract_start_date',
                'contract_end_date'
            ]);
            $input_details['contract_start_date']  = $this->moduleUtil->uf_date($request->input('contract_start_date'));
            $input_details['contract_end_date']    = $this->moduleUtil->uf_date($request->input('contract_end_date'));
            $input_details['employee_name']        = $employee->first_name.' '.$employee->last_name;
            $input_details['employee_age']         = $this->employeeUtil->getAge($employee->birth_date);
            $input_details['employee_dni']         = ($employee->dni != null)? $employee->dni : null;
            $input_details['employee_tax_number']  = ($employee->tax_number != null)? $employee->tax_number : null;
            $input_details['employee_state']       = ($employee->state_id != null)? $employee->state->name : null;
            $input_details['employee_city']        = ($employee->city_id != null)? $employee->city->name : null;
            $input_details['employee_salary']      = ($salaryHistory != null)? $salaryHistory->new_salary : null;
            $input_details['employee_department']  = ($positionHistory != null)? $positionHistory->newDepartment->value : null;
            $input_details['employee_position']    = ($positionHistory != null)? $positionHistory->newPosition1->value : null;
            $input_details['business_name']        = $business->name;
            $input_details['business_tax_number']  = ($business->tax_number != null)? $business->tax_number : null;
            $input_details['business_state']       = ($business->state_id != null)? $business->state->name : null;
            $input_details['current_date_letters'] = Carbon::now();
            
            DB::beginTransaction();
    
            $item = RrhhContract::where('id', $request->id)->where('employee_id', $request->employee_id)->first();
            $contract = $item->update($input_details);
    
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
     * @param  \App\RrhhContract  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        if (!auth()->user()->can('rrhh_contract.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $item = RrhhContract::findOrFail($id);
                $item->delete();
                
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


    public function createMassive(){
        if ( !auth()->user()->can('rrhh_contract.create') ) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');
        $employees = Employees::where('status', 1)->where('business_id', $business_id)->get();
        $types = RrhhTypeContract::where('business_id', $business_id)->where('status', 1)->orderBy('id', 'DESC')->get();

        return view('rrhh.contract.createAll', compact('employees', 'types'));
    }


    public function storeMassive(Request $request) {
        if ( !auth()->user()->can('rrhh_contract.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'contract_start_date'   => 'required',
            'contract_end_date'     => 'required',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');
            $employeesRequest = $request->employees;
            $employees = [];
            if($employeesRequest){
                foreach ($employeesRequest as $employeeId) {
                    $employee = Employees::where('id', $employeeId)->where('status', 1)->where('business_id', $business_id)->where('deleted_at', null)->first();
                    $employees[] = $employee;
                }
            }else{
                $employees = Employees::where('status', 1)->where('business_id', $business_id)->where('deleted_at', null)->get();
            }
            
            $business = Business::findOrFail($business_id);
            
            DB::beginTransaction();

            foreach($employees as $employee){

                $currentContract = RrhhContract::where('employee_id', $employee->id)->where('deleted_at', null)->orderBy('id', 'DESC')->first();
                if($currentContract){
                    if($currentContract->status == 1){
                        $currentContract->status = 0;
                        //$currentContract->contract_end_date = Carbon::now()->format('Y-m-d');
                        $currentContract->update();
                    }                    
    
                    $positionHistory = RrhhPositionHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
                    $salaryHistory = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
        
                    $input_details = $request->only([
                        'contract_start_date',
                        'contract_end_date'
                    ]);
                    
                    $input_details['employee_id']          = $employee->id;
                    $input_details['rrhh_type_contract_id']= $currentContract->rrhh_type_contract_id;
                    $input_details['contract_start_date']  = $this->moduleUtil->uf_date($request->input('contract_start_date'));
                    $input_details['contract_end_date']    = $this->moduleUtil->uf_date($request->input('contract_end_date'));
                    $input_details['employee_name']        = $employee->first_name.' '.$employee->last_name;
                    $input_details['employee_age']         = $this->employeeUtil->getAge($employee->birth_date);
                    $input_details['employee_dni']         = ($employee->dni != null)? $employee->dni : null;
                    $input_details['employee_tax_number']  = ($employee->tax_number != null)? $employee->tax_number : null;
                    $input_details['employee_state']       = ($employee->state_id != null)? $employee->state->name : null;
                    $input_details['employee_city']        = ($employee->city_id != null)? $employee->city->name : null;
                    $input_details['employee_salary']      = ($salaryHistory != null)? $salaryHistory->new_salary : null;
                    $input_details['employee_department']  = ($positionHistory != null)? $positionHistory->newDepartment->value : null;
                    $input_details['employee_position']    = ($positionHistory != null)? $positionHistory->newPosition1->value : null;
                    $input_details['business_name']        = $business->name;
                    $input_details['business_tax_number']  = ($business->tax_number != null)? $business->tax_number : null;
                    $input_details['business_state']       = ($business->state_id != null)? $business->state->name : null;
                    $input_details['current_date_letters'] = Carbon::now();
    
                    RrhhContract::create($input_details);
                }
                
            }
    
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

        return redirect('rrhh-employees')->with('status', $output);
    }
}
