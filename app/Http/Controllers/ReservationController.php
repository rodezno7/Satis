<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Brands;
use App\BusinessLocation;
use App\Category;
use App\Customer;
use App\CustomerGroup;
use App\DocumentCorrelative;
use App\DocumentType;
use App\Employees;
use App\KitHasProduct;
use App\Pos;
use App\Product;
use App\Quote;
use App\QuoteLine;
use App\SellingPriceGroup;
use App\TaxRate;
use App\TransactionPayment;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\CashRegisterUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TaxUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Variation;
use App\VariationLocationDetails;
use Illuminate\Http\Request;
use DB;
use Yajra\DataTables\Facades\DataTables;

class ReservationController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $productUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $taxUtil;
    protected $cashRegisterUtil;
    protected $moduleUtil;
    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(
        ContactUtil $contactUtil,
        ProductUtil $productUtil,
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        TaxUtil $taxUtil,
        CashRegisterUtil $cashRegisterUtil,
        ModuleUtil $moduleUtil,
        NotificationUtil $notificationUtil,
        Util $util
    ) {
        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->taxUtil = $taxUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moduleUtil = $moduleUtil;
        $this->notificationUtil = $notificationUtil;
        $this->util = $util;

        $this->dummyPaymentLine = [
            'method' => 'cash',
            'amount' => 0,
            'note' => '',
            'card_holder_name' => '', /** Card */
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

        // Business types
        $this->business_type = ['small_business', 'medium_business', 'large_business'];
        
        // Payment conditions
        $this->payment_conditions = ['cash', 'credit'];
        
        // Payment note short name
        $this->note_name = 'NA';

        // Payment status
        $this->payment_status = [
            'all' => __("kardex.all"),
            'paid' => __('sale.paid'),
            'pending' => __('sale.pending')
        ];

        // Binnacle data
        $this->module_name = 'reservation';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('reservation.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $reservations = Quote::LeftJoin('employees', 'quotes.employee_id', 'employees.id')
                ->leftJoin('transaction_payments', 'quotes.id', 'transaction_payments.quote_id')
                ->where('quotes.business_id', $business_id)
                ->where('quotes.type', 'reservation')
                ->where('quotes.invoiced', 0)
                ->select(
                    'quotes.quote_ref_no',
                    'quotes.quote_date',
                    'quotes.customer_name',
                    DB::raw("IF(quotes.invoiced = 1, 'yes', 'no') as invoiced"),
                    'quotes.total_final',
                    DB::raw("CONCAT(employees.first_name, ' ', employees.last_name) as employee_name"),
                    'quotes.id',
                    DB::raw("(SELECT SUM(tp1.amount) FROM transaction_payments AS tp1 WHERE tp1.quote_id = quotes.id) as amount_paid"),
                    DB::raw("(SELECT GROUP_CONCAT(DISTINCT tp2.note ORDER BY tp2.note SEPARATOR ', ') FROM transaction_payments AS tp2 WHERE tp2.quote_id = quotes.id) as note")
                );

            // Location filter
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');

                if (! empty($location_id)) {
                    if ($location_id != 'all') {
                        $reservations->where('quotes.location_id', $location_id);
                    }   
                }
            }

            // Document type filter
            if (request()->has('document_type_id')) {
                $document_type_id = request()->get('document_type_id');

                if (! empty($document_type_id)) {
                    if ($document_type_id != 'all') {
                        $reservations->where('quotes.document_type_id', $document_type_id);
                    }
                }
            }

            // Payment status all paid pending
            if (request()->has('payment_status')) {
                $payment_status = request()->get('payment_status');

                if (! empty($payment_status)) {
                    if ($payment_status != 'all') {
                        // Paid reservations
                        if ($payment_status == 'paid') {
                            $reservations->where('quotes.total_final', 'amount_paid');

                        // Reservations pending payment
                        } else {
                            $reservations->where('quotes.total_final', '>', 'amount_paid');
                        }
                    }   
                }
            }

            // Date filter
            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $reservations->whereDate('quotes.quote_date', '>=', $start)
                    ->whereDate('quotes.quote_date', '<=', $end);
            }

            $reservations->groupBy('quotes.id');

            return Datatables::of($reservations)
                ->filterColumn('employee_name', function($query, $keyword) {
                    $query->whereRaw('CONCAT(employees.first_name, " ", employees.last_name) LIKE ?', ['{$keyword}']);
                })
                ->filterColumn('amount_paid', function($query, $keyword) {
                    $query->whereRaw('(SELECT SUM(tp1.amount) FROM transaction_payments AS tp1 WHERE tp1.quote_id = quotes.id) LIKE ?', ['{$keyword}']);
                })
                ->addColumn('action',
                '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") .
                        ' <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        @can("reservation.view")
                            <li><a href="{{ action(\'ReservationController@show\', [$id]) }}" class="show_reservation"><i class="fa fa-eye"></i> @lang("messages.view")</a></li>
                        @endcan
                        @can("reservation.update")
                            <li><a href="{{ action(\'ReservationController@edit\', [$id]) }}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                        @endcan
                        @can("reservation.delete")
                            <li><a href="{{ action(\'ReservationController@destroy\', [$id]) }}" class="delete_reservation"><i class="fa fa-trash"></i> @lang("messages.delete")</a></li>
                        @endcan

                        <li class="divider"></li>

                        @if ($amount_paid != $total_final)
                            @if(auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access"))
                                <li><a href="{{ action(\'TransactionPaymentController@addPaymentToQuote\', [$id]) }}" class="add_payment_modal"><i class="fa fa-money"></i> @lang("purchase.add_payment")</a></li>
                            @endif
                        @endif

                        <li><a href="{{ action(\'TransactionPaymentController@showToQuote\', [$id]) }}" class="view_payment_modal"><i class="fa fa-money"></i> @lang("purchase.view_payments")</a></li>
                    </ul>
                </div>')
                ->editColumn('invoiced', '{{ __("messages." . $invoiced) }}')
                ->editColumn('total_final', '<span class="display_currency" data-currency_symbol="true">$ {{ $total_final ? number_format($total_final, 2) : number_format(0, 2) }}</span>')
                ->editColumn('amount_paid', '<span class="display_currency" data-currency_symbol="true">$ {{ $amount_paid ? number_format($amount_paid, 2) : number_format(0, 2) }}</span>')
                ->removeColumn('id')
                ->rawColumns(['total_final', 'action', 'amount_paid'])
                ->toJson();
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

        // Payment status
        $payment_status = $this->payment_status;

        return view('reservation.index')
            ->with(compact('locations', 'default_location', 'document_types', 'payment_status'));
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
        if (! auth()->user()->can('reservation.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->except('_token');

            if (! empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $commsn_agnt_setting = $request->session()->get('business.sales_cmsn_agnt');

                DB::beginTransaction();
                
                // Date
                if (empty($request->input('transaction_date'))) {
                    $input['quote_date'] =  \Carbon::now();

                } else {
                    $quot_time = session('business.time_format') == 12 ? date('h:i A') : date('H:i');
                    $quot_date = substr($request->input('transaction_date'), 0, 10);
                    $quote_date = $quot_date . ' ' . $quot_time;
                    $input['quote_date'] = $this->productUtil->uf_date($quote_date, true);
                }

                // Get commission agent from employee id
                $commission_agent = Employees::where('id', $request->input('commission_agent'))
                    ->first();

                $input['employee_id'] = ! empty($commission_agent) ? $commission_agent->user_id : null;

                if ($commsn_agnt_setting == 'logged_in_user') {
                    $input['employee_id'] = $user_id;
                }

                // Selling price group
                if ($request->has('price_group')) {
                    $input['selling_price_group_id'] = $request->input('price_group') != 0 ? $request->input('price_group') : null;
                }
                
                // Document type
                if ($request->has('documents')) {
                     $input['document_type_id']  = $request->input('documents');
                     $input['correlative'] = $request->input('correlatives');
                }

                // Customer
                if (! empty($request->input('customer_id'))) {
                    $customer = Customer::find($request->input('customer_id'));
                    
                    if (! empty($customer) && $customer->is_default != 1) {
                        $input['customer_name'] = $customer->name;
                    }
                }

                // 0: paid, 1: credit, 2: partial
                $is_credit = $request->input('is_credit');
                $input['payment_condition'] = $is_credit == '1' || $is_credit == '2' ? 'credit' : 'cash';

                // Get reference number
                $input['ref_no'] = $this->util->generateQuoteReference();

                // Store quote
                $quote = $this->transactionUtil->createReservation($business_id, $input, $user_id);

                // Store binnacle
                $this->transactionUtil->registerBinnacle(
                    $this->module_name,
                    'create',
                    $quote->quote_ref_no,
                    $quote
                );

                // Update payment note correlative
                $payment_note_correlative = DocumentCorrelative::where('business_id', $business_id)
                    ->where('location_id', $input['location_id'])
                    ->whereRaw('initial <= final')
                    ->where('document_type_id', $input['payment_note_id'])
                    ->where('status', 'active')
                    ->first();

                if (! empty($payment_note_correlative)) {
                    if ($payment_note_correlative->actual < $payment_note_correlative->final) {
                        $payment_note_correlative->actual += 1;
                        $payment_note_correlative->save();

                    }  else if ($payment_note_correlative->actual == $payment_note_correlative->final) {
                        $payment_note_correlative->status = 'inactive';
                        $payment_note_correlative->save();
                    }
                }

                // Store quote lines
                $this->transactionUtil->createQuoteLines($quote, $input['products'], $input['location_id']);

                $is_direct_sale = false;

                if (! empty($request->input('is_direct_sale'))) {
                    $is_direct_sale = true;
                }
                
                if (! $is_direct_sale) {
                    // Add change return
                    $change_return = $this->dummyPaymentLine;
                    $change_return['amount'] = $input['change_return'];
                    $change_return['is_return'] = 1;
                    $input['payment'][] = $change_return;
                }

                if ($is_credit == '0' || $is_credit == '2') {
                    $this->transactionUtil->createOrUpdatePaymentLinesToQuote(
                        $quote,
                        $input['payment'],
                        null,
                        $input['cashier_id'],
                        $user_id,
                        $quote->quote_date,
                        $input['note']
                    );
                }

                // Update reserved quantity
                foreach ($input['products'] as $product) {
                    $id = $product['product_id'];
                    $product_q = Product::where('id', $id)->first();
                    $clasification = $product_q->clasification;

                    if ($clasification == 'kits') {
                        $childrens = KitHasProduct::where('parent_id', $id)->get();

                        foreach ($childrens as $item) {
                            $variation_q = Variation::where('id', $item->children_id)->first();

                            $this->productUtil->increaseReservedQuantity(
                                $variation_q->product_id,
                                $item->children_id,
                                $input['location_id'],
                                $input['location_id'],
                                $this->productUtil->num_uf($item->quantity * $product['quantity'])
                            );
                        }

                    } else if ($clasification == 'product' || $clasification == 'material') {
                        $this->productUtil->increaseReservedQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $input['location_id'],
                            $input['location_id'],
                            $this->productUtil->num_uf($product['quantity'])
                        );
                    }
                }

                // Update payment status
                $status = $this->transactionUtil->calculatePaymentStatusToQuotes($quote->id, $quote->total_final);

                // Store cash register
                if (! $is_direct_sale) {
                    // Add payments to cash register
                    $payment_lines = $this->transactionUtil->getPaymentDetailsToQuotes($quote->id);

                    // If some payment exists
                    if (! empty($payment_lines)) {
                        $this->cashRegisterUtil->addSellPaymentsToQuotes($quote, $payment_lines);
                    }

                    // Add credit sell to cash register
                    if ($status != 'paid') {
                        $total_paid = $this->transactionUtil->getTotalPaidToQuotes($quote->id);

                        $this->cashRegisterUtil->addCreditSellPaymentToQuotes($quote, $total_paid, $quote->total_final);
                    }
                }

                DB::commit();
                
                $msg = __('lang_v1.reservation_added_successfully');

                $show_modal = true;

                $output = [
                    'success' => 1,
                    'msg' => $msg,
                    // 'receipt' => $receipt,
                    'transaction_id' => 0,
                    'show_modal' => $show_modal
                ];

            } else {
                $output = [
                    'success' => 0,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();

            } else {
                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

                $msg = __('messages.something_went_wrong');
            }

            $output = [
                'success' => 0,
                'msg' => 'File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage()
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
        if (! auth()->user()->can("reservation.view")) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $reservation = Quote::join('customers as c', 'quotes.customer_id', 'c.id')
            ->leftJoin('document_types as dc', 'quotes.document_type_id', 'dc.id')
            ->leftJoin('employees as e', 'quotes.employee_id', 'e.id')
            ->leftJoin('states as st', 'quotes.state_id', 'st.id')
            ->leftJoin('cities as ct', 'quotes.city_id', 'ct.id')
            ->where('quotes.business_id', $business_id)
            ->where('quotes.id', $id)
            ->where('quotes.type', 'reservation')
            ->select(
                'quotes.*',
                'st.name as state',
                'ct.name as city',
                'c.name as customer_real_name',
                'dc.document_name',
                DB::raw("CONCAT(e.first_name, ' ', e.last_name) as seller_name")
            )
            ->first();

        $quote_lines = Quote::join('quote_lines as ql', 'quotes.id', 'ql.quote_id')
            ->join('variations as v', 'ql.variation_id', 'v.id')
            ->join('products as p', 'v.product_id', 'p.id')
            ->where('quotes.business_id', $business_id)
            ->where('quotes.id', $id)
            ->select(
                'ql.*',
                'p.name as product_name',
                'p.tax as tax_id',
                'p.id as tax_percent' // Used to store tax percent
            )->get();

        // Tax percent added
        foreach ($quote_lines as $ql) {
            $ql->tax_percent = $this->taxUtil->getTaxPercent($ql->tax_id);
        }
        
        $discount_types = [
            'fixed' => __('lang_v1.fixed'),
            'percentage' => __('lang_v1.percentage')
        ];

        return view('reservation.show')
            ->with(compact('reservation', 'quote_lines', 'discount_types'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('reservation.update')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if there is a open register, if no then redirect to create register screen.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->action('CashRegisterController@create');
        }

        $business_id = request()->session()->get('user.business_id');

        $walk_in_customer = $this->contactUtil->getDefaultCustomer($business_id);
        
        $business_details = $this->businessUtil->getDetails($business_id);

        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $payment_types = $this->productUtil->payment_types();

        $transaction = Quote::where('id', $id)->where('type', 'reservation')->first();
        
        $customer = Customer::find($transaction->customer_id);

        // $patient = Patient::join('lab_orders as lo', 'lo.patient_id', 'patients.id')
        //     ->join('transactions as t', 't.id', 'lo.transaction_id')
        //     ->where('lo.quote_id', $quote->id)
        //     ->select('patients.*')
        //     ->first();

        $location_id = $transaction->location_id;

        $location_printer_type = BusinessLocation::find($location_id)->receipt_printer_type;

        $sell_details = QuoteLine::join('variations', 'quote_lines.variation_id', 'variations.id')
            ->join('products AS p', 'variations.product_id', 'p.id')
            ->join('product_variations AS pv', 'variations.product_variation_id', 'pv.id')
            ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
                    $join->on('variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', '=', $location_id);
            })
            ->leftjoin('units', 'units.id', '=', 'p.unit_id')
            ->where('quote_lines.quote_id', $id)
            ->select(
                DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, ' (', pv.name, ':', variations.name, ')'), p.name) AS product_name"),
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
                'p.tax as tax_id',
                'quote_lines.unit_price_exc_tax as default_sell_price',
                // 'quote_lines.unit_price_before_discount as unit_price_before_discount',
                'variations.sell_price_inc_tax as sell_price_inc_tax',
                'quote_lines.id as transaction_sell_lines_id',
                'quote_lines.quantity as quantity_ordered',
                // 'quote_lines.sell_line_note as sell_line_note',
                // 'quote_lines.parent_sell_line_id',
                // 'quote_lines.lot_no_line_id',
                'quote_lines.discount_type as line_discount_type',
                'quote_lines.discount_amount as line_discount_amount',
                DB::raw('vld.qty_available - vld.qty_reserved + quote_lines.quantity AS qty_available')
            )
            ->get();

        if (! empty($sell_details)) {
            foreach ($sell_details as $key => $value) {
                // If modifier sell line then unset
                if (! empty($sell_details[$key]->parent_sell_line_id)) {
                    unset($sell_details[$key]);

                } else {
                    $sell_details[$key]->formatted_qty_available = $this->productUtil->num_f($value->qty_available);

                    // Add available lot numbers for dropdown to sell lines
                    $lot_numbers = [];

                    if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                        $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($value->variation_id, $business_id, $location_id, true);

                        foreach ($lot_number_obj as $lot_number) {
                            # If lot number is selected added ordered quantity to lot quantity available
                            if ($value->lot_no_line_id == $lot_number->purchase_line_id) {
                                $lot_number->qty_available += $value->quantity_ordered;
                            }

                            $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                            $lot_numbers[] = $lot_number;
                        }
                    }

                    $sell_details[$key]->lot_numbers = $lot_numbers;

                    if ($this->transactionUtil->isModuleEnabled('modifiers')) {
                        // Add modifier details to sel line details
                        $sell_line_modifiers = TransactionSellLine::where('parent_sell_line_id', $sell_details[$key]->transaction_sell_lines_id)->get();
                        
                        $modifiers_ids = [];
                        
                        if (count($sell_line_modifiers) > 0) {
                            $sell_details[$key]->modifiers = $sell_line_modifiers;

                            foreach ($sell_line_modifiers as $sell_line_modifier) {
                                $modifiers_ids[] = $sell_line_modifier->variation_id;
                            }
                        }

                        $sell_details[$key]->modifiers_ids = $modifiers_ids;

                        // Add product modifier sets for edit
                        $this_product = Product::find($sell_details[$key]->product_id);

                        if (count($this_product->modifier_sets) > 0) {
                            $sell_details[$key]->product_ms = $this_product->modifier_sets;
                        }
                    }
                }
            }
        }

        $payment_lines = $this->transactionUtil->getPaymentDetailsToQuotes($id);

        // If no payment lines found then add dummy payment line.
        if (empty($payment_lines)) {
            $payment_lines[] = $this->dummyPaymentLine;
        }

        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];

        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id, false);
        } else if ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id, false);
        }

        $categories = Category::catAndSubCategories($business_id);

        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');
        $brands->prepend(__('lang_v1.all_brands'), 'all');

        $change_return = $this->dummyPaymentLine;

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

        // Employees (sellers)
        $employees_sales = Employees::forDropdown(($business_id));
        
        // Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);
        
        // Selling price group
        $price_groups = SellingPriceGroup::forDropdown($business_id);
        
        // Tax groups
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');
        
        // Business type
        $business_type = $this->business_type;
        
        // Payment conditions
        $payment_conditions = $this->payment_conditions;

        // Document types
        $documents =  DocumentType::where('business_id',$business_id)
            ->where('is_active', 1)
           ->select('short_name', 'tax_inc', 'id')
           ->get();

        // Banks
        $banks = Bank::where('business_id', $business_id)
            ->pluck('name', 'id');

        // Pos
        $pos = Pos::where('business_id', $business_id)
            ->pluck('name', 'id');

        // Check if it's editing
        $is_edit = true;

        // Check if it's a quote
        $is_quote = true;

        // Check if user is admin
        $user = User::find(request()->user()->id);
        $is_admin = $user->hasRole('Super Admin#' . $business_id);

        // Document data
        $doc_tax_inc = $transaction->tax_inc;
        $doc_tax_exempt = $transaction->tax_exempt;

        // Number of decimals in sales
        $product_settings = empty($business_details->product_settings) ? $this->businessUtil->defaultProductSettings() : json_decode($business_details->product_settings, true);
        $decimals_in_sales = $product_settings['decimals_in_sales'];
        
        return view('sale_pos.edit')
            ->with(compact(
                'business_details',
                'business_type',
                'payment_conditions',
                'taxes',
                'tax_groups',
                'employees_sales',
                'payment_types',
                'walk_in_customer',
                'sell_details',
                'transaction',
                'payment_lines',
                'location_printer_type',
                'shortcuts',
                'commission_agent',
                'categories',
                'pos_settings',
                'change_return',
                'types',
                'customer_groups',
                'brands',
                'accounts',
                'price_groups',
                'documents',
                'customer',
                'banks',
                'pos',
                'is_edit',
                'is_quote',
                'is_admin',
                'doc_tax_inc',
                'doc_tax_exempt',
                'decimals_in_sales'
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
        if (! auth()->user()->can('reservation.update')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $input = $request->except('_token');

            // $is_direct_sale = false;

            if (! empty($input['products'])) {
                // Get transaction value before updating
                $quote_before = Quote::where('id', $id)->first();
                $status_before = $this->transactionUtil->calculatePaymentStatusToQuotes($quote_before->id, $quote_before->total_final);

                // if ($quote_before->is_direct_sale == 1) {
                //     $is_direct_sale = true;
                // }

                // Check if there is a open register, if no then redirect to create register screen.
                if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
                    return redirect()->action('CashRegisterController@create');
                }

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $commsn_agnt_setting = $request->session()->get('business.sales_cmsn_agnt');

                if (! empty($request->input('transaction_date'))) {
                    $trans_time = session('business.time_format') == 12 ? date('h:i A') : date('H:i');
                    $trans_date = substr($request->input('transaction_date'), 0, 10); # Get date only
                    $transaction_date = $trans_date . ' ' . $trans_time;
                    $input['transaction_date'] = $this->productUtil->uf_date($transaction_date, true);
                }

                $input['commission_agent'] = ! empty($request->input('commission_agent')) ? $request->input('commission_agent') : null;
                
                if ($commsn_agnt_setting == 'logged_in_user') {
                    $input['commission_agent'] = $user_id;
                }

                if (isset($input['exchange_rate']) && $this->transactionUtil->num_uf($input['exchange_rate']) == 0) {
                    $input['exchange_rate'] = 1;
                }

                // Customer group details
                $customer_id = $request->get('customer_id', null);
                $cg = $this->contactUtil->getCustomerGroup($business_id, $customer_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;
                
                // Set selling price group id
                if ($request->has('price_group')) {
                    $input['selling_price_group_id'] = $request->input('price_group');
                }

                // $input['is_suspend'] = isset($input['is_suspend']) && 1 == $input['is_suspend']  ? 1 : 0;
                // if ($input['is_suspend']) {
                //     $input['sale_note'] = !empty($input['additional_notes']) ? $input['additional_notes'] : null;
                // }

                // Set documents
                if ($request->has('documents')) {
                    $input['document_type_id']  = $request->input('documents');
                    $input['correlative'] = $request->input('correlatives');
                }

                // Set customer
                $input['customer_id'] = $request->input('customer_id');
                $input['customer_name'] = is_null($request->input('customer_name')) ? $request->input('customer_name') : $quote_before->customer_name;

                if (! empty($request->input('customer_id'))) {
                    $customer = Customer::find($request->input('customer_id'));

                    if (! empty($customer) && $customer->is_default != 1) {
                        $input['customer_name'] = $customer->name;
                    }
                }

                DB::beginTransaction();

                $quote = $this->transactionUtil->updateReservation($id, $business_id, $input, null, $user_id);

                // Data to create or update output lines
                $quote_lines_before = QuoteLine::where('quote_id', $quote->id)->get();

                // Update sell lines
                $deleted_lines = $this->transactionUtil->createOrUpdateQuoteLines(
                    $quote,
                    $input['products'],
                    $input['location_id'],
                    true,
                    $status_before
                );

                // Delete cash register transactions
                DB::table('cash_register_transactions')->where('quote_id', $quote->id)->delete();

                // Update product reserved
                $this->productUtil->updateQuantityReserved($input);

                // Add payments to cash register
                $payment_lines = $this->transactionUtil->getPaymentDetailsToQuotes($quote->id);

                // If some payment exists
                if (! empty($payment_lines)) {
                    $this->cashRegisterUtil->addSellPaymentsToQuotes($quote, $payment_lines);
                }

                // Update payment status
                $status = $this->transactionUtil->calculatePaymentStatusToQuotes($quote->id, $quote->total_final);

                // Add credit sell to cash register
                if ($status != 'paid') {
                    $total_paid = $this->transactionUtil->getTotalPaidToQuotes($quote->id);

                    $this->cashRegisterUtil->addCreditSellPaymentToQuotes($quote, $total_paid, $quote->total_final);
                }

                DB::commit();
                    
                $msg = trans('reservation.reservation_update_success');
                $receipt = '';
                $show_modal = false;

                $output = [
                    'success' => 1,
                    'msg' => $msg,
                    'receipt' => $receipt,
                    'show_modal' => $show_modal
                ];

            } else {
                $output = [
                    'success' => 0,
                    'msg' => trans('messages.something_went_wrong')
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
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
        if (!auth()->user()->can('reservation.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $quote = Quote::where('id', $id)
                    ->with(['quote_lines'])
                    ->first();

                // Clone record before action
                $quote_old = clone $quote;

                DB::beginTransaction();

                if (! empty($quote)) {
                    $deleted_quote_lines = $quote->quote_lines;
                    $deleted_quote_lines_ids = $deleted_quote_lines->pluck('id')->toArray();
                    
                    $this->transactionUtil->deleteQuoteLines(
                        $deleted_quote_lines_ids,
                        $quote->location_id,
                        $quote->warehouse_id
                    );

                    $this->cashRegisterUtil->refundQuote($quote);

                    $quote->delete();

                    // Store binnacle
                    $this->transactionUtil->registerBinnacle(
                        $this->module_name,
                        'delete',
                        $quote_old->quote_ref_no,
                        $quote_old
                    );
                }
            
                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('reservation.reservation_delete_success')
                ];

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

                $output['success'] = false;
                $output['msg'] = trans('messages.something_went_wrong');
            }

            return $output;
        }
    }

    /**
     * Retrieves list of quotes, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getReservations()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            $location_id = request()->input('location_id');

            $quotes = Quote::join('customers as c', 'quotes.customer_id', 'c.id')
                // ->leftJoin('tax_rate_tax_group as trtg', 'c.tax_group_id', 'trtg.tax_group_id')
                // ->leftJoin('tax_rates as tr', 'trtg.tax_rate_id', 'tr.id')
                ->leftJoin('employees as e', 'quotes.employee_id', 'e.id')
                ->where('quotes.business_id', $business_id)
                ->where('quotes.type', 'reservation')
                ->where('quotes.invoiced', false)
                // ->whereIn("quotes.status", ['opened']);
                ->where('quotes.location_id', $location_id);

            if (!empty($term)) {
                $quotes->where(function ($query) use ($term) {
                    $query->where('quotes.customer_name', 'like', '%' . $term .'%')
                    ->orWhere("quotes.quote_ref_no", 'like', '%' . $term . '%');
                });
            }

            $quotes = $quotes->select(
                'c.id as customer_id',
                'c.name as c_name',
                // 'c.tax_group_id',
                // 'tr.percent as tax_percent',
                // 'tr.min_amount',
                // 'tr.max_amount',
                'c.is_default',
                'c.allowed_credit',
                'c.is_withholding_agent',
                'e.id as employee_id',
                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as employee"),
                'e.agent_code',
                DB::raw("CONCAT(quotes.customer_name, ' #', quotes.quote_ref_no) AS text"),
                'quotes.*'
                // 'quotes.id as order_id',
                // 'quotes.customer_name'
            )->with(['quote_lines', 'payment_lines'])
            ->get();

            return json_encode($quotes);
        }
    }

    public function getProductRow($quote_line_id, $variation_id, $location_id, $row_count)
    {
        $output = [];

        try {
            $business_id = request()->session()->get('user.business_id');
            $warehouse_id = request()->input('warehouse_id');
            $check_qty_available = request()->input('check_qty_available');
            $reservation_id = request()->input('reservation_id', 0);
            $quote_line = QuoteLine::where('id', $quote_line_id)
                ->first();

            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id, $warehouse_id);

            if (empty($product)) {
                $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, null, $warehouse_id);
                $quote_line->quantity = 'N/A';
                $product->enable_stock = 1;
            }

            // $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);
            $product->quantity_ordered = $quote_line->quantity;
            $product->default_sell_price = $quote_line->unit_price_exc_tax;
            $product->sell_price_inc_tax = $quote_line->unit_price_inc_tax;
            $product->line_discount_type = $quote_line->discount_type;
            $product->line_discount_amount = $quote_line->discount_amount;

            if ($product->clasification == 'kits') {
                $formatted_qty_available = null;

                $childrens = KitHasProduct::where('parent_id', $product->product_id)->get();

                foreach ($childrens as $item) {
                    $prod = Variation::join('products as p', 'variations.product_id', 'p.id')
                        ->where('variations.id', $item->children_id)
                        ->select('p.clasification', 'p.enable_stock')
                        ->first();

                    if ($prod->clasification == 'product' && $prod->enable_stock == 1) {
                        $vld = VariationLocationDetails::where('variation_id', $item->children_id)
                            ->where('location_id', $location_id)
                            ->where('warehouse_id', $warehouse_id)
                            ->first();

                        $qty_available_reserved = floor(($vld->qty_available - $vld->qty_reserved) / $item->quantity);

                        if (is_null($formatted_qty_available) || $qty_available_reserved < $formatted_qty_available) {
                            $formatted_qty_available = $qty_available_reserved;
                            $qty_available = floor($vld->qty_available / $item->quantity);
                            $qty_reserved = floor($vld->qty_reserved / $item->quantity);
                        }
                    }
                }

                if ($reservation_id > 0) {
                    $product->formatted_qty_available = $this->productUtil->num_f($qty_available);
                } else {
                    $product->formatted_qty_available = $this->productUtil->num_f($formatted_qty_available);
                }

                $product->qty_available = $qty_available;
                $product->qty_reserved = $qty_reserved;

            } else {
                if ($reservation_id > 0) {
                    $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);

                } else {
                    $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available - $product->qty_reserved);
                }
            }

            // Tax percent added
            $product->tax_percent = $this->taxUtil->getTaxPercent($product->tax_id);

            $enabled_modules = $this->transactionUtil->allModulesEnabled();

            // Get lot number dropdown if enabled
            $lot_numbers = [];

            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($variation_id, $business_id, $location_id, true);
                
                foreach ($lot_number_obj as $lot_number) {
                    $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                    $lot_numbers[] = $lot_number;
                }
            }

            $product->lot_numbers = $lot_numbers;

            $business_details = $this->businessUtil->getDetails($business_id);

            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

            // Check if user is admin
            $user = User::find(request()->user()->id);
            $is_admin = $user->hasRole('Super Admin#' . $business_id);

            // Number of decimals in sales
            $product_settings = empty($business_details->product_settings) ? $this->businessUtil->defaultProductSettings() : json_decode($business_details->product_settings, true);
            $decimals_in_sales = $product_settings['decimals_in_sales'];

            return view('sale_pos.product_row')
                ->with(compact(
                    'product',
                    'row_count',
                    'enabled_modules',
                    'pos_settings',
                    'check_qty_available',
                    'reservation_id',
                    'is_admin',
                    'decimals_in_sales'
                ))
                ->render();
            
        } catch (\Exception $e) {
            \Log::emergency("File: " . $e->getFile(). " Line: " . $e->getLine(). " Message: " . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = __('lang_v1.item_out_of_stock');
        }

        return $output;
    }

    public function getPaymentRow($removable, $row_index, $payment_id)
    {
        $output = [];

        try {
            $business_id = request()->session()->get('user.business_id');

            $payment_types = $this->productUtil->payment_types();

            $payment_line = TransactionPayment::where('id', $payment_id)
                ->first()
                ->toArray();
            
            $accounts = $this->moduleUtil->accountsDropdown($business_id, true);
            
            $banks = Bank::where('business_id', $business_id)
                ->pluck('name', 'id');
            
            $pos = Pos::where('business_id', $business_id)
                ->pluck('name', 'id');

            // Show note field
            $show_note = false;
            $show_multiple_notes = true;

            return view('sale_pos.partials.payment_row')
                ->with(compact('business_id', 'payment_types', 'payment_line', 'accounts', 'banks', 'pos',
                    'removable', 'row_index', 'show_note', 'show_multiple_notes'))
                ->render();
            
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = __('lang_v1.item_out_of_stock');
        }

        return $output;
    }
}
