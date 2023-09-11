<?php

namespace App\Http\Controllers;

use App\AccountBusinessLocation;
use App\TaxRate;
use App\Transaction;
use App\TransactionSellLine;
use App\BusinessLocation;
use App\Business;
use App\User;
use App\Pos;
use App\Bank;
use App\BankAccount;
use App\PaymentTerm;
use App\Quote;
use App\Category;
use App\Brands;
use App\Cashier;
use App\CashierClosure;
use App\CashRegister;
use App\CashRegisterTransaction;
use App\Customer;
use App\Product;
use App\Warehouse;
use App\Variation;
use App\CustomerGroup;
use App\SellingPriceGroup;
use App\NotificationTemplate;
use App\Suplies;
use App\group_sub_products;
use App\VariationLocationDetails;
use App\DocumentType;
use App\Employees;
use App\DocumentCorrelative;
use App\Http\Controllers\Optics\GraduationCardHasDiagnosticController;
use App\KitHasProduct;
use App\MovementType;
use App\Optics\Diagnostic;
use App\Optics\ExternalLab;
use App\Optics\GraduationCard;
use App\Optics\LabOrder;
use App\Optics\LabOrderDetail;
use App\Optics\Patient;
use App\Optics\StatusLabOrder;
use App\PurchaseLine;
use App\TransactionKitSellLine;
use App\TransactionPayment;
use App\TypeEntrie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Utils\TaxUtil;
use App\Utils\CashierUtil;
use App\Utils\CashRegisterUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\Util;
use App\Utils\AccountingUtil;
use Yajra\DataTables\Facades\DataTables;

