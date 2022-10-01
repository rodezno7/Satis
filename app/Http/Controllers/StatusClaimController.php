<?php

namespace App\Http\Controllers;

use App\StatusClaim;
use App\Business;
use Illuminate\Http\Request;
use DB;
use DataTables;

class StatusClaimController extends Controller
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
        if (!auth()->user()->can('claim_status.create')) {
            abort(403, 'Unauthorized action.');
        }
        $validateData = $request->validate(
            [
                'correlative' => 'required|unique:status_claims',
                'name' => 'required|unique:status_claims',
                'color' => 'required',
            ]
        );
        if($request->ajax())
        {
            try {
                $status = StatusClaim::create($request->all());
                $output = [
                    'success' => true,
                    'msg' => __('crm.added_success')
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
     * @param  \App\StatusClaim  $statusClaim
     * @return \Illuminate\Http\Response
     */
    public function show(StatusClaim $statusClaim)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StatusClaim  $statusClaim
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('claim_status.update')) {
            abort(403, 'Unauthorized action.');
        }
        $status = StatusClaim::findOrFail($id);
        return response()->json($status);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StatusClaim  $statusClaim
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('claim_status.update')) {
            abort(403, 'Unauthorized action.');
        }

        $status = StatusClaim::findOrFail($id);
        

        $validateData = $request->validate(
            [
                'name' => 'required|unique:status_claims,name,'.$status->id,
            ]
        );
        if($request->ajax())
        {
            try {

                $status->update($request->all());
                $output = [
                    'success' => true,
                    'msg' => __("crm.updated_success")
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
     * @param  \App\StatusClaim  $statusClaim
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('claim_status.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try{

                $status = StatusClaim::findOrFail($id);
                
                $status->delete();
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

    public function getStatusClaimsData()
    {
        if (!auth()->user()->can('claim_status.view')) {
            abort(403, 'Unauthorized action.');
        }
        $status_claims = DB::table('status_claims as status')
        ->leftJoin('status_claims as predecessor', 'predecessor.id', '=', 'status.predecessor')
        ->select('status.*', 'predecessor.name as predecessor')
        ->get();
        return DataTables::of($status_claims)->addColumn(
            'actions', function($row){
                $html = '';

                if (auth()->user()->can('claim.update')) {
                    $html .= '<a class="btn btn-xs btn-primary" onClick="editStatus('.$row->id.')"><i class="glyphicon glyphicon-edit"></i></a> ';
                }

                /*

                if (auth()->user()->can('claim.delete')) {
                    $html .= '<a class="btn btn-xs btn-danger" onClick="deleteStatus('.$row->id.')"><i class="glyphicon glyphicon-trash"></i></a>';
                }
                */

                $html .= '';
                return $html;
            })
        ->addColumn(
            'status_label', function($row){
                if ($row->status == 1) {
                    $html = __('crm.active');
                } else {
                    $html = __('crm.inactive');
                }

                return $html;
            })
        ->addColumn(
            'color_label', function($row){
                $html = "<span class='dot' style='background-color:".$row->color.";''></span>";
                return $html;
            })
        ->rawColumns(['actions', 'color_label'])
        ->toJson();
    }

    public function getStatusClaims()
    {
        if (!auth()->user()->can('claim_status.view')) {
            abort(403, 'Unauthorized action.');
        }
        $status = StatusClaim::select('id', 'name')
        ->where('status', 1)
        ->get();
        return response()->json($status);
    }

    public function getStatusClaimCorrelative()
    {
        if (!auth()->user()->can('claim_status.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $last_correlative = DB::table('status_claims')
        ->select(DB::raw('MAX(id) as max'))
        ->first();

        if ($last_correlative->max != null) {
            $correlative = $last_correlative->max + 1;
        }
        else {
            $correlative = 1;
        }
        if ($correlative < 10) {
            $correlative = "".$business->status_claim_prefix."0".$correlative."";
        }
        else {
            $correlative = "".$business->status_claim_prefix."".$correlative."";
        }
        $output = [
            'correlative' => $correlative
        ];
        return $output;
    }
}
