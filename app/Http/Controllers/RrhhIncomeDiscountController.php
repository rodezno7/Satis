<?php

namespace App\Http\Controllers;

use App\Employees;
use App\RrhhIncomeDiscount;
use App\RrhhTypeIncomeDiscount;
use App\PaymentPeriod;
use App\Business;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;
use App\Utils\ModuleUtil;

class RrhhIncomeDiscountController extends Controller
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
        if ( !auth()->user()->can('rrhh_income_discount.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $employee = Employees::where('id', $id)->where('business_id', $business_id)->with('rrhhIncomeDiscounts')->first();
        
        return view('rrhh.income_discounts.index', compact('employee', 'business'));
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

    function createIncomeDiscount($id) 
    {
        if ( !auth()->user()->can('rrhh_income_discount.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $typeIncomes = RrhhTypeIncomeDiscount::where('business_id', $business_id)->where('type', 1)->get();
        $typeDiscounts = RrhhTypeIncomeDiscount::where('business_id', $business_id)->where('type', 2)->get();
        $paymentPeriods = PaymentPeriod::where('business_id', $business_id)->get();
        $employee_id = $id;

        return view('rrhh.income_discounts.create', compact('employee_id', 'typeDiscounts', 'typeIncomes', 'paymentPeriods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_income_discount.create') ) {
            abort(403, 'Unauthorized action.');
        }
        if($request->type == 1){//income
            $request->validate([
                'payment_period_id'     => 'required',
                'type'                  => 'required',
                'rrhh_type_income_id'   => 'required',
                'employee_id'           => 'required',
                'total_value'           => 'required',
                'quota'                 => 'required',
                'quota_value'           => 'required',
                'start_date'            => 'required',
                'end_date'              => 'required',
            ]);
        }else{//discount
            $request->validate([
                'payment_period_id'     => 'required',
                'type'                  => 'required',
                'rrhh_type_discount_id' => 'required',
                'employee_id'           => 'required',
                'total_value'           => 'required',
                'quota'                 => 'required',
                'quota_value'           => 'required',
                'start_date'            => 'required',
                'end_date'              => 'required',
            ]);
        }        

        try {
            $input_details = $request->all();
            if($request->type == 1){
                $input_details['rrhh_type_income_discount_id'] = $request->rrhh_type_income_id;
            }

            if($request->type == 2){
                $input_details['rrhh_type_income_discount_id'] = $request->rrhh_type_discount_id;
            }
            
            $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));
            $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));

            DB::beginTransaction();
    
            RrhhIncomeDiscount::create($input_details);
    
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
     * @param  \App\RrhhIncomeDiscount  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhIncomeDiscount $rrhhDocuments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhIncomeDiscount  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        if ( !auth()->user()->can('rrhh_income_discount.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $incomeDiscount = RrhhIncomeDiscount::findOrFail($id);
        $typeIncomes = RrhhTypeIncomeDiscount::where('business_id', $business_id)->where('type', 1)->get();
        $typeDiscounts = RrhhTypeIncomeDiscount::where('business_id', $business_id)->where('type', 2)->get();
        $paymentPeriods = PaymentPeriod::where('business_id', $business_id)->get();
        $employee_id = $incomeDiscount->employee_id;

        return view('rrhh.income_discounts.edit', compact('employee_id', 'incomeDiscount', 'typeDiscounts', 'typeIncomes', 'paymentPeriods'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhIncomeDiscount  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    public function updateIncomeDiscount(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_income_discount.edit') ) {
            abort(403, 'Unauthorized action.');
        }
        if($request->type == 1){//income
            $request->validate([
                'payment_period_id'     => 'required',
                'type'                  => 'required',
                'rrhh_type_income_id'   => 'required',
                'total_value'           => 'required',
                'quota'                 => 'required',
                'quota_value'           => 'required',
                'start_date'            => 'required',
                'end_date'              => 'required',
            ]);
        }else{//discount
            $request->validate([
                'payment_period_id'     => 'required',
                'type'                  => 'required',
                'rrhh_type_discount_id' => 'required',
                'total_value'           => 'required',
                'quota'                 => 'required',
                'quota_value'           => 'required',
                'start_date'            => 'required',
                'end_date'              => 'required',
            ]);
        }        

        try {
            $input_details = $request->all();
            if($request->type == 1){
                $input_details['rrhh_type_income_discount_id'] = $request->rrhh_type_income_id;
            }

            if($request->type == 2){
                $input_details['rrhh_type_income_discount_id'] = $request->rrhh_type_discount_id;
            }
            
            $input_details['start_date'] = $this->moduleUtil->uf_date($request->input('start_date'));
            $input_details['end_date'] = $this->moduleUtil->uf_date($request->input('end_date'));

            DB::beginTransaction();
    
            $item = RrhhIncomeDiscount::findOrFail($request->id);
            $item->update($input_details);
    
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
     * @param  \App\RrhhIncomeDiscount  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        if (!auth()->user()->can('rrhh_income_discount.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $item = RrhhIncomeDiscount::findOrFail($id);
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
