<?php

namespace App\Http\Controllers;

use App\CalculationType;
use App\Employees;
use App\LawDiscount;
use App\PaymentPeriod;
use App\Planilla;
use App\PlanillaDetail;
use App\RrhhIncomeDiscount;
use App\RrhhSalaryHistory;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;

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
        if(!auth()->user()->can('planilla.view')){
            abort(403, "Unauthorized action.");
        }

        return view('planilla.index');
    }


    public function getPlanillas(){
        if ( !auth()->user()->can('plantilla.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = DB::table('planillas as planilla')
            ->join('payment_periods as payment_period', 'payment_period.id', '=', 'planilla.payment_period_id')
            ->join('calculation_types as calculation_type', 'calculation_type.id', '=', 'planilla.calculation_type_id')
            ->join('planilla_statuses as planilla_status', 'planilla_status.id', '=', 'planilla.planilla_status_id')
            ->select('planilla.id as id', 'planilla.year', 'planilla.month', 'planilla.start_date as start_date', 'planilla.end_date as end_date', 'planilla_status.name as status', 'payment_period.name as payment_period', 'calculation_type.name as calculation_type')
            ->where('planilla.business_id', $business_id)
            ->where('planilla.deleted_at', null)
            ->get();

        return DataTables::of($data)
        ->editColumn('period', '{{ @format_date($start_date) }} - {{ @format_date($end_date) }}')
        ->editColumn('month', function ($data) {
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            return $meses[$data->month - 1];
        })->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ( !auth()->user()->can('planilla.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $paymentPeriods = PaymentPeriod::where('business_id', $business_id)->where('id', '<>', 3)->get();
        $calculationTypes = CalculationType::where('business_id', $business_id)->get();
        
        return view('planilla.create', compact('paymentPeriods', 'calculationTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ( !auth()->user()->can('plantilla.create') ) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'year'                => 'required',
            'month'               => 'required',
            'payment_period_id'   => 'required',
            'calculation_type_id' => 'required',
            'start_date'          => 'required',
            'end_date'            => 'required',
            'days'                => 'required',
        ]);  

        try {
            $input_details = $request->all(); 
            $input_details['business_id'] = request()->session()->get('user.business_id');           
            $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));
            $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));
            $input_details['planilla_status_id'] = 1;

            DB::beginTransaction();
    
            Planilla::create($input_details);
    
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate($id)
    {
        //Fecha y tipos de income discount y si afecta afp o no
        //Dias de las ausencias
        $business_id = request()->session()->get('user.business_id');
        $planilla = Planilla::where('id', $id)->where('business_id', $business_id)->with('paymentPeriod')->first();
        if($planilla->planilla_status_id == 1){
            $employees = Employees::where('status', 1)->where('business_id', $business_id)->get();
            

            foreach($employees as $employee){
                $salaryHistory = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
                if($salaryHistory){
                    $salary = $salaryHistory->new_salary;

                    if(
                        mb_strtolower($planilla->paymentPeriod->name) == mb_strtolower('Primera quincena') || 
                        mb_strtolower($planilla->paymentPeriod->name) == mb_strtolower('Segunda quincena')
                    ){
                        $paymentPeriod = 'Quincena';
                    }else{
                        $paymentPeriod = $planilla->paymentPeriod->name;
                    }

                    $details['days'] = $planilla->paymentPeriod->days;
                    $details['hours'] = 8;
                    $details['commissions'] = 0;
                    $details['number_daytime_overtime'] = 0;
                    $details['daytime_overtime'] = 0;
                    $details['number_night_overtime_hours'] = 0;
                    $details['night_overtime_hours'] = 0;
                    
                    $lawDiscounts = LawDiscount::join('institution_laws as institution_law', 'institution_law.id', '=', 'law_discounts.institution_law_id')
                        ->join('calculation_types as calculation_type', 'calculation_type.id', '=', 'law_discounts.calculation_type_id')
                        ->select('law_discounts.id as id', 'law_discounts.from', 'law_discounts.until', 'law_discounts.base', 'law_discounts.fixed_fee', 'law_discounts.employee_percentage', 'law_discounts.employer_value', 'law_discounts.status', 'calculation_type.name as calculation_type', 'institution_law.name as institution_law')
                        ->where('calculation_type.name', $paymentPeriod)
                        ->where('law_discounts.business_id', $business_id)
                        ->where('law_discounts.deleted_at', null)
                        ->get();

                    foreach($lawDiscounts as $lawDiscount){
                        //ISSS
                        if(mb_strtolower($lawDiscount->institution_law) == mb_strtolower('ISSS')){
                            if(mb_strtolower($lawDiscount->calculation_type) == mb_strtolower($paymentPeriod)){
                                if($salary >= $lawDiscount->until){
                                    $details['isss'] = $lawDiscount->until * $lawDiscount->employee_percentage/100;
                                }else{
                                    $details['isss'] = $salary * $lawDiscount->employee_percentage/100;
                                }
                            }  
                        }

                        //AFP
                        if($lawDiscount->institution_law == 'AFP Confia' || $lawDiscount->institution_law == 'AFP Crecer'){
                            if(mb_strtolower($lawDiscount->calculation_type) == mb_strtolower($paymentPeriod)){
                                $details['afp'] = $salary * $lawDiscount->employee_percentage/100;
                            }
                        }
                    }
                    

                    $lawDiscountsRenta = LawDiscount::join('institution_laws as institution_law', 'institution_law.id', '=', 'law_discounts.institution_law_id')
                    ->join('calculation_types as calculation_type', 'calculation_type.id', '=', 'law_discounts.calculation_type_id')
                    ->select('law_discounts.id as id', 'law_discounts.from', 'law_discounts.until', 'law_discounts.base', 'law_discounts.fixed_fee', 'law_discounts.employee_percentage', 'law_discounts.employer_value', 'law_discounts.status', 'institution_law.name as institution_law', 'calculation_type.name as calculation_type')
                    ->where('institution_law.name', 'Renta')
                    ->where('calculation_type.name', $paymentPeriod)
                    ->where('law_discounts.business_id', $business_id)
                    ->where('law_discounts.deleted_at', null)
                    ->get();

                    $totalHours = $details['daytime_overtime'] + $details['night_overtime_hours'];
                    $subtotal = ($salary / 30 * $details['days']) + $details['commissions'] + $totalHours;
                    for ($i=0; $i < count($lawDiscountsRenta); $i++) { 
                        if(($subtotal - $details['isss'] - $details['afp']) <= $lawDiscountsRenta[0]->until){
                            $details['rent'] = 0;
                        }else{
                            if(($subtotal - $details['isss'] - $details['afp']) <= $lawDiscountsRenta[1]->until){
                                // ((M7-N7-O7)-$J$13)*$I$13+$H$13,
                                $details['rent'] = ((($subtotal - $details['isss'] - $details['afp']) - $lawDiscountsRenta[1]->base) * ($lawDiscountsRenta[1]->employee_percentage/100)) + $lawDiscountsRenta[1]->fixed_fee;
                            }else{
                                if(($subtotal - $details['isss'] - $details['afp']) <= $lawDiscountsRenta[2]->until){
                                    // ((M7-N7-O7)-$J$14)*$I$14+$H$14
                                    $details['rent'] = ((($subtotal - $details['isss'] - $details['afp']) - $lawDiscountsRenta[2]->base) * ($lawDiscountsRenta[2]->employee_percentage/100)) + $lawDiscountsRenta[2]->fixed_fee;
                                }else{
                                    if(($subtotal - $details['isss'] - $details['afp']) <= $lawDiscountsRenta[3]->until){
                                        //((M7-N7-O7)-$J$15)*$I$15+$H$15
                                        $details['rent'] = ((($subtotal - $details['isss'] - $details['afp']) - $lawDiscountsRenta[3]->base) * ($lawDiscountsRenta[3]->employee_percentage/100)) + $lawDiscountsRenta[3]->fixed_fee;
                                    }
                                }
                            }
                        }  
                    }         


                    $incomeDiscount = RrhhIncomeDiscount::join('rrhh_type_income_discounts as type', 'type.id', '=', 'type.rrhh_type_income_discount_id')
                    ->select('rrhh_income_discounts.id as id', 'rrhh_income_discounts.from', 'law_discounts.until', 'type.planilla_column')
                    ->where('type.planilla_column', 'Otras deducciones')
                    ->where('law_discounts.business_id', $business_id)
                    ->where('law_discounts.deleted_at', null)
                    ->get();

                    $details['other_deductions'] = 0;
                    $details['total_to_pay'] = $subtotal - $details['isss'] - $details['afp'] - $details['rent'] - $details['other_deductions'];
                    $details['employee_id'] = $employee->id;
                    $details['planilla_id'] = $planilla->id;
                    \Log::info($details);
                    PlanillaDetail::create($details);

                    $planilla->planilla_status_id = 3;
                    $planilla->update();
                }
            }
        }

        return view('planilla.generate', compact('planilla'));
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

}
