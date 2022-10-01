<?php

namespace App\Http\Controllers;

use App\Pos;
use App\Bank;
use App\Contact;
use App\Country;
use App\TaxRate;
use App\Business;
use App\Employees;
use App\Warehouse;
use App\PaymentTerm;
use App\Transaction;
use App\MovementType;
use App\PurchaseLine;
use App\CustomerGroup;
use App\Utils\TaxUtil;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateInternationalPurchaseValidate;

class InternationalPurchaseController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $taxUtil;
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
        BusinessUtil $businessUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->taxUtil = $taxUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash',
            'amount' => 0,
            'note' => '',
            'card_reference' => '',
            'card_type' => '',
            'card_holder_name' => '',
            'card_pos' => null,
            'check_bank' => null,
            'check_number' => '',
            'check_reservation_number' => '',
            'payment_link_reference' => '',
            'telegraphic_transfer_beneficiary_account_number' => '',
            'telegraphic_transfer_swift_code' => '',
            'telegraphic_transfer_beneficiary_name' => '',
            'telegraphic_transfer_beneficiary_bank' => '',
            'telegraphic_transfer_beneficiary_address' => '',
            'telegraphic_transfer_bank_address' => '',
            'telegraphic_transfer_state' => '',
            'bank_transfer_bank' => null,
            'bank_transfer_account_number' => '',
            'bank_transfer_reference' => '',
            'is_return' => 0,
            'transaction_no' => ''
        ];
        /** Business types */
        $this->business_type = ['small_business', 'medium_business', 'large_business'];
        /** Payment conditions */
        $this->payment_conditions = ['cash', 'credit'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

        $business_id = auth()->user()->business_id;

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $taxes = TaxRate::where('business_id', $business_id)
            ->get();
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
        $payment_types = $this->productUtil->payment_types('purchase', $business_id);

        /** Banks */
        $banks = Bank::where('business_id', $business_id)
            ->pluck('name', 'id');
        /** Pos */
        $pos = Pos::where('business_id', $business_id)
            ->where('status', 'active')
            ->pluck('name', 'id');

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        //** Get taxes list */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');
        /** Business type */
        $business_type = $this->business_type;
        /** Payment conditions */
        $payment_conditions = $this->payment_conditions;

        # Gets warehouses
        $warehouses = Warehouse::forDropdown($business_id);

        $countries = Country::forDropdown($business_id);
        // Payments terms
        $payment_terms = PaymentTerm::forDropdown($business_id);
        $countries = Country::forDropdown($business_id);
        return view('purchase.international.create')
            ->with(compact(
                'taxes',
                'orderStatuses',
                'business_locations',
                'currency_details',
                'tax_groups',
                'default_purchase_status',
                'customer_groups',
                'employees_sales',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'accounts',
                'banks',
                'pos',
                'business_type',
                'payment_conditions',
                'warehouses',
                'countries',
                'payment_terms'
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
        // dd($request);
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
            $request->validate([
                'status' => 'required',
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'total_before_tax' => 'required',
                'location_id' => 'required',
                'warehouse_id' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
            ]);

            $business_id = auth()->user()->business_id;
            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }

            $transaction_data = $request->only([
                'ref_no', 'status', 'freight', 'contact_id', 'transaction_date', 'tax_amount', 'total_before_tax',
                'freight_amount', 'deconsolidation_amount', 'import_type', 'dai_amount', 'internal_storage', 'external_storage', 'local_freight_amount', 'location_id', 'final_total',
                'customs_procedure_amount', 'additional_notes', 'warehouse_id', 'purchase_type'
            ]);

            $user_id = auth()->user()->id;
            $enable_product_editing = null; // $request->session()->get('business.enable_editing_product_from_purchase');
            $enable_editing_avg_cost = $request->session()->get('business.enable_editing_avg_cost_from_purchase');

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['total_before_tax']);
            $transaction_data['tax_amount'] = $this->productUtil->num_uf($transaction_data['tax_amount']);
            //$transaction_data['shipping_charges'] = $this->productUtil->num_uf($transaction_data['shipping_charges']);
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total']);
            $transaction_data['freight_amount'] = $this->productUtil->num_uf($transaction_data['freight_amount']);
            $transaction_data['deconsolidation_amount'] = $this->productUtil->num_uf($transaction_data['deconsolidation_amount']);
            $transaction_data['dai_amount'] = $this->productUtil->num_uf($transaction_data['dai_amount']);
            $transaction_data['external_storage'] = $this->productUtil->num_uf($transaction_data['external_storage']);
            $transaction_data['internal_storage'] = $this->productUtil->num_uf($transaction_data['internal_storage']);
            $transaction_data['local_freight_amount'] = $this->productUtil->num_uf($transaction_data['local_freight_amount']);
            $transaction_data['customs_procedure_amount'] = $this->productUtil->num_uf($transaction_data['customs_procedure_amount']);
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'purchase';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date']);

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

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
                    'weight_kg' => $this->productUtil->num_uf($purchase['weight_kg']),
                    'purchase_price' => $this->productUtil->num_uf($purchase['price']),
                    'purchase_price_inc_tax' => $this->productUtil->num_uf($purchase['purchase_price']),
                    'transfer_fee' => $this->productUtil->num_uf($purchase['line_transfer_fee']),
                    'freight' => $purchase['line_freight_inc'],
                    'freight_amount' => $this->productUtil->num_uf($purchase['line_freight_amount']),
                    'deconsolidation_amount' => $this->productUtil->num_uf($purchase['line_deconsolidation_amount']),
                    'dai_amount' => $this->productUtil->num_uf($purchase['line_dai_amount']),
                    'tax_amount' => $this->productUtil->num_uf($purchase['line_tax_amount']),
                    'external_storage' => $this->productUtil->num_uf($purchase['line_external_storage']),
                    'local_freight_amount' => $this->productUtil->num_uf($purchase['line_local_freight_amount']),
                    'customs_procedure_amount' => $this->productUtil->num_uf($purchase['line_customs_procedure_amount']),
                ];

                if (!empty($purchase['mfg_date'])) {
                    $new_purchase_line['mfg_date'] = $this->productUtil->uf_date($purchase['mfg_date']);
                }
                if (!empty($purchase['exp_date'])) {
                    $new_purchase_line['exp_date'] = $this->productUtil->uf_date($purchase['exp_date']);
                }

                $purchase_lines[] = $new_purchase_line;

                //Edit product price
                if ($enable_product_editing == 1) {
                    //Default selling price is in base currency so no need to multiply with exchange rate.
                    $new_purchase_line['default_sell_price'] = $this->productUtil->num_uf($purchase['default_sell_price']);
                    $this->productUtil->updateProductFromPurchase($new_purchase_line);
                }

                $uac = $new_purchase_line;
                # Update average cost
                if ($enable_editing_avg_cost == 1) {
                    $this->productUtil->updateAverageCost(
                        $uac['variation_id'],
                        $uac['purchase_price'],
                        $uac['quantity']
                    );
                }

                // Update quantity only if status is "received"
                if ($transaction_data['status'] == 'received') {
                    # Add warehouse in parameters
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
                if (!empty($request->input('payment'))) {
                    $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));
                }
                //update payment status
                $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
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
            }
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_add_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

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
        $taxes = TaxRate::where('business_id', $business_id)
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
                'payment_lines',
                'tax'
            )
            ->first();
        $payment_methods = $this->productUtil->payment_types('purchase', $business_id);

        $purchase_taxes = [];
        if (!empty($purchase->tax)) {
            if ($purchase->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {
                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }
        return view('purchase.international.show')
            ->with(compact('taxes', 'purchase', 'payment_methods', 'purchase_taxes'));
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

        $business_id = auth()->user()->business_id;

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

        $customer_groups = CustomerGroup::forDropdown($business_id);
        $employees_sales = Employees::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        # Gets warehouses
        $warehouses = Warehouse::forDropdown($business_id);

        $business_type = $this->business_type;

        //** Get taxes list */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');
        /** Payment conditions */
        $payment_conditions = $this->payment_conditions;

        /**Contacts */
        $contacts = Contact::where('business_id', $business_id)->pluck('name', 'id');

        /** Banks */
        $banks = Bank::where('business_id', $business_id)
            ->pluck('name', 'id');
        /** Pos */
        $pos = Pos::where('business_id', $business_id)
            ->where('status', 'active')
            ->pluck('name', 'id');

        $countries = Country::forDropdown($business_id);

        /**Payment terms */
        $payment_terms = PaymentTerm::forDropdown($business_id);

        /** Validate NIT and NRC */
        $verify_tax_reg = Contact::where("id", $purchase->contact_id)
            ->whereNotNull("nit")
            ->whereNotNull("tax_number")
            ->count();

        $verify_tax_reg = $verify_tax_reg ?? 0;
        return view('purchase.international.edit', compact(
            'contacts',
            'payment_conditions',
            'tax_groups',
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
            'shortcuts',
            'business_type',
            'warehouses',
            'pos',
            'banks',
            'countries',
            'verify_tax_reg',
            'payment_terms'
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
        // dd($request);
        if (!auth()->user()->can('purchase.update')) {
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
            $transaction = Transaction::findOrFail($id);

            //Validate document size
            $request->validate([
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
            ]);

            $transaction = Transaction::findOrFail($id);
            $before_status = $transaction->status;
            $business_id = auth()->user()->business_id;
            $enable_product_editing = null; //$request->session()->get('business.enable_editing_product_from_purchase');

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);


            $update_data = $request->only([
                'ref_no', 'status', 'freight', 'contact_id', 'transaction_date', 'tax_amount', 'total_before_tax',
                'freight_amount', 'deconsolidation_amount', 'import_type', 'dai_amount', 'internal_storage', 'external_storage', 'local_freight_amount', 'location_id', 'final_total',
                'customs_procedure_amount', 'additional_notes', 'warehouse_id',
            ]);

            $update_data['transaction_date'] = $this->productUtil->uf_date($update_data['transaction_date']);

            //unformat input values
            $update_data['total_before_tax'] = $this->productUtil->num_uf($update_data['total_before_tax']);

            $update_data['freight_amount'] = $this->productUtil->num_uf($update_data['freight_amount']);
            $update_data['deconsolidation_amount'] = $this->productUtil->num_uf($update_data['deconsolidation_amount']);
            $update_data['dai_amount'] = $this->productUtil->num_uf($update_data['dai_amount']);
            $update_data['external_storage'] = $this->productUtil->num_uf($update_data['external_storage']);
            $update_data['internal_storage'] = $this->productUtil->num_uf($update_data['internal_storage']);
            $update_data['local_freight_amount'] = $this->productUtil->num_uf($update_data['local_freight_amount']);
            $update_data['customs_procedure_amount'] = $this->productUtil->num_uf($update_data['customs_procedure_amount']);
            $update_data['business_id'] = $business_id;

            $update_data['tax_amount'] = $this->productUtil->num_uf($update_data['tax_amount']);
            $update_data['final_total'] = $this->productUtil->num_uf($update_data['final_total']);
            //unformat input values ends

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $update_data['document'] = $document_name;
            }

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            if ($transaction->status == 'received') {
                # Data to create or update kardex lines
                $lines_before = PurchaseLine::where('transaction_id', $transaction->id)->get();
            }

            //Add Purchase payments
            // $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));

            //Update transaction payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id);

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
                        $this->productUtil->updateProductQuantity(
                            $transaction->location_id,
                            $purchase['product_id'],
                            $purchase['variation_id'],
                            $purchase['quantity'],
                            $old_qty,
                            $currency_details,
                            $transaction->warehouse_id
                        );
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
                            $currency_details
                        );
                    }
                }

                $purchase_line->weight_kg = $this->productUtil->num_uf($purchase['weight_kg']);
                $purchase_line->purchase_price = $this->productUtil->num_uf($purchase['price']);
                $purchase_line->purchase_price_inc_tax = $this->productUtil->num_uf($purchase['purchase_price']);
                $purchase_line->transfer_fee = $this->productUtil->num_uf($purchase['line_transfer_fee']);
                $purchase_line->freight = $purchase['line_freight_inc'];
                $purchase_line->freight_amount = $this->productUtil->num_uf($purchase['line_freight_amount']);
                $purchase_line->deconsolidation_amount = $this->productUtil->num_uf($purchase['line_deconsolidation_amount']);
                $purchase_line->dai_amount = $this->productUtil->num_uf($purchase['line_dai_amount']);

                $purchase_line->tax_amount = $this->productUtil->num_uf($purchase['line_tax_amount']);
                $purchase_line->external_storage = $this->productUtil->num_uf($purchase['line_external_storage']);
                $purchase_line->local_freight_amount = $this->productUtil->num_uf($purchase['line_local_freight_amount']);
                $purchase_line->customs_procedure_amount = $this->productUtil->num_uf($purchase['line_customs_procedure_amount']);
                $purchase_line->quantity = $this->productUtil->num_uf($purchase['quantity']);
                $purchase_line->lot_number = !empty($purchase['lot_number']) ? $purchase['lot_number'] : null;
                $purchase_line->mfg_date = !empty($purchase['mfg_date']) ? $this->productUtil->uf_date($purchase['mfg_date']) : null;
                $purchase_line->exp_date = !empty($purchase['exp_date']) ? $this->productUtil->uf_date($purchase['exp_date']) : null;

                $updated_purchase_lines[] = $purchase_line;

                //Edit product price
                if ($enable_product_editing == 1) {
                    $variation_data['variation_id'] = $purchase_line->variation_id;
                    $variation_data['purchase_price'] = $purchase_line->purchase_price;

                    $this->productUtil->updateProductFromPurchase($variation_data);
                }
            }

            //unset deleted purchase lines
            $delete_purchase_line_ids = [];
            if (!empty($updated_purchase_line_ids)) {
                $delete_purchase_lines = PurchaseLine::where('transaction_id', $transaction->id)
                    ->whereNotIn('id', $updated_purchase_line_ids)
                    ->get();

                if ($delete_purchase_lines->count()) {
                    foreach ($delete_purchase_lines as $delete_purchase_line) {
                        $delete_purchase_line_ids[] = $delete_purchase_line->id;

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

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_update_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

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
        //
    }
}
