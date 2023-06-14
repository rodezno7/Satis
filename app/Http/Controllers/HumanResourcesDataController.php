<?php

namespace App\Http\Controllers;

use App\HumanResourcesData;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Illuminate\Validation\Rule;

class HumanResourcesDataController extends Controller
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
    public function create()
    {
        //
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
            'value' => [
                'required',
                Rule::unique('human_resources_datas')
                ->where(function ($query) {
                    return $query->where('human_resources_header_id', request('human_resources_header_id'));
                })
                ->where(function ($query) {
                    return $query->where('business_id', request()->session()->get('user.business_id'));
                })
            ],           
        ]);

        try {
            $code = '';
            if($request->input('human_resources_header_id') > 1 && $request->input('human_resources_header_id') < 6) {
                
                if($request->input('human_resources_header_id') > 1 && $request->input('human_resources_header_id') < 6) {
                    $last_correlative = DB::table('human_resources_datas')
                    ->where('human_resources_header_id', $request->input('human_resources_header_id'))
                    ->count();
                    if ($last_correlative > 0) {
                        $correlative = $last_correlative + 1;
                    } else {
                        $correlative = 1;
                    }
                    $correlative = str_pad($correlative, 5, "0", STR_PAD_LEFT);            
                    $code = $this->getCorrelative($request->input('human_resources_header_id'), $correlative);
                }

                $code = $this->getCorrelative($request->input('human_resources_header_id'), $code);
            }
            $human_resource_item = new HumanResourcesData();
            $human_resource_item->business_id = $request->session()->get('user.business_id');
            $human_resource_item->human_resources_header_id = $request->input('human_resources_header_id');
            $human_resource_item->status = 1;
            $human_resource_item->code = $code;
            $human_resource_item->value = $request->input('value');
            $human_resource_item->save();

            $output = [
                'success' => 1,
                'msg' => __('rrhh.added_successfully')
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
     * Display the specified resource.
     *
     * @param  \App\HumanResourcesData  $humanResourcesData
     * @return \Illuminate\Http\Response
     */
    public function show(HumanResourcesData $humanResourcesData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HumanResourcesData  $humanResourcesData
     * @return \Illuminate\Http\Response
     */
    public function edit(HumanResourcesData $humanResourcesData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HumanResourcesData  $humanResourcesData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'value' => 'required|unique:human_resources_datas,value,'.$id,
        ]);

        try {

            $input_details = $request->all();
            $item = HumanResourcesData::findOrFail($id);
            
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
     * @param  \App\HumanResourcesData  $humanResourcesData
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (!auth()->user()->can('rrhh_catalogues.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            try {
                $count = DB::table('human_resource_employees')
                ->where('afp_id', $id)
                ->orWhere('civil_status_id', $id)
                ->orWhere('department_id', $id)
                ->orWhere('nationality_id', $id)
                ->orWhere('position_id', $id)
                ->orWhere('profession_id', $id)
                ->orWhere('type_id', $id)
                ->count();

                if ($count > 0) {
                    $output = [
                        'success' => false,
                        'msg' => __('rrhh.item_has_childs')
                    ];
                } else {
                    $item = HumanResourcesData::findOrFail($id);
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

    public function getCatalogueData($id) {
        if ( !auth()->user()->can('rrhh_catalogues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = DB::table('human_resources_datas')
        ->select('human_resources_datas.*')
        ->where('human_resources_header_id', $id)
        ->where('business_id', $business_id);


        return DataTables::of($data)
        ->addColumn(
            'status',
            function ($row) {
                if ($row->status == 1) {

                    $html = 'Activo';
                } else {

                    $html = 'Inactivo';
                }
                return $html;
            }
        )
        ->toJson();
    }

    public function createItem($id) {

        if ( !auth()->user()->can('rrhh_catalogues.create') ) {
            abort(403, 'Unauthorized action.');
        }
        
        $type_item = '';
        $header_id = $id;

        switch ($id) {
            case 1:
            $type_item = __('rrhh.marital_status');
            break;

            case 2:
            $type_item = __('rrhh.department');
            break;

            case 3:
            $type_item = __('rrhh.position');
            break;

            case 4:
            $type_item = __('rrhh.afp');
            break;

            case 5:
            $type_item = __('rrhh.type');
            break;

            case 6:
            $type_item = __('rrhh.nationality');
            break;
            
            case 7:
            $type_item = __('rrhh.profession_occupation');
            break;

            case 8:
            $type_item = __('rrhh.way_to_pay');
            break;

            case 9:
            $type_item = __('rrhh.document_type');
            break;
        }

        return view('rrhh.catalogues.create', compact('type_item', 'header_id'));
    }

    public function editItem($id) {

        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $item = HumanResourcesData::findOrFail($id);

        
        $type_item = '';
        $header_id = $item->human_resources_header_id;

        switch ($header_id) {
            case 1:
            $type_item = __('rrhh.marital_status');
            break;

            case 2:
            $type_item = __('rrhh.department');
            break;

            case 3:
            $type_item = __('rrhh.position');
            break;

            case 4:
            $type_item = __('rrhh.afp');
            break;

            case 5:
            $type_item = __('rrhh.type');
            break;

            case 6:
            $type_item = __('rrhh.nationality');
            break;

            case 7:
            $type_item = __('rrhh.profession_occupation');
            break;
            
            case 8:
            $type_item = __('rrhh.way_to_pay');
            break;

            case 9:
            $type_item = __('rrhh.document_type');
            break;
        }

        return view('rrhh.catalogues.edit', compact('type_item', 'header_id', 'item'));
    }


    public function getCorrelative($id, $code) {        

        $count = DB::table('human_resources_datas')
        ->where('human_resources_header_id', $id)
        ->where('code', $code)
        ->count();

        if($count > 0) {

            $correlative = (int) $code + 1;            
            $code = str_pad($correlative, 5, "0", STR_PAD_LEFT);

            return $this->getCorrelative($id, $code);

        } else {

            return $code;
        }
        
    }

}
