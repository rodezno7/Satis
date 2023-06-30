<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use App\User;
use App\City;
use DateTime;
use App\Order;
use App\Quote;
use App\State;
use App\Product;
use App\Business;
use App\Customer;
use App\Employees;
use App\QuoteLine;
use App\Warehouse;
use App\Variation;
use App\DocumentType;
use App\BusinessLocation;
use App\CustomerVehicle;
use App\VariationGroupPrice;

use App\Utils\TaxUtil;
use App\SellingPriceGroup;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use App\Exports\OrderTransactionExport;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Collection;

class OrderController extends Controller
{
    protected $taxUtil;
    protected $productUtil;
    protected $businessUtil;
    protected $transactionUtil;

    protected $delivery_types;

    /**
     * Constructor
     *
     * @param TaxUtil $taxUtil
     * @return void
     */
    public function __construct(TaxUtil $taxUtil, ProductUtil $productUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil){
        $this->taxUtil = $taxUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;

        $this->delivery_types = [
            "at_home" => __("order.at_home"),
            "eastern_route" => __("order.eastern_route"),
            "western_route" => __("order.western_route"),
            "caex" => __("order.caex"),
            "location" => __("order.location"),
            "other" => __("order.other")
        ];

        if (config('app.disable_sql_req_pk')) {
            DB::statement('SET SESSION sql_require_primary_key=0');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->can("order.view")){
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get("user.business_id");
        if(request()->ajax()){
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');

            $orders = Quote::join("employees", "quotes.employee_id", "employees.id")
                ->leftJoin("transactions", "quotes.transaction_id", "transactions.id")
                ->whereRaw('DATE(quotes.quote_date) BETWEEN ? AND ?', [$start_date, $end_date])
                ->where("quotes.business_id", $business_id)
                ->where("quotes.type", "order")
                ->select(
                    "quotes.id",
                    "quotes.quote_date",
                    "quotes.quote_ref_no",
                    "quotes.delivery_type",
                    "quotes.customer_name",
                    "quotes.location_id",
                    "transactions.final_total",
                    DB::raw("IF(quotes.invoiced = 1, 'yes', 'no') as invoiced"),
                    DB::raw("CONCAT(employees.first_name, ' ', employees.last_name) as employee_name")
                );

            /** filter business location permitted */
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $orders->whereIn('quotes.location_id', $permitted_locations);
            }

            // If business location filter applied
            $location_id = request()->get('location_id', null);
            if (!empty($location_id)) {
                $orders->where('quotes.location_id', $location_id);
            }

            return Datatables::of($orders)
                ->addColumn('action',
                '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        @can("order.view")
                            <li><a href="{{action(\'OrderController@show\', [$id])}}" class="show_order"><i class="fa fa-eye"></i> @lang("messages.view")</a></li>
                        @endcan
                        @can("quotes.view")
                            <li><a href="{{ action(\'QuoteController@viewQuoteWorkshop\', [$id]) }}" target="__blank"><i class="fa fa-file-pdf-o"></i>PDF</a></li>
                        @endcan
                        @can("order.update")
                            <li><a href="{{action(\'OrderController@edit\', [$id])}}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                        @endcan
                        @can("order.delete")
                            <li><a href="{{action(\'OrderController@destroy\', [$id])}}" class="delete_order"><i class="fa fa-trash"></i> @lang("messages.delete")</a></li>
                        @endcan
                    </ul>
                </div>')
                ->filterColumn('employee_name', function($query, $keyword){
                    $query->whereRaw('CONCAT(employees.first_name, " ", employees.last_name) LIKE ?', ['{$keyword}']);
                })
                ->editColumn('invoiced', '{{ __("messages.".$invoiced) }}')
                ->editColumn('final_total', '<span class="display_currency" data-currency_symbol="true" data-precision="2">$ {{ $final_total ? $final_total : 0 }}</span>')
                ->editColumn('delivery_type', '{{ __("order.".$delivery_type) }}')
                ->editColumn('quote_date', '{{ @format_date($quote_date) }}')
                ->removeColumn('id')
                ->rawColumns(['final_total', 'quote_date', 'action'])
                ->make(true);
        }
        $locations = BusinessLocation::forDropdown($business_id, false, true);
        $locations = $locations['locations'];

        return view("order.index", compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can("order.create")) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $documents =  DocumentType::where('business_id', $business_id)
            ->where('is_active', 1)
            ->pluck('document_name', 'id');

        $payment_condition = ['cash' => __('order.cash'), 'credit' => __('order.credit')];
        $tax_detail = ['no' => __('messages.no'), 'yes' => __('messages.yes')];
        $discount_types = [ "fixed" => __("lang_v1.fixed"), "percentage" => __("lang_v1.percentage") ];
        $selling_price_groups = SellingPriceGroup::forDropdown($business_id, false);
        $delivery_types = $this->delivery_types;
        
        $e = Employees::where('employees.business_id', $business_id);
        
        /** filter employess by location permitted */
        $permitted_locations = auth()->user()->permitted_locations();
        
        if ($permitted_locations != 'all') {
            $e->whereIn('employees.location_id', $permitted_locations);
        }

        $employees = $e
            ->select('id', DB::raw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) as full_name"))
            ->pluck('full_name', 'id');;


        $warehouses = Warehouse::forDropdown($business_id, false);

        $states = State::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $locations = BusinessLocation::forDropdown($business_id, false, true);
        $locations = $locations['locations'];

        $service_blocks = [];

        return view("order.create")
            ->with(compact(
                'documents',
                'payment_condition',
                'tax_detail',
                'discount_types',
                'delivery_types',
                'warehouses',
                'selling_price_groups',
                'states',
                'employees',
                'locations',
                'service_blocks'
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
        if (!auth()->user()->can("order.create")) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get("user.business_id");
            $quote_id = $request->input("quote_id", null);

            $quote = Quote::firstOrNew(["id" => $quote_id, "business_id" => $business_id]);

            if (empty($request->input("ref_no"))) {
                $last_correlative = Quote::where("business_id", $business_id)->max("id");

                $business = Business::find($business_id);

                $correlative = $last_correlative > 0 ? $last_correlative + 1 : $correlative = 1;
                $cont = str_pad($correlative, 5, "0", STR_PAD_LEFT);
                $quote->quote_ref_no = "".$business->quote_prefix."".$cont."";

            } else {
                $quote->quote_ref_no = $request->input("ref_no");
            }

            $quote->customer_id = $request->input("customer_id");
            $quote->type = "order";
            $quote->status = "opened";
            $quote->customer_name = mb_strtoupper($request->input("customer_name"));
            $quote->contact_name = !empty($request->input("contact_name")) ? mb_strtoupper($request->input("contact_name")) : null;
            $quote->quote_date = $this->transactionUtil->uf_date($request->input("order_date"));
            $quote->employee_id = !empty($request->input("employee_id")) ? $request->input("employee_id") : null;
            $quote->document_type_id = $request->input("document_type_id");
            $quote->location_id = $request->input("location_id");
            $quote->mobile = !empty($request->input("mobile")) ? $request->input("mobile") : null;
            $quote->email =  !empty($request->input("email")) ? $request->input("email") : null;
            $quote->payment_condition = $request->input("payment_condition");
            $quote->validity =  !empty($request->input("validity")) ? $request->input("validity") : null;
            $quote->delivery_time =  !empty($request->input("delivery_time")) ? $request->input("delivery_time") : null;
            $quote->delivery_type =  !empty($request->input("delivery_type")) ? $request->input("delivery_type") : "location";
            $quote->other_delivery_type =  !empty($request->input("other_delivery_type")) ? $request->input("other_delivery_type") : null;
            $quote->delivery_date = $this->transactionUtil->uf_date($request->input("delivery_date"));
            $quote->state_id =  !empty($request->input("state_id")) ? $request->input("state_id") : null;
            $quote->city_id =  !empty($request->input("city_id")) ? $request->input("city_id") : null;
            $quote->tax_detail = $request->input("tax_detail") == "yes" ? 1 : 0;
            $quote->address = !empty($request->input("delivery_address")) ? $request->input("delivery_address") : null;
            $quote->landmark = !empty($request->input("land_mark")) ? $request->input("land_mark") : null;
            $quote->discount_type = $request->input("discount_type");
            $quote->discount_amount = $request->input("discount_amount", null);
            $quote->tax_amount = $this->transactionUtil->num_uf($request->input("tax_amount", null));
            $quote->total_before_tax = $this->transactionUtil->num_uf($request->input("subtotal"));
            $quote->total_final = $this->transactionUtil->num_uf($request->input("total_final"));
            $quote->terms_conditions = !empty($request->input("terms_conditions")) ? $request->input("terms_conditions") : null;
            $quote->note = !empty($request->input("note")) ? $request->input("note") : null;
            $quote->user_id = !empty($quote->user_id) ? $quote->user_id : $request->session()->get("user.id");
            $quote->created_by = !empty($quote->created_by) ? $quote->created_by : $request->session()->get("user.id");

            if (config('app.business') == 'workshop') {
                $quote->customer_vehicle_id = ! empty($quote->customer_vehicle_id) ? $quote->customer_vehicle_id : $request->input("customer_vehicle_id");
            }

            DB::beginTransaction();

            $quote->save();

            $quote_lines = $request->input("order_lines");

            $quote_line_ids = [];

            foreach ($quote_lines as $ql) {
                if ($ql["quote_line_id"] > 0) {
                    $quote_line_ids[] = (int)$ql["quote_line_id"];

                    $quote_line = QuoteLine::find($ql["quote_line_id"]);
                    $quote_line->quantity = $ql["quantity"];
                    $quote_line->discount_type = $ql["discount_line_type"];
                    $quote_line->discount_amount = $this->transactionUtil->num_uf($ql["discount_line_amount"]);
                    $quote_line->tax_amount = $this->transactionUtil->num_uf($ql["tax_line_amount"]);

                    if (config("app.business") == "workshop") {
                        $quote_line->service_parent_id = $ql["service_parent_id"] > 0 ? $ql["service_parent_id"] : null;
                        $quote_line->note = $ql["note_line"];
                    }

                    $quote_line->save();

                } else{
                    $quote_line = new QuoteLine();
                    $quote_line->quote_id = $quote->id;
                    $quote_line->variation_id = $ql["variation_id"];
                    $quote_line->warehouse_id = $ql["warehouse_id"];
                    $quote_line->quantity = $ql["quantity"];
                    $quote_line->unit_price_exc_tax = $this->transactionUtil->num_uf($ql["unit_price_exc_tax"]);
                    $quote_line->unit_price_inc_tax = $this->transactionUtil->num_uf($ql["unit_price_inc_tax"]);
                    $quote_line->discount_type = $ql["discount_line_type"];
                    $quote_line->discount_amount = $this->transactionUtil->num_uf($ql["discount_line_amount"]);
                    $quote_line->tax_amount = $this->transactionUtil->num_uf($ql["tax_line_amount"]);

                    if (config("app.business") == "workshop") {
                        $quote_line->service_parent_id = $ql["service_parent_id"] > 0 ? $ql["service_parent_id"] : null;
                        $quote_line->note = $ql["note_line"];
                    }

                    $quote_line->save();

                    $quote_line_ids[] = $quote_line->id;
                }
            }

            //Delete quote lines deleted
            $quote_deleted_lines = QuoteLine::where("quote_id", $quote->id)
                ->whereNotIn("id", $quote_line_ids)
                ->delete();

            $output = [
                'success' => 1,
                'msg' => __('order.order_added_success')
            ];

            DB::commit();

        } catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect('orders')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!auth()->user()->can("order.view")){
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $order = Quote::join("customers as c", "quotes.customer_id", "c.id")
            ->leftJoin("document_types as dc", "quotes.document_type_id", "dc.id")
            ->leftJoin("employees as e", "quotes.employee_id", "e.id")
            ->leftJoin("states as st", "quotes.state_id", "st.id")
            ->leftJoin("cities as ct", "quotes.city_id", "ct.id")
            ->where("quotes.business_id", $business_id)
            ->where("quotes.id", $id)
            ->where("quotes.type", "order")
            ->select(
                "quotes.*",
                "st.name as state",
                "ct.name as city",
                "c.name as customer_real_name",
                "dc.document_name",
                DB::raw("CONCAT(e.first_name, ' ', e.last_name) as seller_name")
            )
            ->first();

        $quote_lines = Quote::join("quote_lines as ql", "quotes.id", "ql.quote_id")
            ->join("variations as v", "ql.variation_id", "v.id")
            ->join("products as p", "v.product_id", "p.id")
            ->where("quotes.business_id", $business_id)
            ->where("quotes.id", $id)
            ->select(
                "ql.*",
                "p.name as product_name",
                "p.tax as tax_id",
                "p.id as tax_percent" // Used to store tax percent
            )->get();

        /** Tax percent added */
        foreach($quote_lines as $ql){
            $ql->tax_percent = $this->taxUtil->getTaxPercent($ql->tax_id);
        }
        
        $discount_types = [ "fixed" => __("lang_v1.fixed"), "percentage" => __("lang_v1.percentage") ];

        return view("order.show")
            ->with(compact("order", "quote_lines", "discount_types"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can("order.update")) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');

        $order = Quote::join("customers as c", "quotes.customer_id", "c.id")
            ->where("quotes.business_id", $business_id)
            ->where("quotes.id", $id)
            ->where("quotes.type", "order")
            ->select(
                "quotes.*",
                "c.name as customer_real_name",
            )
            ->first();

        $quote_lines = Quote::join("quote_lines as ql", "quotes.id", "ql.quote_id")
            ->join("variations as v", "ql.variation_id", "v.id")
            ->join("products as p", "v.product_id", "p.id")
            ->where("quotes.business_id", $business_id)
            ->where("quotes.id", $id)
            ->select(
                "ql.*",
                "p.name as product_name",
                "p.tax as tax_id",
                "p.id as tax_percent", // Used to store tax percent
                "v.sell_price_inc_tax"
            )->get();

        /** Add tax percent and group prices */
        $group_prices = collect();

        foreach($quote_lines as $ql){
            $ql->tax_percent = $this->taxUtil->getTaxPercent($ql->tax_id);

            $sgp = VariationGroupPrice::join('selling_price_groups as spg', 'variation_group_prices.price_group_id', 'spg.id')
                ->where('variation_group_prices.variation_id', $ql->variation_id)
                ->select(
                    'variation_group_prices.price_group_id',
                    'spg.name as price_group',
                    'variation_group_prices.price_inc_tax'
                )->get();
            
            if (!empty($sgp)) {
                foreach ($sgp as $g) {
                    $item = collect([
                        'variation_id' => $ql->variation_id,
                        'price_group' => $g->price_group,
                        'price_inc_tax' => $g->price_inc_tax
                    ]);

                    $group_prices->push($item);
                }
            }
        }

        $documents =  DocumentType::where('business_id', $business_id)
            ->where('is_active', 1)
            ->pluck('document_name', 'id');

        $payment_condition = ['cash' => __('order.cash'), 'credit' => __('order.credit')];
        $tax_detail = ['no' => __('messages.no'), 'yes' => __('messages.yes')];
        $discount_types = [ "fixed" => __("lang_v1.fixed"), "percentage" => __("lang_v1.percentage") ];
        $selling_price_groups = SellingPriceGroup::forDropdown($business_id);
        $delivery_types = $this->delivery_types;

        $e = Employees::where('employees.business_id', $business_id);
        
        /** filter employess by location permitted */
        $permitted_locations = auth()->user()->permitted_locations();
        
        if ($permitted_locations != 'all') {
            $e->whereIn('employees.location_id', $permitted_locations);
        }

        $employees = $e
            ->select('id', DB::raw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) as full_name"))
            ->pluck('full_name', 'id');;

        $warehouses = $warehouses = Warehouse::forDropdown($business_id, false);

        $states = State::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $cities = City::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $locations = BusinessLocation::forDropdown($business_id, false, true);
        $locations = $locations['locations'];

        return view("order.edit")
            ->with(compact(
                'order',
                'quote_lines',
                'documents',
                'payment_condition',
                'tax_detail',
                'discount_types',
                'warehouses',
                'selling_price_groups',
                'delivery_types',
                'states',
                'cities',
                'employees',
                'group_prices',
                'locations'
            ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Quote  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can("order.update")) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get("user.business_id");
            
            $quote = Quote::where("id", $id)
                ->where("business_id", $business_id)
                ->first();

            $quote->customer_id = $request->input("customer_id");
            $quote->type = "order";
            $quote->status = "opened";
            $quote->customer_name = mb_strtoupper($request->input("customer_name"));
            $quote->contact_name = !empty($request->input("contact_name")) ? mb_strtoupper($request->input("contact_name")) : null;
            $quote->quote_date = $this->transactionUtil->uf_date($request->input("order_date"));
            $quote->employee_id = !empty($request->input("employee_id")) ? $request->input("employee_id") : null;
            $quote->document_type_id = $request->input("document_type_id");
            $quote->location_id = $request->input("location_id");
            $quote->mobile = !empty($request->input("mobile")) ? $request->input("mobile") : null;
            $quote->email =  !empty($request->input("email")) ? $request->input("email") : null;
            $quote->payment_condition = $request->input("payment_condition");
            $quote->validity =  !empty($request->input("validity")) ? $request->input("validity") : null;
            $quote->delivery_time =  !empty($request->input("delivery_time")) ? $request->input("delivery_time") : null;
            $quote->delivery_type =  !empty($request->input("delivery_type")) ? $request->input("delivery_type") : "location";
            $quote->other_delivery_type =  !empty($request->input("other_delivery_type")) ? $request->input("other_delivery_type") : null;
            $quote->delivery_date = $this->transactionUtil->uf_date($request->input("delivery_date"));
            $quote->state_id =  !empty($request->input("state_id")) ? $request->input("state_id") : null;
            $quote->city_id =  !empty($request->input("city_id")) ? $request->input("city_id") : null;
            $quote->tax_detail = $request->input("tax_detail") == "yes" ? 1 : 0;
            $quote->address = !empty($request->input("delivery_address")) ? $request->input("delivery_address") : null;
            $quote->landmark = !empty($request->input("land_mark")) ? $request->input("land_mark") : null;
            $quote->discount_type = $request->input("discount_type");
            $quote->discount_amount = $request->input("discount_amount", null);
            $quote->tax_amount = $this->transactionUtil->num_uf($request->input("tax_amount", null));
            $quote->total_before_tax = $this->transactionUtil->num_uf($request->input("subtotal"));
            $quote->total_final = $this->transactionUtil->num_uf($request->input("total_final"));
            $quote->terms_conditions = !empty($request->input("terms_conditions")) ? $request->input("terms_conditions") : null;
            $quote->note = !empty($request->input("note")) ? $request->input("note") : null;

            if (config('app.business') == 'workshop') {
                $quote->customer_vehicle_id = $request->input('customer_vehicle_id');
            }

            DB::beginTransaction();

            $quote->save();

            $quote_lines = $request->input("order_lines");
            $quote_line_ids = [];

            foreach ($quote_lines as $ql) {
                if (isset($ql["quote_line_id"]) && $ql["quote_line_id"] > 0) {
                    $quote_line_ids[] = (int)$ql["quote_line_id"];

                    $quote_line = QuoteLine::find($ql["quote_line_id"]);
                    $quote_line->quantity = $ql["quantity"];
                    $quote_line->unit_price_exc_tax = $this->transactionUtil->num_uf($ql["unit_price_exc_tax"]);
                    $quote_line->unit_price_inc_tax = $this->transactionUtil->num_uf($ql["unit_price_inc_tax"]);
                    $quote_line->discount_type = $ql["discount_line_type"];
                    $quote_line->discount_amount = $this->transactionUtil->num_uf($ql["discount_line_amount"]);
                    $quote_line->tax_amount = $this->transactionUtil->num_uf($ql["tax_line_amount"]);

                    if (config('app.business') == 'workshop') {
                        $quote_line->service_parent_id = $ql['service_parent_id'] > 0 ? $ql['service_parent_id'] : null;
                        $quote_line->note = $ql['note_line'];
                    }

                    $quote_line->save();

                } else {
                    $quote_line = new QuoteLine();
                    $quote_line->quote_id = $quote->id;
                    $quote_line->variation_id = $ql["variation_id"];
                    $quote_line->warehouse_id = $ql["warehouse_id"];
                    $quote_line->quantity = $ql["quantity"];
                    $quote_line->unit_price_exc_tax = $this->transactionUtil->num_uf($ql["unit_price_exc_tax"]);
                    $quote_line->unit_price_inc_tax = $this->transactionUtil->num_uf($ql["unit_price_inc_tax"]);
                    $quote_line->discount_type = $ql["discount_line_type"];
                    $quote_line->discount_amount = $this->transactionUtil->num_uf($ql["discount_line_amount"]);
                    $quote_line->tax_amount = $this->transactionUtil->num_uf($ql["tax_line_amount"]);

                    if (config('app.business') == 'workshop') {
                        $quote_line->service_parent_id = $ql['service_parent_id'] > 0 ? $ql['service_parent_id'] : null;
                        $quote_line->note = $ql['note_line'];
                    }

                    $quote_line->save();

                    $quote_line_ids[] = $quote_line->id;
                }
            }

            // Delete quote lines deleted
            $quote_deleted_lines = QuoteLine::where("quote_id", $quote->id)
                ->whereNotIn("id", $quote_line_ids)
                ->delete();

            $output = [
                'success' => 1,
                'msg' => __('order.order_updated_success')
            ];

            DB::commit();

        } catch(\Exception $e) {
            DB::rollBack();

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect('orders')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can("order.delete")){
            abort(403, "Unauthorized action");
        }

        try{
            if(request()->ajax()){
                $business_id = request()->session()->get("user.business_id");

                $quote = Quote::where("id", $id)
                    ->where("business_id", $business_id)
                    ->first();
                
                $quote_lines = QuoteLine::where("quote_id", $quote->id);
                
                DB::beginTransaction();
                
                if(!empty($quote_lines)) { $quote_lines->delete(); }
                if(!empty($quote)) { $quote->delete(); }

                $output = ['success' => true,
                            'msg' => __('order.order_deleted_success')
                        ];
                DB::commit();
            }
        } catch(\Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => $e->getMessage()
                        ];
        }

        return $output;
    }

    /**
     * Get all orders for orders planner
     */
    public function orderPlanner(){
        if(!auth()->user()->can("order.view")){
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $start_date = date('Y-m-d', strtotime('- 6 days'));
        $end_date = date('Y-m-d');

        $orders = Quote::join("customers as c", "quotes.customer_id", "c.id")
            ->leftJoin("document_types as dc", "quotes.document_type_id", "dc.id")
            ->leftJoin("employees as e", "quotes.employee_id", "e.id")
            ->where("quotes.business_id", $business_id)
            ->where("quotes.type", "order")
            ->whereBetween("quotes.delivery_date", [$start_date, $end_date])
            ->select(
                "quotes.*",
                "c.name as customer_real_name",
                "dc.document_name",
                DB::raw("CONCAT(e.first_name, ' ', e.last_name) as seller_name")
            )->orderBy("quotes.delivery_date", "desc");
    
        /** filter business location permitted */
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $orders->whereIn('quotes.location_id', $permitted_locations);
        }
        
        $orders = $orders->get();
        
        /** Orders status */
        $status = [
            "opened" => __("order.opened"),
            "in_preparation" => __("order.in_preparation"),
            "prepared" => __("order.prepared"),
            "on_route" => __("order.on_route"),
            "closed" => __("order.closed")
        ];
        /** Orders customers */
        $customers = Customer::where("business_id", $business_id)
            ->pluck("name", "id");
        /** Orders delivery types */
        $delivery_types = $this->delivery_types;
        /** Orders sellers */
        $sellers = Employees::where('business_id', $business_id)
            ->whereIn("position_id", [11, 13])
            ->select('id',
                DB::raw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) as name")
                )
            ->pluck('name', 'id');

        return view("order.orders_planner")
            ->with(compact("orders", 'customers', 'status', 'delivery_types', 'sellers'));
    }

    /**
     * Retrieves list of quotes, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getOrders() {
        if (request()->ajax()) {
            $term = request()->input('q', '');
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $quotes = Quote::join("customers as c", "quotes.customer_id", "c.id")
                ->leftJoin('tax_rate_tax_group AS trtg', 'c.tax_group_id', 'trtg.tax_group_id')
                ->leftJoin('tax_rates as tr', 'trtg.tax_rate_id', 'tr.id')
                ->where('quotes.business_id', $business_id)
                ->where('quotes.type', "order")
                ->where('quotes.invoiced', false)
                ->whereIn("quotes.status", ["opened", "in_preparation", "prepared"]);
            
            /** filter business location permitted */
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $quotes->whereIn('quotes.location_id', $permitted_locations);
            }

