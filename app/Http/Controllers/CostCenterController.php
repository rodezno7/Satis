<?php

namespace App\Http\Controllers;

use App\Catalogue;
use App\CostCenter;
use App\CostCenterMainAccount;
use App\CostCenterOperationAccount;
use App\BusinessLocation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CostCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->can("cost_center.view")){
            abort(403, "Unauthorized action");
        }

        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");
            
            $cost_centers = CostCenter::join("business_locations as bl", "cost_centers.location_id", "bl.id")
                ->where("cost_centers.business_id", $business_id)
                ->select(
                    "cost_centers.id",
                    "cost_centers.name as cost_center_name",
                    "cost_centers.description",
                    "bl.name as location_name"
                );

            return Datatables::of($cost_centers)
                ->addColumn("action", function($row){
                    $html = "<div class='btn-group'>
                        <button type='button' class='btn btn-info dropdown-toggle btn-xs' 
                            data-toggle='dropdown' aria-expanded='false'>" . __("messages.actions") .
                            "<span class='caret'></span><span class='sr-only'>Toggle Dropdown</span>
                        </button>
                        <ul class='dropdown-menu dropdown-menu-right' role='menu'>";
                    if(auth()->user()->can("cost_center.accounts")){
                        $html .= "<li><a href='". action("CostCenterController@getMainAccounts", [$row->id]) ."' class='add_main_accounts'><i class='fa fa-book'></i>". __("cost_center.main_accounts") ."</li>";
                        $html .= "<li><a href='". action("CostCenterController@getOperationAccounts", [$row->id]) ."' class='add_operation_accounts'><i class='fa fa-book'></i>". __("cost_center.operation_accounts") ."</li>";
                        $html .= "<li class='divider'></li>";
                    }
                    if(auth()->user()->can("cost_center.edit")){
                        $html .= "<li><a href='". action("CostCenterController@edit", [$row->id]) ."' class='btn_edit_cost_center'><i class='glyphicon glyphicon-edit'></i>". __("messages.edit") ."</a></li>";
                    }
                    if(auth()->user()->can("cost_center.delete")){
                        $html .= "<li><a href='". action("CostCenterController@destroy", [$row->id]) ."' class='btn_delete_cost_center'><i class='fa fa-trash'></i>". __("messages.delete") ."</a></li>";
                    }
                    $html .= '</ul></div>';
                    return $html;
                })
                ->removeColumn("id")
                ->rawColumns([3])
                ->make(false);
        }

        return view('cost_center.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can("cost_center.create")){
            abort(403, "Unauthorized action");
        }
        $business_id = request()->session()->get("user.business_id");

        $locations = BusinessLocation::forDropdown($business_id, false, true);
        $locations = $locations['locations'];

        return view('cost_center.create')
            ->with(compact("locations"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if(!auth()->user()->can("cost_center.create")){
            abort(403, "Unauthorized action");
        }

        if($request->ajax()) {
            try {
                $cost_center = $request->only("name", "description", "location_id");
                $cost_center["business_id"] = $request->session()->get("user.business_id");
                $cost_center["created_by"] = $request->session()->get("user.id");
                CostCenter::create($cost_center);

                $output = [
                    'success' => true,
                    'msg' => __('accounting.added_successfully')
                ];

            } catch(\Exception $e){
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CostCenter  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $costCenter = CostCenter::findOrFail($id);
        return response()->json($costCenter);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CostCenter  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can("cost_center.edit")){
            abort(403, "Unauthorized action");
        }

        $business_id = request()->session()->get("user.business_id");
        $cost_center = CostCenter::findOrFail($id);

        $locations = BusinessLocation::forDropdown($business_id, false, true);
        $locations = $locations['locations'];

        return view('cost_center.edit')
            ->with(compact("cost_center", "locations"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CostCenter  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!auth()->user()->can("cost_center.edit")){
            abort(403, "Unauthorized action");
        }

        if($request->ajax()){
            try {
                $cost_center = $request->only("name", "description", "location_id");
                $cost_center['updated_by'] = $request->session()->get("user.id");
                CostCenter::findOrFail($id)
                    ->update($cost_center);

                $output = [
                    'success' => true,
                    'msg' => __('accounting.updated_successfully')
                ];

            } catch(\Exception $e){
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
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
     * @param  \App\CostCenter  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $costCenter = CostCenter::findOrFail($id);
        if (request()->ajax()) {
            try{
                $costCenter->forceDelete();
                $output = [
                    'success' => true,
                    'msg' => __('accounting.deleted_successfully')
                ];
                
            }
            catch (\Exception $e){
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    /**
     * GET for Main Accounts
     * @param int $cost_center_id
     * @return \Response
     */
    public function getMainAccounts($cost_center_id){
        if(!auth()->user()->can("cost_center.accounts")){
            abort(403, "Unauthorized action");
        }

        if(request()->ajax()){
            $cost_center =
                CostCenter::where("id", $cost_center_id)
                    ->with("cost_center_main_account")
                    ->first();

            $expense_account = null;
            if($cost_center->cost_center_main_account){
                $expense_account =
                    Catalogue::where("status", 1)
                        ->where("id", $cost_center->cost_center_main_account->expense_account_id)
                        ->select("id", "code", "name")
                        ->first();;
            }

            return view("cost_center.partials.main_accounts")
                ->with(compact("cost_center", "expense_account"));
        }
    }

    /**
     * POST for Main Accounts
     * @param int $cost_center_id
     * @return JSON
     */
    public function postMainAccounts($cost_center_id){
        if(!auth()->user()->can("cost_center.accounts")){
            abort(403, "Unauthorized action");
        }

        try{
            /** Cost main account */
            CostCenterMainAccount::updateOrCreate(
                ["cost_center_id" => $cost_center_id],
                [
                    "expense_account_id" => request()->input("expense_account"),
                    "updated_by" => request()->session()->get("user.id")
                ]
            );

            $output = [
                'success' => true,
                'msg' => __('accounting.added_successfully')
            ];

        } catch(\Exception $e){
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
        return $output;
    }

    /**
     * Get for Operation Accounts
     * @param int $cost_center_id
     * @return \Response
     */
    public function getOperationAccounts($cost_center_id){
        if(!auth()->user()->can("cost_center.accounts")){
            abort(403, "Unauthorized action");
        }

        if(request()->ajax()){
            $cost_center = CostCenter::find($cost_center_id);
            $cost_center_main_account =
                $cost_center ? CostCenterMainAccount::find($cost_center->id) : null;
            $cost_center_operation_account =
                $cost_center ? CostCenterOperationAccount::find($cost_center->id) : null;
            
            $account_names = [
                "sell_expense_account_name" => "",
                "admin_expense_account_name" => "",
                "finantial_expense_account_name" => "",
                "non-dedu_expense_account_name" => "",
            ];
            
            $expense_account_code = "";
            if(!empty($cost_center_main_account)){
                // Expense main account code
                $catalogue = Catalogue::find($cost_center_main_account->expense_account_id);
                $expense_account_code = !empty($catalogue) ? $catalogue->code : "";  
            }

            if(!empty($cost_center_operation_account)) {
                // Sale expenses account name
                $catalogue = Catalogue::find($cost_center_operation_account->sell_expense_account);
                $account_names['sell_expense_account_name'] = !empty($catalogue) ? $catalogue->code . " " . $catalogue->name : "";
                // Administration expenses account
                $catalogue = Catalogue::find($cost_center_operation_account->admin_expense_account);
                $account_names['admin_expense_account_name'] = !empty($catalogue) ? $catalogue->code . " " . $catalogue->name : "";
                // Finantial expenses account
                $catalogue = Catalogue::find($cost_center_operation_account->finantial_expense_account);
                $account_names['finantial_expense_account_name'] = !empty($catalogue) ? $catalogue->code . " " . $catalogue->name : "";
                // Non-deductible expenses account
                $catalogue = Catalogue::find($cost_center_operation_account->non_dedu_expense_account);
                $account_names['non_dedu_expense_account_name'] = !empty($catalogue) ? $catalogue->code . " " . $catalogue->name : "";
            }

            return view("cost_center.partials.operation_accounts")
                ->with(compact("cost_center", "expense_account_code", "cost_center_operation_account", "account_names"));
        }
    }

    /**
     * POST for Operation Accounts
     * @param int $cost_center_id
     * @return JSON
     */
    public function postOperationAccounts($cost_center_id){
        if(!auth()->user()->can("cost_center.accounts")){
            abort(403, "Unauthorized action");
        }

        try{
            /** Update an existing cost operation accounts o create it if not found */
            CostCenterOperationAccount::updateOrCreate(
                ["cost_center_id" => $cost_center_id],
                [
                    "sell_expense_account" => request()->get("sell_expense_account", null),
                    "admin_expense_account" => request()->get("admin_expense_account", null),
                    "finantial_expense_account" => request()->get("finantial_expense_account", null),
                    "non_dedu_expense_account" => request()->get("non_dedu_expense_account", null),
                    "updated_by" => request()->session()->get("user.id")
                ]
            );
            
            $output = [
                'success' => true,
                'msg' => __('accounting.updated_successfully')
            ];

        } catch(\Exception $e){
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
        return $output;
    }
}
