<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UnitGroup;
use App\Unit;
use App\Product;
use App\UnitGroupLines;
use DataTables;
use DB;

class UnitGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
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
    public function store(Request $request)
    {
        if (!auth()->user()->can('unit.create')) {
            abort(403, 'Unauthorized action.');
        }

        try{
            $unit_ids = $request->input('unit_ids');
            $factors = $request->input('factor');
            $business_id = $request->session()->get('user.business_id');

            DB::beginTransaction();
            $group = New UnitGroup;
            $group->business_id = $business_id;
            $group->unit_id = $request->input('unit_parent');
            $group->description = $request->input('description');
            $group->save();

            $default = new UnitGroupLines;
            $default->unit_id = $request->input('unit_parent');
            $default->unit_group_id = $group->id;
            $default->factor = 1;
            $default->default = 1;
            $default->save();

            if (!empty($unit_ids))
            {
                $cont = 0;                
                while($cont < count($unit_ids))
                {
                    $detail = new UnitGroupLines;
                    $detail->unit_id = $unit_ids[$cont];
                    $detail->unit_group_id = $group->id;
                    $detail->factor = $factors[$cont];
                    $detail->default = 0;
                    $detail->save();
                    $cont = $cont + 1;
                } 
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("unit.group_added")
            ];
        }
        catch (\Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
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
        $UnitGroup = UnitGroup::where('id', $id)->first();
        return response()->json($UnitGroup);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $UnitGroup = UnitGroup::where('id', $id)->first();
        return response()->json($UnitGroup);
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
        if (!auth()->user()->can('unit.update')) {
            abort(403, 'Unauthorized action.');
        }

        try{
            $unit_ids = $request->input('eunit_ids');
            $factors = $request->input('efactor');
            $business_id = $request->session()->get('user.business_id');

            DB::beginTransaction();
            $group = UnitGroup::find($id);
            $group->business_id = $business_id;
            //$group->unit_id = $request->input('eunit_parent');
            $group->description = $request->input('edescription');
            $group->save();
            UnitGroupLines::where('unit_group_id', $id)->where('default', 0)->forceDelete();
            if (!empty($unit_ids))
            {
                $cont = 0;                
                while($cont < count($unit_ids))
                {
                    $detail = new UnitGroupLines;
                    $detail->unit_id = $unit_ids[$cont];
                    $detail->unit_group_id = $group->id;
                    $detail->factor = $factors[$cont];
                    $detail->default = 0;
                    $detail->save();
                    $cont = $cont + 1;
                } 
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("unit.group_updated")
            ];
        }
        catch (\Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
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
        $group = UnitGroup::find($id);
        $products = Product::where('unit_id', $id)->count();

        if($products > 0){
            $output = [
                'success' => false,
                'msg' => __("unit.group_has_children")
            ];
        }
        else{
            $group->delete();
            $output = [
                'success' => true,
                'msg' => __("unit.group_delete_success")
            ];
        }
        return $output;
    }

    public function getUnitGroupsData()
    {
        if (!auth()->user()->can('unit.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $unitGroups = UnitGroup::where('business_id', $business_id)->with('unit')->get();
        return DataTables::of($unitGroups)
        ->addColumn('action', function ($row){
            $action = "";
            if (auth()->user()->can('unit.update')){
                $action .= '<a class="btn btn-xs btn-primary" onClick="editUnitGroup('.$row->id.')"><i class="glyphicon glyphicon-edit"></i>'. __("messages.edit") .'</a>';
            }
            if (auth()->user()->can('unit.delete')){
                $action .= ' <a class="btn btn-xs btn-danger" onClick="deleteUnitGroup('.$row->id.')"><i class="glyphicon glyphicon-trash"></i>'. __("messages.delete") .'</a>';
            }
            return $action;            
        })       
        ->toJson();
    }

    public function groupHasLines($id)
    {
        $lines = DB::table('unit_group_lines as lines')
        ->join('units', 'units.id', '=', 'lines.unit_id')
        ->select('lines.*', 'units.actual_name', 'units.short_name')
        ->where('lines.unit_group_id', $id)
        ->where('default', 0)
        ->get();
        return $lines;
    }
}