            if (!empty($term)) {
                $quotes->where(function ($query) use ($term) {
                    $query->where('quotes.customer_name', 'like', '%' . $term .'%')
                    ->orWhere("quotes.quote_ref_no", 'like', '%' . $term . '%');
                });
            }

            $quotes = $quotes->select(
                'c.id as customer_id',
                'c.name as c_name',
                'c.tax_group_id',
                'tr.percent as tax_percent',
                'tr.min_amount',
                'tr.max_amount',
                'c.is_default',
                'c.is_exempt',
                'c.allowed_credit',
                'c.is_withholding_agent',
                DB::raw("CONCAT(quotes.customer_name, ' #', quotes.quote_ref_no) AS text"),
                'quotes.*'
            )->with('quote_lines')
            ->get();

            return json_encode($quotes);
        }
    }

    /**
     * Get quote_lines for POS
     */
    public function getProductRow($quote_line_id, $variation_id, $location_id, $row_count)
    {
        $output = [];

        try {
            $business_id = request()->session()->get('user.business_id');
            $warehouse_id = request()->input('warehouse_id');
            $check_qty_available = request()->input('check_qty_available');
            $quote_line = QuoteLine::where('id', $quote_line_id)
                ->first();

            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id, $warehouse_id);
            if(empty($product)){
                $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, null, $warehouse_id);
                $quote_line->quantity = 'N/A';
                $product->enable_stock = 1;
            }
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);
            $product->quantity_ordered = $quote_line->quantity;
            $product->default_sell_price = $quote_line->unit_price_exc_tax;
            $product->sell_price_inc_tax = $quote_line->unit_price_inc_tax;
            $product->line_discount_type = $quote_line->discount_type;
            $product->line_discount_amount = $quote_line->discount_amount;

            /** Tax percent added */
            $product->tax_percent = $this->taxUtil->getTaxPercent($product->tax_id);

            $enabled_modules = $this->transactionUtil->allModulesEnabled();

            //Get lot number dropdown if enabled
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

            if (config('app.business') == 'workshop') {
                $product->service_parent_id = $quote_line->service_parent_id;
            }

            // Number of decimals in sales
            $business = Business::find($business_id);
            $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
            $decimals_in_sales = $product_settings['decimals_in_sales'];

            // Check if user is admin
            $user = User::find(request()->user()->id);
            $is_admin = $user->hasRole('Super Admin#' . $business_id);

            return view('sale_pos.product_row')
                ->with(compact(
                    'product',
                    'row_count',
                    'enabled_modules',
                    'pos_settings',
                    'is_admin',
                    'check_qty_available',
                    'decimals_in_sales'
                ))
                ->render();
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = __('lang_v1.item_out_of_stock');
        }

        return $output;
    }

    /**
     * Get quote_lines info
     * 
     * @param int $quote_id
     * 
     */
    public function getQuoteLines(){
        $quote_id = request()->input("quote_id", null);
        $warehouse_id = request()->input("warehouse_id", null);
        $variation_id = request()->input("variation_id", null);
        $selling_price_group_id = request()->input("selling_price_group_id", null);
        $tax_detail = request()->input("tax_detail", null);
        $tax_detail = $tax_detail == "yes" ? true : false;

        $quote_lines = Product::join("variations as v", "products.id", "v.product_id")
            ->with('variations.group_prices');

        if(!empty($quote_id)){
            $quote_lines->join("quote_lines as ql",
                function($join) use ($quote_id){
                    $join->on("v.id", "ql.variation_id")
                        ->where("ql.quote_id", $quote_id);
                }
            );
        }

        if(!empty($warehouse_id) && !empty($variation_id)){
            $quote_lines->where("v.id", $variation_id);

            $quote_lines->leftJoin("variation_location_details as vld",
                function($join) {
                    $join->on("v.id", "vld.variation_id");
                }
            );

            $quote_lines->where(function($query) use ($warehouse_id){
                $query->where("vld.warehouse_id", $warehouse_id)
                    ->orWhereNull("vld.warehouse_id");
            });
        }

        $quote_lines->select(
            "products.name as product_name",
            "products.tax as tax_id",
            "products.alert_quantity as tax_percent" //Used form save tax percent
        );

        if(!empty($quote_id)){
            $tax_detail = Quote::find($quote_id);
            if(!empty($tax_detail)){
                $tax_detail = $tax_detail->tax_detail;
            }
            $quote_lines->addSelect("ql.*");
            $quote_lines = $quote_lines->get();
        }

        if(!empty($warehouse_id) && !empty($variation_id)){
            $quote_lines->addSelect(
                "v.id as variation_id",
                "vld.warehouse_id",
                DB::raw("(vld.qty_available - qty_reserved) as qty_available"),
                "v.default_sell_price as unit_price_exc_tax",
                "v.sell_price_inc_tax as unit_price_inc_tax"
            )->groupBy("vld.variation_id");

            $quote_lines = $quote_lines->get();

            
            if (!empty($selling_price_group_id)) {
                foreach($quote_lines as $ql){
                    $variation_group_prices = $this->productUtil->getVariationGroupPrice($ql->variation_id, $selling_price_group_id, $ql->tax_percent);
                    
                    if (!empty($variation_group_prices['price_inc_tax'])) {
                        $ql->unit_price_inc_tax = $variation_group_prices['price_inc_tax'];
                        $ql->unit_price_exc_tax = $variation_group_prices['price_exc_tax'];
                    }
                }
            }
        }

        /** Add tax percent and group prices */
        foreach($quote_lines as $ql){
            $ql->tax_percent = $this->taxUtil->getTaxPercent($ql->tax_id);

            $sgp = VariationGroupPrice::join('selling_price_groups as spg', 'variation_group_prices.price_group_id', 'spg.id')
                ->where('variation_group_prices.variation_id', $ql->variation_id)
                ->select(
                    'variation_group_prices.price_group_id',
                    'spg.name as price_group',
                    'variation_group_prices.price_inc_tax'
                )->get();
            
            if(!empty($sgp)){
                foreach($sgp as $g){
                    $item = collect([
                        'price_group' => $g->price_group,
                        'price_inc_tax' => $g->price_inc_tax
                    ]);

                    $ql->variations->push($item);
                }
            }
        }

        $discount_types = [
            "fixed" => __("lang_v1.fixed"),
            "percentage" => __("lang_v1.percentage")
        ];

        return view("order.partials.product_row")
            ->with(compact("quote_lines", "discount_types", "tax_detail"));
    }

    /**
     * Mark a order as prepared
     * @param int $id
     * 
     */
    public function changeOrderStatus($id, $employee_id = null){
        if(!auth()->user()->can("order.update")){
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $order = Quote::where("id", $id)
                ->where("business_id", $business_id)
                ->first();

            if(!empty($order)){
                switch($order->status){
                    case "opened":
                        $order->status = "in_preparation";
                        break;
                    case "in_preparation":
                        $order->status = "prepared";
                        break;
                    case "prepared":
                        $order->status = "on_route";
                        break;
                    case "on_route":
                        $order->status = "closed";
                        break;
                }

                $order->save();

                $output = ['success' => 1,
                            'msg' => trans("order.status_order_updated")
                        ];
            } else {
                $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Retrives fresh orders
     * @return Json $output
     */
    public function refreshOrdersList()
    {
        if (!auth()->user()->can('order.view')) {
             abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $customer_id = request()->input("customer", null);
        $status = request()->input("status", null);
        $delivery_type = request()->input("delivery_type", null);
        $seller_id = request()->input("seller", null);
        $start_date = request()->input('start_date', null);
        $end_date = request()->input('end_date', null);

        $orders = Quote::join("customers as c", "quotes.customer_id", "c.id")
            ->leftJoin("document_types as dc", "quotes.document_type_id", "dc.id")
            ->leftJoin("employees as e", "quotes.employee_id", "e.id")
            ->where("quotes.business_id", $business_id)
            ->where("quotes.type", "order")
            ->select(
                "quotes.*",
                "c.name as customer_real_name",
                "dc.document_name",
                DB::raw("CONCAT(e.first_name, ' ', e.last_name) as seller_name"),
                DB::raw("e.first_name as seller_only_name")
            )
            ->orderBy("quotes.delivery_date", "desc")
            ->orderBy("quotes.id", "desc");
        
        /** filter business location permitted */
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $orders->whereIn('quotes.location_id', $permitted_locations);
        }

        /** Apply filters */
        if(!empty($customer_id)){
            $orders->where("quotes.customer_id", $customer_id);
        }
        if(!empty($status)){
            $orders->where("quotes.status", $status);
        }
        if(!empty($delivery_type)){
            $orders->where("quotes.delivery_type", $delivery_type);
        }
        if(!empty($seller_id)){
            $orders->where("quotes.employee_id", $seller_id);
        }
        if (!is_null($start_date) && !is_null($end_date)) {
            $orders->whereBetween('quotes.delivery_date', [$start_date, $end_date]);
        }

        $orders = $orders->get();

        return view('order.partials.show_orders', compact('orders'));
    }

    /**
     * Gets employees for in charge person
     * 
     */
    public function getInChargePeople(){
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");

            $employees = Employees::where("business_id", $business_id)
                ->where("position_id", 15) //Inventario y bodega
                ->select(
                    "id",
                    DB::raw("CONCAT(first_name, ' ', last_name) as name")
                )->pluck("name", "id");
            
            return json_encode($employees);
        }
    }
    public function orderPlannerReport(Request $request){
        if (!auth()->user()->can('order.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get("user.business_id");
        $business = Business::where('id', $business_id)->first();
        $type = $request->input("report_type", null);
        $start_date = $request->input("start_date", null);
        $end_date = $request->input("end_date", null);
        $status = $request->input("status", null);
        $delivery_type = $request->input("delivery_type", null);
        $customer = $request->input("customer", null);
        $seller = $request->input("seller", null);
        
        /** Gets transactions from stored procedure */
        $quote_trans =  collect(DB::select('CALL getQuotesTransactions(?,?,?)', array($start_date, $end_date, $business_id)));

        /** Apply filters */
        if(!is_null($customer) || !empty($customer)){
            $quote_trans = $quote_trans->where('customer_id', $customer);
        }
        if(!is_null($status) || !empty($status)){
            $quote_trans = $quote_trans->where('status', $status);
        }
        if(!is_null($delivery_type) || !empty($delivery_type)){
            $quote_trans = $quote_trans->where('delivery_type', $delivery_type);
        }
        if(!is_null($seller) || !empty($seller)){
            $quote_trans = $quote_trans->where('seller_id', $seller);
        }

        $initial_date = \Carbon::parse($start_date);
		$final_date = \Carbon::parse($end_date);
		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));
        $initial_month = $months[($initial_date->format('n')) - 1];
        $final_month = $months[($final_date->format('n')) - 1];
        $initial_year = $initial_date->format('Y');
        $final_year = $final_date->format('Y');

        /*return view("reports.orders_dispatch_report_pdf")
            ->with(compact('quote_trans', 'initial_month', 'initial_date', 'final_date', 'final_month', 'initial_year', 'final_year', 'business'));
        */
        if($type == 'pdf'){
            $pdf = \PDF::loadView('reports.orders_dispatch_report_pdf',
                compact('quote_trans', 'initial_month', 'initial_date', 'final_date', 'final_month', 'initial_year', 'final_year', 'business')
            );
            $pdf->setPaper('A2', 'landscape');
            return $pdf->stream(__('report.dispatch_report') . '.pdf');
        }else{
            return Excel::download(new OrderTransactionExport($quote_trans, $business, $initial_date, $final_date, $months),
                __('report.dispatch_report').'.xlsx');
        }
    }

    /**
     * Add spares to service block.
     * 
     * @param  int  $variation_id
     * @return json
     */
    public function addSpare($variation_id)
    {
        $quote_id = request()->input('quote_id', null);
        $warehouse_id = request()->input('warehouse_id', '');
        $variation_id = request()->input('variation_id', null);
        $selling_price_group_id = request()->input('selling_price_group_id', null);
        $tax_detail = request()->input('tax_detail', null);
        $tax_detail = $tax_detail == 'yes' ? true : false;
        $service_block_index = request()->input('service_block_index');
        $row_index = request()->input('row_index');
        $service_parent_id = request()->input('service_parent_id');

        $product_q = DB::table('variations as variation')
            ->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
            ->leftJoin('variation_location_details as VLD', 'VLD.variation_id', '=', 'variation.id')
            ->where('variation.id', $variation_id)
            ->where('VLD.warehouse_id', $warehouse_id)
            ->select(
                'variation.id as variation_id',
                'product.type as type_product', 
                'product.name as name_product',
                'variation.name as name_variation',
                'product.sku as sku', 
                'variation.sub_sku as sub_sku',
                'VLD.qty_available',
                'product.tax as tax_id',
                'product.alert_quantity as tax_percent', 
                'variation.default_sell_price as unit_price_exc_tax',
                'variation.sell_price_inc_tax as unit_price_inc_tax',
                'variation.default_sell_price as price'
            )
            ->first();

        if (! empty($selling_price_group_id) || ! is_null($selling_price_group_id)) {
            $variation_group_prices = $this->productUtil->getVariationGroupPrice(
                    $variation_id,
                    $selling_price_group_id,
                    $product_q->tax_percent
                );

            if (! empty($variation_group_prices['price_inc_tax'])) {
                $product_q->unit_price_inc_tax = $variation_group_prices['price_inc_tax'];
                $product_q->unit_price_exc_tax = $variation_group_prices['price_exc_tax'];
            }
        }
    
        $product = array(
            'variation_id' => $product_q->variation_id,
            'type_product' => $product_q->type_product,
            'name_product' => $product_q->name_product,
            'name_variation' => $product_q->name_variation,
            'sku' => $product_q->sku,
            'sub_sku' => $product_q->sub_sku,
            'price' => (empty($selling_price_group_id) || is_null($selling_price_group_id)) ? $product_q->price : $product_q->unit_price_exc_tax,
            'price_inc_tax' => $product_q->unit_price_inc_tax,
            'qty_available' => $product_q->qty_available,
            'tax_percent' => $this->taxUtil->getTaxPercent($product_q->tax_id),
            'quantity' => 1
        );

        $group_prices = Variation::where('id', $variation_id)
            ->with('group_prices');

        $output = [
            'success' => 1,
            'product' => $product,
            'service_block_index' => $service_block_index,
            'row_index' => $row_index,
            'service_parent_id' => $service_parent_id
        ];

        $output['html_content'] = view('quote.partials.spare_row', compact(
                'product',
                'warehouse_id',
                'tax_detail',
                'service_block_index',
                'row_index',
                'service_parent_id',
                'group_prices'
            ))
            ->render();

        return $output;
    }

    /**
     * Get quote_lines info.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getSpareLines()
    {
        $quote_id = request()->input("quote_id", null);
        $warehouse_id = request()->input("warehouse_id", null);
        $variation_id = request()->input("variation_id", null);
        $selling_price_group_id = request()->input("selling_price_group_id", null);
        $tax_detail = request()->input("tax_detail", null);
        $tax_detail = $tax_detail == "yes" ? true : false;
        $service_block_index = request()->input('service_block_index');
        $service_parent_id = request()->input('service_parent_id', null);

        $quote_lines = Product::join("variations as v", "products.id", "v.product_id")
            ->with('variations.group_prices');

        if (! empty($quote_id)) {
            $quote_lines->join("quote_lines as ql",
                function ($join) use ($quote_id, $service_parent_id) {
                    $join->on("v.id", "ql.variation_id")
                        ->where("ql.quote_id", $quote_id);
                }
            );

            $quote_lines->where("ql.service_parent_id", $service_parent_id);
        }

        if (! empty($warehouse_id) && ! empty($variation_id)) {
            $quote_lines->where("v.id", $variation_id);

            $quote_lines->leftJoin("variation_location_details as vld",
                function ($join) {
                    $join->on("v.id", "vld.variation_id");
                }
            );

            $quote_lines->where(function ($query) use ($warehouse_id) {
                $query->where("vld.warehouse_id", $warehouse_id)
                    ->orWhereNull("vld.warehouse_id");
            });
        }

        $quote_lines->select(
            "products.name as product_name",
            "products.tax as tax_id",
            "products.alert_quantity as tax_percent"
        );

        if (! empty($quote_id)) {
            $tax_detail = Quote::find($quote_id);

            if (! empty($tax_detail)) {
                $tax_detail = $tax_detail->tax_detail;
            }

            $quote_lines->addSelect("ql.*");

            $quote_lines = $quote_lines->get();
        }

        if (! empty($warehouse_id) && ! empty($variation_id)) {
            $quote_lines->addSelect(
                    "v.id as variation_id",
                    "vld.warehouse_id",
                    "v.default_sell_price as unit_price_exc_tax",
                    "v.sell_price_inc_tax as unit_price_inc_tax"
                )
                ->groupBy("vld.variation_id");

            $quote_lines = $quote_lines->get();

            if (! empty($selling_price_group_id)) {
                foreach ($quote_lines as $ql) {
                    $variation_group_prices = $this->productUtil->getVariationGroupPrice(
                        $ql->variation_id,
                        $selling_price_group_id,
                        $ql->tax_percent
                    );
                    
                    if (! empty($variation_group_prices['price_inc_tax'])) {
                        $ql->unit_price_inc_tax = $variation_group_prices['price_inc_tax'];
                        $ql->unit_price_exc_tax = $variation_group_prices['price_exc_tax'];
                    }
                }
            }
        }

        // Add tax percent and group prices
        foreach ($quote_lines as $ql) {
            $ql->tax_percent = $this->taxUtil->getTaxPercent($ql->tax_id);

            $sgp = VariationGroupPrice::join('selling_price_groups as spg', 'variation_group_prices.price_group_id', 'spg.id')
                ->where('variation_group_prices.variation_id', $ql->variation_id)
                ->select(
                    'variation_group_prices.price_group_id',
                    'spg.name as price_group',
                    'variation_group_prices.price_inc_tax'
                )->get();
            
            if (! empty($sgp)) {
                foreach ($sgp as $g) {
                    $item = collect([
                        'price_group' => $g->price_group,
                        'price_inc_tax' => $g->price_inc_tax
                    ]);

                    $ql->variations->push($item);
                }
            }
        }

        $discount_types = [
            "fixed" => __("lang_v1.fixed"),
            "percentage" => __("lang_v1.percentage")
        ];

        return view("order.partials.spare_row")
            ->with(compact(
                "quote_lines",
                "discount_types",
                "tax_detail",
                "service_block_index",
                "service_parent_id"
            ));
    }
}
