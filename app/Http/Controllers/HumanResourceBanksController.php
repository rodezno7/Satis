<?php

namespace App\Http\Controllers;

use App\HumanResourceBanks;
use Illuminate\Http\Request;
use DB;
use DataTables;

class HumanResourceBanksController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        if ( !auth()->user()->can('rrhh_catalogues.create') ) {
            abort(403, 'Unauthorized action.');
        }        

        return view('rrhh.catalogues.banks.create');
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
            'name' => 'required|unique:human_resource_banks',
        ]);

        try {


            $input_details = $request->all();
            $input_details['created_by'] =  auth()->user()->id;

            $bank =  HumanResourceBanks::create($input_details);


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
     * @param  \App\HumanResourceBanks  $humanResourceBanks
     * @return \Illuminate\Http\Response
     */
    public function show(HumanResourceBanks $humanResourceBanks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HumanResourceBanks  $humanResourceBanks
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $item = HumanResourceBanks::findOrFail($id);

        return view('rrhh.catalogues.banks.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HumanResourceBanks  $humanResourceBanks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|unique:human_resource_banks,name,'.$id,
        ]);

        try {

            $input_details = $request->all();
            $item = HumanResourceBanks::findOrFail($id);

            $input_details['updated_by'] = auth()->user()->id;

            
            $item->update($input_details);


            $output = [
                'success' => 1,
                'msg' => __('rrhh.updated_successfully')
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
     * @param  \App\HumanResourceBanks  $humanResourceBanks
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        if (!auth()->user()->can('rrhh_catalogues.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            try {

                $count = DB::table('human_resource_employees')
                ->where('bank_id', $id)               
                ->count();

                if ($count > 0) {

                    $output = [
                        'success' => false,
                        'msg' => __('rrhh.item_has_childs')
                    ];

                } else {

                    $item = HumanResourceBanks::findOrFail($id);
                    $item->forceDelete();
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

    public function getBanksData() {

        if ( !auth()->user()->can('rrhh_catalogues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $data = DB::table('human_resource_banks')
        ->select('human_resource_banks.*');


        return DataTables::of($data)->toJson();
    }

}
