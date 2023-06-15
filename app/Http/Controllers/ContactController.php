<?php

namespace App\Http\Controllers;

use App\User;
use App\Brands;
use App\Contact;
use App\Business;
use App\Catalogue;
use App\CustomerGroup;
use App\Transaction;
use App\Employees;
use App\Country;
use App\State;
use App\City;
use App\Zone;
use App\PaymentTerm;
use App\TaxGroup;
use App\BusinessType;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use Excel;
use DB;

use App\Utils\Util;
use App\Utils\ModuleUtil;
use App\Utils\ContactUtil;
use App\Utils\TransactionUtil;
use App\Utils\TaxUtil;

class ContactController extends Controller
{
    protected $commonUtil;
    protected $contactUtil;
    protected $transactionUtil;
    protected $taxUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil,
        ModuleUtil $moduleUtil,
        ContactUtil $contactUtil,
        TransactionUtil $transactionUtil,
        TaxUtil $taxUtil
    ) {

        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->contactUtil = $contactUtil;
        $this->transactionUtil = $transactionUtil;
        $this->taxUtil = $taxUtil;

        /** Business types */
        //$this->business_type = ['small_business', 'medium_business', 'large_business'];
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
        $type = request()->get('type');

        if (request()->ajax()) {
            if ($type == 'supplier') {
                return $this->indexSupplier();
            } elseif ($type == 'customer') {
                return $this->indexCustomer();
            } else {
                die("Not Found");
            }
        }

        return view('contact.index')
            ->with(compact('type'));
    }

    /**
     * Returns the database object for supplier
     *
     * @return \Illuminate\Http\Response
     */
    private function indexSupplier()
    {

        if (!auth()->user()->can('supplier.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $contact = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->where('contacts.business_id', $business_id)
            ->onlySuppliers()
            ->select([
                'contacts.contact_id', 'supplier_business_name', 'name', 'mobile',
                'contacts.type', 'contacts.id',
                DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
            ])
            ->groupBy('contacts.id');

        return Datatables::of($contact)
            ->addColumn(
                'due',
                '<span class="display_currency contact_due" data-orig-value="{{$total_purchase - $purchase_paid}}" data-currency_symbol=true data-highlight=false>{{$total_purchase - $purchase_paid }}</span>'
            )
            ->addColumn(
                'return_due',
                '<span class="display_currency return_due" data-orig-value="{{$total_purchase_return - $purchase_return_paid}}" data-currency_symbol=true data-highlight=false>{{$total_purchase_return - $purchase_return_paid }}</span>'
            )
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
                @if(($total_purchase + $opening_balance - $purchase_paid - $opening_balance_paid)  > 0)
                    <li><a href="{{action(\'TransactionPaymentController@getPayContactDue\', [$id])}}?type=purchase" class="pay_purchase_due"><i class="fa fa-money" aria-hidden="true"></i>@lang("contact.pay_due_amount")</a></li>
                @endif
                @if(($total_purchase_return - $purchase_return_paid)  > 0)
                    <li><a href="{{action(\'TransactionPaymentController@getPayContactDue\', [$id])}}?type=purchase_return" class="pay_purchase_due"><i class="fa fa-money" aria-hidden="true"></i>@lang("lang_v1.receive_purchase_return_due")</a></li>
                @endif
                @can("supplier.view")
                    <li><a href="{{action(\'ContactController@show\', [$id])}}"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a></li>
                @endcan
                @can("supplier.update")
                    <li><a href="{{action(\'ContactController@edit\', [$id])}}" class="edit_contact_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                @endcan
                @can("supplier.delete")
                    <li><a href="{{action(\'ContactController@destroy\', [$id])}}" class="delete_contact_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
                @endcan </ul></div>'
            )
            ->removeColumn('opening_balance')
            ->removeColumn('opening_balance_paid')
            ->removeColumn('type')
            ->removeColumn('id')
            ->removeColumn('total_purchase')
            ->removeColumn('purchase_paid')
            ->removeColumn('total_purchase_return')
            ->removeColumn('purchase_return_paid')
            ->rawColumns([4, 5, 6])
            ->make(false);
    }

    /**
     * Returns the database object for customer
     *
     * @return \Illuminate\Http\Response
     */
    private function indexCustomer()
    {

        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $contact = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('customer_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
            ->where('contacts.business_id', $business_id)
            ->onlyCustomers()
            ->addSelect([
                'contacts.contact_id', 'contacts.name', 'cg.name as customer_group', 'city', 'state', 'country', 'landmark', 'mobile', 'contacts.id', 'is_default',
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
            ])
            ->groupBy('contacts.id');

        return Datatables::of($contact)
            ->editColumn(
                'landmark',
                '{{implode(", ", array_filter([$landmark, $city, $state, $country]))}}'
            )
            ->addColumn(
                'due',
                '<span class="display_currency contact_due" data-orig-value="{{$total_invoice - $invoice_received}}" data-currency_symbol=true data-highlight=true>{{($total_invoice - $invoice_received)}}</span>'
            )
            ->addColumn(
                'return_due',
                '<span class="display_currency return_due" data-orig-value="{{$total_sell_return - $sell_return_paid}}" data-currency_symbol=true data-highlight=false>{{$total_sell_return - $sell_return_paid }}</span>'
            )
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
                @if(($total_invoice + $opening_balance - $invoice_received - $opening_balance_paid)  > 0)
                    <li><a href="{{action(\'TransactionPaymentController@getPayContactDue\', [$id])}}?type=sell" class="pay_sale_due"><i class="fa fa-money" aria-hidden="true"></i>@lang("contact.pay_due_amount")</a></li>
                @endif
                @if(($total_sell_return - $sell_return_paid)  > 0)
                    <li><a href="{{action(\'TransactionPaymentController@getPayContactDue\', [$id])}}?type=sell_return" class="pay_purchase_due"><i class="fa fa-money" aria-hidden="true"></i>@lang("lang_v1.pay_sell_return_due")</a></li>
                @endif
                @can("customer.view")
                    <li><a href="{{action(\'ContactController@show\', [$id])}}"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a></li>
                @endcan
                @can("customer.update")
                    <li><a href="{{action(\'ContactController@edit\', [$id])}}" class="edit_contact_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                @endcan
                @if(!$is_default)
                @can("customer.delete")
                    <li><a href="{{action(\'ContactController@destroy\', [$id])}}" class="delete_contact_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
                @endcan
                @endif </ul></div>'
            )
            ->removeColumn('total_invoice')
            ->removeColumn('opening_balance')
            ->removeColumn('opening_balance_paid')
            ->removeColumn('invoice_received')
            ->removeColumn('state')
            ->removeColumn('country')
            ->removeColumn('city')
            ->removeColumn('type')
            ->removeColumn('id')
            ->removeColumn('is_default')
            ->removeColumn('total_sell_return')
            ->removeColumn('sell_return_paid')
            ->rawColumns([5, 6, 7])
            ->make(false);
    }

    public function verifiedIfExistsNIT()
    {
        if (request()->ajax()) {
            $business_id = auth()->user()->business_id;
            $output = ['error' => true, 'fail' => 'validate_tax_number_fail'];
            if (request()->nit && request()->id) {
                // This part is used for when the supplier id is sent in the ajax, specifically the edit section.
                $nit = Contact::where('id', '<>', request()->id)
                    ->where('business_id', $business_id)->where('nit', request()->nit)->exists();

                if ($nit) {
                    $output = ['success' => false, 'msg' => trans("customer.validate_tax_number_error")];
                } else {
                    $output = ['success' => true, 'msg' => trans("customer.validate_tax_number_success")];
                }
            } else if (request()->nit) {
                // Check if there are records in the database that are the same as the input.
                $nit = Contact::where('business_id', $business_id)->where('nit', request()->nit)->exists();
                if ($nit) {
                    $output = ['success' => false, 'msg' => trans("customer.validate_tax_number_error")];
                } else {
                    $output = ['success' => true, 'msg' => trans("customer.validate_tax_number_success")];
                }
            }

            return $output;
        }
    }

    public function verifiedTaxNumberPurchases()
    {
        if (request()->ajax()) {
            $output = [];
            if (request()->contact_id) {
                $business_id = auth()->user()->business_id;
                $contact = Contact::where('business_id', $business_id)->where('id', request()->contact_id)->first();
                if ((!empty($contact->tax_number) && !is_null($contact->tax_number)) &&
                    (!empty($contact->nit) && !is_null($contact->nit))
                ) {
                    $output = ['success' => true, 'msg' => trans("customer.customer_has_yes_nit_nrc")];
                } else {
                    $output = ['success' => false, 'msg' => trans("customer.customer_has_no_nit_nrc")];
                }
            }
            return $output;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
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

        // Llenar Select de Vendedores
        $employees_sales = Employees::forDropdown($business_id);
        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');
        /** Business type */
        $business_type = BusinessType::select('id', 'name')->whereIn('name', ['OTROS', 'Mediana Empresa', 'Gran Empresa'])
            ->pluck('name', 'id');
        /** Payment conditions */
        $payment_conditions = $this->payment_conditions;
        /** Get supplier main account */
        $business = Business::find($business_id);
        $supplier_account = "";
        if ($business->accounting_supplier_id) {
            $supplier_account =
                Catalogue::where("status", 1)
                ->where("id", $business->accounting_supplier_id)
                ->value("code");
        }
        $business_debt_to_pay_type = $business->debt_to_pay_type;
        /* Countries */
        $countries = Country::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $payment_terms = PaymentTerm::select('id', 'name')
            ->pluck('name', 'id');
        $org_type = ['natural' => __('business.natural'), 'juridica' => __('business.juridica')];

        return view('contact.create')
            ->with(compact('types', 'employees_sales', 'tax_groups', 'business_type', 'payment_conditions', 'supplier_account', 'countries', 'payment_terms', 'business_debt_to_pay_type', 'org_type'));
    }

    /** Only for testing */
    public function getTests()
    {
        //$business_id = request()->input('business_id');
        $tax_id = request()->input('tax_id');
        $type = request()->input('type');
        $transaction_id = request()->input('transaction_id');
        $customer_id = request()->input('customer_id');
        //$percent = request()->input('percent');

        //return var_dump($this->taxUtil->getTaxName($tax_id, $type));
        //return $this->transactionUtil->getDocumentTypePrintFormat($transaction_id);
        return $this->contactUtil->getCustomerEmployeeName($customer_id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $type = request()->get('type');

            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }

            if ($request->only(['type']) == 'supplier') {
                $input_brand = $request->only(['description']);
                $input_brand['name'] = $request->input('name1');
                $business_id = $request->session()->get('user.business_id');
                $input_brand['business_id'] = $business_id;
                $input_brand['created_by'] = $request->session()->get('user.id');
            }


            $input = $request->only([
                'supplier_business_name',
                'payment_condition',
                'name',
                'tax_number',
                'business_type_id',
                'business_activity',
                'payment_term_id',
                'mobile',
                'landline',
                'organization_type',
                'alternate_number',
                'city_id',
                'state_id',
                'country_id',
                'landmark',
                'contact_id',
                'custom_field1',
                'custom_field2',
                'custom_field3',
                'custom_field4',
                'email',
                'nit',
                'is_exempt',
                'dni'
            ]);

            $input['type'] = 'supplier';
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            $lastContactId = Contact::select('contact_id')->latest()->first();
            $input['contact_id'] = str_pad(((int)$lastContactId->contact_id + 1), 4, '0', STR_PAD_LEFT);

            if ($request->is_exempt) {
                $input['tax_group_id'] = null;
            } else {
                $input['tax_group_id'] = $request->input('tax_group_id') != 0 ? $request->input('tax_group_id') : null;
            }

            $credit_limit = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
            $payment_condition = '';

            $input['payment_term_id'] = $payment_condition == 'credit' ? $request->input('payment_term_id') : null;

            $input['credit_limit'] = $payment_condition == 'credit' ? $credit_limit : null;

            $input['is_supplier'] = $request->input("is_supplier") ? $request->input("is_supplier") : null;
            $input['is_provider'] = $request->input("is_provider") ? $request->input("is_provider") : null;
            $input['is_exempt'] = $request->input("is_exempt") ? $request->input("is_exempt") : null;
            $input['supplier_catalogue_id'] = $input['is_supplier'] ? $request->input("supplier_catalogue_id") : null;
            $input['provider_catalogue_id'] = $input['is_provider'] ? $request->input("provider_catalogue_id") : null;

            //Check Contact id
            $count = 0;
            if (!empty($input['contact_id'])) {
                $count = Contact::where('business_id', $input['business_id'])
                    ->where('contact_id', $input['contact_id'])
                    ->count();
            }

            if ($count == 0) {
                //Update reference count
                $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts');

                if (empty($input['contact_id'])) {
                    //Generate reference number
                    $input['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count);
                }

                $contact = Contact::create($input);

                if ($request->only(['type']) == 'supplier') {

                    $brands = new Brands;
                    $brands->fill($input_brand);
                    // Guardamos el usuario
                    $brands->save($input_brand);
                }
                //Add opening balance
                if (!empty($request->input('opening_balance'))) {
                    $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'));
                }

                $output = [
                    'success' => true,
                    'data' => $contact,
                    'msg' => __("contact.added_success")
                ];
                return redirect()->action('ContactController@index', ['type' => 'supplier'])->with('status', $output);
            } else {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
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
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $contact = Contact::where('contacts.id', $id)
            ->leftJoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->select(
                DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type = 'sell', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                DB::raw("SUM(IF(t.type = 'sell', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                'contacts.*'
            )->first();

        return view('contact.show')
            ->with(compact('contact'));
    }
    public function showSupplier($id)
    {
        if (!auth()->user()->can('supplier.view')) {
            abort(403, 'Unauthorized action.');
        }
        $supplier = Contact::where('id', $id)->first();
        return response()->json($supplier);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('supplier.update') && !auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $contact = Contact::where('business_id', $business_id)->find($id);

            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
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

            //$customer_groups = CustomerGroup::forDropdown($business_id);

            $ob_transaction =  Transaction::where('contact_id', $id)
                ->where('type', 'opening_balance')
                ->first();
            $opening_balance = !empty($ob_transaction->final_total) ? $this->commonUtil->num_f($ob_transaction->final_total) : 0;
            /** Tax groups */
            $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');
            // Llenar Select de Vendedores
            $employees_sales = Employees::forDropdown($business_id);
            /** Business type */
            $business_type = BusinessType::select('id', 'name')->whereIn('name', ['OTROS', 'Mediana Empresa', 'Gran Empresa'])
                ->pluck('name', 'id');
            /** Payment conditions */
            $payment_conditions = $this->payment_conditions;

            $countries = Country::select('id', 'name')
                ->where('business_id', $business_id)
                ->pluck('name', 'id');

            $states = State::select('id', 'name')
                ->where('business_id', $business_id)
                ->where('country_id', $contact->country_id)
                ->pluck('name', 'id');

            $cities = City::select('id', 'name')
                ->where('business_id', $business_id)
                ->where('state_id', $contact->state_id)
                ->pluck('name', 'id');

            $payment_terms = PaymentTerm::select('id', 'name')
                ->pluck('name', 'id');

            /** get supplier and provider account name */
            $account_name = [];
            if ($contact->is_supplier && $contact->supplier_catalogue_id) {
                $catalogue = Catalogue::where("status", 1)
                    ->where("id", $contact->supplier_catalogue_id)
                    ->select(DB::raw("CONCAT(code, ' ', name) as name"))
                    ->first();

                $account_name[] = [$contact->supplier_catalogue_id => $catalogue->name];
            }

            if ($contact->is_provider && $contact->provider_catalogue_id) {
                $catalogue = Catalogue::where("status", 1)
                    ->where("id", $contact->provider_catalogue_id)
                    ->select(DB::raw("CONCAT(code, ' ', name) as name"))
                    ->first();

                $account_name[] = [$contact->provider_catalogue_id => $catalogue->name];
            }

            /** Get supplier main account */
            $business = Business::find($business_id);
            $supplier_account = "";
            if ($business->accounting_supplier_id) {
                $supplier_account =
                    Catalogue::where("status", 1)
                    ->where("id", $business->accounting_supplier_id)
                    ->value("code");
            }
            $business_debt_to_pay_type = $business->debt_to_pay_type;
            $org_type = ['natural' => __('business.natural'), 'juridica' => __('business.juridica')];
            return view('contact.edit')
                ->with(compact(
                    'org_type',
                    'contact',
                    'types',
                    'opening_balance',
                    'supplier_account',
                    'tax_groups',
                    'business_type',
                    'payment_conditions',
                    'employees_sales',
                    'account_name',
                    'countries',
                    'states',
                    'cities',
                    'payment_terms',
                    'business_debt_to_pay_type',
                ));
        }
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

        try {
            if (!auth()->user()->can('supplier.update') && !auth()->user()->can('customer.update')) {
                abort(403, 'Unauthorized action.');
            }

            if (request()->ajax()) {
                try {
                    $input = $request->only([
                        'supplier_business_name',
                        'name',
                        'nit',
                        'tax_number',
                        'payment_term_id',
                        'mobile',
                        'landline',
                        'employee_id',
                        'alternate_number',
                        'city_id',
                        'state_id',
                        'country_id',
                        'landmark',
                        'payment_condition',
                        'organization_type',
                        'contact_id',
                        'business_type_id',
                        'custom_field1',
                        'custom_field2',
                        'custom_field3',
                        'custom_field4',
                        'email',
                        'business_activity',
                        'dni'
                    ]);

                    $credit_limit = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
                    $payment_condition = '';

                    $input['payment_term_id'] = $payment_condition == 'credit' ? $request->input('payment_term_id') : null;

                    $input['credit_limit'] = $payment_condition == 'credit' ? $credit_limit : null;

                    $input['is_supplier'] = $request->input("is_supplier") ? $request->input("is_supplier") : null;
                    $input['is_provider'] = $request->input("is_provider") ? $request->input("is_provider") : null;
                    $input['is_exempt'] = $request->input("is_exempt") ? $request->input("is_exempt") : null;
                    $input['supplier_catalogue_id'] = $input['is_supplier'] ? $request->input("supplier_catalogue_id") : null;
                    $input['provider_catalogue_id'] = $input['is_provider'] ? $request->input("provider_catalogue_id") : null;
                    
                    if ($request->is_exempt) {
                        $input['tax_group_id'] = null;
                    } else {
                        $input['tax_group_id'] = $request->input('tax_group_id') != 0 ? $request->input('tax_group_id') : null;
                    }

                    $business_id = $request->session()->get('user.business_id');

                    if (!$this->moduleUtil->isSubscribed($business_id)) {
                        return $this->moduleUtil->expiredResponse();
                    }

                    $count = 0;

                    //Check Contact id
                    if (!empty($input['contact_id'])) {
                        $count = Contact::where('business_id', $business_id)
                            ->where('contact_id', $input['contact_id'])
                            ->where('id', '!=', $id)
                            ->count();
                    }
                    if ($count == 0) {
                        $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                        foreach ($input as $key => $value) {
                            $contact->$key = $value;
                        }
                        $contact->save();

                        //Get opening balance if exists
                        $ob_transaction =  Transaction::where('contact_id', $id)
                            ->where('type', 'opening_balance')
                            ->first();

                        if (!empty($ob_transaction)) {
                            $amount = $this->commonUtil->num_uf($request->input('opening_balance'));
                            $ob_transaction->final_total = $amount;
                            $ob_transaction->save();
                            //Update opening balance payment status
                            $this->transactionUtil->updatePaymentStatus($ob_transaction->id, $ob_transaction->final_total);
                        } else {
                            //Add opening balance
                            if (!empty($request->input('opening_balance'))) {
                                $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'));
                            }
                        }

                        $output = [
                            'success' => true,
                            'msg' => __("contact.updated_success")
                        ];
                    } else {
                        throw new \Exception("Error Processing Request", 1);
                    }
                } catch (\Exception $e) {
                    \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                    $output = [
                        'success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
                }

                return $output;
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
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
        if (!auth()->user()->can('supplier.delete') && !auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                //Check if any transaction related to this contact exists
                $count = Transaction::where('business_id', $business_id)
                    ->where('contact_id', $id)
                    ->count();
                if ($count == 0) {
                    $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                    if (!$contact->is_default) {
                        $contact->delete();
                    }
                    $output = [
                        'success' => true,
                        'msg' => __("contact.deleted_success")
                    ];
                } else {
                    $output = [
                        'success' => false,
                        'msg' => __("lang_v1.you_cannot_delete_this_contact")
                    ];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Retrieves list of customers, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getCustomers()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');

            $business_id = request()->session()->get('user.business_id');

            $contacts = Contact::leftJoin('tax_rate_tax_group AS trtg', 'contacts.tax_group_id', 'trtg.tax_group_id')
                ->leftJoin('tax_rates as tr', 'trtg.tax_rate_id', 'tr.id')
                ->where('contacts.business_id', $business_id);

            $selected_contacts = User::isSelectedContacts($user_id);
            if ($selected_contacts) {
                $contacts->join('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
                    ->where('uca.user_id', $user_id);
            }

            if (!empty($term)) {
                $contacts->where(function ($query) use ($term) {
                    $query->where('contacts.name', 'like', '%' . $term . '%')
                        ->orWhere('contacts.supplier_business_name', 'like', '%' . $term . '%')
                        ->orWhere('contacts.mobile', 'like', '%' . $term . '%')
                        ->orWhere('contacts.contact_id', 'like', '%' . $term . '%');
                });
            }

            $contacts = $contacts->select(
                'contacts.id',
                'contacts.name as text',
                'contacts.mobile',
                'contacts.landmark',
                'contacts.city',
                'contacts.state',
                'contacts.payment_condition',
                'contacts.tax_group_id',
                'tr.percent as tax_percent',
                'tr.min_amount',
                'tr.max_amount'
            )
                ->onlyCustomers()
                ->get();
            return json_encode($contacts);
        }
    }

    /**
     * Retrieves list of customers, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');
            $business_id = request()->session()->get('user.business_id');

            $contacts = Contact::where('contacts.type', 'supplier')
                ->where('contacts.business_id', $business_id);

            if (!empty($term)) {
                $contacts->where(function ($query) use ($term) {
                    $query->where('contacts.name', 'like', '%' . $term . '%')
                        ->orWhere('contacts.supplier_business_name', 'like', '%' . $term . '%')
                        ->orWhere('contacts.contact_id', 'like', '%' . $term . '%')
                        ->orWhere('contacts.tax_number', 'like', '%' . $term . '%');
                });
            }
            $contacts = $contacts->select('contacts.id', 'contacts.supplier_business_name as text')->get();

            return json_encode($contacts);
        }
    }

    /**
     * Checks if the given contact id already exist for the current business.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkContactId(Request $request)
    {
        $contact_id = $request->input('contact_id');

        $valid = 'true';
        if (!empty($contact_id)) {
            $business_id = $request->session()->get('user.business_id');
            $hidden_id = $request->input('hidden_id');

            $query = Contact::where('business_id', $business_id)
                ->where('contact_id', $contact_id);
            if (!empty($hidden_id)) {
                $query->where('id', '!=', $hidden_id);
            }
            $count = $query->count();
            if ($count > 0) {
                $valid = 'false';
            }
        }
        echo $valid;
        exit;
    }

    /**
     * Shows import option for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getImportContacts()
    {

        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import'
            ];

            return view('contact.import')
                ->with('notification', $output);
        } else {
            return view('contact.import');
        }
    }

    /**
     * Imports contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postImportContacts(Request $request)
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('contacts_xlsx')) {
                $file = $request->file('contacts_xlsx');
                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[1];

                //removing the header
                unset($imported_data[0]);
                unset($imported_data[1]);
                unset($imported_data[2]);
                unset($imported_data[3]);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    //Check if 23 no. of columns exists
                    if (count($value) != 23) {
                        $is_valid =  false;
                        $error_msg = "Number of columns mismatch in row " . $key;
                        break;
                    }

                    $row_no = $key + 1;

                    //Set supplier
                    $contact_array['type'] = "supplier";

                    //Check contact name
                    if (!empty($value[0])) {
                        $contact_array['name'] = substr($value[0], 0, 100);
                    } else {
                        $is_valid =  false;
                        $error_msg = "Contact name is required in row no. $row_no";
                        break;
                    }

                    //Check business name
                    if (!empty(trim($value[1]))) {
                        $contact_array['supplier_business_name'] = substr($value[1], 0, 100);
                    } else {
                        $is_valid =  false;
                        $error_msg = "Business name is required in row no. $row_no";
                        break;
                    }

                    //Check code
                    if (!empty(trim($value[2]))) {
                        $count = Contact::where('business_id', $business_id)
                            ->where('contact_id', $value[2])
                            ->count();


                        if ($count == 0) {
                            $contact_array['contact_id'] = substr(trim($value[2]), 0, 100);
                        } else {
                            $is_valid =  false;
                            $error_msg = "Code already exists in row no. $row_no";
                            break;
                        }
                    }

                    //Tax number (NRC)
                    if (!empty(trim($value[3]))) {
                        $contact_array['tax_number'] = substr(trim($value[3]), 0, 10);
                    } else {
                        $is_valid =  false;
                        $error_msg = "NRC is required in row no. $row_no";
                        break;
                    }

                    //NIT
                    if (!empty(trim($value[4]))) {
                        $contact_array['nit'] = substr(trim($value[4]), 0, 20);
                    } else {
                        $is_valid =  false;
                        $error_msg = "NIT is required in row no. $row_no";
                        break;
                    }

                    //Business activity
                    if (!empty(trim($value[5]))) {
                        $contact_array['business_activity'] = substr(trim($value[5]), 0, 190);
                    } else {
                        $is_valid =  false;
                        $error_msg = "Business activity is required in row no. $row_no";
                        break;
                    }

                    //Check (is_exempt)
                    $is_exempt = trim($value[7]);
                    if ($is_exempt == "0" || $is_exempt == "1") {
                        $contact_array['is_exempt'] = $is_exempt;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Exempt is required in row no. $row_no";
                        break;
                    }
                    if ($is_exempt == "0") {

                        // Check business type
                        if (!empty(trim($value[6]))) {
                            $business_type = BusinessType::whereRaw('upper(name) = upper("' . trim($value[6]) . '")')->first();
                            if (!empty($business_type)) {
                                if ($business_type->name == 'Gran Empresa') {
                                    $tax_group = TaxGroup::whereRaw("upper(description) = 'PERCEPCIÓN'")
                                        ->where('type', 'contacts')->first();

                                    if (!empty($tax_group)) {
                                        $contact_array['tax_group_id'] = $tax_group->id;
                                    } else {
                                        $is_valid =  false;
                                        $error_msg = __('lang_v1.required_tax_group', ['number' => $row_no]);
                                        break;
                                    }
                                }
                                $contact_array['business_type_id'] = $business_type->id;
                            } else {
                                $is_valid =  false;
                                $error_msg = "Business type not found in row no. $row_no";
                                break;
                            }
                        } else {
                            $is_valid =  false;
                            $error_msg = "Business type is required in row no. $row_no";
                            break;
                        }
                    } else {
                        $contact_array['tax_group_id'] = null;

                        // Check business type
                        if (!empty(trim($value[6]))) {
                            $business_type = BusinessType::whereRaw('upper(name) = upper("' . trim($value[6]) . '")')->first();
                            if (!empty($business_type)) {
                                $contact_array['business_type_id'] = $business_type->id;
                            } else {
                                $is_valid =  false;
                                $error_msg = "Business type not found in row no. $row_no";
                                break;
                            }
                        } else {
                            $is_valid =  false;
                            $error_msg = "Business type is required in row no. $row_no";
                            break;
                        }
                    }

                    //Check opening balance
                    if (!empty(trim($value[8])) && $value[8] != 0) {
                        $contact_array['opening_balance'] = trim($value[8]);
                    }

                    //Check payment condition
                    $payment_condition = strtolower(trim($value[9]));
                    if (in_array($payment_condition, ['1', '0'])) {
                        $payment_condition = $payment_condition == '1' ? "credit" : $payment_condition;
                        $payment_condition = $payment_condition == '0' ? "cash" : $payment_condition;

                        $contact_array['payment_condition'] = $payment_condition;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Payment Condition is required in row no. $row_no";
                        break;
                    }

                    //Check if the payment condition is credit
                    if ($contact_array['payment_condition'] == 'credit') {
                        $days = trim($value[10]);
                        // Check payment terms
                        if (!empty($days)) {
                            $payment_term = PaymentTerm::where('days', $days)->first();
                            if (!empty($payment_term)) {
                                $contact_array['payment_term_id'] = $payment_term->id;
                            } else {
                                $is_valid =  false;
                                $error_msg = "Payment term not found in row no. $row_no";
                                break;
                            }
                        } else {
                            $is_valid =  false;
                            $error_msg = "Payment term is required in row no. $row_no";
                            break;
                        }

                        //Check credit limit
                        if (!empty(trim($value[11]))) {
                            $contact_array['credit_limit'] = $this->transactionUtil->num_uf(trim($value[11]));
                        } else {
                            $is_valid =  false;
                            $error_msg = "Credit limit is required in row no. $row_no";
                            break;
                        }
                    }

                    //Check email
                    if (!empty(trim($value[12]))) {
                        if (filter_var(trim($value[12]), FILTER_VALIDATE_EMAIL)) {
                            $contact_array['email'] = $value[12];
                        } else {
                            $is_valid =  false;
                            $error_msg = "Invalid email id in row no. $row_no";
                            break;
                        }
                    }

                    //Mobile number
                    if (!empty(trim($value[13]))) {
                        $contact_array['mobile'] = $value[13];
                    } else {
                        $is_valid =  false;
                        $error_msg = "Mobile number is required in row no. $row_no";
                        break;
                    }

                    //Landline
                    $contact_array['landline'] = trim($value[14]);

                    // Check country
                    $country = null;
                    if (!empty(trim($value[17]))) {
                        $country = Country::whereRaw('upper(name) = upper("' . trim($value[17]) . '")')->first();
                        if (!empty($country)) {
                            $contact_array['country_id'] = $country->id;
                        } else {
                            $is_valid =  false;
                            $error_msg = "Country not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $contact_array['country_id'] = $country;
                    }

                    // Check state
                    $state = null;
                    if (!empty(trim($value[16])) && !is_null($country)) {
                        $state = State::whereRaw('upper(name) = upper("' . trim($value[16]) . '")')
                            ->where('country_id', $country->id)->first();
                        if (!empty($state)) {
                            $contact_array['state_id'] = $state->id;
                            $zone = Zone::where('id', $state->zone_id)->first();
                            $contact_array['zone_id'] = $zone->id;
                        } else {
                            $is_valid =  false;
                            $error_msg = "State not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $contact_array['state_id'] = $state;
                    }

                    // Check city
                    $city = null;
                    if (!empty(trim($value[15])) && !is_null($state)) {
                        $city = City::whereRaw('upper(name) = upper("' . trim($value[15]) . '")')
                            ->where('state_id', $state->id)->first();
                        if (!empty($city)) {
                            $contact_array['city_id'] = $city->id;
                        } else {
                            $is_valid =  false;
                            $error_msg = "City not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $contact_array['city_id'] = $city;
                    }

                    //Landmark
                    if (!empty(trim($value[18]))) {
                        $contact_array['landmark'] = trim($value[18]);
                    } else {
                        $is_valid =  false;
                        $error_msg = "Landmark is required in row no. $row_no";
                        break;
                    }

                    //Is supplier
                    $is_supplier = trim($value[19]);
                    if ($is_supplier == "0" || $is_supplier == "1") {
                        $contact_array['is_supplier'] = $is_supplier;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Supplier is required in row no. $row_no";
                        break;
                    }

                    //Is provider
                    $is_provider = trim($value[20]);
                    if ($is_provider == "0" || $is_provider == "1") {
                        $contact_array['is_provider'] = $is_provider;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Provider is required in row no. $row_no";
                        break;
                    }

                    //supplier accounting account
                    $supplier_account = trim($value[21]);
                    if ($supplier_account) {
                        $account = intval($supplier_account);

                        if ($account) {
                            $catalogue = Catalogue::where('status', 1)->where('code', $account)->first();

                            if ($catalogue) {
                                $contact_array['supplier_catalogue_id'] = $catalogue->id;
                            } else {
                                $is_valid =  false;
                                $error_msg = "Invalid accounting account in row no. $row_no";
                                break;
                            }
                        } else {
                            $is_valid =  false;
                            $error_msg = "Accounting account must be integer in row no. $row_no";
                            break;
                        }
                    }

                    //provider accounting account
                    $provider_account = trim($value[22]);
                    if ($provider_account) {
                        $account = intval($provider_account);

                        if ($account) {
                            $catalogue = Catalogue::where('status', 1)->where('code', $account)->first();

                            if ($catalogue) {
                                $contact_array['provider_catalogue_id'] = $catalogue->id;
                            } else {
                                $is_valid =  false;
                                $error_msg = "Invalid accounting account in row no. $row_no";
                                break;
                            }
                        } else {
                            $is_valid =  false;
                            $error_msg = "Accounting account must be integer in row no. $row_no";
                            break;
                        }
                    }

                    $formated_data[] = $contact_array;
                }
                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }

                if (!empty($formated_data)) {
                    foreach ($formated_data as $contact_data) {
                        $ref_count = $this->transactionUtil->setAndGetReferenceCount('contacts');
                        //Set contact id if empty
                        if (empty($contact_data['contact_id'])) {
                            $contact_data['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count);
                        }

                        $opening_balance = 0;
                        if (isset($contact_data['opening_balance'])) {
                            $opening_balance = $contact_data['opening_balance'];
                            unset($contact_data['opening_balance']);
                        }

                        $contact_data['business_id'] = $business_id;
                        $contact_data['created_by'] = $user_id;

                        $contact = Contact::create($contact_data);

                        if (!empty($opening_balance)) {
                            $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $opening_balance);
                        }
                    }
                }

                $output = [
                    'success' => 1,
                    'msg' => __('product.file_imported_successfully')
                ];

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            return redirect()->route('contacts.import')->with('notification', $output);
        }

        return redirect()->action('ContactController@index', ['type' => 'supplier'])->with('status', $output);
    }

    /**
     * Verify tax number and Reg number
     * @param int $contact_id
     * @return array
     */
    public function verifyTaxNumberAndRegNumber()
    {
        if (request()->ajax()) {
            $output = [];
            if (request()->contact_id) {
                $business_id = auth()->user()->business_id;
                $contact = Contact::where('business_id', $business_id)->where('id', request()->contact_id)->first();
                if ((!empty($contact->tax_number) && !is_null($contact->tax_number)) &&
                    (!empty($contact->nit) && !is_null($contact->nit))
                ) {
                    $output = ['success' => true, 'msg' => trans("customer.customer_has_yes_nit_nrc")];
                } else {
                    $output = ['success' => false, 'msg' => trans("customer.customer_has_no_nit_nrc")];
                }
            }
            return $output;
        }
    }

    /**
     * Check if there is another provider with the same DUI.
     * 
     * @return array
     */
    public function verifiedIfExistsDUI()
    {
        if (request()->ajax()) {
            $business_id = auth()->user()->business_id;

            $output = [
                'error' => true,
                'fail' => 'validate_tax_number_fail'
            ];

            if (request()->dni && request()->id) {
                // This part is used for when the supplier id is sent in the ajax, specifically the edit section
                $dui = Contact::where('id', '!=', request()->id)
                    ->where('business_id', $business_id)
                    ->where('dni', request()->dni)
                    ->exists();

                if ($dui) {
                    $output = [
                        'success' => false,
                        'msg' => trans('customer.validate_dni_number_error')
                    ];
                } else {
                    $output = [
                        'success' => true,
                        'msg' => trans('customer.validate_dni_success')
                    ];
                }
            } else if (request()->dni) {
                // Check if there are records in the database that are the same as the input
                $dui = Contact::where('business_id', $business_id)
                    ->where('dni', request()->dni)
                    ->exists();

                if ($dui) {
                    $output = [
                        'success' => false,
                        'msg' => trans('customer.validate_tax_number_error')
                    ];
                } else {
                    $output = [
                        'success' => true,
                        'msg' => trans('customer.validate_tax_number_success')
                    ];
                }
            }

            return $output;
        }
    }
}
