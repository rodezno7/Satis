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
    public function __construct(ModuleUtil $moduleUtil, EmployeeUtil $employeeUtil, TransactionUtil $transactionUtil)
    {
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
    public function getByEmployee($id) 
    {
        if ( !auth()->user()->can('rrhh_contract.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $employee = Employees::where('id', $id)->where('business_id', $business_id)->first();
        $contracts = RrhhContract::join('rrhh_type_contracts as type', 'type.id', '=', 'rrhh_contracts.rrhh_type_contract_id')
        ->join('employees as employee', 'employee.id', '=', 'rrhh_contracts.employee_id')
        ->select('rrhh_contracts.id as id', 'type.name as type', 'rrhh_contracts.contract_start_date as contract_start_date', 'rrhh_contracts.contract_end_date as contract_end_date', 'rrhh_contracts.status as status')
        ->where('rrhh_contracts.employee_id', $employee->id)
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
    function create($id) 
    {
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
    public function store(Request $request) 
    {
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
            $business_id = request()->session()->get('user.business_id');
            $employee = Employees::where('id', $request->employee_id)->where('business_id', $business_id)->first();
            $business = Business::findOrFail($business_id);
            $positionHistory = RrhhPositionHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
            $salaryHistory = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();

            $input_details = $request->all();
            $input_details['contract_start_date']  = $this->moduleUtil->uf_date($request->input('contract_start_date'));
            $input_details['contract_end_date']    = $this->moduleUtil->uf_date($request->input('contract_end_date'));
            $input_details['name_employee']        = $employee->first_name.' '.$employee->last_name;
            $input_details['age_employee']         = $this->employeeUtil->getAge($employee->birth_date);
            $input_details['dni_employee']         = ($employee->dni != null)? $employee->dni : null;
            $input_details['tax_number_employee']  = ($employee->tax_number != null)? $employee->tax_number : null;
            $input_details['state_employee']       = ($employee->state_id != null)? $employee->state->name : null;
            $input_details['city_employee']        = ($employee->city_id != null)? $employee->city->name : null;
            $input_details['salary_employee']      = ($salaryHistory != null)? $salaryHistory->new_salary : null;
            $input_details['department_employee']  = ($positionHistory != null)? $positionHistory->newDepartment->value : null;
            $input_details['position_employee']    = ($positionHistory != null)? $positionHistory->newPosition1->value : null;
            $input_details['name_business']        = $business->name;
            $input_details['tax_number_business']  = ($business->tax_number != null)? $business->tax_number : null;
            $input_details['state_business']       = ($business->state_id != null)? $business->state->name : null;
            $input_details['current_date_letters'] = Carbon::now();
            
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
    public function generate(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $contract = RrhhContract::where('id', $id)->first();
        $type = RrhhTypeContract::where('id', $contract->rrhh_type_contract_id)->first();

        $contract_start_date  = $this->employeeUtil->getDateLetters($contract->contract_start_date);
        $contract_end_date    = $this->employeeUtil->getDateLetters($contract->contract_end_date);
        $name_employee        = $contract->name_employee;
        $age_employee         = $contract->age_employee;
        $dni_employee         = $this->employeeUtil->getNumberLetters($contract->dni_employee);
        $tax_number_employee  = $this->employeeUtil->getNumberLetters($contract->tax_number_employee);
        $state_employee       = $contract->state_employee;
        $city_employee        = $contract->city_employee;
        $salary_employee      = $this->transactionUtil->getAmountLetters($contract->salary_employee);
        $department_employee  = $contract->department_employee;
        $position_employee    = $contract->position_employee;
        $name_business        = $contract->name_business;
        $tax_number_business  = $this->employeeUtil->getNumberLetters($contract->tax_number_business);
        $state_business       = $contract->state_business;
        $current_date_letters = $this->employeeUtil->getDateLetters($contract->getDateLetters);

        $template = $contract->rrhhTypeContract->template;

        $template = str_replace("name_employee", $name_employee, $template);
        $template = str_replace("age_employee", $age_employee, $template);
        $template = str_replace("dni_employee", $dni_employee, $template);
        $template = str_replace("tax_number_employee", $tax_number_employee, $template);
        $template = str_replace("state_employee", $state_employee, $template);
        $template = str_replace("city_employee", $city_employee, $template);
        $template = str_replace("salary_employee", $salary_employee, $template);
        $template = str_replace("department_employee", $department_employee, $template);
        $template = str_replace("position_employee", $position_employee, $template);
        $template = str_replace("name_business", $name_business, $template);
        $template = str_replace("tax_number_business", $tax_number_business, $template);
        $template = str_replace("state_business", $state_business, $template);
        $template = str_replace("contract_start_date", $contract_start_date, $template);
        $template = str_replace("contract_end_date", $contract_end_date, $template);
        $template = str_replace("current_date_letters", $current_date_letters, $template);

         $pdf = \PDF::loadView('rrhh.contract.report_pdf', compact('contract', 'template', 'city_employee'));

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
    public function edit($id) 
    {
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

    public function updateContract(Request $request) 
    {

        if ( !auth()->user()->can('rrhh_contract.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rrhh_type_contract_id' => 'required',
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
                'rrhh_type_contract_id',
                'contract_start_date',
                'contract_end_date'
            ]);
            $input_details['contract_start_date']  = $this->moduleUtil->uf_date($request->input('contract_start_date'));
            $input_details['contract_end_date']    = $this->moduleUtil->uf_date($request->input('contract_end_date'));
            $input_details['name_employee']        = $employee->first_name.' '.$employee->last_name;
            $input_details['age_employee']         = $this->employeeUtil->getAge($employee->birth_date);
            $input_details['dni_employee']         = ($employee->dni != null)? $employee->dni : null;
            $input_details['tax_number_employee']  = ($employee->tax_number != null)? $employee->tax_number : null;
            $input_details['state_employee']       = ($employee->state_id != null)? $employee->state->name : null;
            $input_details['city_employee']        = ($employee->city_id != null)? $employee->city->name : null;
            $input_details['salary_employee']      = ($salaryHistory != null)? $salaryHistory->new_salary : null;
            $input_details['department_employee']  = ($positionHistory != null)? $positionHistory->newDepartment->value : null;
            $input_details['position_employee']    = ($positionHistory != null)? $positionHistory->newPosition1->value : null;
            $input_details['name_business']        = $business->name;
            $input_details['tax_number_business']  = ($business->tax_number != null)? $business->tax_number : null;
            $input_details['state_business']       = ($business->state_id != null)? $business->state->name : null;
            $input_details['current_date_letters'] = Carbon::now();
            
            DB::beginTransaction();
    
            //$contract = RrhhContract::create($input_details);
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
}
