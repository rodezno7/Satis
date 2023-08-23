<?php

namespace App\Http\Controllers;

use App\Business;
use App\CalculationType;
use App\InstitutionLaw;
use App\LawDiscount;
use Illuminate\Http\Request;
use DB;
use DataTables;

class LawDiscountController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->can('planilla-catalogues.view')){
            abort(403, "Unauthorized action.");
        }
        return view('planilla.catalogues.law_discounts.index');
    }

    public function getLawDiscounts(){
        if ( !auth()->user()->can('plantilla-catolgues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = DB::table('law_discounts as ld')
            ->join('institution_laws as institution_law', 'institution_law.id', '=', 'ld.institution_law_id')
            ->join('calculation_types as calculation_type', 'calculation_type.id', '=', 'ld.calculation_type_id')
            ->select('ld.id as id', 'ld.from', 'ld.until', 'ld.base', 'ld.fixed_fee', 'ld.employee_percentage', 'ld.employer_value', 'ld.status', 'institution_law.name as institution_law', 'calculation_type.name as calculation_type')
            ->where('ld.business_id', $business_id)
            ->where('ld.deleted_at', null)
            ->get();

        return DataTables::of($data)->editColumn('status', function ($data) {
            if($data->status == 1){
                return __('rrhh.active');
            }else{
                return __('rrhh.inactive');
            }
        })->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ( !auth()->user()->can('planilla-catalogues.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $institutions = InstitutionLaw::where('business_id', $business_id)->get();
        $calculation_types = CalculationType::where('business_id', $business_id)->get();
        return view('planilla.catalogues.law_discounts.create', compact('institutions', 'calculation_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ( !auth()->user()->can('planilla-catalogues.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'institution_law_id'  => 'required',
            'from'                => 'required|numeric',
            'until'               => 'required|numeric',
            'base'                => 'required|numeric',
            'employer_value'      => 'required|numeric',
            'fixed_fee'           => 'required|numeric',
            'employee_percentage' => 'required|numeric',
            'calculation_type_id' => 'required',
        ]);

        try {
            $input_details = $request->all();
            $input_details['business_id'] = request()->session()->get('user.business_id');
            DB::beginTransaction();
            LawDiscount::create($input_details);
    
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
    public function edit($id)
    {
        if ( !auth()->user()->can('planilla-catalogues.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $lawDiscount = LawDiscount::where('id', $id)->where('business_id', $business_id)->first();
        $institutions = InstitutionLaw::where('business_id', $business_id)->get();
        $calculation_types = CalculationType::where('business_id', $business_id)->get();
        return view('planilla.catalogues.law_discounts.edit', compact('lawDiscount', 'institutions', 'calculation_types'));
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
        if ( !auth()->user()->can('planilla-catalogues.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'institution_law_id'  => 'required',
            'from'                => 'required|numeric',
            'until'               => 'required|numeric',
            'base'                => 'required|numeric',
            'employer_value'      => 'required|numeric',
            'fixed_fee'           => 'required|numeric',
            'employee_percentage' => 'required|numeric',
            'calculation_type_id' => 'required',
        ]);

        try {
            $input_details = $request->all();
            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');
            $item = LawDiscount::where('id', $id)->where('business_id', $business_id)->first();
            $lawDiscount = $item->update($input_details);
    
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('planilla-catalogues.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $item = LawDiscount::where('id', $id)->where('business_id', $business_id)->first();
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
