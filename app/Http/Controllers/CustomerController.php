<?php

namespace App\Http\Controllers;

use App\Brands;
use DB;
use Excel;
use App\Business;
use App\Catalogue;
use App\City;
use App\Zone;
use App\State;
use DataTables;
use App\Contact;
use App\Country;
use App\Category;
use App\Customer;
use App\TaxGroup;
use App\PaymentTerm;
use App\BusinessType;
use App\CustomerGroup;
use App\Utils\TaxUtil;
use App\CRMContactMode;
use App\CustomerContact;
use App\BusinessLocation;
use App\CRMContactReason;

use App\CustomerPortfolio;
use App\CustomerVehicle;
use App\Employees;
use App\Exports\AccountsReceivableReportExport;
use App\Optics\LabOrder;
use App\Optics\Patient;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use App\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\Util;

class CustomerController extends Controller
{
    /**
     * Constructor.
     *
     * @param  \App\Utils\TransactionUtil  $transactionUtil
     * @param  \App\Utils\TaxUtil  $taxUtil
     * @param  \App\Utils\BusinessUtil  $businessUtil
     * @param  \App\Utils\Util  $util
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, TaxUtil $taxUtil, BusinessUtil $businessUtil, Util $util)
    {
        $this->transactionUtil = $transactionUtil;
        $this->taxUtil = $taxUtil;
        $this->businessUtil = $businessUtil;
        $this->util = $util;

        // Binnacle data
        $this->module_name = 'customer';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Follow customer
        $business_id = request()->session()->get('user.business_id');

        //Llenar select de Contact Reason
        $contactreason = CRMContactReason::forDropdown($business_id);

        //Llenar select de Known By
        $known_by = CRMContactMode::forDropdown($business_id);

        //Llenar select de Contact Mode
        $contactmode = CRMContactMode::where('name', 'not like', "%cliente%")->pluck('name', 'id');

        //Llenar select de Category
        $categories = Category::forDropdown($business_id);

        //Llenar select de Clientes
        $clients = Contact::where('business_id', $business_id)->whereIn('type', ['customer', 'both'])->pluck('name', 'id');

        $countries = Country::forDropdown($business_id);

        $locations = BusinessLocation::select('name', 'id')->where('business_id', $business_id)->get();

        /** customer portfolio */
        $customer_portfolios =
            CustomerPortfolio::where('business_id', $business_id)
                ->where('status', 1)
                ->pluck('name', 'id');

        $products = DB::table('variations')
            ->join('products', 'products.id', '=', 'variations.product_id')
            ->select('products.name as name_product', 'variations.name as name_variation', 'variations.id', 'variations.sub_sku', 'products.sku')
            ->where('business_id', $business_id)
            ->where('products.clasification', '<>', 'kits')
            ->where('products.clasification', '<>', 'service')
            ->where('products.status', 'active')
            ->get();

