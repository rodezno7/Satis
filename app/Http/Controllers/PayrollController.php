<?php

namespace App\Http\Controllers;

use App\Bank;
use App\BonusCalculation;
use App\Business;
use App\BusinessLocation;
use App\CalculationType;
use App\Employees;
use App\Exports\PaymentFileReportExport;
use App\Exports\PayrollBonusReportExport;
use App\Exports\PayrollSalaryReportExport;
use App\Exports\PayrollHonoraryReportExport;
use App\Exports\PayrollVacationReportExport;
use App\LawDiscount;
use App\Notifications\PaymentFilesNotification;
use App\PaymentPeriod;
use App\Payroll;
use App\PayrollDetail;
use App\PayrollStatus;
use App\RrhhAbsenceInability;
use App\RrhhIncomeDiscount;
use App\RrhhSalaryHistory;
use App\RrhhTypeWage;
use App\PayrollType;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Notifications\PaymentSplisNotification;
use App\RrhhSetting;
use App\RrhhTypeIncomeDiscount;
use App\Utils\EmployeeUtil;
use App\Utils\PayrollUtil;

class PayrollController extends Controller
{
    protected $moduleUtil;
    protected $employeeUtil;
    protected $payrollUtil;

    /**
     * Constructor
     *
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, EmployeeUtil $employeeUtil, PayrollUtil $payrollUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->employeeUtil = $employeeUtil;
        $this->payrollUtil = $payrollUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('payroll.view')) {
            abort(403, "Unauthorized action.");
        }

        return view('payroll.index');
    }


    public function getPayrolls()
    {
        if (!auth()->user()->can('plantilla.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = Payroll::where('business_id', $business_id)->where('deleted_at', null)->get();

        return DataTables::of($data)
            ->editColumn('period', function ($data) {
                if ($data->start_date != null) {
                    return $this->moduleUtil->format_date($data->start_date) . ' - ' . $this->moduleUtil->format_date($data->end_date);
                } else {
                    return 'Fecha de ingreso - ' . $this->moduleUtil->format_date($data->end_date);
                }
            })
            ->editColumn('month', function ($data) {
                $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                return $meses[$data->month - 1];
            })->editColumn('status', function ($data) {
                $html = '';
                if ($data->payrollStatus->name == 'Aprobada') {
                    $html = '<span class="badge" style="background: #449D44">' . $data->payrollStatus->name . '</span>';
                }
                if ($data->payrollStatus->name == 'Calculada') {
                    $html = '<span class="badge" style="background: #00A6DC">' . $data->payrollStatus->name . '</span>';
                }
                if ($data->payrollStatus->name == 'Pagada') {
                    $html = '<span class="badge" style="background: #367FA9">' . $data->payrollStatus->name . '</span>';
                }
                if ($data->payrollStatus->name == 'Iniciada') {
                    $html = '<span class="badge">' . $data->payrollStatus->name . '</span>';
                }
                return $html;
            })
            ->addColumn('type', function ($data) {
                return $data->payrollType->name;
            })
            ->addColumn('isr', function ($data) {
                if ($data->isr_id != null) {
                    $business_id = request()->session()->get('user.business_id');
                    $paymentPeriod = PaymentPeriod::where('business_id', $business_id)->where('id', $data->isr_id)->first();
                    return $paymentPeriod->name;
                }
            })
            ->addColumn('payment_period', function ($data) {
                if ($data->payment_period_id != null) {
                    return $data->paymentPeriod->name;
                } else {
                    return "N/A";
                }
            })
            ->addColumn('statusPayroll', function ($data) {
                return $data->payrollStatus->name;
            })
            ->rawColumns(['type', 'name', 'payment_period', 'period', 'isr', 'status', 'statusPayroll'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('payroll.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $payrollTypes = PayrollType::where('business_id', $business_id)->get();
        $paymentPeriods = PaymentPeriod::where('business_id', $business_id)
            ->where('name', '<>', 'Semanal') //Semanal
            ->where('name', '<>', 'Catorcenal') //Catorcenal
            ->where('name', '<>', 'Quincenal') //Quincenal
            ->where('name', '<>', 'Semestral') //Semestral
            ->where('name', '<>', 'Anual') //Anual
            ->get();
        $isrTables = PaymentPeriod::where('business_id', $business_id)
            ->where('name', '<>', 'Catorcenal') //Catorcenal
            ->where('name', '<>', 'Primera quincena') //Primera quincena
            ->where('name', '<>', 'Segunda quincena') //Segunda quincena
            ->where('name', '<>', 'Semestral') //Semestral
            ->where('name', '<>', 'Anual') //Anual
            ->where('name', '<>', 'Personalizado') //Personalizado
            ->get();


        return view('payroll.create', compact('paymentPeriods', 'payrollTypes', 'isrTables'));
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
        $business_id = request()->session()->get('user.business_id');
        if ($request->input('payment_period_id') != null) {
            $paymentPeriod = PaymentPeriod::where('id', $request->input('payment_period_id'))->where('business_id', $business_id)->first();
            if ($paymentPeriod->name == 'Personalizado') {
                $request->validate([
                    'payroll_type_id' => 'required',
                    'year'            => 'required',
                    'month'           => 'required',
                    'isr_id'          => 'required',
                    'days'            => 'required',
                    'start_date'      => 'required',
                    'end_date'        => 'required',
                ]);
            } else {
                $request->validate([
                    'payroll_type_id' => 'required',
                    'year'            => 'required',
                    'month'           => 'required',
                    'start_date'      => 'required',
                    'end_date'        => 'required',
                ]);
            }
        } else {
            if ($request->input('payroll_type_id') != null) {
                $payrollType = PayrollType::where('id', $request->input('payroll_type_id'))->where('business_id', $business_id)->first();
                if ($payrollType->name == 'Planilla de aguinaldos') {
                    $request->validate([
                        'year'     => 'required',
                        'isr_id'   => 'required',
                        'end_date' => 'required',
                    ]);
                } else {
                    $request->validate([
                        'payment_period_id' => 'required',
                        'year'              => 'required',
                        'month'             => 'required',
                        'start_date'        => 'required',
                        'end_date'          => 'required',
                    ]);
                }
            } else {
                $request->validate([
                    'payroll_type_id'   => 'required',
                    'payment_period_id' => 'required',
                    'year'              => 'required',
                    'month'             => 'required',
                    'start_date'        => 'required',
                    'end_date'          => 'required',
                ]);
            }
        }

        try {
            $input_details = $request->all();
            if ($request->start_date) {
                $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));
            }
            $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));

            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $payrollType = PayrollType::where('id', $request->input('payroll_type_id'))->where('business_id', $business_id)->first();
            if ($payrollType->name != 'Planilla de aguinaldos') {
                if ($paymentPeriod->name != 'Personalizado') {
                    $input_details['name'] = $payrollType->name . ' ' . $paymentPeriod->name . ' - ' . $meses[$request->month - 1] . ' ' . $request->year;
                } else {
                    $input_details['name'] = $payrollType->name . ' - ' . $meses[$request->month - 1] . ' ' . $request->year;
                }
            } else {
                $input_details['name'] = $payrollType->name . ' - ' . $request->year;
                $input_details['start_date'] = $request->year . '-01-01';
                $input_details['month'] = 12;
            }

            if ($request->input('payment_period_id') != null) {
                if ($paymentPeriod->name != 'Personalizado') {
                    if ($paymentPeriod->name == 'Primera quincena' || $paymentPeriod->name == 'Segunda quincena') {
                        $paymentPeriodQuincenal = PaymentPeriod::where('name', 'Quincenal')->where('business_id', $business_id)->first();
                        $input_details['isr_id'] = $paymentPeriodQuincenal->id;
                        $input_details['days'] = $paymentPeriodQuincenal->days;
                    } else {
                        $input_details['isr_id'] = $paymentPeriod->id;
                        $input_details['days'] = $paymentPeriod->days;
                    }
                }
            }
            $input_details['business_id'] = $business_id;

            $status = PayrollStatus::where('name', 'Iniciada')->where('business_id', $input_details['business_id'])->first();
            $input_details['payroll_status_id'] = $status->id;

            DB::beginTransaction();

            $payroll = Payroll::create($input_details);
            if ($request->input('calculate') == 1) {
                $this->calculate($payroll);
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
     * Get payment period 
     */
    public function getPaymentPeriod($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $paymentPeriod = PaymentPeriod::where('business_id', $business_id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($paymentPeriod);
    }

    /**
     * Get payroll type 
     */
    public function getPayrollType($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $payrollType = PayrollType::where('business_id', $business_id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($payrollType);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('plantilla.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->with('paymentPeriod')->firstOrFail();
        if ($payroll->payrollStatus->name == 'Iniciada') {
            $this->calculate($payroll);
            $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->with('paymentPeriod')->firstOrFail();
        }

        return view('payroll.generate_payroll', compact('payroll'));
    }


    public function getPayrollDetail(Request $request, $id)
    {
        if (!auth()->user()->can('plantilla.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->firstOrFail();
        $data = PayrollDetail::where('payroll_id', $id)->with('payroll')->get();

        if ($payroll->payrollType->name == 'Planilla de sueldos') {
            return DataTables::of($data)
                ->editColumn('code', function ($data) {
                    return $data->employee->agent_code;
                })->editColumn('employee', function ($data) {
                    return $data->employee->first_name . ' ' . $data->employee->last_name;
                })->editColumn('montly_salary', function ($data) {
                    return $this->moduleUtil->num_f($data->montly_salary, $add_symbol = true, $precision = 2);
                })->editColumn('regular_salary', function ($data) {
                    return $this->moduleUtil->num_f($data->regular_salary, $add_symbol = true, $precision = 2);
                })->editColumn('commissions', function ($data) {
                    return $this->moduleUtil->num_f($data->commissions, $add_symbol = true, $precision = 2);
                })->editColumn('extra_hours', function ($data) {
                    return $this->moduleUtil->num_f($data->extra_hours, $add_symbol = true, $precision = 2);
                })->editColumn('other_income', function ($data) {
                    return $this->moduleUtil->num_f($data->other_income, $add_symbol = true, $precision = 2);
                })->editColumn('total_income', function ($data) {
                    return '<b>' . $this->moduleUtil->num_f($data->total_income, $add_symbol = true, $precision = 2) . '</b>';
                })->editColumn('isss', function ($data) {
                    return $this->moduleUtil->num_f($data->isss, $add_symbol = true, $precision = 2);
                })->editColumn('afp', function ($data) {
                    return $this->moduleUtil->num_f($data->afp, $add_symbol = true, $precision = 2);
                })->editColumn('rent', function ($data) {
                    return $this->moduleUtil->num_f($data->rent, $add_symbol = true, $precision = 2);
                })->editColumn('other_deductions', function ($data) {
                    return $this->moduleUtil->num_f($data->other_deductions, $add_symbol = true, $precision = 2);
                })->editColumn('total_deductions', function ($data) {
                    return '<b>' . $this->moduleUtil->num_f($data->total_deductions, $add_symbol = true, $precision = 2) . '</b>';
                })->editColumn('total_to_pay', function ($data) {
                    return '<b>' . $this->moduleUtil->num_f($data->total_to_pay, $add_symbol = true, $precision = 2) . '</b>';
                })
                ->rawColumns(['code', 'employee', 'montly_salary', 'days', 'regular_salary', 'commissions', 'extra_hours', 'other_income', 'total_income', 'isss', 'afp', 'rent', 'other_deductions', 'total_deductions', 'total_to_pay'])
                ->make(true);
        }

        if ($payroll->payrollType->name == 'Planilla de honorarios') {
            return DataTables::of($data)
                ->editColumn('code', function ($data) {
                    return $data->employee->agent_code;
                })->editColumn('employee', function ($data) {
                    return $data->employee->first_name . ' ' . $data->employee->last_name;
                })->editColumn('dni', function ($data) {
                    return $data->employee->dni;
                })->editColumn('regular_salary', function ($data) {
                    return $this->moduleUtil->num_f($data->regular_salary, $add_symbol = true, $precision = 2);
                })->editColumn('rent', function ($data) {
                    return $this->moduleUtil->num_f($data->rent, $add_symbol = true, $precision = 2);
                })->editColumn('total_to_pay', function ($data) {
                    return '<b>' . $this->moduleUtil->num_f($data->total_to_pay, $add_symbol = true, $precision = 2) . '</b>';
                })
                ->rawColumns(['code', 'employee', 'dni', 'montly_salary', 'rent', 'total_to_pay'])
                ->make(true);
        }

        if ($payroll->payrollType->name == 'Planilla de aguinaldos') {
            return DataTables::of($data)
                ->editColumn('code', function ($data) {
                    return $data->employee->agent_code;
                })->editColumn('employee', function ($data) {
                    return $data->employee->first_name . ' ' . $data->employee->last_name;
                })->editColumn('date_admission', function ($data) {
                    return $this->moduleUtil->format_date($data->start_date);
                })->editColumn('end_date', function ($data) {
                    return $this->moduleUtil->format_date($data->end_date);
                })->editColumn('montly_salary', function ($data) {
                    return $this->moduleUtil->num_f($data->montly_salary, $add_symbol = true, $precision = 2);
                })->editColumn('days', function ($data) {
                    if ($data->proportional == 1) {
                        return $data->days . '</br>Proporcional';
                    } else {
                        return $data->days;
                    }
                })->editColumn('bonus', function ($data) {
                    return $this->moduleUtil->num_f($data->bonus, $add_symbol = true, $precision = 2);
                })->editColumn('rent', function ($data) {
                    return $this->moduleUtil->num_f($data->rent, $add_symbol = true, $precision = 2);
                })->editColumn('total_to_pay', function ($data) {
                    return '<b>' . $this->moduleUtil->num_f($data->total_to_pay, $add_symbol = true, $precision = 2) . '</b>';
                })
                ->rawColumns(['code', 'employee', 'date_admmission', 'end_date', 'montly_salary', 'days', 'bonus', 'rent', 'total_to_pay'])
                ->make(true);
        }

        if ($payroll->payrollType->name == 'Planilla de vacaciones') {
            return DataTables::of($data)
                ->editColumn('code', function ($data) {
                    return $data->employee->agent_code;
                })->editColumn('employee', function ($data) {
                    return $data->employee->first_name . ' ' . $data->employee->last_name;
                })->editColumn('start_date', function ($data) {
                    return $this->moduleUtil->format_date($data->start_date);
                })->editColumn('end_date', function ($data) {
                    return $this->moduleUtil->format_date($data->end_date);
                })->editColumn('proportional', function ($data) {
                    if ($data->proportional == 1) {
                        return 'Proporcional</br>(' . $data->days . ' días)';
                    } else {
                        return 'Completa';
                    }
                })->editColumn('montly_salary', function ($data) {
                    return $this->moduleUtil->num_f($data->montly_salary, $add_symbol = true, $precision = 2);
                })->editColumn('vacation_bonus', function ($data) {
                    return $this->moduleUtil->num_f($data->vacation_bonus, $add_symbol = true, $precision = 2);
                })->editColumn('regular_salary', function ($data) {
                    return $this->moduleUtil->num_f($data->regular_salary, $add_symbol = true, $precision = 2);
                })->editColumn('total_to_pay', function ($data) {
                    return $this->moduleUtil->num_f($data->total_to_pay, $add_symbol = true, $precision = 2);
                })
                ->rawColumns(['code', 'employee', 'date_admmission', 'end_date', 'montly_salary', 'proportional', 'regular_salary', 'vacation_bonus', 'total_to_pay'])
                ->make(true);
        }
    }

    public function recalculate($id)
    {
        if (!auth()->user()->can('plantilla.recalculate')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->with('paymentPeriod', 'payrollType')->firstOrFail();

            if ($payroll->payrollStatus->name == 'Calculada') {
                DB::beginTransaction();

                PayrollDetail::where('payroll_id', $payroll->id)->forceDelete();
                $this->calculate($payroll);

                DB::commit();

                $output = [
                    'success' => 1,
                    'msg' => __('payroll.recalculation_done_successfully')
                ];
            } else {
                $output = [
                    'success' => 0,
                    'msg' => __('payroll.failed_to_recalculate')
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


    /** Approve payroll */
    public function approve(Request $request, $id)
    {
        if (!auth()->user()->can('payroll.approve')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                DB::beginTransaction();
                $business_id = request()->session()->get('user.business_id');
                $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->firstOrFail();
                $user_id = auth()->user()->id;
                $user = User::findOrFail($user_id);

                if (Hash::check($request->input('password'), $user->password)) {
                    $status = PayrollStatus::where('name', 'Aprobada')->where('business_id', $business_id)->first();
                    $payroll->payroll_status_id = $status->id;
                    $payroll->approval_date = Carbon::now();
                    $payroll->update();

                    if ($request->input('downloadFile') == 1) {
                        $file = $this->generatePaymentFiles($payroll->id);

                        $output = [
                            'success' => 1,
                            'msg' => __('payroll.send_approve_payroll'),
                            'download' => true,
                            'file' => $file
                        ];
                    } else {
                        $output = [
                            'success' => 1,
                            'msg' => __('payroll.approve_payroll')
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
                    'msg' => __('rrhh.error')
                ];
            }
            return $output;
        }
    }


    /** Send Payment Slips payroll */
    public function paymentSlips(Request $request, $id)
    {
        if (!auth()->user()->can('payroll.paymentSlips')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                DB::beginTransaction();
                $business_id = request()->session()->get('user.business_id');
                $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->firstOrFail();
                $this->sendEmailPaymentSlips($payroll);

                $output = [
                    'success' => 1,
                    'msg' => __('payroll.send_payment_slips')
                ];
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


    /** Pay payroll */
    public function pay(Request $request, $id)
    {
        if (!auth()->user()->can('payroll.pay')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                DB::beginTransaction();
                $business_id = request()->session()->get('user.business_id');
                $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->firstOrFail();
                $user_id = auth()->user()->id;
                $user = User::findOrFail($user_id);

                if (Hash::check($request->input('password'), $user->password)) {
                    $status = PayrollStatus::where('name', 'Pagada')->where('business_id', $business_id)->first();
                    $payroll->payroll_status_id = $status->id;
                    $payroll->pay_date = Carbon::now();
                    $payroll->update();

                    foreach ($payroll->payrollDetails as $payrollDetail) {
                        $incomeDiscounts = RrhhIncomeDiscount::where('employee_id', $payrollDetail->employee_id)
                            ->where('start_date', '<=', $payroll->end_date)->get();

                        foreach ($incomeDiscounts as $incomeDiscount) {
                            $numIncomeDiscount = $incomeDiscount->paymentPeriod->days * $incomeDiscount->quota;
                            $numPayroll = $payroll->paymentPeriod->days;
                            $cantQuota = $numPayroll / $numIncomeDiscount;

                            if ($cantQuota < 1) {
                                $quotasApplied = $incomeDiscount->quota * $cantQuota;
                                $incomeDiscount->quotas_applied = $quotasApplied;
                                $incomeDiscount->balance_to_date = $incomeDiscount->balance_to_date - ($incomeDiscount->quota_value * $incomeDiscount->quotas_applied);
                                $incomeDiscount->update();
                            }

                            if ($cantQuota == 1) {
                                $quotasApplied = $incomeDiscount->quota * $cantQuota;
                                $incomeDiscount->quotas_applied = $quotasApplied;
                                $incomeDiscount->balance_to_date = $incomeDiscount->balance_to_date - ($incomeDiscount->quota_value * $incomeDiscount->quotas_applied);
                                $incomeDiscount->update();
                            }

                            if ($cantQuota > 1) {
                                $quotasApplied = $incomeDiscount->quota * 1;
                                $incomeDiscount->quotas_applied = $quotasApplied;
                                $incomeDiscount->balance_to_date = $incomeDiscount->balance_to_date - ($incomeDiscount->quota_value * $incomeDiscount->quotas_applied);
                                $incomeDiscount->update();
                            }
                        }
                    }

                    if ($request->input('sendEmail') == 1) {
                        $this->sendEmailPaymentSlips($payroll);

                        $output = [
                            'success' => 1,
                            'msg' => __('payroll.send_pay_payroll')
                        ];
                    } else {
                        $output = [
                            'success' => 1,
                            'msg' => __('payroll.pay_payroll')
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
                    'msg' => __('rrhh.error')
                ];
            }
            return $output;
        }
    }


    /** Pay payroll */
    function sendEmailPaymentSlips($payroll)
    {
        $business_id = request()->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);
        foreach ($payroll->payrollDetails as $payrollDetail) {
            $employee = Employees::where('id', $payrollDetail->employee_id)->where('business_id', $business_id)->with('user')->firstOrFail();
            $employee->notify(new PaymentSplisNotification($payroll, $business_id, $payrollDetail, $employee->first_name, $employee->last_name, $this->employeeUtil));
        }
    }


    //Generate payments slips for print
    public function generatePaymentSlips($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->firstOrFail();
        $start_date = $this->employeeUtil->getDate($payroll->start_date, true);
        $startDate = [];
        $endDate = [];
        $start_date = '';
        $end_date = '';
        if ($payroll->payrollType->name == 'Planilla de aguinaldos' || $payroll->payrollType->name == 'Planilla de vacaciones') {
            for ($i = 0; $i < count($payroll->payrollDetails); $i++) {
                $startDate[$i] = $this->employeeUtil->getDate($payroll->payrollDetails[$i]->start_date, true);
                $endDate[$i] = $this->employeeUtil->getDate($payroll->payrollDetails[$i]->end_date, true);
            }
        } else {
            $start_date = $this->employeeUtil->getDate($payroll->start_date, true);
            $end_date = $this->employeeUtil->getDate($payroll->end_date, true);
        }

        $pdf = \PDF::loadView('payroll.print_payroll', compact('payroll', 'business', 'start_date', 'startDate', 'endDate', 'end_date'));

        $pdf->setPaper('letter', 'portrait');
        return $pdf->stream('payrollDetail.pdf');
    }

    //Generate payments files
    public function generatePaymentFiles($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->with('payrollType')->firstOrFail();
        $payrollDetails = PayrollDetail::where('payroll_id', $id)->with('payroll')->get();
        $banks = Bank::select('banks.id as id', 'banks.name as name')
            ->join('employees', 'employees.bank_id', '=', 'banks.id')
            ->where('banks.business_id', $business_id)
            ->get();

        // Definir el nombre del archivo zip y crear una nueva instancia de ZipArchive
        $zip_file = 'Archivos_de_pago.zip';
        $zip = new \ZipArchive();

        //Crear archivo zip y abrirlo
        \Storage::disk('local')->put($zip_file,  $zip);
        $zip->open(public_path('uploads/' . $zip_file), \ZipArchive::CREATE);

        // Recorrer el array con un foreach
        foreach ($banks as $bank) {
            $file = $bank->name . '.csv';

            // Guardar el archivo excel en el disco local
            Excel::store(new PaymentFileReportExport($payroll, $payrollDetails, $bank), $file, 'local', \Maatwebsite\Excel\Excel::CSV);

            // Añadir el archivo excel al archivo zip
            $zip->addFile(public_path('uploads/' . $file), $file);
        }

        // Cerrar el archivo zip
        $zip->close();

        // Devolver el archivo zip para descargarlo
        $response = response()->download(public_path('uploads/' . $zip_file));

        // Retornar la respuesta con el archivo zip
        return $response;
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

    //Calcular payroll
    public function calculate($payroll)
    {
        $business_id = request()->session()->get('user.business_id');

        //Obtener empleados
        $employees = Employees::where('business_id', $business_id)
            ->where('date_admission', '<=', $payroll->end_date)
            //->where('status', 1)
            ->get();

        if (count($employees) > 0) {
            foreach ($employees as $employee) {
                $salaryHistory = RrhhSalaryHistory::where('employee_id', $employee->id)
                    ->where('current', 1)
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($salaryHistory) {
                    $details['montly_salary'] = $salaryHistory->new_salary;
                    $typeWage = RrhhTypeWage::where('id', $employee->type_id)->first();

                    if ($payroll->payrollType->name == 'Planilla de sueldos') {
                        if ($typeWage->type == 'Ley de salario') { //----------------------LEY DE SALARIO----------------------
                            $discountHO = 0;
                            $discountCom = 0;
                            $discountOD = 0;
                            $discountOI = 0;

                            $incomeHO = 0;
                            $incomeCom = 0;
                            $incomeOD = 0;
                            $incomeOI = 0;

                            $diasIncapacidad = 0;
                            $startDatePayroll = Carbon::parse($payroll->start_date);
                            $endDatePayroll = Carbon::parse($payroll->end_date);

                            $incapacidades = RrhhAbsenceInability::where('type', 'Incapacidad')
                                ->where('start_date', '<=', $payroll->end_date)
                                ->where('employee_id', $employee->id)
                                ->get();

                            $incomeDiscounts = RrhhIncomeDiscount::join('rrhh_type_income_discounts as type', 'type.id', '=', 'rrhh_income_discounts.rrhh_type_income_discount_id')
                                ->select('rrhh_income_discounts.id as id', 'rrhh_income_discounts.*', 'type.payroll_column')
                                ->where('rrhh_income_discounts.employee_id', $employee->id)
                                ->where('rrhh_income_discounts.start_date', '<=', $payroll->end_date)
                                ->where('rrhh_income_discounts.deleted_at', null)
                                ->get();


                            //Calcular los días de incapacidad
                            foreach ($incapacidades as $incapacidad) {
                                $startDateIncapacidad = Carbon::parse($incapacidad->start_date);
                                $endDateIncapacidad = Carbon::parse($incapacidad->end_date);

                                if ($incapacidad->start_date >= $payroll->start_date && $incapacidad->end_date <= $payroll->end_date) {
                                    $diasIncapacidad += $endDateIncapacidad->diffInDays($startDateIncapacidad);
                                }

                                if ($incapacidad->start_date >= $payroll->start_date && $incapacidad->end_date > $payroll->end_date) {
                                    $diasIncapacidad += $endDatePayroll->diffInDays($startDateIncapacidad);
                                }

                                if ($incapacidad->start_date < $payroll->start_date && $incapacidad->end_date <= $payroll->end_date && $incapacidad->end_date >= $payroll->start_date) {
                                    $diasIncapacidad += $endDateIncapacidad->diffInDays($startDatePayroll);
                                }

                                if ($incapacidad->start_date < $payroll->start_date && $incapacidad->end_date > $payroll->end_date) {
                                    $diasIncapacidad += $endDatePayroll->diffInDays($startDatePayroll);
                                }
                            }

                            //Obteniendo el calculo total de cada ingreso o descuento
                            foreach ($incomeDiscounts as $incomeDiscount) {
                                $payrollColumnHO = 'Horas extras';
                                $payrollColumnCom = 'Comisiones';
                                $payrollColumnOD = 'Otras deducciones';
                                $payrollColumnOI = 'Otros ingresos';

                                ($incomeDiscount->rrhhTypeIncomeDiscount->type == 1) ? $incomeHO += $this->incomeDiscount($incomeDiscount, $payrollColumnHO, $payroll) : $discountHO += $this->incomeDiscount($incomeDiscount, $payrollColumnHO, $payroll);
                                ($incomeDiscount->rrhhTypeIncomeDiscount->type == 1) ? $incomeCom += $this->incomeDiscount($incomeDiscount, $payrollColumnCom, $payroll) : $discountCom += $this->incomeDiscount($incomeDiscount, $payrollColumnCom, $payroll);
                                ($incomeDiscount->rrhhTypeIncomeDiscount->type == 1) ? $incomeOD += $this->incomeDiscount($incomeDiscount, $payrollColumnOD, $payroll) : $discountOD += $this->incomeDiscount($incomeDiscount, $payrollColumnOD, $payroll);
                                ($incomeDiscount->rrhhTypeIncomeDiscount->type == 1) ? $incomeOI += $this->incomeDiscount($incomeDiscount, $payrollColumnOI, $payroll) : $discountOI += $this->incomeDiscount($incomeDiscount, $payrollColumnOI, $payroll);
                            }

                            //Calcular los días trabajados
                            if ($employee->date_admission >= $payroll->start_date) {
                                $daysPayroll = $endDatePayroll->diffInDays($employee->date_admission);
                                $details['days'] = $daysPayroll - $diasIncapacidad;
                            } else {
                                $details['days'] = abs($payroll->days - $diasIncapacidad);
                            }

                            $details['hours'] = 8;
                            $details['commissions'] = $incomeCom - $discountCom;
                            $details['extra_hours'] = $incomeHO - $discountHO;
                            $details['other_income'] = $incomeOI - $discountOI;
                            $details['regular_salary'] = $details['montly_salary'] / 30 * $details['days'];
                            $details['total_income'] = $details['regular_salary'] + $details['commissions'] + $details['extra_hours'] + $details['other_income'];

                            //Calcular ISSS, AFP Renta
                            $details['isss'] = $this->payrollUtil->calculateIsss($details['total_income'], $business_id, $payroll->isr_id);
                            $details['afp'] = $this->payrollUtil->calculateAfp($details['total_income'], $business_id, $payroll->isr_id);
                            $details['rent'] = $this->payrollUtil->calculateRent($details['total_income'], $business_id, $payroll->isr_id, $details['isss'], $details['afp']);       

                            $details['other_deductions'] = $discountOD - $incomeOD;
                            $details['total_deductions'] = $details['isss'] + $details['afp'] + $details['rent'] + $details['other_deductions'];
                            $details['total_to_pay'] = bcdiv(($details['total_income'] - $details['total_deductions']), 1, 2);
                            $details['employee_id']  = $employee->id;
                            $details['payroll_id']  = $payroll->id;

                            //Create register
                            PayrollDetail::create($details);
                        }
                    }

                    if ($payroll->payrollType->name == 'Planilla de honorarios') {
                        if ($typeWage->type == 'Honorario') { //----------------------HONORARIO----------------------
                            if ($employee->date_admission >= $payroll->start_date) {
                                $endDatePayroll = Carbon::parse($payroll->end_date);
                                $daysPayroll = $endDatePayroll->diffInDays($employee->date_admission);
                                $details['days'] = $daysPayroll;
                            } else {
                                $details['days'] = $payroll->days;
                            }
                            $details['regular_salary'] = $details['montly_salary'] / 30 * $details['days'];
                            $details['rent'] = $details['regular_salary'] * 0.1;
                            $details['total_to_pay'] = $details['regular_salary'] - $details['rent'];
                            $details['employee_id']  = $employee->id;
                            $details['payroll_id']  = $payroll->id;

                            //Create register
                            PayrollDetail::create($details);
                        }
                    }

                    if ($payroll->payrollType->name == 'Planilla de aguinaldos') {
                        if ($typeWage->type == 'Ley de salario') { //----------------------LEY DE SALARIO----------------------
                            $details['start_date'] = Carbon::parse($employee->date_admission);
                            $endDatePayroll = Carbon::parse($payroll->end_date);

                            if ($employee->fired_date != null && $employee->status == 0) {
                                if ($employee->fired_date > $endDatePayroll) {
                                    $details['end_date'] = $endDatePayroll;
                                    $seconds = strtotime($endDatePayroll) - strtotime($details['start_date']);
                                } else {
                                    $details['end_date'] = Carbon::parse($employee->fired_date);
                                    $seconds = strtotime($details['end_date']) - strtotime($details['start_date']);
                                }
                            } else {
                                $details['end_date'] = $endDatePayroll;
                                $seconds = strtotime($endDatePayroll) - strtotime($details['start_date']);
                            }

                            $years = $this->employeeUtil->secondsToYear($seconds);

                            $bonusCalculations = BonusCalculation::where('business_id', $business_id)->where('status', 1)->orderBy('until', 'DESC')->get();
                            $lawDiscountsRenta = LawDiscount::join('institution_laws as institution_law', 'institution_law.id', '=', 'law_discounts.institution_law_id')
                                ->join('payment_periods as payment_period', 'payment_period.id', '=', 'law_discounts.payment_period_id')
                                ->select('law_discounts.id as id', 'law_discounts.*', 'institution_law.name as institution_law')
                                ->where('institution_law.name', 'Renta')
                                ->where('payment_period.id', $payroll->isr_id)
                                ->where('law_discounts.business_id', $business_id)
                                ->where('law_discounts.deleted_at', null)
                                ->get();

                            foreach ($bonusCalculations as $bonusCalculation) {
                                if ($years >= $bonusCalculation->from && $years < $bonusCalculation->until) {
                                    $details['days'] = $bonusCalculation->days;
                                    if ($bonusCalculation->proportional == 0) {
                                        $details['bonus'] = $details['montly_salary'] / 30 * $bonusCalculation->days;
                                        $details['proportional'] = null;
                                    } else {
                                        $daysWorked = $this->employeeUtil->getDays($seconds);
                                        $details['bonus'] = ((($details['montly_salary'] / 30) * $bonusCalculation->days) / 365) * $daysWorked;
                                        $details['proportional'] = 1;
                                    }

                                    $setting = RrhhSetting::where('business_id', $business_id)->first();

                                    if ($details['bonus'] <= $setting->exempt_bonus) {
                                        $details['rent'] = 0;
                                    } else {
                                        $value = $details['bonus'] - $setting->exempt_bonus;
                                        foreach ($lawDiscountsRenta as $lawDiscountRenta) {
                                            if ($value >= $lawDiscountRenta->from && $value < $lawDiscountRenta->until) {
                                                $details['rent'] = ((($details['bonus'] - $setting->exempt_bonus) - $lawDiscountRenta->base) * ($lawDiscountRenta->employee_percentage / 100)) + $lawDiscountRenta->fixed_fee;
                                            }
                                        }
                                    }
                                }
                            }

                            $details['total_to_pay'] = $details['bonus'] - $details['rent'];
                            $details['employee_id']  = $employee->id;
                            $details['payroll_id']  = $payroll->id;

                            //Create register
                            PayrollDetail::create($details);
                        }
                    }

                    if ($payroll->payrollType->name == 'Planilla de vacaciones') {
                        if ($typeWage->type == 'Ley de salario') { //----------------------LEY DE SALARIO----------------------
                            $setting = RrhhSetting::where('business_id', $business_id)->first();
                            $details['regular_salary'] = ($details['montly_salary'] / 30) * 15;
                            $details['vacation_bonus'] = $details['regular_salary'] * $setting->vacation_percentage / 100;

                            $details['start_date'] = Carbon::parse($employee->date_admission);
                            $startDatePayroll = Carbon::parse($payroll->start_date);
                            $endDatePayroll = Carbon::parse($payroll->end_date);

                            $seconds = strtotime($endDatePayroll) - strtotime($details['start_date']);
                            $year = $this->employeeUtil->secondsToYear($seconds);

                            if ($employee->status == 1) {
                                if ($year >= 1) {
                                    $endDate = $details['start_date']->addYears($year);
                                    if ($endDate >= $startDatePayroll && $endDate <= $endDatePayroll) {
                                        $secondsDays = strtotime($endDate) - strtotime($endDate->subYear());
                                        $details['proportional'] = 0; //Vacación completa
                                        $details['days'] = $this->employeeUtil->getDays($secondsDays);
                                        $details['total_to_pay'] = $details['regular_salary'] + $details['vacation_bonus'];
                                        $details['employee_id']  = $employee->id;
                                        $details['payroll_id']  = $payroll->id;
                                        $details['end_date'] = $endDate->addYear();

                                        //Create register
                                        PayrollDetail::create($details);
                                    }
                                }
                            } else {
                                $details['end_date'] = Carbon::parse($employee->fired_date);
                                if ($year >= 1) {
                                    $startDate = $details['start_date']->addYears($year);
                                } else {
                                    $startDate = $details['start_date']->subYear();
                                }

                                if ($details['end_date'] >= $startDatePayroll && $details['end_date'] <= $endDatePayroll) {
                                    $secondsDays = strtotime($details['end_date']) - strtotime($startDate);
                                    $details['days'] = $this->employeeUtil->getDays($secondsDays);

                                    if ($details['days'] > 365) {
                                        $details['days'] = $details['days'] - 365;
                                    }
                                    if ($details['days'] >= 200 && $details['days'] < 365) {
                                        $details['proportional'] = 1; //Vacación proporcional
                                        $details['vacation_bonus'] = ($details['days'] / 365) * $details['vacation_bonus'];
                                        $details['total_to_pay'] = $details['regular_salary'] + $details['vacation_bonus'];
                                        $details['employee_id']  = $employee->id;
                                        $details['payroll_id']  = $payroll->id;

                                        //Create register
                                        PayrollDetail::create($details);
                                    }
                                    if ($details['days'] == 365) {
                                        $details['proportional'] = 0; //Vacación completa
                                        $details['total_to_pay'] = $details['regular_salary'] + $details['vacation_bonus'];
                                        $details['employee_id']  = $employee->id;
                                        $details['payroll_id']  = $payroll->id;

                                        //Create register
                                        PayrollDetail::create($details);
                                    }
                                }
                            }
                        }
                    }

                    $status = PayrollStatus::where('name', 'Calculada')->where('business_id', $business_id)->first();
                    $payroll->payroll_status_id = $status->id;
                    $payroll->update();
                } else {
                    //Mensaje que debe completar la info de los empleados
                }
            }
        } else {
            //Mensaje que no hay empleados
        }
    }


    public function exportPayroll($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $payroll = Payroll::where('id', $id)->where('business_id', $business_id)->with('payrollType')->firstOrFail();
        $payrollDetails = PayrollDetail::where('payroll_id', $id)->with('payroll')->get();

        if ($payroll->payrollType->name == 'Planilla de sueldos') {
            return Excel::download(
                new PayrollSalaryReportExport($payroll, $payrollDetails, $business, $this->moduleUtil),
                'Planilla de sueldos - ' . $payroll->name . '.xlsx'
            );
        }
        if ($payroll->payrollType->name == 'Planilla de honorarios') {
            return Excel::download(
                new PayrollHonoraryReportExport($payroll, $payrollDetails, $business, $this->moduleUtil),
                'Planilla de honorarios - ' . $payroll->name . '.xlsx'
            );
        }
        if ($payroll->payrollType->name == 'Planilla de aguinaldos') {
            return Excel::download(
                new PayrollBonusReportExport($payroll, $payrollDetails, $business, $this->moduleUtil),
                'Planilla de aguinaldos - ' . $payroll->name . '.xlsx'
            );
        }
        if ($payroll->payrollType->name == 'Planilla de vacaciones') {
            return Excel::download(
                new PayrollVacationReportExport($payroll, $payrollDetails, $business, $this->moduleUtil),
                'Planilla de vacaciones - ' . $payroll->name . '.xlsx'
            );
        }
    }


    //Get income or discount from an employee
    public function incomeDiscount($incomeDiscount, $payroll_column, $payroll)
    {
        $incomeOrDiscount = 0;
        if ($incomeDiscount->payroll_column == $payroll_column) {
            $numIncomeDiscount = $incomeDiscount->paymentPeriod->days * $incomeDiscount->quota;
            $numPayroll = $payroll->paymentPeriod->days;
            $cantQuota = $numPayroll / $numIncomeDiscount;

            if ($cantQuota < 1) {
                $quotasApplied = $incomeDiscount->quota * $cantQuota;
                $incomeOrDiscount = $incomeDiscount->quota_value * $quotasApplied;
            }

            if ($cantQuota == 1) {
                $quotasApplied = $incomeDiscount->quota * $cantQuota;
                $incomeOrDiscount = $incomeDiscount->quota_value * $quotasApplied;
            }

            if ($cantQuota > 1) {
                $quotasApplied = $incomeDiscount->quota * 1;
                $incomeOrDiscount = $incomeDiscount->quota_value * $quotasApplied;
            }
        }

        return $incomeOrDiscount;
    }
}
