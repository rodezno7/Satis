<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RrhhTypeWage;
use DB;
use DataTables;

class RrhhTypeWageController extends Controller
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
    public function getTypeWagesData() {

        if ( !auth()->user()->can('rrhh_catalogues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id =  request()->session()->get('user.business_id');
        $data = DB::table('rrhh_type_wages')
        ->select('rrhh_type_wages.*')
        ->where('business_id', $business_id)
        ->where('deleted_at', null);

        return DataTables::of($data)
        ->addColumn(
            'isss',
            function ($row) {
                if ($row->isss == 1) {

                    $html = 'Si aplica';
                } else {

                    $html = 'No aplica';
                }
                return $html;
            }
        )->addColumn(
            'afp',
            function ($row) {
                if ($row->afp == 1) {

                    $html = 'Si aplica';
                } else {

                    $html = 'No aplica';
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

        return view('rrhh.catalogues.types_wages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) { 
        //dd($request);       
        if ( !auth()->user()->can('rrhh_catalogues.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required',           
        ]);

        try {
            $input_details = $request->only(['name']);
            $input_details['business_id'] =  request()->session()->get('user.business_id');
            if($request->has('isss')){
                $input_details['isss'] = true;
            }
            if($request->has('afp')){
                $input_details['afp'] = true;
            }
            if($request->input('wage_law')){
                $input_details['type'] = 'Ley de salario';
            }
            if($request->input('honorary')){
                $input_details['type'] = 'Honorario';
            }
            $typeWage =  RrhhTypeWage::create($input_details);
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
     * @param  \App\RrhhTypeWage  $humanResourceBanks
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhTypeWage $humanResourceBanks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhTypeWage  $humanResourceBanks
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $item = RrhhTypeWage::findOrFail($id);

        return view('rrhh.catalogues.types_wages.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhTypeWage  $humanResourceBanks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required',
        ]);

        try {
            $input_details = $request->only(['name']);
            if($request->has('isss')){
                $input_details['isss'] = true;
            }else{
                $input_details['isss'] = false;
            }
            if($request->has('afp')){
                $input_details['afp'] = true;
            }else{
                $input_details['afp'] = false;
            }
            if($request->input('wage_law')){
                $input_details['type'] = 'Ley de salario';
            }
            if($request->input('honorary')){
                $input_details['type'] = 'Honorario';
            }

            $item = RrhhTypeWage::findOrFail($id);
            $item->update($input_details);
            $output = [
                'success' => true,
                'msg' => __('rrhh.added_successfully')
            ];
        } catch (\Exception $e) {
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
     * @param  \App\RrhhTypeWage  $humanResourceBanks
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (!auth()->user()->can('rrhh_catalogues.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            try {
                $count = DB::table('employees')
                ->where('type_id', $id)               
                ->count();

                if ($count > 0) {
                    $output = [
                        'success' => false,
                        'msg' => __('rrhh.item_has_childs')
                    ];
                } else {
                    $item = RrhhTypeWage::findOrFail($id);
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
