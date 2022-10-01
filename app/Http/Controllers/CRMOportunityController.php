<?php

namespace App\Http\Controllers;

use App\CRMOportunity;
use App\CRMContactReason;
use App\CRMContactMode;
use App\Category;
use App\System;
use App\Contact;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use DB;

class CRMOportunityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('crm-oportunities.view') && !auth()->user()->can('crm-oportunities.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        return view('crm_oportunity.index');
    }

    public function getOportunityData()
    {
        if (!auth()->user()->can('crm-oportunities.view') && !auth()->user()->can('crm-oportunities.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $oportunity = DB::table('crm_oportunities')
            ->select('crm_oportunities.contact_type', DB::raw("crm_contact_reasons.name as reason"), 'crm_oportunities.name', 'crm_oportunities.company', 'crm_oportunities.id')
            ->join('crm_contact_reasons', 'crm_oportunities.contact_reason_id', '=', 'crm_contact_reasons.id')
            ->where('crm_oportunities.business_id', $business_id)
            ->where('crm_oportunities.status', 'Oportunidad');
        return DataTables::of($oportunity)
            ->addColumn(
                'action',
                '@can("crm-oportunities.update")
            <button data-href="{{action(\'CRMOportunityController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_oportunity_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                &nbsp;
            <button data-href="{{action(\'FollowOportunitiesController@create\', [$id])}}" class="btn btn-xs btn-success follow_oportunity_button"><i class="glyphicon glyphicon-comment"></i> @lang("crm.tracing")</button>
                &nbsp;
            @endcan
            @can("crm-oportunities.delete")
                <button data-href="{{action(\'CRMOportunityController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_oportunity_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
            @endcan'
            )
            ->removeColumn('id')
            ->rawColumns([4])
            ->make(false);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('crm-oportunities.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Llenar select de Contact Reason
        $contactreason = CRMContactReason::forDropdown($business_id);

        //Llenar select de Contact Mode
        $contactmode = CRMContactMode::forDropdown($business_id);

        //Llenar select de Category
        $categories = Category::forDropdown($business_id);

        //Llenar select de Clientes
        $clients = Contact::where('business_id', $business_id)->whereIn('type', ['customer', 'both'])->select('id', 'name')->get();

        return view('crm_oportunity.create')
            ->with(compact('contactreason', 'contactmode', 'categories', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('crm-oportunities.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $oportunity = $request->only(['contact_type', 'contact_reason_id', 'name', 'company', 'charge', 'email', 'contacts', 'contact_mode_id', 'refered_id', 'product_cat_id']);
            $oportunity['business_id'] = $request->session()->get('user.business_id');
            $oportunity['employee_id'] = $request->session()->get('user.id');
            $oportunity['status'] = "Oportunidad";

            $oportunity = CRMOportunity::create($oportunity);
            $outpout = [
                'success' => true,
                'data' => $oportunity,
                'msg' => __("crm.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => $e->getMessage()];
        }

        return $outpout;
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
        if (!auth()->user()->can('crm-oportunities.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $oportunity = CRMOportunity::where('business_id', $business_id)->find($id);

            //Llenar select de Contact Reason
            $contactreason = CRMContactReason::forDropdown($business_id);

            //Llenar select de Contact Mode
            $contactmode = CRMContactMode::forDropdown($business_id);

            //Llenar select de Category
            $categories = Category::forDropdown($business_id);

            //Llenar select de Clientes
            $clients = Contact::where('business_id', $business_id)->whereIn('type', ['customer', 'both'])->pluck('name', 'id');

            return view('crm_oportunity.edit')
            ->with(compact('contactreason', 'contactmode', 'categories', 'clients', 'oportunity'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
