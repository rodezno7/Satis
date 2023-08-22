<?php

namespace App\Http\Controllers;

use App\CalculationType;
use App\PaymentPeriod;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use DB;
use DataTables;

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
            ->select('planilla.id as id', 'planilla.year', 'planilla.month', 'planilla.start_date', 'planilla.end_date', 'payment_period.name as payment_period', 'calculation_type.name as calculation_type')
            ->where('planilla.business_id', $business_id)
            ->where('planilla.deleted_at', null)
            ->get();

        return DataTables::of($data)->editColumn('period', function ($data) {
            return $this->moduleUtil->uf_date($data->start_date).' - '.$this->moduleUtil->uf_date($data->end_date);
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
        $paymentPeriods = PaymentPeriod::where('business_id', $business_id)->get();
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
        //
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
    public function edit($id)
    {
        //
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
