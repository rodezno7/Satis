<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RrhhTypePersonnelAction;
use DB;
use DataTables;

class RrhhTypePersonnelActionController extends Controller
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
    public function getTypePersonnelActionData() {
        if ( !auth()->user()->can('rrhh_catalogues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id =  request()->session()->get('user.business_id');
        
        $data = RrhhTypePersonnelAction::select('rrhh_type_personnel_actions.*')
        ->where('business_id', $business_id)
        ->get();

        $actions = DB::table('rrhh_action_type as actions_type')
                ->join('rrhh_required_actions as actions', 'actions.id', '=', 'actions_type.rrhh_required_action_id')
                ->join('rrhh_type_personnel_actions as type', 'type.id', '=', 'actions_type.rrhh_type_personnel_action_id')
                ->select('actions_type.id as id', 'type.id as type_id', 'actions.name as actions_name')
                ->get();

        return DataTables::of($data)->addColumn(
            'required_authorization',
            function ($row) {
                if ($row->required_authorization == 1) {
                    $html = 'Requiere';
                } else {
                    $html = 'No requiere';
                }
                return $html;
            }
        )->addColumn(
            'apply_to_many',
            function ($row) {
                if ($row->apply_to_many == 1) {
                    $html = 'Si';
                } else {
                    $html = 'No';
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

        $actions = DB::table('rrhh_class_actions as class_actions')
        ->join('rrhh_class_personnel_actions as class', 'class.id', '=', 'class_actions.rrhh_class_personnel_action_id')
        ->join('rrhh_required_actions as actions', 'actions.id', '=', 'class_actions.rrhh_required_action_id')
        ->select('class_actions.id as id', 'actions.name as name', 'class.id as class_id', 'class.name as class_name')
        ->get();
        $clases = DB::table('rrhh_class_personnel_actions')->get();
        
        return view('rrhh.catalogues.types_personnel_actions.create', compact('actions', 'clases'));
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
        ]);

        try {
            $input_details = $request->only(['name', 'required_authorization', 'apply_to_many']);
            $input_details['business_id'] =  request()->session()->get('user.business_id');
            $typeAction =  RrhhTypePersonnelAction::create($input_details);
            
            $actions = $request->input('action');
            foreach ($actions as $key => $action) {
                $requiredAction = DB::table('rrhh_class_actions as class_actions')
                ->join('rrhh_class_personnel_actions as class', 'class.id', '=', 'class_actions.rrhh_class_personnel_action_id')
                ->join('rrhh_required_actions as actions', 'actions.id', '=', 'class_actions.rrhh_required_action_id')
                ->select('actions.id as id', 'actions.name as name', 'class.id as class_id', 'class.name as class_name')
                ->where('class_actions.id', $action)
                ->first();

                $actionCreated = DB::table('rrhh_action_type')->where('rrhh_type_personnel_action_id', $typeAction->id)->where('rrhh_required_action_id', $requiredAction->id)->where('rrhh_class_personnel_action_id', $requiredAction->class_id)->first();
                //Crear el registro si no existe
                if ($actionCreated === null) {
                    DB::table('rrhh_action_type')->insert(
                        ['rrhh_type_personnel_action_id' => $typeAction->id, 'rrhh_required_action_id' => $requiredAction->id, 'rrhh_class_personnel_action_id' => $requiredAction->class_id]
                    );
                }
            }
            
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
     * @param  \App\RrhhTypePersonnelAction  $rrhhTypePersonnelAction
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhTypePersonnelAction $rrhhTypePersonnelAction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhTypePersonnelAction  $rrhhTypePersonnelAction
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        if ( !auth()->user()->can('rrhh_catalogues.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $item = RrhhTypePersonnelAction::findOrFail($id);
        $actionTypes = DB::table('rrhh_action_type')->where('rrhh_type_personnel_action_id', $item->id)->get();
        $actions = DB::table('rrhh_class_actions as class_actions')
        ->join('rrhh_class_personnel_actions as class', 'class.id', '=', 'class_actions.rrhh_class_personnel_action_id')
        ->join('rrhh_required_actions as actions', 'actions.id', '=', 'class_actions.rrhh_required_action_id')
        ->select('class_actions.id as id', 'actions.name as name', 'class.id as class_id', 'class.name as class_name', 'class_actions.rrhh_required_action_id as rrhh_required_action_id', 'class_actions.rrhh_class_personnel_action_id')
        ->get();
        $clases = DB::table('rrhh_class_personnel_actions')->get();

        return view('rrhh.catalogues.types_personnel_actions.edit', compact('item', 'actions', 'clases', 'actionTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhTypePersonnelAction  $rrhhTypePersonnelAction
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
            if($request->required_authorization == 1){
                $input_details['required_authorization'] = true;
            }else{
                $input_details['required_authorization'] = false;
            }

            if($request->apply_to_many == 1){
                $input_details['apply_to_many'] = true;
            }else{
                $input_details['apply_to_many'] = false;
            }
            
            $typeAction = RrhhTypePersonnelAction::findOrFail($id);
            $typeAction->update($input_details);
            
            DB::table('rrhh_action_type')->where('rrhh_type_personnel_action_id', $typeAction->id)->delete();
            $actions = $request->input('action');

            foreach ($actions as $key => $action) {
                $requiredAction = DB::table('rrhh_class_actions as class_actions')
                ->join('rrhh_class_personnel_actions as class', 'class.id', '=', 'class_actions.rrhh_class_personnel_action_id')
                ->join('rrhh_required_actions as actions', 'actions.id', '=', 'class_actions.rrhh_required_action_id')
                ->select('actions.id as id', 'actions.name as name', 'class.id as class_id', 'class.name as class_name')
                ->where('class_actions.id', $action)
                ->first();
                
                $actionCreated = DB::table('rrhh_action_type')->where('rrhh_type_personnel_action_id', $typeAction->id)->where('rrhh_required_action_id', $requiredAction->id)->where('rrhh_class_personnel_action_id', $requiredAction->class_id)->first();
                //Crear el registro si no existe
                if ($actionCreated === null) {
                    DB::table('rrhh_action_type')->insert(
                        ['rrhh_type_personnel_action_id' => $typeAction->id, 'rrhh_required_action_id' => $requiredAction->id, 'rrhh_class_personnel_action_id' => $requiredAction->class_id]
                    );
                }
            }
            
            $output = [
                'success' => true,
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
     * @param  \App\RrhhTypePersonnelAction  $rrhhTypePersonnelAction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (!auth()->user()->can('rrhh_catalogues.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            try {
                $count = DB::table('rrhh_personnel_actions')
                ->where('rrhh_type_personnel_action_id', $id)               
                ->count();

                if ($count > 0) {
                    $output = [
                        'success' => false,
                        'msg' => __('rrhh.item_has_childs')
                    ];
                } else {
                    $item = RrhhTypePersonnelAction::findOrFail($id);
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
