<?php

namespace App\Http\Controllers;

use App\CRMContactReason;
use App\System;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;

use DB;

class CRMContactReasonController extends Controller
{
    public function __construct(ModuleUtil $moduleUtil){
        $this->moduleUtil = $moduleUtil;
    }

    public function index()
    {
        if(!auth()->user()->can('crm-contactreason.view') && !auth()->user()->can('crm-contactreason.create')){
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        return view('crm_contact_reason.index');
    }

    public function getContactReasonData(){
        if(!auth()->user()->can('crm-contactreason.view') && !auth()->user()->can('crm-contactreason.create')){
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $contact_reason = DB::table('crm_contact_reasons')
        ->select('name', 'description', 'id')
        ->where('business_id', $business_id);
        return DataTables::of($contact_reason)
        ->addColumn(
            'action',
            '@can("crm-contactreason.update")
            <button data-href="{{action(\'CRMContactReasonController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_contactreason_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                &nbsp;
            @endcan
            @can("crm-contactreason.delete")
                <button data-href="{{action(\'CRMContactReasonController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_contactreason_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
            @endcan'
        )
        ->removeColumn('id')
        ->rawColumns([2])
        ->make(false);
    }

    public function create()
    {
        if(!auth()->user()->can('crm-contactreason.create')){
            abort(403, 'Unauthorized action.');
        }
        return view('crm_contact_reason.create');
    }

    public function store(Request $request)
    {
        if(!auth()->user()->can('crm-contactreason.create')){
            abort(403, 'Unauthorized action.');
        }
        try{
            $contact_reason = $request->only(['name', 'description']);
            $contact_reason['business_id'] = $request->session()->get('user.business_id');
            
            $contact_reason = CRMContactReason::create($contact_reason);
            $outpout = ['success' => true,
            'data' => $contact_reason,
            'msg' => __("crm.added_success")];
        }catch(\Exception $e){
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => $e->getMessage()];
        }
        return $outpout;
    }

    public function edit($id)
    {
        if(!auth()->user()->can('crm-contactreason.update')){
            abort(403, 'Unauthorized action.');
        }
        if(request()->ajax()){
            $business_id = request()->session()->get('user.business_id');
            $contact_reason = CRMContactReason::where('business_id', $business_id)->find($id);

            return view('crm_contact_reason.edit')
            ->with(compact('contact_reason'));
        }
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('crm-contactreason.update')) {
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            try{
                $input = $request->only(['name', 'description']);
                $business_id = $request->session()->get('user.business_id');

                $contact_reason = CRMContactReason::where('business_id', $business_id)->findOrFail($id);
                $contact_reason->name = $input['name'];
                $contact_reason->description = $input['description'];
                $contact_reason->save();

                $outpout = ['success' => true, 'data' => $contact_reason, 'msg' => __('crm.updated_success')];
            }catch(\Exception $e){
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $outpout = ['success' => false, 'msg' => $e->getMessage()];
            }

            return $outpout;

        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('crm-contactreason.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            try{
                $business_id = request()->session()->get('user.business_id');
                $contact_reason = CRMContactReason::where('business_id', $business_id)->find($id);

                $contact_reason->delete();
                $outpout = ['success' => true, 'data' => $contact_reason, 'msg' => __('crm.deleted_success')];
            }catch(\Exception $e){
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $outpout = ['success' => false, 'msg' => $e->getMessage()];
            }
            return $outpout;
        }
    }

}
