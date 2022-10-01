<?php

namespace App\Http\Controllers;

use App\Positions;
use App\System;
use App\Notifications\NewNotification;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\DB;
use App\Utils\ModuleUtil;

class ManagePositionsController extends Controller
{
    /**
     * Constructor
     *
     * @param Util $commonUtil
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

    // Mostrar vista para listar los cargos
    public function index(){
        if(!auth()->user()->can('positions.view') && !auth()->user()->can('positions.create')){
            abort(403, "Unauthorized action.");
        }
        $business_id = request()->session()->get('user.business_id');
        return view('manage_positions.index');
    }

    //Mostrar Lista de Cargos
    public function getPositionsData(){
        if(!auth()->user()->can('positions.view') && !auth()->user()->can('positions.create')){
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');
        $positions = DB::table('positions')
        ->select('positions.name', 'positions.descriptions', 'positions.id')
        ->whereNull('deleted_at')
        ->where('positions.business_id', $business_id);
        return DataTables::of($positions)
        ->addColumn(
            'action',
            '@can("positions.update")
            <button data-href="{{action(\'ManagePositionsController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_positions_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                &nbsp;
            @endcan
            @can("positions.delete")
                <button data-href="{{action(\'ManagePositionsController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_positions_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
            @endcan'
        )
        ->removeColumn('id')
        ->rawColumns([2])
        ->make(false);
    }

    public function datos(Request $request){
        
        // $business_id = request()->session()->get('user.business_id');
        // $user_id = request()->session()->get('user.id');
        return Positions::select('positions.name', 'positions.descriptions', 'positions.id')
        ->whereNull('deleted_at')->get();
        // ->where('positions.business_id', $business_id);

        // return $positions;
    }

    public function list()
    {
        return Positions::all();
    }

    //Mostrar el formulario para crear un nuevo Cargo
    public function  create(){
        if(!auth()->user()->can('positions.create')){
            abort(403, 'Unauthorized action.');
        }
        return view('manage_positions.create');
    }

    //Registrar el nuevo cargo en la BD
    public function store(Request $request){
        if(!auth()->user()->can('positions.create')){
            abort(403, 'Unauthorized action.'); 
        }

        try{
            $input = $request->only(['name', 'descriptions']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            $positions = Positions::create($input);
            $outpout = ['success' => true,
            'data' => $positions,
            'msg' => __("positions.added_success")];
        }catch(\Exception $e){
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $outpout = ['success' => false,
            'msg' => __("messages.something_went_wrong")];
        }
        return $outpout;
    }

    //Mostrar el formulario para editar un Cargo
    public function edit($id){
        if(!auth()->user()->can('positions.update')){
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            $business_id = request()->session()->get('user.business_id');
            $positions = Positions::where('business_id', $business_id)->find($id);

            return view('manage_positions.edit')
            ->with(compact('positions'));
        }
    }

    //Actualizar un cargo en la BD
    public function update(Request $request, $id){
        if (!auth()->user()->can('positions.update')) {
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            try{
                $input = $request->only(['name', 'descriptions']);
                $business_id = $request->session()->get('user.business_id');

                $positions = Positions::where('business_id', $business_id)->findOrFail($id);
                $positions->name = $input['name'];
                $positions->descriptions = $input['descriptions'];
                $positions->save();

                $outpout = ['success' => true,
                'data' => $positions,
                'msg' => __("positions.updated_success")];
            }catch(\Exception $e){
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
    
                $outpout = ['success' => false,
                'msg' => __("messages.something_went_wrong")];
            }
            return $outpout;
        }
    }

    public function destroy($id){
        if (!auth()->user()->can('positions.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            try{
                $business_id = request()->session()->get('user.business_id');
                $positions = Positions::where('business_id', $business_id)->find($id);
                
                $positions->delete();
                $outpout = ['success' => true,
                'data' => $positions,
                'msg' => __("positions.deleted_success")];
            }catch(\Exception $e){
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
    
                $outpout = ['success' => false,
                'msg' => __("messages.something_went_wrong")];
            }
            return $outpout;
        }
    }

    
}
