<?php

namespace App\Http\Controllers;

use App\MovementType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MovementTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('movement_type.view') && !auth()->user()->can('movement_type.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $business_id = $business_id = request()->session()->get('user.business_id');
            
            $movement_types = MovementType::where('business_id', $business_id)->select(['name', 'description', 'type', 'id']);

            return Datatables::of($movement_types)
                ->addColumn(
                    'action',
                    '@can("movement_type.update")
                    <button data-href="{{ action(\'MovementTypeController@edit\', [$id]) }}"
                        class="btn btn-xs btn-primary edit_movement_types_button">
                        <i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")
                    </button>
                    &nbsp;
                    @endcan
                    @can("movement_type.delete")
                    <button data-href="{{ action(\'MovementTypeController@destroy\', [$id]) }}"
                        class="btn btn-xs btn-danger delete_movement_types_button">
                        <i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")
                    </button>
                    @endcan'
                )
                ->editColumn(
                    'type',
                    '@if($type == "input")
                    <span class="badge" style="background-color: #5cb85c;">{{ __("movement_type." . $type) }}</span>
                    @else
                    <span class="badge" style="background-color: #d9534f;">{{ __("movement_type." . $type) }}</span>
                    @endif'
                )
                ->removeColumn('id')
                ->rawColumns([2, 3])
                ->make(false);
        }

        return view('movement_type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('movement_type.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('movement_type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('movement_type.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'description', 'type']);

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $movement_type = MovementType::create($input);

            $output = [
                'success' => true,
                'data' => $movement_type,
                'msg' => __("movement_type.added_success")
            ];

            \Log::info('Movement type ' . $movement_type->id . ' successfully added by ' . $request->session()->get('user.id'));

        } catch (\Exception $e) {
            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

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
     * @param  \App\MovementType  $movementType
     * @return \Illuminate\Http\Response
     */
    public function show(MovementType $movementType)
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
        if (!auth()->user()->can('movement_type.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $movement_type = MovementType::findOrFail($id);

            return view('movement_type.edit')->with(compact('movement_type'));
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
        if (!auth()->user()->can('movement_type.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'description', 'type']);

                $movement_type = MovementType::findOrFail($id);
                $movement_type->fill($input);
                $movement_type->save();

                $output = [
                    'success' => true,
                    'msg' => __("movement_type.updated_success")
                ];

                \Log::info('Movement type ' . $movement_type->id . ' successfully updated by ' . $request->session()->get('user.id'));

            } catch (\Exception $e) {
                \Log::emergency("File: " . $e->getFile(). " Line: " . $e->getLine(). " Message: " . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (!auth()->user()->can('movement_type.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $movement_type = MovementType::findOrFail($id);

                $movement_type->delete();

                $output = [
                    'success' => true,
                    'msg' => __("movement_type.deleted_success")
                ];

                \Log::info('Movement type ' . $id . ' successfully deleted by ' . $request->session()->get('user.id'));

            } catch (\Exception $e) {
                \Log::emergency("File: " . $e->getFile(). " Line: " . $e->getLine(). " Message: " . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
