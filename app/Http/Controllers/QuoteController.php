<?php

namespace App\Http\Controllers;

use DB;
use View;
use Excel;
use App\Quote;
use Validator;
use App\Reason;
use DataTables;
use App\Contact;
use App\Business;
use App\Customer;
use App\LostSale;
use App\Employees;
use App\QuoteLine;
use App\Warehouse;
use Carbon\Carbon;
use App\DocumentType;

use App\Utils\TaxUtil;
use App\Utils\TransactionUtil;

use App\BusinessLocation;
use App\CustomerVehicle;
use App\SellingPriceGroup;
use App\Utils\ProductUtil;
use App\Exports\QuoteExport;
use App\Utils\EmployeeUtil;
use App\Variation;
use App\VariationLocationDetails;
use Illuminate\Http\Request;

class QuoteController extends Controller
{

    protected $taxUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $employeeUtil;

    public function __construct(TaxUtil $taxUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, EmployeeUtil $employeeUtil){
        $this->taxUtil = $taxUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->employeeUtil = $employeeUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        if(!auth()->user()->can('quotes.view')){
            abort(403, 'Unauthorized action.');
        }
        return view("quote.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('quotes.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $last_correlative = DB::table('quotes')
            ->select(DB::raw('MAX(id) as max'))
            ->first();

        if ($last_correlative->max != null) {
            $correlative = $last_correlative->max + 1;
        } else {
            $correlative = 1;
        }

        $cont = str_pad($correlative, 5, "0", STR_PAD_LEFT);
        
        $correlative = "" . $business->quote_prefix . "" . $cont . "";
        
        $documents =  DocumentType::where('business_id',$business_id)
            ->where('is_active', 1)
            ->pluck('document_name', 'id');
        
        $customers = DB::table('customers as customer')
            ->leftJoin('customer_portfolios as portfolio', 'portfolio.id', '=', 'customer.customer_portfolio_id')
            ->leftJoin('employees as employee', 'employee.id', '=', 'portfolio.seller_id')
            ->select('customer.*', 'employee.id as employee_id')
            ->where('customer.business_id', $business_id)
            ->get();

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);

        $employees = Employees::SellersDropdown($business_id, true);

        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $payment_condition = ['cash' => __('quote.cash'), 'credit' => __('lang_v1.credit')];

        $tax_detail = ['0' => __('messages.no'), '1' => __('messages.yes')];

        $warehouses = Warehouse::select('id', 'name')
            ->where('status', 'active')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        /** Prices list */
        $prices_group = SellingPriceGroup::forDropdown($business_id);

        $service_blocks = [];

        $html = View::make('quote.create', compact(
                'documents',
                'payment_condition',
                'tax_detail', 
                'customers',
                'correlative',
                'bl_attributes',
                'business_locations' ,
                'warehouses',
                'employees',
                'prices_group',
                'service_blocks'
            ))
            ->render();

        return $html;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('quotes.create')) {
            abort(403, 'Unauthorized action.');
        }

        $validateData = $request->validate([
            'employee_id' => 'required',
            'document_type_id' => 'required',
            'quote_date' => 'required',
            'quote_ref_no' => 'required',
            'customer_name' => 'required',
            'payment_condition' => 'required',
            'tax_detail' => 'required',
            'discount_type' => 'required',
            'total_before_tax' => 'required',
            'tax_amount' => 'required',
            'total_final' => 'required',
        ]);

