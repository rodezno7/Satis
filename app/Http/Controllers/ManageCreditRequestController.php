<?php

namespace App\Http\Controllers;

use DataTables;
use DB;
use App\CreditRequest;
use App\CreditHasReference;
use App\CreditHasFamilyMember;
use App\Business;
use Carbon\Carbon;
use Storage;

use Illuminate\Http\Request;

class ManageCreditRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('credit.view')) {
            abort(403, 'Unauthorized action.');
        }
        return view('manage_credits.index');
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
        //
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
        if (!auth()->user()->can('credit.update')) {
            abort(403, 'Unauthorized action.');
        }


        $credit = CreditRequest::findOrFail($id);
        return response()->json($credit);
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
        if (!auth()->user()->can('credit.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try{

                $credit = CreditRequest::findOrFail($id);
                $credit->delete();
                
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

    public function getCreditsData()
    {
        if (!auth()->user()->can('credit.view')) {
            abort(403, 'Unauthorized action.');
        }
        $credit_requests = CreditRequest::select('id', 'correlative', 'type_person', 'date_request', 'observations', 'file', 'status')->get();
        return DataTables::of($credit_requests)
        ->addColumn('type_person_label', function($row){
            if($row->type_person == 'legal') {
                return __('credit.legal_person');
            }
            else
            {
                return __('credit.natural');
            }
        })
        ->addColumn('status_label', function($row){
            if($row->status == 'pending') {
                return __('credit.pending');
            }

            if($row->status == 'approved') {
                return __('credit.approved');
            }

            if($row->status == 'denied') {
                return __('credit.denied');
            }
        })
        ->addColumn('file', function ($row) {
            if ($row->file != null) {
                return '<a href="'.asset("credit_files/".$row->file).'" target="_blank"><i class="glyphicon glyphicon-download-alt"></i></a>';
            }
            else
            {
                return "N/A";
            }
        })
        ->rawColumns(['file'])
        ->toJson();
    }

    function viewCredit($id)
    {
        if (!auth()->user()->can('credit.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $logo = $business->logo;

        $credit = CreditRequest::where('id', $id)->first();
        $references = CreditHasReference::where('credit_id', $id)->get();
        $relationships = CreditHasFamilyMember::where('credit_id', $id)->get();

        $date = Carbon::parse($credit->request_date);

        $months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));          

        $month = $months[($date->format('n')) - 1];


        $footer = "En la ciudad de ______________________________________  a los ______ días del mes de ___________________ del año _______________ ";



        $pdf = \PDF::loadView('reports.credit_request', compact('credit', 'references', 'relationships', 'footer', 'logo'));
        $pdf->setPaper('letter', 'portrait');
        return $pdf->stream();
    }

    function editCredit(Request $request)
    {
        if (!auth()->user()->can('credit.update')) {
            abort(403, 'Unauthorized action.');
        }

        if($request->ajax())
        {
            try {

                $id = $request->input('credit_id');

                $credit = CreditRequest::findOrFail($id);
                $credit->status = $request->input('status');
                $credit->observations = $request->input('observations');

                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $name = time().$file->getClientOriginalName();
                    Storage::disk('credits')->put($name,  \File::get($file));
                    $credit->file = $name;
                }

                $credit->save();

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
}
