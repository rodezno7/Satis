<?php

namespace App\Http\Controllers;

use App\User;
use App\Business;
use App\BusinessLocation;
use App\TaxRate;
use App\Currency;
use App\Unit;
use App\Catalogue;
use App\Shortcut;
use App\State;
use App\Module;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

use DateTimeZone;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Utils\BusinessUtil;
use App\Utils\RestaurantUtil;

use App\Utils\ModuleUtil;

use App\System;
use App\Utils\TaxUtil;

class BusinessController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BusinessController
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new business/business as well as their
    | validation and creation.
    |
    */

    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $restaurantUtil;
    protected $moduleUtil;
    protected $taxUtil;
    protected $mailDrivers;

    /**
     * Constructor.
     *
     * @param  App\Utils\BusinessUtil  $businessUtil
     * @param  App\Utils\RestaurantUtil  $restaurantUtil
     * @param  App\Utils\ModuleUtil  $moduleUtil
     * @param  App\Utils\TaxUtil  $taxUtil
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, RestaurantUtil $restaurantUtil, ModuleUtil $moduleUtil, TaxUtil $taxUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->taxUtil = $taxUtil;

        $this->avlble_modules = [
            'tables' => [
                'name' => __('restaurant.tables'),
                'tooltip' => __('restaurant.tooltip_tables')
            ],
            'modifiers' => [
                'name' => __('restaurant.modifiers'),
                'tooltip' => __('restaurant.tooltip_modifiers')
            ],
            'service_staff' => [
                'name' => __('restaurant.service_staff'),
                'tooltip' => __('restaurant.tooltip_service_staff')
            ],
            'kitchen' => [
                'name' => __('restaurant.kitchen_for_restaurant')
            ],
            'account' => [
                'name' => __('lang_v1.account')
            ]
        ];

        $this->theme_colors = [
            'blue' => 'Blue',
            'black' => 'Black',
            'purple' => 'Purple',
            'green' => 'Green',
            'red' => 'Red',
            'yellow' => 'Yellow',
            'blue-light' => 'Blue Light',
            'black-light' => 'Black Light',
            'purple-light' => 'Purple Light',
            'green-light' => 'Green Light',
            'red-light' => 'Red Light',
        ];

        $this->mailDrivers = [
            'smtp' => 'SMTP',
            'sendmail' => 'Sendmail',
            'mailgun' => 'Mailgun',
            'mandrill' => 'Mandrill',
            'ses' => 'SES',
            'sparkpost' => 'Sparkpost'
        ];

        $this->check_format_kits = [
            1 => __('accounting.format_1'),
            2 => __('accounting.format_2')
        ];
    }

    /**
     * Shows registration form
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        if (!env('ALLOW_REGISTRATION', true)) {
            return redirect('/');
        }

        $currencies = $this->businessUtil->allCurrencies();
        
        $timezone_list = $this->businessUtil->allTimeZones();

        $months = [];
        for ($i=1; $i<=12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $accounting_methods = $this->businessUtil->allAccountingMethods();
        $package_id = request()->package;

        $system_settings = System::getProperties(['superadmin_enable_register_tc', 'superadmin_register_tc'], true);
        
        return view('business.register', compact(
            'currencies',
            'timezone_list',
            'months',
            'accounting_methods',
            'package_id', 'system_settings'
        ));
    }

    /**
     * Handles the registration of a new business and it's owner
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        if (!env('ALLOW_REGISTRATION', true)) {
            return redirect('/');
        }
        
        try {
            $validator = $request->validate(
                [
                    'name' => 'required|max:255',
                    'currency_id' => 'required|numeric',
                    'country' => 'required|max:255',
                    'state' => 'required|max:255',
                    'city' => 'required|max:255',
                    'zip_code' => 'required|max:255',
                    'landmark' => 'required|max:255',
                    'time_zone' => 'required|max:255',
                    'surname' => 'max:10',
                    'email' => 'sometimes|nullable|email|max:255',
                    'first_name' => 'required|max:255',
                    'username' => 'required|min:4|max:255|unique:users',
                    'password' => 'required|min:4|max:255',
                    'fy_start_month' => 'required',
                    'accounting_method' => 'required',
                ],
                [
                    'name.required' => __('validation.required', ['attribute' => __('business.business_name')]),
                    'name.currency_id' => __('validation.required', ['attribute' => __('business.currency')]),
                    'country.required' => __('validation.required', ['attribute' => __('business.country')]),
                    'state.required' => __('validation.required', ['attribute' => __('business.state')]),
                    'city.required' => __('validation.required', ['attribute' => __('business.city')]),
                    'zip_code.required' => __('validation.required', ['attribute' => __('business.zip_code')]),
                    'landmark.required' => __('validation.required', ['attribute' => __('business.landmark')]),
                    'time_zone.required' => __('validation.required', ['attribute' => __('business.time_zone')]),
                    'email.email' => __('validation.email', ['attribute' => __('business.email')]),
                    'first_name.required' => __('validation.required', ['attribute' =>
                        __('business.first_name')]),
                    'username.required' => __('validation.required', ['attribute' => __('business.username')]),
                    'username.min' => __('validation.min', ['attribute' => __('business.username')]),
                    'password.required' => __('validation.required', ['attribute' => __('business.username')]),
                    'password.min' => __('validation.min', ['attribute' => __('business.username')]),
                    'fy_start_month.required' => __('validation.required', ['attribute' => __('business.fy_start_month')]),
                    'accounting_method.required' => __('validation.required', ['attribute' => __('business.accounting_method')]),
                ]
            );

            DB::beginTransaction();

            //Create owner.
            $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password', 'language']);
            $user = User::create_user($owner_details);

            // Store binnacle
            $this->businessUtil->registerBinnacle(
                'user',
                'create',
                $user->username,
                $user
            );

            $business_details = $request->only(['name', 'start_date', 'currency_id', 'time_zone','nit','nrc','line_of_business','legal_representative','business_full_name']);
            $business_details['fy_start_month'] = 1;

            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);
            
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = Carbon::createFromFormat('m/d/Y', $business_details['start_date'])->toDateString();
            }
            
            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }
            
            $business = $this->businessUtil->createNewBusiness($business_details);

            //Update user with business id
            $user->business_id = $business->id;
            $user->save();

            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtil->addLocation($business->id, $business_location);

            //create new permission with the new location
            if (Module::where('name', 'Sucursales')->first()) {

                $module = Module::where('name', 'Sucursales')->first();                
                $permission = Permission::where('name', 'location.' . $business->id)->select('name')->first();

                if (empty($permission)) {
                    $permission = Permission::create([
                        'name' => 'location.' . $new_location->id,
                        'description' => 'Bodega ' . $business->name,
                        'guard_name' => 'web',
                        'module_id' => $module->id,
                    ]);

                    // Store binnacle
                    $this->businessUtil->registerBinnacle(
                        'permission',
                        'create',
                        $permission->name,
                        $permission
                    );
                }
            }           

            DB::commit();

            //Process payment information if superadmin is installed & package information is present
            $is_installed_superadmin = $this->moduleUtil->isSuperadminInstalled();
            $package_id = $request->get('package_id', null);
            if ($is_installed_superadmin && !empty($package_id) && (config('app.env') != 'demo')) {
                $package = \Modules\Superadmin\Entities\Package::find($package_id);
                if (!empty($package)) {
                    Auth::login($user);
                    return redirect()->route('register-pay', ['package_id' => $package_id]);
                }
            }

            $output = ['success' => 1,
            'msg' => __('business.business_created_succesfully')
        ];

        return redirect('login')->with('status', $output);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

        $output = ['success' => 0,
        'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
    ];

    return back()->with('status', $output)->withInput();
}
}

    /**
     * Handles the validation username
     *
     * @return \Illuminate\Http\Response
     */
    public function postCheckUsername(Request $request)
    {
        $username = $request->input('username');

        if (!empty($request->input('username_ext'))) {
            $username .= $request->input('username_ext');
        }

        $count = User::where('username', $username)->count();
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }
    
    /**
     * Shows business settings form
     *
     * @return \Illuminate\Http\Response
     */
    public function getBusinessSettings()
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $timezone_list = [];
        foreach ($timezones as $timezone) {
            $timezone_list[$timezone] = $timezone;
        }

        $catalogue = Catalogue::select('id', DB::raw("CONCAT(code, ' ', name) as full_name"))
        ->where('status', 1)
        //->whereNOTIn('id', [DB::raw("select parent from catalogues")])
        ->pluck('full_name', 'id');

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        //agregando datos de lacation
        $business_location = BusinessLocation::where('business_id', $business_id)->first();

        //
        
        $currencies = $this->businessUtil->allCurrencies();
        $tax_details = TaxRate::forBusinessDropdown($business_id);
        $tax_rates = $tax_details['tax_rates'];

        $months = [];
        for ($i=1; $i<=12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $accounting_methods = [
            'fifo' => __('business.fifo'),
            'lifo' => __('business.lifo')
        ];
        $commission_agent_dropdown = [
            '' => __('lang_v1.disable'),
            'logged_in_user' => __('lang_v1.logged_in_user'),
            'user' => __('lang_v1.select_from_users_list'),
            'cmsn_agnt' => __('lang_v1.select_from_commisssion_agents_list')
        ];

        $units_dropdown = Unit::forDropdown($business_id, true);

        $date_formats = [
            'd-m-Y' => 'dd-mm-yyyy',
            'm-d-Y' => 'mm-dd-yyyy',
            'd/m/Y' => 'dd/mm/yyyy',
            'm/d/Y' => 'mm/dd/yyyy'
        ];

        $shortcuts = json_decode($business->keyboard_shortcuts, true);
        
        if (empty($business->pos_settings)) {
            $pos_settings = $this->businessUtil->defaultPosSettings();
        } else {
            $pos_settings = json_decode($business->pos_settings, true);
        }

        $email_settings = [];
        if (empty($business->email_settings)) {
            $email_settings = $this->businessUtil->defaultEmailSettings();
        } else {
            $email_settings = $business->email_settings;
        }

        $sms_settings = [];
        if (empty($business->sms_settings)) {
            $sms_settings = $this->businessUtil->defaultSmsSettings();
        } else {
            $sms_settings = $business->sms_settings;
        }

        // Dashboard settings
        if (empty($business->dashboard_settings)) {
            $dashboard_settings = $this->businessUtil->defaultDashboardSettings();
        } else {
            $dashboard_settings = json_decode($business->dashboard_settings, true);
            $default_dashboard_settings = $this->businessUtil->defaultDashboardSettings();
            foreach ($default_dashboard_settings as $key => $value) {
                if (! isset($dashboard_settings[$key])) {
                    $dashboard_settings[$key] = $value;
                }
            }
        }

        // Customer settings
        if (empty($business->customer_settings)) {
            $customer_settings = $this->businessUtil->defaultCustomerSettings();
        } else {
            $customer_settings = json_decode($business->customer_settings, true);
        }

        // Product settings
        if (empty($business->product_settings)) {
            $product_settings = $this->businessUtil->defaultProductSettings();
        } else {
            $product_settings = json_decode($business->product_settings, true);

            $default_product_settings = $this->businessUtil->defaultProductSettings();

            foreach ($default_product_settings as $key => $value) {
                if (! isset($product_settings[$key])) {
                    $product_settings[$key] = $value;
                }
            }
        }

        // Sale settings
        if (empty($business->sale_settings)) {
            $sale_settings = $this->businessUtil->defaultSaleSettings();
        } else {
            $sale_settings = json_decode($business->sale_settings, true);
        }

        $modules = $this->avlble_modules;

        $theme_colors = $this->theme_colors;

        $mail_drivers = $this->mailDrivers;

        $states = State::forDropdown($business_id, false);

        $product_taxes = $this->taxUtil->getTaxGroups($business_id, 'products')
            ->pluck('name', 'id');

        return view('business.settings', compact(
            'business',
            'currencies',
            'tax_rates',
            'timezone_list',
            'months',
            'accounting_methods',
            'commission_agent_dropdown',
            'units_dropdown',
            'date_formats',
            'shortcuts',
            'pos_settings',
            'modules',
            'theme_colors',
            'email_settings',
            'sms_settings',
            'mail_drivers',
            'business_location',
            'catalogue',
            'states',
            'dashboard_settings',
            'customer_settings',
            'product_settings',
            'sale_settings',
            'product_taxes'
        ));
    }

    public function getAccountingSettings()
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }
        $shortcuts = Shortcut::get();

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $business_accounts = [];
        $account_ids = [];
        if(!is_null($business->accounting_inventory_id)){ array_push($account_ids, $business->accounting_inventory_id); }
        if(!is_null($business->accounting_debtor_result_id)){ array_push($account_ids, $business->accounting_debtor_result_id); }
        if(!is_null($business->accounting_creditor_result_id)){ array_push($account_ids, $business->accounting_creditor_result_id); }
        if(!is_null($business->accounting_cost_id)){ array_push($account_ids, $business->accounting_cost_id); }
        if(!is_null($business->accounting_profit_and_loss_id)){ array_push($account_ids, $business->accounting_profit_and_loss_id); }
        if(!is_null($business->accounting_deficit_id)){ array_push($account_ids, $business->accounting_deficit_id); }
        if(!is_null($business->accounting_utility_id)){ array_push($account_ids, $business->accounting_utility_id); }
        if(!is_null($business->accounting_expense_id)){ array_push($account_ids, $business->accounting_expense_id); }
        if(!is_null($business->accounting_supplier_id)){ array_push($account_ids, $business->accounting_supplier_id); }
        if(!is_null($business->accounting_customer_id)){ array_push($account_ids, $business->accounting_customer_id); }
        if(!is_null($business->accounting_bank_id)){ array_push($account_ids, $business->accounting_bank_id); }
        if(!is_null($business->accounting_extra_expenses_id)){ array_push($account_ids, $business->accounting_extra_expenses_id); }
        if(!is_null($business->accounting_extra_incomes_id)){ array_push($account_ids, $business->accounting_extra_incomes_id); }
        if(!is_null($business->accounting_ordinary_expenses_id)){ array_push($account_ids, $business->accounting_ordinary_expenses_id); }
        if(!is_null($business->accounting_sells_cost_id)){ array_push($account_ids, $business->accounting_sells_cost_id); }
        if(!is_null($business->accounting_return_sells_id)){ array_push($account_ids, $business->accounting_return_sells_id); }
        if(!is_null($business->accounting_ordinary_incomes_id)){ array_push($account_ids, $business->accounting_ordinary_incomes_id); }
        
        if(!is_null($business->accounting_vat_local_purchase_id)){ array_push($account_ids, $business->accounting_vat_local_purchase_id); }
        if(!is_null($business->accounting_vat_import_id)){ array_push($account_ids, $business->accounting_vat_import_id); }
        if(!is_null($business->accounting_perception_id)){ array_push($account_ids, $business->accounting_perception_id); }
        if(!is_null($business->accounting_withheld_id)){ array_push($account_ids, $business->accounting_withheld_id); }

        if(!empty($account_ids)){
            $business_accounts = Catalogue::where('status', 1)
            ->whereIn('id', $account_ids)
            ->select(
                DB::raw("CONCAT(code, ' ', name) as account_name"),
                'id'
            )->get()
            ->pluck('account_name', 'id');
        }

        $cost_main_account = Catalogue::select('code')->where('id', $business->accounting_cost_id)->first();

        //To get the index of the enum fields        
        $debt_to_pay_type_selected = $business->debt_to_pay_type;
        $receivable_type_selected = $business->receivable_type;
        
        $check_format_kits = $this->check_format_kits;

        return view('business.settings_accounting', compact(
            'business',
            'business_accounts',
            'cost_main_account',
            'shortcuts',
            'debt_to_pay_type_selected',
            'receivable_type_selected',
            'check_format_kits'
        ));

        //To get the index of the enum fields        
        $debt_to_pay_type_selected = $business->debt_to_pay_type;
        $receivable_type_selected = $business->receivable_type;       
        return view('business.settings_accounting', compact('business', 'business_accounts', 'cost_main_account', 'shortcuts', 'debt_to_pay_type_selected', 'receivable_type_selected'));
    }

    /**
     * Updates business settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postBusinessSettings(Request $request)
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $business_details = $request->only([
                'name',
                'start_date',
                'currency_id',
                'tax_label_1',
                'tax_number_1',
                'tax_label_2',
                'tax_number_2',
                'default_profit_percent',
                'default_sales_tax',
                'default_sales_discount',
                'sell_price_tax',
                'sku_prefix',
                'portfolio_prefix',
                'time_zone',
                'fy_start_month',
                'accounting_method',
                'transaction_edit_days',
                'sales_cmsn_agnt',
                'item_addition_method',
                'currency_symbol_placement',
                'on_product_expiry',
                'stop_selling_before',
                'default_unit',
                'expiry_type',
                'date_format',
                'time_format',
                'ref_no_prefixes',
                'theme_color',
                'email_settings',
                'sms_settings',
                'nit',
                'nrc',
                'line_of_business',
                'legal_representative',
                'business_full_name',
                'claim_prefix',
                'cashier_prefix',
                'credit_prefix',
                'status_claim_prefix',
                'claim_type_prefix',
                'warehouse_prefix',
                'fixed_asset_prefix',
                'quote_validity',
                'quote_legend',
                'quote_prefix',
                'state_id',
                'physical_inventory_record_date',
                'account_statement_legend'
            ]);

            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = Carbon::createFromFormat('m/d/Y', $business_details['start_date'])->toDateString();
            }

            if(!empty($request->input('show_open_daily_z_cut_amount') && $request->input('show_open_daily_z_cut_amount') == 1)){
                $business_details['show_open_daily_z_cut_amount'] = 1;
            } else {
                $business_details['show_open_daily_z_cut_amount'] = 0;
            }

            if (!empty($request->input('enable_tooltip')) &&  $request->input('enable_tooltip') == 1) {
                $business_details['enable_tooltip'] = 1;
            } else {
                $business_details['enable_tooltip'] = 0;
            }

            $business_details['show_expenses_on_sales_report'] = $request->input('show_expenses_on_sales_report') ? 1 : 0;

            $business_details['enable_product_expiry'] = !empty($request->input('enable_product_expiry')) &&  $request->input('enable_product_expiry') == 1 ? 1 : 0;
            if ($business_details['on_product_expiry'] == 'keep_selling') {
                $business_details['stop_selling_before'] = null;
            }

            $business_details['stock_expiry_alert_days'] = !empty($request->input('stock_expiry_alert_days')) ? $request->input('stock_expiry_alert_days') : 30;

            //Check for Purchase currency
            if (!empty($request->input('purchase_in_diff_currency')) &&  $request->input('purchase_in_diff_currency') == 1) {
                $business_details['purchase_in_diff_currency'] = 1;
                $business_details['purchase_currency_id'] = $request->input('purchase_currency_id');
                $business_details['p_exchange_rate'] = $request->input('p_exchange_rate');
            } else {
                $business_details['purchase_in_diff_currency'] = 0;
                $business_details['purchase_currency_id'] = null;
                $business_details['p_exchange_rate'] = 1;
            }

            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }

            $checkboxes = [
                'enable_editing_product_from_purchase',
                'enable_inline_tax',
                'annull_sale_expiry',
                'enable_brand',
                'enable_category',
                'enable_unit_groups',
                'enable_sub_category',
                'enable_price_tax',
                'enable_purchase_status',
                'enable_lot_number',
                'enable_racks',
                'enable_row',
                'enable_position',
                'enable_editing_avg_cost_from_purchase',
                'enable_remission_note'
            ];

            foreach ($checkboxes as $value) {
                $business_details[$value] = !empty($request->input($value)) &&  $request->input($value) == 1 ? 1 : 0;
            }
            
            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();

            //Update business settings
            if (!empty($business_details['logo'])) {
                $business->logo = $business_details['logo'];
            } else {
                unset($business_details['logo']);
            }

            //System settings
            $shortcuts = $request->input('shortcuts');
            $business_details['keyboard_shortcuts'] = json_encode($shortcuts);

            //pos_settings
            $pos_settings = $request->input('pos_settings');
            $default_pos_settings = $this->businessUtil->defaultPosSettings();
            foreach ($default_pos_settings as $key => $value) {
                if (!isset($pos_settings[$key])) {
                    $pos_settings[$key] = $value;
                }
            }
            $business_details['pos_settings'] = json_encode($pos_settings);

            // Dashboard settings
            $dashboard_settings = $request->input('dashboard_settings');

            $default_dashboard_settings = $this->businessUtil->defaultDashboardSettings();

            foreach ($default_dashboard_settings as $key => $value) {
                if (! isset($dashboard_settings[$key])) {
                    if ($value === 1) {
                        $dashboard_settings[$key] = 0;
                    } else {
                        $dashboard_settings[$key] = $value;
                    }
                }
            }

            $business_details['dashboard_settings'] = json_encode($dashboard_settings);

            // Customer settings
            $customer_settings = $request->input('customer_settings');

            $default_customer_settings = $this->businessUtil->defaultCustomerSettings();

            foreach ($default_customer_settings as $key => $value) {
                if (! isset($customer_settings[$key])) {
                    if ($value === 1) {
                        $customer_settings[$key] = 0;
                    } else {
                        $customer_settings[$key] = $value;
                    }
                }
            }

            $business_details['customer_settings'] = json_encode($customer_settings);

            // Product settings
            $product_settings = $request->input('product_settings');

            $default_product_settings = $this->businessUtil->defaultProductSettings();

            foreach ($default_product_settings as $key => $value) {
                if (! isset($product_settings[$key])) {
                    if ($value === 1) {
                        $product_settings[$key] = 0;
                    } else {
                        $product_settings[$key] = $value;
                    }
                }
            }

            $business_details['product_settings'] = json_encode($product_settings);

            // Sale settings
            $sale_settings = $request->input('sale_settings');

            $default_sale_settings = $this->businessUtil->defaultSaleSettings();

            foreach ($default_sale_settings as $key => $value) {
                if (! isset($sale_settings[$key])) {
                    if ($value === 1) {
                        $sale_settings[$key] = 0;
                    } else {
                        $sale_settings[$key] = $value;
                    }
                }
            }

            $business_details['sale_settings'] = json_encode($sale_settings);

            //Enabled modules
            $enabled_modules = $request->input('enabled_modules');
            $business_details['enabled_modules'] = !empty($enabled_modules) ? $enabled_modules : null;

            $business->fill($business_details);
            $business->save();

            //update session data
            $request->session()->put('business', $business);

            //Update Currency details
            $currency = Currency::find($business->currency_id);
            $request->session()->put('currency', [
                'id' => $currency->id,
                'code' => $currency->code,
                'symbol' => $currency->symbol,
                'thousand_separator' => $currency->thousand_separator,
                'decimal_separator' => $currency->decimal_separator,
            ]);
            
            //update current financial year to session
            $financial_year = $this->businessUtil->getCurrentFinancialYear($business->id);
            $request->session()->put('financial_year', $financial_year);

            $location_details = $request->only([
                'mobile',
                'alternate_number',
                'email',
                'landmark'
            ]);
    
            $business_location = BusinessLocation::first();
    
            if (! empty($business_location)) {
                $business_location->mobile = $location_details['mobile'];
                $business_location->alternate_number = $location_details['alternate_number'];
                $business_location->email = $location_details['email'];
                $business_location->landmark = $location_details['landmark'];
    
                $business_location->save();
            }

            DB::commit();
            
            $output = [
                'success' => 1,
                'msg' => __('business.settings_updated_success')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect('business/settings')->with('status', $output);
    }

public function postAccountingSettings(Request $request)
{
    if (!auth()->user()->can('business_settings.access')) {
        abort(403, 'Unauthorized action.');
    }

    try {
        $business_details =
            $request->only([
                'accounting_supplier_id',
                'accounting_expense_id',
                'accounting_customer_id',
                'accounting_bank_id',
                'entries_numeration_mode',
                'accounting_utility_id',
                'accounting_deficit_id',
                'accounting_cost_id',
                'accounting_debtor_result_id',
                'accounting_creditor_result_id',
                'accounting_profit_and_loss_id',
                'balance_debit_levels_number',
                'balance_credit_levels_number',
                'accounting_ordinary_incomes_id',
                'accounting_return_sells_id',
                'accounting_sells_cost_id',
                'accounting_ordinary_expenses_id',
                'accounting_extra_incomes_id',
                'accounting_extra_expenses_id',
                'level_childrens_ordynary_incomes',
                'level_childrens_ordynary_expenses',
                'level_childrens_extra_incomes',
                'level_childrens_extra_expenses',
                'accounting_inventory_id',
                'accounting_vat_local_purchase_id',
                'accounting_vat_import_id',
                'accounting_perception_id',
                'debt_to_pay_type',
                'receivable_type',
                'check_format_kit',
                'ledger_digits',
                'sale_accounting_entry_mode',
                'accounting_withheld_id'
            ]);

        $checkboxes = [
            'enable_sub_accounts_in_bank_transactions',
            'enable_validation_entries',
            'edition_in_approved_entries',
            'deletion_in_approved_entries',
            'edition_in_number_entries',
            'allow_uneven_totals_entries',
            'allow_nullate_checks_in_approved_entries',
            'allow_entries_approval_disorder',
            'enable_description_line_entries_report',
            'match_check_n_expense'
        ];
        
        foreach ($checkboxes as $value) {
            $business_details[$value] = !empty($request->input($value)) &&  $request->input($value) == 1 ? 1 : 0;
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        DB::beginTransaction();

        $business->fill($business_details);
        $business->save();

        $shortcut_id = $request->input('shortcut_id');
        $shortcut_description = $request->input('description');
        

        if (!empty($shortcut_id))
        {
            $cont = 0;                
            while($cont < count($shortcut_id))
            {
                $shortcut = Shortcut::findOrFail($shortcut_id[$cont]);
                $shortcut->description = $shortcut_description[$cont];
                $shortcut->save();
                $cont = $cont + 1;
            } 
        }

        DB::commit();
        $output = [
            'success' => true,
            'msg' => __('business.settings_updated_success')
        ];
    }
    catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong')
        ];
    }
    return $output;
}
public function updateAnullSaleExpiry(Request $request)
{
    if (!auth()->user()->can('business_settings.access')) {
        abort(403, 'Unauthorized action.');
    }
    try {
        $business_details = $request->only(['annull_sale_expiry']);
        $business_details['annull_sale_expiry'] = $request->annull_sale_expiry && !empty($request->annull_sale_expiry) ? 1 : 0;
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        DB::beginTransaction();

        $business->fill($business_details);
        $business->save();
        DB::commit();
        $output = [
            'success' => true,
            'msg' => __("business.annull_sale_expiry_update"),
            'msg_dos' => 'Restricción de anulación agregada'
        ];
    } catch (\Exception $e) {
        \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
    }
    return $output;
}
}