class SellPosController extends Controller
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
    protected $cashierUtil;
    protected $cashRegisterUtil;
    protected $moduleUtil;
    protected $notificationUtil;
    protected $util;
    protected $accountingUtil;

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
        CashierUtil $cashierUtil,
        CashRegisterUtil $cashRegisterUtil,
        ModuleUtil $moduleUtil,
        NotificationUtil $notificationUtil,
        Util $util,
        AccountingUtil $accountingUtil
    ) {

        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->taxUtil = $taxUtil;
        $this->cashierUtil = $cashierUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moduleUtil = $moduleUtil;
        $this->notificationUtil = $notificationUtil;
        $this->util = $util;
        $this->accountingUtil = $accountingUtil;

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
        /** Business types */
        $this->business_type = ['small_business', 'medium_business', 'large_business'];
        /** Payment conditions */
        $this->payment_conditions = ['cash', 'credit'];

        // Short names of document types
        if (config('app.business') == 'optics') {
            $this->document_names = ['FACTURA', 'CCF'];

        } else {
            $this->document_names = ['FCF', 'CCF'];
        }

        // Payment note short name
        $this->note_name = 'NA';

        // Binnacle data
        $this->module_name = 'sale';
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
        
        return view('sale_pos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('HomeController@index'));

        } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
        }
        
        // Check if there is a open register, if no then redirect to create register screen.
        if (config('app.business') == 'optics') {
            if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
                return redirect()->action('CashRegisterController@create');
            }

        } else {
            if ($this->cashierUtil->countOpenedCashier() == 0) {
                return redirect()->action('CashRegisterController@create');
            }
        }

        // $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        $walk_in_customer = $this->contactUtil->getDefaultCustomer($business_id);
        
        $business_details = $this->businessUtil->getDetails($business_id);
        
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);
        
        $payment_types = $this->productUtil->payment_types();

        $payment_lines[] = $this->dummyPaymentLine;

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $default_location = null;

        if (count($business_locations) == 1) {
            foreach ($business_locations as $id => $name) {
                $default_location = $id;
            }
        }

        // Shortcuts
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);
        
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        
        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];

        if ($commsn_agnt_setting == 'user') {
            if (config('app.business') == 'optics') {
                $commission_agent = User::forDropdownAllBusiness();
                
            } else {
                $commission_agent = User::forDropdown($business_id, false);
            }

        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            if (config('app.business') == 'optics') {
                $commission_agent = User::saleCommissionAgentsDropdownAllBusiness();

            } else {
                $commission_agent = User::saleCommissionAgentsDropdown($business_id, false);
            }
        }

        $categories = Category::catAndSubCategories($business_id);

        $brands = Brands::where('business_id', $business_id)
        ->pluck('name', 'id');
        $brands->prepend(__('lang_v1.all_brands'), 'all');

        // Creando el select para tipo documentos
        $documents =  DocumentType::where('business_id',$business_id)
        ->where('is_active', 1)
        ->where('is_document_sale', 1)
        ->select('short_name', 'tax_inc', 'tax_exempt', 'id', 'is_default')
        ->get();

        $default =  DocumentType::where('business_id',$business_id)
        ->where('is_active', 1)
        ->where('is_default', 1)
        ->select('id')
        ->first();
        
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

        // Llenar Select de Vendedores
        $employees_sales = Employees::forDropdown(($business_id));

        // Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        // Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);
        
        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');
        
        /** Business type */
        $business_type = $this->business_type;
        
        /** Payment conditions */
        $payment_conditions = $this->payment_conditions;
        
        /** Warehouses */
        $warehouses = Warehouse::forDropdown($business_id, false);
        
        /** Cashiers */
        $cashiers = Cashier::forDropdown($business_id, false);
        
        /** Banks */
        $banks = Bank::where('business_id', $business_id)
        ->pluck('name', 'id');

        /** Bank account */
        $bank_accounts = BankAccount::pluck('name', 'id');
        
        /** Pos */
        $pos = Pos::forDropdown($business_id);

        // FCF document
        $fcf_document = DocumentType::where('short_name', $this->document_names[0])->first();

        // CCF document
        $ccf_document = DocumentType::where('short_name', $this->document_names[1])->first();

        $cashier_closure_id = request()->input('cashier_closure_id', null);

        // Default warehouse
        $default_warehouse = null;

        if (count($warehouses) == 1) {
            foreach ($warehouses as $id => $name) {
                $default_warehouse = $id;
            }
        }

        // Default cashier
        $default_cashier = null;

        if (count($cashiers) == 1) {
            foreach ($cashiers as $id => $name) {
                $default_cashier = $id;
            }
        }

        // Number of decimals in sales
        $product_settings = empty($business_details->product_settings) ? $this->businessUtil->defaultProductSettings() : json_decode($business_details->product_settings, true);
        $decimals_in_sales = $product_settings['decimals_in_sales'];

        // Check if user is admin
        $user = User::find(request()->user()->id);
        $is_admin = $user->hasRole('Super Admin#' . $business_id);

        $business_q = Business::findOrFail($business_id);
        $type_discount = $business_q->type_discount;
        $max_discount = $business_q->limit_discount;

        if (config('app.business') == 'optics') {
            // Patients
            $patients = Patient::where('business_id', $business_id)
            ->pluck('full_name', 'id');

            // Status lab orders
            $status_lab_orders = StatusLabOrder::where('business_id', $business_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

            // External labs
            $external_labs = ExternalLab::where('business_id', $business_id)
            ->pluck('name', 'id');

            // Products
            $products = Product::where('business_id', $business_id)
            ->pluck('name', 'id');

            // Lab order code
            $code = $this->util->generateLabOrderCode();

            // Payment note document
            $payment_note_id = DocumentType::where('business_id', $business_id)
            ->where('short_name', $this->note_name)
            ->first()
            ->id;

            // Show note field
            $show_note = true;
            $show_multiple_notes = false;

            

            return view('sale_pos.create')
            ->with(compact(
                'type_discount',
                'max_discount',
                'business_details',
                'taxes',
                'payment_types',
                'walk_in_customer',
                'cashier_closure_id',
                'payment_lines',
                'business_locations',
                'bl_attributes',
                'default_location',
                'shortcuts',
                'commission_agent',
                'categories',
                'brands',
                'pos_settings',
                'change_return',
                'types',
                'customer_groups',
                'employees_sales',
                'accounts',
                'price_groups',
                'documents',
                'tax_groups',
                'business_type',
                'payment_conditions',
                'warehouses',
                'cashiers',
                'banks',
                'bank_accounts',
                'pos',
                'default',
                'fcf_document',
                'ccf_document',
                'patients',
                'status_lab_orders',
                'external_labs',
                'products',
                'code',
                'default_cashier',
                'payment_note_id',
                'show_note',
                'show_multiple_notes',
                'is_admin',
                'default_warehouse',
                'decimals_in_sales'
            ));

        } else {
            return view('sale_pos.create')
            ->with(compact(
                'type_discount',
                'max_discount',
                'business_details',
                'taxes',
                'payment_types',
                'walk_in_customer',
                'cashier_closure_id',
                'payment_lines',
                'business_locations',
                'bl_attributes',
                'default_location',
                'shortcuts',
                'commission_agent',
                'categories',
                'brands',
                'pos_settings',
                'change_return',
                'types',
                'customer_groups',
                'employees_sales',
                'accounts',
                'price_groups',
                'documents',
                'tax_groups',
                'business_type',
                'payment_conditions',
                'warehouses',
                'cashiers',
                'banks',
                'bank_accounts',
                'pos',
                'default',
                'is_admin',
                'fcf_document',
                'ccf_document',
                'default_warehouse',
                'default_cashier',
                'decimals_in_sales'
            ));
        }
    }

    public function getCorrelatives(){
        $location_id = request()->input('location_id');
        $document_type = request()->input('document_type');
        $business_id = request()->session()->get('user.business_id');
        
        $correlatives = DocumentCorrelative::where('document_type_id', $document_type)
        ->where('location_id', $location_id)
        ->where('status', 'active')
        ->whereRaw('initial <= final')
        ->where('business_id', $business_id)
        ->select('actual')
        ->first();

        if(!empty($correlatives->actual)){
            return $correlatives->actual;
        }else{
            return "0";
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('sell.create') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $is_direct_sale = false;

        if (!empty($request->input('is_direct_sale'))) {
            $is_direct_sale = true;
        }

        // Check if there is a open register, if no then redirect to create register screen.
        if (config('app.business') == 'optics') {
            if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
                return redirect()->action('CashRegisterController@create');
            }

        } else {
            if (!$is_direct_sale && $this->cashierUtil->countOpenedCashier() == 0) {
                return redirect()->action('CashRegisterController@create');
            }
        }

        $correlative_valid = $this->validateCorrelative(
            $request->input('location_id'),
            $request->input('documents'),
            $request->input('correlatives')
        );

        if($correlative_valid['flag'] == true) {
            return ['success' => 0, 'msg' => __('sale.correlative_exists')];
        }

        try {
            $input = $request->except('_token');

            // Status is send as quotation from Add sales screen.
            if ($input['status'] == 'quotation') {
                $input['status'] = 'draft';
                $input['is_quotation'] = 1;
            }

            if (!empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');

                // Check if subscribed or not, then check for users quota
                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse();

                } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                    return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
                }

                $user_id = $request->session()->get('user.id');
                $commsn_agnt_setting = $request->session()->get('business.sales_cmsn_agnt');

                DB::beginTransaction();
                
                if (empty($request->input('transaction_date'))) {
                    $input['transaction_date'] =  \Carbon::now();

                } else {
                    $trans_time = session('business.time_format') == 12 ? date('h:i A') : date('H:i');
                    $trans_date = substr($request->input('transaction_date'), 0, 10); // Get date only
                    $transaction_date = $trans_date. " " . $trans_time;
                    $input['transaction_date'] = $this->productUtil->uf_date($transaction_date, true);
                }

                if ($is_direct_sale) {
                    $input['is_direct_sale'] = 1;
                }

                if (config('app.business') == 'optics') {
                    // Get commission agent from employee id
                    $commission_agent = Employees::where('id', $request->input('commission_agent'))->first();
                    $input['commission_agent'] = ! empty($commission_agent) ? $commission_agent->user_id : null;

                } else {
                    $input['commission_agent'] = !empty($request->input('commission_agent')) ? $request->input('commission_agent') : null;
                }

                if ($commsn_agnt_setting == 'logged_in_user') {
                    $input['commission_agent'] = $user_id;
                }

                if (isset($input['exchange_rate']) && $this->transactionUtil->num_uf($input['exchange_rate']) == 0) {
                    $input['exchange_rate'] = 1;
                }

                //Customer group details
                $customer_id = $request->get('customer_id', null);
                $cg = $this->contactUtil->getCustomerGroup($business_id, $customer_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;

                // Set selling price group id
                if ($request->has('price_group')) {
                    $input['selling_price_group_id'] = $request->input('price_group');
                }   
                
                // Set documents
                if ($request->has('documents')) {
                    $input['document_types_id']  = $request->input('documents');
                    $input['correlative'] = $request->input('correlatives');
                }                

                $input['is_suspend'] = isset($input['is_suspend']) && 1 == $input['is_suspend']  ? 1 : 0;

                if ($input['is_suspend']) {
                    $input['sale_note'] = !empty($input['additional_notes']) ? $input['additional_notes'] : null;
                }

                /** 0: paid, 1:credit, 2:partial */
                $is_credit = $request->input('is_credit');
                $input['payment_condition'] = $is_credit == '1' || $is_credit == '2' ? 'credit' : 'cash';

                $document_correlative = DocumentCorrelative::where('document_correlatives.business_id', $business_id)
                ->where('document_correlatives.location_id', $input['location_id'])
                ->whereRaw('document_correlatives.initial <= document_correlatives.final')
                ->where('document_correlatives.document_type_id', $input['document_types_id'])
                ->where('document_correlatives.status', 'active')
                ->first();

                // To check that the correlative is unique
                $input['serie'] = $document_correlative ? $document_correlative->serie : 0;
                $input['resolution'] = $document_correlative ? $document_correlative->resolution : 0;
                $input['document_correlative_id'] = $document_correlative ? $document_correlative->id : null;

                if (config('app.business') != 'optics') {
                    $input['cashier_closure_id'] = $this->cashierUtil->getCashierClosureActive($input['cashier_id']);
                } else {
                    $input['cashier_closure_id'] = null;
                }

                $transaction = $this->transactionUtil->createSellTransaction(
                    $business_id,
                    $input,
                    null,
                    // $invoice_total,
                    $user_id
                );
                
                // Store binnacle
                $reference = ! empty($transaction->document_type) ? $transaction->document_type->short_name . ' ' . $transaction->correlative : $transaction->correlative;

                $this->transactionUtil->registerBinnacle(
                    $this->module_name,
                    'create',
                    $reference,
                    $transaction
                );

                /** Update order's status if exists **/
                if ($request->input("order_id")) {
                    $quote = Quote::find($request->input("order_id"));

                    if (!empty($quote)) {
                        $quote->transaction_id = $transaction->id;
                        $quote->invoiced = true;
                        $quote->save();
                    }
                }

                if (!empty($document_correlative)) {
                    if ($document_correlative->actual < $document_correlative->final) {
                        $document_correlative->actual += 1;
                        $document_correlative->save();

                    } else if ($document_correlative->actual == $document_correlative->final) {
                        $document_correlative->status = 'inactive';
                        $document_correlative->save();
                    }

                } else {
                    $output = [
                        'success' => 0,
                        'msg' => trans("messages.correlative_not_available")
                    ];

                    return $output;
                }

                // Update reservation status if exists
                if ($request->input('reservation_id')) {
                    $quote = Quote::find($request->input("reservation_id"));

                    // Clone record before action
                    $quote_old = clone $quote;

                    if (! empty($quote)) {
                        $quote->transaction_id = $transaction->id;
                        $quote->invoiced = true;
                        $quote->save();

                        // Store binnacle
                        $this->transactionUtil->registerBinnacle(
                            'reservation',
                            'update',
                            $quote->quote_ref_no,
                            $quote_old,
                            $quote
                        );
                    }

                // Update payment note correlative
                } else if (config('app.business') == 'optics') {
                    // Sale settings
                    $business = Business::find($business_id);
                    $sale_settings = empty($business->sale_settings) ? null : json_decode($business->sale_settings, true);
                    $no_note_full_payment = is_null($sale_settings) ? 0 : $sale_settings['no_note_full_payment'];

                    if (count($input['payment']) > 1 || $input['payment'][0]['amount'] > 0) {
                        $payment_total = 0;

                        foreach ($input['payment'] as $payment) {
                            $payment_total += $this->transactionUtil->num_uf($payment['amount']);
                        }

                        if ($payment_total != $transaction->final_total || $no_note_full_payment == 0) {
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

                        } else if ($payment_total == $transaction->final_total && $no_note_full_payment == 1 && isset($input['note'])) {
                            $input['note'] = null;
                        }
                    }
                }

                $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id']);
                
                if (!$is_direct_sale) {
                    // Add change return
                    $change_return = $this->dummyPaymentLine;
                    $change_return['amount'] = $input['change_return'];
                    $change_return['is_return'] = 1;
                    $input['payment'][] = $change_return;
                }

                if (!$transaction->is_suspend && ($is_credit == "0" || $is_credit == "2")) {
                    $this->transactionUtil->createOrUpdatePaymentLines(
                        $transaction,
                        $input['payment'],
                        null,
                        null,
                        isset($input['note']) ? $input['note'] : null
                    );
                }

                if ($this->transactionUtil->isModuleEnabled('tables')) {
                    $transaction->res_table_id = request()->get('res_table_id');
                    $transaction->save();
                }

                if ($this->transactionUtil->isModuleEnabled('service_staff')) {
                    $transaction->res_waiter_id = request()->get('res_waiter_id');
                    $transaction->save();
                }

                // Save tax and payment amounts
                $this->transactionUtil->saveTaxAndPayment($transaction);

                // Check for final and do some processing.
                if ($transaction->status == 'final') {
                    $trans =
                    Transaction::where("id", $transaction->id)
                    ->with("sell_lines")
                    ->first();

                    // Update product stock
                    foreach ($trans->sell_lines as $tsl) {
                        $id = $tsl->product_id;
                        $product_q = Product::where('id', $id)->first();
                        $clasification = $product_q->clasification;

                        if ($clasification == 'kits') {
                            $childrens = KitHasProduct::where('parent_id', $id)->get();

                            $business = request()->session()->get('business');

                            $location = [
                                "business_id" => $request->session()->get('user.business_id'),
                                "location_id" => $transaction->location_id,
                                "warehouse_id" => $transaction->warehouse_id,
                                "enable_product_expiry" => $business['enable_product_expiry'],
                                "on_product_expiry" => $business['on_product_expiry'],
                                "accounting_method" => $request->session()->get('business.accounting_method'),
                                "stop_selling_before" => $request->session()->get('business.stop_selling_before'),
                            ];

                            foreach ($childrens as $item) {
                                $variation_q = Variation::where('id', $item->children_id)->first();

                                $this->productUtil->decreaseProductQuantity(
                                    $variation_q->product_id,
                                    $item->children_id,
                                    $transaction->location_id,
                                    $this->productUtil->num_uf($item->quantity * $tsl->quantity),
                                    0,
                                    $transaction->warehouse_id
                                );

                                $quantity = $item->quantity * $tsl->quantity;
                                $transaction_kit_sell_line_details['transaction_id'] = $transaction->id;
                                $transaction_kit_sell_line_details['variation_id'] = $item->children_id;
                                $transaction_kit_sell_line_details['quantity'] = $quantity;

                                $this->transactionUtil->mapPurchaseSellKit($item->children_id, $tsl->id, $quantity, $location);
                                $transactionKitSellLine = TransactionKitSellLine::create($transaction_kit_sell_line_details);
                            }

                        } elseif ($clasification == 'product' || $clasification == 'material') {

                            $this->productUtil->decreaseProductQuantity(
                                $tsl->product_id,
                                $tsl->variation_id,
                                $transaction->location_id,
                                $this->productUtil->num_uf($tsl->quantity),
                                0,
                                $transaction->warehouse_id
                            );
                        }
                    }
                    
                    // Data to create or update kardex lines
                    $sell_lines = TransactionSellLine::where('transaction_id', $transaction->id)->get();

                    $movement_type = MovementType::where('name', 'sell')
                    ->where('type', 'output')
                    ->where('business_id', $business_id)
                    ->first();

                    // Check if movement type is set else create it
                    if (empty($movement_type)) {
                        $movement_type = MovementType::create([
                            'name' => 'sell',
                            'type' => 'output',
                            'business_id' => $business_id
                        ]);
                    }

                    $reference = $transaction->document_type->short_name . $transaction->correlative;

                    // Store kardex
                    $this->transactionUtil->createOrUpdateOutputLines($movement_type, $transaction, $reference, $sell_lines);

                    // Update reserved quantity
                    if ($request->input('reservation_id')) {
                        $reservation = Quote::find($request->input('reservation_id'));

                        foreach ($reservation->quote_lines as $line) {
                            $variation = Variation::find($line->variation_id);
                            $product = Product::find($variation->product_id);

                            if ($product->clasification == 'kits') {
                                $childrens = KitHasProduct::where('parent_id', $product->id)->get();

                                foreach ($childrens as $item) {
                                    $variation_q = Variation::find($item->children_id);

                                    $this->productUtil->updateProductQtyReserved(
                                        $reservation->location_id,
                                        $variation_q->product_id,
                                        $item->children_id,
                                        0,
                                        $this->productUtil->num_uf($item->quantity * $line->quantity),
                                        null,
                                        $reservation->warehouse_id
                                    );
                                }

                            } else if ($product->clasification == 'product' || $product->clasification == 'material') {
                                $this->productUtil->updateProductQtyReserved(
                                    $reservation->location_id,
                                    $product->id,
                                    $variation->id,
                                    0,
                                    $this->productUtil->num_uf($line->quantity),
                                    null,
                                    $reservation->warehouse_id
                                );
                            }
                        }
                    }

                    // Update payment status
                $status = $this->transactionUtil->updatePaymentStatus($transaction->id/*, $transaction->final_total*/);

                $sale_accounting_entry = Business::find($business_id)->value('sale_accounting_entry_mode');
                if ($sale_accounting_entry == 'transaction') {
                    /** generate sale accounting entry */
                    $this->createTransAccountingEntry($transaction->id);
                }

                    // Add payments to cash register
                if (config('app.business') == 'optics') {
                    if (! $is_direct_sale && ! $transaction->is_suspend && empty($request->input('reservation_id'))) {
                        $payment_lines = $this->transactionUtil->getPaymentDetails($transaction->id);

                            // If some payment exists
                        if (!empty($payment_lines)) {
                            $this->cashRegisterUtil->addSellPayments($transaction, $payment_lines);
                        }

                            // Add credit sell to cash register
                        if ($status != 'paid') {
                            $total_paid = $this->transactionUtil->getTotalPaid($transaction->id);

                            $this->cashRegisterUtil->addCreditSellPayment($transaction, $total_paid, $transaction->final_total);
                        }
                    }
                }

                    //Allocate the quantity from purchase and add mapping of
                    //purchase & sell lines in
                    //transaction_sell_lines_purchase_lines table
                $business = [
                    'id' => $business_id,
                    'accounting_method' => $request->session()->get('business.accounting_method'),
                    'location_id' => $input['location_id']
                ];

                $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');

                    // Auto send notification
                $this->notificationUtil->autoSendNotification($business_id, 'new_sale', $transaction, $transaction->contact);
            }

            DB::commit();

            $msg = '';
            $receipt = '';

            if ($input['status'] == 'draft' && $input['is_quotation'] == 0) {
                $msg = trans("sale.draft_added");

            } elseif ($input['status'] == 'draft' && $input['is_quotation'] == 1) {
                $msg = trans("lang_v1.quotation_added");

                if (!$is_direct_sale) {
                    $receipt = $this->receiptContent($transaction->type, $business_id, $input['location_id'], $transaction->id);

                } else {
                    $receipt = '';
                }

            } elseif ($input['status'] == 'final') {
                $msg = trans("sale.pos_sale_added");

                if (!$is_direct_sale && !$transaction->is_suspend) {
                    $receipt = $this->receiptContent($transaction->type, $business_id, $input['location_id'], $transaction->id);

                } else {
                    $receipt = '';
                }
            }

            if (config('app.business') == 'optics') {
                $show_modal = true;

                $output = [
                    'success' => 1,
                    'msg' => $msg,
                        // 'receipt' => $receipt,
                    'transaction_id' => $transaction->id,
                    'show_modal' => $show_modal
                ];

            } else {
                $output = [
                    'success' => 1,
                    'msg' => $msg,
                    'receipt' => $receipt
                ];
            }

        } else {
            $output = [
                'success' => 0,
                'msg' => trans("messages.something_went_wrong")
            ];
        }

    } catch (\Exception $e) {
        DB::rollBack();

        if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
            $msg = $e->getMessage();

        } else {
            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            $msg = trans("messages.something_went_wrong");
        }

        $output = [
            'success' => 0,
            'msg' => "File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage()
        ];
    }

    if (!$is_direct_sale) {
        return $output;

    } else {
        if ($input['status'] == 'draft') {
            if (isset($input['is_quotation']) && $input['is_quotation'] == 1) {
                return redirect()
                ->action('SellController@getQuotations')
                ->with('status', $output);

            } else {
                return redirect()
                ->action('SellController@getDrafts')
                ->with('status', $output);
            }

        } else {
            return redirect('sells')->with('status', $output);
        }
    }
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
        $transaction_type,
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null
    ) {

        $output = ['is_enabled' => false,
        'print_type' => 'browser',
        'html_content' => null,
        'printer_config' => [],
        'data' => []
    ];

    $business_details = $this->businessUtil->getDetails($business_id);
    $location_details = BusinessLocation::find($location_id);

        //Check if printing of invoice is enabled or not.
    if ($location_details->print_receipt_on_invoice == 1) {
            //If enabled, get print type.
        $output['is_enabled'] = true;

        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);

            //Check if printer setting is provided.
        $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

        /** Get print format form document type */
        $print_format = $this->transactionUtil->getDocumentTypePrintFormat($transaction_id);
        if($transaction_type == "sell_return" || !$print_format){
            $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);
        } else{
            $receipt_details = $this->transactionUtil->getFormatDetails($transaction_id, $invoice_layout, $business_id, $location_details);
        }

        $receipt_details->currency = session('currency');

        // dd($print_format);

            //If print type browser - return the content, printer - return printer config data, and invoice format config
        if ($receipt_printer_type == 'printer') {
            $output['print_type'] = 'printer';
            $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
            $output['data'] = $receipt_details;
        } else {
                //$layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';
            if($print_format){
                $layout = 'sale_pos.receipts.' . $print_format;
            } else{
                $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';
            }

            $output['html_content'] = view($layout, compact('receipt_details'))->render();
        }
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
        if (!auth()->user()->can('sell.update')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');

        if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()->with('status', [
                'success' => 0,
                'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])
            ]);
        }

        // Check if there is a open register, if no then redirect to create register screen.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->action('CashRegisterController@create');
        }
        
        // Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', [
                'success' => 0,
                'msg' => __('lang_v1.return_exist')
            ]);
        }

        $business_id = request()->session()->get('user.business_id');
        // $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        $walk_in_customer = $this->contactUtil->getDefaultCustomer($business_id);
        
        $business_details = $this->businessUtil->getDetails($business_id);

        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $payment_types = $this->productUtil->payment_types();

        $transaction = Transaction::join('document_types', 'document_types.id', '=', 'transactions.document_types_id')
        ->where('transactions.business_id', $business_id)
        ->where('type', 'sell')
        ->select(
            'transactions.*',
            'document_types.document_name as doc_name',
            'document_types.tax_inc',
            'document_types.tax_exempt'
        )
        ->findOrFail($id);

        $location_id = $transaction->location_id;
        $location_printer_type = BusinessLocation::find($location_id)->receipt_printer_type;

        $sell_details = TransactionSellLine::join('products AS p', 'transaction_sell_lines.product_id', 'p.id')
        ->join('variations AS variations', 'transaction_sell_lines.variation_id', 'variations.id')
        ->join('product_variations AS pv', 'variations.product_variation_id', 'pv.id')
        ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
            $join->on('variations.id', '=', 'vld.variation_id')
            ->where('vld.location_id', '=', $location_id);
        })
        ->leftjoin('units', 'units.id', 'p.unit_id')
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
            'transaction_sell_lines.unit_price_before_discount as default_sell_price',
            'transaction_sell_lines.unit_price_before_discount as unit_price_before_discount',
            'transaction_sell_lines.unit_price as sell_price_inc_tax',
            'transaction_sell_lines.id as transaction_sell_lines_id',
            'transaction_sell_lines.quantity as quantity_ordered',
            'transaction_sell_lines.sell_line_note as sell_line_note',
            'transaction_sell_lines.parent_sell_line_id',
            'transaction_sell_lines.lot_no_line_id',
            'transaction_sell_lines.line_discount_type',
            'transaction_sell_lines.line_discount_amount',
            DB::raw('vld.qty_available + transaction_sell_lines.quantity AS qty_available'),
            'p.tax as tax_id'
        )
        ->get();

        if (!empty($sell_details)) {
            foreach ($sell_details as $key => $value) {
                // If modifier sell line then unset
                if (!empty($sell_details[$key]->parent_sell_line_id)) {
                    unset($sell_details[$key]);

                } else {
                    $sell_details[$key]->formatted_qty_available = $this->productUtil->num_f($value->qty_available);

                    // Add available lot numbers for dropdown to sell lines
                    $lot_numbers = [];

                    if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                        $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($value->variation_id, $business_id, $location_id);
                        
                        foreach ($lot_number_obj as $lot_number) {
                            // If lot number is selected added ordered quantity to lot quantity available
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

                // Tax percent
                if (! empty($sell_details[$key]->tax_id)) {
                    $sell_details[$key]->tax_percent = $this->taxUtil->getTaxPercent($sell_details[$key]->tax_id);
                }
            }
        }

        $payment_lines = $this->transactionUtil->getPaymentDetails($id);
        
        // If no payment lines found then add dummy payment line
        if (empty($payment_lines)) {
            $payment_lines[] = $this->dummyPaymentLine;
        }

        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;

        $commission_agent = [];

        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id, false);

        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
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

        // Llenar Select de Vendedores
        $employees_sales = Employees::forDropdown(($business_id));
        
        // Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);
        
        // Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);
        
        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');
        
        /** Business type */
        $business_type = $this->business_type;
        
        /** Payment conditions */
        $payment_conditions = $this->payment_conditions;

        // Llenar Select de Vendedores
        $employees_sales = Employees::forDropdown(($business_id));
        
        $pos = Pos::where('business_id', $business_id)->where('id', $id)
        ->pluck('name', 'id');
        
        $banks = Bank::where('business_id', $business_id)
        ->pluck('name', 'id');

        // Document data
        $doc_tax_inc = $transaction->tax_inc;
        $doc_tax_exempt = $transaction->tax_exempt;

        // Number of decimals in sales
        $product_settings = empty($business_details->product_settings) ? $this->businessUtil->defaultProductSettings() : json_decode($business_details->product_settings, true);
        $decimals_in_sales = $product_settings['decimals_in_sales'];

        // Check if user is admin
        $user = User::find(request()->user()->id);
        $is_admin = $user->hasRole('Super Admin#' . $business_id);

        if (config('app.business') == 'optics') {
            // Document types
            $documents =  DocumentType::where('business_id',$business_id)
            ->where('is_active', 1)
            ->select('short_name', 'tax_inc', 'id')
            ->get();

            // Customer
            $customer = Customer::find($transaction->customer_id);

            // Patient
            $patient = Patient::join('lab_orders as lo', 'lo.patient_id', 'patients.id')
            ->join('transactions as t', 't.id', 'lo.transaction_id')
            ->where('lo.transaction_id', $transaction->id)
            ->select('patients.*')
            ->first();

            // Check if it's editing
            $is_edit = true;

            // Check if it's a quote
            $is_quote = false;

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
                'pos',
                'banks',
                'doc_tax_inc',
                'doc_tax_exempt',
                'documents',
                'customer',
                'patient',
                'is_edit',
                'is_quote',
                'is_admin',
                'decimals_in_sales'
            ));

        } else {
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
                'pos',
                'banks',
                'is_admin',
                'doc_tax_inc',
                'doc_tax_exempt',
                'decimals_in_sales'
            ));
        }
    }

    /**
     * Update the specified resource in storage.
     * TODO: Add edit log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('sell.update') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $input = $request->except('_token');

            // Status is send as quotation from edit sales screen.
            if ($input['status'] == 'quotation') {
                $input['status'] = 'draft';
                $input['is_quotation'] = 1;
            }

            $is_direct_sale = false;

            if (!empty($input['products'])) {
                // Get transaction value before updating.
                $transaction_before = Transaction::find($id);
                $status_before =  $transaction_before->status;

                if ($transaction_before->is_direct_sale == 1) {
                    $is_direct_sale = true;
                }

                $business_id = $request->session()->get('user.business_id');

                $business_details = $this->businessUtil->getDetails($business_id);
                $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

                if ($pos_settings['partial_payment_any_customer'] == 0) {
                    // Check Customer credit limit
                    $is_credit_limit_exeeded = $this->transactionUtil->isCustomerCreditLimitExeeded($input, $id);

                    if ($is_credit_limit_exeeded !== false) {
                        $credit_limit_amount = $this->transactionUtil->num_f($is_credit_limit_exeeded, true);

                        $output = [
                            'success' => 0,
                            'msg' => __('lang_v1.cutomer_credit_limit_exeeded', ['credit_limit' => $credit_limit_amount])
                        ];

                        if (!$is_direct_sale) {
                            return $output;
                        } else {
                            return redirect('sells')->with('status', $output);
                        }
                    }
                }

                // Check if there is a open register, if no then redirect to Create Register screen.
                if (config('app.business') == 'optics') {
                    if (!$is_direct_sale && $this->cashRegisterUtil->countOpenedRegister() == 0) {
                        return redirect()->action('CashRegisterController@create');
                    }

                } else {
                    if (!$is_direct_sale && $this->cashierUtil->countOpenedCashier() == 0) {
                        return redirect()->action('CashRegisterController@create');
                    }
                }

                $user_id = $request->session()->get('user.id');
                $commsn_agnt_setting = $request->session()->get('business.sales_cmsn_agnt');

                $discount = [
                    'discount_type' => $input['discount_type'],
                    'discount_amount' => $input['discount_amount']
                ];

                // $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);

                if (!empty($request->input('transaction_date'))) {
                    $input['transaction_date'] = $this->productUtil->uf_date($request->input('transaction_date'), true);
                }

                $input['commission_agent'] = !empty($request->input('commission_agent')) ? $request->input('commission_agent') : null;

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

                $input['is_suspend'] = isset($input['is_suspend']) && 1 == $input['is_suspend']  ? 1 : 0;

                if ($input['is_suspend']) {
                    $input['sale_note'] = !empty($input['additional_notes']) ? $input['additional_notes'] : null;
                }

                if (config('app.business') != 'optics') {
                    $document_correlative = DocumentCorrelative::where('document_correlatives.business_id', $business_id)
                    ->where('document_correlatives.location_id', $input['location_id'])
                    ->whereRaw('document_correlatives.initial <= document_correlatives.final')
                    ->where('document_correlatives.document_type_id', $input['documents'])
                    ->where('document_correlatives.status', 'active')
                    ->first();

                    $input['serie'] = $document_correlative ? $document_correlative->serie : 0;
                    $input['resolution'] = $document_correlative ? $document_correlative->resolution : 0;
                    $input['document_correlative_id'] = ! empty($document_correlative) ? $document_correlative->id : null;
                }

                // 0: paid, 1:credit, 2:partial
                $is_credit = $request->input('is_credit');
                $input['payment_condition'] = $is_credit == '1' || $is_credit == '2' ? 'credit' : 'cash';

                // Begin transaction
                DB::beginTransaction();

                // Clone record before action
                $transaction_old = clone $transaction_before;

                $transaction = $this->transactionUtil
                ->updateSellTransaction(
                    $id,
                    $business_id,
                    $input,
                    null,
                        // $invoice_total,
                    $user_id
                );

                // Store binnacle
                $reference = ! empty($transaction->document_type) ? $transaction->document_type->short_name . ' ' . $transaction->correlative : $transaction->correlative;

                $this->transactionUtil->registerBinnacle(
                    $this->module_name,
                    'update',
                    $reference,
                    $transaction_old,
                    $transaction
                );

                // Data to create or update kardex lines
                $sell_lines_before = TransactionSellLine::where('transaction_id', $transaction->id)->get();

                // Update Sell lines
                $deleted_lines = $this->transactionUtil->createOrUpdateSellLines(
                    $transaction,
                    $input['products'],
                    $input['location_id'],
                    true,
                    $status_before
                );

                // Update update lines
                if (!$is_direct_sale && !$transaction->is_suspend) {
                    // Add change return
                    $change_return = $this->dummyPaymentLine;
                    $change_return['amount'] = $input['change_return'];
                    $change_return['is_return'] = 1;
                    
                    if (!empty($input['change_return_id'])) {
                        $change_return['id'] = $input['change_return_id'];
                    }

                    $input['payment'][] = $change_return;

                    $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment']);

                    // Update cash register
                    // $this->cashRegisterUtil->updateSellPayments($status_before, $transaction, $input['payment']);
                }

                // Update payment status
                $status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

                if (config('app.business') == 'optics') {
                    if (! $is_direct_sale && ! $transaction->is_suspend) {
                        // Get Cash register ID
                        $cash_register = CashRegisterTransaction::where('transaction_id', $transaction->id)->first();

                        if (empty($cash_register)) {
                            $quote = Quote::where('transaction_id', $transaction->id)->first();

                            if (! empty($quote)) {
                                $cash_register = CashRegisterTransaction::where('quote_id', $quote->id)->first();
                            }
                        }

                        $cash_register_id = ! empty($cash_register->cash_register_id) ? $cash_register->cash_register_id : null;

                        // Delete cash register transactions
                        DB::table('cash_register_transactions')->where('transaction_id', $transaction->id)->delete();

                        // Add payments to cash register
                        $payment_lines = $this->transactionUtil->getPaymentDetails($transaction->id);

                        // If some payment exists
                        if (! empty($payment_lines)) {
                            $this->cashRegisterUtil->addSellPayments($transaction, $payment_lines, $cash_register_id);
                        }

                        // Add credit sell to cash register
                        if ($status != 'paid') {
                            $total_paid = $this->transactionUtil->getTotalPaid($transaction->id);

                            $this->cashRegisterUtil->addCreditSellPayment($transaction, $total_paid, $transaction->final_total);
                        }
                    }
                }

                // Update product stock
                $this->productUtil->adjustProductStockForInvoice($status_before, $transaction, $input);

                // Data to create or update output lines
                $sell_lines = TransactionSellLine::where('transaction_id', $transaction->id)->get();

                $movement_type = MovementType::where('name', 'sell')
                ->where('type', 'output')
                ->where('business_id', $business_id)
                ->first();

                // Check if movement type is set else create it
                if (empty($movement_type)) {
                    $movement_type = MovementType::create([
                        'name' => 'sell',
                        'type' => 'output',
                        'business_id' => $business_id
                    ]);
                }

                $reference = $transaction->document_type->short_name . $transaction->correlative;

                // Store kardex
                $this->transactionUtil->createOrUpdateOutputLines(
                    $movement_type,
                    $transaction,
                    $reference,
                    $sell_lines,
                    $sell_lines_before
                );

                // Allocate the quantity from purchase and add mapping of
                // purchase & sell lines in
                // transaction_sell_lines_purchase_lines table
                $business = [
                    'id' => $business_id,
                    'accounting_method' => $request->session()->get('business.accounting_method'),
                    'location_id' => $input['location_id']
                ];

                $this->transactionUtil->adjustMappingPurchaseSell($status_before, $transaction, $business, $deleted_lines);

                if ($this->transactionUtil->isModuleEnabled('tables')) {
                    $transaction->res_table_id = request()->get('res_table_id');
                    $transaction->save();
                }

                if ($this->transactionUtil->isModuleEnabled('service_staff')) {
                    $transaction->res_waiter_id = request()->get('res_waiter_id');
                    $transaction->save();
                }

                DB::commit();

                $msg = '';
                $receipt = '';

                if ($input['status'] == 'draft' && $input['is_quotation'] == 0) {
                    $msg = trans("sale.draft_added");

                } elseif ($input['status'] == 'draft' && $input['is_quotation'] == 1) {
                    $msg = trans("lang_v1.quotation_updated");

                    if (!$is_direct_sale) {
                        $receipt = $this->receiptContent($transaction->type, $business_id, $input['location_id'], $transaction->id);
                    } else {
                        $receipt = '';
                    }

                } elseif ($input['status'] == 'final') {
                    $msg = trans("sale.pos_sale_updated");

                    if (!$is_direct_sale && !$transaction->is_suspend) {
                        $receipt = $this->receiptContent($transaction->type, $business_id, $input['location_id'], $transaction->id);
                    } else {
                        $receipt = '';
                    }
                }

                if (config('app.business') == 'optics') {
                    $show_modal = false;
                    $output = [
                        'success' => 1,
                        'msg' => $msg,
                        'receipt' => $receipt,
                        'show_modal' => $show_modal
                    ];

                } else {
                    $output = [
                        'success' => 1,
                        'msg' => $msg,
                        'receipt' => $receipt
                    ];
                }

            } else {
                $output = [
                    'success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        if (!$is_direct_sale) {
            return $output;

        } else {
            if ($input['status'] == 'draft') {
                if (isset($input['is_quotation']) && $input['is_quotation'] == 1) {
                    return redirect()
                    ->action('SellController@getQuotations')
                    ->with('status', $output);

                } else {
                    return redirect()
                    ->action('SellController@getDrafts')
                    ->with('status', $output);
                }

            } else {
                return redirect('sells')->with('status', $output);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('sell.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                // Check if return exist then not allowed
                if ($this->transactionUtil->isReturnExist($id)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.return_exist')
                    ];

                    return $output;
                }

                $business_id = request()->session()->get('user.business_id');

                $transaction = Transaction::where('id', $id)
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->with(['sell_lines'])
                ->first();

                // Clone record before action
                $transaction_old = clone $transaction;

                // Begin transaction
                DB::beginTransaction();

                $order = Quote::where('transaction_id', $id)->first();

                if (!empty($order)) {
                    $order->transaction_id = null;
                    $order->invoiced = 0;
                    $order->save();
                }

                if (!empty($transaction)) {
                    // If status is draft direct delete transaction
                    if ($transaction->status == 'draft' || $transaction->status == 'annulled') {
                        $transaction->delete();

                    } else {
                        $deleted_sell_lines = $transaction->sell_lines;
                        $deleted_sell_lines_ids = $deleted_sell_lines->pluck('id')->toArray();

                        $this->transactionUtil->deleteSaleLines(
                            $deleted_sell_lines_ids,
                            $transaction->location_id,
                            $transaction->warehouse_id
                        );

                        // Delete kardex lines for sale
                        $this->transactionUtil->deleteKardexByTransaction($transaction->id);

                        if (config('app.business') == 'optics') {
                            // Stock adjusment for lab orders
                            $lab_orders = LabOrder::where('transaction_id', $transaction->id)->get();

                            foreach ($lab_orders as $lab_order) {
                                // Delete kardex lines for lab order
                                $this->transactionUtil->deleteKardexByLabOrder($lab_order->id);

                                $lod = LabOrderDetail::where('lab_order_id', $lab_order->id)->get();

                                foreach ($lod as $item) {
                                    $stock = VariationLocationDetails::where('variation_id', $item->variation_id)
                                    ->where('location_id', $item->location_id)
                                    ->where('warehouse_id', $item->warehouse_id)
                                    ->first();

                                    $stock->qty_available = $stock->qty_available + $item->quantity;

                                    $stock->save();

                                    $item->delete();
                                }

                                // Clone record before action
                                $lab_order_old = clone $lab_order;

                                $lab_order->delete();

                                // Store binnacle
                                $this->transactionUtil->registerBinnacle(
                                    'lab_order',
                                    'delete',
                                    $lab_order_old->no_order,
                                    $lab_order_old
                                );
                            }
                        }

                        $transaction->status = 'draft';
                        $business = [
                            'id' => $business_id,
                            'accounting_method' => request()->session()->get('business.accounting_method'),
                            'location_id' => $transaction->location_id
                        ];

                        $this->transactionUtil->adjustMappingPurchaseSell('final', $transaction, $business, $deleted_sell_lines_ids);

                        $transaction->delete();
                    }

                    // Store binnacle
                    $reference = ! empty($transaction_old->document_type) ? $transaction_old->document_type->short_name . ' ' . $transaction_old->correlative : $transaction_old->correlative;

                    $this->transactionUtil->registerBinnacle(
                        $this->module_name,
                        'delete',
                        $reference,
                        $transaction_old
                    );
                }

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.sale_delete_success')
                ];

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output['success'] = false;
                $output['msg'] = trans("messages.something_went_wrong");
            }

            return $output;
        }
    }

    public function annul($id){
        if (!auth()->user()->can('sell.annul')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                // Check if return exist then not allowed
                if ($this->transactionUtil->isReturnExist($id)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.return_exist')
                    ];

                    return $output;
                }

                $business_id = request()->session()->get('user.business_id');

                $transaction = Transaction::where('id', $id)
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->with(['sell_lines'])
                ->first();

                // Check if the cash register is open
                if (config('app.business') == 'optics') {
                    if ($transaction->type == 'sell') {
                        $register =  CashRegister::where('cashier_id', $transaction->cashier_id)
                        ->where('status', 'open')
                        ->first();

                        if (empty($register)) {
                            $output = [
                                'success' => false,
                                'msg' => __('cash_register.cash_register_not_opened')
                            ];

                            return $output;
                        }
                    }
                }

                /** Check if transaction come from order, if so, change invoiced column status */
                $order = Quote::where('transaction_id', $id)
                ->first();

                if (!empty($order)) {
                    $order->transaction_id = null;
                    $order->invoiced = 0;
                    $order->save();
                }

                DB::beginTransaction();

                if (!empty($transaction)) {
                    // Clone record before action
                    $transaction_old = clone $transaction;

                    $sell_lines = $transaction->sell_lines;

                    $deleted_sell_lines_ids = $sell_lines->pluck('id')->toArray();

                    /** Annul transactions lines */
                    foreach($sell_lines as $sl){
                        /** Adjust quantity */
                        $this->transactionUtil->adjustStock(
                            $transaction->location_id,
                            $transaction->warehouse_id,
                            $sl->product_id,
                            $sl->variation_id,
                            $sl->quantity
                        );
                    }

                    /** Delete payment transactions */
                    $this->transactionUtil->deletePaymentLines($transaction);

                    /** Delte tax transacions details */
                    $this->transactionUtil->deleteTransactionTaxDetail($sell_lines);

                    /** Refund sell on cash register */
                    $refund_sell = $this->cashRegisterUtil->refundSell($transaction);

                    if (!$refund_sell) {
                        return $output = [
                            'success' => false,
                            'msg' => __("cash_register.cash_register_not_opened")
                        ];
                    }

                    $business = [
                        'id' => $business_id,
                        'accounting_method' => request()->session()->get('business.accounting_method'),
                        'location_id' => $transaction->location_id
                    ];

                    $transaction->status = 'draft';

                    $this->transactionUtil->adjustMappingPurchaseSell('final', $transaction, $business, $deleted_sell_lines_ids);

                    // Delete kardex lines
                    $this->transactionUtil->deleteKardexByTransaction($transaction->id);

                    /** Annul transaction */
                    $transaction->status = "annulled";
                    $transaction->payment_status = null;
                    $transaction->save();

                    // Store binnacle
                    $reference = ! empty($transaction->document_type) ? $transaction->document_type->short_name . ' ' . $transaction->correlative : $transaction->correlative;

                    $this->transactionUtil->registerBinnacle(
                        $this->module_name,
                        'annul',
                        $reference,
                        $transaction_old,
                        $transaction
                    );

                    if (config('app.business') == 'optics') {
                        // Annul lab orders
                        $lab_orders = LabOrder::where('transaction_id', $transaction->id)->get();

                        if (! empty($lab_orders)) {
                            foreach ($lab_orders as $lab_order) {
                                // Clone record before action
                                $lab_order_old = clone $lab_order;

                                $lab_order->is_annulled = 1;
                                $lab_order->save();

                                // Store binnacle
                                $this->transactionUtil->registerBinnacle(
                                    $this->module_name,
                                    'annul',
                                    $lab_order->no_order,
                                    $lab_order_old,
                                    $lab_order
                                );
                            }
                        }
                    }
                }

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('sale.sale_annul_success')
                ];

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

                $output['success'] = false;
                $output['msg'] = trans("messages.something_went_wrong");
            }

            return $output;
        }
    }

    /**
     * Create transaction accounting entry
     * @param int $transaction_id
     */
    public function createTransAccountingEntry($transaction_id) {
        $transaction = Transaction::join('customers as c', 'transactions.customer_id', 'c.id')
        ->join('document_types as dt', 'transactions.document_types_id', 'dt.id')
        ->where('transactions.id', $transaction_id)
        ->select(
            'transactions.transaction_date as date',
            'transactions.location_id',
            DB::raw('IFNULL(c.business_name, c.name) as customer_name'),
            'dt.short_name as doc_type',
            'transactions.correlative'
        )->first();

        try {
            $date = $this->accountingUtil->format_date($transaction->date);
            $description = 'VENTA '. $transaction->doc_type . $transaction->correlative .' FECHA '. $date.' CLIENTE '. $transaction->customer_name ;

            $entry = [
                'date' => $this->transactionUtil->uf_date($date),
                'description' => $description,
                'short_name' => null,
                'business_location_id' => $transaction->location_id,
                'status_bank_transaction' => 1
            ];

            $entry['type_entrie_id'] = 
            TypeEntrie::where('name', 'Diarios')
                ->orWhere('name', 'Diario')
                ->first()->id;

            $entry_lines = $this->createTransAccountingEntryLines($transaction_id);

            $output = $this->accountingUtil->createAccountingEntry($entry, $entry_lines, $entry['date']);

        }  catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Create transaction accounting entry lines
     * 
     * @param int $transaction_id
     * @return Array
     * @author Arquímides Martínez
     */
    private function createTransAccountingEntryLines($transaction_id) {
        $entry_lines = [];
        
        $transaction = Transaction::join('document_types as dt', 'transactions.document_types_id', 'dt.id')
            ->where('transactions.id', $transaction_id)
            ->select(
                'transactions.business_id',
                'transactions.location_id',
                'transactions.customer_id',
                'dt.short_name as doc_type',
                'tax_amount as withheld_amount',
                'transactions.payment_condition',
                'final_total'
            )->first();

        $business = Business::find($transaction->business_id);

        $is_exempt = Customer::where('id', $transaction->customer_id)
            ->where('is_exempt', 1)
            ->count();

        /** payments */
        $payments = TransactionPayment::where('transaction_id', $transaction_id)
            ->select(
                DB::raw('IF(method IN ("cash", "check"), "cash", method) as method'),
                'card_pos',
                'transfer_receiving_bank',
                DB::raw('SUM(IF(method IN ("cash", "check"), IF(is_return = 0, amount, amount * -1), 0)) as cash'),
                DB::raw('SUM(IF(method = "card", IF(is_return = 0, amount, amount * -1), 0)) as card'),
                DB::raw('SUM(IF(method = "bank_transfer", IF(is_return = 0, amount, amount * -1), 0)) as bank_transfer')
            )->groupBy('card_pos', 'transfer_receiving_bank')
            ->get();

        $location_accounts =
            AccountBusinessLocation::where('location_id', $transaction->location_id)
            ->select(
                'general_cash_id',
                'vat_final_customer_id',
                'vat_taxpayer_id',
                'account_receivable_id'
            )->first();

        $entry_lines = [];
        /** sale */
        foreach($payments as $p) {
            if($p->method == 'cash') { // cash and check
                $entry_lines[] = [
                    'catalogue_id' => $location_accounts->general_cash_id,
                    'type' => 'debit',
                    'amount' => $p->cash
                ];

            } else if($p->method == 'card') {
                $pos = Pos::join('bank_accounts as ba', 'pos.bank_account_id', 'ba.id')
                ->select('ba.catalogue_id')
                ->first();

                $entry_lines[] = [
                    'catalogue_id' => $pos->catalogue_id,
                    'type' => 'debit',
                    'amount' => $p->card
                ];

            } else if($p->method == 'bank_transfer') {
                $bank_account = BankAccount::where('id', $p->transfer_receiving_bank)
                ->value('catalogue_id');

                $entry_lines[] = [
                    'catalogue_id' => $bank_account,
                    'type' => 'debit',
                    'amount' => $p->bank_transfer
                ];
            }
        }

        /** Credit sales */
        if ($transaction->payment_condition == 'credit' || $transaction->payment_condition == 'partial') {
            $credit_amount = $transaction->final_total;

            foreach($payments as $p) {
                $credit_amount -= ($p->cash + $p->card + $p->check + $p->bank_transfer);
            }

            if ($credit_amount > 0) {
                if ($business->receivable_type == 'customer') {
                    $customer = Customer::find($transaction->customer_id);

                    if (!empty($customer->accounting_account_id)) {
                        $entry_lines[] = [
                            'catalogue_id' => $customer->accounting_account_id,
                            'type' => 'debit',
                            'amount' => $credit_amount
                        ];

                    } else {
                        $entry_lines[] = [
                            'catalogue_id' => $location_accounts->account_receivable_id,
                            'type' => 'debit',
                            'amount' => $credit_amount
                        ];
                    }
                } else if ($business->receivable_type == 'bag_account') {
                    $entry_lines[] = [
                        'catalogue_id' => $location_accounts->account_receivable_id,
                        'type' => 'debit',
                        'amount' => $credit_amount
                    ];
                }
            }
        }

        /** withheld */
        if($transaction->withheld_amount > 0) {
            $withheld_account = Business::find($transaction->business_id)->accounting_withheld_id;

            $entry_lines[] = [
                'catalogue_id' => $withheld_account,
                'type' => 'debit',
                'amount' => $transaction->withheld_amount
            ];
        }

        /** taxes */
        if($is_exempt == 0) {
            $sell_lines =
            TransactionSellLine::where('transaction_id', $transaction_id)
            ->select(
                DB::raw('SUM(tax_amount) as tax_amount')
            )->first();

            if($transaction->doc_type == 'FCF' || $transaction->doc_type == 'Ticket') {
                $entry_lines[] = [
                    'catalogue_id' => $location_accounts->vat_final_customer_id,
                    'type' => 'credit',
                    'amount' => $sell_lines->tax_amount
                ];

            } else if ($transaction->doc_type == 'CCF') {
                $entry_lines[] = [
                    'catalogue_id' => $location_accounts->vat_taxpayer_id,
                    'type' => 'credit',
                    'amount' => $sell_lines->tax_amount
                ];
            }
        }

        /** inputs */
        $inputs = $this->getTransactionInputs($transaction_id, $transaction->location_id);
        for ($i = 0; $i < count($inputs); $i++) {
            array_push($entry_lines, $inputs[$i]);
        }

        /** cost and inventory */
        $costs = $this->getTransactionCosts($transaction_id, $transaction->location_id);
        for ($i = 0; $i < count($costs); $i++) { 
            array_push($entry_lines, $costs[$i]);
        }

        //return $transaction;
        return $entry_lines;
    }

    /**
     * Get inputs from transaction by product
     * 
     * @param int $transaction_id
     * @param int $location_id
     * @return Array
     * @author Arquímides Martínez
     */
    private function getTransactionInputs($transaction_id, $location_id) {
        $sell_lines =
        TransactionSellLine::join('product_accounts_locations as pal', 'transaction_sell_lines.product_id', 'pal.product_id')
        ->where('transaction_sell_lines.transaction_id', $transaction_id)
        ->where('pal.location_id', $location_id)
        ->where('pal.type', 'input')
        ->select(
            'pal.catalogue_id',
            DB::raw('SUM(transaction_sell_lines.unit_price_exc_tax) as amount')
        )->groupBy('pal.catalogue_id')
        ->get();
        
        $lines = [];
        foreach($sell_lines as $sl) {
            $lines[] = [
                'catalogue_id' => $sl->catalogue_id,
                'type' => 'credit',
                'amount' => $sl->amount
            ];
        }

        return $lines;
    }

    /**
     * Get costs from transaction by product
     * 
     * @param int $transaction_id
     * @param int location_id
     * @return Array
     * @author Arquímides Martínez
     */
    private function getTransactionCosts($transaction_id, $location_id) {
        $products = TransactionSellLine::join('products as p', 'transaction_sell_lines.product_id', 'p.id')
        ->where('p.clasification', 'product')
        ->count();

        $lines = [];
        if($products > 0) {
            $cost_lines =
            TransactionSellLine::join('product_accounts_locations as pal', 'transaction_sell_lines.product_id', 'pal.product_id')
            ->where('transaction_sell_lines.transaction_id', $transaction_id)
            ->where('pal.location_id', $location_id)
            ->where('pal.type', 'cost')
            ->select(
                'pal.catalogue_id',
                DB::raw('SUM(transaction_sell_lines.unit_price_exc_tax) as amount')
            )->groupBy('pal.catalogue_id')
            ->get();

            foreach($cost_lines as $cl) {
                $lines[] = [
                    'catalogue_id' => $cl->catalogue_id,
                    'type' => 'debit',
                    'amount' => $cl->amount
                ];
            }

            $inventory_lines =
            TransactionSellLine::join('product_accounts_locations as pal', 'transaction_sell_lines.product_id', 'pal.product_id')
            ->where('transaction_sell_lines.transaction_id', $transaction_id)
            ->where('pal.location_id', $location_id)
            ->where('pal.type', 'inventory')
            ->select(
                'pal.catalogue_id',
                DB::raw('SUM(transaction_sell_lines.unit_price_exc_tax) as amount')
            )->groupBy('pal.catalogue_id')
            ->get();

            foreach($inventory_lines as $il) {
                $lines[] = [
                    'catalogue_id' => $il->catalogue_id,
                    'type' => 'credit',
                    'amount' => $il->amount
                ];
            }
        }

        return $lines;
    }

    /**
     * Returns the HTML row for a product in POS
     *
     * @param  int  $variation_id
     * @param  int  $location_id
     * @return \Illuminate\Http\Response
     */
    public function getProductRow($variation_id, $location_id)
    {
        $output = [];

        try {
            $row_count = request()->get('product_row');

            $is_direct_sell = false;

            if (request()->get('is_direct_sell') == 'true') {
                $is_direct_sell = true;
            }

            $business_id = request()->session()->get('user.business_id');

            $warehouse_id = request()->input('warehouse_id', null);

            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id, $warehouse_id);

            $reservation_id = request()->get('reservation_id', 0);
            
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

                $product->formatted_qty_available = $formatted_qty_available;
                $product->qty_available = $qty_available;
                $product->qty_reserved = $qty_reserved;

            } else {
                if ($reservation_id > 0) {
                    $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);

                } else {
                    $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available - $product->qty_reserved);
                }
            }

            // Get customer group and change the price accordingly
            $customer_id = request()->get('customer_id', null);

            $cg = $this->contactUtil->getCustomerGroup($business_id, $customer_id);
            
            $percent = (empty($cg) || empty($cg->amount)) ? 0 : $cg->amount;

            $product->default_sell_price = $product->default_sell_price + ($percent * $product->default_sell_price / 100);
            $product->sell_price_inc_tax = $product->sell_price_inc_tax + ($percent * $product->sell_price_inc_tax / 100);

            /** Tax percent added */
            $product->tax_percent = $this->taxUtil->getTaxPercent($product->tax_id);

            $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);

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

            $price_group = request()->input('price_group');

            if (!empty($price_group)) {
                $variation_group_prices = $this->productUtil->getVariationGroupPrice($variation_id, $price_group, $product->tax_percent);
                
                if (!empty($variation_group_prices['price_inc_tax'])) {
                    $product->sell_price_inc_tax = $variation_group_prices['price_inc_tax'];
                    $product->default_sell_price = $variation_group_prices['price_exc_tax'];
                }
            }

            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

            // Check if user is admin
            if (config('app.business') == 'optics') {
                $user = User::find(request()->user()->id);
                $is_admin = $user->hasRole('Super Admin#' . $business_id);

            } else {
                $is_admin = false;
            }

            // Number of decimals in sales
            $product_settings = empty($business_details->product_settings) ? $this->businessUtil->defaultProductSettings() : json_decode($business_details->product_settings, true);
            $decimals_in_sales = $product_settings['decimals_in_sales'];

            $output['success'] = true;

            if (request()->get('type') == 'sell-return') {
                $output['html_content'] =  view('sell_return.partials.product_row')
                ->with(compact(
                    'product',
                    'row_count',
                    'tax_dropdown',
                    'enabled_modules',
                    'is_admin'
                ))
                ->render();

            } else {
                $output['html_content'] =  view('sale_pos.product_row')
                ->with(compact(
                    'product',
                    'row_count',
                    'tax_dropdown',
                    'enabled_modules',
                    'pos_settings',
                    'reservation_id',
                    'is_admin',
                    'decimals_in_sales'
                ))
                ->render();
            }
            
            $output['enable_sr_no'] = $product->enable_sr_no;

            if ($this->transactionUtil->isModuleEnabled('modifiers')  && !$is_direct_sell) {
                $this_product = Product::where('business_id', $business_id)
                ->find($product->product_id);

                if (count($this_product->modifier_sets) > 0) {
                    $product_ms = $this_product->modifier_sets;

                    $output['html_modifier'] = view('restaurant.product_modifier_set.modifier_for_product')
                    ->with(compact('product_ms', 'row_count'))
                    ->render();
                }
            }

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = __('lang_v1.item_out_of_stock');
        }

        return $output;
    }

    /**
     * Returns the HTML row for a payment in POS
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getPaymentRow(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $row_index = $request->input('row_index');
        $removable = true;
        $payment_types = $this->productUtil->payment_types();

        $payment_line = $this->dummyPaymentLine;

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);
        /** Banks */
        $banks = Bank::where('business_id', $business_id)
        ->pluck('name', 'id');
        /** Pos */
        $pos = Pos::where('business_id', $business_id)
        ->pluck('name', 'id');
        /** Payment terms */
        $payment_terms = PaymentTerm::where('business_id', $business_id)
        ->pluck('name', 'id');

        /** Bank account */
        $bank_accounts = BankAccount::pluck('name', 'id');
        

        return view('sale_pos.partials.payment_row')
        ->with(compact('payment_types', 'row_index', 'removable', 'payment_line', 'accounts',
            'banks', 'pos', 'payment_terms', 'bank_accounts'));
    }

    /**
     * Returns recent transactions
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getRecentTransactions(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');
        $transaction_status = $request->get('status');

        $query = Transaction::where('business_id', $business_id)
        ->where('created_by', $user_id)
        ->where('type', 'sell')
        ->where('is_direct_sale', 0);

        if ($transaction_status == 'quotation') {
            $query->where('status', 'draft')
            ->where('is_quotation', 1);
        } elseif ($transaction_status == 'draft') {
            $query->where('status', 'draft')
            ->where('is_quotation', 0);
        } else {
            $query->where('status', $transaction_status);
        }

        $transactions = $query->latest()
        ->limit(10)
        ->get();

        return view('sale_pos.partials.recent_transactions')
        ->with(compact('transactions'));
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

                $receipt = $this->receiptContent($transaction->type, $business_id, $transaction->location_id, $transaction_id, 'browser');

                if (!empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = ['success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Print CCF detail for sells
     * 
     * @param int $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function printCCF($transaction_id) {
        if (request()->ajax()) {
            try {
                $transaction = Transaction::findOrFail($transaction_id);
    
                if (empty($transaction)) {
                    return [ 'success' => 0,
                        'msg' => trans("messages.something_went_wrong") ];
                }
    
                $business_name = Business::find($transaction->business_id)->business_full_name;

                $transaction = Transaction::join('customers as c', 'transactions.customer_id', 'c.id')
                    ->leftJoin('quotes as q', 'transactions.id', 'q.transaction_id')
                    ->leftJoin('employees as e', 'q.employee_id', 'e.id')
                    ->where('transactions.id', $transaction_id)
                    ->select(
                        'transactions.transaction_date as date',
                        'transactions.correlative',
                        DB::raw('IF(c.business_name IS NOT NULL, c.business_name, c.name) AS customer_name'),
                        DB::raw("CONCAT(COALESCE(e.first_name,''),' ',COALESCE(e.last_name,'')) as seller_name"),
                        'transactions.total_before_tax as subtotal',
                        'transactions.tax_group_amount as tax_amount',
                        'transactions.final_total'
                    )->first();

                $transaction_sell_lines = Transaction::join('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
                    ->join('variations as v', 'tsl.variation_id', 'v.id')
                    ->join('products as p', 'v.product_id', 'p.id')
                    ->where('transactions.id', $transaction_id)
                    ->select(
                        'tsl.quantity',
                        'v.sub_sku as sku',
                        'p.name as product',
                        'tsl.unit_price_before_discount as unit_exc_tax',
                        'tsl.unit_price_exc_tax as line_total_exc_tax'
                    )
                    ->groupBy('tsl.id')
                    ->get();

                $receipt['content'] = view('sale_pos.receipts.fiscal_credit_details',
                    compact('business_name', 'transaction', 'transaction_sell_lines'))->render();

                if (!empty($receipt)) {
                    return [ 'success' => 1,
                        'receipt' => $receipt ];
                }

            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                return [ 'success' => 0,
                    'msg' => trans("messages.something_went_wrong") ];
            }
        }
    }

    /**
     * Gives suggetion for product based on category
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getProductSuggestion(Request $request)
    {
        if ($request->ajax()) {
            $category_id = $request->get('category_id');
            $brand_id = $request->get('brand_id');
            $location_id = $request->get('location_id');
            $term = $request->get('term');

            $check_qty = false;
            $business_id = $request->session()->get('user.business_id');

            $products = Product::join(
                'variations',
                'products.id',
                '=',
                'variations.product_id'
            )
            ->leftjoin(
                'variation_location_details AS VLD',
                function ($join) use ($location_id) {
                    $join->on('variations.id', '=', 'VLD.variation_id');

                            //Include Location
                    if (!empty($location_id)) {
                        $join->where(function ($query) use ($location_id) {
                            $query->where('VLD.location_id', '=', $location_id);
                                            //Check null to show products even if no quantity is available in a location.
                                            //TODO: Maybe add a settings to show product not available at a location or not.
                            $query->orWhereNull('VLD.location_id');
                        });
                        ;
                    }
                }
            )
            ->where('products.business_id', $business_id)
            ->where('products.type', '!=', 'modifier');

            //Include search
            if (!empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term .'%');
                    $query->orWhere('sku', 'like', '%' . $term .'%');
                    $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                });
            }

            //Include check for quantity
            if ($check_qty) {
                $products->where('VLD.qty_available', '>', 0);
            }
            
            if ($category_id != 'all') {
                $products->where(function ($query) use ($category_id) {
                    $query->where('products.category_id', $category_id);
                    $query->orWhere('products.sub_category_id', $category_id);
                });
            }
            if ($brand_id != 'all') {
                $products->where('products.brand_id', $brand_id);
            }

            $products = $products->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'products.enable_stock',
                'variations.id as variation_id',
                'variations.name as variation',
                'VLD.qty_available',
                'variations.default_sell_price as selling_price',
                'variations.sub_sku',
                'products.image'
            )
            ->orderBy('products.name', 'asc')
            ->groupBy('variations.id')
            ->paginate(20);

            return view('sale_pos.partials.product_list')
            ->with(compact('products'));
        }
    }

    /**
     * Check if the correlative exists.
     * @param  int  $document
     * @param  string  $correlative
     * @return array
     */
    public function validateCorrelative($location, $document, $correlative, $transaction_id = 0)
    {
        $business_id = request()->session()->get('user.business_id');

        $document_correlative = DocumentCorrelative::where('document_type_id', $document)
        ->where('business_id', $business_id)
        ->where('status', 'active')
        ->where('location_id', $location)
        ->first();

        $transaction = Transaction::where('business_id', $business_id)
        ->where('id', '!=', $transaction_id)
        ->where('correlative', $correlative)
        ->where('document_types_id', $document);

        if (! empty($document_correlative)) {
            $transaction = $transaction->where('serie', $document_correlative->serie);
        }

        $transaction = $transaction->first();

        /** validate cashier closure open and close correlative */
        $cashier_closure = CashierClosure::join('cashiers as c', 'cashier_closures.cashier_id', 'c.id')
        ->where('c.business_location_id', $location)
        ->where(function($query) use ($correlative) {
            $query->where('cashier_closures.open_correlative', $correlative)
            ->orWhere('cashier_closures.close_correlative', $correlative);
        })->count();
        
        $doc_type = DocumentType::where('id', $document)
        ->where('short_name', 'Ticket')
        ->count();

        if (config('app.business') == 'optics') {
            $output = ! empty($transaction) ? ['flag' => true] : ['flag' => false];

        } else {
            if (! empty($transaction) || ($cashier_closure > 0 && $doc_type > 0)) {
                $output = ['flag' => true];
            } else {
                $output = ['flag' => false];
            }
        }

        return $output;
    }

    /**
     * Save tax and payment amounts from transactions already recorded.
     * 
     * @return string
     */
    public function calculateTaxAndPayments()
    {
        try {
            $transactions = Transaction::where('type', 'sell')
            ->where('status', 'final')
            ->get();

            \Log::info('--- START ---');

            foreach ($transactions as $transaction) {
                $this->transactionUtil->saveTaxAndPayment($transaction);
                \Log::info('TRANSACTION ' . $transaction->id);
            }

            \Log::info('--- END ---');

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $output = 'FAIL';
        }

        return $output;
    }

    /**
     * Get the final correlative for a given document type and location.
     * 
     * @return  array
     */
    public function getFinalCorrelative()
    {
        $location_id = request()->get('location_id');
        $document_id = request()->get('document_id');

        $document_correlative = DocumentCorrelative::where('location_id', $location_id)
        ->where('document_type_id', $document_id)
        ->where('status', 'active')
        ->first();

        if (! empty($document_correlative)) {
            $output = [
                'success' => true,
                'final_correlative' => $document_correlative->final
            ];

        } else {
            $output = [
                'success' => false,
                'final_correlative' => ''
            ];
        }

        return $output;
    }

    /**
     * Get lab order for transaction
     * 
     * @param  int  $transaction_id
     * @param  int  $patient_id
     * @return \Illuminate\Http\Response
     */
    public function getLabOrder($transaction_id = null, $patient_id = null)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get("user.business_id");

            $transaction = Transaction::join("customers as c", "transactions.customer_id", "c.id")
            ->where("transactions.business_id", $business_id)
            ->where("transactions.id", $transaction_id)
            ->select(
                "transactions.contact_id",
                "transactions.location_id",
                "transactions.customer_id"
            )
            ->first();

            $has_hoop = TransactionSellLine::join("variations as v", "transaction_sell_lines.variation_id", "v.id")
            ->join("products as p", "v.product_id", "p.id")
            ->join("categories as c", "p.category_id", "c.id")
            ->where("transaction_sell_lines.transaction_id", $transaction_id)
            ->where("c.name", "AROS")
            ->pluck('p.name', 'v.id');

            $hoop_values = null;
            
            $own_hoop_aux = 0;

            if (empty($has_hoop) || $has_hoop->count() == 0) {
                $own_hoop_aux = 1;

            } elseif ($has_hoop->count() == 1) {
                $hoop_values = TransactionSellLine::join("variations as v", "transaction_sell_lines.variation_id", "v.id")
                ->join("products as p", "v.product_id", "p.id")
                ->join("categories as c", "p.category_id", "c.id")
                ->where("transaction_sell_lines.transaction_id", $transaction_id)
                ->where("v.id", array_key_first($has_hoop->toArray()))
                ->where("c.name", "AROS")
                ->select(
                    "v.id as id",
                    "p.name as name",
                    "p.measurement as size",
                    DB::raw("(SELECT name FROM `variation_value_templates` WHERE code = (SUBSTRING(p.sku, (COUNT(p.sku)) - 4, 3))) as color")
                )
                ->first();
            } else {
                if ($has_hoop->count() > 1) {
                    $own_hoop_aux = 2;
                }
            }

            // V.S. glasses
            $has_glass = TransactionSellLine::join("variations as v", "transaction_sell_lines.variation_id", "v.id")
            ->join("products as p", "v.product_id", "p.id")
            ->join("categories as c", "p.category_id", "c.id")
            ->where("transaction_sell_lines.transaction_id", $transaction_id)
            ->where("c.name", "LENTES")
            ->where("p.name", "not like", "%IZQUIERDO%")
            ->where("p.name", "not like", "%DERECHO%");
            
            $has_glass->where(function ($query) {
                $query->where("p.name", "like", '%V.S.%');
                $query->orWhere("p.name", "like", "%VS.%");
                $query->orWhere("p.name", "like", "%V.S%");
                $query->orWhere("p.name", "like", "%VS%");
                $query->orWhere("p.name", "like", "%bifocal%");
                $query->orWhere("p.name", "like", "%invisible%");
            });

            $has_glass = $has_glass->select(
                "v.id as id",
                "p.name as name"
            )
            ->pluck('p.name', 'v.id');
            
            // Right glass
            $has_glass_od = TransactionSellLine::join("variations as v", "transaction_sell_lines.variation_id", "v.id")
            ->join("products as p", "v.product_id", "p.id")
            ->join("categories as c", "p.category_id", "c.id")
            ->where("transaction_sell_lines.transaction_id", $transaction_id)
            ->where("c.name", "LENTES")
            ->where("p.name", "like", "%DERECHO%")
            ->select(
                "v.id as id",
                "p.name as name"
            )
            ->pluck('p.name', 'v.id');

            // Left glass
            $has_glass_os = TransactionSellLine::join("variations as v", "transaction_sell_lines.variation_id", "v.id")
            ->join("products as p", "v.product_id", "p.id")
            ->join("categories as c", "p.category_id", "c.id")
            ->where("transaction_sell_lines.transaction_id", $transaction_id)
            ->where("c.name", "LENTES")
            ->where("p.name", "like", "%IZQUIERDO%")
            ->select(
                "v.id as id",
                "p.name as name"
            )
            ->pluck('p.name', 'v.id');
            
            $has_ar = TransactionSellLine::join("variations as v", "transaction_sell_lines.variation_id", "v.id")
            ->join("products as p", "v.product_id", "p.id")
            ->where("transaction_sell_lines.transaction_id", $transaction_id)
            ->where("p.clasification", "service")
            ->whereIn("p.ar", ["green", "blue", "premium"])
            ->select("p.ar", "p.name");
            
            $ar_aux = 0;

            if (! empty($has_ar)) {
                if ($has_ar->count() == 1) {
                    $ar_aux = 1;
                    $has_ar = $has_ar->first();

                } elseif ($has_ar->count() > 1) {
                    $ar_aux = 2;
                    $has_ar = $has_ar->pluck('p.name', 'p.ar');
                }
            }

            $show_modal = true;

            if (empty($has_hoop) && empty($has_glass) && empty($has_ar)) {
                $show_modal = false;
            }

            $business_locations = BusinessLocation::forDropdown($business_id, false, true);
            $bl_attributes = $business_locations['attributes'];
            $business_locations = $business_locations['locations'];

            $default_location = null;

            if (count($business_locations) == 1) {
                foreach ($business_locations as $id => $name) {
                    $default_location = $id;
                }
            }

            // Lab Order Data
            $code = $this->util->generateLabOrderCode($transaction->location_id);
            
            $date_delivery = \Carbon::now()->addDay(3)->format('d/m/Y H:i');

            $customers = Customer::where('id', $transaction->customer_id)
            ->pluck('name', 'id');

            $patients = Patient::where('id', $patient_id)
            ->pluck('full_name', 'id');

            return view("sale_pos.partials.lab_order")
            ->with(compact(
                "transaction",
                "customers",
                "patients",
                "code",
                "business_locations",
                "bl_attributes",
                "default_location",
                "has_hoop",
                "has_glass",
                "has_ar",
                "date_delivery",
                "transaction_id",
                "own_hoop_aux",
                "hoop_values",
                "has_glass_od",
                "has_glass_os",
                "ar_aux",
                "patient_id"
            ));
        }
    }

    /**
     * Check if there are other customers or patients with a similar name.
     * 
     * @return array
     */
    public function checkCustomerPatientName() {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $is_customer = ! empty(request()->input('is_customer')) ? request()->input('is_customer') : 0;
            $is_patient = ! empty(request()->input('is_patient')) ? request()->input('is_patient') : 0;
            $term = ! empty(request()->input('term')) ? trim(request()->input('term')) : 0;

            $output = ['success' => 1];

            if ($term !== 0) {
                $term = mb_strtoupper($term);

                $flag_customer = 0;

                if ($is_customer) {
                    $customers = Customer::where('business_id', $business_id)
                    ->whereRaw("UPPER(name) LIKE '%" . $term . "%'")
                    ->get();

                    if (count($customers) > 0) {
                        $output = [
                            'success' => 0,
                            'msg' => __('customer.warning_customer_name')
                        ];

                        $flag_customer = 1;
                    }
                }
                
                if ($is_patient === 1 && $flag_customer === 0) {
                    $patients = Patient::where('business_id', $business_id)
                    ->whereRaw("UPPER(full_name) LIKE '%" . $term . "%'")
                    ->get();

                    if (count($patients) > 0) {
                        $output = [
                            'success' => 0,
                            'msg' => __('customer.warning_patient_name')
                        ];
                    }
                }
            }

            return $output;
        }
    }

    /**
     * Update fiscal document data in transactions.
     * 
     * @return string
     */
    public function updateFiscalDocumentData()
    {
        try {
            DB::beginTransaction();

            \Log::info("--- START ---");

            $sales = Transaction::where('type', 'sell')
            ->whereNotNull('document_types_id')
            ->get();

            if (! empty($sales)) {
                foreach ($sales as $sale) {
                    $document_correlative = DocumentCorrelative::where('business_id', $sale->business_id)
                    ->where('location_id', $sale->location_id)
                    ->where('document_type_id', $sale->document_types_id)
                    ->whereRaw('CONVERT(initial, UNSIGNED INTEGER) <= ?', [$sale->correlative])
                    ->whereRaw('CONVERT(final, UNSIGNED INTEGER) >= ?', [$sale->correlative])
                    ->first();

                    if (! empty($document_correlative)) {
                        $sale->serie = is_null($sale->serie) || empty($sale->serie) ? $document_correlative->serie : $sale->serie;
                        $sale->resolution = is_null($sale->resolution) || empty($sale->resolution) ? $document_correlative->resolution : $sale->resolution;
                        $sale->document_correlative_id = is_null($sale->document_correlative_id) || empty($sale->document_correlative_id) ? $document_correlative->id : $sale->document_correlative_id;

                        $sale->save();

                        \Log::info("TRANSACTION: id -> $sale->id correlative -> $sale->correlative");
                    }
                }
            }

            \Log::info("--- END ---");

            DB::rollBack();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    /**
     * Check number of POS terminals.
     * 
     * @return array
     */
    public function checkPosNumber()
    {
        $business_id = request()->session()->get('user.business_id');

        $pos = Pos::where('business_id', $business_id)
        ->where('status', 'active')
        ->get();

        $route = action('PosController@index');

        $msg = __('card_pos.pos_not_available_msg');

        if (auth()->user()->can('pos.view')) {
            $msg .= ' ' . __('card_pos.pos_not_available_msg_route', ['route' => $route]);
        }

        return [
            'pos_number' => count($pos),
            'title' => __('card_pos.pos_not_available'),
            'msg' => $msg
        ];
    }

    /**
     * Fill in the unit_cost_exc_tax and unit_cost_inc_tax fields.
     * 
     * @param  int  $tsl_initial
     * @param  int  $tsl_final
     * @return string
     */
    public function updateUnitCostToSellLines($tsl_initial = null, $tsl_final = null)
    {
        try {
            // Set maximum PHP execution time
            ini_set('max_execution_time', 0);

            if (is_null($tsl_initial) || is_null($tsl_final)) {
                $transaction_sell_lines = TransactionSellLine::all();
            } else {
                $transaction_sell_lines = TransactionSellLine::whereBetween('id', [$tsl_initial, $tsl_final])->get();
            }

            DB::beginTransaction();

            \Log::info('--- START ---');

            if (! empty($transaction_sell_lines)) {
                foreach ($transaction_sell_lines as $tsl) {
                    $tsl->unit_cost_exc_tax = $tsl->unit_price_before_discount;
                    $tsl->unit_cost_inc_tax = $tsl->unit_price;
                    $tsl->save();

                    // \Log::info('TSL: ' . $tsl->id);
                }
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

    /**
     * Fill the sale_price field in the transaction_sell_lines table.
     * 
     * @param  int  $tsl_initial
     * @param  int  $tsl_final
     * @return string
     */
    public function updateSalePriceToSellLines($tsl_initial = null, $tsl_final = null)
    {
        try {
            // Set maximum PHP execution time
            ini_set('max_execution_time', 0);

            if (is_null($tsl_initial) || is_null($tsl_final)) {
                $transaction_sell_lines = TransactionSellLine::all();
            } else {
                $transaction_sell_lines = TransactionSellLine::whereBetween('id', [$tsl_initial, $tsl_final])->get();
            }

            DB::beginTransaction();

            \Log::info('--- START ---');

            if (! empty($transaction_sell_lines)) {
                foreach ($transaction_sell_lines as $tsl) {
                    if (is_null($tsl->sale_price)) {
                        $variation = Variation::find($tsl->variation_id);

                        $tsl->sale_price = $variation->sell_price_inc_tax;
                        $tsl->save();

                        // \Log::info('TSL: ' . $tsl->id);
                    }
                }
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

    /**
     * Fill the sale_price field in the purchase_lines table.
     * 
     * @param  int  $pl_initial
     * @param  int  $pl_final
     * @return string
     */
    public function updateSalePriceToPurchaseLines($pl_initial = null, $pl_final = null)
    {
        try {
            // Set maximum PHP execution time
            ini_set('max_execution_time', 0);

            if (is_null($pl_initial) || is_null($pl_final)) {
                $purchase_lines = PurchaseLine::all();
            } else {
                $purchase_lines = PurchaseLine::whereBetween('id', [$pl_initial, $pl_final])->get();
            }

            DB::beginTransaction();

            \Log::info('--- START ---');

            if (! empty($purchase_lines)) {
                foreach ($purchase_lines as $pl) {
                    if (is_null($pl->sale_price)) {
                        $variation = Variation::find($pl->variation_id);

                        $pl->sale_price = $variation->sell_price_inc_tax;
                        $pl->save();

                        // \Log::info('TSL: ' . $tsl->id);
                    }
                }
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