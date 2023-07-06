<?php

namespace App\Http\Controllers;

use App\Employees;
use App\User;
use App\Business;
use App\RrhhPersonnelAction;
use App\RrhhSalaryHistory;
use App\RrhhPositionHistory;
use App\Bank;
use App\RrhhPersonnelActionAuthorizer;
use App\RrhhTypePersonnelAction;
use App\RrhhPersonnelActionFile;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use App\Notifications\PersonnelActionNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notification;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\Hash;

class RrhhPersonnelActionController extends Controller
{
    protected $moduleUtil;
    private $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtil $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('rrhh_personnel_action.authorize')) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('rrhh.personnel_actions.index_by_authorizer');
    }

    public function getByAuthorizer()
    {
        if (!auth()->user()->can('rrhh_personnel_action.authorize')) {
            abort(403, 'Unauthorized action.');
        }
        $user_id = auth()->user()->id;
        $data = DB::table('rrhh_personnel_actions as personnel_action')
            ->join('rrhh_type_personnel_actions as type', 'type.id', '=', 'personnel_action.rrhh_type_personnel_action_id')
            ->join('rrhh_personnel_action_authorizers as personnel_action_authorizer', 'personnel_action_authorizer.rrhh_personnel_action_id', '=', 'personnel_action.id')
            ->join('employees as employees', 'employees.id', '=', 'personnel_action.employee_id')
            ->select('personnel_action.id as id', DB::raw("CONCAT(employees.first_name, ' ', employees.last_name) as full_name"), 'personnel_action.created_at as created_at', 'personnel_action.authorization_date as 	authorization_date', 'type.name as type', 'personnel_action.status as status', 'personnel_action_authorizer.authorized as authorized')
            ->where('personnel_action_authorizer.user_id', $user_id)
            ->get();
        
        return DataTables::of($data)->editColumn('created_at', '{{ @format_date($created_at) }} {{ @format_time($created_at) }}')
        ->editColumn('authorization_date', function ($data) {
            return ($data->authorization_date != null)? $this->transactionUtil->format_date($data->authorization_date): '---';
        })->addColumn('authorizations', function ($data) {
            $authorizations = RrhhPersonnelActionAuthorizer::where('rrhh_personnel_action_id', $data->id)->where('authorized', 1)->get();
            $authorize = RrhhPersonnelActionAuthorizer::where('rrhh_personnel_action_id', $data->id)->get();
            return count($authorizations).' '.__('rrhh.of').' '.count($authorize);
        })->toJson();
    }

    public function getByEmployee($id)
    {
        if (!auth()->user()->can('rrhh_personnel_action.view')) {
            abort(403, 'Unauthorized action.');
        }

        $employee = Employees::findOrFail($id);
        $personnelActions = DB::table('rrhh_personnel_actions as personnel_action')
            ->join('rrhh_type_personnel_actions as type', 'type.id', '=', 'personnel_action.rrhh_type_personnel_action_id')
            ->select('personnel_action.id as id', 'personnel_action.description as description', 'personnel_action.created_at as created_at', 'type.name as type', 'type.required_authorization as required_authorization', 'personnel_action.status as status')
            ->where('personnel_action.employee_id', $employee->id)
            ->get();

        $personnelActionAuthorizers = RrhhPersonnelActionAuthorizer::all();
        
        return view('rrhh.personnel_actions.index', compact('personnelActions', 'employee', 'personnelActionAuthorizers'));
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

    function createPersonnelAction($id)
    {
        if (!auth()->user()->can('rrhh_personnel_action.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $actions = DB::table('rrhh_action_type')->orderBy('id', 'DESC')->get();
        $typesPersonnelActions = RrhhTypePersonnelAction::where('business_id', $business_id)->get();
        $departments = DB::table('rrhh_datas')->where('rrhh_header_id', 2)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $positions = DB::table('rrhh_datas')->where('rrhh_header_id', 3)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $payments = DB::table('rrhh_datas')->where('rrhh_header_id', 8)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $banks = Bank::where('business_id', $business_id)->orderBy('name', 'ASC')->pluck('name', 'id');

        $users = DB::table('users as user')
            ->join('model_has_roles as user_role', 'user_role.model_id', '=', 'user.id')
            ->join('roles as roles', 'roles.id', '=', 'user_role.role_id')
            ->join('role_has_permissions as role_permission', 'role_permission.role_id', '=', 'roles.id')
            ->join('permissions as permissions', 'permissions.id', '=', 'role_permission.permission_id')
            ->select('user.id as id', 'user.first_name as first_name', 'user.last_name as last_name', 'user.email as email')
            ->where('permissions.name', 'rrhh_personnel_action.authorize')
            ->where('user.business_id', $business_id)
            ->where('user.deleted_at', null)
            ->get();

        $employee = Employees::findOrFail($id);

        $positionHistory = RrhhPositionHistory::where('employee_id', $id)
            ->where('current', 1)
            ->orderBy('id', 'DESC')
            ->first();

        $salaryHistory = RrhhSalaryHistory::where('employee_id', $id)
            ->where('current', 1)
            ->orderBy('id', 'DESC')
            ->first();

        return view('rrhh.personnel_actions.create', compact('actions', 'typesPersonnelActions', 'employee', 'positionHistory', 'salaryHistory', 'departments', 'positions', 'payments', 'banks', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('rrhh_personnel_action.create')) {
            abort(403, 'Unauthorized action.');
        }
        $requiredUser = 'nullable';
        $requiredDepartment = 'nullable';
        $requiredPosition = 'nullable';
        $requiredSalary = 'nullable';
        $requiredStartDate = 'nullable';
        $requiredEndDate = 'nullable';
        $requiredBankAccount = 'nullable';
        $requiredPayment = 'nullable';
        $requiredBank = 'nullable';
        $requiredEffectiveDate = 'nullable';

        if ($request->rrhh_type_personnel_action_id != null) {
            $business_id = request()->session()->get('user.business_id');
            $type = RrhhTypePersonnelAction::where('business_id', $business_id)->where('id', $request->rrhh_type_personnel_action_id)->first();

            if ($type->required_authorization == 1) { // Requiere autorizacion la accion de personal
                $requiredUser = 'required';
            }

            $actions = DB::table('rrhh_action_type')->orderBy('id', 'DESC')->get();
            foreach ($actions as $action) {
                if ($action->rrhh_type_personnel_action_id == $type->id) {

                    if ($action->rrhh_required_action_id == 2) { // Cambiar departamento/puesto
                        $requiredDepartment = 'required';
                        $requiredPosition = 'required';
                        $requiredEffectiveDate = 'required';
                    }

                    if ($action->rrhh_required_action_id == 3) { // Cambiar salario
                        $requiredSalary = 'required';
                        $requiredEffectiveDate = 'required';
                    }

                    if ($action->rrhh_required_action_id == 4) { // Seleccionar un periodo en específico
                        $requiredStartDate = 'required';
                        $requiredEndDate = 'required';
                    }

                    if ($action->rrhh_required_action_id == 5) { // Cambiar cuenta bancaria
                        $requiredBankAccount = 'required';
                    }

                    if ($action->rrhh_required_action_id == 6) { // Cambiar forma de pago
                        $requiredBankAccount = 'required';
                        $requiredPayment = 'required';
                        $requiredBank = 'required';
                    }

                    if ($action->rrhh_required_action_id == 7) { // Seleccionar la fecha en que entra en vigor
                        $requiredEffectiveDate = 'required';
                    }
                }
            }
        }

        $request->validate([
            'rrhh_type_personnel_action_id' => 'required',
            'description'                   => 'required',
            'user_id'                       => $requiredUser,
            'department_id'                 => $requiredDepartment,
            'position1_id'                  => $requiredPosition,
            'new_salary'                    => $requiredSalary,
            'start_date'                    => $requiredStartDate,
            'end_date'                      => $requiredEndDate,
            'bank_account'                  => $requiredBankAccount,
            'payment_id'                    => $requiredPayment,
            'bank_id'                       => $requiredBank,
            'effective_date'                => $requiredEffectiveDate,
        ]);

        try {
            $input_details = $request->only(['rrhh_type_personnel_action_id', 'description', 'employee_id']);
            $input_details['user_id'] = auth()->user()->id;
            $employee = Employees::findOrFail($request->employee_id);
            $positionHistory = '';
            $salaryHistory = '';
            DB::beginTransaction();

            $actions = DB::table('rrhh_action_type')->orderBy('id', 'DESC')->get();
            foreach ($actions as $action) {
                if ($action->rrhh_type_personnel_action_id == $type->id) {
                    if ($action->rrhh_required_action_id == 1 && $type->required_authorization == 0) { // Cambiar estado de empleado (De inactivo a activo)
                        $employee->status = 1;
                        $employee->update();
                    }

                    if ($action->rrhh_required_action_id == 2) { // Cambiar departamento/puesto
                        if ($type->required_authorization == 0) {
                            RrhhPositionHistory::where('employee_id', $employee->id)->update(['current' => 0]);
                            $positionHistory = RrhhPositionHistory::create(
                                ['department_id' => $request->input('department_id'), 'position1_id' => $request->input('position1_id'), 'employee_id' => $employee->id, 'current' => 1]
                            );
                        }
                        else{
                            $positionHistory = RrhhPositionHistory::create(
                                ['department_id' => $request->input('department_id'), 'position1_id' => $request->input('position1_id'), 'employee_id' => $employee->id]
                            );
                        }
                        $input_details['effective_date'] = $this->moduleUtil->uf_date($request->input('effective_date'));
                    }

                    if ($action->rrhh_required_action_id == 3) { // Cambiar salario
                        if ($type->required_authorization == 0) {
                            RrhhSalaryHistory::where('employee_id', $employee->id)->update(['current' => 0]);
                            $salaryHistory = RrhhSalaryHistory::create(
                                ['employee_id' => $employee->id, 'salary' => $request->input('new_salary'), 'current' => 1]
                            );
                        }else{
                            $salaryHistory = RrhhSalaryHistory::create(
                                ['employee_id' => $employee->id, 'salary' => $request->input('new_salary')]
                            );
                        }
                        $input_details['effective_date'] = $this->moduleUtil->uf_date($request->input('effective_date'));
                    }

                    if ($action->rrhh_required_action_id == 4) { // Seleccionar un periodo en específico
                        $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));
                        $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));
                    }

                    if ($action->rrhh_required_action_id == 5) { // Cambiar cuenta bancaria
                        if ($type->required_authorization == 0) {
                            $input_employee = $request->only(['bank_account']);
                            $employee->update($input_employee);
                        } else {
                            $input_details['bank_account'] = $request->input('bank_account');
                        }
                    }

                    if ($action->rrhh_required_action_id == 6) { // Cambiar forma de pago
                        if ($type->required_authorization == 0) {
                            $input_employee = $request->only(['payment_id', 'bank_id', 'bank_account']);
                            $employee->update($input_employee);
                        } else {
                            $input_details['payment_id'] = $request->input('payment_id');
                            $input_details['bank_id'] = $request->input('bank_id');
                            $input_details['bank_account'] = $request->input('bank_account');
                        }
                    }

                    if ($action->rrhh_required_action_id == 7) { // Seleccionar la fecha en que entra en vigor
                        $input_details['effective_date'] = $this->moduleUtil->uf_date($request->input('effective_date'));
                    }

                    if ($action->rrhh_required_action_id == 8 && $type->required_authorization == 0) { // Cambiar estado de empleado (De inactivo a activo)
                        $employee->status = 0;
                        $employee->update();
                    }
                }
            }
            if ($type->required_authorization == 1) { // Requiere autorizacion la accion de personal
                $input_details['status'] = 'No autorizada (En tramite)';
            }

            $personnelAction = RrhhPersonnelAction::create($input_details);

            foreach ($actions as $action) {
                if ($action->rrhh_type_personnel_action_id == $type->id) {
                    if ($action->rrhh_required_action_id == 2) { // Cambiar departamento/puesto
                        RrhhPositionHistory::where('employee_id', $employee->id)->where('id', $positionHistory->id)->orderBy('id', 'DESC')->update(['rrhh_personnel_action_id' => $personnelAction->id]);
                    }

                    if ($action->rrhh_required_action_id == 3) { // Cambiar salario
                        RrhhSalaryHistory::where('employee_id', $employee->id)->where('id', $salaryHistory->id)->orderBy('id', 'DESC')->update(['rrhh_personnel_action_id' => $personnelAction->id]);
                    }
                }
            }

            if ($type->required_authorization == 1) { // Requiere autorizacion la accion de personal
                $users = $request->input('user_id');
                foreach ($users as $userID) {
                    $user = User::findOrFail($userID);
                    RrhhPersonnelActionAuthorizer::insert(
                        ['rrhh_personnel_action_id' => $personnelAction->id, 'user_id' => $userID]
                    );
                    $user->notify(new PersonnelActionNotification($user->first_name, $user->last_name, $type->name, $employee->first_name, $employee->last_name));
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

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RrhhPersonnelAction  $rrhhPersonnelAction
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhPersonnelAction $rrhhPersonnelAction)
    {

    }

    public function viewPersonnelAction(RrhhPersonnelAction $rrhhPersonnelAction, $id)
    {
        $user_id = auth()->user()->id;
        $personnelAction = DB::table('rrhh_personnel_actions as personnel_action')
            ->join('rrhh_type_personnel_actions as type', 'type.id', '=', 'personnel_action.rrhh_type_personnel_action_id')
            ->join('users as user', 'user.id', '=', 'personnel_action.user_id')
            ->select('personnel_action.id as id', 'personnel_action.description as description', 'personnel_action.created_at as created_at', 'personnel_action.authorization_date as authorization_date', 'personnel_action.effective_date as effective_date', 'personnel_action.status as status', 'personnel_action.employee_id as employee_id', 'personnel_action.bank_account as bank_account', 'type.name as type', 'type.id as type_id', 'user.first_name as first_name', 'user.last_name as last_name')
            ->where('personnel_action.id', $id)
            ->get();

        $payment = DB::table('rrhh_datas as payment')
            ->join('rrhh_personnel_actions as personnel_action', 'payment.id', '=', 'personnel_action.payment_id')
            ->select('payment.value as name')
            ->where('personnel_action.id', $id)
            ->where('payment.rrhh_header_id', 8)
            ->get();

        $bank = DB::table('banks as bank')
            ->join('rrhh_personnel_actions as personnel_action', 'bank.id', '=', 'personnel_action.bank_id')
            ->select('bank.name as name')
            ->where('personnel_action.id', $id)
            ->get();

        $employee = Employees::findOrFail($personnelAction[0]->employee_id);

        $previousSalary = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->first(); 
        $newSalary = RrhhSalaryHistory::where('employee_id', $employee->id)->where('rrhh_personnel_action_id', $personnelAction[0]->id)->first(); 
 
        $previousPosition = RrhhPositionHistory::where('employee_id', $employee->id)->where('current', 1)->first(); 
        $newPosition = RrhhPositionHistory::where('employee_id', $employee->id)->where('rrhh_personnel_action_id', $personnelAction[0]->id)->first(); 
 
        //$positions = RrhhPositionHistory::where('employee_id', $employee->id)->orderBy('id', 'DESC')->take(2)->get();
        $users = RrhhPersonnelActionAuthorizer::where('rrhh_personnel_action_id', $personnelAction[0]->id)->get();
        $actions = DB::table('rrhh_action_type')->where('rrhh_type_personnel_action_id', $personnelAction[0]->type_id)->orderBy('id', 'DESC')->get();
                    
        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        
        return view('rrhh.personnel_actions.view',
            compact('personnelAction', 'previousSalary', 'newSalary', 'previousPosition', 'newPosition', 'business', 'actions', 'employee', 'users', 'payment', 'bank'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhPersonnelAction  $rrhhPersonnelAction
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('rrhh_personnel_action.edit')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $personnelAction = DB::table('rrhh_personnel_actions as personnel_action')
            ->join('rrhh_type_personnel_actions as type', 'type.id', '=', 'personnel_action.rrhh_type_personnel_action_id')
            ->join('users as user', 'user.id', '=', 'personnel_action.user_id')
            ->select('personnel_action.id as id', 'personnel_action.description as description', 'personnel_action.start_date as start_date', 'personnel_action.end_date as end_date', 'personnel_action.created_at as created_at', 'personnel_action.authorization_date as authorization_date', 'personnel_action.effective_date as effective_date', 'personnel_action.status as status', 'personnel_action.employee_id as employee_id', 'personnel_action.bank_account as bank_account', 'type.name as type', 'type.required_authorization as required_authorization', 'type.id as type_id', 'user.first_name as first_name', 'user.last_name as last_name')
            ->where('personnel_action.id', $id)
            ->get();

        $actions = DB::table('rrhh_action_type')->orderBy('id', 'DESC')->get();
        $departments = DB::table('rrhh_datas')->where('rrhh_header_id', 2)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $positions = DB::table('rrhh_datas')->where('rrhh_header_id', 3)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $payments = DB::table('rrhh_datas')->where('rrhh_header_id', 8)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $banks = Bank::where('business_id', $business_id)->orderBy('name', 'ASC')->pluck('name', 'id');

        $users = DB::table('users as user')
            ->join('model_has_roles as user_role', 'user_role.model_id', '=', 'user.id')
            ->join('roles as roles', 'roles.id', '=', 'user_role.role_id')
            ->join('role_has_permissions as role_permission', 'role_permission.role_id', '=', 'roles.id')
            ->join('permissions as permissions', 'permissions.id', '=', 'role_permission.permission_id')
            ->select('user.id as id', 'user.first_name as first_name', 'user.last_name as last_name', 'user.email as email')
            ->where('permissions.name', 'rrhh_personnel_action.authorize')
            ->where('user.business_id', $business_id)
            ->where('user.deleted_at', null)
            ->get();
        
        $authorizers = RrhhPersonnelActionAuthorizer::where('rrhh_personnel_action_id', $personnelAction[0]->id)->get();
        $employee = Employees::findOrFail($personnelAction[0]->employee_id);
        
        $positionHistory = RrhhPositionHistory::where('employee_id', $employee->id)->where('rrhh_personnel_action_id', $personnelAction[0]->id)->first();
        
        $previousSalary = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->first(); 
        $newSalary = RrhhSalaryHistory::where('employee_id', $employee->id)->where('rrhh_personnel_action_id', $personnelAction[0]->id)->first(); 

        return view('rrhh.personnel_actions.edit', compact('actions', 'personnelAction', 'employee', 'positionHistory', 'previousSalary', 'newSalary', 'departments', 'positions', 'payments', 'banks', 'users', 'authorizers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhPersonnelAction  $rrhhPersonnelAction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function updatePersonnelAction(Request $request)
    {
        if (!auth()->user()->can('rrhh_personnel_action.edit')) {
            abort(403, 'Unauthorized action.');
        }
        $personnelAction = RrhhPersonnelAction::findOrFail($request->input('id'));
        if($personnelAction->status != 'Autorizada')
        {
            $requiredUser = 'nullable';
            $requiredDepartment = 'nullable';
            $requiredPosition = 'nullable';
            $requiredSalary = 'nullable';
            $requiredStartDate = 'nullable';
            $requiredEndDate = 'nullable';
            $requiredBankAccount = 'nullable';
            $requiredPayment = 'nullable';
            $requiredBank = 'nullable';
            $requiredEffectiveDate = 'nullable';

            $business_id = request()->session()->get('user.business_id');
            $type = RrhhTypePersonnelAction::where('business_id', $business_id)->where('id', $personnelAction->rrhh_type_personnel_action_id)->first();

            if ($type->required_authorization == 1) { // Requiere autorizacion la accion de personal
                $requiredUser = 'required';
            }

            $actions = DB::table('rrhh_action_type')->orderBy('id', 'DESC')->get();
            foreach ($actions as $action) {
                if ($action->rrhh_type_personnel_action_id == $type->id) {

                    if ($action->rrhh_required_action_id == 2) { // Cambiar departamento/puesto
                        $requiredDepartment = 'required';
                        $requiredPosition = 'required';
                        $requiredEffectiveDate = 'required';
                    }

                    if ($action->rrhh_required_action_id == 3) { // Cambiar salario
                        $requiredSalary = 'required';
                        $requiredEffectiveDate = 'required';
                    }

                    if ($action->rrhh_required_action_id == 4) { // Seleccionar un periodo en específico
                        $requiredStartDate = 'required';
                        $requiredEndDate = 'required';
                    }

                    if ($action->rrhh_required_action_id == 5) { // Cambiar cuenta bancaria
                        $requiredBankAccount = 'required';
                    }

                    if ($action->rrhh_required_action_id == 6) { // Cambiar forma de pago
                        $requiredBankAccount = 'required';
                        $requiredPayment = 'required';
                        $requiredBank = 'required';
                    }

                    if ($action->rrhh_required_action_id == 7) { // Seleccionar la fecha en que entra en vigor
                        $requiredEffectiveDate = 'required';
                    }
                }
            }

            $request->validate([
                'description'                   => 'required',
                'user_id'                       => $requiredUser,
                'department_id'                 => $requiredDepartment,
                'position1_id'                  => $requiredPosition,
                'new_salary'                    => $requiredSalary,
                'start_date'                    => $requiredStartDate,
                'end_date'                      => $requiredEndDate,
                'bank_account'                  => $requiredBankAccount,
                'payment_id'                    => $requiredPayment,
                'bank_id'                       => $requiredBank,
                'effective_date'                => $requiredEffectiveDate,
            ]);

            try {
                $input_details['description'] = $request->input('description');
                $input_details['user_id'] = auth()->user()->id;
                $employee = Employees::findOrFail($request->input('employee_id'));
                $positionHistory = '';
                $salaryHistory = '';
                DB::beginTransaction();

                $actions = DB::table('rrhh_action_type')->orderBy('id', 'DESC')->get();
                foreach ($actions as $action) {
                    if ($action->rrhh_type_personnel_action_id == $type->id) {
                        if ($action->rrhh_required_action_id == 1 && $type->required_authorization == 0) { // Cambiar estado de empleado (De inactivo a activo)
                            $employee->status = 1;
                            $employee->update();
                        }

                        if ($action->rrhh_required_action_id == 2) { // Cambiar departamento/puesto
                            $positionHistory = RrhhPositionHistory::where('rrhh_personnel_action_id', $personnelAction->id)->orderBy('id', 'DESC')->first();

                            if($positionHistory != null){
                                if ($type->required_authorization == 0) {
                                    $positionHistory->update(
                                        ['department_id' => $request->input('department_id'), 'position1_id' => $request->input('position1_id'), 'employee_id' => $employee->id, 'current' => 1]
                                    );
                                }
                                else{
                                    $positionHistory->update(
                                        ['department_id' => $request->input('department_id'), 'position1_id' => $request->input('position1_id'), 'employee_id' => $employee->id]
                                    );
                                }
                            }else{
                                if ($type->required_authorization == 0) {
                                    RrhhPositionHistory::where('employee_id', $employee->id)->update(['current' => 0]);
                                    $positionHistory = RrhhPositionHistory::create(
                                        ['department_id' => $request->input('department_id'), 'position1_id' => $request->input('position1_id'), 'employee_id' => $employee->id, 'current' => 1]
                                    );
                                }
                                else{
                                    $positionHistory = RrhhPositionHistory::create(
                                        ['department_id' => $request->input('department_id'), 'position1_id' => $request->input('position1_id'), 'employee_id' => $employee->id]
                                    );
                                }
                            }
                            $input_details['effective_date'] = $this->moduleUtil->uf_date($request->input('effective_date'));
                        }

                        if ($action->rrhh_required_action_id == 3) { // Cambiar salario
                            $salaryHistory = RrhhSalaryHistory::where('rrhh_personnel_action_id', $personnelAction->id)->orderBy('id', 'DESC')->first();
                            if($salaryHistory != null){
                                if ($type->required_authorization == 0) {
                                    $salaryHistory->update(
                                        ['employee_id' => $employee->id, 'salary' => $request->input('new_salary'), 'current' => 1]
                                    );
                                }else{
                                    $salaryHistory->update(
                                        ['employee_id' => $employee->id, 'salary' => $request->input('new_salary')]
                                    );
                                }
                            }else{
                                if ($type->required_authorization == 0) {
                                    RrhhSalaryHistory::where('employee_id', $employee->id)->update(['current' => 0]);
                                    $salaryHistory = RrhhSalaryHistory::create(
                                        ['employee_id' => $employee->id, 'salary' => $request->input('new_salary'), 'current' => 1]
                                    );
                                }else{
                                    $salaryHistory = RrhhSalaryHistory::create(
                                        ['employee_id' => $employee->id, 'salary' => $request->input('new_salary')]
                                    );
                                }
                            }

                            $input_details['effective_date'] = $this->moduleUtil->uf_date($request->input('effective_date'));
                        }

                        if ($action->rrhh_required_action_id == 4) { // Seleccionar un periodo en específico
                            $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));
                            $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));
                        }

                        if ($action->rrhh_required_action_id == 5) { // Cambiar cuenta bancaria
                            if ($type->required_authorization == 0) {
                                $input_employee = $request->only(['bank_account']);
                                $employee->update($input_employee);
                            } else {
                                $input_details['bank_account'] = $request->input('bank_account');
                            }
                        }

                        if ($action->rrhh_required_action_id == 6) { // Cambiar forma de pago
                            if ($type->required_authorization == 0) {
                                $input_employee = $request->only(['payment_id', 'bank_id', 'bank_account']);
                                $employee->update($input_employee);
                            } else {
                                $input_details['payment_id'] = $request->input('payment_id');
                                $input_details['bank_id'] = $request->input('bank_id');
                                $input_details['bank_account'] = $request->input('bank_account');
                            }
                        }

                        if ($action->rrhh_required_action_id == 7) { // Seleccionar la fecha en que entra en vigor
                            $input_details['effective_date'] = $this->moduleUtil->uf_date($request->input('effective_date'));
                        }

                        if ($action->rrhh_required_action_id == 8 && $type->required_authorization == 0) { // Cambiar estado de empleado (De inactivo a activo)
                            $employee->status = 0;
                            $employee->update();
                        }
                    }
                }                
                
                if ($type->required_authorization == 1) { // Requiere autorizacion la accion de personal
                    $input_details['status'] = 'No autorizada (En tramite)';
                }

                $personnelAction->update($input_details);

                foreach ($actions as $action) {
                    if ($action->rrhh_type_personnel_action_id == $type->id) {
                        if ($action->rrhh_required_action_id == 2) { // Cambiar departamento/puesto
                            $positionHistory->update(['rrhh_personnel_action_id' => $personnelAction->id]);
                        }

                        if ($action->rrhh_required_action_id == 3) { // Cambiar salario
                            $salaryHistory->update(['rrhh_personnel_action_id' => $personnelAction->id]);
                        }
                    }
                }

                if ($type->required_authorization == 1) { // Requiere autorizacion la accion de personal
                    $users = $request->input('user_id');
                    RrhhPersonnelActionAuthorizer::where('rrhh_personnel_action_id', $personnelAction->id)->delete();
                    foreach ($users as $userID) {
                        $user = User::findOrFail($userID);
                        RrhhPersonnelActionAuthorizer::insert(
                            ['rrhh_personnel_action_id' => $personnelAction->id, 'user_id' => $userID]
                        );
                        $user->notify(new PersonnelActionNotification($user->first_name, $user->last_name, $type->name, $employee->first_name, $employee->last_name));
                    }
                }

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
        }else{
            $output = [
                'success' => 0,
                'msg' => __('rrhh.unauthorized_action')
            ];
        }

        return $output;
    }

    /** Authorizer personnel action */
    function confirmAuthorization(Request $request, $id)
    {
        if (!auth()->user()->can('rrhh_personnel_action.view')) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($request->ajax()) {
            try{
                DB::beginTransaction();

                $personnelAction = RrhhPersonnelAction::findOrFail($id);
                $user_id = auth()->user()->id;
                $user = User::findOrFail($user_id);

                if (Hash::check($request->input('password'), $user->password)) {
                    $business_id = request()->session()->get('user.business_id');
                    $type = RrhhTypePersonnelAction::where('business_id', $business_id)->where('id', $personnelAction->rrhh_type_personnel_action_id)->first();
                    $employee = Employees::findOrFail($personnelAction->employee_id);
                    $actions = DB::table('rrhh_action_type')->where('rrhh_type_personnel_action_id', $type->id)->orderBy('id', 'DESC')->get();
                    foreach ($actions as $action) {
                        if ($action->rrhh_required_action_id == 1) { // Cambiar estado de empleado (De inactivo a activo)
                            $employee->status = 1;
                            $employee->update();
                        }
        
                        if ($action->rrhh_required_action_id == 2) { // Cambiar departamento/puesto
                            RrhhPositionHistory::where('employee_id', $employee->id)->update(['current' => 0]);
                            $lastPosition = RrhhPositionHistory::where('employee_id', $employee->id)->orderBy('id', 'DESC')->take(1)->update(['current' => 1]);
                        }
        
                        if ($action->rrhh_required_action_id == 3) { // Cambiar salario
                            RrhhSalaryHistory::where('employee_id', $employee->id)->update(['current' => 0]);
                            $lastSalary = RrhhSalaryHistory::where('employee_id', $employee->id)->orderBy('id', 'DESC')->take(1)->update(['current' => 1]);
                        }
        
                        if ($action->rrhh_required_action_id == 5) { // Cambiar cuenta bancaria
                            $employee->bank_account = $personnelAction->bank_account;
                            $employee->update();
                        }
        
                        if ($action->rrhh_required_action_id == 6) { // Cambiar forma de pago
                            $employee->payment_id = $personnelAction->payment_id;
                            $employee->bank_id = $personnelAction->bank_id;
                            $employee->bank_account = $personnelAction->bank_account;
                            $employee->update();
                        }
        
                        if ($action->rrhh_required_action_id == 8) { // Cambiar estado de empleado (De inactivo a activo)
                            $employee->status = 0;
                            $employee->update();
                       }
                        
                    }

                    RrhhPersonnelActionAuthorizer::where('rrhh_personnel_action_id', $personnelAction->id)->where('user_id', $user_id)->update(['authorized' => 1]);
                    $users = RrhhPersonnelActionAuthorizer::where('rrhh_personnel_action_id', $personnelAction->id)->where('authorized', 0)->get();

                    if (count($users) == 0) { // Requiere autorizacion la accion de personal
                        $personnelAction->status = 'Autorizada';
                        $personnelAction->authorization_date = Carbon::now();
                        $personnelAction->update();
                    }
        
                    $output = ['success' => 1,
                        'msg' => __('rrhh.authorized_successfully')
                    ];
                } else {
                    $output = ['success' => 0,
                        'msg' => __('rrhh.wrong_password_authorize')
                    ];
                }
                DB::commit();
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

    /** Generate report of personnel action */
    public function authorizationReport($id, $employee_id = null)
    {
        $user_id = auth()->user()->id;
        $personnelAction = DB::table('rrhh_personnel_actions as personnel_action')
            ->join('rrhh_type_personnel_actions as type', 'type.id', '=', 'personnel_action.rrhh_type_personnel_action_id')
            ->join('users as user', 'user.id', '=', 'personnel_action.user_id')
            ->select('personnel_action.id as id', 'personnel_action.description as description', 'personnel_action.created_at as created_at', 'personnel_action.authorization_date as authorization_date', 'personnel_action.effective_date as effective_date', 'personnel_action.status as status', 'personnel_action.employee_id as employee_id', 'personnel_action.bank_account as bank_account', 'type.name as type', 'type.id as type_id', 'user.first_name as first_name', 'user.last_name as last_name')
            ->where('personnel_action.id', $id)
            ->get();

        $payment = DB::table('rrhh_datas as payment')
            ->join('rrhh_personnel_actions as personnel_action', 'payment.id', '=', 'personnel_action.payment_id')
            ->select('payment.value as name')
            ->where('personnel_action.id', $id)
            ->where('payment.rrhh_header_id', 8)
            ->get();

        $bank = DB::table('banks as bank')
            ->join('rrhh_personnel_actions as personnel_action', 'bank.id', '=', 'personnel_action.bank_id')
            ->select('bank.name as name')
            ->where('personnel_action.id', $id)
            ->get();
 
        $employee = Employees::findOrFail($personnelAction[0]->employee_id);
        $previousSalary = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->first(); 
        $newSalary = RrhhSalaryHistory::where('employee_id', $employee->id)->where('rrhh_personnel_action_id', $personnelAction[0]->id)->first(); 
 
        $previousPosition = RrhhPositionHistory::where('employee_id', $employee->id)->where('current', 1)->first(); 
        $newPosition = RrhhPositionHistory::where('employee_id', $employee->id)->where('rrhh_personnel_action_id', $personnelAction[0]->id)->first(); 
 
        //$salaries = RrhhSalaryHistory::where('employee_id', $employee->id)->orderBy('id', 'DESC')->take(2)->get(); 
        //$positions = RrhhPositionHistory::where('employee_id', $employee->id)->orderBy('id', 'DESC')->take(2)->get();

        $users = RrhhPersonnelActionAuthorizer::where('rrhh_personnel_action_id', $personnelAction[0]->id)->get();
        $actions = DB::table('rrhh_action_type')->where('rrhh_type_personnel_action_id', $personnelAction[0]->type_id)->orderBy('id', 'DESC')->get();
                    
        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        
        $pdf = \PDF::loadView('rrhh.personnel_actions.authorization_report_pdf',
            compact('personnelAction', 'previousSalary', 'newSalary', 'previousPosition', 'newPosition', 'business', 'actions', 'employee', 'users', 'payment', 'bank'));
        
        $pdf->setPaper('letter', 'portrait');
        return $pdf->download(__('rrhh.personnel_action') . '.pdf');
    }


    public function createDocument($id) 
    {
        if ( !auth()->user()->can('rrhh_personnel_action.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $personnelAction = RrhhPersonnelAction::findOrFail($id);

        return view('rrhh.personnel_actions.file', compact('personnelAction'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDocument(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_personnel_action.create') ) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'file' => 'required',
        ]);
        
        try {
            DB::beginTransaction();
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $name = time().$file->getClientOriginalName();
                Storage::disk('flags')->put($name,  \File::get($file));
                $input_details['file'] = $name;
                $input_details['rrhh_personnel_action_id'] = $request->input('rrhh_personnel_action_id');

                RrhhPersonnelActionFile::create($input_details);
        
                $output = [
                    'success' => 1,
                    'msg' => __('rrhh.added_successfully')
                ];
            }
            DB::commit();
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
     * @param  \App\RrhhPersonnelAction  $rrhhPersonnelAction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('rrhh_personnel_action.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $item = RrhhPersonnelAction::findOrFail($id);
                $salaryHistories = RrhhSalaryHistory::where('rrhh_personnel_action_id', $item->id)->get(); 
                if(count($salaryHistories) != 0){
                    RrhhSalaryHistory::where('rrhh_personnel_action_id', $item->id)->delete(); 
                }
                $positionHistories = RrhhPositionHistory::where('rrhh_personnel_action_id', $item->id)->get(); 
                if(count($positionHistories) != 0){
                    RrhhPositionHistory::where('rrhh_personnel_action_id', $item->id)->delete(); 
                }
                $item->delete();

                $output = [
                    'success' => true,
                    'msg' => __('rrhh.deleted_successfully')
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __('rrhh.error')
                ];
            }

            return $output;
        }
    }
}
