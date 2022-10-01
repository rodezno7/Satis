<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Transaction;
use App\BusinessLocation;
use App\PaymentCommitment;
use App\PaymentCommitmentLine;

use DB;
use DataTables;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;

class PaymentCommitmentController extends Controller
{
    /**
     * All Utils instance
     */
    private $transactionUtil;

    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil){
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('payment_commitment.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->user()->business_id;
        if(request()->ajax()){
            $payment_commitments = PaymentCommitment::join('contacts as c', 'payment_commitments.supplier_id', 'c.id')
                ->join('business_locations as bl', 'payment_commitments.location_id', 'bl.id')
                ->where('payment_commitments.business_id', $business_id)
                ->select(
                    'payment_commitments.id',
                    'payment_commitments.date',
                    'payment_commitments.type',
                    'payment_commitments.is_annulled',
                    'payment_commitments.reference',
                    'c.supplier_business_name as supplier_name',
                    'bl.name as location_name',
                    'payment_commitments.total'
                );

            return DataTables::of($payment_commitments)
                ->addColumn('action', function($row){
                    $action = "";
                    if(auth()->user()->can('payment_commitment.edit')){
                        $action .= "<a class='btn btn-primary btn-xs edit_payment_commitment' href=". action("PaymentCommitmentController@edit", [$row->id]) ." title='Editar'><i class='glyphicon glyphicon-edit'></i></a>";
                    }
                    if(auth()->user()->can('payment_commitment.print') && !$row->is_annulled){
                        $action .= "&nbsp;<a class='btn btn-success btn-xs print_payment_commitment' href=". action("PaymentCommitmentController@print", [$row->id]) ." target='_blank' title='Imprimir'><i class='glyphicon glyphicon-print'></i></a>";
                    }
                    if(auth()->user()->can('payment_commitment.annul') && !$row->is_annulled){
                        $action .= "&nbsp;<a class='btn btn-warning btn-xs annul_payment_commitment' href=". action("PaymentCommitmentController@annul", [$row->id]) ." title='Anular'><i class='fa fa-times-circle-o' aria-hidden='true'></i></a>";
                    }
                    if(auth()->user()->can('payment_commitment.delete')){
                        $action .= "&nbsp;<a class='btn btn-danger btn-xs delete_payment_commitment' href=". action("PaymentCommitmentController@destroy", [$row->id]) ." title='Eliminar'><i class='glyphicon glyphicon-trash'></i></a>";
                    }
                    return $action;
                })->removeColumn('id')
                ->editColumn('type', '{{ __("contact.". $type) }}')
                ->editColumn('reference', function($row){
                    $reference = $row->reference;
                    if($row->is_annulled){
                        $reference = '<span style="color:red;">'. $reference .' ('. __('contact.annulled') .')</span>';
                    }
                    return $reference;

                })->removeColumn('is_annulled')
                ->editColumn('date', '{{ @format_date($date) }}')
                ->editColumn('total', '<span class="display_currency" data-currency_symbol="true" ">{{ $total }}</span>')
                ->rawColumns(['type', 'reference', 'date', 'total', 'action'])
                ->make(true);
        }

        return view("payment_commitment.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('payment_commitment.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->user()->business_id;
        $locations = BusinessLocation::forDropdown($business_id);

        return view("payment_commitment.create", compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('payment_commitment.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $pc = new PaymentCommitment();
            $pc->reference = $request->input('code');
            $pc->date = $this->transactionUtil->uf_date($request->input('date'));
            $pc->type = $request->input('type');
            $pc->supplier_id = $request->input('supplier_id');
            $pc->business_id = $request->user()->business_id;
            $pc->location_id = $request->input('location_id');
            $pc->total = $this->transactionUtil->num_uf($request->input('total_amount'));
            $pc->save();

            $pc_lines = $request->input('payment_commitment_lines');
            foreach($pc_lines as $pcl){
                $line = new PaymentCommitmentLine();
                $line->payment_commitment_id = $pc->id;
                $line->transaction_id = $pc->type == "manual" ? null : $pcl['transaction_id'];
                $line->document_name = $pcl['doc_type'];
                $line->reference = $pcl['ref_no'];
                $line->total = $this->transactionUtil->num_uf($pcl['amount']);
                $line->save();
            }

            return [
                'success' => true,
                'transaction_id' => $pc->id,
                'msg' => __("contact.payment_commitment_added_successfully")
            ];
        
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [ 'success' => false, 'msg' => __("messages.something_went_wrong") ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentCommitment  $paymentCommitment
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentCommitment $paymentCommitment)
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
        if (!auth()->user()->can('payment_commitment.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $pc = PaymentCommitment::where("id", $id)
            ->with("payment_commitment_lines")
            ->first();

        $supplier = Contact::where('id', $pc->supplier_id)
            ->pluck('supplier_business_name', 'id')
            ->toArray();

        $business_id = request()->user()->business_id;
        $locations = BusinessLocation::forDropdown($business_id);

        return view('payment_commitment.edit', compact('pc', 'supplier', 'locations'));
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
        if (!auth()->user()->can('payment_commitment.edit')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $pc = PaymentCommitment::find($id);
            $pc->reference = $request->input('code');
            $pc->date = $this->transactionUtil->uf_date($request->input('date'));
            $pc->type = $request->input('type');
            $pc->supplier_id = $request->input('supplier_id');
            $pc->business_id = $request->user()->business_id;
            $pc->location_id = $request->input('location_id');
            $pc->total = $this->transactionUtil->num_uf($request->input('total_amount'));
            $pc->save();

            $pc_lines = $request->input('payment_commitment_lines');
            foreach($pc_lines as $pcl){
                $line = PaymentCommitmentLine::updateOrCreate(
                    [
                        "id" => $pcl['pcl_id'],
                        'payment_commitment_id' => $pc->id
                    ],
                    [
                        'transaction_id' => $pc->type == "manual" ? null : $pcl['transaction_id'],
                        'document_name' => $pcl['doc_type'],
                        'reference' => $pcl['ref_no'],
                        'total' => $this->transactionUtil->num_uf($pcl['amount'])
                    ]
                );
            }

            return [
                'success' => true,
                'msg' => __("contact.payment_commitment_updated_successfully")
            ];
        
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
        if (!auth()->user()->can('payment_commitment.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $payment_commitment = PaymentCommitment::find($id);

            $payment_commitment_lines =
                PaymentCommitmentLine::where('payment_commitment_id', $id)
                    ->delete();

            $payment_commitment->delete();

            DB::commit();

            $output = [ 'success' => true, 'msg' => __("contact.payment_commitment_deleted_successfully") ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            DB::rollback();

            $output = [ 'success' => false, 'msg' => __("messages.something_went_wrong") ];
        }

        return $output;
    }

    /**
     * Annul payment commitment
     * 
     * @param int $id
     * @return array
     * @author Arquímides Martínez
     */
    public function annul($id){
        if (!auth()->user()->can('payment_commitment.annul')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $pc = PaymentCommitment::find($id);
            $pc->is_annulled = true;
            $pc->updated_by = auth()->user()->id;
            $pc->save();

            $output = [ 'success' => true, 'msg' => __("contact.payment_commitment_annulled_successfully") ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            DB::rollback();

            $output = [ 'success' => false, 'msg' => __("messages.something_went_wrong") ];
        }

        return $output;
    }

    /**
     * print payment commitment
     * @param int payment_commitment_id
     */
    public function print($id){
    if (!auth()->user()->can('payment_commitment.view')) {
            abort(403, 'Unauthorized action.');
        }
        
        $pc = PaymentCommitment::join("business as b", "payment_commitments.business_id", "b.id")
            ->join("business_locations as bl", "payment_commitments.location_id", "bl.id")
            ->join("contacts as c", "payment_commitments.supplier_id", "c.id")
            ->where("payment_commitments.id", $id)
            ->select(
                "b.business_full_name as business_name",
                "bl.name as location_name",
                "c.supplier_business_name as supplier_name",
                "payment_commitments.date",
                "payment_commitments.reference as ref_no",
                "payment_commitments.total",
            )->first();
        
        $pcl = PaymentCommitmentLine::where("payment_commitment_id", $id)
            ->get();

        $pdf = \PDF::loadView('payment_commitment.partials.print_pdf', compact('pc', 'pcl'));
		$pdf->setPaper('letter', 'portrait');
		return $pdf->stream();
    }

    /**
     * add payment commitment row
     * @param int $transaction_id
     */
    public function addPaymentCommitmentRow(){
        $transaction_id = request()->input('transaction_id');
        $type = request()->input('type');

        $transaction = null;
        if($type == "automatic"){
            $transaction = Transaction::join("document_types as dt", "transactions.document_types_id", "dt.id")
                ->where("transactions.id", $transaction_id)
                ->select(
                    "transactions.id as transaction_id",
                    "dt.document_name as doc_type",
                    "transactions.ref_no",
                    "transactions.final_total as amount"
                )->first();
        }

        return view('payment_commitment.partials.payment_commitment_row', compact('transaction', 'type'));
    }
}
