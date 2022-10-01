<?php

namespace App\Http\Controllers;

use App\User;
use DataTables;
use App\Customer;
use App\Employees;
use Carbon\Carbon;
use App\Transaction;
use App\DocumentType;
use App\CreditDocuments;
use App\SupportDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreditDocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('cdocs.view') && !auth()->user()->can('cdocs.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = Auth::user()->business_id;
        return view('credit_documents.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('cdocs.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = Auth::user()->business_id;
        $document_types = DocumentType::forDropDown($business_id);
        $supprt_docs = SupportDocuments::forDropDown($business_id);
        $employees = Employees::forDropdown($business_id);
        return view('credit_documents.create')
            ->with(compact('document_types', 'supprt_docs', 'employees'));
    }

    public function getTransactionByInvoice($invoice, $doctype)
    {

        $business_id = Auth::user()->business_id;

        if (request()->ajax()) {
            try {

                $invoice_data = Transaction::where('document_types_id', $doctype)
                    ->where('correlative', $invoice)
                    ->where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->first();

                if (!empty($invoice_data)) {

                    $customer = Customer::where('id', $invoice_data->customer_id)
                        ->where('business_id', $business_id)
                        ->first();

                    $outpout = [
                        'success' => true,
                        'found' => 1,
                        'amount' => $invoice_data->final_total,
                        'date' => $invoice_data->transaction_date,
                        'customer' => $customer->name,
                        'id' => $invoice_data->id,
                        'inv' => true
                    ];
                } else {
                    $outpout = [
                        'success' => true,
                        'found' => 0,
                        'msg' => __('cxc.invoice_does_not_exist'),
                        'inv' => true
                    ];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $outpout = [
                    'success' => false,
                    'inv' => false,
                    'msg' => $e->getMessage(),
                ];
            }
            return $outpout;
        }
    }

    public function getCDocsData()
    {
        if (!auth()->user()->can('cdocs.view') && !auth()->user()->can('cdocs.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = Auth::user()->business_id;

        $creditDocuments = DB::table('credit_documents')
            ->join('transactions', 'transactions.id', '=', 'credit_documents.transaction_id')
            ->join('customers', 'customers.id', '=', 'transactions.customer_id')
            ->join('document_types', 'document_types.id', '=', 'transactions.document_types_id')
            ->where('credit_documents.business_id', $business_id)
            ->select(
                'credit_documents.id',
                DB::raw('DATE_FORMAT(credit_documents.register_date,"%d/%m/%Y") as date'),
                'customers.name',
                'document_types.document_name',
                'transactions.correlative'
            )
            ->get();


        return DataTables::of($creditDocuments)
            ->addColumn(
                'action',
                function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (auth()->user()->can('cdocs.reception')) {
                        $html .= '<li><a href="#" data-href="' . action('CreditDocumentsController@reception', [$row->id]) . '" class="reception_cdocs_button"><i class="glyphicon glyphicon-copy"></i> ' . __("cxc.add_reception") . '</a></li>';
                    }
                    if (auth()->user()->can('cdocs.custodian')) {
                        $html .= '<li><a href="#" data-href="' . action('CreditDocumentsController@custodian', [$row->id]) . '" class="custodian_cdocs_button"><i class="glyphicon glyphicon-lock"></i> ' . __("cxc.add_custodian") . '</a></li>';
                    }
                    if (auth()->user()->can('cdocs.view')) {
                        $html .= '<li><a href="#" data-href="' . action('CreditDocumentsController@show', [$row->id]) . '" class="show_cdocs_button"><i class="glyphicon glyphicon-search"></i> ' . __("messages.view") . '</a></li>';
                    }
                    if (auth()->user()->can('cdocs.update')) {
                        $html .= '<li><a href="#" data-href="' . action('CreditDocumentsController@edit', [$row->id]) . '" class="edit_cdocs_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }
                    if (auth()->user()->can('cdocs.delete')) {
                        $html .= '<li><a href="#" onClick="deleteCreditDocuments(' . $row->id . ')" class="delete_cdocs_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '</ul></div>';
                    return $html;
                }
            )
            ->removeColumn('id')
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('cdocs.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $creditDocuments = $request->only(['transaction_id', 'reason_id', 'courier_id']);
            $creditDocuments['register_date'] = Carbon::now();
            $creditDocuments['business_id'] = Auth::user()->business_id;
            $creditDocuments = CreditDocuments::create($creditDocuments);

            $outpout = [
                'success' => true,
                'data' => $creditDocuments,
                'msg' => __('crm.added_success')
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
     * @param  \App\CreditDocuments  $creditDocuments
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('cdocs.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = Auth::user()->business_id;

            $credit_document = CreditDocuments::join('transactions', 'credit_documents.transaction_id', '=', 'transactions.id')
                ->join('customers', 'customers.id', '=', 'transactions.customer_id')
                ->where('credit_documents.id', $id)
                ->first();

            $document_type = DocumentType::where('id', $credit_document->document_types_id)->select('document_name')->first();
            $reason = SupportDocuments::where('id', $credit_document->reason_id)->select('name')->first();
            $courier = Employees::where('id', $credit_document->courier_id)->select('first_name', 'last_name')->first();


            return view('credit_documents.show')
                ->with(compact('credit_document', 'credit_document', 'document_type', 'reason', 'courier'));
        }
    }

    public function reception($id)
    {
        if (!auth()->user()->can('cdocs.reception')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = Auth::user()->business_id;
            $user_id = request()->session()->get('user.id');
            $supprt_docs = SupportDocuments::forDropDown($business_id);
            $employees = User::forDropdown($business_id);
            $cdocs = CreditDocuments::where('business_id', $business_id)->find($id);
            return view('credit_documents.reception')
                ->with(compact('supprt_docs', 'employees', 'user_id', 'cdocs'));
        }
    }

    public function custodian($id)
    {
        if (!auth()->user()->can('cdocs.custodian')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = Auth::user()->business_id;
            $custodians = User::CustodiansDropdown($business_id);
            $cdocs = CreditDocuments::where('business_id', $business_id)->find($id);
            return view('credit_documents.custodian')
                ->with(compact('custodians', 'cdocs'));
        }
    }

    public function saveReception(Request $request, $id)
    {
        if (!auth()->user()->can('cdocs.reception')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['reception_user_id', 'document_type_received', 'document_number']);
                $business_id = Auth::user()->business_id;

                $reception = CreditDocuments::where('business_id', $business_id)->findOrFail($id);
                $reception->reception_user_id = $input['reception_user_id'];
                $reception->reception_date = Carbon::now();
                $reception->document_type_received = $input['document_type_received'];
                $reception->document_number = $input['document_number'];
                $reception->save();

                $outpout = [
                    'success' => true,
                    'data' => $reception,
                    'msg' => __('cxc.reception_added')
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $outpout = ['success' => false, 'msg' => $e->getMessage()];
            }
            return $outpout;
        }
    }

    public function saveCustodian(Request $request, $id)
    {
        if (!auth()->user()->can('cdocs.custodian')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['custodian_id']);
                $business_id = Auth::user()->business_id;

                $custodian = CreditDocuments::where('business_id', $business_id)->findOrFail($id);

                if (!empty($custodian->document_number) || !empty($custodian->document_type_received)) {
                    $custodian->custodian_id = $input['custodian_id'];
                    $custodian->custodian_receive_date = Carbon::now();
                    $custodian->save();

                    $outpout = [
                        'success' => true,
                        'data' => $custodian,
                        'msg' => __('cxc.custodian_added')
                    ];
                } else {
                    $outpout = [
                        'success' => false,
                        'data' => $custodian,
                        'msg' => __('cxc.doc_does_not_received')
                    ];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $outpout = ['success' => false, 'msg' => $e->getMessage()];
            }
            return $outpout;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CreditDocuments  $creditDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if (!auth()->user()->can('cdocs.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = Auth::user()->business_id;
        $document_types = DocumentType::forDropDown($business_id);
        $supprt_docs = SupportDocuments::forDropDown($business_id);
        $employees = Employees::forDropdown($business_id);

        $credit_document = CreditDocuments::join('transactions', 'credit_documents.transaction_id', '=', 'transactions.id')
            ->join('customers', 'customers.id', '=', 'transactions.customer_id')
            ->where('credit_documents.id', $id)
            ->first();


        return view('credit_documents.edit')
            ->with(compact('document_types', 'supprt_docs', 'employees', 'credit_document'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CreditDocuments  $creditDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('cdocs.update')) {
            abort(403, 'Unauthorized action.');
        }

        if ($id == null || $id == "") {
            return "problemas con el id";
        }
        try {
            $credit_array = $request->only(['transaction_id', 'reason_id', 'courier_id']);
            $creditDocuments = CreditDocuments::findOrFail($request->credit_document_id);
            $creditDocuments->transaction_id = $credit_array['transaction_id'];
            $creditDocuments->reason_id = $credit_array['reason_id'];
            $creditDocuments->courier_id = $credit_array['courier_id'];
            $creditDocuments->update();

            $outpout = [
                'success' => true,
                'data' => $creditDocuments,
                'msg' => __('crm.added_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => $e->getMessage()];
        }
        return $outpout;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CreditDocuments  $creditDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('cdocs.delete')) {
            abort(403, 'Unauthorized action.');
        }


        try {
            $creditDocuments = CreditDocuments::findOrFail($id)->delete();
            $outpout = [
                'success' => true,
                'data' => $creditDocuments,
                'msg' => __('crm.added_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => $e->getMessage()];
        }
        return $outpout;
    }
}