        try {
            $quote_details = $request->only([
                'customer_id',
                'employee_id',
                'document_type_id',
                'quote_date',
                'customer_name',
                'contact_name',
                'email',
                'mobile',
                'address',
                'payment_condition',
                'tax_detail',
                'validity',
                'delivery_time',
                'note',
                'terms_conditions',
                'discount_type',
                'discount_amount',
                'total_before_tax',
                'tax_amount',
                'total_final',
                'price_group_id',
            ]);

            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            $validity = $business->quote_validity;

            $quote_date = $this->taxUtil->uf_date($request->quote_date);
            $due_date = Carbon::parse($quote_date);
            $due_date->addDays($validity);
            $due_date = $due_date->format('Y-m-d');

            $last_correlative = DB::table('quotes')
                ->select(DB::raw('MAX(id) as max'))
                ->first();

            if ($last_correlative->max != null) {
                $correlative = $last_correlative->max + 1;
            } else {
                $correlative = 1;
            }

            $cont = str_pad($correlative, 5, "0", STR_PAD_LEFT);

            $correlative = "" . $business->quote_prefix . "" . $cont . "";

            $quote_details['quote_ref_no'] = $correlative;
            $quote_details['business_id'] = $business_id;
            $quote_details['user_id'] = $request->session()->get('user.id');
            $quote_details['created_by'] = $request->session()->get('user.id');
            $quote_details['due_date'] = $due_date;
            $quote_details['status'] = "opened";
            $quote_details['quote_date'] = $quote_date;
            $quote_details['selling_price_group_id'] = isset($quote_details['price_group_id']) ? ($quote_details['price_group_id'] == 0 ? null : $quote_details['price_group_id']) : null;
            $quote_details['discount_amount'] = $this->productUtil->num_uf($quote_details['discount_amount']);
            $quote_details['total_before_tax'] = $this->productUtil->num_uf($quote_details['total_before_tax']);
            $quote_details['tax_amount'] = $this->productUtil->num_uf($quote_details['tax_amount']);
            $quote_details['total_final'] = $this->productUtil->num_uf($quote_details['total_final']);

            if (config('app.business') == 'workshop') {
                $quote_details['customer_vehicle_id'] = $request->input('customer_vehicle_id');

                $service_parent_id = $request->input('service_parent_id');
                $note_line = $request->input('note_line');
            }

            DB::beginTransaction();

            $quote = Quote::create($quote_details);

            $variation_id = $request->input('variation_id');
            $warehouse_id = $request->input('line_warehouse_id');
            $quantity = $request->input('quantity');
            $unit_price_exc_tax = $request->input('unit_price_exc_tax');
            $unit_price_inc_tax = $request->input('unit_price_inc_tax');
            $discount_type = $request->input('line_discount_type');
            $discount_amount = $request->input('line_discount_amount');
            $tax_amount = $request->input('line_tax_amount');

            if (!empty($variation_id)) {
                $cont = 0;

                while ($cont < count($variation_id)) {
                    $detail = new QuoteLine;

                    $detail->quote_id = $quote->id;
                    $detail->variation_id = $variation_id[$cont];
                    $detail->warehouse_id = $warehouse_id[$cont];
                    $detail->quantity = $this->productUtil->num_uf($quantity[$cont]);
                    $detail->unit_price_exc_tax = $this->productUtil->num_uf($unit_price_exc_tax[$cont]);
                    $detail->unit_price_inc_tax = $this->productUtil->num_uf($unit_price_inc_tax[$cont]);
                    $detail->discount_type = $discount_type[$cont];

                    $discount_amount_ = $this->productUtil->num_uf($discount_amount[$cont]);
                    $unit_price_inc_tax_ = $this->productUtil->num_uf($unit_price_inc_tax[$cont]);
                    $unit_price_exc_tax_ = $this->productUtil->num_uf($unit_price_exc_tax[$cont]);
                    $tax_amount_ = $this->productUtil->num_uf($tax_amount[$cont]);
                    $quantity_ = $this->productUtil->num_uf($quantity[$cont]);

                    if ($discount_amount != null) {
                        $detail->discount_amount = $discount_amount_;
                        $tax_percent = $unit_price_inc_tax_ / $unit_price_exc_tax_;
                        $new_price_inc_tax = $unit_price_inc_tax_ - $discount_amount_;
                        $new_price_exc_tax = $new_price_inc_tax / $tax_percent;
                        $detail->tax_amount = ($new_price_inc_tax - $new_price_exc_tax) * $quantity_;

                    } else {
                        $detail->discount_amount = 0.00;
                        $detail->tax_amount = $tax_amount_;
                    }

                    if (config('app.business') == 'workshop') {
                        $detail->service_parent_id = $service_parent_id[$cont] > 0 ? $service_parent_id[$cont] : null;
                        $detail->note = $note_line[$cont];
                    }
                    
                    $detail->save();

                    $cont = $cont + 1;
                } 
            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __("quote.added_success")
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

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
     * @param  \App\Quote  $quotes
     * @return \Illuminate\Http\Response
     */
    public function show(Quote $quotes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Quote  $quotes
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('quotes.update')) {
            abort(403, 'Unauthorized action.');
        }

        $quote = Quote::where('id', $id)->first();

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $documents =  DocumentType::where('business_id',$business_id)
            ->where('is_active', 1)
            ->pluck('document_name', 'id');

        $customers = DB::table('customers as customer')
            ->leftJoin('customer_portfolios as portfolio', 'portfolio.id', '=', 'customer.customer_portfolio_id')
            ->leftJoin('employees as employee', 'employee.id', '=', 'portfolio.seller_id')
            ->select('customer.*', 'employee.id as employee_id')
            ->where('customer.business_id', $business_id)
            ->get();

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);

        $employees = Employees::SellersDropdown($business_id, true);

        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $payment_condition = ['cash' => __('quote.cash'), 'credit' => __('lang_v1.credit')];

        $tax_detail = ['0' => __('messages.no'), '1' => __('messages.yes')];

        $warehouses = Warehouse::select('id', 'name')
            ->where('status', 'active')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $prices_group = SellingPriceGroup::forDropdown($business_id);

        if (config('app.business') == 'workshop') {
            $customer_vehicles = CustomerVehicle::where('customer_id', $quote->customer_id)
                ->select(
                    DB::raw("CONCAT(COALESCE(customer_vehicles.license_plate, ''), ' - ', COALESCE(customer_vehicles.model, ''), ' ', COALESCE(customer_vehicles.year, ''), ' ', COALESCE(customer_vehicles.color, '')) as name"),
                    'customer_vehicles.id'
                )
                ->pluck('name', 'id');

            $service_block_q = DB::table('quote_lines as line')
                ->join('variations as variation', 'variation.id', '=', 'line.variation_id')
                ->join('products as product', 'product.id', '=', 'variation.product_id')
                ->select(
                    'line.*',
                    'product.name as name_product',
                    'variation.name as name_variation',
                    'product.sku as sku',
                    'variation.sub_sku as sub_sku',
                    'product.tax as tax_id',
                    'product.type as type_product'
                )
                ->whereNull('line.service_parent_id')
                ->where('line.quote_id', $id)
                ->orderBy('line.id', 'asc')
                ->get();
    
            $service_blocks = array();
    
            foreach ($service_block_q as $item) {
                $service_block_array = array(
                    'quote_line_id' => $item->id,
                    'quote_id' => $item->quote_id,
                    'variation_id'=> $item->variation_id,
                    'warehouse_id' => $item->warehouse_id,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price_exc_tax,
                    'price_inc_tax' => $item->unit_price_inc_tax,
                    'discount_type' => $item->discount_type,
                    'discount_amount' => $item->discount_amount,
                    'tax_amount' => $item->tax_amount,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'name_product' => $item->name_product,
                    'name_variation' => $item->name_variation,
                    'sku' => $item->sku,
                    'sub_sku' => $item->sub_sku,
                    'tax_percent' => $this->taxUtil->getTaxPercent($item->tax_id),
                    'service_parent_id' => 0,
                    'type_product' => $item->type_product,
                    'note' => $item->note
                );
    
                $spare_rows_q = DB::table('quote_lines as line')
                    ->join('variations as variation', 'variation.id', '=', 'line.variation_id')
                    ->join('products as product', 'product.id', '=', 'variation.product_id')
                    ->select(
                        'line.*',
                        'product.name as name_product',
                        'variation.name as name_variation',
                        'product.sku as sku',
                        'variation.sub_sku as sub_sku',
                        'product.tax as tax_id',
                        'product.type as type_product',
                    )
                    ->where('line.service_parent_id', $item->variation_id)
                    ->where('line.quote_id', $id)
                    ->orderBy('line.id', 'asc')
                    ->get();
    
                $spare_rows = array();
    
                foreach ($spare_rows_q as $spare) {
                    $spare_row_array = array(
                        'quote_line_id' => $spare->id,
                        'quote_id' => $spare->quote_id,
                        'variation_id'=> $spare->variation_id,
                        'warehouse_id' => $spare->warehouse_id,
                        'quantity' => $spare->quantity,
                        'price' => $spare->unit_price_exc_tax,
                        'price_inc_tax' => $spare->unit_price_inc_tax,
                        'discount_type' => $spare->discount_type,
                        'discount_amount' => $spare->discount_amount,
                        'tax_amount' => $spare->tax_amount,
                        'created_at' => $spare->created_at,
                        'updated_at' => $spare->updated_at,
                        'name_product' => $spare->name_product,
                        'name_variation' => $spare->name_variation,
                        'sku' => $spare->sku,
                        'sub_sku' => $spare->sub_sku,
                        'tax_percent' => $this->taxUtil->getTaxPercent($spare->tax_id),
                        'service_parent_id' => $spare->service_parent_id,
                        'type_product' => $spare->type_product,
                        'note' => $item->note
                    );
    
                    array_push($spare_rows, $spare_row_array);
                }
    
                $service_block_array['spare_rows'] = $spare_rows;
    
                array_push($service_blocks, $service_block_array);
            }

            $row_index_count = QuoteLine::where('quote_id', $id)->count();

        } else {
            $customer_vehicles = [];
            $service_blocks = [];
            $row_index_count = null;
        }

        $html = View::make('quote.edit', compact(
                'quote',
                'documents',
                'payment_condition',
                'tax_detail', 
                'customers',
                'bl_attributes',
                'business_locations',
                'warehouses',
                'employees',
                'prices_group',
                'customer_vehicles',
                'service_blocks',
                'row_index_count'
            ))
            ->render();

        return $html;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Quote  $quotes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('quotes.update')) {
            abort(403, 'Unauthorized action.');
        }

