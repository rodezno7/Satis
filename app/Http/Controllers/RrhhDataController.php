<?php

namespace App\Http\Controllers;

use App\RrhhData;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Illuminate\Validation\Rule;

class RrhhDataController extends Controller
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

    public function getCatalogueData($id) {
        if ( !auth()->user()->can('rrhh_catalogues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = DB::table('rrhh_datas')
        ->select('rrhh_datas.*')
        ->where('rrhh_header_id', $id)
        ->where('business_id', $business_id)
        ->where('deleted_at', null);


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
        ->addColumn(
            'date_required',
            function ($row) {
                if ($row->date_required == 1) {

                    $html = 'Requerida';
                } else {

                    $html = 'No requerida';
                }
                return $html;
            }
        )
        ->toJson();
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

            case 10:
            $type_item = __('rrhh.special_capabilities');
            break;

            case 11:
            $type_item = __('rrhh.employee_classification');
            break;

            case 12:
            $type_item = __('rrhh.types_studies');
            break;

            case 13:
            $type_item = __('rrhh.types_absences');
            break;
            
            case 14:
            $type_item = __('rrhh.types_inabilities');
            break;

            case 15:
            $type_item = __('rrhh.types_relationships');
            break;

            case 16:
            $type_item = __('rrhh.types_income_discounts');
            break;
        }

        return view('rrhh.catalogues.create', compact('type_item', 'header_id'));
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
                Rule::unique('rrhh_datas')
                ->where(function ($query) {
                    return $query->where('rrhh_header_id', request('rrhh_header_id'));
                })
                ->where(function ($query) {
                    return $query->where('business_id', request()->session()->get('user.business_id'));
                })
                ->where(function ($query) {
                    return $query->where('deleted_at', null);
                })
            ],           
        ]);

        try {
            
            $code = '';
            if($request->input('rrhh_header_id') > 1 && $request->input('rrhh_header_id') < 6) {
                
                if($request->input('rrhh_header_id') > 1 && $request->input('rrhh_header_id') < 6) {
                    $last_correlative = DB::table('rrhh_datas')
                    ->where('rrhh_header_id', $request->input('rrhh_header_id'))
                    ->count();
                    if ($last_correlative > 0) {
                        $correlative = $last_correlative + 1;
                    } else {
                        $correlative = 1;
                    }
                    $correlative = str_pad($correlative, 5, "0", STR_PAD_LEFT);            
                    $code = $this->getCorrelative($request->input('rrhh_header_id'), $correlative);
                }

                $code = $this->getCorrelative($request->input('rrhh_header_id'), $code);
            }
            $date_required = null;
            if($request->input('rrhh_header_id') == 9) {
                if($request->input('date_required')){
                    $date_required = 1;
                }else{
                    $date_required = 0;
                }
            }
            $item = new RrhhData();
            $item->business_id = $request->session()->get('user.business_id');
            $item->rrhh_header_id = $request->input('rrhh_header_id');
            $item->status = 1;
            $item->code = $code;
            $item->date_required = $date_required;
            $item->value = $request->input('value');
            $item->save();

            $output = [
                'success' => 1,
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
     * Display the specified resource.
     *
     * @param  \App\RrhhData  $rrhhData
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhData $rrhhData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhData  $rrhhData
     * @return \Illuminate\Http\Response
     */
    public function edit(RrhhData $rrhhData)
    {
        //
    }

    public function editItem($id) {

        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $item = RrhhData::findOrFail($id);

        
        $type_item = '';
        $header_id = $item->rrhh_header_id;

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
            
            case 10:
            $type_item = __('rrhh.special_capabilities');
            break;

            case 11:
            $type_item = __('rrhh.employee_classification');
            break;

            case 12:
            $type_item = __('rrhh.types_studies');
            break;

            case 13:
            $type_item = __('rrhh.types_absences');
            break;
            
            case 14:
            $type_item = __('rrhh.types_inabilities');
            break;

            case 15:
            $type_item = __('rrhh.types_relationships');
            break;

            case 16:
            $type_item = __('rrhh.types_income_discounts');
            break;
        }

        return view('rrhh.catalogues.edit', compact('type_item', 'header_id', 'item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhData  $rrhhData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'value' => 'required|unique:rrhh_datas,value,'.$id,
        ]);

        try {
            $item = RrhhData::findOrFail($id);
            $item->status = $request->input('status');
            $item->date_required = $request->input('date_required');
            $item->value = $request->input('value');
            $item->save();

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
     * @param  \App\RrhhData  $rrhhData
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (!auth()->user()->can('rrhh_catalogues.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            try {
                $count = DB::table('employees')
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
                    $item = RrhhData::findOrFail($id);
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



    public function getCorrelative($id, $code) {        

        $count = DB::table('rrhh_datas')
        ->where('rrhh_header_id', $id)
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
