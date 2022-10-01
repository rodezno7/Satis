<?php

namespace App\Http\Controllers;

use App\Catalogue;
use App\FixedAsset;
use App\FixedAssetType;

use DataTables;
use Illuminate\Http\Request;

class FixedAssetTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		if(!auth()->user()->can('fixed_asset_type.view')) {
			abort(403, "Unauthorized action.");
		}

        if(request()->ajax()){
            $business_id = request()->user()->business_id;
            
            $fixed_asset_types = FixedAssetType::join('catalogues as c', 'fixed_asset_types.accounting_account_id', 'c.id')
                ->where('business_id', $business_id)
                ->select(
                    'fixed_asset_types.id',
                    'fixed_asset_types.name',
                    'fixed_asset_types.description',
                    'fixed_asset_types.percentage',
                    'c.name as account_name'
                );

            return DataTables::of($fixed_asset_types)
                ->addColumn('action', function($row){
                    $action = "";
                    if(auth()->user()->can('fixed_asset_type.edit')){
                        $action .= "<a class='btn btn-primary btn-xs edit_fixed_asset_type' href=". action("FixedAssetTypeController@edit", [$row->id]) ."><i class='glyphicon glyphicon-edit'></i></a>";
                    }
                    if(auth()->user()->can('fixed_asset_type.delete')){
                        $action .= "&nbsp;<a class='btn btn-danger btn-xs delete_fixed_asset_type' href=". action("FixedAssetTypeController@destroy", [$row->id]) ."><i class='glyphicon glyphicon-trash'></i></a>";
                    }
                    return $action;
                })->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('fixed_asset_type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('fixed_asset_type.create')) {
			abort(403, "Unauthorized action.");
		}
        
        return view('fixed_asset_type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!auth()->user()->can('fixed_asset_type.create')) {
			abort(403, "Unauthorized action.");
		}

        try {
            $input = $request->except('_token');
            $input['business_id'] = $request->user()->business_id;

            FixedAssetType::create($input);

            $output = [ 'success' => true, 'msg' => __("fixed_asset.fixed_asset_type_added_success") ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [ 'success' => false, 'msg' => __("messages.something_went_wrong") ];
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
        if(!auth()->user()->can('fixed_asset_type.edit')) {
			abort(403, "Unauthorized action.");
		}

        $fa_type = FixedAssetType::find($id);

        $accounting_account = [];
        if(!is_null($fa_type->accounting_account_id)){
            $catalogue = Catalogue::find($fa_type->accounting_account_id);

            $accounting_account = [ $catalogue->id => $catalogue->name ];
        }

        return view('fixed_asset_type.edit', compact('fa_type', 'accounting_account'));
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
        if(!auth()->user()->can('fixed_asset_type.edit')) {
			abort(403, "Unauthorized action.");
		}

        try {
            $fixed_asset_type = FixedAssetType::find($id);
            $input = $request->except('_token');

            $fixed_asset_type->update($input);

            $output = [ 'success' => true, 'msg' => __("fixed_asset.fixed_asset_type_updated_success") ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [ 'success' => false, 'msg' => __("messages.something_went_wrong") ];
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
        if(!auth()->user()->can('fixed_asset_type.delete')) {
			abort(403, "Unauthorized action.");
		}

        try {
            $fixed_assets = FixedAsset::withTrashed()
                ->where('fixed_asset_type_id', $id)->count();

            if(!$fixed_assets > 0){
                $fixed_asset_type = FixedAssetType::find($id);

                $fixed_asset_type->delete();
                $output = [ 'success' => true, 'msg' => __("fixed_asset.fixed_asset_type_deleted_success") ];

            } else {
                $output = [ 'success' => false, 'msg' => __("fixed_asset.fixed_asset_type_has_assoc_fixed_asset") ];
            }

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [ 'success' => false, 'msg' => __("messages.something_went_wrong") ];
        }

        return $output;
    }
}
