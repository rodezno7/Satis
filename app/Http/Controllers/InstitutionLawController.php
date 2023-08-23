<?php

namespace App\Http\Controllers;

use App\InstitutionLaw;
use Illuminate\Http\Request;
use DB;
use DataTables;

class InstitutionLawController extends Controller
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
        return view('planilla.catalogues.institution_laws.index');
    }

    public function getInstitutionLaws(){
        if ( !auth()->user()->can('plantilla-catolgues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = DB::table('institution_laws as il')
        ->select('il.id as id', 'il.name', 'il.description', 'il.employeer_number')
        ->where('il.business_id', $business_id)
        ->where('il.deleted_at', null)
        ->get();
        
        return DataTables::of($data)
        ->editColumn('employeer_number', function ($data) {
            if($data->employeer_number != null){
                return $data->employeer_number;
            }else{
                return '---';
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

        return view('planilla.catalogues.institution_laws.create');
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
            'name'                 => 'required',
            'description'          => 'required',
            'employeer_number'     => 'nullable|regex:/^[0-9]+$/',
        ]);

        try {
            $input_details = $request->all();
            $input_details['business_id'] = request()->session()->get('user.business_id');
            DB::beginTransaction();
    
            InstitutionLaw::create($input_details);
    
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
        $institutionLaw = InstitutionLaw::where('id', $id)->where('business_id', $business_id)->first();

        return view('planilla.catalogues.institution_laws.edit', compact('institutionLaw'));
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
        \Log::info($request);
        if ( !auth()->user()->can('planilla-catalogues.edit') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'                 => 'required',
            'description'          => 'required',
            'employeer_number'     => 'nullable|regex:/^[0-9]+$/',
        ]);

        try {
            $input_details = $request->all();
            DB::beginTransaction();
    
            $business_id = request()->session()->get('user.business_id');
            $item = InstitutionLaw::where('id', $id)->where('business_id', $business_id)->first();
            $institutionLaw = $item->update($input_details);
    
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
                $item = InstitutionLaw::where('id', $id)->where('business_id', $business_id)->first();
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
