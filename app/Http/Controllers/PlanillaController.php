<?php

namespace App\Http\Controllers;

use App\Business;
use App\CalculationType;
use App\Employees;
use App\Exports\PayrollSalaryReportExport;
use App\Exports\PayrollHonoraryReportExport;
use App\LawDiscount;
use App\PaymentPeriod;
use App\Planilla;
use App\PlanillaDetail;
use App\PlanillaStatus;
use App\RrhhAbsenceInability;
use App\RrhhIncomeDiscount;
use App\RrhhSalaryHistory;
use App\RrhhTypeWage;
use App\TypePlanilla;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


class PlanillaController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ModuleUtil $moduleUtil
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
        if (!auth()->user()->can('planilla.view')) {
            abort(403, "Unauthorized action.");
        }

        return view('planilla.index');
    }


    public function getPlanillas()
    {
        if (!auth()->user()->can('plantilla.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = DB::table('planillas as planilla')
            ->join('type_planillas as type_planilla', 'type_planilla.id', '=', 'planilla.type_planilla_id')
            ->join('payment_periods as payment_period', 'payment_period.id', '=', 'planilla.payment_period_id')
            ->join('planilla_statuses as planilla_status', 'planilla_status.id', '=', 'planilla.planilla_status_id')
            ->select('planilla.id as id', 'planilla.*', 'planilla_status.name as status', 'type_planilla.name as type', 'payment_period.name as payment_period')
            ->where('planilla.business_id', $business_id)
            ->where('planilla.deleted_at', null)
            ->get();

        return DataTables::of($data)
            ->editColumn('period', '{{ @format_date($start_date) }} - {{ @format_date($end_date) }}')
            ->editColumn('month', function ($data) {
                $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                return $meses[$data->month - 1];
            })->editColumn('status', function ($data) {
                $html = '';
                if ($data->status == 'Aprobada'){
                    $html = '<span class="badge" style="background: #449D44">'.$data->status.'</span>';
                }
                if ($data->status == 'Calculada'){
                    $html = '<span class="badge" style="background: #00A6DC">'.$data->status.'</span>';
                }
                if ($data->status == 'Iniciada'){
                    $html = '<span class="badge">'.$data->status.'</span>';
                }
                return $html;
            })
            ->addColumn('statusPlanilla', function ($data) {
                return $data->status;
            })
            ->rawColumns(['type', 'name', 'payment_period', 'period', 'status', 'statusPlanilla'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('planilla.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $paymentPeriods = PaymentPeriod::where('business_id', $business_id)
            ->where('id', '<>', 1) //Semanal
            ->where('id', '<>', 2) //Catorcenal
            ->where('id', '<>', 3) //Quincenal
            ->where('id', '<>', 7) //Semestral
            ->where('id', '<>', 8) //Anual
            ->get();
        $typePlanillas = TypePlanilla::where('business_id', $business_id)->get();

        return view('planilla.create', compact('paymentPeriods', 'typePlanillas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('plantilla.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'type_planilla_id'    => 'required',
            'year'                => 'required',
            'month'               => 'required',
            'payment_period_id'   => 'required',
            'start_date'          => 'required',
            'end_date'            => 'required',
        ]);

        try {
            $input_details = $request->all();
            $business_id = request()->session()->get('user.business_id');
            $paymentPeriod = PaymentPeriod::where('id', $request->input('payment_period_id'))->where('business_id', $business_id)->first();
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $input_details['name'] = __('planilla.planilla').' '.$paymentPeriod->name.' - '.$meses[$request->month - 1].' '.$request->year;
            $input_details['business_id'] = $business_id;
            $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));
            $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));
            $status = PlanillaStatus::where('name', 'Iniciada')->where('business_id', $input_details['business_id'])->first();
            $input_details['planilla_status_id'] = $status->id;

            DB::beginTransaction();

            $planilla = Planilla::create($input_details);
            if($request->calculate == true){
                $this->calculate($planilla);
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
                'msg' => $e->getMessage()
                //'msg' => __('rrhh.error')
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $planilla = Planilla::where('id', $id)->where('business_id', $business_id)->with('paymentPeriod')->firstOrFail();
        if ($planilla->planillaStatus->name == 'Iniciada') {
            $this->calculate($planilla);
        }

        if($planilla->typePlanilla->name == 'Planilla de sueldos'){
            return view('planilla.generate_sueldo', compact('planilla'));
        }

        if($planilla->typePlanilla->name == 'Planilla de honorarios'){
            return view('planilla.generate_honorario', compact('planilla'));
        }
    }


    public function getPlanillaDetail(Request $request, $id)
    {
        if (!auth()->user()->can('plantilla.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $planilla = Planilla::where('id', $id)->where('business_id', $business_id)->firstOrFail();
        $data = PlanillaDetail::where('planilla_id', $id)->with('planilla')->get();
        
        if($planilla->typePlanilla->name == 'Planilla de sueldos'){
            return DataTables::of($data)
            ->editColumn('employee', function ($data) {
                return $data->employee->first_name.' '.$data->employee->last_name;
            })->editColumn('salary', function ($data) {
                $salary = 0;
                foreach ($data->employee->salaryHistories as $salaryHistory){
                    if ($salaryHistory->current == 1){
                        $salary = $salaryHistory->new_salary;
                    }
                }
                return $this->moduleUtil->num_f($salary, $add_symbol = true, $precision = 2);
            })->editColumn('commissions', function ($data) {
                return $this->moduleUtil->num_f($data->commissions, $add_symbol = true, $precision = 2);
            })->editColumn('daytime_overtime', function ($data) {
                return $this->moduleUtil->num_f($data->daytime_overtime, $add_symbol = true, $precision = 2);
            })->editColumn('night_overtime_hours', function ($data) {
                return $this->moduleUtil->num_f($data->night_overtime_hours, $add_symbol = true, $precision = 2);
            })->editColumn('total_hours', function ($data) {
                return $this->moduleUtil->num_f($data->total_hours, $add_symbol = true, $precision = 2);
            })->editColumn('subtotal', function ($data) {
                return $this->moduleUtil->num_f($data->subtotal, $add_symbol = true, $precision = 2);
            })->editColumn('isss', function ($data) {
                return $this->moduleUtil->num_f($data->isss, $add_symbol = true, $precision = 2);
            })->editColumn('afp', function ($data) {
                return $this->moduleUtil->num_f($data->afp, $add_symbol = true, $precision = 2);
            })->editColumn('rent', function ($data) {
                return $this->moduleUtil->num_f($data->rent, $add_symbol = true, $precision = 2);
            })->editColumn('other_deductions', function ($data) {
                return $this->moduleUtil->num_f($data->other_deductions, $add_symbol = true, $precision = 2);
            })->editColumn('total_to_pay', function ($data) {
                return $this->moduleUtil->num_f($data->total_to_pay, $add_symbol = true, $precision = 2);
            })->toJson();
        }
        
        if($planilla->typePlanilla->name == 'Planilla de honorarios'){
            return DataTables::of($data)
                ->editColumn('employee', function ($data) {
                    return $data->employee->first_name.' '.$data->employee->last_name;
                })->editColumn('salary', function ($data) {
                    $salary = 0;
                    foreach ($data->employee->salaryHistories as $salaryHistory){
                        if ($salaryHistory->current == 1){
                            $salary = $salaryHistory->new_salary;
                        }
                    }
                    return $this->moduleUtil->num_f($salary, $add_symbol = true, $precision = 2);
                })->editColumn('rent', function ($data) {
                    return $this->moduleUtil->num_f($data->rent, $add_symbol = true, $precision = 2);
                })->editColumn('total_to_pay', function ($data) {
                    return $this->moduleUtil->num_f($data->total_to_pay, $add_symbol = true, $precision = 2);
                })->toJson();
        }
    }

    public function recalculate($id)
    {
        if (!auth()->user()->can('plantilla.recalculate')) {
            abort(403, 'Unauthorized action.');
        }
        try{
            $business_id = request()->session()->get('user.business_id');
            $planilla = Planilla::where('id', $id)->where('business_id', $business_id)->with('paymentPeriod', 'typePlanilla')->firstOrFail();
            
            if ($planilla->planillaStatus->name == 'Calculada') {
                DB::beginTransaction();

                PlanillaDetail::where('planilla_id', $planilla->id)->delete();
                $this->calculate($planilla);

                DB::commit();

                $output = [
                    'success' => 1,
                    'msg' => __('planilla.recalculation_done_successfully')
                ];
            }else{
                $output = [
                    'success' => 0,
                    'msg' => __('planilla.failed_to_recalculate')
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
    

    /** Authorizer personnel action */
    function approve(Request $request, $id)
    {
        if (!auth()->user()->can('planilla.approve')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                DB::beginTransaction();
                $business_id = request()->session()->get('user.business_id');
                $planilla = Planilla::where('id', $id)->where('business_id', $business_id)->firstOrFail();
                $user_id = auth()->user()->id;
                $user = User::findOrFail($user_id);

                if (Hash::check($request->input('password'), $user->password)) {
                    $status = PlanillaStatus::where('name', 'Aprobada')->where('business_id', $business_id)->first();
                    $planilla->planilla_status_id = $status->id;
                    $planilla->approval_date = Carbon::now();
                    $planilla->update();

                    if($request->input('sendEmail') == 1){
                        $output = [
                            'success' => 1,
                            'msg' => __('planilla.send_approve_payroll')
                        ];
                    }else{
                        $output = [
                            'success' => 1,
                            'msg' => __('planilla.approve_payroll')
                        ];
                    }
                } else {
                    $output = [
                        'success' => 0,
                        'msg' => __('rrhh.wrong_password_authorize')
                    ];
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => 0,
                    'msg' => $e->getMessage()
                ];
            }
            return $output;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //Calcular planilla
    public function calculate($planilla)
    {
        $business_id = request()->session()->get('user.business_id');
        if (mb_strtolower($planilla->paymentPeriod->name) == mb_strtolower('Primera quincena') || mb_strtolower($planilla->paymentPeriod->name) == mb_strtolower('Segunda quincena')) {
            $paymentPeriod = 'Quincenal';
        } else {
            $paymentPeriod = $planilla->paymentPeriod->name;
        }

        //Obtener empleados
        $employees = Employees::where('business_id', $business_id)
        ->where('date_admission', '<=', $planilla->end_date)
        ->where('status', 1)
        ->get();

        if(count($employees) > 0){
            foreach ($employees as $employee) {
                $salaryHistory = RrhhSalaryHistory::where('employee_id', $employee->id)
                    ->where('current', 1)
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($salaryHistory) {
                    $salary = $salaryHistory->new_salary;
                    $typeWage = RrhhTypeWage::where('id', $employee->type_id)->first();

                    if($planilla->typePlanilla->name == 'Planilla de sueldos'){
                        if($typeWage->type == 'Ley de salario'){ //----------------------LEY DE SALARIO----------------------
                            $discountDO = 0;
                            $discountNOH = 0;
                            $discountCom = 0; 
                            $discountOD = 0;
            
                            $incomeDO = 0;
                            $incomeNOH = 0;
                            $incomeCom = 0;
                            $incomeOD = 0;

                            $diasIncapacidad = 0;
                            $start_date_planilla = Carbon::parse($planilla->start_date);
                            $end_date_planilla = Carbon::parse($planilla->end_date);

                            $incapacidades = RrhhAbsenceInability::where('type', 'Incapacidad')
                                ->where('start_date', '<=', $planilla->end_date)
                                ->where('employee_id', $employee->id)
                                ->get();
        
                            $incomeDiscounts = RrhhIncomeDiscount::join('rrhh_type_income_discounts as type', 'type.id', '=', 'rrhh_income_discounts.rrhh_type_income_discount_id')
                                ->select('rrhh_income_discounts.id as id', 'rrhh_income_discounts.*', 'type.planilla_column')
                                ->where('rrhh_income_discounts.employee_id', $employee->id)
                                ->where('rrhh_income_discounts.start_date', '<=', $planilla->end_date)
                                ->where('rrhh_income_discounts.deleted_at', null)
                                ->get();
            
                            $lawDiscounts = LawDiscount::join('institution_laws as institution_law', 'institution_law.id', '=', 'law_discounts.institution_law_id')
                                ->join('payment_periods as payment_period', 'payment_period.id', '=', 'law_discounts.payment_period_id')
                                ->select('law_discounts.id as id', 'law_discounts.*', 'payment_period.name as payment_period', 'institution_law.name as institution_law')
                                ->where('payment_period.name', $paymentPeriod)
                                ->where('law_discounts.business_id', $business_id)
                                ->where('law_discounts.deleted_at', null)
                                ->get();
            
                            $lawDiscountsRenta = LawDiscount::join('institution_laws as institution_law', 'institution_law.id', '=', 'law_discounts.institution_law_id')
                                ->join('payment_periods as payment_period', 'payment_period.id', '=', 'law_discounts.payment_period_id')
                                ->select('law_discounts.id as id', 'law_discounts.*', 'institution_law.name as institution_law', 'payment_period.name as payment_period')
                                ->where('institution_law.name', 'Renta')
                                ->where('payment_period.name', $paymentPeriod)
                                ->where('law_discounts.business_id', $business_id)
                                ->where('law_discounts.deleted_at', null)
                                ->get();
            
                            
                            //Calcular los días de incapacidad
                            foreach($incapacidades as $incapacidad){
                                $start_date_incapacidad = Carbon::parse($incapacidad->start_date);
                                $end_date_incapacidad = Carbon::parse($incapacidad->end_date);
            
                                if($incapacidad->start_date >= $planilla->start_date && $incapacidad->end_date <= $planilla->end_date){
                                    $diasIncapacidad += $end_date_incapacidad->diffInDays($start_date_incapacidad);
                                }
            
                                if($incapacidad->start_date >= $planilla->start_date && $incapacidad->end_date > $planilla->end_date){
                                    $diasIncapacidad += $end_date_planilla->diffInDays($start_date_incapacidad);
                                }
            
                                if($incapacidad->start_date < $planilla->start_date && $incapacidad->end_date <= $planilla->end_date){
                                    $diasIncapacidad += $end_date_incapacidad->diffInDays($start_date_planilla);
                                }
            
                                if($incapacidad->start_date < $planilla->start_date && $incapacidad->end_date > $planilla->end_date){
                                    $diasIncapacidad += $end_date_planilla->diffInDays($start_date_planilla);
                                }
                            }
            
                            //Obteniendo el calculo total de cada ingreso o descuento
                            foreach($incomeDiscounts as $incomeDiscount){
                                $planillaColumnDO = 'Número de horas extras diurnas';
                                $planillaColumnNOH = 'Número de horas extras nocturnas';
                                $planillaColumnCom = 'Comisiones';
                                $planillaColumnOD = 'Otras deducciones';
                                if($incomeDiscount->start_date >= $planilla->start_date && $incomeDiscount->end_date <= $planilla->end_date){
            
                                    ($incomeDiscount->type == 1) ? $incomeDO += $this->incomeDiscount($incomeDiscount, $planillaColumnDO) : $discountDO += $this->incomeDiscount($incomeDiscount, $planillaColumnDO);
                                    ($incomeDiscount->type == 1) ? $incomeNOH += $this->incomeDiscount($incomeDiscount, $planillaColumnNOH) : $discountNOH += $this->incomeDiscount($incomeDiscount, $planillaColumnNOH);
                                    ($incomeDiscount->type == 1) ? $incomeCom += $this->incomeDiscount($incomeDiscount, $planillaColumnCom) : $discountCom += $this->incomeDiscount($incomeDiscount, $planillaColumnCom);
                                    ($incomeDiscount->type == 1) ? $incomeOD += $this->incomeDiscount($incomeDiscount, $planillaColumnOD) : $discountOD += $this->incomeDiscount($incomeDiscount, $planillaColumnOD);
            
                                }
            
                                if($incomeDiscount->start_date >= $planilla->start_date && $incomeDiscount->end_date > $planilla->end_date){
                                    
                                    ($incomeDiscount->type == 1) ? $incomeDO += $this->incomeDiscount($incomeDiscount, $planillaColumnDO) : $discountDO += $this->incomeDiscount($incomeDiscount, $planillaColumnDO);
                                    ($incomeDiscount->type == 1) ? $incomeNOH += $this->incomeDiscount($incomeDiscount, $planillaColumnNOH) : $discountNOH += $this->incomeDiscount($incomeDiscount, $planillaColumnNOH);
                                    ($incomeDiscount->type == 1) ? $incomeCom += $this->incomeDiscount($incomeDiscount, $planillaColumnCom) : $discountCom += $this->incomeDiscount($incomeDiscount, $planillaColumnCom);
                                    ($incomeDiscount->type == 1) ? $incomeOD += $this->incomeDiscount($incomeDiscount, $planillaColumnOD) : $discountOD += $this->incomeDiscount($incomeDiscount, $planillaColumnOD);
            
                                }
            
                                if($incomeDiscount->start_date < $planilla->start_date && $incomeDiscount->end_date <= $planilla->end_date){
                                    
                                    ($incomeDiscount->type == 1) ? $incomeDO += $this->incomeDiscount($incomeDiscount, $planillaColumnDO) : $discountDO += $this->incomeDiscount($incomeDiscount, $planillaColumnDO);
                                    ($incomeDiscount->type == 1) ? $incomeNOH += $this->incomeDiscount($incomeDiscount, $planillaColumnNOH) : $discountNOH += $this->incomeDiscount($incomeDiscount, $planillaColumnNOH);
                                    ($incomeDiscount->type == 1) ? $incomeCom += $this->incomeDiscount($incomeDiscount, $planillaColumnCom) : $discountCom += $this->incomeDiscount($incomeDiscount, $planillaColumnCom);
                                    ($incomeDiscount->type == 1) ? $incomeOD += $this->incomeDiscount($incomeDiscount, $planillaColumnOD) : $discountOD += $this->incomeDiscount($incomeDiscount, $planillaColumnOD);
            
                                }
            
                                if($incomeDiscount->start_date < $planilla->start_date && $incomeDiscount->end_date > $planilla->end_date){
                                    
                                    ($incomeDiscount->type == 1) ? $incomeDO += $this->incomeDiscount($incomeDiscount, $planillaColumnDO) : $discountDO += $this->incomeDiscount($incomeDiscount, $planillaColumnDO);
                                    ($incomeDiscount->type == 1) ? $incomeNOH += $this->incomeDiscount($incomeDiscount, $planillaColumnNOH) : $discountNOH += $this->incomeDiscount($incomeDiscount, $planillaColumnNOH);
                                    ($incomeDiscount->type == 1) ? $incomeCom += $this->incomeDiscount($incomeDiscount, $planillaColumnCom) : $discountCom += $this->incomeDiscount($incomeDiscount, $planillaColumnCom);
                                    ($incomeDiscount->type == 1) ? $incomeOD += $this->incomeDiscount($incomeDiscount, $planillaColumnOD) : $discountOD += $this->incomeDiscount($incomeDiscount, $planillaColumnOD);
            
                                }
                            }

                            //Calcular los días trabajados
                            if($employee->date_admission >= $planilla->start_date){
                                $daysPlanilla = $end_date_planilla->diffInDays($employee->date_admission);
                                $details['days'] = $daysPlanilla - $diasIncapacidad;
                            }else{
                                $details['days'] = abs($planilla->paymentPeriod->days - $diasIncapacidad);
                            }

                            $details['hours'] = 8;
                            $details['commissions'] = abs(0 - $discountCom + $incomeCom);
                            $details['number_daytime_overtime'] = 0;
                            $details['daytime_overtime'] =abs( 0 - $discountDO + $incomeDO);
                            $details['number_night_overtime_hours'] = 0;
                            $details['night_overtime_hours'] = abs(0 - $discountNOH + $incomeNOH);
                            $details['total_hours'] = $details['daytime_overtime'] + $details['night_overtime_hours'];
                            $details['subtotal'] = ($salary / 30 * $details['days']) + $details['commissions'] + $details['total_hours'];
                            
                            //Calcular ISSS y AFP
                            foreach ($lawDiscounts as $lawDiscount) {
                                //---------------------------------ISSS---------------------------------
                                if (mb_strtolower($lawDiscount->institution_law) == mb_strtolower('ISSS')) {
                                    if (mb_strtolower($lawDiscount->payment_period) == mb_strtolower($paymentPeriod)) {
                                        if ($details['subtotal'] >= $lawDiscount->until) {
                                            $details['isss'] = $lawDiscount->until * $lawDiscount->employee_percentage / 100;
                                        } else {
                                            $details['isss'] = $details['subtotal'] * $lawDiscount->employee_percentage / 100;
                                        }
                                    }
                                }
        
                                //---------------------------------AFP---------------------------------
                                if ($lawDiscount->institution_law == 'AFP Confia' || $lawDiscount->institution_law == 'AFP Crecer') {
                                    if (mb_strtolower($lawDiscount->payment_period) == mb_strtolower($paymentPeriod)) {
                                        $details['afp'] = $details['subtotal'] * $lawDiscount->employee_percentage / 100;
                                    }
                                }
                            }
        
                            //Calcular Renta        
                            for ($i = 0; $i < count($lawDiscountsRenta); $i++) {
                                if (($details['subtotal'] - $details['isss'] - $details['afp']) <= $lawDiscountsRenta[0]->until) {
                                    $details['rent'] = 0;
                                } else {
                                    if (($details['subtotal'] - $details['isss'] - $details['afp']) <= $lawDiscountsRenta[1]->until) {
                                        // ((M7-N7-O7)-$J$13)*$I$13+$H$13,
                                        $details['rent'] = round(((($details['subtotal'] - $details['isss'] - $details['afp']) - $lawDiscountsRenta[1]->base) * ($lawDiscountsRenta[1]->employee_percentage / 100)) + $lawDiscountsRenta[1]->fixed_fee, 2);
                                    } else {
                                        if (($details['subtotal'] - $details['isss'] - $details['afp']) <= $lawDiscountsRenta[2]->until) {
                                            // ((M7-N7-O7)-$J$14)*$I$14+$H$14
                                            $details['rent'] = round(((($details['subtotal'] - $details['isss'] - $details['afp']) - $lawDiscountsRenta[2]->base) * ($lawDiscountsRenta[2]->employee_percentage / 100)) + $lawDiscountsRenta[2]->fixed_fee, 2);
                                        } else {
                                            if (($details['subtotal'] - $details['isss'] - $details['afp']) <= $lawDiscountsRenta[3]->until) {
                                                //((M7-N7-O7)-$J$15)*$I$15+$H$15
                                                $details['rent'] = round(((($details['subtotal'] - $details['isss'] - $details['afp']) - $lawDiscountsRenta[3]->base) * ($lawDiscountsRenta[3]->employee_percentage / 100)) + $lawDiscountsRenta[3]->fixed_fee, 2);
                                            }
                                        }
                                    }
                                }
                            }

                            $details['other_deductions'] = abs(0 - $discountOD + $incomeOD);
                            $details['total_to_pay'] = bcdiv(($details['subtotal'] - $details['isss'] - $details['afp'] - $details['rent'] - $details['other_deductions']), 1, 2);
                            $details['employee_id']  = $employee->id;
                            $details['planilla_id']  = $planilla->id;
                            
                            //Create register
                            PlanillaDetail::create($details);
                        }
                    }

                    if($planilla->typePlanilla->name == 'Planilla de honorarios'){
                        if($typeWage->type == 'Honorario'){ //----------------------HONORARIO----------------------
                            $details['rent'] = $salary * 0.1;
                            $details['total_to_pay'] = bcdiv($salary - $details['rent'], 1, 2);
                            $details['employee_id']  = $employee->id;
                            $details['planilla_id']  = $planilla->id;
                            
                            //Create register
                            PlanillaDetail::create($details);
                        }
                    }
                
                    
                    
                    $status = PlanillaStatus::where('name', 'Calculada')->where('business_id', $business_id)->first();
                    $planilla->planilla_status_id = $status->id;
                    $planilla->update();
                }else{
                    //Mensaje que debe completar la info de los empleados
                }
            }
        }else{
            //Mensaje que no hay empleados
        }
    }


    public function exportPayrollSalary($id){
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $planilla = Planilla::where('id', $id)->where('business_id', $business_id)->with('typePlanilla')->firstOrFail();
        $planillaDetails = PlanillaDetail::where('planilla_id', $id)->with('planilla')->get();
        
        if($planilla->typePlanilla->name == 'Planilla de sueldos'){
            return Excel::download(
                new PayrollSalaryReportExport($planilla, $planillaDetails, $business, $this->moduleUtil),
                'Planilla de sueldos - '.$planilla->name . '.xlsx'
            );
        }
        if($planilla->typePlanilla->name == 'Planilla de honorarios'){
            return Excel::download(
                new PayrollHonoraryReportExport($planilla, $planillaDetails, $business, $this->moduleUtil),
                'Planilla de honorarios - '.$planilla->name . '.xlsx'
            );
        }
        
    }

    public function incomeDiscount($incomeDiscount, $planilla_column){
        $incomeOrDiscount = 0;
        if($incomeDiscount->planilla_column == $planilla_column){
            $incomeOrDiscount = $incomeDiscount->quota_value;
        }

        return $incomeOrDiscount;
    }
}