        $validateData = $request->validate([
            //'customer_id' => 'required',
            'employee_id' => 'required',
            'document_type_id' => 'required',
            'quote_date' => 'required',
            'customer_name' => 'required',
            'contact_name' => 'required',
            //'email' => 'required',
            //'mobile' => 'required',
            'address' => 'required',
            'payment_condition' => 'required',
            'tax_detail' => 'required',
            'validity' => 'required',
            'delivery_time' => 'required',
            'note' => 'required',
            //'legend' => 'required',
            'terms_conditions' => 'required',
            'discount_type' => 'required',
            'total_before_tax' => 'required',
            'tax_amount' => 'required',
            'total_final' => 'required',
        ]);

        try {
            $quote_details = $request->only([
                'customer_id',
                'employee_id',
                'document_type_id',
                'quote_date',
                'customer_name',
                'contact_name',
                'email',
                'mobile',
                'address',
                'payment_condition',
                'tax_detail',
                'validity',
                'delivery_time',
                'note',
                'terms_conditions',
                'discount_type',
                'discount_amount',
                'total_before_tax',
                'tax_amount',
                'total_final',
                'price_group_id'
            ]);

            $quote = Quote::findOrFail($id);

            $quote_details['user_id'] = $request->session()->get('user.id');

            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            $validity = $business->quote_validity;
    
            $quote_date = $this->taxUtil->uf_date($request->quote_date);
            $due_date = Carbon::parse($quote_date);
            $due_date->addDays($validity);
            $actual_date = Carbon::now();

            if ($actual_date->greaterThan($due_date)) {
                $quote_details['status'] = 'expired';
            }

            $due_date = $due_date->format('Y-m-d');
            $actual_date = $actual_date->format('Y-m-d');

            $quote_details['discount_amount'] = $this->productUtil->num_uf($quote_details['discount_amount']);
            $quote_details['total_before_tax'] = $this->productUtil->num_uf($quote_details['total_before_tax']);
            $quote_details['tax_amount'] = $this->productUtil->num_uf($quote_details['tax_amount']);
            $quote_details['total_final'] = $this->productUtil->num_uf($quote_details['total_final']);
            $quote_details['quote_date'] = $quote_date;
            $quote_details['selling_price_group_id'] = $quote_details['price_group_id'] == 0 ? null : $quote_details['price_group_id'];
            $quote_details['due_date'] = $due_date;

            if (config('app.business') == 'workshop') {
                $quote_details['customer_vehicle_id'] = $request->input('customer_vehicle_id');

                $service_parent_id = $request->input('service_parent_id');
                $note_line = $request->input('note_line');
            }

            DB::beginTransaction();

            $quote->update($quote_details);

            $variation_id = $request->input('variation_id');
            $warehouse_id = $request->input('line_warehouse_id');
            $quantity = $request->input('quantity');
            $unit_price_exc_tax = $request->input('unit_price_exc_tax');
            $unit_price_inc_tax = $request->input('unit_price_inc_tax');
            $discount_type = $request->input('line_discount_type');
            $discount_amount = $request->input('line_discount_amount');
            $tax_amount = $request->input('line_tax_amount');

            QuoteLine::where('quote_id', $id)->forceDelete();

            if (!empty($variation_id)) {
                $cont = 0;   
             
                while($cont < count($variation_id)) {
                    $detail = new QuoteLine;

                    $detail->quote_id = $quote->id;
                    $detail->variation_id = $variation_id[$cont];

                    if ($warehouse_id[$cont] != "null") {
                        $detail->warehouse_id = $warehouse_id[$cont];
                    }

                    $quantity_ = $this->productUtil->num_uf($quantity[$cont]);
                    $unit_price_exc_tax_ = $this->productUtil->num_uf($unit_price_exc_tax[$cont]);
                    $unit_price_inc_tax_ = $this->productUtil->num_uf($unit_price_inc_tax[$cont]);
                    $tax_amount_ = $this->productUtil->num_uf($tax_amount[$cont]);
                    $discount_amount_ = $this->productUtil->num_uf($discount_amount[$cont]);

                    $detail->quantity = $quantity_;
                    $detail->unit_price_exc_tax = $unit_price_exc_tax_;
                    $detail->unit_price_inc_tax = $unit_price_inc_tax_;
                    $detail->discount_type = $discount_type[$cont];

                    if ($discount_amount != null) {
                        $detail->discount_amount = $discount_amount_;
                        $tax_percent = $unit_price_exc_tax_ != 0 ? $unit_price_inc_tax_ / $unit_price_exc_tax_ : 0;
                        $new_price_inc_tax = $unit_price_inc_tax_ - $discount_amount_;
                        $new_price_exc_tax = $tax_percent != 0 ? $new_price_inc_tax / $tax_percent : 0;
                        $detail->tax_amount = ($new_price_inc_tax - $new_price_exc_tax) * $quantity_;

                    } else {
                        $detail->discount_amount = 0.00;
                        $detail->tax_amount = $tax_amount_;
                    }

                    if (config('app.business') == 'workshop') {
                        $detail->service_parent_id = $service_parent_id[$cont] > 0 ? $service_parent_id[$cont] : null;
                        $detail->note = $note_line[$cont];
                    }

                    $detail->save();

                    $cont = $cont + 1;
                } 
            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __("quote.updated_success")
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Quote  $quotes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('quotes.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try{

                $quote = Quote::findOrFail($id);
                $quote->forceDelete();
                $output = [
                    'success' => true,
                    'msg' => __('quote.deleted_success')
                ];
                
            }
            catch (\Exception $e){
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
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
    public function getQuotes() {
        if (request()->ajax()) {
            $term = request()->input('q', '');

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $quotes = Quote::join("customers as c", "quotes.customer_id", "c.id")
                ->where('quotes.business_id', $business_id)
                ->where('quotes.type', "quote");

            if (!empty($term)) {
                $quotes->where(function ($query) use ($term) {
                    $query->where('quotes.customer_name', 'like', '%' . $term .'%')
                        ->orWhere("quotes.quote_ref_no", 'like', '%' . $term . '%');
                });
            }

            $quotes = $quotes->select(
                    'c.id as customer_id',
                    'c.name as c_name',
                    'c.is_exempt',
                    DB::raw("CONCAT(quotes.customer_name, ' #', quotes.quote_ref_no) AS text"),
                    'quotes.*'
                )
                ->get();

            foreach ($quotes as $qt) {
                $delivery_address = Quote::where("business_id", $business_id)
                    ->where("type", "order") 
                    ->where("status", "closed")
                    ->where("customer_id", $qt->customer_id)
                    ->orderBy("quote_date", "asc")
                    ->get()
                    ->last();

                if (! empty($delivery_address)) {
                    $qt->address = $delivery_address->address;
                }

                if (config('app.business') == 'workshop') {
                    $customer_vehicles = CustomerVehicle::where('customer_id', $qt->customer_id)
                        ->select(
                            DB::raw("CONCAT(COALESCE(customer_vehicles.license_plate, ''), ' - ', COALESCE(customer_vehicles.model, ''), ' ', COALESCE(customer_vehicles.year, ''), ' ', COALESCE(customer_vehicles.color, '')) as name"),
                            'customer_vehicles.id'
                        )
                        ->get();
    
                    if (! empty($customer_vehicles)) {
                        $qt->customer_vehicles = $customer_vehicles;
                    }

                    $service_blocks = QuoteLine::where('quote_id', $qt->id)
                        ->whereNull('service_parent_id')
                        ->get();

                    if (! empty($service_blocks)) {
                        $qt->service_blocks = $service_blocks;
                    }
                }
            }

            return json_encode($quotes);
        }
    }

    public function addProduct($variation_id, $warehouse_id, $selling_price_group_id = null)
    {
        $product_q = DB::table('variations as variation')
        ->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
        ->leftJoin('variation_location_details as VLD', 'VLD.variation_id', '=', 'variation.id')
        ->where('variation.id', $variation_id)
        ->where('VLD.warehouse_id', $warehouse_id)
        ->select('variation.id as variation_id', 'product.type as type_product', 
            'product.name as name_product', 'variation.name as name_variation', 'product.sku as sku', 
            'variation.sub_sku as sub_sku', 'VLD.qty_available', 'product.tax as tax_id', 'product.alert_quantity as tax_percent', 
            "variation.default_sell_price as unit_price_exc_tax", "variation.sell_price_inc_tax as unit_price_inc_tax", 'variation.default_sell_price as price'
        )->first();

        if(!empty($selling_price_group_id) || !is_null($selling_price_group_id)){
        $variation_group_prices = $this->productUtil->getVariationGroupPrice($variation_id, $selling_price_group_id, $product_q->tax_percent); 
        if (!empty($variation_group_prices['price_inc_tax'])) {
                $product_q->unit_price_inc_tax = $variation_group_prices['price_inc_tax'];
                $product_q->unit_price_exc_tax = $variation_group_prices['price_exc_tax'];
            }
        }
    
        $product_array = array(
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
        );
        $product = json_decode(json_encode($product_array), FALSE);
        return response()->json($product);
    }

    public function addProductNotStock($variation_id)
    {
        $product_q = DB::table('variations as variation')
        ->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
        ->select('variation.id as variation_id', 'product.name as name_product', 'variation.name as name_variation', 'product.sku as sku', 'variation.sub_sku as sub_sku', 'variation.default_sell_price as price', 'variation.sell_price_inc_tax as price_inc_tax', 'product.tax as tax_id')
        ->where('variation.id', $variation_id)
        ->first();

        $product_array = array(
            'variation_id' => $product_q->variation_id,
            'name_product' => $product_q->name_product,
            'name_variation' => $product_q->name_variation,
            'sku' => $product_q->sku,
            'sub_sku' => $product_q->sub_sku,
            'price' => $product_q->price,
            'price_inc_tax' => $product_q->price_inc_tax,
            'tax_percent' => $this->taxUtil->getTaxPercent($product_q->tax_id),
        );
        $product = json_decode(json_encode ($product_array), FALSE);

        return response()->json($product);
    }

    public function getQuotesData()
    {
        $quotes = DB::table('quotes as quote')
        ->join('users as user', 'user.id', '=', 'quote.created_by')
        ->join('employees as employee', 'employee.id', '=', 'quote.employee_id')
        ->select('quote.*', 'employee.short_name as short_name')
        ->where('quote.type', 'quote');
        

        return DataTables::of($quotes)
        ->addColumn(
            'actions', function($row){
                $user_id = request()->session()->get('user.id');

                $html ='<div class="btn-group">
                <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                if (auth()->user()->can('quotes.view')) {
                    $html .= '<li><a href="#" onClick="viewQuote('.$row->id.')"><i class="fa fa-file-pdf-o"></i>PDF</a></li>';
                }

                if (config('app.business') != 'workshop') {
                    if (auth()->user()->can('quotes.view')) {
                        $html .= '<li><a href="#" onClick="excelQuote('.$row->id.')"><i class="fa fa-file-excel-o"></i>Excel</a></li>';
                    }
                }

                if ((auth()->user()->can('quotes.update')) && ($row->status == 'opened')) {
                    $html .= '<li><a href="#" data-id="' . $row->id . '" class="edit_quote_button"><i class="glyphicon glyphicon-edit"></i>'.__('messages.edit').'</a></li>';
                }
                if(auth()->user()->can('quotes.update') && ($row->type != 'order')){
                    $now = \Carbon::parse(now());
                    $due_date = \Carbon::parse($row->due_date);
                    if($now->gt($due_date)){
                        if(is_null($row->lost_sale_id)){
                            $html .= '<li><a href="#" data-href="' . action('QuoteController@createLostSale', [$row->id]) . '" class="add_lost_sale"><i class="fa fa-exclamation-triangle"></i> ' . __("Venta perdida") . '</a></li>';
                        }else{
                            $html .= '<li><a href="#" data-href="' . action('QuoteController@editLostSale', [$row->lost_sale_id]) . '" class="edit_lost_sale"><i class="fa fa-exclamation-triangle"></i> ' . __("Editar venta perdida") . '</a></li>';
                        }
                    }
                }

                if ((auth()->user()->can('quotes.delete')) && ($row->status == 'opened')) {
                    $html .= '<li><a href="#" onClick="deleteQuote('.$row->id.')"><i class="glyphicon glyphicon-trash"></i>'.__('messages.delete').'</a></li>';
                }

                $html .= '</ul></div>';
                return $html;
            })
        ->editColumn('lost_sale_id', function($row){
            $is_lost_sale_html = '';
            if(is_null($row->lost_sale_id)){
                $is_lost_sale_html = '<span style="color: #56B81F;">'.__('crm.no').'</span>';
            }else{
                $is_lost_sale_html = '<span style="color: #F3400C;">'.__('crm.yes').'</span> <i class="fa fa-exclamation-triangle" style="color:#F3400C;"></i>';
            }
            return $is_lost_sale_html;
        })
        ->rawColumns(['actions', 'lost_sale_id'])
        ->toJson();
    }

    public function getLinesByQuote($id)
    {
        $product_q = DB::table('quote_lines as line')
        ->join('variations as variation', 'variation.id', '=', 'line.variation_id')
        ->join('products as product', 'product.id', '=', 'variation.product_id')
        ->select('line.*', 'product.name as name_product', 'variation.name as name_variation', 'product.sku as sku', 'variation.sub_sku as sub_sku', 'product.tax as tax_id')
        ->where('line.quote_id', $id)
        ->orderBy('line.id', 'asc')
        ->get();

        $lines_array = array();

        foreach ($product_q as $item) {

            $product_array = array(
                "id" => $item->id,
                "quote_id" => $item->quote_id,
                "variation_id"=> $item->variation_id,
                "warehouse_id" => $item->warehouse_id,
                "quantity" => $item->quantity,
                "unit_price_exc_tax" => $item->unit_price_exc_tax,
                "unit_price_inc_tax" => $item->unit_price_inc_tax,
                "discount_type" => $item->discount_type,
                "discount_amount" => $item->discount_amount,
                "tax_amount" => $item->tax_amount,
                "created_at" => $item->created_at,
                "updated_at" => $item->updated_at,
                'name_product' => $item->name_product,
                'name_variation' => $item->name_variation,
                'sku' => $item->sku,
                'sub_sku' => $item->sub_sku,
                'tax_percent' => $this->taxUtil->getTaxPercent($item->tax_id),
            );

            array_push($lines_array, $product_array);

        }


        $lines = json_decode(json_encode ($lines_array), FALSE);

        return response()->json($lines);

    }

    public function viewQuote($id)
    {
        if (!auth()->user()->can('quotes.view')) {
            abort(403, 'Unauthorized action.');
        }

        $quote = DB::table('quotes as quote')
        ->join('employees as employee', 'employee.id', '=', 'quote.employee_id')
        ->select('quote.*', DB::raw('CONCAT(employee.first_name, " ", employee.last_name) as employee'), 'employee.short_name as short_name')
        ->where('quote.id', $id)
        ->first();

        $letters = $this->transactionUtil->getAmountLetters($quote->total_final);
        $letters2 = utf8_decode(strtolower($letters));
        $letters3 = substr($letters2, 0, 1);
        $letters4 = strtoupper($letters3);
        $letters5 = substr($letters2, 1);
        $value_letters = $letters4.$letters5;

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $legend = $business->quote_legend;

        $lines = DB::table('quote_lines as line')
        ->join('variations as variation', 'variation.id', '=', 'line.variation_id')
        ->join('products as product', 'product.id', '=', 'variation.product_id')
        ->select('line.*', 'product.name as name_product', 'variation.name as name_variation', 'product.sku as sku', 'variation.sub_sku as sub_sku', 'product.warranty as warranty')
        ->where('line.quote_id', $id)
        ->orderBy('line.id', 'asc')
        ->get();
        $quote_date = $this->employeeUtil->getDate($quote->quote_date, true);
        $customer_name = ucwords(strtolower($quote->customer_name));

        $pdf = \PDF::loadView('quote.view', compact('quote', 'business', 'quote_date', 'customer_name', 'lines', 'value_letters', 'legend'));
        return $pdf->stream('quote.pdf');
    }


    public function viewExcel($id)
    {
        if (!auth()->user()->can('quotes.view')) {
            abort(403, 'Unauthorized action.');
        }

        $quote = DB::table('quotes as quote')
        ->join('employees as employee', 'employee.id', '=', 'quote.employee_id')
        ->select('quote.*', DB::raw('CONCAT(employee.first_name, " ", employee.last_name) as employee'), 'employee.short_name as short_employee')
        ->where('quote.id', $id)
        ->first();

        $letters = $this->transactionUtil->getAmountLetters($quote->total_final);
        $letters2 = utf8_decode(strtolower($letters));
        $letters3 = substr($letters2, 0, 1);
        $letters4 = strtoupper($letters3);
        $letters5 = substr($letters2, 1);
        $value_letters = $letters4.$letters5;

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $legend = $business->quote_legend;

        $lines = DB::table('quote_lines as line')
        ->join('variations as variation', 'variation.id', '=', 'line.variation_id')
        ->join('products as product', 'product.id', '=', 'variation.product_id')
        ->select('line.*', 'product.name as name_product', 'variation.name as name_variation', 'product.sku as sku', 'variation.sub_sku as sub_sku', 'product.warranty as warranty')
        ->where('line.quote_id', $id)
        ->orderBy('line.id', 'asc')
        ->get();

        return Excel::download(new QuoteExport($quote, $lines, $value_letters, $legend), 'Quote.xlsx');
    }

    // create lost sale
    public function createLostSale($id){
        if (!auth()->user()->can('quotes.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = auth()->user()->business_id;
        $reasons = Reason::where('business_id', $business_id)->pluck('reason', 'id');
        $quote_id = $id;
        return view('quote.partials.lost_sale_create', compact('reasons', 'quote_id'));
    }

    public function editLostSale($id){
        if (!auth()->user()->can('quotes.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = auth()->user()->business_id;
        $reasons = Reason::where('business_id', $business_id)->pluck('reason', 'id');
        $lost_sale = LostSale::find($id);

        return view('quote.partials.lost_sale_edit', compact('reasons', 'lost_sale'));
    }

    // add lost sale
    public function storeLostSale(Request $request){
        if (!auth()->user()->can('quotes.create')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate(
            [
                'reason_id' => 'required',
                'comments' => 'required',
            ],
            [
                'reason_id.required' => trans('La razón es requerida'),
                'comments.required' => trans('La explicación es requerida'),
            ]
        );

        try {
            DB::beginTransaction();
            $business_id = auth()->user()->business_id;
            $lost_sale = new LostSale();
            
            $lost_sale->quote_id = $request->quote_id;
            $lost_sale->user_id = auth()->user()->id;
            $lost_sale->reason_id = $request->reason_id;
            $lost_sale->business_id = $business_id;
            $lost_sale->lost_date = now();
            $lost_sale->comments = $request->comments;

            // dd($lost_sale);
            $lost_sale->save();
            if($lost_sale){
                $quote = Quote::find($lost_sale->quote_id);
                $quote->lost_sale_id = $lost_sale->id;
                $quote->update();
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("informacion guardada correctamente"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;

    }
    //edit lost sale
    public function updateLostSale(Request $request, $id){
        if (!auth()->user()->can('quotes.update')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate(
            [
                'reason_id' => 'required',
                'comments' => 'required',
            ],
            [
                'reason_id.required' => trans('La razón es requerida'),
                'comments.required' => trans('La explicación es requerida'),
            ]
        );

        try {
            DB::beginTransaction();
            $lost_sale = LostSale::find($id);
            $lost_sale->reason_id = $request->reason_id;
            $lost_sale->comments = $request->comments;
            $lost_sale->update();
            DB::commit();
            
            $output = [
                'success' => true,
                'msg' => __("informacion actualizada correctamente"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }

    /**
     * Add service block to quote form.
     * 
     * @param  int  $id
     * @return array
     */
    public function addServiceBlock($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $selling_price_group_id = request()->input('selling_price_group_id', null);
            $warehouse_id = request()->input('warehouse_id', '');
            $tax_detail = request()->input('tax_detail', 0);
            $service_block_index = request()->input('service_block_index');
            $row_index = request()->input('row_index');
            $view = request()->input('view', 'quote');
            $quote_line_id = request()->input('quote_line_id', null);

            $service_q = Variation::leftJoin('products', 'products.id', '=', 'variations.product_id')
                ->where('variations.id', $id)
                ->select(
                    'variations.id as variation_id',
                    'products.type as type_product', 
                    'products.name as name_product',
                    'variations.name as name_variation',
                    'products.sku as sku', 
                    'variations.sub_sku as sub_sku',
                    'products.tax as tax_id',
                    'products.alert_quantity as tax_percent', 
                    'variations.default_sell_price as unit_price_exc_tax',
                    'variations.sell_price_inc_tax as unit_price_inc_tax',
                    'variations.default_sell_price as price'
                )
                ->first();

            if (! empty($selling_price_group_id) || ! is_null($selling_price_group_id)) {
                $variation_group_prices = $this->productUtil->getVariationGroupPrice($id, $selling_price_group_id, $service_q->tax_percent); 
                
                if (! empty($variation_group_prices['price_inc_tax'])) {
                    $service_q->unit_price_inc_tax = $variation_group_prices['price_inc_tax'];
                    $service_q->unit_price_exc_tax = $variation_group_prices['price_exc_tax'];
                }
            }

            $service_block = array(
                'variation_id' => $service_q->variation_id,
                'type_product' => $service_q->type_product,
                'name_product' => $service_q->name_product,
                'name_variation' => $service_q->name_variation,
                'sku' => $service_q->sku,
                'sub_sku' => $service_q->sub_sku,
                'price' => (empty($selling_price_group_id) || is_null($selling_price_group_id)) ? $service_q->price : $service_q->unit_price_exc_tax,
                'price_inc_tax' => $service_q->unit_price_inc_tax,
                'qty_available' => $service_q->qty_available,
                'tax_percent' => $this->taxUtil->getTaxPercent($service_q->tax_id),
                'spare_rows' => []
            );

            if (! empty($quote_line_id)) {
                $service_block['quote_line_id'] = $quote_line_id;
                $service_block['note'] = QuoteLine::find($quote_line_id)->note;
            }

            $output = [
                'success' => 1,
                'block' => [],
                'service_block_index' => $service_block_index,
                'service_id' => $id
            ];

            $output['html_content'] = view($view . '.partials.service_block', compact(
                    'service_block_index',
                    'service_block',
                    'warehouse_id',
                    'tax_detail',
                    'row_index',
                    'id'
                ))
                ->render();

            return $output;
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
        $selling_price_group_id = request()->input('selling_price_group_id', null);
        $warehouse_id = request()->input('warehouse_id', '');
        $tax_detail = request()->input('tax_detail', 0);
        $service_block_index = request()->input('service_block_index');
        $row_index = request()->input('row_index');
        $service_parent_id = request()->input('service_parent_id');
        $quote_id = request()->input('quote_id');

        $variation = Variation::find($variation_id);

        $product_q = DB::table('variations as variation')
            ->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
            ->where('variation.id', $variation_id)
            ->select(
                'variation.id as variation_id',
                'product.type as type_product', 
                'product.name as name_product',
                'variation.name as name_variation',
                'product.sku as sku', 
                'variation.sub_sku as sub_sku',
                'product.tax as tax_id',
                'product.alert_quantity as tax_percent', 
                'variation.default_sell_price as unit_price_exc_tax',
                'variation.sell_price_inc_tax as unit_price_inc_tax',
                'variation.default_sell_price as price'
            );

        if ($variation->product->clasification != 'service') {
            $product_q = $product_q->leftJoin('variation_location_details as VLD', 'VLD.variation_id', '=', 'variation.id')
                ->where('VLD.warehouse_id', $warehouse_id);

            $product_q = $product_q->addSelect('VLD.qty_available');
        }

        $product_q = $product_q->first();

        if (! empty($selling_price_group_id) || ! is_null($selling_price_group_id)) {
            $variation_group_prices = $this->productUtil->getVariationGroupPrice($variation_id, $selling_price_group_id, $product_q->tax_percent);

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
            'qty_available' => isset($product_q->qty_available) ? $product_q->qty_available : 0,
            'tax_percent' => $this->taxUtil->getTaxPercent($product_q->tax_id)
        );

        if (! empty($quote_id)) {
            $quote_line = QuoteLine::where('quote_id', $quote_id)
                ->where('variation_id', $variation_id)
                ->where('service_parent_id', $service_parent_id)
                ->first();

            if (! empty($quote_line)) {
                $product['quote_line_id'] = $quote_line->id;
                $product['warehouse_id'] = $quote_line->warehouse_id;
                $product['quantity'] = $quote_line->quantity;
                $product['discount_type'] = $quote_line->discount_type;
                $product['discount_amount'] = $quote_line->discount_amount;
                $product['price'] = $quote_line->unit_price_exc_tax;
                $product['price_inc_tax'] = $quote_line->unit_price_inc_tax;
            }
        }

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
                'service_parent_id'
            ))
            ->render();

        return $output;
    }

    /**
     * Add spares not stock to service block.
     * 
     * @param  int  $variation_id
     * @return json
     */
    public function addSpareNotStock($variation_id)
    {
        $selling_price_group_id = request()->input('selling_price_group_id', null);
        $warehouse_id = request()->input('warehouse_id', '');
        $tax_detail = request()->input('tax_detail', 0);
        $service_block_index = request()->input('service_block_index');
        $row_index = request()->input('row_index');
        $service_parent_id = request()->input('service_parent_id');
        $quote_id = request()->input('quote_id');

        $product_q = DB::table('variations as variation')
            ->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
            ->where('variation.id', $variation_id)
            ->select(
                'variation.id as variation_id',
                'product.type as type_product', 
                'product.name as name_product',
                'variation.name as name_variation',
                'product.sku as sku', 
                'variation.sub_sku as sub_sku',
                'product.tax as tax_id',
                'variation.default_sell_price as price',
                'variation.sell_price_inc_tax as price_inc_tax'
            )
            ->first();
    
        $product = array(
            'variation_id' => $product_q->variation_id,
            'type_product' => $product_q->type_product,
            'name_product' => $product_q->name_product,
            'name_variation' => $product_q->name_variation,
            'sku' => $product_q->sku,
            'sub_sku' => $product_q->sub_sku,
            'price' => $product_q->price,
            'price_inc_tax' => $product_q->price_inc_tax,
            'qty_available' => null,
            'tax_percent' => $this->taxUtil->getTaxPercent($product_q->tax_id),
        );

        if (! empty($quote_id)) {
            $quote_line = QuoteLine::where('quote_id', $quote_id)
                ->where('variation_id', $variation_id)
                ->where('service_parent_id', $service_parent_id)
                ->first();

            if (! empty($quote_line)) {
                $product['quote_line_id'] = $quote_line->id;
                $product['warehouse_id'] = $quote_line->warehouse_id;
                $product['quantity'] = $quote_line->quantity;
                $product['discount_type'] = $quote_line->discount_type;
                $product['discount_amount'] = $quote_line->discount_amount;
                $product['price'] = $quote_line->unit_price_exc_tax;
                $product['price_inc_tax'] = $quote_line->unit_price_inc_tax;
            }
        }

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
                'service_parent_id'
            ))
            ->render();

        return $output;
    }

    /**
     * Get service blocks by quote.
     * 
     * @param  int  $id
     * @return json
     */
    public function getServiceBlocksByQuote($id)
    {
        $service_block_q = DB::table('quote_lines as line')
            ->join('variations as variation', 'variation.id', '=', 'line.variation_id')
            ->join('products as product', 'product.id', '=', 'variation.product_id')
            ->select(
                'line.*',
                'product.name as name_product',
                'variation.name as name_variation',
                'product.sku as sku',
                'variation.sub_sku as sub_sku',
                'product.tax as tax_id'
            )
            ->whereNull('line.service_parent_id')
            ->where('line.quote_id', $id)
            ->orderBy('line.id', 'asc')
            ->get();

        $service_blocks = array();

        foreach ($service_block_q as $item) {
            $service_block_array = array(
                'quote_line_id' => $item->id,
                'quote_id' => $item->quote_id,
                'variation_id'=> $item->variation_id,
                'warehouse_id' => $item->warehouse_id,
                'quantity' => $item->quantity,
                'unit_price_exc_tax' => $item->unit_price_exc_tax,
                'unit_price_inc_tax' => $item->unit_price_inc_tax,
                'discount_type' => $item->discount_type,
                'discount_amount' => $item->discount_amount,
                'tax_amount' => $item->tax_amount,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'name_product' => $item->name_product,
                'name_variation' => $item->name_variation,
                'sku' => $item->sku,
                'sub_sku' => $item->sub_sku,
                'tax_percent' => $this->taxUtil->getTaxPercent($item->tax_id),
                'service_parent_id' => 0
            );

            $spare_rows_q = DB::table('quote_lines as line')
                ->join('variations as variation', 'variation.id', '=', 'line.variation_id')
                ->join('products as product', 'product.id', '=', 'variation.product_id')
                ->select(
                    'line.*',
                    'product.name as name_product',
                    'variation.name as name_variation',
                    'product.sku as sku',
                    'variation.sub_sku as sub_sku',
                    'product.tax as tax_id'
                )
                ->where('line.service_parent_id', $item->variation_id)
                ->where('line.quote_id', $id)
                ->orderBy('line.id', 'asc')
                ->get();

            $spare_rows = array();

            foreach ($spare_rows_q as $spare) {
                $spare_row_array = array(
                    'quote_line_id' => $spare->id,
                    'quote_id' => $spare->quote_id,
                    'variation_id'=> $spare->variation_id,
                    'warehouse_id' => $spare->warehouse_id,
                    'quantity' => $spare->quantity,
                    'unit_price_exc_tax' => $spare->unit_price_exc_tax,
                    'unit_price_inc_tax' => $spare->unit_price_inc_tax,
                    'discount_type' => $spare->discount_type,
                    'discount_amount' => $spare->discount_amount,
                    'tax_amount' => $spare->tax_amount,
                    'created_at' => $spare->created_at,
                    'updated_at' => $spare->updated_at,
                    'name_product' => $spare->name_product,
                    'name_variation' => $spare->name_variation,
                    'sku' => $spare->sku,
                    'sub_sku' => $spare->sub_sku,
                    'tax_percent' => $this->taxUtil->getTaxPercent($spare->tax_id),
                    'service_parent_id' => $spare->service_parent_id
                );

                array_push($spare_rows, $spare_row_array);
            }

            $service_block_array['spare_rows'] = $spare_rows;

            array_push($service_blocks, $service_block_array);
        }

        $output = [
            'success' => 1
        ];

        $output['html_content'] = view('quote.partials.service_block', compact(
                'service_block_index',
                'service_block',
                'warehouse_id',
                'tax_detail',
                'row_index'
            ))
            ->render();

        return $output;

    }

    /**
     * Get quote in PDF format.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewQuoteWorkshop($id)
    {
        if (! auth()->user()->can('quotes.view')) {
            abort(403, 'Unauthorized action.');
        }

        $quote = DB::table('quotes as quote')
            ->join('employees as employee', 'employee.id', '=', 'quote.employee_id')
            ->select(
                'quote.*',
                DB::raw('CONCAT(employee.first_name, " ", employee.last_name) as employee'),
                'employee.short_name as short_name')
            ->where('quote.id', $id)
            ->first();

        // Business info
        $business_id = request()->session()->get('user.business_id');
        $business = Business::leftJoin('states', 'states.id', 'business.state_id')
            ->where('business.id', $business_id)
            ->select(
                'business.*',
                'states.name as state'
            )
            ->first();
        
        $location = BusinessLocation::first();
        $business->landmark = $location->landmark;
        $business->city = $location->city;
        $business->mobile = $location->mobile;
        $business->alternate_number = $location->alternate_number;
        $business->email = $location->email;

        // Customer info
        $customer_id = $quote->customer_id;
        $customer = Customer::find($customer_id);

        // Customer vehicle info
        $customer_vehicle = CustomerVehicle::find($quote->customer_vehicle_id);

        // Lines
        $service_block_q = DB::table('quote_lines as line')
            ->join('variations as variation', 'variation.id', '=', 'line.variation_id')
            ->join('products as product', 'product.id', '=', 'variation.product_id')
            ->select(
                'line.*',
                'product.name as name_product',
                'variation.name as name_variation',
                'product.sku as sku',
                'variation.sub_sku as sub_sku',
                'product.tax as tax_id',
                'product.type as type_product'
            )
            ->whereNull('line.service_parent_id')
            ->where('line.quote_id', $id)
            ->orderBy('line.id', 'asc')
            ->get();

        $service_blocks = array();

        foreach ($service_block_q as $item) {
            $service_block_array = array(
                'quote_line_id' => $item->id,
                'quote_id' => $item->quote_id,
                'variation_id'=> $item->variation_id,
                'warehouse_id' => $item->warehouse_id,
                'quantity' => $item->quantity,
                'unit_price_exc_tax' => $item->unit_price_exc_tax,
                'unit_price_inc_tax' => $item->unit_price_inc_tax,
                'discount_type' => $item->discount_type,
                'discount_amount' => $item->discount_amount,
                'tax_amount' => $item->tax_amount,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'name_product' => $item->name_product,
                'name_variation' => $item->name_variation,
                'sku' => $item->sku,
                'sub_sku' => $item->sub_sku,
                'tax_percent' => $this->taxUtil->getTaxPercent($item->tax_id),
                'service_parent_id' => 0,
                'type_product' => $item->type_product,
                'note' => $item->note,
                'sku' => $item->sub_sku
            );

            $spare_rows_q = DB::table('quote_lines as line')
                ->join('variations as variation', 'variation.id', '=', 'line.variation_id')
                ->join('products as product', 'product.id', '=', 'variation.product_id')
                ->select(
                    'line.*',
                    'product.name as name_product',
                    'variation.name as name_variation',
                    'product.sku as sku',
                    'variation.sub_sku as sub_sku',
                    'product.tax as tax_id',
                    'product.type as type_product',
                )
                ->where('line.service_parent_id', $item->variation_id)
                ->where('line.quote_id', $id)
                ->orderBy('line.id', 'asc')
                ->get();

            $spare_rows = array();

            foreach ($spare_rows_q as $spare) {
                $spare_row_array = array(
                    'quote_line_id' => $spare->id,
                    'quote_id' => $spare->quote_id,
                    'variation_id'=> $spare->variation_id,
                    'warehouse_id' => $spare->warehouse_id,
                    'quantity' => $spare->quantity,
                    'unit_price_exc_tax' => $spare->unit_price_exc_tax,
                    'unit_price_inc_tax' => $spare->unit_price_inc_tax,
                    'discount_type' => $spare->discount_type,
                    'discount_amount' => $spare->discount_amount,
                    'tax_amount' => $spare->tax_amount,
                    'created_at' => $spare->created_at,
                    'updated_at' => $spare->updated_at,
                    'name_product' => $spare->name_product,
                    'name_variation' => $spare->name_variation,
                    'sku' => $spare->sku,
                    'sub_sku' => $spare->sub_sku,
                    'tax_percent' => $this->taxUtil->getTaxPercent($spare->tax_id),
                    'service_parent_id' => $spare->service_parent_id,
                    'type_product' => $spare->type_product,
                    'note' => $item->note,
                );

                array_push($spare_rows, $spare_row_array);
            }

            $service_block_array['spare_rows'] = $spare_rows;

            array_push($service_blocks, $service_block_array);
        }

        $pdf = \PDF::loadView('quote.view_workshop', compact(
            'quote',
            'service_blocks',
            'customer',
            'customer_vehicle',
            'business'
        ));

        return $pdf->stream('quote.pdf');
    }

    /**
     * Get workshop data.
     * 
     * @param  int  $id
     * @return json
     */
    public function workshopData($id)
    {
        $quote = Quote::find($id);

        $customer_vehicles = CustomerVehicle::where('customer_id', $quote->customer_id)
            ->select(
                DB::raw("CONCAT(COALESCE(customer_vehicles.license_plate, ''), ' - ', COALESCE(customer_vehicles.model, ''), ' ', COALESCE(customer_vehicles.year, ''), ' ', COALESCE(customer_vehicles.color, '')) as name"),
                'customer_vehicles.id'
            )
            ->get();

        if (! empty($customer_vehicles)) {
            $quote->customer_vehicles = $customer_vehicles;
        }

        $service_blocks = QuoteLine::where('quote_id', $quote->id)
            ->whereNull('service_parent_id')
            ->get();

        if (! empty($service_blocks)) {
            $quote->service_blocks = $service_blocks;
        }

        return json_encode($quote);
    }

    /**
     * Get spares from a service block.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getSpareLines()
    {
        $quote_id = request()->input('quote_id', null);
        $service_block_index = request()->input('service_block_index');
        $service_parent_id = request()->input('service_parent_id', null);

        $quote_lines = QuoteLine::join('variations', 'variations.id', 'quote_lines.variation_id')
            ->join('products', 'products.id', 'variations.product_id')
            ->where('quote_lines.quote_id', $quote_id)
            ->where('quote_lines.service_parent_id', $service_parent_id)
            ->select(
                'quote_lines.*',
                'products.clasification'
            )
            ->orderBy('quote_lines.id')
            ->get();

        $result = [];

        foreach ($quote_lines as $quote_line) {
            if ($quote_line->enable_stock == 1) {
                $vld = VariationLocationDetails::where('variation_id', $quote_line->variation_id)
                    ->where('warehouse_id', $quote_line->warehouse_id)
                    ->first();

                if (! empty($vld)) {
                    $validate_stock = $vld->qty_available != null ? 1 : 0;
                } else {
                    $validate_stock = 0;
                }

            } else {
                $validate_stock = 0;
            }

            $line = [
                'variation_id' => $quote_line->variation_id,
                'service_block_index' => $service_block_index,
                'service_parent_id' => $service_parent_id,
                'warehouse_id' => $quote_line->warehouse_id,
                'validate_stock' => $validate_stock,
                'quote_id' => $quote_id,
                'id' => $quote_line->id
            ];

            array_push($result, $line);
        }

        return json_encode($result);
    }
}
