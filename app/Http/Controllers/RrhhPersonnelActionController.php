<?php

namespace App\Http\Controllers;

use App\Employees;
use App\User;
use App\RrhhPersonnelAction;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;
use App\Utils\ModuleUtil;
use App\Notifications\PersonnelActionNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notification;

class RrhhPersonnelActionController extends Controller
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
        
    }

    public function getByEmployee($id)
    {
        if (!auth()->user()->can('rrhh_overall_payroll.view')) {
            abort(403, 'Unauthorized action.');
        }

        $employee = Employees::findOrFail($id);
        $personnelActions = DB::table('rrhh_personnel_actions as personnel_action')
            ->join('rrhh_type_personnel_actions as type', 'type.id', '=', 'personnel_action.rrhh_type_personnel_action_id')
            ->select('personnel_action.id as id', 'personnel_action.description as description', 'personnel_action.created_at as created_at', 'type.name as type', 'type.required_authorization as required_authorization', 'personnel_action.authorized as authorized')
            ->where('personnel_action.employee_id', $employee->id)
            ->get();

        return view('rrhh.personnel_actions.index', compact('personnelActions', 'employee'));
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
        if (!auth()->user()->can('rrhh_overall_payroll.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $actions = DB::table('rrhh_action_type')->orderBy('id', 'DESC')->get();
        $typesPersonnelActions = DB::table('rrhh_type_personnel_actions')->where('business_id', $business_id)->get();
        $departments = DB::table('rrhh_datas')->where('rrhh_header_id', 2)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $positions = DB::table('rrhh_datas')->where('rrhh_header_id', 3)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $payments = DB::table('rrhh_datas')->where('rrhh_header_id', 8)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $banks = DB::table('banks')->where('business_id', $business_id)->orderBy('name', 'ASC')->pluck('name', 'id');
        
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
        
        $positionHistory = DB::table('rrhh_position_history')
        ->where('employee_id', $id)
        ->where('current', 1)
        ->orderBy('id', 'DESC')
        ->first();

        $salaryHistory = DB::table('rrhh_salary_history')
        ->where('employee_id', $id)
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
        if (!auth()->user()->can('rrhh_overall_payroll.create')) {
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
            $type = DB::table('rrhh_type_personnel_actions')->where('business_id', $business_id)->where('id', $request->rrhh_type_personnel_action_id)->first();

            if ($type->required_authorization == 1) { // Requiere autorizacion la accion de personal
                $requiredUser = 'required';
            }

            $actions = DB::table('rrhh_action_type')->orderBy('id', 'DESC')->get();
            foreach ($actions as $action) {
                if ($action->rrhh_type_personnel_action_id == $type->id) {

                    if ($action->rrhh_required_action_id == 2) { // Cambiar departamento/puesto
                        $requiredDepartment = 'required';
                        $requiredPosition = 'required';
                    }

                    if ($action->rrhh_required_action_id == 3) { // Cambiar salario
                        $requiredSalary = 'required';
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
            $employee = Employees::findOrFail($request->employee_id);

            DB::beginTransaction();

            $actions = DB::table('rrhh_action_type')->orderBy('id', 'DESC')->get();
            foreach ($actions as $action) {
                if ($action->rrhh_type_personnel_action_id == $type->id) {
                    if ($action->rrhh_required_action_id == 1 && $type->required_authorization == 0) { // Cambiar estado de empleado (De inactivo a activo)
                        $employee->status = 1;
                        $employee->update();
                    }

                    if ($action->rrhh_required_action_id == 2) { // Cambiar departamento/puesto
                        if($type->required_authorization == 0){
                            DB::table('rrhh_position_history')->where('employee_id', $employee->id)->update(['current' => 0]);
                        }
                        DB::table('rrhh_position_history')->insert(
                            ['department_id' => $request->input('department_id'), 'position1_id' => $request->input('position1_id'), 'employee_id' => $employee->id]
                        );
                    }

                    if ($action->rrhh_required_action_id == 3) { // Cambiar salario
                        if($type->required_authorization == 0){
                            DB::table('rrhh_salary_history')->where('employee_id', $employee->id)->update(['current' => 0]);
                        }

                        DB::table('rrhh_salary_history')->insert(
                            ['employee_id' => $employee->id, 'salary' => $request->input('new_salary')]
                        );
                    }

                    if ($action->rrhh_required_action_id == 4) { // Seleccionar un periodo en específico
                        $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));
                        $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));
                    }

                    if ($action->rrhh_required_action_id == 5) { // Cambiar cuenta bancaria
                        if($type->required_authorization == 0){
                            $input_employee = $request->only(['bank_account']);
                            $employee->update($input_employee);
                        }
                        else{
                            $input_details['bank_account'] = $request->input('bank_account');
                        }
                    }

                    if ($action->rrhh_required_action_id == 6) { // Cambiar forma de pago
                        if($type->required_authorization == 0){
                            $input_employee = $request->only(['payment_id', 'bank_id', 'bank_account']);
                            $employee->update($input_employee);
                        }
                        else{
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

            $personnelAction = RrhhPersonnelAction::create($input_details);

            if ($type->required_authorization == 1) { // Requiere autorizacion la accion de personal
                $users = $request->input('user_id');
                \Log::emergency($users);
                foreach ($users as $userID) {
                    $user = User::findOrFail($userID);
                    DB::table('rrhh_personnel_action_authorizer')->insert(
                        ['personnel_action_id' => $personnelAction->id, 'user_id' => $userID]
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
        //
    }

    function viewFile($id)
    {
        if (!auth()->user()->can('rrhh_overall_payroll.view')) {
            abort(403, 'Unauthorized action.');
        }
        $document = RrhhPersonnelAction::findOrFail($id);
        $state = DB::table('states')->where('id', $document->state_id)->first();
        $city = DB::table('cities')->where('id', $document->city_id)->first();
        $type = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('id', $document->document_type_id)->first();

        $route = 'flags/' . $document->file;
        $ext = substr($document->file, -3);


        return view('rrhh.personnel_actions.file', compact('route', 'ext', 'document', 'state', 'city', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhPersonnelAction  $rrhhPersonnelAction
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('rrhh_overall_payroll.edit')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $document = RrhhPersonnelAction::findOrFail($id);
        $states = DB::table('states')->pluck('name', 'id');
        $cities = DB::table('cities')->where('state_id', $document->state_id)->pluck('name', 'id');
        $type = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('business_id', $business_id)->where('status', 1)->where('id', $document->document_type_id)->first();
        //$types = DB::table('rrhh_datas')->where('rrhh_header_id', 9)->where('business_id', $business_id)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $employee_id = $document->employee_id;

        return view('rrhh.personnel_actions.edit', compact('type', 'document', 'states', 'cities', 'employee_id'));
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
        if (!auth()->user()->can('rrhh_overall_payroll.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $item = RrhhPersonnelAction::findOrFail($request->id);
        $business_id = request()->session()->get('user.business_id');
        $type = DB::table('rrhh_datas')->where('business_id', $business_id)->where('rrhh_header_id', 9)->where('status', 1)->where('date_required', 1)->where('id', $item->document_type_id)->first();
        if ($type) {
            $request->validate([
                'state_id'              => 'required',
                'city_id'               => 'required',
                'number'                => 'required',
                'date_expedition'       => 'required',
                'date_expiration'       => 'required',
                'file'                  => 'required',
            ]);
        } else {
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

            if ($input_details['date_expedition'] < $input_details['date_expiration']) {
                DB::beginTransaction();

                $item = RrhhPersonnelAction::findOrFail($request->id);

                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $name = time() . $file->getClientOriginalName();
                    Storage::disk('flags')->put($name,  \File::get($file));
                    $input_details['file'] = $name;
                }

                $document = $item->update($input_details);

                DB::commit();


                $output = [
                    'success' => 1,
                    'msg' => __('rrhh.updated_successfully')
                ];
            } else {
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
     * @param  \App\RrhhPersonnelAction  $rrhhPersonnelAction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('rrhh_overall_payroll.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $item = RrhhPersonnelAction::findOrFail($id);
                $item->forceDelete();

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
