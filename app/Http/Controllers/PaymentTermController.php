<?php

namespace App\Http\Controllers;

use App\PaymentTerm;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentTermController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('payment_term.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('payment_terms.index');
    }

    public function getPaymentTermData()
    {
        $business_id = request()->user()->business_id;

        $payment_terms = PaymentTerm::where('business_id', $business_id);

        return DataTables::of($payment_terms)
            ->addColumn(
                'actions',
                function ($row) {
                    $html = '<div class="btn-group">
                <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (auth()->user()->can('customer.update')) {
                        $html .= '<li><a href="#" data-href="' . action('PaymentTermController@edit', [$row->id]) . '" class="edit_payment_terms_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }


                    if (auth()->user()->can('customer.delete')) {
                        $html .= '<li><a href="#" onClick="deletePaymentTerm(' . $row->id . ')"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
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
        return view('payment_terms.create');
    }


    public function store(Request $request)
    {
        if (!auth()->user()->can('payment_term.create')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate(
            [
                'name' => 'required',
                'days' => 'required',
            ],
            [
                'name.required' => 'El nombre es requerido',
                'days.required' => 'El campo dia es requerido'
            ]
        );

        try {

            $payment_terms = new PaymentTerm();
            $payment_terms->name = trim($request->name);
            $payment_terms->description = trim($request->description);
            $payment_terms->days = trim($request->days);
            $payment_terms->business_id = Auth::user()->business_id;
            $payment_terms->save();

            $output = [
                'success' => true,
                'msg' => __("payment.payment_terms_create"),
                'payment_term_id' => $payment_terms->id,
                'payment_term_name' => $payment_terms->name,
                'payment_term_description' => $payment_terms->description,
                'payment_term_days' => $payment_terms->days,
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
        $payment_term = PaymentTerm::findOrFail($id);
        return view('payment_terms.edit', compact('payment_term'));
    }


    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('payment_term.update')) {
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

            $payment_terms = PaymentTerm::findOrFail($id);
            $payment_terms->name = trim($request->name);
            $payment_terms->description = trim($request->description);
            $payment_terms->days = trim($request->days);
            $payment_terms->business_id = Auth::user()->business_id;
            $payment_terms->update();

            $output = [
                'success' => true,
                'msg' => __("payment.payment_terms_update"),
                'payment_term_id' => $payment_terms->id,
                'payment_term_name' => $payment_terms->name,
                'payment_term_description' => $payment_terms->description,
                'payment_term_days' => $payment_terms->days,
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('payment_term.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {

            PaymentTerm::find($id)->delete();
            $output = [
                'success' => true,
                'msg' => __("payment.payment_terms_delete"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    /** 
     * Get payment terms
     */
    public function getPaymentTerms(){
        $business_id = request()->session()->get('user.business_id');

        $payment_terms = PaymentTerm::where('business_id', $business_id)
            ->pluck('name', 'days');

        return json_encode($payment_terms);
    }
}
