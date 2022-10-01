<?php

namespace App\Http\Controllers;

use App\CreditRequest;
use App\CreditHasReference;
use App\CreditHasFamilyMember;
use App\Business;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class CreditRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {



        $business = Business::where('id', 3)->first();
        $logo = $business->logo;

        return view('credit_request.index', compact('logo'));
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

        $type_person = $request->input('type_person');

        if($type_person == "legal") {
            $validateData = $request->validate(
                [
                    'business_name' => 'required',
                    'trade_name' => 'required',
                    'nrc' => 'required',
                    'nit_business' => 'required',
                    'business_type' => 'required',
                    'address' => 'required',
                    'category_business' => 'required',
                    'phone_business' => 'required',
                    'fax_business' => 'required',
                    'legal_representative' => 'required',
                    'dui_legal_representative' => 'required',
                    'purchasing_agent' => 'required',
                    'phone_purchasing_agent' => 'required',
                    'fax_purchasing_agent' => 'required',
                    'email_purchasing_agent' => 'required',
                    'payment_manager' => 'required',
                    'phone_payment_manager' => 'required',
                    'email_payment_manager' => 'required',
                    'term_business' => 'required',
                    'warranty_business' => 'required',


                    'amount_request_business' => 'required|numeric',
                    'name_reference.*' => ['required'],
                    'phone_reference.*' => ['required'],
                    'amount_reference.*' => ['required', 'numeric'],
                    'date_reference.*' => ['required', 'date'],

                    'name_relationship.*' => ['required'],
                    'relation_relationship.*' => ['required'],
                    'phone_relationship.*' => ['required'],
                    'address_relationship.*' => ['required'],

                ]
            );
        }
        else {
            $validateData = $request->validate(
                [
                    'amount_request_natural' => 'required|numeric',

                    'name_natural' => 'required',
                    'dui_natural' => 'required',
                    'age' => 'required',
                    'birthday' => 'required|date',
                    'phone_natural' => 'required',
                    'category_natural' => 'required',
                    'nit_natural' => 'required',
                    'address_natural' => 'required',
                    'amount_request_natural' => 'required',
                    'term_natural' => 'required',
                    'warranty_natural' => 'required',

                    'name_reference.*' => ['required'],
                    'phone_reference.*' => ['required'],
                    'amount_reference.*' => ['required', 'numeric'],
                    'date_reference.*' => ['required', 'date'],

                    'name_relationship.*' => ['required'],
                    'relation_relationship.*' => ['required'],
                    'phone_relationship.*' => ['required'],
                    'address_relationship.*' => ['required'],

                ]
            );

        }

        


        try {

            if($type_person == "legal") {
                $credit_details = $request->only([
                    'type_person',
                    'business_name',
                    'trade_name',
                    'nrc',
                    'nit_business',
                    'business_type',
                    'address',
                    'category_business',
                    'phone_business',
                    'fax_business',
                    'legal_representative',
                    'dui_legal_representative',
                    'purchasing_agent',
                    'phone_purchasing_agent',
                    'fax_purchasing_agent',
                    'email_purchasing_agent',
                    'payment_manager',
                    'phone_payment_manager',
                    'email_payment_manager',
                    'amount_request_business',
                    'term_business',
                    'warranty_business',
                ]);

                $credit_details['date_request'] = Carbon::now();

            } else {
                $credit_details = $request->only([
                    'type_person',
                    'name_natural',
                    'dui_natural',
                    'age',
                    'birthday',
                    'phone_natural',
                    'category_natural',
                    'nit_natural',
                    'address_natural',
                    'amount_request_natural',
                    'term_natural',
                    'warranty_natural',
                    'own_business_name',
                    'own_business_address',
                    'own_business_time',
                    'own_business_phone',
                    'own_business_fax',
                    'own_business_email',
                    'average_monthly_income',
                    'spouse_name',
                    'spouse_dui',
                    'spouse_work_address',
                    'spouse_phone',
                    'spouse_income_date',
                    'spouse_position',
                    'spouse_salary',
                ]);

                $credit_details['date_request'] = Carbon::now();
                $order_purchase = $request->input('order_purchase');
                $order_via_fax = $request->input('order_via_fax');
                if ($order_purchase) {
                    $credit_details['order_purchase'] = 1;
                }
                else {
                    $credit_details['order_purchase'] = 0;
                }
                if ($order_via_fax) {
                    $credit_details['order_via_fax'] = 1;
                }
                else {
                    $credit_details['order_via_fax'] = 0;
                }

            }

            //$business_id = request()->session()->get('user.business_id');

            $business = Business::where('id', 3)->first();

            $last_correlative = DB::table('credit_requests')
            ->select(DB::raw('MAX(id) as max'))
            ->first();

            if ($last_correlative->max != null) {
                $correlative = $last_correlative->max + 1;
            }
            else {
                $correlative = 1;
            }
            if ($correlative < 10) {
                $credit_details['correlative'] = "".$business->credit_prefix."0".$correlative."";
            }
            else {
                $credit_details['correlative'] = "".$business->credit_prefix."".$correlative."";
            }

            DB::beginTransaction();

            $credit = CreditRequest::create($credit_details);

            $name_reference = $request->input('name_reference');
            $phone_reference = $request->input('phone_reference');
            $amount_reference = $request->input('amount_reference');
            $date_reference = $request->input('date_reference');

            if (!empty($name_reference))
            {
                $cont = 0;                
                while($cont < count($name_reference))
                {
                    $detail = new CreditHasReference;
                    $detail->credit_id = $credit->id;
                    $detail->name = $name_reference[$cont];
                    $detail->phone = $phone_reference[$cont];
                    $detail->amount = $amount_reference[$cont];
                    $detail->date_cancelled = $date_reference[$cont];
                    $detail->save();
                    $cont = $cont + 1;
                } 
            }

            $name_relationship = $request->input('name_relationship');
            $relation_relationship = $request->input('relation_relationship');
            $phone_relationship = $request->input('phone_relationship');
            $address_relationship = $request->input('address_relationship');

            if (!empty($name_relationship))
            {
                $cont = 0;                
                while($cont < count($name_relationship))
                {
                    $detail = new CreditHasFamilyMember;
                    $detail->credit_id = $credit->id;
                    $detail->name = $name_relationship[$cont];
                    $detail->relationship = $relation_relationship[$cont];
                    $detail->phone = $phone_relationship[$cont];
                    $detail->address = $address_relationship[$cont];
                    $detail->save();
                    $cont = $cont + 1;
                } 
            }

            $output = [
                'success' => true,
                'id' => $credit->id,
                'msg' => __("crm.added_success")
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CreditRequest  $creditRequest
     * @return \Illuminate\Http\Response
     */
    public function show(CreditRequest $creditRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CreditRequest  $creditRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(CreditRequest $creditRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CreditRequest  $creditRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CreditRequest $creditRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CreditRequest  $creditRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(CreditRequest $creditRequest)
    {
        //
    }

    public function showReport(Request $request)
    {
        if($request->ajax()) {
            $business = Business::where('id', 3)->first();
            $logo = $business->logo;

            $id = $request->input('id');
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
    }
}
