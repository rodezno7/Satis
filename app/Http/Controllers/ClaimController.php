<?php

namespace App\Http\Controllers;

use App\Claim;
use App\ClaimType;
use App\Business;
use App\User;
use App\StatusClaim;
use App\Customer;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;

class ClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('claim.view')) {
            abort(403, 'Unauthorized action.');
        }
        $types = ClaimType::select('id', 'name')->get();
        $status = StatusClaim::select('id', 'name')
        ->where('status', 1)
        ->get();

        $business_id = request()->session()->get('user.business_id');

        $users = DB::table('users as user')
        ->join('model_has_roles as rol', 'rol.model_id', '=', 'user.id')
        ->select('user.id', DB::raw('CONCAT(user.first_name, " ", user.last_name) as full_name'))
        ->whereIn('rol.role_id', [DB::raw("select role_id from role_has_permissions where permission_id = 114")])
        ->where('business_id', $business_id)
        ->get();

        $status_claims = StatusClaim::select('id', 'name')
        ->where('status', 1)
        ->where('predecessor', null)
        ->get();

        $status_claims_follow = StatusClaim::select('id', 'name')
        ->where('status', 1)
        ->get();

        $customers = Customer::select('id', 'name')
        ->where('business_id', $business_id)
        ->get();

        $products = DB::table('variations')
        ->join('products', 'products.id', '=', 'variations.product_id')
        ->select('products.name as name_product', 'variations.name as name_variation', 'variations.id', 'variations.sub_sku', 'products.sku')
        ->where('business_id', $business_id)
        ->where('products.clasification', '<>', 'kits')
        ->where('products.clasification', '<>', 'service')
        ->where('products.status', 'active')
        ->get();

        $user_id = request()->session()->get('user.id');

        $role_id_q = DB::table('model_has_roles')
        ->select('role_id')
        ->where('model_id', $user_id)
        ->first();

        $is_default_q = DB::table('roles')
        ->select('is_default')
        ->where('id', $role_id_q->role_id)
        ->first();

        $is_default = $is_default_q->is_default;
        
        return view('claims.index', compact('types', 'status', 'users', 'status_claims', 'status_claims_follow', 'customers', 'products', 'user_id', 'is_default'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('claim.create')) {
            abort(403, 'Unauthorized action.');
        }
        return view('claims.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('claim.create')) {
            abort(403, 'Unauthorized action.');
        }
        $chk_resolution = $request->input('proceed');
        
        $validateData = $request->validate(
            [
                'correlative' => 'required|unique:claims',
                'claim_type' => 'required',
                //'status_claim_id' => 'required',
                'description' => 'required',
                //'claim_date' => 'required|date',
                //'suggested_closing_date' => 'required|date',
            ]
        );

        try {

            $claim_details = $request->only([
                'correlative',
                'claim_type',
                //'status_claim_id',
                'description',
                //'claim_date',
                //'suggested_closing_date',
                //'review_description',
                //'proceed',
                //'resolution',
                //'close_date',
                'customer_id',
                'variation_id',
                'invoice',
                'equipment_reception',
                'equipment_reception_desc'
            ]);

            $claim_details['status_claim_id'] = 1;

            $claim_date = Carbon::now();
            $claim_date = $claim_date->format('Y-m-d');

            $claim_details['claim_date'] = $claim_date;

            $claim_type = ClaimType::findOrFail($request->input('claim_type'));
            $days = $claim_type->resolution_time;

            $suggested_closing_date = Carbon::now();
            $suggested_closing_date->addDays($days);
            $suggested_closing_date = $suggested_closing_date->format('Y-m-d');

            $claim_details['suggested_closing_date'] = $suggested_closing_date;

            $claim_details['register_by'] = $request->session()->get('user.id');
            

            $claim = Claim::create($claim_details);
            $output = [
                'success' => true,
                'msg' => __("crm.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Claim  $claim
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('claim.view')) {
            abort(403, 'Unauthorized action.');
        }
        $claim = DB::table('claims as claim')
        ->leftJoin('claim_types as type', 'type.id', '=', 'claim.claim_type')
        ->leftJoin('status_claims as status', 'status.id', '=', 'claim.status_claim_id')
        ->leftJoin('users as authorized', 'authorized.id', '=', 'claim.authorized_by')
        ->leftJoin('users as register', 'register.id', '=', 'claim.register_by')
        ->leftJoin('customers as customer', 'customer.id', '=', 'claim.customer_id')
        ->leftJoin('variations as variation', 'variation.id', '=', 'claim.variation_id')
        ->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
        ->select('claim.*', 'type.name as type', 'status.name as status', DB::raw('CONCAT(authorized.first_name, " ", authorized.last_name) as authorized, CONCAT(register.first_name, " ", register.last_name) as register'), 'customer.name as customer', 'variation.name as name_variation', 'product.name as name_product', 'variation.sub_sku', 'product.sku')
        ->where('claim.id', $id)
        ->first();
        return view('claims.show', compact('claim'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Claim  $claim
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('claim.update')) {
            abort(403, 'Unauthorized action.');
        }
        $claim = Claim::findOrFail($id);
        return response()->json($claim);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Claim  $claim
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('claim.update')) {
            abort(403, 'Unauthorized action.');
        }
        $chk_resolution = $request->input('proceed');

        $review = $request->input('review');
        $proceed = $request->input('proceed');
        $not_proceed = $request->input('not_proceed');
        $close = $request->input('close');

        $claim = Claim::findOrFail($id);
        
        $validateData = $request->validate(
            [
                //'claim_type' => 'required',
                'description' => 'required',
            ]
        );

        try {

            $claim_details = $request->only([
                'correlative',
                //'claim_type',
                'description',
                'review_description',
                'justification',
                'resolution',
                'customer_id',
                'variation_id',
                'invoice',
                'equipment_reception',
                'equipment_reception_desc'
            ]);

            if($review == 1) {
                $claim_details['status_claim_id'] = 2;
                if (($proceed == 1) || ($not_proceed == 1)) {
                    $claim_details['status_claim_id'] = 3;
                    if($proceed == 1) {
                        $claim_details['proceed'] = 1;
                        $claim_details['not_proceed'] = 0;
                        $claim_details['authorized_by'] = $request->session()->get('user.id');
                    } else {
                        $claim_details['proceed'] = 0;
                        $claim_details['not_proceed'] = 1;
                    }

                    if ($close == 1) {
                        $claim_details['status_claim_id'] = 4;
                        $claim_details['closed'] = 1;

                        $close_date = Carbon::now();
                        

                        $claim_details['close_date'] = $close_date;

                        if ($close_date > $claim->suggested_closing_date) {
                            $close_date = $close_date->format('Y-m-d');
                            $claim_details['status_claim_id'] = 5;
                        } else {
                            $close_date = $close_date->format('Y-m-d');
                            $claim_details['status_claim_id'] = 4;
                        }


                    } else {
                        $claim_details['status_claim_id'] = 3;
                        $claim_details['closed'] = 0;
                    }

                } else {
                    $claim_details['status_claim_id'] = 2;
                    $claim_details['proceed'] = 0;
                    $claim_details['not_proceed'] = 0;
                    $claim_details['closed'] = 0;
                }

            } else {
                $claim_details['status_claim_id'] = 1;
                $claim_details['proceed'] = 0;
                $claim_details['not_proceed'] = 0;
                $claim_details['closed'] = 0;

            }

            $claim = $claim->update($claim_details);
            $output = [
                'success' => true,
                'msg' => __("crm.updated_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Claim  $claim
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('claim.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try{

                $claim = Claim::findOrFail($id);

                $claim->delete();
                $output = [
                    'success' => true,
                    'msg' => __('crm.deleted_success')
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

    public function getClaimsData()
    {
        if (!auth()->user()->can('claim.view')) {
            abort(403, 'Unauthorized action.');
        }
        $claims = DB::table('claims as claim')
        ->leftJoin('claim_types as type', 'type.id', '=', 'claim.claim_type')
        ->leftJoin('users as user_authorize', 'user_authorize.id', '=', 'claim.authorized_by')
        ->leftJoin('users as user_register', 'user_register.id', '=', 'claim.register_by')
        ->leftJoin('status_claims as status', 'status.id', '=', 'claim.Status_claim_id')
        ->select('claim.*', 'type.name as name_type', 'status.color as status_color', 'status.name as status_name', DB::raw('CONCAT(user_authorize.first_name, " ", user_authorize.last_name) as name_authorize, CONCAT(user_register.first_name, " ", user_register.last_name) as name_register'))
        ->get();

        return DataTables::of($claims)->addColumn(
            'actions', function($row){
                $html = '';

                if (auth()->user()->can('claim.view')) {
                    $html .= '<a class="btn btn-xs btn-info" onClick="viewClaim('.$row->id.')"><i class="glyphicon glyphicon-eye-open edit-glyphicon"></i></a> ';
                }

                if (auth()->user()->can('claim.update')) {
                    $html .= '<a class="btn btn-xs btn-primary" onClick="editClaim('.$row->id.')"><i class="glyphicon glyphicon-edit"></i></a> ';
                }

                if (auth()->user()->can('claim.delete')) {
                    $html .= '<a class="btn btn-xs btn-danger" onClick="deleteClaim('.$row->id.')"><i class="glyphicon glyphicon-trash"></i></a>';
                }

                $html .= '';
                return $html;
            })
        ->addColumn(
            'color_label', function($row){
                $html = "<span class='dot' style='background-color:".$row->status_color.";''></span> ".$row->status_name."";
                return $html;
            })
        ->rawColumns(['actions', 'color_label'])
        ->toJson();
    }

    public function getClaimCorrelative()
    {
        if (!auth()->user()->can('claim.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $last_correlative = DB::table('claims')
        ->select(DB::raw('MAX(id) as max'))
        ->first();

        if ($last_correlative->max != null) {
            $correlative = $last_correlative->max + 1;
        }
        else {
            $correlative = 1;
        }
        if ($correlative < 10) {
            $correlative = "".$business->claim_prefix."0".$correlative."";
        }
        else {
            $correlative = "".$business->claim_prefix."".$correlative."";
        }
        $output = [
            'correlative' => $correlative
        ];
        return $output;
    }

    public function getNexState($state_id, $claim_id)
    {
        if (!auth()->user()->can('claim.update')) {
            abort(403, 'Unauthorized action.');
        }
        $claim = Claim::findOrFail($claim_id);
        $actual_state_q = StatusClaim::where('id', $claim->status_claim_id)->first();
        $actual_state = $actual_state_q->id;

        $new_state_q = StatusClaim::where('id', $state_id)->first();
        $new_state_predecessor = $new_state_q->predecessor;

        if ($actual_state == $new_state_predecessor) {
            $output = [
                'success' => true,
                'msg' => 'OK'
            ];
        } else {
            $output = [
                'success' => false,
                'msg' => __('crm.status_invalid')
            ];
        }
        return $output;
    }

    public function getUsersByClaimType($id)
    {
        if (!auth()->user()->can('claim.update')) {
            abort(403, 'Unauthorized action.');
        }

        $users = DB::table('claim_type_has_users as users')
        ->select('user_id')
        ->where('claim_type_id', $id)
        ->get();
        return response()->json($users);

    }
    
}