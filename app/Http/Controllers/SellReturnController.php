<?php

namespace App\Http\Controllers;

use App\TaxRate;
use App\TaxGroup;

use App\Transaction;
use App\DocumentType;
use App\MovementType;
use App\Utils\TaxUtil;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\CashierUtil;

use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use App\DocumentCorrelative;
use App\TransactionSellLine;
use Illuminate\Http\Request;

use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SellReturnController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $contactUtil;
    protected $businessUtil;
    protected $moduleUtil;
    protected $cashierUtil;
    protected $taxUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(
        ProductUtil $productUtil,
        TransactionUtil $transactionUtil,
        ContactUtil $contactUtil,
        BusinessUtil $businessUtil,
        ModuleUtil $moduleUtil,
        CashierUtil $cashierUtil,
        TaxUtil $taxUtil
        )
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->cashierUtil = $cashierUtil;
        $this->taxUtil = $taxUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash', 
            'amount' => 0,
            'note' => '',
            'card_holder_name' => '',/** Card */
            'card_transaction_number' => '',
            'card_authotization_number' => '',
            'card_type' => '',
            'card_pos' => null,
            'check_number' => '', /** Check */
            'check_account' => '',
            'check_bank' => null,
            'check_account_owner' => '',
            'transfer_ref_no' => '', /** Transfer */
            'transfer_issuing_bank' => null,
            'transfer_destination_account' => '',
            'transfer_receiving_bank' => null,
            'credit_payment_term' => null, /** Credit */
            'is_return' => 0
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $sells =
            Transaction::leftJoin('customers', 'transactions.customer_id', 'customers.id')
                    ->join('business_locations AS bl', 'transactions.location_id', 'bl.id')
                    ->join('transactions as T1', 'transactions.return_parent_id', 'T1.id')
                    ->leftJoin('transaction_payments AS TP', 'transactions.id', 'TP.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return')
                    ->where('transactions.status', 'final')
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.correlative',
                        DB::raw('IF(customers.is_default = 1, T1.customer_name, customers.name) as customer_name'),
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.correlative as parent_sale',
                        'T1.id as parent_sale_id',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                        ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    @if(auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") )
                        <li><a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'SellReturnController@show\', [$parent_sale_id])}}"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a></li>
                        <li><a href="{{action(\'SellReturnController@add\', [$parent_sale_id])}}" ><i class="fa fa-edit" aria-hidden="true"></i> @lang("messages.edit")</a></li>
                    @endif

                    @if(auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") )
                        <li><a href="#" class="print-invoice" data-href="{{action(\'SellReturnController@printInvoice\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a></li>
                    @endif
                    </ul>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('parent_sale', function ($row) {
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="' . action('SellController@show', [$row->parent_sale_id]) . '">' . $row->parent_sale . '</button>';
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}</span></a>'
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellReturnController@show', [$row->parent_sale_id]) ;
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['final_total', 'action', 'parent_sale', 'payment_status', 'payment_due'])
                ->make(true);
        }

        return view('sell_return.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     if (!auth()->user()->can('sell.create')) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $business_id = request()->session()->get('user.business_id');

    //     //Check if subscribed or not
    //     if (!$this->moduleUtil->isSubscribed($business_id)) {
    //         return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
    //     }

    //     $business_locations = BusinessLocation::forDropdown($business_id);
    //     //$walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

    //     return view('sell_return.create')
    //         ->with(compact('business_locations'));
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not

        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $sell = Transaction::where('business_id', $business_id)
                            ->with(['sell_lines', 'location', 'return_parent', 'contact', 'tax'])
                            ->find($id);

        foreach ($sell->sell_lines as $key => $value) {
            $sell->sell_lines[$key]->formatted_qty = $this->transactionUtil->num_f($value->quantity);
        }

        /** Get parent doc type */
        $parent_doc_type = Transaction::join('document_types as dt', 'transactions.document_types_id', 'dt.id')
            ->where('transactions.id', $id)
            ->select('dt.short_name')
            ->first();

        /** return document filter by parent doc type */
        if ($parent_doc_type->short_name == 'Ticket') {
            $document_types =
                DocumentType::where('is_active', 1)
                    ->where('is_return_document', 1)
                    ->where('is_document_sale', 1)
                    ->where('print_format', 'ticket')
                    ->get()
                    ->pluck('document_name', 'id');

        } else if($parent_doc_type->short_name == 'CCF'){
            $document_types =
                DocumentType::where('is_active', 1)
                    ->where('is_return_document', 1)
                    ->where('is_document_sale', 1)
                    ->where('print_format', 'fiscal_credit')
                    ->get()
                    ->pluck('document_name', 'id');

        } else {
            $document_types =
                DocumentType::where('is_active', 1)
                    ->where('is_return_document', 1)
                    ->where('is_document_sale', 1)
                    ->get()
                    ->pluck('document_name', 'id');
        }

        return view('sell_return.add')
            ->with(compact('sell', 'document_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }
        $sell_return_id =
            !empty($request->input('sell_return_id')) ?
                $request->input('sell_return_id') : 0;

        try {
            $correlative_valid = $this->transactionUtil->validateCorrelative(
                $request->input('business_location_id'),
                $request->input('document_type'),
                $request->input('invoice_no'),
                $sell_return_id
            );
            
            if($correlative_valid['flag'] == true) {
                return ['success' => 0, 'msg' => __('sale.correlative_exists')];
            }

            $input = $request->except('_token');

            if (!empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');

                // Check if subscribed or not
                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
                }
        
                $user_id = $request->session()->get('user.id');

                $discount = [
                    'discount_type' => $input['discount_type'],
                    'discount_amount' => $input['discount_amount']
                ];
                $tax_id = $this->taxUtil->getTaxPercentSellReturn($input['transaction_id']);
                $tax_percent = $this->taxUtil->getTaxPercent($tax_id);
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $tax_percent, $discount);

                // Get parent sale
                $sell = Transaction::where('business_id', $business_id)
                    ->with(['sell_lines'])
                    ->findOrFail($input['transaction_id']);

                // Check if any sell return exists for the sale
                $sell_return = Transaction::where('business_id', $business_id)
                        ->where('type', 'sell_return')
                        ->where('return_parent_id', $sell->id)
                        ->first();

                $old_amount = ! empty($sell_return) ? $sell_return->final_total : 0;

                $sell_return_data = [
                    'correlative' => $input['invoice_no'],
                    'document_types_id' => $input['document_type'],
                    'discount_type' => $discount['discount_type'],
                    'discount_amount' => $this->productUtil->num_uf($input['discount_amount']),
                    // 'tax_id' => $input['tax_id'],
                    // 'tax_amount' => $invoice_total['tax'],
                    'total_before_tax' => $invoice_total['total_before_tax'],
                    'final_total' => $invoice_total['final_total'],
                    'payment_condition' => $sell->payment_condition
                ];

                if (empty($request->input('transaction_date'))) {
                    $sell_return_data['transaction_date'] =  \Carbon::now();

                } else {
                    $trans_time = session('business.time_format') == 12 ? date('h:i A') : date('H:i');
                    $trans_date = substr($request->input('transaction_date'), 0, 10); // Get date only
                    $transaction_date = $trans_date . ' ' . $trans_time;
                    $sell_return_data['transaction_date'] = $this->productUtil->uf_date($transaction_date, true);
                }

                $type_document = DocumentCorrelative::where('business_id', $business_id)
                    ->where('location_id', $sell->location_id)
                    ->where('status', 'active')
                    ->where('document_type_id', $input['document_type'])
                   ->first();
 
                $sell_return_data['serie'] = $type_document->serie;
                $sell_return_data['resolution'] = $type_document->resolution;
                $sell_return_data['cashier_closure_id'] = !empty($sell_return) ?
                    $sell_return->cashier_closure_id :
                    $this->cashierUtil->getCashierClosureActive($sell->cashier_id);

                DB::beginTransaction();

                if(!empty($type_document)){
                    if($type_document->actual <= $type_document->final){
                        $type_document->actual += 1;
                        $type_document->save();
                    }
                }
                
                // Generate reference number
                if (empty($sell_return_data['invoice_no'])) {
                    // Update reference count
                    $ref_count = $this->productUtil->setAndGetReferenceCount('sell_return');
                    $sell_return_data['invoice_no'] = $this->productUtil->generateReferenceNumber('sell_return', $ref_count);
                }

                if (empty($sell_return)) {
                    $sell_return_data['business_id'] = $business_id;
                    $sell_return_data['location_id'] = $sell->location_id;
                    $sell_return_data['warehouse_id'] = $sell->warehouse_id;
                    $sell_return_data['customer_id'] = $sell->customer_id;
                    $sell_return_data['customer_group_id'] = $sell->customer_group_id;
                    $sell_return_data['type'] = 'sell_return';
                    $sell_return_data['status'] = 'final';
                    $sell_return_data['created_by'] = $user_id;
                    $sell_return_data['return_parent_id'] = $sell->id;
                    $sell_return = Transaction::create($sell_return_data);
                } else {
                    $sell_return->update($sell_return_data);
                }

                // Update payment balance and total amount recovered
                $sell->payment_balance = ($sell->payment_balance - $old_amount) + $sell_return->final_total;
                $sell->total_amount_recovered = $sell_return->final_total;
                $sell->save();

                // Update sale payment status
                $this->transactionUtil->updatePaymentStatus($sell->id);

                // Update payment status
                $this->transactionUtil->updatePaymentStatus($sell_return->id, $sell_return->final_total);

                // Update quantity returned in sell line
                $returns = [];
                $product_lines = $request->input('products');

                foreach ($product_lines as $product_line) {
                    $returns[$product_line['sell_line_id']] = $product_line['quantity'];
                }

                foreach ($sell->sell_lines as $sell_line) {
                    if (array_key_exists($sell_line->id, $returns)) {
                        $quantity = $this->transactionUtil->num_uf($returns[$sell_line->id]);

                        $quantity_before = $this->transactionUtil->num_f($sell_line->quantity_returned);
                        $sell_line->quantity_returned = $quantity;
                        $sell_line->save();

                        // Update quantity sold in corresponding purchase lines
                        $this->transactionUtil->updateQuantitySoldFromSellLine($sell_line, $returns[$sell_line->id], $quantity_before);

                        // Update quantity in variation location details
                        $this->productUtil->updateProductQuantity($sell_return->location_id, $sell_line->product_id, $sell_line->variation_id, $returns[$sell_line->id], $quantity_before);
                    }
                }

                # Data to create or update kardex lines
                $lines = TransactionSellLine::where('transaction_id', $sell->id)
                    ->where('quantity_returned', '>', 0)
                    ->get();

                $movement_type = MovementType::where('name', 'sell_return')
                    ->where('type', 'input')
                    ->where('business_id', $business_id)
                    ->first();

                # Check if movement type is set else create it
                if (empty($movement_type)) {
                    $movement_type = MovementType::create([
                        'name' => 'sell_return',
                        'type' => 'input',
                        'business_id' => $business_id
                    ]);
                }
            
                # Store kardex lines
                $this->transactionUtil->createOrUpdateInputLines($movement_type, $sell_return, $sell_return->invoice_no, $lines);

                $receipt = $this->receiptContent($business_id, $sell_return->location_id, $sell_return->id);

                // Edit avarage cost
                // $enable_editing_avg_cost = $request->session()->get('business.enable_editing_avg_cost_from_purchase');

                // foreach ($lines as $line) {
                //     if ($enable_editing_avg_cost == 1) {
                //         $this->productUtil->recalculateProductCost($line->variation_id);
                //     }
                // }

                DB::commit();

                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.success'),
                    'receipt' => $receipt
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();

            } else {
                \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
                $msg = __('messages.something_went_wrong');
            }

            $output = [
                'success' => 0,
                'msg' => $msg
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $sell = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'customer',
                                    'return_parent',
                                    'tax',
                                    'sell_lines',
                                    'sell_lines.product',
                                    'sell_lines.variations',
                                    'location'
                                )
                                ->first();
        $sell_taxes = [];
        if (!empty($sell->return_parent->tax)) {
            if ($sell->return_parent->tax->is_tax_group) {
                $sell_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->return_parent->tax, $sell->return_parent->tax_amount));
            } else {
                $sell_taxes[$sell->return_parent->tax->name] = $sell->return_parent->tax_amount;
            }
        }

        $total_discount = 0;
        if ($sell->return_parent->discount_type == 'fixed') {
            $total_discount = $sell->return_parent->discount_amount;
        } elseif ($sell->return_parent->discount_type == 'percentage') {
            $total_after_discount = $sell->return_parent->final_total - $sell->return_parent->tax_amount;
            $total_before_discount = $total_after_discount * 100 / (100 - $sell->return_parent->discount_amount);
            $total_discount = $total_before_discount - $total_after_discount;
        }
        
        return view('sell_return.show')
            ->with(compact('sell', 'sell_taxes', 'total_discount'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    /**
     * Return the row for the product
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProductRow()
    {
    }

    /**
     * Returns the content for the receipt
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  int  $transaction_id
     * @param string $printer_type = null
     *
     * @return array
     */
    private function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null
    ) {
    
        $output = [
            'is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => []
        ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        // Check if printing of invoice is enabled or not.
        if ($location_details->print_receipt_on_invoice == 1) {
            // If enabled, get print type.
            $output['is_enabled'] = true;

            $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);

            // Check if printer setting is provided.
            $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

            // Get print format form document type
            $print_format = $this->transactionUtil->getDocumentTypePrintFormat($transaction_id);
            if ($print_format) {
                $receipt_details = $this->transactionUtil->getFormatDetails(
                    $transaction_id,
                    $invoice_layout,
                    $business_id,
                    $location_details
                );

            } else {
                $receipt_details = $this->transactionUtil->getReceiptDetails(
                    $transaction_id,
                    $location_id,
                    $invoice_layout,
                    $business_details,
                    $location_details,
                    $receipt_printer_type
                );
            }

            $receipt_details->currency = session('currency');
            
            // If print type browser - return the content, printer - return printer config data, and invoice format config
            if ($receipt_printer_type == 'printer') {
                $output['print_type'] = 'printer';
                $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
                $output['data'] = $receipt_details;

            } else {
                if ($print_format) {
                    $layout = 'sale_pos.receipts.' . $print_format . '_return';

                } else {
                    $layout = ! empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';
                }

                $output['html_content'] = view($layout, compact('receipt_details'))->render();
            }
        }

        return $output;
    }

    /**
     * Prints invoice for sell
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice(Request $request, $transaction_id)
    {
        if (request()->ajax()) {
            try {
                $output = ['success' => 0,
                        'msg' => trans("messages.something_went_wrong")
                        ];

                $business_id = $request->session()->get('user.business_id');
            
                $transaction = Transaction::where('business_id', $business_id)
                                ->where('id', $transaction_id)
                                ->first();

                if (empty($transaction)) {
                    return $output;
                }

                $receipt = $this->receiptContent($business_id, $transaction->location_id, $transaction_id, 'browser');

                if (!empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                $output = [
                    'success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];

                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            }

            return $output;
        }
    }
}
