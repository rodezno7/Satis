<?php

namespace App\Http\Controllers\Optics;

use App\Optics\MaterialType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class MaterialTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('material_type.view') && !auth()->user()->can('material_type.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $material_types = MaterialType::where('business_id', $business_id)
                    ->select(['name', 'description', 'id']);
            
            return Datatables::of($material_types)
                ->addColumn(
                    'action',
                    '@can("material_type.update")
                    <button data-href="{{ action(\'Optics\MaterialTypeController@edit\', [$id]) }}" class="btn btn-xs btn-primary edit_material_types_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                    &nbsp;
                    @endcan
                    @can("material_type.delete")
                    <button data-href="{{ action(\'Optics\MaterialTypeController@destroy\', [$id]) }}" class="btn btn-xs btn-danger delete_material_types_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('optics.material_type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('material_type.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        return view('optics.material_type.create')
            ->with(compact('quick_add'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('material_type.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'description']);

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $input['created_by'] = $request->session()->get('user.id');

            $material_type = MaterialType::create($input);
    
            $output = ['success' => true,
                'data' => $material_type,
                'msg' => __("material_type.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MaterialType  $materialType
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialType $materialType)
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
        if (!auth()->user()->can('material_type.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $material_type = MaterialType::find($id);

            return view('optics.material_type.edit')
                ->with(compact('material_type'));
        }
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
        if (!auth()->user()->can('material_type.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'description']);
                
                $material_type = MaterialType::findOrFail($id);
                $material_type->fill($input);
                $material_type->save();

                $output = ['success' => true,
                    'msg' => __("material_type.updated_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('material_type.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $material_type = MaterialType::findOrFail($id);
                $material_type->delete();

                $output = ['success' => true,
                    'msg' => __("material_type.deleted_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
