<?php

namespace App\Http\Controllers;

use App\TypeEntrie;
use App\AccountingEntrie;
use Illuminate\Http\Request;
use Datatables;
use DB;

class TypeEntrieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('entries.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('entries.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate(
            [
                'name' => 'required|unique:type_entries',
                'description' => 'required',
                'short_name' => 'required',
            ],
            [
                'name.required' => __('accounting.name_required'),
                'name.unique' => __('accounting.name_unique'),
                'description.required' => __('accounting.description_required'),
                'short_name.required' => __('accounting.short_name_required'),
            ]
        );
        if($request->ajax())
        {
            $type = TypeEntrie::create($request->all());
            return response()->json([
                "msj" => 'Created'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TypeEntrie  $typeEntrie
     * @return \Illuminate\Http\Response
     */
    public function show(TypeEntrie $typeEntrie)
    {
        return response()->json($typeEntrie);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TypeEntrie  $typeEntrie
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $typeEntrie = TypeEntrie::findOrFail($id);
        return response()->json($typeEntrie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TypeEntrie  $typeEntrie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $typeEntrie = TypeEntrie::findOrFail($id);
        $validateData = $request->validate(
            [
                'name' => 'required|unique:type_entries,name,'.$typeEntrie->id,
                'description' => 'required',
                'short_name' => 'required',
            ],
            [
                'name.required' => __('accounting.name_required'),
                'name.unique' => __('accounting.name_unique'),
                'description.required' => __('accounting.description_required'),
                'short_name.required' => __('accounting.short_name_required'),
            ]
        );
        if($request->ajax()){
            $typeEntrie->update($request->all());
            return response()->json([
                "msj" => 'Updated'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TypeEntrie  $typeEntrie
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try{
                $typeEntrie = TypeEntrie::findOrFail($id);
                $entries = AccountingEntrie::where('type_entrie_id', $typeEntrie->id)->count();

                if($entries > 0){
                    $output = [
                        'success' => false,
                        'msg' =>  __('accounting.type_has_entries')
                    ];
                }
                else{
                    $typeEntrie->forceDelete();
                    $output = [
                        'success' => true,
                        'msg' => __('accounting.type_deleted')
                    ];
                }
            }
            catch (\Exception $e){
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    public function getTypes()
    {
        $types = TypeEntrie::select('id', 'name')->get();
        return response()->json($types);
    }

    public function getTypesData()
    {
        $types = DB::table('type_entries as type')
        ->select('type.*');

        return DataTables::of($types)->toJson();
    }
}
