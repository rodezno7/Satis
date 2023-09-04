<?php

namespace App\Http\Controllers;

use App\RrhhIncomeDiscount;
use Illuminate\Http\Request;
use App\RrhhTypeIncomeDiscount;
use DB;
use DataTables;

class RrhhTypeIncomeDiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function getTypeIncomeDiscountData() {

        if ( !auth()->user()->can('rrhh_catalogues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id =  request()->session()->get('user.business_id');
        $data = DB::table('rrhh_type_income_discounts')
        ->select('rrhh_type_income_discounts.*')
        ->where('business_id', $business_id)
        ->where('deleted_at', null);

        return DataTables::of($data)
        ->addColumn(
            'type',
            function ($row) {
                if ($row->type == 1) {

                    $html = __('rrhh.income');
                } else {

                    $html = __('rrhh.discount');
                }
                return $html;
            }
        )->addColumn(
            'status',
            function ($row) {
                if ($row->status == 1) {

                    $html = 'Activo';
                } else {

                    $html = 'Inactivo';
                }
                return $html;
            }
        )->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        if ( !auth()->user()->can('rrhh_catalogues.create') ) {
            abort(403, 'Unauthorized action.');
        }        

        $planillaColumns = RrhhTypeIncomeDiscount::$planillaColumns;
        return view('rrhh.catalogues.types_income_discounts.create', compact('planillaColumns'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {      
        if ( !auth()->user()->can('rrhh_catalogues.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required',     
            'type' => 'required',
            'planilla_column' => 'required',           
        ]);

        try {
            $input_details = $request->all();
            $planillaColumns = RrhhTypeIncomeDiscount::$planillaColumns;
            for ($i=0; $i < count($planillaColumns); $i++) { 
                if($request->planilla_column == $i){
                    $input_details['planilla_column'] = $planillaColumns[$i];
                }
            }

            $input_details['business_id'] =  request()->session()->get('user.business_id');

            RrhhTypeIncomeDiscount::create($input_details);
            $output = [
                'success' => true,
                'msg' => __('rrhh.added_successfully')
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

    /**
     * Display the specified resource.
     *
     * @param  \App\RrhhTypeIncomeDiscount  $rrhhTypeWage
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhTypeIncomeDiscount $rrhhTypeWage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhTypeIncomeDiscount  $rrhhTypeWage
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $planillaColumns = RrhhTypeIncomeDiscount::$planillaColumns;
        $item = RrhhTypeIncomeDiscount::findOrFail($id);

        return view('rrhh.catalogues.types_income_discounts.edit', compact('planillaColumns', 'item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhTypeIncomeDiscount  $rrhhTypeWage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required',     
            'type' => 'required',
            'planilla_column' => 'required',           
        ]);

        try {
            $input_details = $request->all();
            $planillaColumns = RrhhTypeIncomeDiscount::$planillaColumns;
            for ($i=0; $i < count($planillaColumns); $i++) { 
                if($request->planilla_column == $i){
                    $input_details['planilla_column'] = $planillaColumns[$i];
                }
            }            

            $item = RrhhTypeIncomeDiscount::findOrFail($id);
            $item->update($input_details);
            $output = [
                'success' => true,
                'msg' => __('rrhh.updated_successfully')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }
        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RrhhTypeIncomeDiscount  $rrhhTypeWage
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (!auth()->user()->can('rrhh_catalogues.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            try {
                $business_id = request()->session()->get('user.business_id');
                $count = RrhhIncomeDiscount::where('rrhh_type_income_discount_id', $id)->where('business_id', $business_id)->count();

                if ($count > 0) {
                    $output = [
                        'success' => false,
                        'msg' => __('rrhh.item_has_childs')
                    ];
                } else {
                    $item = RrhhTypeIncomeDiscount::findOrFail($id);
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
}
