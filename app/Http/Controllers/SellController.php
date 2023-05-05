<?php

namespace App\Http\Controllers;

use App\User;
use App\TaxRate;
use App\Business;
use App\Customer;
use App\Transaction;
use App\DiscountCard;
use App\DocumentType;
use App\CustomerGroup;
use App\Utils\TaxUtil;
use App\BusinessLocation;
use App\Employees;
use App\Utils\ModuleUtil;
use App\SellingPriceGroup;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\TransactionPayment;
use App\Utils\BusinessUtil;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class SellController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $taxUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, TaxUtil $taxUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->taxUtil = $taxUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash',
            'amount' => 0,
            'note' => '',
            'card_transaction_number' => '',
            'card_number' => '',
            'card_type' => '',
            'card_holder_name' => '',
            'card_month' => '',
            'card_year' => '',
            'card_security' => '',
            'cheque_number' => '',
            'bank_account_number' => '',
            'is_return' => 0,
            'transaction_no' => ''
        ];

        // Payment status
        $this->payment_status = [
            'all' => __("kardex.all"),
            'paid' => __('sale.paid'),
            'pending' => __('sale.pending')
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $business = Business::where('id', $business_id)->select('annull_sale_expiry')->first();
            
            $discount = DiscountCard::where('business_id', '=', $business_id)->get();

            // Set maximum php execution time
            if (request()->get('length') == -1) {
                ini_set('max_execution_time', 0);
            }
            
            // Parameters
            $params = [
                // Filters
                'created_by' => request()->input('created_by'),
                'location_id' => request()->input('location_id'),
                'customer_id' => request()->customer_id,
                'seller_id' => request()->get("seller_id", 0),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date,
                'is_direct_sale' => request()->is_direct_sale,
                'commission_agent' => request()->get('commission_agent'),
                'payment_status' => request()->get('payment_status'),
                'document_type_id' => request()->input('document_type_id'),

                // Datatable parameters
                'start_record' => request()->get('start'),
                'page_size' => request()->get('length'),
                'search' => request()->get('search'),
                'order' => request()->get('order')
            ];

            // Sales
            $sales = collect($this->getSalesData($params));

            $datatable = Datatables::of($sales['data'])
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs btn-actions" data-transaction-id="{{ $id }}" data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <div id="loading" class="text-center">
                                <img src="{{ asset(\'img/loader.gif\') }}" alt="loading" />
                            </div>
                        </ul>
                    </div>'
                )
                ->editColumn(
                    'method', function ($row) {
                        if ($row->status != 'annulled') {
                            $method = "";

                            if ($row->payment_condition == 'cash' && !is_null($row->method)) {
                                if ($row->count_payments > 1) {
                                    $method = __('lang_v1.checkout_multi_pay');
                                } else {
                                    $method = __("lang_v1." . $row->method);
                                }

                            } else {
                                $method = __("lang_v1.credit");
                            }

                            return $method;

                        } else {
                            return "";
                        }
                    }
                )
                ->removeColumn('payment_condition')
                ->editColumn(
                    'final_total', function($row) {
                        $total = '<p class="text-right" style="margin-bottom: 0; color: #000;"><span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->final_total . '">"' . $row->final_total . '"</span>';

                        if ($row->amount_return > 0) {
                            $total .= '<br><strong>' . __('sale.return') . ':</strong><br>';
                            $total .= '<a href="' . action('SellReturnController@show', [$row->id]) . '" class="btn-modal">';
                            $total .= '<span class="display_currency" data-currency_symbol="true">' . $row->amount_return . '</span></a>';
                        }

                        $total .= '</p>';

                        return $total;
                    }
                )
                ->editColumn(
                    'discount_amount',
                    function($row) {
                        $discount_amount = 0.00;
                        if ($row->status != 'annulled') {
                            $discount_amount = $this->transactionUtil->getDiscountValue($row->total_before_tax, $row->discount_type, $row->discount_amount);
                        }
                        return '<span class="display_currency discount_amount" data-currency_symbol="true" data-orig-value="' . $discount_amount . '">' . $discount_amount . '</span>';
                    }
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="display_currency subtotal" data-currency_symbol="true" data-orig-value="{{$total_before_tax}}">{{$total_before_tax}}</span>'
                )
                ->editColumn(
                    'tax_amount', function ($row) {
                        if ($row->status != 'annulled') {
                            if ($row->tax_inc) {
                                $discount_amount = $this->transactionUtil->getDiscountValue($row->total_before_tax, $row->discount_type, $row->discount_amount);
                                $tax_amount = $this->taxUtil->getTaxAmount($row->id, 'sell', $discount_amount);
                                return '<span class="display_currency tax-amount" data-currency_symbol="true" data-orig-value="' . $tax_amount . '">' . $tax_amount . '</span>';
                            } else {
                                return '<span class="display_currency tax-amount" data-currency_symbol="true" data-orig-value="0.00">0.00</span>';
                            }
                            
                        } else {
                            return '<span class="display_currency tax-amount" data-currency_symbol="true" data-orig-value="0.00">0.00</span>';
                        }
                    }
                )
                ->editColumn(
                    'payment_status',
                    '<p class="text-center" style="margin-bottom: 0;">
                        <a href="{{ action("TransactionPaymentController@show", [$id]) }}" class="view_payment_modal payment-status-label" data-orig-value="{{ $payment_status }}" data-status-name="{{ __(\'lang_v1.\' . $payment_status) }}">
                            <span class="label @payment_status($payment_status)">{{ __(\'lang_v1.\' . $payment_status) }}</span>
                        </a>
                    </p>'
                )
                ->editColumn(
                    'customer_name', function ($row) {
                        if ($row->status == 'annulled') {
                            return "<strong style='color: red;'>" . $row->customer_name . ' ' . __('lang_v1.annulled') . "</strong>";
                        } else {
                            return $row->customer_name;
                        }
                    }
                )
                ->addColumn(
                    'total_remaining', function ($row) {
                        $total_remaining =  $row->final_total - $row->total_paid;
                        if ($row->status == 'annulled') {
                            $total_remaining = 0.00;
                        }
                        $total_remaining_html = '<span class="display_currency total_remaining" data-currency_symbol="true" data-orig-value="' . $total_remaining . '">' . $total_remaining . '</span>';
                        return $total_remaining_html;
                    }
                )
                ->editColumn(
                    'total_paid', function ($row) {
                        $total_paid = $row->total_paid > 0 ? $row->total_paid : 0;
                        $total_paid_html = '<span class="display_currency total_paid" data-currency_symbol="true" data-orig-value="' . $total_paid . '">' . $total_paid . '</span>';
                        return $total_paid_html;
                    }
                )
                ->addColumn(
                    'final_total_bc', function($row) {
                        $final_total = $row->final_total > 0 ? $row->final_total : 0;
                        $final_total_html =  '<span class="display_currency final_total_bc" data-currency_symbol="true" data-orig-value="' . $final_total . '">' . $final_total . '</span>';
                        return $final_total_html;
                    }
                )
                ->addColumn(
                    'actions', function ($row) {
                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        if (auth()->user()->can('sell.payments')) {
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_customer" style="cursor: p"><i class="fa fa-credit-card"></i> ' . __("messages.add_payment") . '</a></li>';
                        }

                        $html .= '</ul></div>';
                        return $html;
                    }
                );

            if (request()->get('length') != -1) {
                $datatable = $datatable->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);
            }

            $datatable = $datatable->removeColumn('id', 'tax_inc')
                ->rawColumns([
                    'customer_name',
                    'total_before_tax',
                    'tax_amount',
                    'total_remaining',
                    'final_total',
                    'action',
                    'total_paid',
                    'payment_status',
                    'actions',
                    'final_total_bc',
                    'discount_amount'
                ])
                ->setTotalRecords($sales['count'])
                ->setFilteredRecords($sales['count'])
                ->skipPaging()
                ->toJson();

            return $datatable;
        }
        
        // Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);

        $default_location = null;

        // Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
            }
            
        // Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__("kardex.all_2"), 'all');
        }

        // Document types
        $document_types = DocumentType::forDropdown($business_id, false, false);
        $document_types = $document_types->prepend(__("kardex.all"), 'all');

        /***  */
        $sellers = Employees::SellersDropdown($business_id, false);

        // Payment status
        $payment_status = $this->payment_status;

        return view('sell.index')
            ->with(compact(
                'locations',
                'default_location',
                'document_types',
                'payment_status',
                'sellers'
            ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellController@index'));
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $default_location = null;
        if (count($business_locations) == 1) {
            foreach ($business_locations as $id => $name) {
                $default_location = $id;
            }
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
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

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->transactionUtil->payment_types();

        //Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $default_datetime = $this->businessUtil->format_date('now', true);

        return view('sell.create')
            ->with(compact(
                'business_details',
                'taxes',
                'walk_in_customer',
                'business_locations',
                'bl_attributes',
                'default_location',
                'commission_agent',
                'types',
                'customer_groups',
                'payment_line',
                'payment_types',
                'price_groups',
                'default_datetime'
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
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
            ->pluck('name', 'id');
        $sell = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['sell_lines' => function ($q) {
                $q->whereNull('parent_sell_line_id');
            }, 'sell_lines.product', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax'])
            ->first();
        $payment_types = $this->transactionUtil->payment_types();

        $customer = Customer::leftJoin("countries as cnt", "customers.country_id", "cnt.id")
            ->leftJoin("states as st", "customers.state_id", "st.id")
            ->leftJoin("cities as ct", "customers.city_id", "ct.id")
            ->where("customers.id", $sell->customer_id)
            ->select(
                "cnt.name as country",
                "st.name as state",
                "ct.name as city",
                "customers.*"
            )
            ->first();

        /*$order_taxes = [];
        if (!empty($sell->tax)) {
            if ($sell->tax->is_tax_group) {
                $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
            } else {
                $order_taxes[$sell->tax->name] = $sell->tax_amount;
            }
        }*/
        $discount_amount = $this->transactionUtil->getDiscountValue($sell->total_before_tax, $sell->discount_type, $sell->discount_amount);
        $tax_percent = $this->taxUtil->getLinesTaxPercent($sell->id);

        $order_taxes =  ($sell->total_before_tax - $discount_amount) * $tax_percent; //$this->taxUtil->getTaxAmount($sell->id, "sell", $discount_amount);

        return view('sale_pos.show')
            ->with(compact('taxes', 'sell', 'customer', 'payment_types', 'order_taxes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
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

        $business_id = request()->session()->get('user.business_id');

        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $transaction = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->findorfail($id);

        $location_id = $transaction->location_id;
        $location_printer_type = BusinessLocation::find($location_id)->receipt_printer_type;

        $sell_details = TransactionSellLine::join(
                'products AS p',
                'transaction_sell_lines.product_id',
                '=',
                'p.id'
            )
            ->join(
                'variations AS variations',
                'transaction_sell_lines.variation_id',
                '=',
                'variations.id'
            )
            ->join(
                'product_variations AS pv',
                'variations.product_variation_id',
                '=',
                'pv.id'
            )
            ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', '=', $location_id);
            })
            ->leftjoin('units', 'units.id', '=', 'p.unit_id')
            ->where('transaction_sell_lines.transaction_id', $id)
            ->select(
                DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                'p.id as product_id',
                'p.enable_stock',
                'p.name as product_actual_name',
                'pv.name as product_variation_name',
                'pv.is_dummy as is_dummy',
                'variations.name as variation_name',
                'variations.sub_sku',
                'p.barcode_type',
                'p.enable_sr_no',
                'variations.id as variation_id',
                'units.short_name as unit',
                'units.allow_decimal as unit_allow_decimal',
                'transaction_sell_lines.tax_id as tax_id',
                'transaction_sell_lines.item_tax as item_tax',
                'transaction_sell_lines.unit_price as default_sell_price',
                'transaction_sell_lines.unit_price_inc_tax as sell_price_inc_tax',
                'transaction_sell_lines.unit_price_before_discount as unit_price_before_discount',
                'transaction_sell_lines.id as transaction_sell_lines_id',
                'transaction_sell_lines.quantity as quantity_ordered',
                'transaction_sell_lines.sell_line_note as sell_line_note',
                'transaction_sell_lines.lot_no_line_id',
                'transaction_sell_lines.line_discount_type',
                'transaction_sell_lines.line_discount_amount',
                DB::raw('vld.qty_available + transaction_sell_lines.quantity AS qty_available')
            )
            ->get();
        if (!empty($sell_details)) {
            foreach ($sell_details as $key => $value) {
                $sell_details[$key]->formatted_qty_available = $this->transactionUtil->num_f($value->qty_available);

                $lot_numbers = [];
                if (request()->session()->get('business.enable_lot_number') == 1) {
                    $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($value->variation_id, $business_id, $location_id);
                    foreach ($lot_number_obj as $lot_number) {
                        //If lot number is selected added ordered quantity to lot quantity available
                        if ($value->lot_no_line_id == $lot_number->purchase_line_id) {
                            $lot_number->qty_available += $value->quantity_ordered;
                        }

                        $lot_number->qty_formated = $this->transactionUtil->num_f($lot_number->qty_available);
                        $lot_numbers[] = $lot_number;
                    }
                }
                $sell_details[$key]->lot_numbers = $lot_numbers;
            }
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
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

        //Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $transaction->transaction_date = $this->transactionUtil->format_date($transaction->transaction_date, true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        return view('sell.edit')
            ->with(compact('business_details', 'taxes', 'sell_details', 'transaction', 'commission_agent', 'types', 'customer_groups', 'price_groups', 'pos_settings'));
    }

    public function editInvoiceTrans($id)
    {
        if (!auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $transaction = Transaction::join('document_types', 'document_types.id', '=', 'transactions.document_types_id')
            ->where('transactions.business_id', $business_id)
            ->where('type', 'sell')
            ->select(
                'transactions.id',
                'document_types_id',
                'correlative',
                'transactions.location_id',
                'transactions.customer_id',
                'transactions.return_parent_id',
                'transactions.parent_correlative',
                'status',
                'final_total',
                'transaction_date',
                'commission_agent',
                'staff_note',
                'additional_notes'
            )
            ->findOrFail($id);
        
        $parent_doc_date = "";
        if (!empty($transaction->parent_correlative)) {
            $parent_doc_date = Transaction::where('id', $transaction->return_parent_id)
                ->select(DB::raw('DATE_FORMAT(transaction_date, "%d/%m/%Y") as date'))
                ->first()->date;
        }

        $payments = TransactionPayment::where('transaction_id', $id)->get();
        $document_types = DocumentType::where('business_id', $business_id)->pluck('document_name', 'id');

        $transaction->transaction_date = $this->transactionUtil->format_date($transaction->transaction_date, true);
        
        // metodos de pago
        $payment_types = $this->transactionUtil->payment_types();

        // Commission agent
        $business_details = $this->businessUtil->getDetails($business_id);

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;

        $commission_agent = [];

        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id, false);

        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id, false);
        }

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        return view('sell.editInvoice')->with(compact(
            'transaction',
            'parent_doc_date',
            'document_types',
            'payment_types',
            'payments',
            'commission_agent',
            'pos_settings'
        ));
    }


    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('sell.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $transaction = Transaction::findOrFail($id);

            $input_date = $this->transactionUtil->uf_date($request->input('transaction_date'));
            $new_date = substr($input_date, 0, 10) . ' ' . substr($transaction->transaction_date, 11, 18);

            $transaction->transaction_date = $new_date;

            if (config('app.business') == 'optics') {
                $transaction->document_types_id = $request->document_type_id != null ? $request->document_type_id : $transaction->document_types_id;
            }

            $transaction->correlative = $request->correlative;
            $transaction->commission_agent = isset($request->commission_agent) ? $request->commission_agent : $transaction->commission_agent;
            $transaction->staff_note = isset($request->staff_note) ? $request->staff_note : $transaction->staff_note;
            $transaction->additional_notes = isset($request->additional_notes) ? $request->additional_notes : $transaction->additional_notes;
            
            /** Update parent document */
            if (!empty($transaction->parent_correlative)) {
                $transaction->return_parent_id = $request->get('return_parent_id', null);
                $transaction->parent_correlative = $request->get('parent_correlative', null);
            }

            $transaction->save();
            
            DB::commit();
            
            $output = [
                'success' => true,
                'msg' => __("messages.update_invoice"),
            ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     //
    // }

    /**
     * Display a listing sell drafts.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDrafts()
    {
        if (!auth()->user()->can('sell.draft') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        return view('sale_pos.draft');
    }

    /**
     * Display a listing sell quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getQuotations()
    {
        if (!auth()->user()->can('sell.quotation') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        return view('sale_pos.quotations');
    }

    /**
     * Get invoice parent correlative for final customer documents
     * 
     * @param \Illuminate\Http\Request
     * @return json
     * @author Arquímides Martínez
     */
    public function getParentCorrelative(Request $request) {
        $q = $request->get('q');
        $location_id = $request->input('location');
        $customer_id = $request->input('customer');
        $data = collect();

        if (!empty($q)) {
            $data = Transaction::join("document_types as dt", "transactions.document_types_id", "dt.id")
                ->where("location_id", $location_id)
                ->where("customer_id", $customer_id)
                ->whereNull("parent_correlative")
                ->where("correlative", "LIKE", "%{$q}%")
                ->where("dt.short_name", "FCF")
                ->select(
                    'transactions.id',
                    'transactions.correlative',
                    DB::raw("CONCAT('#', transactions.correlative, ' - ', DATE_FORMAT(transactions.transaction_date, '%d/%m/%Y')) as text"),
                )
                ->get();
        }

        return json_encode($data);
    }

    /**
     * Send the datatable response for draft or quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDraftDatables()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $is_quotation = request()->only('is_quotation', 0);

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'draft')
                ->where('is_quotation', $is_quotation)
                ->select(
                    'transactions.id',
                    'transaction_date',
                    'invoice_no',
                    'contacts.name',
                    'bl.name as business_location',
                    'is_direct_sale'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transaction_date', '>=', $start)
                    ->whereDate('transaction_date', '<=', $end);
            }
            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<a href="#" data-href="{{action(\'SellController@show\', [$id])}}" class="btn btn-xs btn-success btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a>
                &nbsp;
                @if($is_direct_sale == 1)
                <a target="_blank" href="{{action(\'SellController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</a>
                @else
                <a target="_blank" href="{{action(\'SellPosController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</a>
                @endif

                &nbsp; 
                <a href="#" class="print-invoice btn btn-xs btn-info" data-href="{{route(\'sell.printInvoice\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>

                &nbsp; <a href="{{action(\'SellPosController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete-sale"><i class="fa fa-trash"></i>  @lang("messages.delete")</a>
                '
                )
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ])
                ->rawColumns(['action', 'invoice_no', 'transaction_date'])
                ->make(true);
        }
    }

    /**
     * Creates copy of the requested sale.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicateSell($id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $transaction = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->findorfail($id);
            $duplicate_transaction_data = [];
            foreach ($transaction->toArray() as $key => $value) {
                if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                    $duplicate_transaction_data[$key] = $value;
                }
            }
            $duplicate_transaction_data['status'] = 'draft';
            $duplicate_transaction_data['payment_status'] = null;
            $duplicate_transaction_data['transaction_date'] =  \Carbon::now();
            $duplicate_transaction_data['created_by'] = $user_id;

            DB::beginTransaction();
            $duplicate_transaction_data['invoice_no'] = $this->transactionUtil->getInvoiceNumber($business_id, 'draft', $duplicate_transaction_data['location_id']);

            //Create duplicate transaction
            $duplicate_transaction = Transaction::create($duplicate_transaction_data);

            //Create duplicate transaction sell lines
            $duplicate_sell_lines_data = [];

            foreach ($transaction->sell_lines as $sell_line) {
                $new_sell_line = [];
                foreach ($sell_line->toArray() as $key => $value) {
                    if (!in_array($key, ['id', 'transaction_id', 'created_at', 'updated_at', 'lot_no_line_id'])) {
                        $new_sell_line[$key] = $value;
                    }
                }

                $duplicate_sell_lines_data[] = $new_sell_line;
            }

            $duplicate_transaction->sell_lines()->createMany($duplicate_sell_lines_data);

            DB::commit();

            $output = [
                'success' => 0,
                'msg' => trans("lang_v1.duplicate_sell_created_successfully")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans("messages.something_went_wrong")
            ];
        }

        if ($duplicate_transaction->is_direct_sale == 1) {
            return redirect()->action('SellController@edit', [$duplicate_transaction->id])->with(['status', $output]);
        } else {
            return redirect()->action('SellPosController@edit', [$duplicate_transaction->id])->with(['status', $output]);
        }
    }

    /**
     * Get sales data.
     * 
     * @param  array  $params
     * @return array
     */
    public function getSalesData($params)
    {
        // Business filter
        $business_id = request()->session()->get('user.business_id');

        // Created by filter
        if (! empty($params['created_by'])) {
            $created_by = $params['created_by'];
        } else {
            $created_by = 0;
        }

        // Location filter
        if (! empty($params['location_id']) && $params['location_id'] != 'all') {
            $location_id = $params['location_id'];
        } else {
            $location_id = 0;
        }

        // Customer filter
        if (! empty($params['customer_id'])) {
            $customer_id = $params['customer_id'];
        } else {
            $customer_id = 0;
        }

        // Seller filter
        if (! empty($params['seller_id'])) {
            $seller_id = $params['seller_id'];
        } else {
            $seller_id = 0;
        }

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start = $params['start_date'];
            $end =  $params['end_date'];
        } else {
            $start = '';
            $end =  '';
        }

        // Direct sale filter
        if (! empty($params['is_direct_sale'])) {
            $is_direct_sale = $params['is_direct_sale'];
        } else {
            $is_direct_sale = -1;
        }

        // Commission agent filter
        if (! empty($params['commission_agent'])) {
            $commission_agent = $params['commission_agent'];
        } else {
            $commission_agent = 0;
        }

        // Payment status filter
        if (! empty($params['payment_status']) && $params['payment_status'] != 'all') {
            $payment_status = $params['payment_status'];
        } else {
            $payment_status = '';
        }

        // Document type filter
        if (! empty($params['document_type_id']) && $params['document_type_id'] != 'all') {
            $document_type_id = $params['document_type_id'];
        } else {
            $document_type_id = 0;
        }

        // Datatable parameters
        $start_record = $params['start_record'];
        $page_size = $params['page_size'];
        $search_array = $params['search'];
        $search = ! is_null($search_array['value']) ? $search_array['value'] : '';
        $order = $params['order'];

        // Count sales
        $count = DB::select(
            'CALL count_all_sales(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $business_id,
                $location_id,
                $document_type_id,
                $created_by,
                $customer_id,
                $seller_id,
                $start,
                $end,
                $is_direct_sale,
                $commission_agent,
                $payment_status,
                $search
            )
        );

        if (config('app.business') == 'optics') {
            $parameters = [
                $business_id,
                $location_id,
                $document_type_id,
                $created_by,
                $customer_id,
                $start,
                $end,
                $is_direct_sale,
                $commission_agent,
                $payment_status,
                $search,
                $start_record,
                $page_size,
                $order[0]['column'],
                $order[0]['dir']
            ];

            $sales = DB::select(
                'CALL get_all_sales_optics(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $parameters
            );

        } else {
            // Sales
            $parameters = [
                $business_id,
                $location_id,
                $document_type_id,
                $seller_id,
                $created_by,
                $customer_id,
                $start,
                $end,
                $is_direct_sale,
                $commission_agent,
                $payment_status,
                $search,
                $start_record,
                $page_size,
                $order[0]['column'],
                $order[0]['dir']
            ];

            \Log::emergency($parameters);

            $sales = DB::select(
                'CALL get_all_sales(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $parameters
            );
        }

        $result = [
            'data' => $sales,
            'count' => $count[0]->count
        ];

        return $result;
    }

    /**
     * Get sales togle dropdown.
     * 
     * @param  array  $params
     * @return @return \Illuminate\Http\Response
     */
    public function getToggleDropdown($id)
    {
        $output = [];

        try {
            $transaction = Transaction::where('id', $id)->first();

            $is_direct_sale = $transaction->is_direct_sale;
            $payment_status = $transaction->payment_status;
            $status = $transaction->status;
            $transaction_date = $transaction->transaction_date;
            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->select('annull_sale_expiry', 'enable_sell_delete')->first();
            $enable_sell_delete = $business->enable_sell_delete;


            $parent_doc = null;
            if (!empty($transaction->parent_correlative)) {
                $parent_doc = Transaction::where('id', $transaction->return_parent_id)
                    ->where('location_id', $transaction->location_id)
                    //->where('customer_id', $transaction->customer_id)
                    ->value('id');
            }

            return view('sale_pos.partials.toggle_dropdown')
                ->with(compact('id', 'is_direct_sale', 'payment_status', 'status', 'parent_doc', 'transaction_date', 'business', 'enable_sell_delete'))
                ->render();
            
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = __('messages.something_went_wrong');
        }

        return $output;
    }

    /**
     * Update field payment_balance of transactions table.
     * 
     * @return string
     */
    public function updatePaymentBalance() {
        try {
            \Log::info('--- START ---');

            DB::beginTransaction();

            $transactions = Transaction::where('type', 'sell')->get();

            foreach ($transactions as $transaction) {
                $payment_balance = DB::select("
                    SELECT
                        IF (t.status = 'annulled', 0.00, SUM(IF (tp.is_return = 1, -1 * tp.amount, tp.amount))) AS total_paid
                    FROM transactions AS t
                    LEFT JOIN transaction_payments AS tp
                        ON t.id = tp.transaction_id
                    WHERE t.id = ?
                ", [$transaction->id]);

                $transaction->payment_balance = $payment_balance[0]->total_paid;
                $transaction->save();

                \Log::info("TRANSACTION: ID -> $transaction->id - CORRELATIVE -> $transaction->correlative");
            }

            DB::commit();

            \Log::info('--- END ---');

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }
}
