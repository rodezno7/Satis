<?php

namespace App\Http\Controllers;

use App\MovementType;
use App\PurchaseLine;
use App\DocumentType;
use Illuminate\Http\Request;

use App\Transaction;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use App\Utils\TaxUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;

class PurchaseReturnController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $taxUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, TaxUtil $taxUtil)
    {
        $this->taxUtil = $taxUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $purchases_returns = Transaction::leftJoin('contacts', 'transactions.contact_id', 'contacts.id')
                ->join('business_locations AS BS', 'transactions.location_id', 'BS.id')
                ->join('transactions AS T', 'transactions.return_parent_id', 'T.id')
                ->leftJoin('transaction_payments AS TP', 'transactions.id', 'TP.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase_return')
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.ref_no',
                    'contacts.name',
                    'transactions.status',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'transactions.return_parent_id',
                    'T.import_type as import_type',
                    'BS.name as location_name',
                    'T.ref_no as parent_purchase',
                    DB::raw('SUM(TP.amount) as amount_paid')
                )
                ->groupBy('transactions.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases_returns->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->supplier_id)) {
                $supplier_id = request()->supplier_id;
                $purchases_returns->where('contacts.id', $supplier_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchases_returns->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }
            return Datatables::of($purchases_returns)
                ->addColumn('action', function ($row) {
                    $html = '<a href="' . action('PurchaseReturnController@add', $row->return_parent_id) . '" class="btn btn-info btn-xs" ><i class="glyphicon glyphicon-edit"></i>' .
                        __("messages.edit") .
                        '</a>';

                    return $html;
                })
                ->removeColumn('id')
                ->removeColumn('return_parent_id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="@if($payment_status != "paid"){{__(\'lang_v1.\' . $payment_status)}}@else{{__("lang_v1.received")}}@endif"><span class="label @payment_status($payment_status)">@if($payment_status != "paid"){{__(\'lang_v1.\' . $payment_status)}} @else {{__("lang_v1.received")}} @endif
                        </span></a>'
                )
                ->editColumn('parent_purchase', function ($row) {
                    $route = !is_null($row->import_type) 
                        ? route('international-purchases.show', $row->return_parent_id) 
                        :  action('PurchaseController@show', [$row->return_parent_id]);
                    return '<a href="#" data-href="' .$route . '" class="btn-modal" data-container=".view_modal">' . $row->parent_purchase . '</a>';
                })
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        $route = '';
                        if (auth()->user()->can("purchase.view")) {
                            $route = !is_null($row->import_type) ? 
                                route('international-purchases.show', $row->return_parent_id) : 
                                action('PurchaseReturnController@show', [$row->return_parent_id]);
                        }
                        return $route;
                    }
                ])
                ->rawColumns(['final_total', 'action', 'payment_status', 'parent_purchase', 'payment_due'])
                ->make(true);
        }
        return view('purchase_return.index');
    }

    /**
     * Show the form for purchase return.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $purchase = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->with(['purchase_lines', 'contact', 'tax', 'return_parent'])
            ->find($id);
        
        $tax_id = null;
        $tax_percent = 13;
        foreach ($purchase->purchase_lines as $pl) {
            $tax_percent = $this->taxUtil->getTaxPercent($pl->tax_id);
            $tax_percent = $tax_percent * 100;
            
            $tax_id = $pl->tax_id;
            break;
        }

        $documents =
            DocumentType::where('is_active', 1)
                ->where('is_document_purchase', 1)
                ->where('is_return_document', 1)
                ->pluck('document_name', 'id');

        foreach ($purchase->purchase_lines as $key => $value) {
            $qty_available = $value->quantity - $value->quantity_sold - $value->quantity_adjusted;
            $purchase->purchase_lines[$key]->formatted_qty_available = $this->transactionUtil->num_f($qty_available);
        }

        return view('purchase_return.add')
            ->with(compact('purchase', 'documents', 'tax_percent', 'tax_id'));
    }

    /**
     * Saves Purchase returns in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $purchase = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase')
                ->with(['purchase_lines'])
                ->findOrFail($request->input('transaction_id'));

            $return_quantities = $request->input('returns');
            $return_total = 0;

            DB::beginTransaction();

            foreach ($purchase->purchase_lines as $purchase_line) {
                $old_return_qty = $purchase_line->quantity_returned;
                $purchase_line->quantity_returned = !empty($return_quantities[$purchase_line->id]) ? $this->productUtil->num_uf($return_quantities[$purchase_line->id]) : 0;
                $purchase_line->save();
                $return_total += $purchase_line->purchase_price_inc_tax * $purchase_line->quantity_returned;

                //Decrease quantity in variation location details
                if ($old_return_qty != $purchase_line->quantity_returned) {
                    $this->productUtil->decreaseProductQuantity(
                        $purchase_line->product_id,
                        $purchase_line->variation_id,
                        $purchase->location_id,
                        $purchase_line->quantity_returned,
                        $old_return_qty
                    );
                }
            }
            $return_total_inc_tax = $return_total + $request->input('tax_amount');

            $return_transaction_data = [
                'document_types_id' => $request->input('document_type'),
                'serie' => $request->input('serie'),
                'ref_no' => $request->input('ref_no'),
                'transaction_date' => $this->transactionUtil->uf_date($request->input('transaction_date')),
                'total_before_tax' => $return_total,
                'final_total' => $return_total_inc_tax,
                'tax_amount' => $request->input('tax_amount'),
                'tax_id' => $request->input('tax_id')
            ];

            $return_transaction = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase_return')
                ->where('return_parent_id', $purchase->id)
                ->first();

            if (!empty($return_transaction)) {
                $return_transaction->update($return_transaction_data);
            } else {
                $return_transaction_data['business_id'] = $business_id;
                $return_transaction_data['location_id'] = $purchase->location_id;
                $return_transaction_data['warehouse_id'] = $purchase->warehouse_id;
                $return_transaction_data['type'] = 'purchase_return';
                $return_transaction_data['status'] = 'final';
                $return_transaction_data['contact_id'] = $purchase->contact_id;
                $return_transaction_data['transaction_date'] = \Carbon::now();
                $return_transaction_data['created_by'] = request()->session()->get('user.id');
                $return_transaction_data['return_parent_id'] = $purchase->id;
                $return_transaction_data['purchase_type'] = $purchase->purchase_type;

                $return_transaction = Transaction::create($return_transaction_data);
            }

            # Data to create or update kardex lines
            $lines = PurchaseLine::where('transaction_id', $purchase->id)->get();

            $movement_type = MovementType::where('name', 'purchase_return')
                ->where('type', 'output')
                ->where('business_id', $business_id)
                ->first();

            # Check if movement type is set else create it
            if (empty($movement_type)) {
                $movement_type = MovementType::create([
                    'name' => 'purchase_return',
                    'type' => 'output',
                    'business_id' => $business_id
                ]);
            }

            # Store kardex lines
            $this->transactionUtil->createOrUpdateOutputLines($movement_type, $return_transaction, $return_transaction->ref_no, $lines);

            //update payment status
            $this->updatePaymentStatus($purchase->id, $return_transaction->final_total);

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.purchase_return_added_success')
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('purchase-return')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $purchase = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->with(['return_parent', 'return_parent.tax', 'purchase_lines', 'contact', 'tax'])
            ->find($id);

        $purchase_taxes = [];
        if (!empty($purchase->return_parent->tax)) {
            if ($purchase->return_parent->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->return_parent->tax, $purchase->return_parent->tax_amount));
            } else {
                $purchase_taxes[$purchase->return_parent->tax->name] = $purchase->return_parent->tax_amount;
            }
        }

        return view('purchase_return.show')
            ->with(compact('purchase', 'purchase_taxes'));
    }

    /**
     * get purchase return by discount
     * 
     * @param int $id
     * @return Response
     * 
     * @author Arquímides Martínez
     */
    public function getPurchaseReturnDiscount($id) {
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $purchase = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->with(['contact', 'return_parent'])
            ->find($id);

        $documents =
            DocumentType::where('is_active', 1)
                ->where('is_document_purchase', 1)
                ->where('is_return_document', 1)
                ->pluck('document_name', 'id');

        $taxes = $this->taxUtil->getTaxGroups($business_id, 'products', true);

        return view ('purchase_return.add_discount',
            compact('purchase', 'documents', 'taxes'));
    }

    /**
     * post purchase return by discount
     * 
     * @param Request $request
     * @param int $int
     * @return json
     * 
     * @author Arquímides Martínez
     */
    public function postPurchaseReturnDiscount(Request $request, $id){
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $parent = Transaction::find($id);
            $user_id = $request->user()->id;

            $return = Transaction::updateOrCreate(
                [ 'return_parent_id' => $id ],
                [
                    'business_id' => $parent->business_id,
                    'transaction_date' => $this->transactionUtil->uf_date($request->input('transaction_date')),
                    'location_id' => $parent->location_id,
                    'contact_id' => $parent->contact_id,
                    'type' => 'purchase_return',
                    'status' => 'final',
                    'payment_status' => 'due',
                    'ref_no' => $request->input('ref_no'),
                    'total_before_tax' => $request->input('total_before_tax'),
                    'tax_id' => $request->input('tax_group_id'),
                    'tax_amount' => $request->input('tax_amount'),
                    'final_total' => $request->input('final_total'),
                    'document_types_id' => $request->input('document_type'),
                    'serie' => $request->input('serie'),
                    'created_by' => $user_id
                ]
            );

            if ($return->final_total <= $parent->final_total){
                //update payment status
                $this->updatePaymentStatus($parent->id, $return->final_total);

                $output = ['success' => true, 'msg' => __('lang_v1.purchase_return_added_success')];

                DB::commit();

            } else {
                $output = ['success' => false, 'msg' => __('purchase.purchase_return_amounts_not_match')];
                DB::rollback();;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }

    /**
     * Update payment status for return discount transaction
     * 
     * @param int $parent_id
     * @param double $final_total
     * @return void
     * 
     * @author Arquímides Martínez
     */
    private function updatePaymentStatus($parent_id, $final_total){
        $parent_transaction = Transaction::find($parent_id);

        $total_paid = $this->transactionUtil->getTotalPaid($parent_id);

        $status = 'due';

        $parent_final_total = $parent_transaction->purchase_type == 'international' ? $parent_transaction->total_after_expense : $parent_transaction->final_total;

        if ($parent_final_total <= ($final_total + $total_paid)) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        $parent_transaction->payment_status = $status;
        $parent_transaction->save();
    }
}
