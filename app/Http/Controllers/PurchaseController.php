<?php

namespace App\Http\Controllers;

use App\Pos;
use App\User;
use App\Bank;
use App\TaxRate;
use App\Product;
use App\Contact;
use App\TaxGroup;
use App\Business;
use App\Catalogue;
use App\Employees;
use App\Warehouse;
use App\Variation;
use App\Country;
use App\TypeEntrie;
use App\Transaction;
use App\PaymentTerm;
use App\DocumentType;
use App\PurchaseLine;
use App\MovementType;
use App\CustomerGroup;
use App\BusinessLocation;
use App\AccountBusinessLocation;
use App\Apportionment;
use App\ApportionmentHasTransaction;
use App\BankAccount;
use App\BusinessType;
use App\TransactionHasImportExpense;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Validator;

use App\Exports\DebtsToPayReportExport;

use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\TaxUtil;
use App\Utils\AccountingUtil;
use App\Utils\ContactUtil;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Excel;

class PurchaseController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $taxUtil;
    protected $accountingUtil;
    protected $contactUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(
        ProductUtil $productUtil,
        TransactionUtil $transactionUtil,
        TaxUtil $taxUtil,
        ContactUtil $contactUtil,
        BusinessUtil $businessUtil,
        ModuleUtil $moduleUtil,
        AccountingUtil $accountingUtil
    )
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->taxUtil = $taxUtil;
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->accountingUtil = $accountingUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash',
            'amount' => 0,
            'note' => '',
            'card_holder_name' => '',
            /** Card */
            'card_transaction_number' => '',
            'card_authotization_number' => '',
            'card_type' => '',
            'card_pos' => null,
            'check_number' => '',
            /** Check */
            'check_account' => '',
            'check_bank' => null,
            'check_account_owner' => '',
            'transfer_ref_no' => '',
            /** Transfer */
            'transfer_issuing_bank' => null,
            'transfer_destination_account' => '',
            'transfer_receiving_bank' => null,
            'credit_payment_term' => null,
            /** Credit */
            'is_return' => 0
        ];

        /** Business types */
        $this->business_type = BusinessType::select('id', 'name')
            ->pluck('name', 'id');

        /** Payment conditions */
        $this->payment_conditions = ['cash', 'credit'];

        // Binnacle data
        $this->module_name = 'purchase';
        //DB::statement('SET SESSION sql_require_primary_key=0');
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

            // Filters
            $purchase_type = ! empty(request()->input('purchase_type')) ? request()->input('purchase_type') : 'all';
            $payment_status = ! empty(request()->input('payment_status')) ? request()->input('payment_status') : 'all';

            $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->join('business_locations AS BS', 'transactions.location_id', 'BS.id')
                ->leftJoin('transaction_payments AS TP', 'transactions.id', 'TP.transaction_id')
                ->leftJoin('transactions AS PR', 'transactions.id', 'PR.return_parent_id')
                ->leftJoin('document_types', 'transactions.document_types_id', 'document_types.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->select(
                    'transactions.id',
                    'transactions.document',
                    'transactions.transaction_date',
                    'transactions.ref_no',
                    'contacts.name',
                    'transactions.status',
                    'transactions.payment_status',
                    'transactions.import_type',
                    'BS.name as location_name',
                    'PR.id as return_transaction_id',
                    DB::raw('SUM(TP.amount) as amount_paid'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=PR.id ) as return_paid'),
                    DB::raw('COUNT(PR.id) as return_exists'),
                    DB::raw('COALESCE(PR.final_total, 0) as amount_return'),
                    'document_types.document_name as doc_name',
                    'transactions.purchase_type',
                    DB::raw("IF(transactions.purchase_type = 'international', transactions.total_after_expense, transactions.final_total) as final_total")
                )
                ->groupBy('transactions.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->supplier_id)) {
                $supplier_id = request()->supplier_id;
                $purchases->where('contacts.id', $supplier_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            // Purchase type filter
            if ($purchase_type != 'all') {
                $purchases->where('transactions.purchase_type', $purchase_type);
            }

            // Payment status filter
            if ($payment_status != 'all') {
                if ($payment_status == 'paid') {
                    $purchases->where('transactions.payment_status', $payment_status);
                } else {
                    $purchases->where('transactions.payment_status', '!=', 'paid');
                }
            }

            return Datatables::of($purchases)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (auth()->user()->can("purchase.view")) {
                        if (!is_null($row->import_type)) {
                            $html .= '<li><a href="#" data-href="' . route('international-purchases.show', $row->id) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                        } else {
                            $html .= '<li><a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                        }
                    }
                    if (auth()->user()->can("purchase.view")) {
                        if (!is_null($row->import_type)) {
                            $html .= '<li><a href="#" class="print-invoice" data-href="' . action('PurchaseController@printInvoice', [$row->id, 'import']) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        } else {
                            $html .= '<li><a href="#" class="print-invoice" data-href="' . action('PurchaseController@printInvoice', [$row->id, 'national']) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        }
                    }
                    if (auth()->user()->can("purchase.update")) {
                        $html .= '<li><a href="' . action('PurchaseController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                    }

                    $is_finished = 0;

                    $has_apportionment = ApportionmentHasTransaction::where('transaction_id', $row->id)->first();

                    if (! empty($has_apportionment)) {
                        $apportionment = Apportionment::find($has_apportionment->apportionment_id);

                        if (! empty($apportionment)) {
                            $is_finished = $apportionment->is_finished;
                        }
                    }

                    if (auth()->user()->can("purchase.delete") && $is_finished == 0) {
                        $html .= '<li><a href="' . action('PurchaseController@destroy', [$row->id]) . '" class="delete-purchase"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '<li><a href="' . action('LabelsController@show') . '?purchase_id=' . $row->id . '" data-toggle="tooltip" title="Print Barcode/Label"><i class="fa fa-barcode"></i>' . __('barcode.labels') . '</a></li>';

                    if (auth()->user()->can("purchase.view") && !empty($row->document)) {
                        $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document;
                        $html .= '<li><a href="' . url('uploads/documents/' . $row->document) . '" download="' . $document_name . '"><i class="fa fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';
                    }

                    if (auth()->user()->can("purchase.create")) {
                        $html .= '<li class="divider"></li>';
                        if ($row->payment_status != 'paid') {
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i>' . __("purchase.add_payment") . '</a></li>';
                        }
                        $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) .
                            '" class="view_payment_modal"><i class="fa fa-money" aria-hidden="true" ></i>' . __("purchase.view_payments") . '</a></li>';
                    }

                    if (auth()->user()->can("purchase.update")) {
                        $html .= '<li><a href="' . action('PurchaseReturnController@add', [$row->id]) .
                            '"><i class="fa fa-undo" aria-hidden="true" ></i>' . __("lang_v1.purchase_return") . '</a></li>';
                    }

                    if (auth()->user()->can("purchase.update")) {
                        $html .= '<li><a class="return_discount" href="' . action('PurchaseReturnController@getPurchaseReturnDiscount', [$row->id]) .
                            '"><i class="fa fa-undo" aria-hidden="true" ></i>' . __("lang_v1.purchase_return_discount") . '</a></li>';
                    }

                    if (auth()->user()->can("send_notification")) {
                        if ($row->status == 'ordered') {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "new_order"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.new_order_notification") . '</a></li>';
                        } else if ($row->status == 'received') {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "items_received"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_received_notification") . '</a></li>';
                        } else if ($row->status == 'pending') {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "items_pending"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_pending_notification") . '</a></li>';
                        }
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn('ref_no', function ($row) {
                    return !empty($row->return_exists) ? $row->ref_no . ' <small class="label bg-red label-round" title="' . __('lang_v1.some_qty_returned') . '"><i class="fa fa-undo"></i></small>' : $row->ref_no;
                })
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn(
                    'status',
                    '<span class="label @transaction_status($status) status-label" data-status-name="{{__(\'lang_v1.\' . $status)}}" data-orig-value="{{$status}}">{{__(\'lang_v1.\' . $status)}}
                        </span>'
                )
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}
                        </span></a>'
                )
                ->addColumn('payment_due', function ($row) {
                    $due = round($row->final_total, 2) - round($row->amount_paid, 2);
                    $due_html = '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';

                    if (!empty($row->return_exists)) {
                        $return_due = round($row->amount_return, 2) - round($row->return_paid, 2);
                        $due_html .= '<br><strong>' . __('lang_v1.purchase_return') . ':</strong> <a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="display_currency purchase_return" data-currency_symbol="true" data-orig-value="' . $return_due . '">' . $return_due . '</span></a>';
                    }
                    return $due_html;
                })
                ->addColumn('purchase_type', function ($row) {
                    if (! is_null($row->import_type)) {
                        $has_apportionment = ApportionmentHasTransaction::where('transaction_id', $row->id)->first();

                        if (! empty($has_apportionment)) {
                            $apportionment = Apportionment::find($has_apportionment->apportionment_id);

                            $color = $apportionment->is_finished == 1 ? '#98D973' : '#00c0ef';
                            $text = $apportionment->is_finished == 1 ? __('apportionment.apportionment_processed') : __('apportionment.apportionment');

                            $purchase_type =
                                "<i class=\"fa fa-circle\"
                                    data-toggle=\"tooltip\"
                                    data-placement=\"top\"
                                    data-html=\"true\"
                                    data-original-title=\"$text: " . $apportionment->reference . "\"
                                    aria-hidden=\"true\"
                                    style=\"color: $color;\">
                                </i>&nbsp;&nbsp;" .
                                __('purchase.importation');
                            
                        } else {
                            $purchase_type =
                                "<i class=\"fa fa-circle\"
                                    data-toggle=\"tooltip\"
                                    data-placement=\"top\"
                                    data-html=\"true\"
                                    data-original-title=\"" . __('apportionment.without_apportionment') . "\"
                                    aria-hidden=\"true\"
                                    style=\"color: #dd4b39;\">
                                </i>&nbsp;&nbsp;" .
                                __('purchase.importation');
                        }
                        
                    } else {
                        $purchase_type = __('purchase.national');
                    }

                    return $purchase_type;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        $route = '';
                        if (auth()->user()->can("purchase.view")) {
                            $route = !is_null($row->import_type) ? 
                                route('international-purchases.show', $row->id) : 
                                action('PurchaseController@show', [$row->id]);
                        }
                        return $route;
                    }
                ])
                ->rawColumns(['final_total', 'action', 'payment_due', 'payment_status', 'status', 'ref_no', 'purchase_type'])
                ->make(true);
        }
        return view('purchase.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        //$taxes = TaxRate::where('business_id', $business_id)
        //    ->get();

        $taxes = $this->taxUtil->getTaxGroups($business_id, 'products', true);

        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }

        $customer_groups = CustomerGroup::forDropdown($business_id);
        $employees_sales = Employees::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types();

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);
        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'products');
        /** Business type */
        $business_type = $this->business_type;
        /** Payment conditions */
        $payment_conditions = $this->payment_conditions;

        $payment_condition = ['cash' => __('order.cash'), 'credit' => __('order.credit')];

        // Gets warehouses
        $warehouses = Warehouse::forDropdown($business_id);

        // Gets document types
        $document_types = DocumentType::forDropdown($business_id);

        $payment_terms = PaymentTerm::forDropdown($business_id);

        /** Banks */
        $banks = Bank::where('business_id', $business_id)
            ->pluck('name', 'id');
        /** Pos */
        $pos = Pos::where('business_id', $business_id)
            ->pluck('name', 'id');

        /** Accounting account for suppliers/providers */
        $business = Business::find($business_id);
        $supplier_account = "";
        if($business->accounting_supplier_id){
            $supplier_account =
                Catalogue::where("status", 1)
                    ->where("id", $business->accounting_supplier_id)
                    ->value("code");
        }

        // Purchase type
        $purchase_type = request()->get('type') == 1 ? 'national' : 'international';
        $countries = Country::select('id', 'name')
        ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $business_debt_to_pay_type = $business->debt_to_pay_type;

        // Number of decimals in sales
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $decimals_in_purchases = $product_settings['decimals_in_purchases'];

        // Bank account
        $bank_accounts = BankAccount::pluck('name', 'id');

        return view('purchase.create')
            ->with(compact(
                'taxes',
                'orderStatuses',
                'tax_groups',
                'business_locations',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'employees_sales',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'accounts',
                'business_type',
                'payment_conditions',
                'warehouses',
                'document_types',
                'payment_condition',
                'payment_terms',
                'pos',
                'banks',
                'supplier_account',
                'purchase_type',
                'countries',
                'business_debt_to_pay_type',
                'decimals_in_purchases',
                'bank_accounts'
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        if(!auth()->user()->can('is_close_book') &&
            $this->transactionUtil->isClosed($request->input('transaction_date')) > 0){
            $output = [
                'success' => 0,
                'msg' => __('purchase.month_closed')
            ];
            return redirect('purchases')->with('status', $output);
        }

        try {
            //Adding temporary fix by validating
            $request->validate([
                'status' => 'required',
                'contact_id' => 'required',
                'transaction_date' => 'required',
                // 'total_before_tax' => 'required',
                'location_id' => 'required',
                'warehouse_id' => 'required',
                'final_total' => 'required',
                'payment_condition' => 'required',
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
            ]);

            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }

            $transaction_data = $request->only([
                'ref_no',
                'status',
                'contact_id',
                'transaction_date',
                'total_before_tax',
                'location_id',
                'discount_type',
                'discount_amount',
                'tax_id',
                'tax_amount',
                'final_total',
                'additional_notes',
                'exchange_rate',
                'warehouse_id',
                'document_types_id',
                'payment_condition',
                'payment_term_id',
                'purchase_type',
                'import_type',
                'serie',
                'document_date'
            ]);

            $exchange_rate = $transaction_data['exchange_rate'];
            //Reverse exchange rate and save it.
            //$transaction_data['exchange_rate'] = $transaction_data['exchange_rate'];

            $user_id = auth()->user()->id;

            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            // Edit avarage cost
            $enable_editing_avg_cost = $request->session()->get('business.enable_editing_avg_cost_from_purchase');

            //Update business exchange rate.
            // Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['total_before_tax']);

            if ($transaction_data['purchase_type'] == 'national') {
                $transaction_data['tax_amount'] = !empty($request->perception_amount) ? $this->productUtil->num_uf($request->perception_amount) : null;
            } else {
                $transaction_data['tax_amount'] = 0;
            }
            
            // $transaction_data['shipping_charges'] = $this->productUtil->num_uf($transaction_data['shipping_charges'], $currency_details)*$exchange_rate;
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total']);

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'purchase';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date']);
            $transaction_data['tax_id'] = !empty($request->contact_tax_id) ? $request->contact_tax_id : null;
            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            $transaction_data['document_date'] = $this->productUtil->uf_date($transaction_data['document_date']);

            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
            }

            $transaction = Transaction::create($transaction_data);

            $purchase_lines = [];
            $purchases = $request->input('purchases');

            foreach ($purchases as $purchase) {
                $new_purchase_line = [
                    'product_id' => $purchase['product_id'],
                    'variation_id' => $purchase['variation_id'],
                    'quantity' => $this->productUtil->num_uf($purchase['quantity']),
                    'purchase_price' => $this->productUtil->num_uf($purchase['purchase_price']),
                    'item_tax' => $this->productUtil->num_uf($purchase['tax_line_amount']),
                    'tax_id' => $request->tax_id > 0 ? $request->tax_id : null,
                    'tax_amount' => $this->productUtil->num_uf($purchase['tax_line_amount']),
                    'purchase_price_inc_tax' => $this->productUtil->num_uf($purchase['purchase_price_inc_tax']),
                    'dai_percent' => isset($purchase['dai_percent']) ? $this->productUtil->num_uf($purchase['dai_percent']) : null,
                    'dai_amount' => isset($purchase['dai_amount']) ? $this->productUtil->num_uf($purchase['dai_amount']) : null,
                    'weight_kg' => isset($purchase['product_weight']) ? $this->productUtil->num_uf($purchase['product_weight']) : null
                ];

                $purchase_lines[] = $new_purchase_line;

                // Edit product price
                // if ($enable_product_editing == 1) {
                //     //Default selling price is in base currency so no need to multiply with exchange rate.
                //     $new_purchase_line['default_sell_price'] = $this->productUtil->num_uf($purchase['default_sell_price'], $currency_details);
                //     $this->productUtil->updateProductFromPurchase($new_purchase_line);
                // }

                // Edit average cost
                if ($transaction->purchase_type == 'national'
                    && $transaction->status == 'received'
                    && $enable_editing_avg_cost == 1) {

                        $this->productUtil->updateAverageCost(
                            $purchase['variation_id'],
                            $this->productUtil->num_uf($purchase['purchase_price']),
                            $this->productUtil->num_uf($purchase['quantity'])
                        );
                }

                // Update quantity only if status is "received"
                if ($transaction_data['status'] == 'received') {
                    // Add warehouse in parameters
                    $this->productUtil->updateProductQuantity(
                        $transaction_data['location_id'],
                        $purchase['product_id'],
                        $purchase['variation_id'],
                        $purchase['quantity'],
                        0,
                        null,
                        $transaction_data['warehouse_id']
                    );
                }

                //Add Purchase payments
                $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));

                // Update payment status
                $final_total = $transaction->purchase_type == 'international' ? $transaction->total_after_expense : $transaction->final_total;

                $this->transactionUtil->updatePaymentStatus($transaction->id, $final_total);
            }

            if (!empty($purchase_lines)) {
                $transaction->purchase_lines()->createMany($purchase_lines);
            }

            if ($transaction_data['status'] == 'received') {
                # Data to create or update kardex lines
                $lines = PurchaseLine::where('transaction_id', $transaction->id)->get();

                $movement_type = MovementType::where('name', 'purchase')
                    ->where('type', 'input')
                    ->where('business_id', $business_id)
                    ->first();

                # Check if movement type is set else create it
                if (empty($movement_type)) {
                    $movement_type = MovementType::create([
                        'name' => 'purchase',
                        'type' => 'input',
                        'business_id' => $business_id
                    ]);
                }

                # Store kardex
                $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $transaction->ref_no, $lines);

                /** Generate accounting entry */
                if (config('app.business') != 'optics') {
                    $this->createAccountinEntry($transaction->id);
                }
            }

            // Save import expenses
            $expenses = $request->input('import_expenses');
            $import_expenses = [];

            if (! empty($expenses)) {
                $transaction->distributing_base = $request->input('base');
                $transaction->save();

                foreach ($expenses as $expense) {
                    $new_expense = [
                        'amount' => $expense['import_expense_amount'],
                        'import_expense_id' => $expense['import_expense_id']
                    ];
    
                    $import_expenses[] = $new_expense;
                }

                $transaction->import_expenses()->createMany($import_expenses);
            }

            // Update import data
            $this->transactionUtil->updateImportData($transaction->id);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_add_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('purchases')->with('status', $output);
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
        //$taxes = TaxRate::where('business_id', $business_id)
        //                    ->pluck('name', 'id');

        $taxes = $this->taxUtil->getTaxGroups($business_id, 'products')
            ->pluck('name', 'id');
        // $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'products');

        $purchase = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(
                'contact',
                'purchase_lines',
                'purchase_lines.product',
                'purchase_lines.tax_groups',
                'purchase_lines.variations',
                'purchase_lines.variations.product_variation',
                'location',
                'payment_lines',
                'tax'
            )
            ->first();
        $payment_methods = $this->productUtil->payment_types();

        /** Perception */
        $taxes = [];

        if($purchase->tax_id){
            $taxes [] = [
                "tax_name" => $this->taxUtil->getTaxName($purchase->tax_id),
                "tax_amount" => $purchase->tax_amount
            ];
        }

        /** VAT */
        if($purchase->purchase_lines[0]->tax_id){
            $taxes [] = [
                "tax_name" => $this->taxUtil->getTaxName($purchase->purchase_lines[0]->tax_id),
                "tax_amount" => $this->taxUtil->getTaxAmount($purchase->id, "purchase"),
            ];
        }

        // Number of decimals in sales
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $decimals_in_purchases = $product_settings['decimals_in_purchases'];

        return view('purchase.show')
            ->with(compact('taxes', 'purchase', 'payment_methods', 'taxes', 'decimals_in_purchases'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
        }

        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', [
                    'success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])
                ]);
        }

        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', [
                'success' => 0,
                'msg' => __('lang_v1.return_exist')
            ]);
        }

        $business = Business::find($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
            ->get();
        $purchase = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(
                'contact',
                'purchase_lines',
                'purchase_lines.product',
                'purchase_lines.product.unit',
                'purchase_lines.variations',
                'purchase_lines.variations.product_variation',
                'location'
            )
            ->first();
        $taxes = TaxRate::where('business_id', $business_id)
            ->get();
        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $employees_sales = Employees::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        // Gets warehouses
        $warehouses = Warehouse::forDropdown($business_id);

        // Gets document types
        $document_types = DocumentType::forDropdown($business_id);

        /** Business type */
        $business_type = $this->business_type;

        $payment_condition = ['cash' => __('order.cash'), 'credit' => __('order.credit')];
        $payment_terms = PaymentTerm::forDropdown($business_id);

        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'products');

        /** Payment conditions */
        $payment_conditions = $this->payment_conditions;

        /**Percent of tax products */
        $tax_percent_products = $this->taxUtil->getTaxPercent($purchase->purchase_lines[0]->tax_id);

        /** Products tax amount */
        $tax_amount = $this->taxUtil->getTaxAmount($purchase->id, "purchase");

        /** Get information about contact tax */
        $tax_contact = $this->contactUtil->getTaxInfo($purchase->contact->id);

        /** Validate NIT and NRC */
        $flag = Contact::where("id", $purchase->contact_id)
            ->whereNotNull("nit")
            ->whereNotNull("tax_number")
            ->count();

        /** Accounting account for suppliers/providers */
        $business = Business::find($business_id);
        $supplier_account = "";
        if($business->accounting_supplier_id){
            $supplier_account =
                Catalogue::where("status", 1)
                    ->where("id", $business->accounting_supplier_id)
                    ->value("code");
        }

        $flag = $flag > 0 ? 1 : 0;

        // Determine if purchase has import costs
        $import_expenses = TransactionHasImportExpense::leftJoin('import_expenses as ie', 'ie.id', 'transaction_has_import_expenses.import_expense_id')
            ->where('transaction_id', $purchase->id)
            ->select(
                'transaction_has_import_expenses.id',
                'ie.id as import_expense_id',
                'ie.name',
                'transaction_has_import_expenses.amount'
            )
            ->get();

        $has_import_expenses = $import_expenses->count() > 0 ? 1 : 0;

        $purchase_lines = PurchaseLine::where('transaction_id', $purchase->id)->get();
        $other_import_expenses = $purchase_lines->sum('import_expense_amount');
        $dai_amount = $purchase_lines->sum('dai_amount');
        $iva_amount = $purchase_lines->sum('tax_amount');

        // Disabled if finished
        $apportionment = ApportionmentHasTransaction::join('apportionments as a', 'a.id', 'apportionment_has_transactions.apportionment_id')
            ->where('apportionment_has_transactions.transaction_id', $id)
            ->first();

        $disabled = '';
        $readonly = '';

        if (! empty($apportionment)) {
            $disabled = $apportionment->is_finished == 1 ? 'disabled' : '';
            $readonly = $apportionment->is_finished == 1 ? 'readonly' : '';
        }

        $countries = Country::select('id', 'name')
                ->where('business_id', $business_id)
                ->pluck('name', 'id');

        $business_debt_to_pay_type = $business->debt_to_pay_type;

        // Number of decimals in sales
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $decimals_in_purchases = $product_settings['decimals_in_purchases'];

        return view('purchase.edit')
            ->with(compact(
                'taxes',
                'purchase',
                'taxes',
                'orderStatuses',
                'business_locations',
                'business',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'employees_sales',
                'types',
                'shortcuts',
                'warehouses',
                'document_types',
                'business_type',
                'tax_groups',
                'payment_conditions',
                'payment_condition',
                'payment_terms',
                'tax_percent_products',
                'tax_amount',
                'tax_contact',
                'supplier_account',
                'flag',
                'import_expenses',
                'has_import_expenses',
                'other_import_expenses',
                'dai_amount',
                'iva_amount',
                'disabled',
                'business_debt_to_pay_type',
                'countries',
                'readonly',
                'decimals_in_purchases'
            ));
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
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        // Set maximum PHP execution time
        ini_set('max_execution_time', 0);

        if(!auth()->user()->can('is_close_book') &&
            $this->transactionUtil->isClosed($request->input('transaction_date')) > 0){
            $output = [
                'success' => 0,
                'msg' => __('purchase.month_closed')
            ];
            return redirect('purchases')->with('status', $output);
        }

        try {
            $transaction = Transaction::findOrFail($id);

            // Clone record before action
            $transaction_old = clone $transaction;

            // Validate document size
            $request->validate([
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000),
                'payment_condition' => 'required',
            ]);

            $transaction = Transaction::findOrFail($id);
            $before_status = $transaction->status;
            $business_id = auth()->user()->business_id;
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            // Add document type
            $update_data = $request->only([
                'ref_no',
                'status',
                'contact_id',
                'transaction_date',
                'total_before_tax',
                'location_id',
                'discount_type',
                'discount_amount',
                'tax_id',
                'tax_amount',
                'final_total',
                'additional_notes',
                'exchange_rate',
                'warehouse_id',
                'payment_condition',
                'payment_term_id',
                'document_types_id',
                'import_type',
                'serie',
                'document_date'
            ]);

            $exchange_rate = $update_data['exchange_rate'];

            //Reverse exchage rate and save
            //$update_data['exchange_rate'] = number_format(1 / $update_data['exchange_rate'], 2);

            $update_data['transaction_date'] = $this->productUtil->uf_date($update_data['transaction_date']);

            //unformat input values
            $update_data['total_before_tax'] = $this->productUtil->num_uf($update_data['total_before_tax'], $currency_details) * $exchange_rate;

            $update_data['tax_amount'] = !empty($request->perception_amount) ? $this->productUtil->num_uf($request->perception_amount) : null;
            // $update_data['shipping_charges'] = $this->productUtil->num_uf($update_data['shipping_charges'], $currency_details) * $exchange_rate;
            $update_data['final_total'] = $this->productUtil->num_uf($update_data['final_total']);
            $update_data['tax_id'] = !empty($request->contact_tax_id) ? $request->contact_tax_id : null;
            //unformat input values ends

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $update_data['document'] = $document_name;
            }

            $update_data['document_date'] = $this->productUtil->uf_date($update_data['document_date']);

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            if ($transaction->status == 'received') {
                # Data to create or update kardex lines
                $lines_before = PurchaseLine::where('transaction_id', $transaction->id)->get();
            }

            //Update transaction payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id);

            // Store binnacle
            $reference = ! empty($transaction->document_type) ? $transaction->document_type->short_name . ' ' . $transaction->ref_no : $transaction->ref_no;

            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'update',
                $reference,
                $transaction_old,
                $transaction
            );

            $apportionment = ApportionmentHasTransaction::join('apportionments as a', 'a.id', 'apportionment_has_transactions.apportionment_id')
                ->where('apportionment_has_transactions.transaction_id', $id)
                ->first();

            $apportionment_validation = ! empty($apportionment) ? $apportionment->is_finished : 0;

            if ($apportionment_validation == 0) {
                $purchases = $request->input('purchases');

                $updated_purchase_lines = [];

                $updated_purchase_line_ids = [0];

                //P => R (All items quantity update)
                //R => P (Existing minus)
                //R => R (Exisitng quantity update, New product add, minus deleted products)

                foreach ($purchases as $purchase) {
                    //update existing purchase line
                    if (isset($purchase['purchase_line_id'])) {
                        $purchase_line = PurchaseLine::findOrFail($purchase['purchase_line_id']);
                        $updated_purchase_line_ids[] = $purchase_line->id;
                        $old_qty = $this->productUtil->num_f($purchase_line->quantity);

                        //Update quantity for existing products
                        if ($before_status == 'received' && $transaction->status == 'received') {
                            //if status received update existing quantity
                            $this->productUtil->updateProductQuantity($transaction->location_id, $purchase['product_id'], $purchase['variation_id'], $purchase['quantity'], $old_qty, $currency_details, $transaction->warehouse_id);
                        } elseif ($before_status == 'received' && $transaction->status != 'received') {
                            //decrease quantity only if status changed from received to not received
                            $this->productUtil->decreaseProductQuantity(
                                $purchase['product_id'],
                                $purchase['variation_id'],
                                $transaction->location_id,
                                $purchase_line->quantity,
                                0,
                                $transaction->warehouse_id
                            );
                        } elseif ($before_status != 'received' && $transaction->status == 'received') {
                            $this->productUtil->updateProductQuantity($transaction->location_id, $purchase['product_id'], $purchase['variation_id'], $purchase['quantity'], 0, $currency_details, $transaction->warehouse_id);
                        }
                    } else {
                        //create newly added purchase lines
                        $purchase_line = new PurchaseLine();
                        $purchase_line->product_id = $purchase['product_id'];
                        $purchase_line->variation_id = $purchase['variation_id'];

                        //Increase quantity only if status is received
                        if ($transaction->status == 'received') {
                            $this->productUtil->updateProductQuantity(
                                $transaction->location_id,
                                $purchase['product_id'],
                                $purchase['variation_id'],
                                $purchase['quantity'],
                                0,
                                $currency_details,
                                $transaction->warehouse_id
                            );
                        }
                    }

                    $purchase_line->quantity = $this->productUtil->num_uf($purchase['quantity']);
                    // $purchase_line->pp_without_discount = $this->productUtil->num_uf($purchase['pp_without_discount'], $currency_details)*$exchange_rate;
                    // $purchase_line->discount_percent = $this->productUtil->num_uf($purchase['discount_percent'], $currency_details);
                    $purchase_line->purchase_price = $this->productUtil->num_uf($purchase['purchase_price']);
                    $purchase_line->tax_id = $request->tax_id > 0 ? $request->tax_id : null;
                    $purchase_line->tax_amount = $this->productUtil->num_uf($purchase['tax_line_amount']);
                    $purchase_line->item_tax = $this->productUtil->num_uf($purchase['tax_line_amount']);
                    $purchase_line->purchase_price_inc_tax = $this->productUtil->num_uf($purchase['purchase_price_inc_tax']);

                    $purchase_line->dai_percent = isset($purchase['dai_percent']) ? $this->productUtil->num_uf($purchase['dai_percent']) : null;
                    $purchase_line->dai_amount = isset($purchase['dai_amount']) ? $this->productUtil->num_uf($purchase['dai_amount']) : null;
                    $purchase_line->weight_kg = isset($purchase['product_weight']) ? $this->productUtil->num_uf($purchase['product_weight']) : null;

                    $purchase_line->initial_purchase_price = ! is_null($purchase_line->initial_purchase_price) ? $this->productUtil->num_uf($purchase['purchase_price']) : null;

                    $updated_purchase_lines[] = $purchase_line;
                }

                //unset deleted purchase lines
                $delete_purchase_line_ids = [];
                $delete_variation_ids = [];

                if (!empty($updated_purchase_line_ids)) {
                    $delete_purchase_lines = PurchaseLine::where('transaction_id', $transaction->id)
                        ->whereNotIn('id', $updated_purchase_line_ids)
                        ->get();

                    if ($delete_purchase_lines->count()) {
                        foreach ($delete_purchase_lines as $delete_purchase_line) {
                            $delete_purchase_line_ids[] = $delete_purchase_line->id;
                            $delete_variation_ids[] = $delete_purchase_line->variation_id;

                            //decrease deleted only if previous status was received
                            if ($before_status == 'received') {
                                $this->productUtil->decreaseProductQuantity(
                                    $delete_purchase_line->product_id,
                                    $delete_purchase_line->variation_id,
                                    $transaction->location_id,
                                    $delete_purchase_line->quantity,
                                    0,
                                    $transaction->warehouse_id
                                );
                            }
                        }
                        //Delete deleted purchase lines
                        PurchaseLine::where('transaction_id', $transaction->id)
                            ->whereIn('id', $delete_purchase_line_ids)
                            ->delete();
                    }
                }

                //update purchase lines
                if (!empty($updated_purchase_lines)) {
                    $transaction->purchase_lines()->saveMany($updated_purchase_lines);
                }

                // Edit avarage cost
                $enable_editing_avg_cost = $request->session()->get('business.enable_editing_avg_cost_from_purchase');

                if ($transaction->purchase_type == 'national'
                    && $transaction->status == 'received'
                    && $enable_editing_avg_cost == 1) {

                    $variation_ids = PurchaseLine::where('transaction_id', $transaction->id)->pluck('variation_id');
                        
                    foreach ($variation_ids as $variation_id) {
                        $this->productUtil->recalculateProductCost($variation_id);
                    }

                    if (! empty($delete_variation_ids)) {
                        foreach ($delete_variation_ids as $variation_id) {
                            $this->productUtil->recalculateProductCost($variation_id);
                        }
                    }
                }
            }

            if ($transaction->status == 'received') {
                # Data to create or update kardex lines
                $lines = PurchaseLine::where('transaction_id', $transaction->id)->get();

                $movement_type = MovementType::where('name', 'purchase')
                    ->where('type', 'input')
                    ->where('business_id', $business_id)
                    ->first();

                # Check if movement type is set else create it
                if (empty($movement_type)) {
                    $movement_type = MovementType::create([
                        'name' => 'purchase',
                        'type' => 'input',
                        'business_id' => $business_id
                    ]);
                }

                # Store kardex lines
                $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $transaction->ref_no, $lines, $lines_before);
            } else {
                # Delete kardex lines
                $this->transactionUtil->deleteKardexByTransaction($transaction->id);
            }

            if ($apportionment_validation == 0) {
                //Update mapping of purchase & Sell.
                $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);

                // Save import expenses
                $expenses = $request->input('import_expenses');

                if (! empty($expenses)) {
                    $transaction->distributing_base = $request->input('base');
                    $transaction->save();

                    $saved_ids = [];

                    foreach ($expenses as $expense) {
                        if (isset($expense['id'])) {
                            $updated_expense = TransactionHasImportExpense::find($expense['id']);
                            $updated_expense->amount = $expense['import_expense_amount'];
                            $updated_expense->save();

                            $saved_ids[] = $expense['id'];

                        } else {
                            $new_expense = TransactionHasImportExpense::create([
                                'amount' => $expense['import_expense_amount'],
                                'import_expense_id' => $expense['import_expense_id'],
                                'transaction_id' => $transaction->id
                            ]);

                            $saved_ids[] = $new_expense->id;
                        }
                    }

                    DB::table('transaction_has_import_expenses')
                        ->where('transaction_id', $transaction->id)
                        ->whereNotIn('id', $saved_ids)
                        ->delete();

                } else {
                    DB::table('transaction_has_import_expenses')
                        ->where('transaction_id', $transaction->id)
                        ->delete();
                }
            }

            // Update import data
            $this->transactionUtil->updateImportData($transaction->id);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_update_success')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            
            return back()->with('status', $output);
        }

        return redirect('purchases')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');

                //Check if return exist then not allowed
                if ($this->transactionUtil->isReturnExist($id)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.return_exist')
                    ];
                    return $output;
                }

                $transaction = Transaction::where('id', $id)
                    ->where('business_id', $business_id)
                    ->with(['purchase_lines'])
                    ->first();

                // Clone record before action
                $transaction_old = clone $transaction;

                //Check if lot numbers from the purchase is selected in sale
                if (request()->session()->get('business.enable_lot_number') == 1 && $this->transactionUtil->isLotUsed($transaction)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.lot_numbers_are_used_in_sale')
                    ];
                    return $output;
                }

                $delete_purchase_lines = $transaction->purchase_lines;

                DB::beginTransaction();

                $variation_ids = PurchaseLine::where('transaction_id', $transaction->id)->pluck('variation_id');

                $transaction_status = $transaction->status;
                if ($transaction_status != 'received') {
                    $transaction->delete();
                } else {
                    //Delete purchase lines first
                    $delete_purchase_line_ids = [];
                    foreach ($delete_purchase_lines as $purchase_line) {
                        $delete_purchase_line_ids[] = $purchase_line->id;
                        $this->productUtil->decreaseProductQuantity(
                            $purchase_line->product_id,
                            $purchase_line->variation_id,
                            $transaction->location_id,
                            $purchase_line->quantity
                        );
                    }
                    PurchaseLine::where('transaction_id', $transaction->id)
                        ->whereIn('id', $delete_purchase_line_ids)
                        ->delete();

                    # Delete kardex lines
                    $this->transactionUtil->deleteKardexByTransaction($transaction->id);

                    # Delete kardex lines of purchase return
                    $return_transaction = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase_return')
                        ->where('return_parent_id', $transaction->id)
                        ->first();

                    if (!empty($return_transaction)) {
                        $this->transactionUtil->deleteKardexByTransaction($return_transaction->id);
                    }

                    //Update mapping of purchase & Sell.
                    $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($transaction_status, $transaction, $delete_purchase_lines);
                }

                // Edit avarage cost
                $enable_editing_avg_cost = request()->session()->get('business.enable_editing_avg_cost_from_purchase');

                if ($transaction->purchase_type == 'national' && $enable_editing_avg_cost == 1) {
                    foreach ($variation_ids as $variation_id) {
                        $this->productUtil->recalculateProductCost($variation_id);
                    }
                }

                //Delete Transaction
                $transaction->delete();

                // Store binnacle
                $reference = ! empty($transaction_old->document_type) ? $transaction_old->document_type->short_name . ' ' . $transaction_old->ref_no : $transaction_old->ref_no;

                $this->transactionUtil->registerBinnacle(
                    $this->module_name,
                    'delete',
                    $reference,
                    $transaction_old
                );

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.purchase_delete_success')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Retrieves supliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query =
                Contact::leftJoin('tax_rate_tax_group AS trtg', 'contacts.tax_group_id', 'trtg.tax_group_id')
                    ->leftJoin('tax_rates as tr', 'trtg.tax_rate_id', 'tr.id')
                    ->where('contacts.business_id', $business_id)
                    ->whereIn("contacts.type", ["supplier", "both"]);

            $suppliers = $query->where(function ($query) use ($term) {
                $query->where('contacts.name', 'like', '%' . $term . '%')
                    ->orWhere('contacts.supplier_business_name', 'like', '%' . $term . '%')
                    ->orWhere('contacts.contact_id', 'like', '%' . $term . '%');
            })
                ->select('contacts.id',
                    'contacts.name as text',
                    'contacts.supplier_business_name as business_name',
                    'contacts.contact_id',
                    'contacts.tax_group_id',
                    'tr.min_amount',
                    'tr.max_amount',
                    'contacts.tax_group_id as perception_percent')
                ->get();

            foreach($suppliers as $sp){
                if($sp->tax_group_id) {
                    $sp->perception_percent = $this->taxUtil->getTaxPercent($sp->tax_group_id);
                }
            }

            return json_encode($suppliers);
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        if (request()->ajax()) {
            $term = request()->term;

            $check_enable_stock = true;
            if (isset(request()->check_enable_stock)) {
                $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
            }

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = auth()->user()->business_id;
            $q = Product::leftJoin('variations', 'products.id', 'variations.product_id')
                ->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                })
                ->where('business_id', $business_id)
                ->whereIn('clasification', ['product', 'material'])
                ->where('status', 'active')
                ->whereNull('variations.deleted_at')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku'
                )
                ->groupBy('variation_id')
                ->limit(50);

            if ($check_enable_stock) {
                $q->where('enable_stock', 1);
            }
            $products = $q->get();

            $products_array = [];
            foreach ($products as $product) {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['variations'][]
                    = [
                        'variation_id' => $product->variation_id,
                        'variation_name' => $product->variation,
                        'sub_sku' => $product->sub_sku
                    ];
            }

            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (!empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    if ($no_of_records > 1 && $value['type'] != 'single') {
                        $result[] = [
                            'id' => $i,
                            'text' => $value['name'] . ' - ' . $value['sku'],
                            'variation_id' => 0,
                            'product_id' => $key
                        ];
                    }
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            $text = $text . ' (' . $variation['variation_name'] . ')';
                        }
                        $i++;
                        $result[] = [
                            'id' => $i,
                            'text' => $text . ' - ' . $variation['sub_sku'],
                            'product_id' => $key,
                            'variation_id' => $variation['variation_id'],
                        ];
                    }
                    $i++;
                }
            }

            return json_encode($result);
        }
    }

    /**
     * Get debts purchases
     */
    public function getDebtPurchases(){
        if(request()->ajax()){
            $term = request()->input('q', '');
            $business_id = request()->user()->business_id;
            $supplier_id = request()->input('supplier_id', '');
            $location_id = request()->input('location_id', '');

            $purchases = Transaction::join("contacts as c", "transactions.contact_id", "c.id")
                ->join("business_locations as bl", "transactions.location_id", "bl.id")
                ->join("document_types as dt", "transactions.document_types_id", "dt.id")
                ->where("transactions.business_id", $business_id)
                ->where("transactions.type", "purchase")
                ->whereIn("transactions.payment_status", ["partial", "due"])
                ->where("c.id", $supplier_id)
                ->where("bl.id", $location_id);

           if (!empty($term)) {
                $purchases->where(function ($query) use ($term) {
                    $query->where("transactions.ref_no", 'like', '%' . $term . '%')
                        ->orWhere("transactions.final_total", 'like', '%' . $term . '%');
                });
            }

            $purchases = $purchases->select(
                DB::raw("CONCAT(transactions.ref_no, ' | ', DATE_FORMAT(transactions.transaction_date, '%d/%m/%Y'), ' | $', ROUND(transactions.final_total, 2)) AS text"),
                "transactions.id"
            )->get();
            
            return $purchases;
        }   
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseEntryRow()
    {
        if (request()->ajax()) {
            $product_id = request()->input('product_id');
            $variation_id = request()->input('variation_id');
            $purchase_type = request()->input('purchase_type', 'national');
            $business_id = request()->session()->get('user.business_id');

            $hide_tax = 'hide';
            if (request()->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            if (!empty($product_id)) {
                $row_count = request()->input('row_count');
                $product = Product::where('id', $product_id)
                    ->with(['unit'])
                    ->first();

                $query = Variation::where('product_id', $product_id)
                    ->with(['product_variation']);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations =  $query->get();
                $taxes = TaxRate::where('business_id', $business_id)
                    ->get();

                // Number of decimals in purchases
                $business = Business::find($business_id);
                $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
                $decimals_in_purchases = $product_settings['decimals_in_purchases'];

                return view('purchase.partials.purchase_entry_row', compact(
                    'product',
                    'variations',
                    'row_count',
                    'variation_id',
                    'taxes',
                    'currency_details',
                    'hide_tax',
                    'purchase_type',
                    'decimals_in_purchases'
                ));
            }
        }
    }


    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkRefNumber()
    {
        $business_id = request()->session()->get('user.business_id');
        $ref_no = request()->input('ref_no', null);
        $document_type_id = request()->input('document$document_type_id', null);
        $contact_id = request()->input('contact_id', null);
        $purchase_id = request()->input('purchase_id', null);

        $count = 0;
        $query = Transaction::where('business_id', $business_id);
        if (!empty($ref_no)) {
            //check in transactions table
            $query = $query->where('ref_no', trim($ref_no));
        }
        if (!empty($document_type_id)) {
            $query = $query->where('document_types_id', [$document_type_id]);
        }
        if (!empty($contact_id)) {
            $query = $query->where('contact_id', [$contact_id]);
        }
        if (!empty($purchase_id)) {
            $query = $query->whereNotIn('id', [$purchase_id]);
        }

        $count = $query->count();
        
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id, $type = "")
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $taxes = $this->taxUtil->getTaxGroups($business_id, 'products')
                ->pluck('name', 'id');

            $purchase = Transaction::where('business_id', $business_id)
                ->where('id', $id)
                ->with(
                    'contact',
                    'purchase_lines',
                    'purchase_lines.product',
                    'purchase_lines.variations',
                    'purchase_lines.variations.product_variation',
                    'location',
                    'payment_lines'
                )
                ->first();
            $payment_methods = $this->productUtil->payment_types();
            $percent = 0;
            $tax_id_group = 0;
            $default_tax = 3;
            $name_tax_purchase = "";
            $flag = false;

            /**Percent of tax products */

            if ($purchase->tax_id != null) {
                /**
                 * If the traction has a tax id then it will be verified if the id is equal to 3 or 6 if not a default id is added.
                 * This section was thought so that before purchases could be saved with tax id that were not in the taxGroups
                 */

                if (($purchase->tax_id == 3 || $purchase->tax_id == 6)) {
                    $tax_id_group = $purchase->tax_id;
                } else {
                    $tax_id_group = $default_tax;
                }
            } else {
                /**
                 * All the purchase lines that the transaction has are brought
                 * The number of lines that have the tax id in null are counted, also those that are not null are counted
                 */
                $percent_lines = PurchaseLine::where('transaction_id', $purchase->id)->select('tax_id')->get();
                $max_null = PurchaseLine::where('transaction_id', $purchase->id)->where('tax_id', null)->count();
                $max_not_null = PurchaseLine::where('transaction_id', $purchase->id)->where('tax_id', '<>', null)->count();

                if ($max_null > $max_not_null) {
                    $flag = true;
                } else {
                    foreach ($percent_lines as $p) {
                        if ($p->tax_id == 3 || $p->tax_id == 6) {
                            // Enters if the tax id is different from 1,2,4 and stops the loop when it finds the first one
                            $tax_id_group = $p->tax_id;
                            break;
                        }
                    }
                }
            }

            /**
             * If the flag is true then it is assumed that the purchase does not have taxes and the percentage is assigned to 0
             */
            if ($flag) {
                $percent = 0;
            } else if ((!empty($tax_id_group))) {
                $percent = $this->taxUtil->getTaxes($tax_id_group);
            } else {
                $percent = $this->taxUtil->getTaxes($default_tax);
            }

            //The name of the tax is searched.
            foreach ($taxes as $key =>  $t) {
                if ((!is_null($tax_id_group) || !empty($tax_id_group))) {
                    if ($key == $tax_id_group) {
                        $name_tax_purchase = $t;
                    }
                } else {
                    if ($key == $default_tax) {
                        $name_tax_purchase = $t;
                    }
                }
            }

            // Perception
            $taxes = [];

            if($purchase->tax_id) {
                $taxes[] = [
                    "tax_name" => $this->taxUtil->getTaxName($purchase->tax_id),
                    "tax_amount" => $purchase->tax_amount
                ];
            }

            // VAT
            if($purchase->purchase_lines[0]->tax_id) {
                $taxes[] = [
                    "tax_name" => $this->taxUtil->getTaxName($purchase->purchase_lines[0]->tax_id),
                    "tax_amount" => $this->taxUtil->getTaxAmount($purchase->id, "purchase")
                ];
            }

            // Number of decimals in sales
            $business = Business::find($business_id);
            $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
            $decimals_in_purchases = $product_settings['decimals_in_purchases'];

            if (!empty($type)) {
                if ($type == 'import') {
                    $output = ['success' => 1, 'receipt' => []];
                    $output['receipt']['html_content'] = view('purchase.international.show_details', compact('taxes', 'purchase', 'payment_methods', 'decimals_in_purchases'))->render();
                } else {
                    $output = ['success' => 1, 'receipt' => []];
                    $output['receipt']['html_content'] = view('purchase.partials.show_details', compact('taxes', 'purchase', 'payment_methods', 'name_tax_purchase', 'percent', 'decimals_in_purchases'))->render();
                }
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Shows import option for purchases.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getImportPurchases()
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = auth()->user()->business_id;
        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'products');

        $zip_loaded = extension_loaded('zip') ? true : false;

        $errors = [];

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => __('messages.install_enable_zip')
            ];

            return view('purchase.import')
                ->with([
                    'notification' => $output,
                    'errors' => $errors,
                    'tax_groups' => $tax_groups
                ]);
        } else {
            return view('purchase.import', compact('tax_groups', 'errors'));
        }
    }

    /**
     * Imports purchases.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postImportPurchases(Request $request)
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $exception = 0;
        $business_id = auth()->user()->business_id;
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'products');


        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            // Errors list
            $error_msg = [];

            // General information about the purchase
            $general_info = [];

            // Purchases lines
            $purchases = [];

            // There are/aren't unregistered products
            $created_product = true;

            // Show error form (false) or process purchase form (true)
            $status = true;


            // Tax id
            $tax_id = !empty($request->tax_id) ? $request->tax_id : null;

            // return percent tax
            $tax_percent = $tax_id != null ? $this->taxUtil->getTaxPercent($tax_id) : 0;

            //discount type and amount
            $discount_type = $request->discount_type;
            $discount_amount = $request->discount_amount;

            $business_id = $request->session()->get('user.business_id');

            $user_id = $request->session()->get('user.id');

            if ($request->hasFile('purchases_csv')) {
                // Validate csv
                $file = $request->file('purchases_csv');

                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::TSV)[0];
                unset($imported_data[0]);

                // To get general information about the purchase
                $first_iteration = true;

                // Options
                $status_options = ['received', 'pending', 'ordered'];
                $discount_options = ['fixed', 'percentage'];

                $total_before_tax = 0;
                $discount_general = 0;
                $tax_amount = 0;
                $shipping_charges = 0;
                $final_total = 0;

                // Process file
                foreach ($imported_data as $key => $value) {
                    // Check if 11 no. of columns exists

                    if (count($value) != 11) {
                        $error_line = [
                            'row' => 'N/A',
                            'msg' => __('purchase.number_of_columns_mismatch') . ' (' . $value . ')'
                        ];
                        array_push($error_msg, $error_line);
                    }

                    // Row number
                    $row_no = $key + 1;

                    // General info
                    if ($first_iteration) {
                        // ---------- CONTACT_ID ----------
                        $supplier = trim($value[4]);

                        // Check empty
                        if (empty($supplier)) {
                            $error_line = [
                                'row' => $row_no,
                                'msg' => __('purchase.empty_supplier_field')
                            ];

                            array_push($error_msg, $error_line);

                            // Check existence
                        } else {
                            $contact = Contact::whereRaw('upper(contact_id) = upper("' . $supplier . '")')
                                ->where('type', 'supplier')
                                ->where('business_id', $business_id)
                                ->first();

                            if (empty($contact)) {
                                $error_line = [
                                    'row' => $row_no,
                                    'msg' => __('purchase.provider_does_not_exist')
                                ];

                                array_push($error_msg, $error_line);
                            } else {
                                $general_info['contact_id'] = $contact->id;
                            }
                        }

                        // ---------- DOCUMENT_TYPES_ID ----------
                        $document_type_code = trim($value[5]);

                        // Check empty
                        if (empty($document_type_code)) {
                            $error_line = [
                                'row' => $row_no,
                                'msg' => __('purchase.empty_document_type_field')
                            ];

                            array_push($error_msg, $error_line);

                            // Check existence
                        } else {
                            $document_type = DocumentType::whereRaw('upper(short_name) = upper("' . $document_type_code . '")')
                                ->where('is_active', 1)
                                ->where('business_id', $business_id)
                                ->first();

                            if (empty($document_type)) {
                                $error_line = [
                                    'row' => $row_no,
                                    'msg' => __('purchase.document_type_does_not_exist')
                                ];

                                array_push($error_msg, $error_line);
                            } else {
                                $general_info['document_types_id'] = $document_type->id;
                            }
                        }

                        // ---------- REF_NO ----------
                        $general_info['ref_no'] = trim($value[6]);

                        // ---------- TRANSACTION_DATE ----------
                        $general_info['transaction_date'] = trim($value[7]);
                        // dd($general_info['transaction_date']);

                        // $separated_date = explode("/", trim($value[7])); 
                        // Check empty
                        if (empty($general_info['transaction_date'])) {
                            $error_line = [
                                'row' => $row_no,
                                'msg' => __('purchase.empty_purchase_date_field')
                            ];

                            array_push($error_msg, $error_line);
                        } else {
                            // $general_info['transaction_date'] = \Carbon::createFromFormat('Y-m-d', $general_info['transaction_date'])->format('Y-m-d');
                            $general_info['transaction_date'] = \Carbon::createFromFormat('Y-m-d', $general_info['transaction_date'])->format('Y-m-d');
                        }

                        // ---------- STATUS ----------
                        $general_info['status'] = trim($value[8]);

                        // Check empty
                        if (empty($general_info['status'])) {
                            $error_line = [
                                'row' => $row_no,
                                'msg' => __('purchase.empty_purchase_status_field')
                            ];

                            array_push($error_msg, $error_line);

                            // Check status valid
                        } else {
                            if (!in_array($general_info['status'], $status_options)) {
                                $error_line = [
                                    'row' => $row_no,
                                    'msg' => __('purchase.purchase_status_is_invalid')
                                ];

                                array_push($error_msg, $error_line);
                            }
                        }

                        // ---------- WAREHOUSE_ID ----------
                        $warehouse_code = trim($value[9]);

                        // Check empty
                        if (empty($warehouse_code)) {
                            $error_line = [
                                'row' => $row_no,
                                'msg' => __('purchase.empty_warehouse_field')
                            ];

                            array_push($error_msg, $error_line);

                            // Check existence
                        } else {
                            $warehouse = Warehouse::whereRaw('upper(code) = upper("' . $warehouse_code . '")')
                                ->where('status', 1)
                                ->where('business_id', $business_id)
                                ->first();

                            if (empty($warehouse)) {
                                $error_line = [
                                    'row' => $row_no,
                                    'msg' => __('purchase.warehouse_does_not_exist')
                                ];

                                array_push($error_msg, $error_line);
                            } else {
                                $general_info['warehouse_id'] = $warehouse->id;

                                $location = BusinessLocation::find($warehouse->business_location_id);
                                $general_info['location_id'] = $location->id;
                            }
                        }

                        // ---------- ADDITIONAL_NOTES ----------
                        $general_info['additional_notes'] = trim($value[10]);

                        $first_iteration = false;
                    }

                    // ---------- PURCHASE LINES ----------
                    $purchase = []; //Comienzo :(

                    // Cost with tax
                    $cost_with_tax = 0;

                    // ---------- PRODUCT DATA ----------
                    $purchase['sku'] = trim($value[0]);
                    $purchase['product'] = trim($value[1]);
                    $purchase['product_id'] = null;
                    $purchase['variation_id'] = null;

                    // Check empty
                    if (empty($purchase['sku'])) {
                        $error_line = [
                            'row' => $row_no,
                            'msg' => __('purchase.empty_sku_field')
                        ];

                        array_push($error_msg, $error_line);

                        // Check existence
                    } else {
                        $product = Product::whereRaw('upper(sku) = upper("' .  $purchase['sku'] . '")')
                            ->where('status', 'active')
                            ->where('business_id', $business_id)
                            ->first();

                        if (empty($product)) {
                            $error_line = [
                                'row' => $row_no,
                                'msg' => __('purchase.product_does_not_exist')
                            ];

                            array_push($error_msg, $error_line);

                            if (empty($purchase['product'])) {
                                $created_product = false;
                            }

                            // Check repetition
                        } else {
                            $purchase['product_id'] = $product->id;

                            foreach ($purchases as $p) {
                                if ($p['sku'] == $product->sku) {
                                    $error_line = [
                                        'row' => $row_no,
                                        'msg' => __('purchase.repeated_product')
                                    ];

                                    array_push($error_msg, $error_line);
                                }
                            }

                            // Get cost with tax
                            $variation = Variation::where('product_id', $product->id)
                                ->where('sub_sku', $product->sku)
                                ->first();

                            $purchase['variation_id'] = $variation->id;

                            $cost_with_tax = $variation->dpp_inc_tax;
                        }
                    }

                    // ---------- QUANTITY ----------
                    $purchase['quantity'] = trim($value[2]);

                    // Check empty
                    if (empty($purchase['quantity'])) {
                        $error_line = [
                            'row' => $row_no,
                            'msg' => __('purchase.empty_purchase_quantity_field')
                        ];

                        array_push($error_msg, $error_line);
                    } else {
                        if ($purchase['quantity'] < 0) {
                            $error_line = [
                                'row' => $row_no,
                                'msg' => __('purchase.purchase_quantity_greater_than_zero')
                            ];

                            array_push($error_msg, $error_line);
                        } else {
                            $purchase['quantity'] = $this->productUtil->num_uf($purchase['quantity']);
                        }
                    }

                    //calcular las lineas de los producutos
                    $purchase['purchase_price'] = trim($value[3]);


                    if (empty($purchase['purchase_price'])) {
                        $error_line = [
                            'row' => $row_no,
                            'msg' => __('purchase.purchase_required')
                        ];

                        array_push($error_msg, $error_line);
                    } else {
                        if ($purchase['purchase_price'] <= 0) {
                            $error_line = [
                                'row' => $row_no,
                                'msg' => __('purchase.purchase_greater_than_zero')
                            ];
                            array_push($error_msg, $error_line);
                        } else {
                            $purchase['purchase_price'] = $this->productUtil->num_uf($purchase['purchase_price']);
                        }
                    }


                    // se calcula el monto
                    $total_amount = $this->productUtil->num_uf(($purchase['quantity'] * $purchase['purchase_price']));

                    //inpuesto por linea de producto
                    $tax_line_amount = $this->productUtil->num_uf($total_amount *  $tax_percent);

                    $purchase_price = $this->productUtil->num_uf($purchase['purchase_price']);

                    //se calcula el precio incluyendo impuestos
                    $purchase_price_inc_tax = $this->productUtil->num_uf(($tax_line_amount + $purchase_price));
                    $purchase['tax_id'] = $tax_id;
                    $purchase['tax_amount'] = $tax_line_amount;
                    $purchase['purchase_price_inc_tax'] = $purchase_price_inc_tax;

                    // Calculate total_before_tax
                    $total_before_tax += $total_amount;
                    // dd($purchase);
                    array_push($purchases, $purchase);
                } //endforeach

                // dd($purchase, $general_info);
                $general_info['discount_type'] = $discount_type;
                $general_info['discount_amount_total'] = 0;

                //calculando el descuento
                if ($discount_type == 'percentage') {
                    $general_info['discount_amount_total'] = ($total_before_tax * $discount_amount) / 100;
                } elseif ($discount_type == 'fixed') {
                    $general_info['discount_amount_total'] = $discount_amount;
                } else {
                    $general_info['discount_amount_total'] = 0;
                }

                // Calculate tax_amount
                // $tax_amount = $total_before_tax * $tax_percent;
                //se calcula el impuesto en general
                $tax_amount = ($total_before_tax - $general_info['discount_amount_total']) * $tax_percent;
                $final_total = ($total_before_tax + $tax_amount) - $general_info['discount_amount_total'];

                $general_info['total_before_tax'] = $total_before_tax;
                $general_info['tax_amount'] = $tax_amount;
                $general_info['final_total'] = $final_total;
                $general_info['tax_id'] = $tax_id;
                $general_info['discount_amount'] = $discount_amount;
                $discount_general = $general_info['discount_amount_total'];
            }
            // Business
            $status = [
                'success' => 1,
                'msg' => __('purchase.successful_verified_purchase')
            ];
        } catch (\Exception $e) {
            $exception = 1;

            $error_line = [
                'row' => 'N/A',
                'msg' => $e->getMessage()
            ];

            array_push($error_msg, $error_line);

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $status = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        session([
            'general_info' => $general_info,
            'purchases' => $purchases
        ]);

        $errors = $error_msg;

        if (count($error_msg) == 0 && $exception == 0) {
            $flag = true;
        } else {
            $flag = false;
        }


        return view('purchase.import')
            ->with(compact(
                'errors',
                'created_product',
                'status',
                'total_before_tax',
                'tax_amount',
                'final_total',
                'discount_general',
                'shipping_charges',
                'flag',
                'exception',
                'tax_groups'
            ));
    }



    public function importPurchases(Request $request)
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            // Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }

            // Purchase lines
            $purchases = session('purchases');

            // Transaction data
            $transaction_data = session('general_info');
            unset($transaction_data['discount_amount_total']);
            // $exchange_rate = $transaction_data['exchange_rate'];

            $user_id = auth()->user()->id;

            $enable_editing_avg_cost = $request->session()->get('business.enable_editing_avg_cost_from_purchase');

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'purchase';
            $transaction_data['payment_status'] = 'due';

            DB::beginTransaction();

            // Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);

            // Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
            }

            $transaction = Transaction::create($transaction_data);

            $purchase_lines = [];

            foreach ($purchases as $purchase) {
                // TODO: create product

                $new_purchase_line = [
                    'product_id' => $purchase['product_id'],
                    'variation_id' => $purchase['variation_id'],
                    'quantity' => $purchase['quantity'],
                    'purchase_price' => $purchase['purchase_price'],
                    'purchase_price_inc_tax' => $purchase['purchase_price'],
                    'tax_id' => $purchase['tax_id'],
                    'tax_amount' => $this->productUtil->num_uf($purchase['tax_amount']),
                ];

                $purchase_lines[] = $new_purchase_line;

                // Edit average cost
                if ($enable_editing_avg_cost == 1) {
                    $this->productUtil->updateAverageCost($purchase['variation_id'], $purchase['purchase_price'], $purchase['quantity']);
                }

                // Update quantity only if status is "received"
                if ($transaction_data['status'] == 'received') {
                    $this->productUtil->updateProductQuantity(
                        $transaction_data['location_id'],
                        $purchase['product_id'],
                        $purchase['variation_id'],
                        $purchase['quantity'],
                        0,
                        null,
                        $transaction_data['warehouse_id']
                    );
                }
            }

            if (!empty($purchase_lines)) {
                $transaction->purchase_lines()->createMany($purchase_lines);
            }

            DB::commit();

            if ($transaction_data['status'] == 'received') {
                # Data to create or update kardex lines
                $lines = PurchaseLine::where('transaction_id', $transaction->id)->get();

                $movement_type = MovementType::where('name', 'purchase')
                    ->where('type', 'input')
                    ->where('business_id', $business_id)
                    ->first();

                # Check if movement type is set else create it
                if (empty($movement_type)) {
                    $movement_type = MovementType::create([
                        'name' => 'purchase',
                        'type' => 'input',
                        'business_id' => $business_id
                    ]);
                }

                # Store kardex
                $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $transaction->ref_no, $lines);
            }

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_add_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('purchases')->with('status', $output);
    }

    /**
     * Create purchase accounting entry
     * @param int $transaction_id
     */
    public function createAccountinEntry($transaction_id){
        $transaction =
            Transaction::join('business_locations as bl', 'transactions.location_id', 'bl.id')
                ->where('transactions.id', $transaction_id)
                ->select(
                    'transactions.transaction_date',
                    'bl.id as location_id',
                    'bl.name as location_name'
                )->first();

        try{
            $date = $this->accountingUtil->format_date($transaction->transaction_date);
            $description = "COMPRA DEL DÍA " . $date . " EN " . mb_strtoupper($transaction->location_name);

            $entry = [
                'date' => $this->accountingUtil->uf_date($date),
                'description' => $description,
                'short_name' => null,
                'business_location_id' => $transaction->location_id,
                'status_bank_transaction' => 1
            ];
            
            $entry_lines = $this->getPurchaseAccountingEntry($transaction_id);

            $entry_type =
                TypeEntrie::where('name', 'Diarios')
                    ->orWhere('name', 'Diario')
                    ->first();
            $entry['type_entrie_id'] = $entry_type->id;

            $output = $this->accountingUtil->createAccountingEntry($entry, $entry_lines, $entry['date']);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Get purchase accounting entry
     * @param int $transaction_id
     */
    private function getPurchaseAccountingEntry($transaction_id){
        $transaction = Transaction::find($transaction_id);
        $business = Business::find($transaction->business_id);
        $account_location = AccountBusinessLocation::where('location_id', $transaction->location_id)->first();

        $inventory_amount =
            Transaction::join('purchase_lines as pl', 'transactions.id', 'pl.transaction_id')
                ->where('transactions.id', $transaction_id)
                ->sum(DB::raw('pl.purchase_price * pl.quantity'));
        
        $tax_amount = $this->taxUtil->getTaxAmount($transaction->id, "purchase");

        return [
            [
                'catalogue_id' => $account_location->inventory_account_id,
                'amount' => $inventory_amount,
                'type' => 'debit',
                'description' => 'ENTRADA POR COMPRA DE MERCADERÍA'
            ],
            [
                'catalogue_id' => $business->accounting_vat_local_purchase_id,
                'amount' => $tax_amount,
                'type' => 'debit',
                'description' => 'IVA CRÉDITO FISCAL POR COMPRA DE MERCADERÍA'
            ],
            [
                'catalogue_id' => $business->accounting_perception_id,
                'amount' => $transaction->tax_amount,
                'type' => 'debit',
                'description' => 'IVA PERCEPCIÓN POR COMPRA DE MERCADERÍA',
            ],
            [
                'catalogue_id' => $account_location->supplier_account_id,
                'amount' => $transaction->final_total,
                'type' => 'credit',
                'description' => 'CUENTA POR PAGAR POR COMPRA DE MERCADERÍA'
            ]
        ];
    }

    /**
     * Debts to pay report
     * @param int
     */
    public function debtsToPay(){
        if (!auth()->user()->can('debts-to-pay.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->user()->business_id;

        if(request()->ajax()){
            $supplier_id = request()->input('supplier_id') ? request()->input('supplier_id') : 0;
            $location_id = request()->input('location_id') ? request()->input('location_id') : 0;
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');

            $transactions = collect(DB::select('CALL get_debts_to_pay(?, ?, ?, ?, ?)', [$business_id, $supplier_id, $location_id, $start_date, $end_date]));

            return DataTables::of($transactions)
                ->editColumn('transaction_date', '{{ @format_date($transaction_date) }}')
                ->editColumn('expire_date', '{{ empty($expire_date) ? "" : @format_date($expire_date) }}')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{ $final_total }}">{{ $final_total }}</span>'
                )->editColumn(
                    'payments',
                    '<span class="display_currency payments" data-currency_symbol="true" data-orig-value="{{ $payments }}">{{ $payments }}</span>'
                )->addColumn('debt_amount', function($row){
                    $debt_amount = round($row->final_total, 2) - round($row->payments, 2);
                    return '<span class="display_currency debt_amount" data-currency_symbol="true" data-orig-value="'. $debt_amount .'">'. $debt_amount .'</span>';
                })
                ->removeColumn('days_30')
                ->removeColumn('days_60')
                ->removeColumn('days_90')
                ->removeColumn('days_120')
                ->removeColumn('more_than_120')
                ->removeColumn('contact_id')
                ->rawColumns(['transaction_date', 'expire_date', 'final_total', 'payments', 'debt_amount'])
                ->toJson();
        }

        # Locations
		$locations = BusinessLocation::forDropdown($business_id, true);

        return view('contact.partials.debts_to_pay')
            ->with(compact('locations'));
    }

    /**
     * Generate debt to pay report
     */
    public function debtsToPayReport(){
        if (!auth()->user()->can('debts-to-pay.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->user()->business_id;
        $supplier_id = request()->input('supplier_id') ? request()->input('supplier_id') : 0;
        $location_id = request()->input('location_id') ? request()->input('location_id') : 0;
        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');
        $report_type = request()->input('report_type');

        $transactions = collect(DB::select('CALL get_debts_to_pay(?, ?, ?, ?, ?)',
            [$business_id, $supplier_id, $location_id, $start_date, $end_date]));

        $business_name = Business::find($business_id)->business_full_name;
        $report_name = __('report.debts_to_pay_report') ." ".  __("accounting.from_date") ." ". $this->transactionUtil->format_date($start_date) ." ". __("accounting.to_date") ." ". $this->transactionUtil->format_date($end_date);

        $final_totals = [
            'days_30' => $transactions->sum('days_30'),
            'days_60' => $transactions->sum('days_60'),
            'days_90' => $transactions->sum('days_90'),
            'days_120' => $transactions->sum('days_120'),
            'more_than_120_days' => $transactions->sum('more_than_120')
        ];
        $final_totals['totals'] = $final_totals['days_30'] + $final_totals['days_60'] + $final_totals['days_90'] + $final_totals['days_120'] + $final_totals['more_than_120_days'];

        if($report_type == 'pdf'){
            $debt_to_pay_report = \PDF::loadView('contact.partials.debts_to_pay_report_pdf',
                compact('transactions', 'business_name', 'report_name', 'final_totals'));
            $debt_to_pay_report->setPaper("A3", "landscape");

		    return $debt_to_pay_report->stream('debts_to_pay_report.pdf');

        } else if($report_type == 'excel'){
            return Excel::download(new DebtsToPayReportExport($transactions, $business_name, $report_name, $final_totals, $this->transactionUtil), 'debts_to_pay_report.xlsx');
        }
    }

    /**
     * Close VAT purchase book
     * @param date $start_date
     * @param date $end_date
     */
    public function closePurchaseBook(){
        if (!auth()->user()->can('accounting.close_vat_book')) {
            abort(403, 'Unauthorized action.');
        }

        try{
            $business_id = auth()->user()->business_id;
            $start_date = $this->transactionUtil->uf_date(request()->input("start_date"));
            $end_date = $this->transactionUtil->uf_date(request()->input("end_date"));
    
            // Query
            $lines = DB::select('CALL get_purchases_book(?, ?, ?)', [$start_date, $end_date, $business_id]);
    
            foreach($lines as $ln){
                $transaction = Transaction::find($ln->id);
                
                if(!empty($transaction)){
                    $transaction->is_closed = true;
                    $transaction->save();
                }
            }

            $output = [
                'success' => 1,
                'msg' => __('report.book_closed')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * valitade month date if closed
     */
    public function isClosed(){
        if(auth()->user()->can('is_close_book')){
            return 0;
        }
        $transaction_date = request()->input('date');

        $closed_trans = $this->transactionUtil->isClosed($transaction_date);

        return $closed_trans;
    }

    /**
     * Retrieves purchases.
     *
     * @return json
     */
    public function getPurchases()
    {
        if (request()->ajax()) {
            $term = request()->term;

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = auth()->user()->business_id;

            $purchases = Transaction::leftJoin('contacts as c', 'c.id', 'transactions.contact_id')
                ->leftJoin('apportionment_has_transactions as aht', 'aht.transaction_id', 'transactions.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->where('transactions.purchase_type', 'international')
                ->whereNull('aht.id')
                ->where(function ($query) use ($term) {
                    $query->where('transactions.ref_no', 'like', '%' . $term . '%');
                    $query->orWhere('c.name', 'like', '%' . $term . '%');
                    $query->orWhere('c.supplier_business_name', 'like', '%' . $term . '%');
                    $query->orWhere('c.contact_id', 'like', '%' . $term . '%');
                })
                ->select(
                    'transactions.id',
                    DB::raw("CONCAT(COALESCE(transactions.ref_no, ''), ' - ', COALESCE(c.name, '')) as text")
                )
                ->get();

            return json_encode($purchases);
        }
    }

    /**
     * Retrieves purchase row.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseRow()
    {
        if (request()->ajax()) {
            $id = request()->input('id');

            if (! empty($id)) {
                $row_count = request()->input('row_count_p');

                $purchase = Transaction::leftJoin('contacts as c', 'c.id', 'transactions.contact_id')
                    ->leftJoin('transaction_has_import_expenses as thie', 'thie.transaction_id', 'transactions.id')
                    ->where('transactions.id', $id)
                    ->select(
                        'transactions.ref_no',
                        'c.name',
                        'transactions.total_before_tax as final_total',
                        'transactions.id',
                        DB::raw('SUM(thie.amount) as import_expenses')
                    )
                    ->groupBy('transactions.id')
                    ->first();

                $business_id = auth()->user()->business_id;
                $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

                return view('purchase.partials.purchase_row', compact(
                    'purchase',
                    'row_count',
                    'currency_details'
                ));
            }
        }
    }

    /**
     * Update import data.
     * 
     * @return string
     */
    public function updateImports()
    {
        try {
            DB::beginTransaction();

            $imports = Transaction::where('type', 'purchase')
                ->where('purchase_type', 'international')
                ->pluck('id');

            \Log::info('--- START ---');

            foreach ($imports as $import_id) {
                // Update import data
                $this->transactionUtil->updateImportData($import_id);

                \Log::info('TRANSACTION: ' . $import_id);
            }

            \Log::info('--- END ---');

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }
}
