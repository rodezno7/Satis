<?php

namespace App\Http\Controllers;

use DataTables;
use App\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessTypeController extends Controller
{

    public function index()
    {
        if (!auth()->user()->can('business_type.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_type = BusinessType::all();

        return view('business_type.index', compact('business_type'));
    }

    public function getBusinessTypeData()
    {
        $customers = DB::table('business_types')
            ->select('*');

        return DataTables::of($customers)
            ->addColumn(
                'actions',
                function ($row) {
                    $html = '<div class="btn-group">
                <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (auth()->user()->can('business_type.update')) {
                        $html .= '<li><a href="#" data-href="' . action('BusinessTypeController@edit', [$row->id]) . '" class="edit_business_type_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }


                    if (auth()->user()->can('business_type.delete')) {
                        $html .= '<li><a href="#" onClick="deleteBusinessType(' . $row->id . ')"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '</ul></div>';
                    return $html;
                }
            )
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create()
    {
        return view('business_type.create');
    }


    public function store(Request $request)
    {
        if (!auth()->user()->can('business_type.create')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate(
            [
                'name' => 'required',
                'description' => 'required'
            ],
            [
                'name.required' => 'El nombre es requerido',
                'description' => 'La descripcion es requerida',
            ]
        );

        try {

            $business_type = new BusinessType();
            $business_type->name = trim($request->name);
            $business_type->description = trim($request->description);

            $business_type->save();

            $output = [
                'success' => true,
                'msg' => __("business.business_type_create"),
                'business_type_id' => $business_type->id,
                'business_type_name' => $business_type->name,
                'business_type_description' => $business_type->description,
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $business_type = BusinessType::findOrFail($id);
        return view('business_type.edit', compact('business_type'));
    }


    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('business_type.update')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate(
            [
                'name' => 'required',
                'description' => 'required'
            ],
            [
                'name.required' => 'El nombre es requerido',
                'description' => 'La descripcion es requerida',
            ]
        );

        try {

            $business_type = BusinessType::findOrFail($id);
            $business_type->name = trim($request->name);
            $business_type->description = trim($request->description);

            $business_type->save();

            $output = [
                'success' => true,
                'msg' => __("business.business_type_update"),
                'business_type_id' => $business_type->id,
                'business_type_name' => $business_type->name,
                'business_type_description' => $business_type->description,
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('business_type.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {

            BusinessType::find($id)->delete();
            $output = [
                'success' => true,
                'msg' => __("business.business_type_delete"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }
}