        return view('customer.index',
            compact('contactreason', 'known_by', 'categories', 'clients', 'contactmode', 'countries', 'products', 'locations', 'customer_portfolios'));
    }


    public function verifiedIfExistsDocument($type, $value)
    {
        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            //verifica si hay registtos en la base de datos
            if ($type == 'dni') {
                if (Customer::where('dni', $value)->where('business_id', $business_id)->exists()) {
                    $output = [
                        'success' => true,
                        'msg' => trans('customer.DNI_invalid')
                    ];
                    return  $output;
                } else {
                    $output = [
                        'success' => false,
                        'msg' => trans('customer.DNI_valid')
                    ];
                    return  $output;
                }
            } else if ($type == 'reg_number') {
                if (Customer::where('reg_number', $value)->where('business_id', $business_id)->exists()) {
                    $output2 = [
                        'success' => true,
                        'msg' => trans('customer.num_reg_invalid')
                    ];
                    return  $output2;
                } else {
                    $output2 = [
                        'success' => false,
                        'msg' => trans('customer.num_reg_valid'),
                    ];
                    return  $output2;
                }
            }
        }
    }

    //validacion para la vista edit de clientes 
    public function verifiedIfExistsDocumentID($type, $value, $id = '')
    {
        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            //verifica si hay registtos en la base de datos
            //omite el id del clienre que se manda por parametro, es opcional y sirve para la vista edir
            if ($type == 'dni' && $id != '') {
                if (Customer::where('id', '<>', $id)->where('dni', $value)->where('business_id', $business_id)->exists()) {
                    $output = [
                        'success' => true,
                        'msg' => trans('customer.DNI_invalid')
                    ];
                    return  $output;
                } else {
                    $output = [
                        'success' => false,
                        'msg' => trans('customer.DNI_valid')
                    ];
                    return  $output;
                }
            } else if ($type == 'reg_number' && $id != null) {
                if (Customer::where('id', '<>', $id)->where('reg_number', $value)->where('business_id', $business_id)->exists()) {
                    $output2 = [
                        'success' => true,
                        'msg' => trans('customer.num_reg_invalid')
                    ];
                    return  $output2;
                } else {
                    $output2 = [
                        'success' => false,
                        'msg' => trans('customer.num_reg_valid'),
                    ];
                    return  $output2;
                }
            }
        }
    }

    public function verifiedIfExistsTaxNumber()
    {
        if (request()->ajax()) {
            $business_id = auth()->user()->business_id;
            $output = ['error' => true, 'fail' => 'validate_tax_number_fail'];
            if (request()->tax_number && request()->customer_id) {
                // This part is used for when the client id is sent in the ajax, specifically the edit section.
                $tax_number = Customer::where('id', '<>', request()->customer_id)
                    ->where('business_id', $business_id)->where('tax_number', request()->tax_number)->exists();

                if ($tax_number) {
                    $output = ['success' => false, 'msg' => trans("customer.validate_tax_number_error")];
                } else {
                    $output = ['success' => true, 'msg' => trans("customer.validate_tax_number_success")];
                }
            } else if (request()->tax_number) {
                // Check if there are records in the database that are the same as the input.
                $tax_number = Customer::where('business_id', $business_id)->where('tax_number', request()->tax_number)->exists();
                if ($tax_number) {
                    $output = ['success' => false, 'msg' => trans("customer.validate_tax_number_error")];
                } else {
                    $output = ['success' => true, 'msg' => trans("customer.validate_tax_number_success")];
                }
            }

            return $output;
        }
    }

    public function verifiedTaxNumberSellPos()
    {
        if (request()->ajax()) {
            $output = [];
            if (request()->customer_id) {
                $business_id = auth()->user()->business_id;
                $customer = Customer::where('business_id', $business_id)->where('id', request()->customer_id)->first();
                if ((!empty($customer->tax_number) && !is_null($customer->tax_number)) &&
                    (!empty($customer->reg_number) && !is_null($customer->reg_number))
                ) {
                    $output = ['success' => true, 'msg' => trans("customer.customer_has_no_nit_nrc")];
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
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_types = BusinessType::select('id', 'name')
            ->pluck('name', 'id');

        $customer_portfolios = CustomerPortfolio::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $customer_groups = CustomerGroup::all();

        $countries = Country::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $payment_terms = PaymentTerm::select('id', 'name')
            ->pluck('name', 'id');

        // Contact modes
        $contact_modes = CRMContactMode::where('business_id', $business_id)
            ->pluck('name', 'id');

        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');                        

        $main_customer_account = "";

        $business = Business::find($business_id);
        if ($business->accounting_customer_id) {
            $main_customer_account = Catalogue::where("status", 1)
                                    ->where("id", $business->accounting_customer_id)
                                    ->value("code");
        }

        $business_receivable_type = $business->receivable_type;

        // Locate NIT in general information section
        $customer_settings = empty($business->customer_settings) ? null : json_decode($business->customer_settings, true);
        $nit_in_general_info = empty($customer_settings) ? 0 : $customer_settings['nit_in_general_info'];

        return view('customer.create', compact(
            'business_types',
            'customer_portfolios',
            'customer_groups',
            'countries',
            'payment_terms',
            'contact_modes',
            'tax_groups',
            'main_customer_account',
            'business_receivable_type',
            'nit_in_general_info'
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
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        // return $request;
        $is_taxpayer = $request->input('is_taxpayer');
        $allowed_credit = $request->input('allowed_credit');

        if (empty($request->input('email'))) {
            $validateData = $request->validate(
                [
                    'name' => 'required',
                    'telphone' => 'required',
                ]
            );
        } elseif (empty($request->input('telphone'))) {
            $validateData = $request->validate(
                [
                    'name' => 'required',
                    'email' => 'required|email',
                ]
            );
        } else {
            $validateData = $request->validate(
                [
                    'name' => 'required',
                    'telphone' => 'required',
                ]
            );
        }

        if ($is_taxpayer) {
            $request->validate([
                'reg_number'    => 'required',
                'tax_number'    => 'required',
                'business_line' => 'required',
                'business_type_id' => 'required'
            ]);
        }

        try {

            $customer_details = $request->only([
                'name',
                'business_name',
                'email',
                'telphone',
                'dni',
                'business_type_id',
                'customer_portfolio_id',
                'customer_group_id',
                'address',
                'country_id',
                'state_id',
                'city_id',
                'contact_mode_id',
                'first_purchase_location',
                'latitude',
                'length',
                'selling_price_group_id',
                'is_exempt',
                'is_foreign',
                'accounting_account_id',
                'from',
                'to',
                'cost',
            ]);

            if ($request->is_exempt) {
                $customer_details['tax_group_id'] = null;
            } else {
                $customer_details['tax_group_id'] = $request->input('tax_group_id') != 0 ? $request->input('tax_group_id') : null;
            }

            $business_id = request()->session()->get('user.business_id');

            $customer_details['business_id'] = $business_id;
            $customer_details['created_by'] = $request->session()->get('user.id');
            $customer_details['latitude'] = $request->latitude;
            $customer_details['length'] = $request->length;

            if ($request->input('state_id')) {
                $state = State::findOrFail($request->input('state_id'));
                $zone = Zone::where('id', $state->zone_id)->first();
                $customer_details['zone_id'] = $zone->id;
            }

            $is_gov_institution = $request->input('is_gov_institution');

            // Locate NIT in general information section
            $business = Business::find($business_id);
            $customer_settings = empty($business->customer_settings) ? null : json_decode($business->customer_settings, true);

            if ($customer_settings['nit_in_general_info']) {
                $customer_details['tax_number'] = $request->input('tax_number');
            }

            if ($is_taxpayer) {
                $customer_details['is_taxpayer'] = 1;
                $customer_details['reg_number'] = $request->input('reg_number');
                $customer_details['business_line'] = $request->input('business_line');

                if (! $customer_settings['nit_in_general_info']) {
                    $customer_details['tax_number'] = $request->input('tax_number');
                }

            } else if ($is_gov_institution) {
                $customer_details['is_taxpayer'] = 2;

                if (! $customer_settings['nit_in_general_info']) {
                    $customer_details['tax_number'] = $request->input('tax_number');
                }

            } else {
                $customer_details['is_taxpayer'] = 0;
            }

            if ($allowed_credit) {
                $customer_details['allowed_credit'] = 1;
                $customer_details['opening_balance'] = $request->input('opening_balance');
                $customer_details['credit_limit'] = $request->input('credit_limit');
                $customer_details['credit_balance'] = $request->input('opening_balance');
                $customer_details['payment_terms_id'] = $request->input('payment_terms_id');
            } else {
                $customer_details['allowed_credit'] = 0;
            }

            // return $customer_details;
            $customer = Customer::create($customer_details);
            $customer->code = 'C'. str_pad($customer->id, 4, 0, STR_PAD_LEFT);
            $customer->save();
            
            $customer_id = $customer->id;

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'create',
                $customer->name,
                $customer
            );

            //Se verifica que el campo nombre de contacto no sea vacio, si este no es vacio los demas no lo seran puesto que tienen required
            if (!empty($request->input("contactname"))) {
                //se recorre cada uno de los campos de contactos y se agregan a un array

                foreach ($request->input("contactname") as $contactname) {
                    $contactnames[] = $contactname;
                }

                foreach ($request->input("contactphone") as $contactmobil) {
                    $contactmobile[] = $contactmobil;
                }

                foreach ($request->input("contactlandline") as $contactlandline) {
                    $contactlandlines[] = $contactlandline;
                }

                foreach ($request->input("contactemail") as $contactemail) {
                    $contactmails[] = $contactemail;
                }
                foreach ($request->input("contactcargo") as $contactcargo) {
                    $contactcargos[] = $contactcargo;
                }

                if (!empty($contactnames)) {

                    for ($i = 0; $i < count($contactnames); $i++) {
                        //se crea un nuevo contacto acorde a la cantidad de datos mandados en el array $contactnames
                        CustomerContact::create([
                            'name'      => $contactnames[$i],
                            'phone'     => $contactmobile[$i],
                            'landline'  => $contactlandlines[$i],
                            'email'     => $contactmails[$i],
                            'cargo'     => $contactcargos[$i],
                            'customer_id' => $customer_id
                        ]);
                    }
                }
            }

            // Add opening balance
            if (!empty($request->input('opening_balance'))) {
                $this->transactionUtil->createOpeningBalanceTransaction($business_id, null, $customer->opening_balance, $customer->id);
            }

            // Save vehicles
            if (config('app.business') == 'workshop') {
                $vehicles = $request->input('vehicles');
                $customer_vehicles = [];
    
                if (! empty($vehicles)) {
                    foreach ($vehicles as $vehicle) {
                        $new_vehicle = [
                            'customer_id' => $customer->id,
                            'license_plate' => $vehicle['license_plate'],
                            'brand_id' => $vehicle['brand_id'],
                            'model' => $vehicle['model'],
                            'year' => $vehicle['year'],
                            'color' => $vehicle['color'],
                            'responsible' => $vehicle['responsible'],
                            'engine_number' => $vehicle['engine_number'],
                            'vin_chassis' => $vehicle['vin_chassis'],
                            'mi_km' => $vehicle['mi_km']
                        ];
        
                        $customer_vehicles[] = $new_vehicle;
                    }
    
                    $customer->vehicles()->createMany($customer_vehicles);
                }
            }

            $output = [
                'success' => true,
                'msg' => __("customer.added_success"),
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'is_default' => $customer->is_default,
                'allowed_credit' => $customer->allowed_credit,
                'is_withholding_agent' => $customer->is_withholding_agent
            ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $customer = DB::table('customers as customer')
            ->leftJoin('business_types as business_type', 'business_type.id', '=', 'customer.business_type_id')
            ->leftJoin('customer_portfolios as customer_portfolio', 'customer_portfolio.id', '=', 'customer.customer_portfolio_id')
            ->leftJoin('customer_groups as customer_group', 'customer_group.id', '=', 'customer.customer_group_id')
            ->leftJoin('countries as country', 'country.id', '=', 'customer.country_id')
            ->leftJoin('states as state', 'state.id', '=', 'customer.state_id')
            ->leftJoin('cities as city', 'city.id', '=', 'customer.city_id')
            ->leftJoin('zones as zone', 'zone.id', '=', 'customer.zone_id')
            ->leftJoin('payment_terms as payment_term', 'payment_term.id', '=', 'customer.payment_terms_id')
            ->leftJoin('crm_contact_modes as ccm', 'customer.contact_mode_id', 'ccm.id')
            ->leftJoin('business_locations as bl', 'customer.first_purchase_location', 'bl.id')
            ->select('customer.*', 'business_type.name as business_type_value', 'customer_portfolio.name as customer_portfolio_value', 'customer_group.name as customer_group_value', 'country.name as country_value', 'state.name as state_value', 'city.name as city_value', 'zone.name as zone_value', 'payment_term.name as payment_terms_value', 'ccm.name as contact_mode_value', 'bl.name as location_value')
            ->where('customer.id', $id)
            ->first();

        //return view('customer.show', compact('customer'));
        return response()->json($customer);
    }
    public function getContacts($id)
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $contacts = CustomerContact::where('customer_id', $id)->get();
        return response()->json($contacts);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }
        $customer = Customer::findOrFail($id);

        $business_id = request()->session()->get('user.business_id');

        $business_types = BusinessType::select('id', 'name')
            ->pluck('name', 'id');

        $customer_portfolios = CustomerPortfolio::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $customer_groups = CustomerGroup::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $countries = Country::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $states = State::select('id', 'name')
            ->where('business_id', $business_id)
            ->where('country_id', $customer->country_id)
            ->pluck('name', 'id');

        $cities = City::select('id', 'name')
            ->where('business_id', $business_id)
            ->where('state_id', $customer->state_id)
            ->pluck('name', 'id');


        $payment_terms = PaymentTerm::select('id', 'name')
            ->pluck('name', 'id');

        // Contact modes
        $contact_modes = CRMContactMode::where('business_id', $business_id)
            ->pluck('name', 'id');

        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');

        //contactos multiples que pertenecen a este cliente
        $customer_contacts = CustomerContact::where('customer_id', $id)->get();

        $main_customer_account = "";

        $business = Business::find($business_id);
        if ($business->accounting_customer_id) {
            $main_customer_account = Catalogue::where("status", 1)
            ->where("id", $business->accounting_customer_id)
                ->value("code");
        }

        $account_name = [];
        if ($customer->accounting_account_id) {
            $catalogue = Catalogue::where("status", 1)
            ->where("id", $customer->accounting_account_id)
                ->select(DB::raw("CONCAT(code, ' ', name) as name"))
                ->first();

            $account_name[] = [$customer->accounting_account_id => $catalogue->name];
        }

        $business_receivable_type = $business->receivable_type;

        // Locate NIT in general information section
        $customer_settings = empty($business->customer_settings) ? null : json_decode($business->customer_settings, true);
        $nit_in_general_info = empty($customer_settings) ? 0 : $customer_settings['nit_in_general_info'];

        $vehicles = CustomerVehicle::where('customer_id', $id)->get();

        $brands = Brands::pluck('name', 'id');

        // return $customer_contacts;
        return view('customer.edit', compact(
            'customer',
            'business_types',
            'customer_portfolios',
            'customer_groups',
            'countries',
            'states',
            'cities',
            'payment_terms',
            'contact_modes',
            'tax_groups',
            'customer_contacts',
            'main_customer_account',
            'account_name',
            'business_receivable_type',
            'nit_in_general_info',
            'vehicles',
            'brands'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        $is_taxpayer = $request->input('is_taxpayer');
        $allowed_credit = $request->input('allowed_credit');

        if (empty($request->input('email'))) {
            $validateData = $request->validate(
                [
                    'name' => 'required',
                    'telphone' => 'required',
                ]
            );
        } elseif (empty($request->input('telphone'))) {
            $validateData = $request->validate(
                [
                    'name' => 'required',
                    'email' => 'required|email',
                ]
            );
        } else {
            $validateData = $request->validate(
                [
                    'name' => 'required',
                    'telphone' => 'required',
                ]
            );
        }

        try {
            $customer = Customer::findOrFail($id);

            // Clone record before action
            $customer_old = clone $customer;

            $customer_details = $request->only([
                'name',
                'business_name',
                'email',
                'telphone',
                'dni',
                'business_type_id',
                'customer_portfolio_id',
                'group_id',
                'address',
                'country_id',
                'state_id',
                'city_id',
                'contact_mode_id',
                'first_purchase_location',
                'latitude',
                'length',
                'selling_price_group_id',
                'accounting_account_id',
                'from',
                'to',
                'cost',
            ]);

            $customer_details['is_exempt'] = !empty($request->input('is_exempt')) ? $request->input('is_exempt') : null;
            $customer_details['is_foreign'] = !empty($request->input('is_foreign')) ? $request->input('is_foreign') : null;

            $customer_details['tax_group_id'] = $request->input('tax_group_id') != 0 ? $request->input('tax_group_id') : null;
            $customer_details['latitude'] = $request->latitude;
            $customer_details['length'] = $request->length;
            if ($request->input('state_id')) {
                $state = State::findOrFail($request->input('state_id'));
                $zone = Zone::where('id', $state->zone_id)->first();
                $customer_details['zone_id'] = $zone->id;
            }

            $is_gov_institution = $request->input('is_gov_institution');

            // Locate NIT in general information section
            $business_id = request()->session()->get('user.business_id');
            $business = Business::find($business_id);
            $customer_settings = empty($business->customer_settings) ? null : json_decode($business->customer_settings, true);

            if ($customer_settings['nit_in_general_info']) {
                $customer_details['tax_number'] = $request->input('tax_number');
            }

            if ($is_taxpayer) {
                $customer_details['is_taxpayer'] = 1;
                $customer_details['reg_number'] = $request->input('reg_number');
                $customer_details['business_line'] = $request->input('business_line');

                if (! $customer_settings['nit_in_general_info']) {
                    $customer_details['tax_number'] = $request->input('tax_number');
                }

            } else if ($is_gov_institution) {
                $customer_details['is_taxpayer'] = 2;

                if (! $customer_settings['nit_in_general_info']) {
                    $customer_details['tax_number'] = $request->input('tax_number');
                }

            } else {
                $customer_details['is_taxpayer'] = 0;
            }

            if ($allowed_credit) {
                $customer_details['allowed_credit'] = 1;
                $customer_details['opening_balance'] = $request->input('opening_balance');
                $customer_details['credit_limit'] = $request->input('credit_limit');
                $customer_details['credit_balance'] = $request->input('opening_balance');
                $customer_details['payment_terms_id'] = $request->input('payment_terms_id');
            } else {
                $customer_details['allowed_credit'] = 0;
            }

            $customer->customer_group_id = $request->get('group_id');
            $customer->update($customer_details);
            //se crea un array el cual tendra todos los contactos del cliente
            $oldContact = CustomerContact::where('customer_id', $customer->id)->pluck('id');
            $newContact = [];
            if (!empty($request->input("contactname"))) {
                //se recorre cada uno de los campos de contactos y se agregan a un array
                foreach ($request->input('contactid') as $contactid) {
                    $contactids[] = $contactid;
                }

                foreach ($request->input("contactname") as $contactname) {
                    $contactnames[] = $contactname;
                }

                foreach ($request->input("contactphone") as $contactmobil) {
                    $contactmobile[] = $contactmobil;
                }

                foreach ($request->input("contactlandline") as $contactlandline) {
                    $contactlandlines[] = $contactlandline;
                }

                foreach ($request->input("contactemail") as $contactemail) {
                    $contactmails[] = $contactemail;
                }
                foreach ($request->input("contactcargo") as $contactcargo) {
                    $contactcargos[] = $contactcargo;
                }

                if (!empty($contactids)) {

                    for ($i = 0; $i < count($contactids); $i++) {
                        //se crea un nuevo contacto si este no existe o es diferentes a los antes registrados
                        if ($contactids[$i] == "0") {
                            CustomerContact::create([
                                'name'      => $contactnames[$i],
                                'phone'     => $contactmobile[$i],
                                'landline'  => $contactlandlines[$i],
                                'email'     => $contactmails[$i],
                                'cargo'     => $contactcargos[$i],
                                'customer_id' => $customer->id
                            ]);
                        } else {
                            //se actualizan los contactos que se les ha cambiado la informacion
                            CustomerContact::find($contactids[$i])
                                ->update([
                                    'name'      => $contactnames[$i],
                                    'phone'     => $contactmobile[$i],
                                    'landline'  => $contactlandlines[$i],
                                    'email'     => $contactmails[$i],
                                    'cargo'     => $contactcargos[$i],
                                ]);
                            $newContact[] = $contactids[$i];
                        }
                    }
                }
            }

            //Eliminar contactos
            foreach ($oldContact as $o) {
                $delete = true;

                foreach ($newContact as $n) {
                    if ($o == $n) {
                        $delete = false;
                    }
                }

                if ($delete) {
                    CustomerContact::findOrFail($o)->delete();
                }
            }

            // Get opening balance if exists
            $ob_transaction =  Transaction::where('customer_id', $id)
                ->where('type', 'opening_balance')
                ->first();

            if (! empty($ob_transaction)) {
                $amount = $this->transactionUtil->num_uf($request->input('opening_balance'));
                $ob_transaction->final_total = $amount;
                $ob_transaction->save();

                // Update opening balance payment status
                $this->transactionUtil->updatePaymentStatus($ob_transaction->id, $ob_transaction->final_total);

            } else {
                // Add opening balance
                if (! empty($request->input('opening_balance'))) {
                    $this->transactionUtil->createOpeningBalanceTransaction(
                        $request->session()->get('user.business_id'),
                        null,
                        $request->input('opening_balance'),
                        $id
                    );
                }
            }

            // Save vehicles
            if (config('app.business') == 'workshop') {
                $vehicles = $request->input('vehicles');
    
                if (! empty($vehicles)) {
                    $saved_ids = [];
    
                    foreach ($vehicles as $vehicle) {
                        if (isset($vehicle['id'])) {
                            $updated_vehicle = CustomerVehicle::find($vehicle['id']);
                            $updated_vehicle->license_plate = $vehicle['license_plate'];
                            $updated_vehicle->brand_id = $vehicle['brand_id'];
                            $updated_vehicle->model = $vehicle['model'];
                            $updated_vehicle->year = $vehicle['year'];
                            $updated_vehicle->color = $vehicle['color'];
                            $updated_vehicle->responsible = $vehicle['responsible'];
                            $updated_vehicle->engine_number = $vehicle['engine_number'];
                            $updated_vehicle->vin_chassis = $vehicle['vin_chassis'];
                            $updated_vehicle->mi_km = $vehicle['mi_km'];
                            $updated_vehicle->save();
    
                            $saved_ids[] = $vehicle['id'];
    
                        } else {
                            $new_vehicle = CustomerVehicle::create([
                                'customer_id' => $customer->id,
                                'license_plate' => $vehicle['license_plate'],
                                'brand_id' => $vehicle['brand_id'],
                                'model' => $vehicle['model'],
                                'year' => $vehicle['year'],
                                'color' => $vehicle['color'],
                                'responsible' => $vehicle['responsible'],
                                'engine_number' => $vehicle['engine_number'],
                                'vin_chassis' => $vehicle['vin_chassis'],
                                'mi_km' => $vehicle['mi_km']
                            ]);
    
                            $saved_ids[] = $new_vehicle->id;
                        }
                    }
    
                    DB::table('customer_vehicles')
                        ->where('customer_id', $customer->id)
                        ->whereNotIn('id', $saved_ids)
                        ->delete();
    
                } else {
                    DB::table('customer_vehicles')
                        ->where('customer_id', $customer->id)
                        ->delete();
                }
            }

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'update',
                $customer->name,
                $customer_old,
                $customer
            );

            $output = [
                'success' => true,
                'msg' => __("customer.updated_success")
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
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {

                $customer = Customer::findOrFail($id);

                $transactions = Transaction::where('customer_id', $id)->get();
                $allow_delete = count($transactions) > 0 ? false : true;

                if (config('app.business') == 'optics' && $allow_delete == true) {
                    $lab_orders = LabOrder::where('customer_id', $id)->get();
                    $allow_delete = count($lab_orders) > 0 ? false : true;
                }

                if ($allow_delete) {
                    // Clone record before action
                    $customer_old = clone $customer;
    
                    $customer->delete();
    
                    // Store binnacle
                    $this->transactionUtil->registerBinnacle(
                        $this->module_name,
                        'delete',
                        $customer_old->name,
                        $customer_old
                    );
    
                    $output = [
                        'success' => true,
                        'msg' => __('customer.deleted_success')
                    ];

                } else {
                    $output = [
                        'success' => false,
                        'msg' => __('customer.transactions_already_exist')
                    ];
                }

            } catch (\Exception $e) {
                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }
            return $output;
        }
    }

    /**
     * Add customer from POS modal
     *
     * @return \Illuminate\Http\Response
     */
    public function getAddCustomer($customer_name = null)
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_types = BusinessType::select('id', 'name')
            ->pluck('name', 'id');

        $customer_portfolios = CustomerPortfolio::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $customer_groups = CustomerGroup::all();

        $countries = Country::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $payment_terms = PaymentTerm::select('id', 'name')
            ->pluck('name', 'id');

        // Contact modes
        $contact_modes = CRMContactMode::where('business_id', $business_id)
            ->pluck('name', 'id');

        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');

        //To get receivable_type
        $main_customer_account = "";

        $business = Business::find($business_id);
        if ($business->accounting_customer_id) {
            $main_customer_account = Catalogue::where("status", 1)
            ->where("id", $business->accounting_customer_id)
                ->value("code");
        }        
        $business_receivable_type = $business->receivable_type;

        return view('customer.partials.add_customer_modal')
            ->with(compact(
                'customer_name',
                'business_types',
                'customer_portfolios',
                'customer_groups',
                'countries',
                'payment_terms',
                'contact_modes',
                'tax_groups',
                'business_receivable_type',
                'main_customer_account',
            ));
    }

    public function getCustomersData()
    {
        $portfolio_id = request()->input('portfolio_id', null);

        $business_id = auth()->user()->business_id;

        $customers = DB::table('customers')
            ->where('business_id', $business_id)
            ->select(
                'customers.*',
                DB::raw("(SELECT t1.final_total FROM transactions AS t1 WHERE t1.customer_id = customers.id AND t1.type = 'opening_balance' LIMIT 1) AS opening_balance_amount"),
                DB::raw("(SELECT SUM(tp.amount) FROM transaction_payments AS tp WHERE tp.transaction_id = (SELECT t2.id FROM transactions AS t2 WHERE t2.customer_id = customers.id AND t2.type = 'opening_balance' LIMIT 1)) AS total_paid"),
                DB::raw("(SELECT t3.id FROM transactions AS t3 WHERE t3.customer_id = customers.id AND t3.type = 'opening_balance' AND t3.payment_status IN ('due', 'partial') LIMIT 1) AS opening_balance_id")
            );

        /** customer portfolio filter */
        if(!empty($portfolio_id)){
            $customers->where('customer_portfolio_id', $portfolio_id);
        }

        return DataTables::of($customers)
            ->addColumn(
                'actions',
                function ($row) {
                    $html = '<div class="btn-group">
                <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                    if (auth()->user()->can('customer.view')) {
                        //$html .= '<li><a href="#" data-href="' . action('CustomerController@show', [$row->id]) . '" class="view_customer_button"><i class="glyphicon glyphicon-eye-open"></i> '.__("messages.view").'</a></li>'
                        $html .= '<li><a href="#" onClick="viewCustomer(' . $row->id . ')"><i class="glyphicon glyphicon-eye-open"></i> ' . __("messages.view") . '</a></li>';
                    }

                    if (auth()->user()->can('customer.update')) {
                        $html .= '<li><a href="#" data-href="' . action('CustomerController@edit', [$row->id]) . '" class="edit_customer_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }

                    $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->opening_balance_id]) . '" class="view_payment_modal"><i class="fa fa-money"></i> ' . __("purchase.view_payments") . '</a></li>';

                    if (auth()->user()->can('purchase.payments') || auth()->user()->can('sell.payments')) {
                        if (($row->opening_balance - $row->total_paid) > 0) {
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->opening_balance_id]) . '" class="add_payment_modal"><i class="fa fa-money"></i> ' . __("customer.pay_opening_balance") . '</a></li>';
                        }
                    }

                    if (auth()->user()->can('customer.create')) {
                        $html .= '<li><a href="#" onClick="createFollowCustomer(' . $row->id . ',\'' . $row->name . '\')"><i class="glyphicon glyphicon-comment"></i> ' . __("crm.tracing") . '</a></li>';
                    }

                    if (auth()->user()->can('customer.delete')) {
                        $html .= '<li><a href="#" onClick="deleteCustomer(' . $row->id . ')"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '</ul></div>';
                    return $html;
                }
            )

            ->rawColumns(['actions'])
            ->toJson();
    }

    /**
     * Shows import option for customers
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getImportCustomers()
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => __('messages.install_enable_zip')
            ];

            return view('customer.import')
                ->with('notification', $output);
        } else {
            return view('customer.import');
        }
    }

    /**
     * Imports customers
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postImportCustomers(Request $request)
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('customers_xlsx')) {
                $file = $request->file('customers_xlsx');

                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[1];

                //removing the header
                unset($imported_data[0]);
                unset($imported_data[1]);
                unset($imported_data[2]);
                unset($imported_data[3]);

                $business_id = auth()->user()->business_id;
                $user_id = auth()->user()->id;

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                DB::beginTransaction();
                                
                foreach ($imported_data as $key => $value) {
                    //Check if 26 no. of columns exists                    
                    if (count($value) != 26) {
                        $is_valid =  false;
                        $error_msg = __('customer.number_of_columns_mismatch') . ' ' . $key;
                        break;
                    }

                    $row_no = $key + 1;
                    $customer_array = [];

                    // Check name
                    if (!empty(trim($value[0]))) {
                        $customer_array['name'] = $value[0];
                    } else {
                        $is_valid =  false;
                        $error_msg = __('customer.customer_name_is_required', ['number' => $row_no]);
                        break;
                    }

                    //Check is foreign
                    $is_foreign = trim($value[1]);
                    if ($is_foreign == "0" || $is_foreign == "1") {
                        $customer_array['is_foreign'] = $value[1];
                    } else {
                        $is_valid =  false;
                        $error_msg = __('customer.customer_is_foreign_required', ['number' => $row_no]);
                        break;
                    }

                    //Check DNI (DUI)
                    if (!empty(trim($value[2]))) {
                        $verif_exist_dni = Customer::where('business_id', $business_id)
                            ->where('dni', trim($value[2]))->exists();
                        if ($verif_exist_dni) {
                            $is_valid =  false;
                            $error_msg = __('customer.exists_dni', ['number' => $row_no]);
                            break;
                        } else {
                            $customer_array['dni'] = trim($value[2]);
                        }
                    }

                    //Check email
                    if (!empty(trim($value[3]))) {
                        if (filter_var(trim($value[3]), FILTER_VALIDATE_EMAIL)) {
                            $customer_array['email'] = trim($value[3]);
                        } else {
                            $is_valid =  false;
                            $error_msg = __('customer.email_is_invalid', ['number' => $row_no]);
                            break;
                        }
                    }

                    // Telephone
                    $customer_array['telphone'] = trim($value[4]);
                    // Address
                    $customer_array['address'] = trim($value[5]);

                    // Check country
                    $country = null;
                    if (!empty(trim($value[6]))) {
                        $country = Country::whereRaw('upper(name) = upper("' . trim($value[6]) . '")')->first();
                        if (!empty($country)) {
                            $customer_array['country_id'] = $country->id;
                        } else {
                            $is_valid =  false;
                            $error_msg = __('customer.customer_country_not_found', ['number' => $row_no]);
                            break;
                        }
                    } else {
                        $customer_array['country_id'] = $country;
                    }

                    // Check state
                    $state = null;
                    if (!empty(trim($value[7])) && !is_null($country)) {
                        $state = State::whereRaw('upper(name) = upper("' . trim($value[7]) . '")')
                            ->where('country_id', $country->id)->first();
                        if (!empty($state)) {
                            $customer_array['state_id'] = $state->id;
                            $zone = Zone::where('id', $state->zone_id)->first();
                            $customer_array['zone_id'] = $zone->id;
                        } else {
                            $is_valid =  false;
                            $error_msg = __('customer.customer_state_not_found', ['number' => $row_no]);
                            break;
                        }
                    } else {
                        $customer_array['state_id'] = $state;
                    }

                    // Check city
                    $city = null;
                    if (!empty(trim($value[8])) && !is_null($state)) {
                        $city = City::whereRaw('upper(name) = upper("' . trim($value[8]) . '")')
                            ->where('state_id', $state->id)->first();
                        if (!empty($city)) {
                            $customer_array['city_id'] = $city->id;
                        } else {
                            $is_valid =  false;
                            $error_msg = __('customer.customer_city_not_found', ['number' => $row_no]);
                            break;
                        }
                    } else {
                        $customer_array['city_id'] = $city;
                    }

                    //check latitude
                    $customer_array['latitude'] = trim($value[9]);
                    //check length
                    $customer_array['length'] = trim($value[10]);

                    //Check (is_exempt)
                    $is_exempt = trim($value[11]);
                    if ($is_exempt == "0" || $is_exempt == "1") {
                        $customer_array['is_exempt'] = $is_exempt;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Exempt is required in row no. $row_no";
                        break;
                    }

                    // Check is taxpayer
                    if (!empty($value[12]) || $value[12] == 0) {
                        if ($value[12] == 0 || $value[12] == 1) {
                            $customer_array['is_taxpayer'] = $value[12];
                            if ($value[12] == 1) {
                                // Business name
                                if (!empty($value[13])) {
                                    $customer_array['business_name'] = trim($value[13]);
                                } else {
                                    $is_valid =  false;
                                    $error_msg = __('customer.business_name_required', ['number' => $row_no]);
                                    break;
                                }

                                //Tax number (NIT)
                                if (!empty($value[14])) {
                                    $verif_exist_tax_n = Customer::where('business_id', $business_id)
                                        ->where('tax_number', trim($value[14]))->exists();
                                    if ($verif_exist_tax_n) {
                                        $is_valid =  false;
                                        $error_msg = __('customer.exists_tax_num', ['number' => $row_no]);
                                        break;
                                    } else {
                                        $customer_array['tax_number'] = trim($value[14]);
                                    }
                                } else {
                                    $is_valid =  false;
                                    $error_msg = __('customer.tax_number_required', ['number' => $row_no]);
                                    break;
                                }

                                //reg number
                                if (!empty($value[15])) {
                                    $verif_exist_reg = Customer::where('business_id', $business_id)
                                        ->where('reg_number', trim($value[15]))->exists();
                                    if ($verif_exist_reg) {
                                        $is_valid =  false;
                                        $error_msg = __('customer.exists_reg', ['number' => $row_no]);
                                        break;
                                    } else {
                                        $customer_array['reg_number'] = trim($value[15]);
                                    }
                                } else {
                                    $is_valid =  false;
                                    $error_msg = __('customer.reg_number_required', ['number' => $row_no]);
                                    break;
                                }

                                //business line
                                if (!empty($value[16])) {
                                    $customer_array['business_line'] = trim($value[16]);
                                } else {
                                    $is_valid =  false;
                                    $error_msg = __('customer.business_line_required', ['number' => $row_no]);
                                    break;
                                }

                                // Check business type
                                if (!empty(trim($value[17]))) {
                                    $business_type = BusinessType::whereRaw('upper(name) = upper("' . trim($value[17]) . '")')->first();
                                    if (!empty($business_type)) {
                                        if ($business_type->name == 'Gran Empresa') {
                                            $tax_group = TaxGroup::whereRaw("upper(description) = 'RETENCIÓN'")
                                                ->where('type', 'contacts')->first();

                                            if (!empty($tax_group)) {
                                                $customer_array['tax_group_id'] = $tax_group->id;
                                            } else {
                                                $is_valid =  false;
                                                $error_msg = __('lang_v1.required_tax_group', ['number' => $row_no]);
                                                break;
                                            }
                                        }
                                        $customer_array['business_type_id'] = $business_type->id;
                                    } else {
                                        $is_valid =  false;
                                        $error_msg = __('customer.business_type_required', ['number' => $row_no]);
                                        break;
                                    }
                                } else {
                                    $is_valid =  false;
                                    $error_msg = __('customer.business_type_required', ['number' => $row_no]);
                                    break;
                                }
                            }
                        } else {
                            $is_valid =  false;
                            $error_msg = __('customer.option_is_taxpayer_invalid', ['number' => $row_no]);
                            break;
                        }
                    } else {
                        $is_valid =  false;
                        $error_msg = __('customer.taxpayer_required', ['number' => $row_no]);
                        break;
                    }

                    // accounting account
                    $accounting_account = trim($value[18]);
                    if ($accounting_account) {
                        $account = intval($accounting_account);

                        if ($account) {
                            $catalogue = Catalogue::where('status', 1)->where('code', $account)->first();

                            if ($catalogue) {
                                $customer_array['accounting_account_id'] = $catalogue->id;
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

                    // Check DNI (DUI)                    
                    if ($customer_array['is_taxpayer'] == 0) {
                        if (empty($customer_array['dni'])) {
                            $is_valid =  false;
                            $error_msg = __('customer.dui_required', ['number' => $row_no]);
                            break;
                        }
                    }

                    // Check allowed credit
                    if (!empty(trim($value[19]))) {
                        if ($value[19] == 0 || $value[19] == 1) {
                            $customer_array['allowed_credit'] = trim($value[19]);

                            if ($customer_array['allowed_credit'] == 1) {
                                // Check opening balance
                                if (!empty(trim($value[20])) && $value[20] >= 0) {
                                    $customer_array['opening_balance'] = trim($value[20]);
                                }

                                // Check credit limit
                                if (!empty(trim($value[21])) && $value[21] >= 0) {
                                    $customer_array['credit_limit'] = trim($value[21]);
                                } else {
                                    $is_valid =  false;
                                    $error_msg = __('customer.credit_limit_required', ['number' => $row_no]);
                                    break;
                                }

                                // Check payment terms
                                $days = trim($value[22]);
                                if (!empty($days)) {
                                    $payment_term = PaymentTerm::where('days', $days)->first();
                                    if (!empty($payment_term)) {
                                        $customer_array['payment_terms_id'] = $payment_term->id;
                                    } else {
                                        $is_valid =  false;
                                        $error_msg = __('customer.payment_term_invalid', ['number' => $row_no]);
                                        break;
                                    }
                                } else {
                                    $is_valid =  false;
                                    $error_msg = __('customer.payment_term_required', ['number' => $row_no]);
                                    break;
                                }
                            }
                        } else {
                            $is_valid =  false;
                            $error_msg = __('customer.option_allowed_credit_invalid', ['number' => $row_no]);
                            break;
                        }
                    } else {
                        $customer_array['opening_balance'] = 0;
                    }

                    // check social contact
                    if (!empty(trim($value[23]))) {
                        $contact_modes = CRMContactMode::where('business_id', $business_id)
                            ->whereRaw('upper(name) = upper("' . trim($value[23]) . '")')->first();

                        if (!empty($contact_modes)) {
                            $customer_array['contact_mode_id'] = $contact_modes->id;
                        }
                    }

                    // Check customer group
                    if (!empty(trim($value[24]))) {
                        $customer_group = CustomerGroup::whereRaw('upper(name) = upper("' . trim($value[24]) . '")')->first();
                        if (!empty($customer_group)) {
                            $customer_array['customer_group_id'] = $customer_group->id;
                        }
                    }

                    // Check customer portfolio
                    if (!empty(trim($value[25]))) {
                        $customer_portfolio = CustomerPortfolio::whereRaw('upper(name) = upper("' . trim($value[25]) . '")')->first();
                        if (!empty($customer_portfolio)) {
                            $customer_array['customer_portfolio_id'] = $customer_portfolio->id;
                        }
                    }

                    $formated_data[] = $customer_array;
                }

                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }

                if (!empty($formated_data)) {
                    foreach ($formated_data as $customer_data) {
                        $customer_data['business_id'] = $business_id;
                        $customer_data['created_by'] = $user_id;
                        //$customer_data['type'] = 'customer';
                        $customer = Customer::create($customer_data);

                        // Store binnacle
                        $this->transactionUtil->registerBinnacle(
                            $this->module_name,
                            'create',
                            $customer->name,
                            $customer
                        );
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
            return redirect()->route('customers.import')->with('notification', $output);
        }

        return redirect()->action('CustomerController@index')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getFollowCustomer($id)
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $oportunity = DB::table('oportunities as oportunity')
            ->leftJoin('crm_contact_reasons as reason', 'reason.id', '=', 'oportunity.contact_reason_id')
            ->leftJoin('crm_contact_modes as mode', 'mode.id', '=', 'oportunity.contact_mode_id')
            ->leftJoin('crm_contact_modes as known', 'known.id', '=', 'oportunity.known_by')
            ->leftJoin('contacts as customer', 'customer.id', '=', 'oportunity.refered_id')
            ->leftJoin('countries as country', 'country.id', '=', 'oportunity.country_id')
            ->leftJoin('states as state', 'state.id', '=', 'oportunity.state_id')
            ->leftJoin('cities as city', 'city.id', '=', 'oportunity.city_id')
            ->leftJoin('categories as category', 'category.id', '=', 'oportunity.product_cat_id')
            ->select('oportunity.*', 'reason.name as reason', 'mode.name as mode', 'country.name as country', 'state.name as state', 'city.name as city', 'known.name as knowned', 'customer.name as customer', 'category.name as category')
            ->where('oportunity.id', $id)
            ->first();

        return response()->json($oportunity);
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
            $user_id = request()->session()->get('user.id');

            $customers = Customer::leftJoin('tax_rate_tax_group AS trtg', 'customers.tax_group_id', 'trtg.tax_group_id')
                ->leftJoin('tax_rates as tr', 'trtg.tax_rate_id', 'tr.id')
                ->leftJoin('states as s', 'customers.state_id', 's.id')
                ->leftJoin('cities as c', 'customers.city_id', 'c.id')
                ->leftJoin('payment_terms as pt', 'customers.payment_terms_id', 'pt.id')
                ->leftJoin('customer_contacts as cc', 'customers.id', 'cc.customer_id')
                ->leftJoin('customer_portfolios as cp', 'customers.customer_portfolio_id', 'cp.id')
                ->where('customers.business_id', $business_id);

            if (!empty($term)) {
                $customers->where(function ($query) use ($term) {
                    $query->where('customers.name', 'like', '%' . $term . '%')
                        ->orWhere('customers.business_name', 'like', '%' . $term . '%')
                        ->orWhere('customers.dni', 'like', '%' . $term . '%')
                        ->orWhere('customers.tax_number', 'like', '%' . $term . '%')
                        ->orWhere('customers.reg_number', 'like', '%' . $term . '%');
                });
            }

            $customers = $customers->select(
                'customers.id',
                DB::raw('CONCAT(customers.name, " ", IFNULL(customers.reg_number, "")) as text'),
                'cc.id as contact',
                'customers.email',
                'customers.telphone',
                'customers.address',
                's.id as state',
                'c.id as city',
                'customers.is_default',
                'customers.is_taxpayer',
                'customers.allowed_credit',
                'pt.name as payment_term',
                'cp.seller_id',
                'customers.is_withholding_agent',
                'customers.is_exempt',
                'customers.tax_group_id',
                'tr.percent as tax_percent',
                'tr.min_amount',
                'tr.max_amount'
            )->groupBy('customers.id')
            ->get();

            foreach ($customers as $c) {
                if ($c->contact > 0) {
                    $c->contact = CustomerContact::where('customer_id', $c->id)->first()->name;
                }
            }

            return json_encode($customers);
        }
    }

    //funciones para saldos por clienre

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function indexBalancesCustomer()
    {
        if (! auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
    
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $months = array(
            '01' => __('accounting.january'),
            '02' => __('accounting.february'),
            '03' => __('accounting.march'),
            '04' => __('accounting.april'),
            '05' => __('accounting.may'),
            '06' => __('accounting.june'),
            '07' => __('accounting.july'),
            '08' => __('accounting.august'),
            '09' => __('accounting.september'),
            '10' => __('accounting.october'),
            '11' => __('accounting.november'),
            '12' => __('accounting.december')
        );

        $business = Business::find($business_id);

        $sellers = CustomerPortfolio::pluck('name', 'id');

        return view('balances_customer.index', compact('date_filters', 'months', 'business', 'sellers'));
    }

    public function getBalancesCustomersData()
    {
        $business_id = auth()->user()->business_id;
        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');
        $seller = request()->input('seller') ?? 0;

        $customers = DB::select('CALL customer_balance(?, ?, ?, ?)', [$business_id, $start_date, $end_date, $seller]);

        return DataTables::of($customers)
            ->addColumn(
                'total_remaining', function ($row) {
                    $total_remaining =  round(($row->final_total - $row->total_paid), 6);

                    return '<span class="display_currency remaining_credit" data-currency_symbol="true" data-orig-value="' . $total_remaining . '">' . $total_remaining . '</span>';
                }
            )
            ->editColumn(
                'final_total', function ($row) {
                    $final_total = 0;

                    if ($row->final_total) {
                        $final_total = $row->final_total;
                    }

                    return '<span class="display_currency balance_to_date" data-currency_symbol="true" data-orig-value="' . $final_total . '">' . $final_total . '</span>';
                }
            )
            ->editColumn(
                'total_paid', function ($row) {
                    $total_paid = 0;

                    if ($row->total_paid) {
                        $total_paid = $row->total_paid;
                    }

                    return '<span class="display_currency payments" data-currency_symbol="true" data-orig-value="' . $total_paid . '">' . $total_paid . '</span>';
                }
            )
            ->editColumn(
                'credit_limit', function ($row) {
                    $credit_limit = "";

                    if ($row->credit_limit != 0) {
                        $credit_limit = '<span style="color: #029600">$ ' . number_format($row->credit_limit, 2) . '</span>';
                    } else {
                        $credit_limit = '<span style="color: #029600">$ ' . number_format(0, 2) . '</span>';
                    }

                    return $credit_limit;
                }
            )
            ->addColumn(
                'limit_balance', function ($row) {
                    $total_remaining =  $row->final_total - $row->total_paid;
                    $limit_balance = (float) ($row->credit_limit - $total_remaining);
                    $total = number_format($limit_balance, 2);
                    $limit_balance_html = "";
                    
                    if ($limit_balance < 0) {
                        $limit_balance_html = '
                        <span class="display_currency"data-currency_symbol="true" style="color:red">
                            <strong>$' . $total . '</strong>
                        </span>
                        <i class="fa fa-exclamation-triangle" aria-hidden="true" style="color:red">
                        </i>
                        ';
                    } else {
                        $limit_balance_html = '<span>$' . $total . '</span>';
                    }

                    return $limit_balance_html;
                }
            )
            ->setRowAttr([
                'data-href' => function ($row) {
                    if (auth()->user()->can("customer.view")) {
                        return  action('CustomerController@showBalances', [$row->id]);
                    } else {
                        return '';
                    }
                },
                'data-customer' => function($row) {
                    return $row->id;
                }
            ])
            ->rawColumns([
                'total_remaining',
                'total_paid',
                'final_total',
                'limit_balance',
                'credit_limit'
            ])
            ->toJson();
    }

    /**
     * Get details of the customer's account statement.
     * 
     * @param  int  $id
     * @return JSON
     */
    public function showBalances($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $customer = Transaction::leftJoin('customers', 'customers.id', 'transactions.customer_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.customer_id', $id)
            ->whereIn('transactions.type', ['sell', 'opening_balance'])
            //->where('transactions.type', 'sell')
            ->whereIn('transactions.status', ['final', 'annulled'])
            ->whereIn('transactions.payment_status', ['due', 'partial'])
            ->select(
                DB::raw('IFNULL(customers.business_name, customers.name) as name'),
                DB::raw("SUM(transactions.final_total) as final_total"),
                DB::raw("SUM(transactions.payment_balance) as total_paid"),
                'customers.credit_limit',
                'customers.email'
            )
            ->groupBy('transactions.customer_id')
            ->first();

        return json_encode($customer);
    }

    /**
     * Get accounts receivable
     * 
     */
    public function accountsReceivable() {
        if (!auth()->user()->can('cxc.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->user()->business_id;

        if (request()->ajax()) {
            $customer_id = request()->input('customer_id') ? request()->input('customer_id') : 0;
            $location_id = request()->input('location_id') ? request()->input('location_id') : 0;
            $seller_id = request()->input('seller_id') ? request()->input('seller_id') : 0;
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');

            $transactions = collect(DB::select('CALL get_accounts_receivable(?, ?, ?, ?, ?, ?)',
                [$business_id, $customer_id, $seller_id, $location_id, $start_date, $end_date]));

            return DataTables::of($transactions)
                ->editColumn('transaction_date', '{{ @format_date($transaction_date) }}')
                ->editColumn('expire_date', '{{ empty($expire_date) ? "" : @format_date($expire_date) }}')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{ $final_total }}">{{ $final_total }}</span>'
                )->editColumn(
                    'payments',
                    '<span class="display_currency payments" data-currency_symbol="true" data-orig-value="{{ $payments }}">{{ $payments }}</span>'
                )->addColumn('receivable_amount', function($row){
                    $receivable_amount = round($row->final_total, 2) - round($row->payments, 2);
                    return '<span class="display_currency receivable_amount" data-currency_symbol="true" data-orig-value="'. $receivable_amount .'">'. $receivable_amount .'</span>';
                })
                ->removeColumn('days_30')
                ->removeColumn('days_60')
                ->removeColumn('days_90')
                ->removeColumn('days_120')
                ->removeColumn('more_than_120')
                ->removeColumn('customer_id')
                ->rawColumns(['transaction_date', 'expire_date', 'final_total', 'payments', 'receivable_amount'])
                ->toJson();
        }

        # Locations and sellers
		$locations = BusinessLocation::forDropdown($business_id, true);
        $sellers = CustomerPortfolio::pluck('name', 'id');

        return view('customer.partials.accounts_receivable')
            ->with(compact('locations', 'sellers'));
    }

    /**
     *  Generate accounts receivable reporte
     * 
     * @return PDF | Excel
     */
    public function accountsReceivableReport() {
        if (!auth()->user()->can('cxc.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->user()->business_id;
        $customer_id = request()->input('customer_id') ? request()->input('customer_id') : 0;
        $location_id = request()->input('location_id') ? request()->input('location_id') : 0;
        $seller_id = request()->input('seller_id') ? request()->input('seller_id') : 0;
        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');
        $report_type = request()->input('report_type');

        $transactions = collect(DB::select('CALL get_accounts_receivable(?, ?, ?, ?, ?, ?)',
            [$business_id, $customer_id, $seller_id, $location_id, $start_date, $end_date]));

        $business_name = Business::find($business_id)->business_full_name;
        $report_name = __('cxc.cxc') ." ".  __("accounting.from_date") ." ". $this->transactionUtil->format_date($start_date) ." ". __("accounting.to_date") ." ". $this->transactionUtil->format_date($end_date);

        $final_totals = [
            'days_30' => $transactions->sum('days_30'),
            'days_60' => $transactions->sum('days_60'),
            'days_90' => $transactions->sum('days_90'),
            'days_120' => $transactions->sum('days_120'),
            'more_than_120_days' => $transactions->sum('more_than_120')
        ];
        $final_totals['totals'] = $final_totals['days_30'] + $final_totals['days_60'] + $final_totals['days_90'] + $final_totals['days_120'] + $final_totals['more_than_120_days'];

        if($report_type == 'pdf'){
            $accounts_receivable_report = \PDF::loadView('customer.partials.accounts_receivable_report_pdf',
                compact('transactions', 'business_name', 'report_name', 'final_totals'));
            $accounts_receivable_report->setPaper("A3", "landscape");

		    return $accounts_receivable_report->stream(__('cxc.cxc') .'.pdf');

        } else if($report_type == 'excel'){
            return Excel::download(new AccountsReceivableReportExport($transactions, $business_name, $report_name, $final_totals, $this->transactionUtil), __('cxc.cxc'). '.xlsx');
        }
    }

    public function getClients()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }
            $business_id = request()->session()->get('user.business_id');

            $query = Customer::where('business_id', $business_id);

            $customers = $query->where(function ($query) use ($term) {
                $query->where('customers.name', 'like', '%' . $term . '%')
                    ->orWhere('customers.business_name', 'like', '%' . $term . '%');
                })
                ->select('customers.id', DB::raw('IFNULL(customers.business_name, customers.name) as text'), 'customers.business_name as business_name')
                ->get();
            return json_encode($customers);
        }
    }

    /**
     * Create opening balance transactions from customer opening balances.
     * 
     * @param  int  $business_id
     * @return string
     */
    public function createAllOpeningBalances($business_id)
    {
        try {
            $customers = Customer::where('opening_balance', '>', 0)->get();

            \Log::info("--- START ---");

            foreach ($customers as $customer) {
                \Log::info("CUSTOMER " . $customer->id);

                $this->transactionUtil->createOpeningBalanceTransaction($business_id, null, $customer->opening_balance, $customer->id);
            }

            \Log::info("--- END ---");

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
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
            $business = Business::where('id', $business_id)->select('annull_sale_expiry')->first();

            return view('balances_customer.partials.toggle_dropdown')
                ->with(compact('id', 'is_direct_sale', 'payment_status', 'status', 'transaction_date', 'business'))
                ->render();
            
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = __('messages.something_went_wrong');
        }

        return $output;
    }

    /**
     * Retrieves vehicle row.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVehicleRow()
    {
        if (request()->ajax()) {
            $row_count = request()->input('row_count');
            $brands = Brands::pluck('name', 'id');
            return view('customer.partials.vehicle_row', compact('row_count', 'brands'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCustomerAndPatient($customer_name = null)
    {
        if (! auth()->user()->can('customer.create') && ! auth()->user()->can('patients.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_types = BusinessType::select('id', 'name')
            ->pluck('name', 'id');

        $customer_portfolios = CustomerPortfolio::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $customer_groups = CustomerGroup::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $countries = Country::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $payment_terms = PaymentTerm::select('id', 'name')
            ->pluck('name', 'id');
        
        $code = $this->util->generatePatientsCode();
        
        $sexs = $this->util->Sexs();
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        return view('customer.create-customer-patient')
            ->with(compact(
                'customer_name',
                'business_types',
                'customer_portfolios',
                'customer_groups',
                'countries',
                'payment_terms',
                'code',
                'sexs',
                'business_locations'
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCustomerAndPatient(Request $request)
    {
        if (! auth()->user()->can('customer.create') && ! auth()->user()->can('patients.create')) {
            abort(403, 'Unauthorized action.');
        }

        $is_taxpayer = $request->input('is_taxpayer');
        $allowed_credit = $request->input('allowed_credit');
        $is_patient = !empty($request->input('is_patient')) ? true : false;

        try {
            DB::beginTransaction();

            $customer_details = $request->only([
                'name',
                'business_name',
                'email',
                'telphone',
                'dni',                
                'business_type_id',
                'customer_portfolio_id',
                'customer_group_id',
                'address',
                'country_id',
                'state_id',
                'city_id'
            ]);

            $business_id = request()->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            $customer_details['business_id'] = $business_id;
            $customer_details['created_by'] = $user_id;

            if ($request->input('state_id')) {
                $state = State::findOrFail($request->input('state_id'));
                $zone = Zone::where('id', $state->zone_id)->first();
                $customer_details['zone_id'] = $zone->id;
            }

            if ($is_taxpayer) {
                $customer_details['is_taxpayer'] = 1;
                $customer_details['reg_number'] = $request->input('reg_number');
                $customer_details['tax_number'] = $request->input('tax_number');
                $customer_details['business_line'] = $request->input('business_line');

            } else {
                $customer_details['is_taxpayer'] = 0;
            }
            
            if ($allowed_credit) {
                $customer_details['allowed_credit'] = 1;
                $customer_details['opening_balance'] = $request->input('opening_balance') ?? 0;
                $customer_details['credit_limit'] = $request->input('credit_limit') ?? 0;
                $customer_details['credit_balance'] = $request->input('opening_balance') ?? 0;
                $customer_details['payment_terms_id'] = $request->input('payment_terms_id');

            } else {
                $customer_details['allowed_credit'] = 0;
            }

            $customer = Customer::create($customer_details);

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'create',
                $customer->name,
                $customer
            );

            $full_name = '';
            $pat_id = 0;

            if ($is_patient) {
                $patient_details = $request->only([
                    'code',
                    'age',
                    'sex',
                    'email',
                    'address',
                    'glasses_graduation',
                    'location_id'
                ]);

                $patient_details['full_name'] = $request->input('name');
                $patient_details['contacts'] = $request->input('telphone');
                $patient_details['business_id'] = $business_id;
                $patient_details['register_by'] = $user_id;
                $patient_details['glasses'] = !empty($request->input('chkhas_glasses')) ? 1 : 0;
                $patient_details['notes'] = $request->input('txt-notes');

                $employee_code = $request->input('employee_code');
                if (!empty($employee_code)) {
                    $employee = Employees::where('agent_code', $request->input('employee_code'))->first();
                    $patient_details['employee_id'] = !empty($employee) ? $employee->id : null;
                }

                $patient = Patient::create($patient_details);

                // Store binnacle
                $this->transactionUtil->registerBinnacle(
                    'patient',
                    'create',
                    $patient->code,
                    $patient
                );

                $full_name = $patient->full_name;
                $pat_id = $patient->id;
            }

            DB::commit();

            $outpout = [
                'success' => true,
                'msg' => __("customer.added_success"),
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'allowed_credit' => $customer->allowed_credit,
                'is_withholding_agent' => $customer->is_withholding_agent,
                'full_name' => $full_name,
                'pat_id' => $pat_id,
                'is_patient' => $is_patient,
                'is_default' => $customer->is_default
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $outpout = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')];
        }

        return $outpout;
    }

    /**
     * Get customer vehicles list.
     * 
     * @param  int  $id
     * @param  json
     */
    public function getCustomerVehicles($id)
    {
        $customer_vehicles = CustomerVehicle::where('customer_id', $id)
            ->leftJoin('brands', 'brands.id', 'customer_vehicles.brand_id')
            ->select(
                DB::raw("CONCAT(COALESCE(customer_vehicles.license_plate, ''), ' - ', COALESCE(brands.name, ''), ' ', COALESCE(customer_vehicles.model, ''), ' ', COALESCE(customer_vehicles.year, ''), ' ', COALESCE(customer_vehicles.color, '')) as name"),
                'customer_vehicles.id'
            )
            ->get();

        return response()->json($customer_vehicles);
    }

    /**
     * Generates glasses consumption report in PDF or Excel.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        if (! auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            // Records
            $records = Customer::leftJoin('business_types as bt', 'bt.id', 'customers.business_type_id')
                ->leftJoin('countries as co', 'co.id', 'customers.country_id')
                ->leftJoin('states as s', 's.id', 'customers.state_id')
                ->leftJoin('cities as ci', 'ci.id', 'customers.city_id')
                ->leftJoin('payment_terms as pt', 'pt.id', 'customers.payment_terms_id')
                ->leftJoin('crm_contact_modes as cm', 'cm.id', 'customers.contact_mode_id')
                ->leftJoin('customer_portfolios as cp', 'cp.id', 'customers.customer_portfolio_id')
                ->leftJoin('customer_groups as cg', 'cg.id', 'customers.customer_group_id')
                ->where('customers.business_id', $business_id)
                ->select(
                    'customers.name',
                    'customers.business_name',
                    'customers.business_line',
                    'bt.name as business_type',
                    'customers.dni',
                    'customers.tax_number',
                    'customers.reg_number',
                    'customers.telphone',
                    'customers.email',
                    'co.name as country',
                    's.name as state',
                    'ci.name as city',
                    'customers.address',
                    'customers.latitude',
                    'customers.length',
                    'customers.is_exempt',
                    'customers.allowed_credit',
                    'customers.opening_balance',
                    'customers.credit_limit',
                    'pt.name as payment_term',
                    'cm.name as contact_mode',
                    'cp.name as customer_portfolio',
                    'cg.name as customer_groups'
                )
                ->get();

            // Report type
            $report_type = $request->input('format');

            // Additional data
            $business = Business::find($business_id);

            // Title
            $title = __('report.customer_list');

            // Table headers
            $headers = [
                __('customer.name'),
                __('customer.business_name'),
                __('customer.business_line'),
                __('lang_v1.size'),
                __('customer.dui'),
                __('customer.tax_number'),
                __('customer.reg_number'),
                __('customer.phone'),
                __('customer.email'),
                __('customer.country'),
                __('customer.state'),
                __('customer.city'),
                __('customer.address'),
                __('customer.latitude'),
                __('customer.length'),
                __('customer.is_exempt'),
                __('customer.credit_enabled'),
                __('customer.opening_balance'),
                __('customer.credit_limit'),
                __('customer.payment_terms'),
                __('customer.contact_mode'),
                __('customer.customer_portfolio'),
                __('customer.customer_group')
            ];

            // Data
            $data = [];

            $header_data = [];

            if ($report_type == 'pdf') {
                $header_data['business_name'] = mb_strtoupper($business->business_full_name);
                $header_data['report_name'] = mb_strtoupper($title);
                
            } else {
                $data[] = [mb_strtoupper($business->business_full_name)];
                $data[] = [mb_strtoupper($title)];
                $data[] = [];
                $data[] = $headers;
            }

            foreach ($records as $item) {
                $line = [
                    $item->name,
                    $item->business_name,
                    $item->business_line,
                    $item->business_type,
                    $item->dni,
                    $item->tax_number,
                    $item->reg_number,
                    $item->telphone,
                    $item->email,
                    $item->country,
                    $item->state,
                    $item->city,
                    $item->address,
                    $item->latitude,
                    $item->length,
                    $item->is_exempt == 1 ? __('lang_v1.yes') : __('lang_v1.no'),
                    $item->allowed_credit == 1 ? __('lang_v1.yes') : __('lang_v1.no'),
                    $item->opening_balance,
                    $item->credit_limit,
                    $item->payment_term,
                    $item->contact_mode,
                    $item->customer_portfolio,
                    $item->customer_groups,
                ];

                $data[] = $line;
            }

            $output = [
                'success' => true,
                'data' => $data,
                'type' => $report_type,
                'header_data' => $header_data,
                'headers' => [$headers]
            ];

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
}
