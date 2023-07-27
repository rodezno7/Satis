<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employees;
use App\RrhhTypeContract;
use DB;
use DataTables;

class RrhhTypeContractController extends Controller
{
    public function index(){

    }

    public function getTypes(){

        if ( !auth()->user()->can('rrhh_catalogues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = RrhhTypeContract::where('business_id', $business_id)->where('deleted_at', null);
        
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
        if(!auth()->user()->can('rrhh_catalogues.create')){
            abort(403, "Unauthorized action.");
        }
        return view('rrhh.catalogues.types_contracts.create');
    }

    public function store(Request $request){
        if ( !auth()->user()->can('rrhh_catalogues.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'          => 'required',
            'template'      => 'required',
            'margin_top'    => 'required|numeric|between:0.01,3.00',
            'margin_bottom' => 'required|numeric|between:0.01,3.00',
            'margin_left'   => 'required|numeric|between:0.01,3.00',
            'margin_right'  => 'required|numeric|between:0.01,3.00',
        ]);

        try {
            DB::beginTransaction();
            $input_details = $request->only([
                'name', 
                'template',
                'margin_top',
                'margin_bottom',
                'margin_left',
                'margin_right',
            ]);
            $input_details['business_id'] = $request->session()->get('user.business_id');
            //dd($input_details);
            RrhhTypeContract::create($input_details);

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

        return redirect('rrhh-catalogues')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RrhhEconomicDependence  $rrhhDocuments
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!auth()->user()->can('rrhh_catalogues.view')){
            abort(403, "Unauthorized action.");
        }

        // $business_id = request()->session()->get('user.business_id');
        // $type = RrhhTypeContract::where('id', $id)->where('business_id', $business_id)->first();
        
        // return view('rrhh.catalogues.types_contracts.show', compact('type'));
        $business_id = request()->session()->get('user.business_id');
        $type = RrhhTypeContract::where('id', $id)->where('business_id', $business_id)->first();
        $pdf = \PDF::loadView(
            'rrhh.catalogues.types_contracts.show',
            compact([
                'type',
            ])
        );

        $pdf->setPaper('letter', 'portrait');
        return $pdf->stream(__('rrhh.contract') . '.pdf');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('rrhh_catalogues.update')){
            abort(403, "Unauthorized action.");
        }

        $business_id = request()->session()->get('user.business_id');
        $type = RrhhTypeContract::where('id', $id)->where('business_id', $business_id)->first();
        return view('rrhh.catalogues.types_contracts.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhTypeContract  $rrhhTypeContract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'          => 'required',
            'template'      => 'required',
            'margin_top'    => 'required|numeric|between:0.01,3.00',
            'margin_bottom' => 'required|numeric|between:0.01,3.00',
            'margin_left'   => 'required|numeric|between:0.01,3.00',
            'margin_right'  => 'required|numeric|between:0.01,3.00',
        ]);

        try {
            DB::beginTransaction();
            $input_details = $request->only([
                'name', 
                'template',
                'margin_top',
                'margin_bottom',
                'margin_left',
                'margin_right',
                'status',
            ]);

            $business_id = request()->session()->get('user.business_id');
            $type = RrhhTypeContract::where('id', $id)->where('business_id', $business_id)->first();
            $type->update($input_details);

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

        return redirect('rrhh-catalogues')->with('status', $output);
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
                ->where('bank_id', $id)               
                ->count();

                if ($count > 0) {
                    $output = [
                        'success' => false,
                        'msg' => __('rrhh.item_has_childs')
                    ];
                } else {
                    $item = RrhhTypeContract::findOrFail($id);
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
