<?php

namespace App\Http\Controllers;

use App\Apportionment;
use App\ApportionmentHasTransaction;
use App\Product;
use App\Brands;
use App\Category;
use App\Catalogue;
use App\Unit;
use App\UnitGroup;
use App\UnitGroupLines;
use App\TaxRate;
use App\VariationTemplate;
use App\ProductVariation;
use App\Variation;
use App\Business;
use App\PaymentTerm;
use App\Country;
use App\PurchaseLine;
use App\VariationLocationDetails;
use App\BusinessLocation;
use App\SellingPriceGroup;
use App\VariationGroupPrice;
use App\group_sub_products;
use App\Suplies;
use App\ProductHasSuppliers;
use App\CustomerGroup;
use App\Employees;
use App\KitHasProduct;
use App\ProductAccountsLocation;
use App\SalePriceScale;
use App\TaxGroup;
use App\Contact;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Excel;
use App\Utils\ProductUtil;
use App\Utils\TaxUtil;
use App\Utils\ModuleUtil;
use ProductHasSupplier;

class ProductController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    private $taxUtil;
    private $barcode_types;
    private $clone_product;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TaxUtil $taxUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->taxUtil = $taxUtil;
        $this->moduleUtil = $moduleUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
        /** Business types */
        $this->business_type = ['small_business', 'medium_business', 'large_business'];
        /** Payment conditions */
        $this->payment_conditions = ['cash', 'credit'];

        $this->clone_product = config('app.clone_product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('product.view') && !auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $rack_enabled = (request()->session()->get('business.enable_racks') || request()->session()->get('business.enable_row') || request()->session()->get('business.enable_position'));

        //All for view create
        $business_id = request()->session()->get('user.business_id');
        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);

        //Check if subscribed or not, then check for products quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('products', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action('ProductController@index'));
        }
        
        $products = DB::table('products as product')
            ->leftJoin('brands', 'product.brand_id', '=', 'brands.id')
            ->leftJoin('categories as c1', 'product.category_id', '=', 'c1.id')
            ->leftJoin('categories as c2', 'product.sub_category_id', '=', 'c2.id')
            ->where('product.business_id', $business_id)
            ->where('product.type', '!=', 'modifier')
            ->select(
                'product.id as id',
                'product.name as name',
                'product.clasification as clasification',
                'product.type as type',
                'c1.name as category',
                'c2.name as sub_category',
                'product.status as status',
                'brands.name as brand',
                'product.sku as sku'
            )->get();

        return view('product.index')
            ->with(compact(
                'rack_enabled',
                'selling_price_group_count',
                'products',
            ));
    }

    public function getProductsData()
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $products = DB::table('products as product')
            ->leftJoin('brands', 'product.brand_id', '=', 'brands.id')
            ->leftJoin('unit_groups as unit_group', 'product.unit_group_id', '=', 'unit_group.id')
            ->leftJoin('units as unit', 'product.unit_id', '=', 'unit.id')
            ->leftJoin('categories as c1', 'product.category_id', '=', 'c1.id')
            ->leftJoin('categories as c2', 'product.sub_category_id', '=', 'c2.id')
            ->leftJoin('tax_groups', 'product.tax', '=', 'tax_groups.id')
            ->where('product.business_id', $business_id)
            ->where('product.type', '!=', 'modifier')
            ->select(
                'product.id',
                'product.name as product',
                'product.clasification',
                'product.type',
                'product.dai',
                'c1.name as category',
                'c2.name as sub_category',
                'product.status',
                'unit_group.description as unit_group',
                'brands.name as brand',
                'tax_groups.description as tax',
                'product.sku',
                'product.image',
                'tax_groups.description as applicable_tax'
            );

        return Datatables::of($products)
            ->editColumn('status', function($row){
                $status = __('product.status_inactive');
                if($row->status == 'active'){
                    $status = __('product.status_active');
                }
                return $status;

            })->setRowAttr([
                'data-href' => function ($row) {
                    if (auth()->user()->can("product.view")) {
                        return  action('ProductController@view', [$row->id]);
                    } else {
                        return '';
                    }
                }
            ])

            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $rack_enabled = (request()->session()->get('business.enable_racks') || request()->session()->get('business.enable_row') || request()->session()->get('business.enable_position'));

        //All for view create
        $business_id = request()->session()->get('user.business_id');
        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);

        //Check if subscribed or not, then check for products quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('products', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action('ProductController@index'));
        }

        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');

        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');

        if ($this->getUnitConf($business_id) == 1) {
            $units = UnitGroup::forDropdown($business_id);
        } else {
            $units = Unit::forDropdown($business_id);
        }


        $product_data = Product::where('business_id', $business_id)
            ->pluck('name', 'id');

        //$tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        //$taxes = $tax_dropdown['tax_rates'];
        //$tax_attributes = $tax_dropdown['attributes'];
        $taxes = $this->taxUtil->getTaxGroups($business_id, 'products')
            ->pluck('name', 'id');

        $barcode_types = $this->barcode_types;
        $barcode_default =  $this->productUtil->barcode_default();

        $default_profit_percent = Business::where('id', $business_id)->value('default_profit_percent');

        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);

        //Duplicate product
        $duplicate_product = null;
        $rack_details = null;

        $sub_categories = [];
        if (!empty(request()->input('d'))) {
            $duplicate_product = Product::where('business_id', $business_id)->find(request()->input('d'));
            $duplicate_product->name .= ' (copy)';

            if (!empty($duplicate_product->category_id)) {
                $sub_categories = Category::where('business_id', $business_id)
                    ->where('parent_id', $duplicate_product->category_id)
                    ->pluck('name', 'id')
                    ->toArray();
            }

            //Rack details
            if (!empty($duplicate_product->id)) {
                $rack_details = $this->productUtil->getRackDetails($business_id, $duplicate_product->id);
            }
        }

        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);
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

        $employees_sales = Employees::forDropdown(($business_id));
        //$products = Product::forDropdown($business_id);

        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');
        /** Business types */
        $business_type = $this->business_type;
        /** Payment conditions */
        $payment_conditions = $this->payment_conditions;
        /** Get supplier main account */
        $business = Business::find($business_id);
        $supplier_account = "";
        if($business->accounting_supplier_id){
            $supplier_account =
                Catalogue::where("status", 1)
                    ->where("id", $business->accounting_supplier_id)
                    ->value("code");
        }
        $payment_terms = PaymentTerm::select('id', 'name')
            ->pluck('name', 'id');

        $countries = Country::select('id', 'name')
        ->where('business_id', $business_id)
        ->pluck('name', 'id');
        
        $products = DB::table('variations')
            ->join('products', 'products.id', '=', 'variations.product_id')
            ->select('products.name as name_product', 'variations.name as name_variation', 'variations.id', 'variations.sub_sku', 'products.sku')
            ->where('business_id', $business_id)
            ->where('products.clasification', '<>', 'kits')
            ->where('products.status', 'active')
            ->get();

        $business_debt_to_pay_type = $business->debt_to_pay_type;

        // Default products tax
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $default_products_tax = $product_settings['default_products_tax'];

        return view('product.create_product')
            ->with(compact(
                'rack_enabled',
                'categories',
                'brands',
                'units',
                'taxes',
                'barcode_types',
                'default_profit_percent',
                'barcode_default',
                'business_locations',
                'duplicate_product',
                'sub_categories',
                'rack_details',
                'selling_price_group_count',
                'product_data',
                'types',
                'customer_groups',
                'employees_sales',
                'products',
                'tax_groups',
                'business_type',
                'payment_conditions',
                'selling_price_group_count',
                'supplier_account',
                'payment_terms',
                'countries',
                'business_debt_to_pay_type',
                'default_products_tax'
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
        //dd($request);
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $product_ids = $request->input('product_ids');
            $quantity = $request->input('quantity');
            $clas = $request->input('clas_product');
            $child = $request->input('kit_child');

            $business_id = $request->session()->get('user.business_id');

            $product_details = $request->only(
                ['name', 'brand_id', 'category_id', 'tax', 'type', 'barcode_type', 'sku',
                'alert_quantity', 'tax_type', 'weight', 'product_description', 'discount_card',
                'clasification', 'provider_code', 'drive_unit', 'volume', 'download_time']);

            $product_details['provider_code'] = $request->provider_code;
            $product_details['drive_unit'] = $request->drive_unit;


            if ($this->getUnitConf($business_id) == 1) {
                $product_details['unit_group_id'] = $request->input('unit_id');
            } else {
                $product_details['unit_id'] = $request->input('unit_id');
            }

            $product_details['business_id'] = $business_id;
            $product_details['created_by'] = $request->session()->get('user.id');
            $product_details['status'] = !empty($request->input('is_active')) ? 'active' : 'inactive';
            $product_details['dai'] = !empty($request->input('dai')) ? $request->input('dai') : 0.00;


            if (!empty($request->input('enable_stock')) &&  $request->input('enable_stock') == 1) {
                $product_details['enable_stock'] = 1;
            }
            if (!empty($request->input('sub_category_id'))) {
                $product_details['sub_category_id'] = $request->input('sub_category_id');
            }
            if (empty($product_details['sku'])) {
                $product_details['sku'] = ' ';
            }

            if (! empty($request->input('check_dai')) && $request->input('check_dai') == 1) {
                $product_details['check_dai'] = 1;
            }

            $expiry_enabled = $request->session()->get('business.enable_product_expiry');
            if (!empty($request->input('expiry_period_type')) && !empty($request->input('expiry_period')) && !empty($expiry_enabled) && ($product_details['enable_stock'] == 1)) {
                $product_details['expiry_period_type'] = $request->input('expiry_period_type');
                $product_details['expiry_period'] = $this->productUtil->num_uf($request->input('expiry_period'));
            }

            if (!empty($request->input('enable_sr_no')) &&  $request->input('enable_sr_no') == 1) {
                $product_details['enable_sr_no'] = 1;
            }
            //upload document
            $product_details['image'] = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'));

            $clasification = $request->input('clasification');
            if ($clasification == "service") {
                $product_details['barcode_type'] = null;
                $product_details['enable_stock'] = 0;
                $product_details['alert_quantity'] = 0;
                $product_details['brand_id'] = null;
                $product_details['unit_id'] = null;
                $product_details['dai'] = 0.00;

                $product_details['type'] = 'single';
                $product_details['enable_sr_no'] = 0;
            }
            if ($clasification == "kits") {
                $product_details['enable_stock'] = 0;
                $product_details['alert_quantity'] = 0;
                $product_details['brand_id'] = null;
                $product_details['unit_group_id'] = null;
                $product_details['unit_id'] = null;
                $product_details['dai'] = 0.00;

                $product_details['type'] = 'single';
                $product_details['enable_sr_no'] = 0;
            }

            if ($request->get('has_warranty') == 1) {
                $product_details['warranty'] = $request->input('warranty');
                $product_details['has_warranty'] = 1;
            } else {
                $product_details['has_warranty'] = 0;
            }


            DB::beginTransaction();
            $product = Product::create($product_details);
            
            if ($clasification == "kits") {
                if (!empty($product_ids)) {
                    $cont = 0;
                    while ($cont < count($product_ids)) {
                        $detail = new KitHasProduct;
                        $detail->parent_id = $product->id;
                        $detail->children_id = $product_ids[$cont];
                        $detail->quantity = $quantity[$cont];
                        if ($clas[$cont] == 'product') {
                            if ($this->getUnitConf($business_id) == 1) {
                                $detail->unit_group_id_line = $child[$cont];
                            } else {
                                $detail->unit_id = $child[$cont];
                            }
                        }
                        $detail->save();
                        $cont = $cont + 1;
                    }
                }
            }

            if (empty(trim($request->input('sku')))) {
                $sku = $this->productUtil->generateProductSku($product->id);
                $product->sku = $sku;
                $product->save();
            }

            if ($product->type == 'single') {
                $this->productUtil->createSingleProductVariation($product->id, $product->sku, $request->input('single_dpp'), $request->input('single_dpp_inc_tax'), $request->input('profit_percent'), $request->input('single_dsp'), $request->input('single_dsp_inc_tax'));
            } elseif ($product->type == 'variable') {
                if (!empty($request->input('product_variation'))) {
                    $input_variations = $request->input('product_variation');
                    $this->productUtil->createVariableProductVariations($product->id, $input_variations);
                }
            }

            //Add product racks details.
            $product_racks = $request->get('product_racks', null);
            if (!empty($product_racks)) {
                $this->productUtil->addRackDetails($business_id, $product->id, $product_racks);
            }

            /** Sync product */
            if ($this->clone_product && $product->type == "single") {
                $this->productUtil->syncProduct($product->id, $product->sku, "store");
            }

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('product.product_added_success'),
                'product_id' => $product->id
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        if ($request->input('submit_type') == 'submit_n_add_opening_stock') {
            return redirect()->action('OpeningStockController@add', ['product_id' => $product->id]);
        } else if ($request->input('submit_type') == 'submit_n_add_selling_prices') {
            return redirect()->action('ProductController@addSellingPrices', [$product->id]);
        } else if ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action('ProductController@create')->with('status', $output);
        } else {
            return redirect('products')->with('status', $output);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $details = $this->productUtil->getRackDetails($business_id, $id, true);

        return view('product.show')->with(compact('details'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');


        $conf_units = $this->getUnitConf($business_id);

        if ($conf_units == 1) {
            $units = UnitGroup::forDropdown($business_id);
        } else {
            $units = Unit::forDropdown($business_id);
        }

        //$tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        //$taxes = $tax_dropdown['tax_rates'];
        //$tax_attributes = $tax_dropdown['attributes'];
        $taxes = $this->taxUtil->getTaxGroups($business_id, 'products')
            ->pluck('name', 'id');

        $barcode_types = $this->barcode_types;

        $product = Product::where('business_id', $business_id)
            ->where('id', $id)
            ->first();
        if ($product->status == 'active') {
            $is_active = true;
        } else {
            $is_active = false;
        }

        $tax_percent = $this->taxUtil->gettaxPercent($product->tax);
        $tax_percent = $tax_percent * 100;

        $sub_categories = [];

        $sub_categories = Category::where('business_id', $business_id)
            ->where('parent_id', $product->category_id)
            ->pluck('name', 'id')
            ->toArray();
        $sub_categories = ["" => "None"] + $sub_categories;

        $default_profit_percent = Business::where('id', $business_id)->value('default_profit_percent');

        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);
        //Rack details
        $rack_details = $this->productUtil->getRackDetails($business_id, $id);

        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);

        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);
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

        $employees_sales = Employees::forDropdown(($business_id));
        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');
        /** Business types */
        $business_type = $this->business_type;
        /** Payment conditions */
        $payment_conditions = $this->payment_conditions;
        /** Get supplier main account */
        $business = Business::find($business_id);
        $supplier_account = "";
        if($business->accounting_supplier_id){
            $supplier_account =
                Catalogue::where("status", 1)
                    ->where("id", $business->accounting_supplier_id)
                    ->value("code");
        }

        $payment_terms = PaymentTerm::select('id', 'name')
        ->pluck('name', 'id');

        $business_debt_to_pay_type = $business->debt_to_pay_type;

        $countries = Country::select('id', 'name')
        ->where('business_id', $business_id)
        ->pluck('name', 'id');

        $products = DB::table('variations')
            ->join('products', 'products.id', '=', 'variations.product_id')
            ->select('products.name as name_product', 'variations.name as name_variation', 'variations.id', 'variations.sub_sku', 'products.sku')
            ->where('business_id', $business_id)
            ->where('products.clasification', '<>', 'kits')
            ->where('products.status', 'active')
            ->get();


        return view('product.edit')
            ->with(compact(
                'categories', 'brands', 'units', 'conf_units', 'taxes', 'tax_percent',
                'barcode_types', 'product', 'sub_categories', 'default_profit_percent',
                'business_locations', 'rack_details', 'selling_price_group_count',
                'is_active', 'types', 'customer_groups', 'employees_sales', 'products',
                'tax_groups', 'business_type', 'payment_conditions', 'supplier_account',
                'payment_terms', 'business_debt_to_pay_type', 'countries'));
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
        //dd($request);
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            //fields to product_has_suppliers table pivote
            //$supplier_ids = $request->input('supplier_ids');
            // $catalogue = $request->input('catalogue');
            // $uxc = $request->input('uxc');
            // $weight_product = $request->input('weight_product');
            // $dimensions = $request->input('dimensions');
            // $custom_fields = $request->input('custom_field');

            //fields to kit_has_products table pivote
            $product_ids = $request->input('product_ids');
            $quantity = $request->input('quantity');
            $clas = $request->input('clas_product');
            $child = $request->input('kit_child');
            //

            $business_id = $request->session()->get('user.business_id');
            $product_details = $request->only([
                'name',
                'brand_id',
                'category_id',
                'tax',
                'barcode_type',
                'sku',
                'alert_quantity',
                'tax_type',
                'weight',
                'product_description',
                'unit_id',
                'warranty',
                'volume',
                'download_time',
                //'drive_unit'
            ]);

            $product_details['status'] = !empty($request->input('is_active')) ? 'active' : 'inactive';

            if (!empty($request->input('enable_stock')) &&  $request->input('enable_stock') == 1) {
                $product_details['enable_stock'] = 1;
            } else {
                $product_details['enable_stock'] = 0;
            }

            $product_details['dai'] = !empty($request->input('dai')) ? $request->input('dai') : 0.00;

            if (! empty($request->input('check_dai')) &&  $request->input('check_dai') == 1) {
                $product_details['check_dai'] = 1;
            } else {
                $product_details['check_dai'] = 0;
            }

            DB::beginTransaction();

            $product = Product::where('business_id', $business_id)
                ->where('id', $id)
                ->with(['product_variations'])
                ->first();
            $product->name = $product_details['name'];
            $product->brand_id = $product_details['brand_id'];


            if ($this->getUnitConf($business_id) == 1) {
                $product->unit_group_id = $product_details['unit_id'];
            } else {
                $product->unit_id = $product_details['unit_id'];
            }

            $product->category_id = $product_details['category_id'];
            $product->tax = $product_details['tax'];
            $product->barcode_type = $product_details['barcode_type'];
            $product->sku = $product_details['sku'];
            $product->alert_quantity = $product_details['alert_quantity'];
            $product->tax_type = $product_details['tax_type'];
            $product->weight = $product_details['weight'];
            $product->product_description = $product_details['product_description'];
            $product->warranty = $product_details['warranty'];
            $product->enable_stock = $product_details['enable_stock'];
            $product->dai = $product_details['dai'];
            $product->check_dai = $product_details['check_dai'];
            $product->status = $product_details['status'];
            $product->volume = $product_details['volume'];
            $product->download_time = $product_details['download_time'];
            //$product->drive_unit = $product_details['drive_unit'];

            if (!empty($request->input('sub_category_id'))) {
                $product->sub_category_id = $request->input('sub_category_id');
            } else {
                $product->sub_category_id = null;
            }

            $expiry_enabled = $request->session()->get('business.enable_product_expiry');
            if (!empty($expiry_enabled)) {
                if (!empty($request->input('expiry_period_type')) && !empty($request->input('expiry_period')) && ($product->enable_stock == 1)) {
                    $product->expiry_period_type = $request->input('expiry_period_type');
                    $product->expiry_period = $this->productUtil->num_uf($request->input('expiry_period'));
                } else {
                    $product->expiry_period_type = null;
                    $product->expiry_period = null;
                }
            }

            if (!empty($request->input('enable_sr_no')) &&  $request->input('enable_sr_no') == 1) {
                $product->enable_sr_no = 1;
            } else {
                $product->enable_sr_no = 0;
            }

            //upload document
            $file_name = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'));
            if (!empty($file_name)) {
                $product->image = $file_name;
            }

            //verifica si la opcion de garantia esta activada
            if ($request->get('has_warranty') == 1) {
                $product_details['warranty'] = $request->input('warranty');
                $product_details['has_warranty'] = 1;
            } else {
                $product_details['has_warranty'] = 0;
            }

            //se asignan lo datos en el objeto de productos
            $product->warranty = $product_details['warranty'];
            $product->has_warranty = $product_details['has_warranty'];

            //se asignan lo datos en el objeto de productos
            $product->warranty = $product_details['warranty'];
            $product->has_warranty = $product_details['has_warranty'];


            $product->save();

            if ($product->type == 'single') {
                if (auth()->user()->can("product.edit_price")) {
                    $single_data = $request->only(['single_variation_id', 'single_dpp', 'single_dpp_inc_tax', 'single_dsp_inc_tax', 'profit_percent', 'single_dsp']);
                    $variation = Variation::find($single_data['single_variation_id']);

                    $variation->sub_sku = $product->sku;
                    $variation->default_purchase_price = $this->productUtil->num_uf($single_data['single_dpp']);
                    $variation->dpp_inc_tax = $this->productUtil->num_uf($single_data['single_dpp_inc_tax']);
                    $variation->profit_percent = $this->productUtil->num_uf($single_data['profit_percent']);
                    $variation->default_sell_price = $this->productUtil->num_uf($single_data['single_dsp']);
                    $variation->sell_price_inc_tax = $this->productUtil->num_uf($single_data['single_dsp_inc_tax']);
                    $variation->save();
                }
            } elseif ($product->type == 'variable') {
                if (auth()->user()->can("product.edit_price")) {
                    //Update existing variations
                    $input_variations_edit = $request->get('product_variation_edit');
                    if (!empty($input_variations_edit)) {
                        $this->productUtil->updateVariableProductVariations($product->id, $input_variations_edit);
                    }

                    //Add new variations created.
                    $input_variations = $request->input('product_variation');
                    if (!empty($input_variations)) {
                        $this->productUtil->createVariableProductVariations($product->id, $input_variations);
                    }
                }
            }

            //Add product racks details.
            $product_racks = $request->get('product_racks', null);
            if (!empty($product_racks)) {
                $this->productUtil->addRackDetails($business_id, $product->id, $product_racks);
            }

            $product_racks_update = $request->get('product_racks_update', null);
            if (!empty($product_racks_update)) {
                $this->productUtil->updateRackDetails($business_id, $product->id, $product_racks_update);
            }
            // if ($product->clasification == "product") {
            //     if (!empty($supplier_ids)) {
            //         ProductHasSuppliers::where('product_id', $id)->forceDelete();
            //         $cont = 0;
            //         while ($cont < count($supplier_ids)) {
            //             $detail = new ProductHasSuppliers;
            //             $detail->product_id = $product->id;
            //             $detail->contact_id = $supplier_ids[$cont];

            //             $detail->catalogue = $catalogue[$cont];
            //             $detail->uxc = $uxc[$cont];
            //             $detail->weight = $weight_product[$cont];
            //             $detail->dimensions = $dimensions[$cont];
            //             $detail->custom_field = $custom_fields[$cont];

            //             $detail->save();
            //             $cont = $cont + 1;
            //         }
            //     } else {
            //         ProductHasSuppliers::where('product_id', $id)->forceDelete();
            //     }
            // }
            
            
            if ($product->clasification == "kits") {
                if (!empty($product_ids)) {
                    //dd($quantity);
                    KitHasProduct::where('parent_id', $id)->forceDelete();
                    $cont = 0;
                    while ($cont < count($product_ids)) {
                        $detail = new KitHasProduct;
                        $detail->parent_id = $id;
                        $detail->children_id = $product_ids[$cont];
                        $detail->quantity = $quantity[$cont];
                        if ($clas[$cont] == 'product') {
                            if ($this->getUnitConf($business_id) == 1) {
                                $detail->unit_group_id_line = $child[$cont];
                            } else {
                                $detail->unit_id = $child[$cont];
                            }
                        }
                        $detail->save();
                        $cont = $cont + 1;
                    }
                } else {
                    KitHasProduct::where('parent_id', $id)->forceDelete();
                }
            }

            /** Sync product */
            if ($this->clone_product && $product->type == "single") {
                $this->productUtil->syncProduct($product->id, $product->sku, "update");
            }

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('product.product_updated_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        // if ($request->input('submit_type') == 'update_n_edit_opening_stock') {
        //     return redirect()->action(
        //         'OpeningStockController@add',
        //         ['product_id' => $product->id]
        //     );
        // } else if ($request->input('submit_type') == 'submit_n_add_selling_prices') {
        //     return redirect()->action(
        //         'ProductController@addSellingPrices',
        //         [$product->id]
        //     );
        // } else if ($request->input('submit_type') == 'save_n_add_another') {
        //     return redirect()->action(
        //         'ProductController@create'
        //     )->with('status', $output);
        // }

        return redirect('products')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $can_be_deleted = true;
                $error_msg = '';

                //Check if any purchase or transfer exists
                $count = PurchaseLine::join(
                    'transactions as T',
                    'purchase_lines.transaction_id',
                    '=',
                    'T.id'
                )
                    ->whereIn('T.type', ['purchase'])
                    ->where('T.business_id', $business_id)
                    ->where('purchase_lines.product_id', $id)
                    ->count();
                if ($count > 0) {
                    $can_be_deleted = false;
                    $error_msg = __('lang_v1.purchase_already_exist');
                } else {
                    //Check if any opening stock sold
                    $count = PurchaseLine::join(
                        'transactions as T',
                        'purchase_lines.transaction_id',
                        '=',
                        'T.id'
                    )
                        ->where('T.type', 'opening_stock')
                        ->where('T.business_id', $business_id)
                        ->where('purchase_lines.product_id', $id)
                        ->where('purchase_lines.quantity_sold', '>', 0)
                        ->count();
                    if ($count > 0) {
                        $can_be_deleted = false;
                        $error_msg = __('lang_v1.opening_stock_sold');
                    } else {
                        //Check if any stock is adjusted
                        $count = PurchaseLine::join(
                            'transactions as T',
                            'purchase_lines.transaction_id',
                            '=',
                            'T.id'
                        )
                            ->where('T.business_id', $business_id)
                            ->where('purchase_lines.product_id', $id)
                            ->where('purchase_lines.quantity_adjusted', '>', 0)
                            ->count();
                        if ($count > 0) {
                            $can_be_deleted = false;
                            $error_msg = __('lang_v1.stock_adjusted');
                        } else {
                            $count = DB::table('kit_has_products')
                                ->select('id')
                                ->whereIn('children_id', [DB::raw("select id from variations where product_id = " . $id . "")])
                                ->count();

                            if ($count > 0) {
                                $can_be_deleted = false;
                                $error_msg = __('product.product_in_kit');
                            }
                        }
                    }
                }
                if ($can_be_deleted) {
                    $product = Product::where('id', $id)
                        ->where('business_id', $business_id)
                        ->first();
                    if (!empty($product)) {
                        DB::beginTransaction();

                        //Delete variation location details
                        VariationLocationDetails::where('product_id', $id)
                            ->delete();
                        ProductHasSuppliers::where('product_id', $id)->delete();
                        $product->delete();

                        DB::commit();
                    }

                    $output = [
                        'success' => true,
                        'msg' => __("lang_v1.product_delete_success")
                    ];
                } else {
                    $output = [
                        'success' => false,
                        'msg' => $error_msg
                    ];
                }
            } catch (\Exception $e) {
                DB::rollBack();
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
     * Get subcategories list for a category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSubCategories(Request $request)
    {
        if (!empty($request->input('cat_id'))) {
            $category_id = $request->input('cat_id');
            $business_id = $request->session()->get('user.business_id');
            $sub_categories = Category::where('business_id', $business_id)
                ->where('parent_id', $category_id)
                ->select(['name', 'id'])
                ->get();
            $html = '<option value="">None</option>';
            if (!empty($sub_categories)) {
                foreach ($sub_categories as $sub_category) {
                    $html .= '<option value="' . $sub_category->id . '">' . $sub_category->name . '</option>';
                }
            }
            echo $html;
            exit;
        }
    }

    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProductVariationFormPart(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;

        $action = $request->input('action');
        if ($request->input('action') == "add") {
            if ($request->input('type') == 'single') {
                return view('product.partials.single_product_form_part')
                    ->with(['profit_percent' => $profit_percent]);
            } elseif ($request->input('type') == 'variable') {
                $variation_templates = VariationTemplate::where('business_id', $business_id)->pluck('name', 'id')->toArray();
                $variation_templates = ["" => __('messages.please_select')] + $variation_templates;

                return view('product.partials.variable_product_form_part')
                    ->with(compact('variation_templates', 'profit_percent', 'action'));
            }
        } elseif ($request->input('action') == "edit" || $request->input('action') == "duplicate") {
            $product_id = $request->input('product_id');
            if ($request->input('type') == 'single') {
                $product_deatails = ProductVariation::where('product_id', $product_id)
                    ->with(['variations'])
                    ->first();

                return view('product.partials.edit_single_product_form_part')
                    ->with(compact('product_deatails'));
            } elseif ($request->input('type') == 'variable') {
                $product_variations = ProductVariation::where('product_id', $product_id)
                    ->with(['variations'])
                    ->get();
                return view('product.partials.variable_product_form_part')
                    ->with(compact('product_variations', 'profit_percent', 'action'));
            }
        }
    }

    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getVariationValueRow(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;

        $variation_index = $request->input('variation_row_index');
        $value_index = $request->input('value_index') + 1;

        $row_type = $request->input('row_type', 'add');

        return view('product.partials.variation_value_row')
            ->with(compact('profit_percent', 'variation_index', 'value_index', 'row_type'));
    }

    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProductVariationRow(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;

        $variation_templates = VariationTemplate::where('business_id', $business_id)
            ->pluck('name', 'id')->toArray();
        $variation_templates = ["" => __('messages.please_select')] + $variation_templates;

        $row_index = $request->input('row_index', 0);
        $action = $request->input('action');

        return view('product.partials.product_variation_row')
            ->with(compact('variation_templates', 'row_index', 'action', 'profit_percent'));
    }

    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getVariationTemplate(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;

        $template = VariationTemplate::where('id', $request->input('template_id'))
            ->with(['values'])
            ->first();
        $row_index = $request->input('row_index');

        return view('product.partials.product_variation_template')
            ->with(compact('template', 'row_index', 'profit_percent'));
    }

    /**
     * Retrieves products list.
     *
     * @param  string  $q
     * @param  boolean  $check_qty
     *
     * @return JSON
     */
    public function getProducts()
    {
        if (request()->ajax()) {
            $term = request()->input('term', '');

            // Validation for barcode gun: Remove # when it's at the end of the string.
            if ($term[strlen($term) - 1] == '#') {
                $term = substr($term, 0, -1);
            }

            $location_id = request()->get('location_id', '');
            $warehouse_id = request()->get('warehouse_id', '');

            $check_qty = request()->get('check_qty', false);

            $price_group_id = request()->get('price_group', '');

            $business_id = request()->session()->get('user.business_id');

            $products = Product::join('variations', 'products.id', 'variations.product_id')
                ->where('status', 'active')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier')
                ->whereNull('variations.deleted_at')
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
                        }
                    }
                );

            //Include search
            if (!empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                });
            }

            if (!empty($price_group_id)) {
                $products->leftjoin(
                    'variation_group_prices AS VGP',
                    function ($join) use ($price_group_id) {
                        $join->on('variations.id', '=', 'VGP.variation_id')
                            ->where('VGP.price_group_id', '=', $price_group_id);
                    }
                );
            }

            //Include warehouse
            if (!empty($warehouse_id)) {
                $products->where(function ($query) use ($warehouse_id) {
                    $query->where('VLD.warehouse_id', $warehouse_id);
                    $query->orWhereNull('VLD.warehouse_id');
                });
            }

            $products->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

            //Include search
            if (!empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                });
            }

            //Include check for quantity
            if ($check_qty) {
                $products->where('VLD.qty_available', '>', 0);
            }

            $products->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'products.enable_stock',
                'variations.id as variation_id',
                'variations.name as variation',
                'VLD.qty_available',
                'variations.sell_price_inc_tax as selling_price',
                'variations.sub_sku',
                'VLD.qty_reserved',
                'products.clasification'
            );

            if (!empty($price_group_id)) {
                $products->addSelect('VGP.price_inc_tax as variation_group_price');
            }

            $result = $products->orderBy('VLD.qty_available', 'desc')
                ->get();

            // Validate stock of kit products
            foreach ($result as $product) {
                $product->state_disabled = 0;

                if ($product->clasification == 'kits') {
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
                            
                            // Validate quantity requested by the kit
                            if (!empty($vld)) {
                                if (($vld->qty_available - $vld->qty_reserved) < $item->quantity) {
                                    $product->state_disabled = 1;
                                    break;
                                }
                            // If record in variation_location_details doesn't exist
                            } else {
                                $product->state_disabled = 1;
                                break;
                            }
                        }
                    }
                }
            }

            return json_encode($result);
        }
    }

    public function getProductsTransferStock()
    {
        if (request()->ajax()) {
            $term = request()->get('term', '');
            $warehouse_id = request()->get('warehouse_id', '');

            $check_qty = request()->get('check_qty', false);

            $price_group_id = request()->get('price_group', '');

            $business_id = request()->session()->get('user.business_id');

            $products = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->where('status', 'active')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier')
                ->whereNull('variations.deleted_at')
                ->leftjoin(
                    'variation_location_details AS VLD',
                    function ($join) use ($warehouse_id) {

                        $join->on('variations.id', '=', 'VLD.variation_id');

                        //Include Location
                        if (!empty($warehouse_id)) {
                            $join->where(function ($query) use ($warehouse_id) {
                                $query->where('VLD.warehouse_id', '=', $warehouse_id);
                                //Check null to show products even if no quantity is available in a location.
                                //TODO: Maybe add a settings to show product not available at a location or not.
                                $query->orWhereNull('VLD.warehouse_id');
                            });;
                        }
                    }
                );
            if (!empty($price_group_id)) {
                $products->leftjoin(
                    'variation_group_prices AS VGP',
                    function ($join) use ($price_group_id) {
                        $join->on('variations.id', '=', 'VGP.variation_id')
                            ->where('VGP.price_group_id', '=', $price_group_id);
                    }
                );
            }

            //Include search
            if (!empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                });
            }

            //Include check for quantity
            if ($check_qty) {
                $products->where('VLD.qty_available', '>', 0);
            }

            $products->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'products.enable_stock',
                'variations.id as variation_id',
                'variations.name as variation',
                'VLD.qty_available',
                'variations.sell_price_inc_tax as selling_price',
                'variations.sub_sku',
                'VLD.qty_reserved'
            );
            if (!empty($price_group_id)) {
                $products->addSelect('VGP.price_inc_tax as variation_group_price');
            }
            $result = $products->orderBy('VLD.qty_available', 'desc')
                ->get();
            return json_encode($result);
        }
    }

    public function getProductsToQuote()
    {
        if (request()->ajax()) {
            $term = request()->input('term', '');
            $warehouse_id = request()->input('warehouse_id', '');
            $selling_price_group_id = request()->input('selling_price_group_id', null);
            $check_qty = request()->input('check_qty', false);
            $business_id = request()->session()->get('user.business_id');

            $products = Product::join('variations', 'products.id', 'variations.product_id')
                ->leftJoin('product_racks', 'products.id', 'product_racks.product_id')
                ->where('products.status', 'active')
                ->whereNull('variations.deleted_at')
                ->leftjoin(
                    'variation_location_details AS VLD',
                    function ($join) use ($warehouse_id) {

                        $join->on('variations.id', '=', 'VLD.variation_id');

                            //Include Location
                        if (!empty($warehouse_id)) {
                            $join->where(function ($query) use ($warehouse_id) {
                                $query->where('VLD.warehouse_id', '=', $warehouse_id);
                                    //Check null to show products even if no quantity is available in a location.
                                    //TODO: Maybe add a settings to show product not available at a location or not.
                                $query->orWhereNull('VLD.warehouse_id');
                            });
                        }
                    }
                )
                ->with('variations.group_prices')
                ->groupBy('products.id');

            if (!empty($selling_price_group_id)) {
                $products->leftjoin(
                    'variation_group_prices AS vgp',
                    function ($join) use ($selling_price_group_id) {
                        $join->on('variations.id', 'vgp.variation_id')
                            ->where('VGP.price_group_id', $selling_price_group_id);
                    }
                );
            }

            $products->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

            //Include search
            if (!empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('products.sku', 'like', '%' . $term . '%');
                    $query->orWhere('variations.sub_sku', 'like', '%' . $term . '%');
                });
            }

            //Include check for quantity
            if ($check_qty) {
                $products->where('VLD.qty_available', '>', 0);
            }

            $products->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'products.enable_stock',
                DB::raw('IFNULL(product_racks.rack, "N/A") as rack'),
                DB::raw('IFNULL(product_racks.row, "N/A") as `row`'),
                DB::raw('IFNULL(product_racks.position, "N/A") as position'),
                'variations.id as variation_id',
                'variations.name as variation',
                'VLD.qty_available',
                'VLD.qty_reserved',
                'variations.sell_price_inc_tax as selling_price',
                'variations.sub_sku'
            )->take(20); //Get only first 25 items

            if (!empty($selling_price_group_id)) {
                $products->addSelect('VGP.price_inc_tax as variation_group_price');
            }

            $result = $products->orderBy('VLD.qty_available', 'desc')
                ->get();
            
            foreach($result as $r){
                $sgp = VariationGroupPrice::join('selling_price_groups as spg', 'variation_group_prices.price_group_id', 'spg.id')
                    ->where('variation_group_prices.variation_id', $r->variation_id)
                    ->select(
                        'variation_group_prices.price_group_id',
                        'spg.name as price_group',
                        'variation_group_prices.price_inc_tax'
                    )->get();
                
                if(!empty($sgp)){
                    foreach($sgp as $g){
                        $item = collect([
                            'price_group_id' => $g->price_group_id,
                            'price_group' => $g->price_group,
                            'price_inc_tax' => $g->price_inc_tax
                        ]);

                        $r->variations->push($item);
                    }
                }
            }
            return json_encode($result);
        }
    }

    /**
     * Retrieves products list without variation list
     *
     * @param  string  $q
     * @param  boolean  $check_qty
     *
     * @return JSON
     */
    public function getProductsWithoutVariations()
    {
        if (request()->ajax()) {
            $term = request()->input('term', '');
            //$location_id = request()->input('location_id', '');

            //$check_qty = request()->input('check_qty', false);

            $business_id = request()->session()->get('user.business_id');

            $products = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->where('status', 'active')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

            //Include search
            if (!empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                });
            }

            //Include check for quantity
            // if($check_qty){
            //     $products->where('VLD.qty_available', '>', 0);
            // }

            $products = $products->groupBy('products.id')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    'products.enable_stock',
                    'products.sku'
                )
                ->orderBy('products.name')
                ->get();
            return json_encode($products);
        }
    }

    /**
     * Checks if product sku already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkProductSku(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $sku = $request->input('sku');
        $product_id = $request->input('product_id');

        //check in products table
        $query = Product::where('business_id', $business_id)
            ->where('sku', $sku);
        if (!empty($product_id)) {
            $query->where('id', '!=', $product_id);
        }
        $count = $query->count();

        //check in variation table if $count = 0
        if ($count == 0) {
            $count = Variation::where('sub_sku', $sku)
                ->join('products', 'variations.product_id', '=', 'products.id')
                ->where('product_id', '!=', $product_id)
                ->where('business_id', $business_id)
                ->count();
        }
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }

    /**
     * Loads quick add product modal.
     *
     * @return \Illuminate\Http\Response
     */
    public function quickAdd()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $product_name = !empty(request()->input('product_name')) ? request()->input('product_name') : '';

        $product_for = !empty(request()->input('product_for')) ? request()->input('product_for') : null;


        $business_id = request()->session()->get('user.business_id');
        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $barcode_types = $this->barcode_types;

        $default_profit_percent = Business::where('id', $business_id)->value('default_profit_percent');

        $locations = BusinessLocation::forDropdown($business_id);

        $enable_expiry = request()->session()->get('business.enable_product_expiry');
        $enable_lot = request()->session()->get('business.enable_lot_number');

        return view('product.partials.quick_add_product')
            ->with(compact('categories', 'brands', 'units', 'taxes', 'barcode_types', 'default_profit_percent', 'tax_attributes', 'product_name', 'locations', 'product_for', 'enable_expiry', 'enable_lot'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveQuickProduct(Request $request)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $product_details = $request->only([
                'name', 'brand_id', 'unit_id', 'category_id', 'tax', 'barcode_type', 'tax_type', 'sku',
                'alert_quantity', 'type'
            ]);
            $product_details['type'] = empty($product_details['type']) ? 'single' : $product_details['type'];
            $product_details['product_description'] = $request->input('product_description');
            $product_details['clasification'] = 'product';
            $product_details['business_id'] = $business_id;
            $product_details['created_by'] = $request->session()->get('user.id');
            if (!empty($request->input('enable_stock')) &&  $request->input('enable_stock') == 1) {
                $product_details['enable_stock'] = 1;
                //TODO: Save total qty
                //$product_details['total_qty_available'] = 0;
            }
            if (empty($product_details['sku'])) {
                $product_details['sku'] = ' ';
            }

            $expiry_enabled = $request->session()->get('business.enable_product_expiry');
            if (!empty($request->input('expiry_period_type')) && !empty($request->input('expiry_period')) && !empty($expiry_enabled)) {
                $product_details['expiry_period_type'] = $request->input('expiry_period_type');
                $product_details['expiry_period'] = $this->productUtil->num_uf($request->input('expiry_period'));
            }

            if (!empty($request->input('enable_sr_no')) &&  $request->input('enable_sr_no') == 1) {
                $product_details['enable_sr_no'] = 1;
            }

            DB::beginTransaction();

            $product = Product::create($product_details);

            if (empty(trim($request->input('sku')))) {
                $sku = $this->productUtil->generateProductSku($product->id);
                $product->sku = $sku;
                $product->save();
            }

            $this->productUtil->createSingleProductVariation(
                $product->id,
                $product->sku,
                $request->input('single_dpp'),
                $request->input('single_dpp_inc_tax'),
                $request->input('profit_percent'),
                $request->input('single_dsp'),
                $request->input('single_dsp_inc_tax')
            );

            if ($product->enable_stock == 1 && !empty($request->input('opening_stock'))) {
                $user_id = $request->session()->get('user.id');

                $transaction_date = $request->session()->get("financial_year.start");
                $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();

                $this->productUtil->addSingleProductOpeningStock($business_id, $product, $request->input('opening_stock'), $transaction_date, $user_id);
            }

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('product.product_added_success'),
                'product' => $product,
                'variation' => $product->variations->first()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $product = Product::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['brand', 'unit', 'category', 'sub_category', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices'])
            ->first();

        $price_groups = SellingPriceGroup::where('business_id', $business_id)->pluck('name', 'id');

        $allowed_group_prices = [];
        foreach ($price_groups as $key => $value) {
            if (auth()->user()->can('selling_price_group.' . $key)) {
                $allowed_group_prices[$key] = $value;
            }
        }

        $group_price_details = [];

        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
            }
        }

        $rack_details = $this->productUtil->getRackDetails($business_id, $id, true);

        // Gets business locations
        // $business_locations = BusinessLocation::where('business_id', $business_id)->orderBy('name')->get();

        // Gets variations list
        $variations_list = Variation::where('product_id', $id)->pluck('name', 'id');

        // Gets sale price scales
        // $sale_price_scale = SalePriceScale::where('product_id', $id)->get();

        // Gets warehouses
        // $warehouses = Warehouse::where('business_id', $business_id)->orderBy('name')->get();

        // For tab price list
        $price_groups_pl = SellingPriceGroup::where('business_id', $business_id)->get();

        return view('product.view-modal')->with(compact(
            'product',
            'rack_details',
            'allowed_group_prices',
            'group_price_details',
            'price_groups_pl',
            'variations_list'
        ));
    }

    /**
     * Mass deletes products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        if (!auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (!empty($request->input('selected_rows'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_rows = explode(',', $request->input('selected_rows'));

                $products = Product::where('business_id', $business_id)
                    ->whereIn('id', $selected_rows)
                    ->with('purchase_lines')
                    ->get();
                $deletable_products = [];

                DB::beginTransaction();

                foreach ($products as $product) {
                    //Delete if no purchase found
                    if (empty($product->purchase_lines->toArray())) {
                        //Delete variation location details
                        VariationLocationDetails::where('product_id', $product->id)
                            ->delete();
                        $product->delete();
                    }
                }

                DB::commit();
            }

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.deleted_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Shows form to add selling price group prices for a product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addSellingPrices($id)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $product = Product::where('business_id', $business_id)
            ->with(['variations', 'variations.group_prices', 'variations.product_variation'])
            ->findOrFail($id);

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->get();
        $variation_prices = [];
        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $variation_prices[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
            }
        }
        return view('product.add-selling-prices')->with(compact('product', 'price_groups', 'variation_prices'));
    }

    /**
     * Saves selling price group prices for a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSellingPrices(Request $request)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $product = Product::where('business_id', $business_id)
                ->with(['variations'])
                ->findOrFail($request->input('product_id'));
            DB::beginTransaction();
            foreach ($product->variations as $variation) {
                $variation_group_prices = [];
                foreach ($request->input('group_prices') as $key => $value) {
                    if (isset($value[$variation->id])) {
                        $variation_group_price =
                            VariationGroupPrice::where('variation_id', $variation->id)
                            ->where('price_group_id', $key)
                            ->first();
                        if (empty($variation_group_price)) {
                            $variation_group_price = new VariationGroupPrice([
                                'variation_id' => $variation->id,
                                'price_group_id' => $key
                            ]);
                        }

                        $variation_group_price->price_inc_tax = $this->productUtil->num_uf($value[$variation->id]);
                        $variation_group_prices[] = $variation_group_price;
                    }
                }

                if (!empty($variation_group_prices)) {
                    $variation->group_prices()->saveMany($variation_group_prices);
                }
            }
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __("lang_v1.updated_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        if ($request->input('submit_type') == 'submit_n_add_opening_stock') {
            return redirect()->action(
                'OpeningStockController@add',
                ['product_id' => $product->id]
            );
        } else if ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                'ProductController@create'
            )->with('status', $output);
        }

        return redirect('products')->with('status', $output);
    }

    public function viewGroupPrice($id)
    {

        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $product = Product::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['variations', 'variations.product_variation', 'variations.group_prices'])
            ->first();

        $price_groups = SellingPriceGroup::where('business_id', $business_id)->pluck('name', 'id');

        $allowed_group_prices = [];
        foreach ($price_groups as $key => $value) {
            if (auth()->user()->can('selling_price_group.' . $key)) {
                $allowed_group_prices[$key] = $value;
            }
        }

        $group_price_details = [];

        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
            }
        }

        return view('product.view-product-group-prices')->with(compact('product', 'allowed_group_prices', 'group_price_details'));
    }

    public function viewSupplier($id)
    {
        $suppliers = DB::table('product_has_suppliers')
            ->join('contacts', 'contacts.id', '=', 'product_has_suppliers.contact_id')
            ->select('contacts.id', 'supplier_business_name', 'name', 'mobile', 'product_has_suppliers.catalogue', 'product_has_suppliers.uxc', 'product_has_suppliers.weight', 'product_has_suppliers.dimensions', 'product_has_suppliers.custom_field')
            ->where('product_has_suppliers.product_id', $id)
            ->get();
        //$product = Product::select('name')->where('id', $id)->first();
        //$product_name = $product->name;
        $product = Product::findOrFail($id);
        $data = array();
        foreach ($suppliers as $supplier) {
            $last_purchase = DB::table('contacts')
                ->join('transactions', 'contacts.id', '=', 'transactions.contact_id')
                ->join('purchase_lines', 'transactions.id', '=', 'purchase_lines.transaction_id')
                ->select('purchase_lines.quantity', 'purchase_lines.purchase_price_inc_tax', DB::raw('DATE_FORMAT(transactions.transaction_date, "%d/%m/%Y") as date'))
                ->where('purchase_lines.product_id', $id)
                ->where('contacts.id', $supplier->id)
                ->orderBy('transactions.transaction_date', 'desc')
                ->take(1)
                ->first();
            if ($last_purchase == null) {
                $last_purchase_date = "N/A";
                $quantity = "N/A";
                $price = "N/A";
                $total = "N/A";
            } else {
                $last_purchase_date = $last_purchase->date;
                $quantity = $last_purchase->quantity;
                $price = $last_purchase->purchase_price_inc_tax;
                $total = $price * $quantity;
            }
            $item = array(
                'id' => $supplier->id,
                'supplier_business_name' => $supplier->supplier_business_name,
                'name' => $supplier->name,
                'mobile' => $supplier->mobile,
                'last_purchase' => $last_purchase_date,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $total,
                'catalogue' => $supplier->catalogue,
                'uxc' => $supplier->uxc,
                'weight' => $supplier->weight,
                'dimensions' => $supplier->dimensions,
                'custom_field' => $supplier->custom_field,
            );
            array_push($data, $item);
        }
        $dataSupplier = json_decode(json_encode($data), FALSE);
        return view('product.view-supplier', compact('dataSupplier', 'product'));
    }

    public function addSupplier(Request $request, $id)
    {
        try{
            $product = Product::findOrFail($id);
            $supplier_ids = $request->input('supplier_ids');

            if (!empty($supplier_ids)) {
                ProductHasSuppliers::where('product_id', $product->id)->forceDelete();
                $cont = 0;
                while ($cont < count($supplier_ids)) {
                    $detail = new ProductHasSuppliers;
                    $detail->product_id = $product->id;
                    $detail->contact_id = $supplier_ids[$cont];
    
                    $detail->catalogue = '-';
                    $detail->uxc = 0;
                    $detail->weight = 0;
                    $detail->dimensions = 0;
                    $detail->custom_field = '-';
    
                    $detail->save();
                    $cont = $cont + 1;
                }
            } else {
                ProductHasSuppliers::where('product_id', $id)->forceDelete();
            }
            $output = [
                'success' => 1,
                'msg' => __('product.supplier_added_success'),
                'product_id' => $product->id
            ];
        }catch(\Exception $e){
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        
        return redirect('products')->with('status', $output);
    }

    public function deleteSupplier($id, $supplierId)
    {
        $detail = ProductHasSuppliers::where('product_id', $id)->where('contact_id', $supplierId)->first();
        if($detail != null){
            $detail->delete();
        }
    }

    public function showProduct($id)
    {
        $products = DB::table('variations')
            ->leftJoin('products', 'products.id', '=', 'variations.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            //->leftJoin('variation_location_details as stock', 'variations.id', '=', 'stock.variation_id')
            ->select('products.clasification', 'variations.id as variation_id', 'products.id as product_id', 'products.name as name_product', 'variations.name as name_variation', 'products.sku', 'variations.sub_sku', 'brands.name as brand', 'products.unit_group_id', 'products.unit_id', 'variations.default_purchase_price')
            ->where('products.id', $id)
            ->first();
        return response()->json($products);
    }

    public function showStock($variation_id, $location_id)
    {
        $products = DB::table('variations')
            ->leftJoin('products', 'products.id', '=', 'variations.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('variation_location_details as stock', 'variations.id', '=', 'stock.variation_id')
            ->select('products.clasification', 'variations.id as variation_id', 'products.id as product_id', 'products.name as name_product', 'variations.name as name_variation', 'products.sku', 'variations.sub_sku', 'brands.name as brand', 'products.unit_group_id', 'products.unit_id', 'variations.default_purchase_price', 'stock.qty_available as quantity')
            ->where('variations.id', $variation_id)
            //->where('stock.location_id', $location_id)
            ->first();
        return response()->json($products);
    }

    public function productHasSuppliers($id)
    {
        $suppliers = DB::table('product_has_suppliers')
            ->join('contacts', 'contacts.id', '=', 'product_has_suppliers.contact_id')
            ->select('contacts.id', 'supplier_business_name', 'name', 'mobile', 'product_has_suppliers.catalogue', 'product_has_suppliers.uxc', 'product_has_suppliers.weight', 'product_has_suppliers.dimensions', 'product_has_suppliers.custom_field')
            ->where('product_has_suppliers.product_id', $id)
            ->get();
        $product = Product::select('name')->where('id', $id)->first();
        $product_name = $product->name;
        $data = array();
        foreach ($suppliers as $supplier) {
            $last_purchase = DB::table('contacts')
                ->join('transactions', 'contacts.id', '=', 'transactions.contact_id')
                ->join('purchase_lines', 'transactions.id', '=', 'purchase_lines.transaction_id')
                ->select('purchase_lines.quantity', 'purchase_lines.purchase_price_inc_tax', DB::raw('DATE_FORMAT(transactions.transaction_date, "%d/%m/%Y") as date'))
                ->where('purchase_lines.product_id', $id)
                ->where('contacts.id', $supplier->id)
                ->orderBy('transactions.transaction_date', 'desc')
                ->take(1)
                ->first();
            if ($last_purchase == null) {
                $last_purchase_date = "N/A";
                $quantity = "N/A";
                $price = "N/A";
                $total = "N/A";
            } else {
                $last_purchase_date = $last_purchase->date;
                $quantity = $last_purchase->quantity;
                $price = $last_purchase->purchase_price_inc_tax;
                $total = $price * $quantity;
            }
            $item = array(
                'id' => $supplier->id,
                'supplier_business_name' => $supplier->supplier_business_name,
                'name' => $supplier->name,
                'mobile' => $supplier->mobile,
                'last_purchase' => $last_purchase_date,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $total,
                'catalogue' => $supplier->catalogue,
                'uxc' => $supplier->uxc,
                'weight' => $supplier->weight,
                'dimensions' => $supplier->dimensions,
                'custom_field' => $supplier->custom_field,
            );
            array_push($data, $item);
        }
        $dataSupplier = json_decode(json_encode($data), FALSE);
        return $dataSupplier;
    }

    public function kitHasProduct($id)
    {
        $kit_lines = KitHasProduct::select('id', 'children_id', 'quantity', 'unit_id', 'unit_group_id_line')->where('parent_id', $id)->get();
        $datos = array();
        foreach ($kit_lines as $kit) {
            $product = DB::table('variations')
                ->leftJoin('products', 'products.id', '=', 'variations.product_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->select('products.clasification', 'variations.id as variation_id', 'products.id as product_id', 'products.name as name_product', 'variations.name as name_variation', 'products.sku', 'variations.sub_sku', 'brands.name as brand', 'products.unit_group_id', 'products.unit_id', 'variations.default_purchase_price')
                ->where('variations.id', $kit->children_id)
                ->first();

            if (($kit->unit_id == null) && ($kit->unit_group_id_line == null)) {
                $unit = "N/A";
                $unit_kit = "N/A";
            }
            if (($kit->unit_id != null) && ($kit->unit_group_id_line == null)) {
                $name_unit = Unit::select('actual_name')->where('id', $kit->unit_id)->first();
                $unit = $name_unit->actual_name;
                $unit_kit = $kit->unit_id;
            }
            if (($kit->unit_id == null) && ($kit->unit_group_id_line != null)) {
                $name_unit = DB::table('unit_group_lines')
                    ->join('units', 'units.id', '=', 'unit_group_lines.unit_id')
                    ->select('units.actual_name as name')
                    ->where('unit_group_lines.id', $kit->unit_group_id_line)
                    ->first();
                $unit = $name_unit->actual_name;
                $unit_kit = $kit->unit_group_id_line;
            }


            $item = array(
                'kit_line' => $kit->id,
                'clasification' => $product->clasification,
                'variation_id' => $product->variation_id,
                'product_id' => $product->product_id,
                'name_product' => $product->name_product,
                'name_variation' => $product->name_variation,
                'sku' => $product->sku,
                'sub_sku' => $product->sub_sku,
                'brand' => $product->brand,
                'unit_group_id' => $product->unit_group_id,
                'unit_id' => $product->unit_id,
                'quantity' => $kit->quantity,
                'default_purchase_price' => $product->default_purchase_price,
                'unit' => $unit,
                'unit_kit' => $unit_kit,
            );
            array_push($datos, $item);
        }

        return response()->json($datos);
    }

    public function getUnitConf($business_id)
    {
        $conf_units = Business::select('enable_unit_groups')->where('id', $business_id)->first();
        $conf_units = $conf_units->enable_unit_groups;
        return $conf_units;
    }
    public function getUnitPlan($id)
    {
        $plan = Product::select('unit_group_id', 'unit_id')->where('id', $id)->first();
        if ($plan->unit_group_id != null) {
            $plan_product = 'group';
            $unit_group_id = $plan->unit_group_id;
            $datos = array(
                'plan' => $plan_product,
                'unit_group_id' => $unit_group_id,
            );
        } else {
            $unit = Unit::select('actual_name')->where('id', $plan->unit_id)->first();
            $plan_product = 'single';
            $unit_id = $plan->unit_id;
            $name = $unit->actual_name;
            $datos = array(
                'plan' => $plan_product,
                'unit_id' => $unit_id,
                'name' => $name,
            );
        }
        return $datos;
    }

    public function getUnitsFromGroup($id)
    {
        $units = DB::table('unit_group_lines')
            ->join('units', 'units.id', '=', 'unit_group_lines.unit_id')
            ->select('unit_group_lines.id', 'units.actual_name')
            ->where('unit_group_id', $id)
            ->get();
        return $units;
    }
    public function getMeasureFromKitLines($id)
    {
        $measures = KitHasProduct::select('unit_id', 'unit_group_id_line')->where('id', $id)->first();
        if ($measures->unit_id != null) {
            $data = Unit::select('id', 'actual_name')->where('id', $measures->unit->id)->first();
            $datos = array(
                'id' => $data->id,
                'name' => $data->actual_name,
            );
        } else {
            $data = DB::table('unit_group_lines')
                ->join('units', 'units.id', '=', 'unit_group_lines.unit_id')
                ->select('unit_group_lines.id as id', 'units.actual_name as name')
                ->where('unit_group_lines.id', $measures->unit_group_id_line)
                ->first();
            $datos = array(
                'idd' => $data->id,
                'namee' => $data->name,
            );
        }
        return $datos;
    }
    public function viewKit($id)
    {
        $kit = Product::select('name')->where('id', $id)->first();
        $kit_name = $kit->name;

        $kit_lines = KitHasProduct::select('id', 'children_id', 'quantity', 'unit_id', 'unit_group_id_line')->where('parent_id', $id)->get();
        $datos = array();
        foreach ($kit_lines as $kit) {
            $product = DB::table('variations')
                ->leftJoin('products', 'products.id', '=', 'variations.product_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->select('products.clasification', 'variations.id as variation_id', 'products.id as product_id', 'products.name as name_product', 'variations.name as name_variation', 'products.sku', 'variations.sub_sku', 'brands.name as brand', 'products.unit_group_id', 'products.unit_id', 'variations.default_purchase_price')
                ->where('variations.id', $kit->children_id)
                ->first();

            if (($kit->unit_id == null) && ($kit->unit_group_id_line == null)) {
                $unit = "N/A";
                $unit_kit = "N/A";
            }
            if (($kit->unit_id != null) && ($kit->unit_group_id_line == null)) {
                $name_unit = Unit::select('actual_name')->where('id', $kit->unit_id)->first();
                $unit = $name_unit->actual_name;
                $unit_kit = $kit->unit_id;
            }
            if (($kit->unit_id == null) && ($kit->unit_group_id_line != null)) {
                $name_unit = DB::table('unit_group_lines')
                    ->join('units', 'units.id', '=', 'unit_group_lines.unit_id')
                    ->select('units.actual_name as name')
                    ->where('unit_group_lines.id', $kit->unit_group_id_line)
                    ->first();
                $unit = $name_unit->actual_name;
                $unit_kit = $kit->unit_group_id_line;
            }

            $item = array(
                'kit_line' => $kit->id,
                'clasification' => $product->clasification,
                'variation_id' => $product->variation_id,
                'product_id' => $product->product_id,
                'name_product' => $product->name_product,
                'name_variation' => $product->name_variation,
                'sku' => $product->sku,
                'sub_sku' => $product->sub_sku,
                'brand' => $product->brand,
                'unit_group_id' => $product->unit_group_id,
                'unit_id' => $product->unit_id,
                'quantity' => $kit->quantity,
                'default_purchase_price' => $product->default_purchase_price,
                'unit' => $unit,
                'unit_kit' => $unit_kit,
            );
            array_push($datos, $item);
        }
        $data = json_decode(json_encode($datos), FALSE);
        return view('product.view-kit', compact('data', 'kit_name'));
    }

    public function getSalePriceScale($id)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            // Sale price scale list
            $sps = SalePriceScale::leftJoin('variations as v', 'sale_price_scales.variation_id', 'v.id')
                ->where('sale_price_scales.product_id', $id)
                ->where('sale_price_scales.business_id', $business_id)
                ->select('v.name', 'sale_price_scales.from', 'sale_price_scales.to', 'sale_price_scales.price', 'sale_price_scales.id', 'v.id as vid');

            return Datatables::of($sps)
                ->addColumn(
                    'action',
                    function ($row) use ($sps) {
                        return '<button data-href="' . action('ProductController@destroySalePriceScale', [$row->id]) . '" class="btn btn-xs btn-danger delete_sale_price_button"><i class="fa fa-times"></i></button>';
                    }
                )
                ->rawColumns(['action'])
                ->toJson();
        }
    }

    public function storeSalePriceScale(Request $request)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        /*
        $validateData = $request->validate([
            //
        ]);
        */

        if (request()->ajax()) {
            try {
                $input = $request->only(['product_id', 'variation_id', 'from', 'to', 'price']);

                $business_id = $request->session()->get('user.business_id');
                $input['business_id'] = $business_id;

                $sps = SalePriceScale::create($input);

                $output = [
                    'success' => true,
                    'data' => $sps,
                    'msg' => __("product.added_success_sps")
                ];
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

    public function destroySalePriceScale($id)
    {
        if (!auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $sps = SalePriceScale::findOrFail($id);
                $sps->delete();

                $output = [
                    'success' => true,
                    'msg' => __("product.deleted_success_sps")
                ];
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

    public function editSalePriceScale(Request $request, $id)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['variation_id', 'product_id', 'from', 'to', 'price']);
                $sps = SalePriceScale::findOrFail($id);
                $sps->fill($input);
                $sps->save();

                $output = [
                    'success' => true,
                    'msg' => __("product.updated_success_sps")
                ];
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
    public function getHistoryPurchase($id)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = auth()->user()->business_id;
        $product = Product::where('business_id', $business_id)
            ->where('id', $id)
            ->first();
        return view('product.partials.view_history_purchase', compact('product'));
    }

    public function getDataHistoryPurchase($id)
    {
        $purchases = Product::leftJoin('purchase_lines as pl', 'products.id', 'pl.product_id')
            ->leftJoin('transactions as t', 'pl.transaction_id', 't.id')
            ->leftJoin('contacts as c', 't.contact_id', 'c.id')
            ->where('pl.product_id', $id)
            ->where('t.type', 'purchase')
            ->select(
                DB::raw('DATE_FORMAT(t.transaction_date, "%d-%m-%Y") as transaction_date'),
                'c.name',
                'pl.quantity',
                'pl.purchase_price',
                DB::raw('FORMAT(if(pl.quantity * pl.purchase_price = 0, 0.0000,  pl.quantity * pl.purchase_price), 2) as total'),
            );

        return DataTables::of($purchases)
            ->toJson();
    }

    public function getProductsSelect()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }
            $business_id = request()->session()->get('user.business_id');

            $query = Product::where('business_id', $business_id)
                    ->where('clasification', 'product');

            $products = $query->where(function ($query) use ($term) {
                $query->where('products.name', 'like', '%' . $term . '%')
                    ->orWhere('products.sku', 'like', '%' . $term . '%');
            })
                ->select('products.id', 'products.name as text', 'products.sku as sku')
                ->get();
            return json_encode($products);
        }
    }



    public function getPriceList()
    {

        if (!auth()->user()->can('product.import-price-list')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = auth()->user()->business_id;

        $zip_loaded = extension_loaded('zip') ? true : false;
        //Check if zip extension it loaded or not.
        $price_list = SellingPriceGroup::where('business_id', $business_id)->get();
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import'
            ];

            return view('import_opening_stock.index_price_group')
                ->with('notification', compact('output', 'price_list'));
        } else {
            return view('product.import.import_price_list', compact('price_list'));
        }
    }

    public function postPriceList(Request $request)
    {
        if (!auth()->user()->can('product.import-price-list')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            DB::beginTransaction();

            if ($request->hasFile('prices_csv')) {
                $file = $request->file('prices_csv');
                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::TSV)[0];
                unset($imported_data[0]);
                // dd($imported_data);
                $business_id = auth()->user()->business_id;
                $variation_group_price = [];
                $price_group_id = $request->get('group_id');

                if (is_null($price_group_id) || empty($price_group_id)) {
                    return redirect('import-price-list')->with('notification', [
                        'success' => 0,
                        'msg' => 'El precio de venta no puede estar vacio'
                    ]);
                }

                if (count($imported_data)) {
                    foreach ($imported_data as $key => $value) {
                        $row_no = $key + 1;
                        // //Get price default
                        if (!empty(trim($value[1]))) {
                            $price_inc_tax = str_replace(array('$'), " ",  $value[1]);
                            $variation_group_price['price_inc_tax'] = $this->productUtil->num_uf(trim($price_inc_tax));
                        } else {
                            $output = [
                                'success' => 0,
                                // 'msg' => "Price is required in row no. $row_no",
                                'msg' => __('product.price_required', ['row' => $row_no])
                            ];
                            return redirect('import-price-list')->with('notification', $output);
                        }

                        //Check for product SKU, variation id.
                        if (!empty(trim($value[0]))) {
                            $variation_group_price['price_group_id'] = $price_group_id;
                            $products_info = Product::join('variations as v', 'v.product_id', 'products.id')
                                ->where('products.business_id', $business_id)
                                ->where('products.sku', $value[0])
                                ->select('v.id as variation_id')
                                ->get();

                            if (count($products_info)) {
                                foreach ($products_info as $pi) {
                                    $price_list_update = VariationGroupPrice::where('variation_id', $pi->variation_id)
                                        ->where('price_group_id', $variation_group_price['price_group_id'])->first();
                                    if (!empty($price_list_update)) {
                                        //verificar si ya existe uno ,
                                        $price_list_update->variation_id = $pi->variation_id;
                                        $price_list_update->price_group_id = $variation_group_price['price_group_id'];
                                        $price_list_update->price_inc_tax = $variation_group_price['price_inc_tax'];
                                        $price_list_update->update();
                                    } else {
                                        $price_list = new VariationGroupPrice();
                                        $price_list->variation_id = $pi->variation_id;
                                        $price_list->price_group_id = $variation_group_price['price_group_id'];
                                        $price_list->price_inc_tax = $variation_group_price['price_inc_tax'];
                                        $price_list->save();
                                    }
                                }
                            } else {
                                $output = [
                                    'success' => 0,
                                    'msg' => trans('product.sub_sku_exists', ['sub' => $value[0], 'row' => $row_no]),
                                ];
                                return redirect('import-price-list')->with('notification', $output);
                            }
                        } else {
                            $output = [
                                'success' => 0,
                                'msg' => __('product.required_sku', ['row' => $row_no])
                            ];
                            return redirect('import-price-list')->with('notification', $output);
                        }
                    }
                } else {
                    return redirect('import-price-list')->with('notification', [
                        'success' => 0,
                        'msg' => __('product.empty_csv')
                    ]);
                }
            }

            $output = [
                'success' => 1,
                'msg' => __('product.file_imported_successfully'),
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage()
            ];
            return redirect('import-price-list')->with('notification', $output);
        }

        return redirect('import-price-list')->with('status', $output);
    }

    /**
     * Get services list.
     * 
     * @return json
     */
    public function getServices()
    {
        if (request()->ajax()) {
            $term = request()->input('term', '');
            // $selling_price_group_id = request()->input('selling_price_group_id', null);
            $business_id = request()->session()->get('user.business_id');

            $services = Product::join('variations', 'products.id', 'variations.product_id')
                ->where('products.status', 'active')
                ->whereNull('variations.deleted_at')
                ->where('products.clasification', 'service')
                ->with('variations.group_prices')
                ->groupBy('products.id');

            $services->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

            // Include search
            if (! empty($term)) {
                $services->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('products.sku', 'like', '%' . $term . '%');
                    $query->orWhere('variations.sub_sku', 'like', '%' . $term . '%');
                });
            }

            $services->select(
                DB::raw("CONCAT(COALESCE(products.name, ''), ' - ', COALESCE(variations.sub_sku, '')) as text"),
                'variations.id as id'
            )
            ->take(25);

            $result = $services->orderBy('products.name', 'desc')
                ->get();

            return json_encode($result);
        }
    }

    /**
     * Recalculate average product cost based on transactions and update data.
     * 
     * @param  int  $variation_id
     * @return array
     */
    public function recalculateProductCost($variation_id)
    {
        if (! auth()->user()->can('product.recalculate_cost')) {
			abort(403, 'Unauthorized action.');
		}

        $variation = Variation::find($variation_id);
        $product = Product::find($variation->product_id);

        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                \Log::info('--- RECALCULATE COST - VARIATION: ' . $variation_id . ' ---');

                $business_id = request()->session()->get('user.business_id');

                $purchases = Transaction::join('purchase_lines', 'purchase_lines.transaction_id', 'transactions.id')
                    ->whereIn('transactions.type', ['opening_stock', 'purchase'])
                    // ->whereIn('transactions.type', ['opening_stock', 'purchase', 'purchase_transfer', 'sell_return'])
                    ->where('transactions.business_id', $business_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->select('transactions.*')
                    ->orderBy('transactions.transaction_date')
                    ->orderBy('transactions.id')
                    ->groupBy('transactions.id')
                    ->get();

                $tax_rate = 13;

                if (! empty($variation->product->tax)) {
                    $tax_rate = $this->taxUtil->getTaxPercent($variation->product->tax) * 100;
                }

                $stock = 0;
                $purchase_price = 0;

                foreach ($purchases as $purchase) {
                    \Log::info('PURCHASE: ' . $purchase->id);

                    // Allow recalculation of product cost
                    $flag = false;

                    // Purchase date
                    $transaction_date = $purchase->transaction_date;

                    // Add time when transaction_date ends at 00:00:00
                    $hour = substr($transaction_date, 11, 18);

                    if ($hour == '00:00:00' || $hour == '') {
                        $transaction_date = substr($transaction_date, 0, 10) . ' ' . substr($purchase->created_at, 11, 18);
                    }

                    if ($purchase->type == 'purchase' && $purchase->purchase_type == 'international') {
                        $has_apportionment = ApportionmentHasTransaction::where('transaction_id', $purchase->id)->first();

                        if (! empty($has_apportionment)) {
                            $apportionment = Apportionment::find($has_apportionment->apportionment_id);
                            $flag = $apportionment->is_finished == 0 ? false : true;
                        }

                    } else {
                        $flag = true;
                    }

                    if ($flag) {
                        $purchase_lines = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
                            ->where('purchase_lines.transaction_id', $purchase->id)
                            ->where('transactions.business_id', $business_id)
                            ->where('purchase_lines.variation_id', $variation_id)
                            ->select('purchase_lines.*')
                            ->orderBy('purchase_lines.id')
                            ->get();
                            
                        // Check if there are several lines of the same product in the purchase
                        $flag_line = $purchase_lines->count() > 1 ? 1 : 0;

                        foreach ($purchase_lines as $purchase_line) {
                            \Log::info('PURCHASE LINE: ' . $purchase_line->id);

                            $purchase_line_purchase_price = $purchase_line->purchase_price;

                            if ($purchase->type == 'purchase' && $purchase->purchase_type == 'international') {
                                $purchase_line_purchase_price = $purchase_line->purchase_price_inc_tax;
                            }

                            $result = DB::select(
                                'CALL get_stock_before_a_specific_time(?, ?, ?, ?, ?)',
                                [$business_id, $variation_id, $purchase_line->id, $transaction_date, $flag_line]
                            );

                            $stock = $result[0]->stock;

                            if ($purchase_price != $purchase_line->purchase_price) {
                                // Set default purchase price exc. tax
                                $variation->default_purchase_price = (($purchase_price * $stock) + ($purchase_line_purchase_price * $purchase_line->quantity)) / ($stock + $purchase_line->quantity);
                        
                                // Set default purchase price inc. tax
                                $variation->dpp_inc_tax = $this->productUtil->calc_percentage($variation->default_purchase_price, $tax_rate, $variation->default_purchase_price);

                                // Set profit margin
                                $variation->profit_percent = $this->productUtil->get_percent($variation->default_purchase_price, $variation->default_sell_price);

                                $variation->save();

                                $purchase_price = $variation->default_purchase_price;
                            }
                        }
                    }
                }

                DB::commit();

                $output = [
                    'success' => 1,
                    'msg' => __('product.product_cost_calculated_successfully'),
                    'default_purchase_price' => $variation->default_purchase_price,
                    'dpp_inc_tax' => $variation->dpp_inc_tax,
                    'profit_percent' => $variation->profit_percent,
                    'msg_massive' => '(' . $variation->sub_sku . ') ' . $product->name . ' -- ' . __('product.new_cost') . ': $ ' . $variation->default_purchase_price . ' - $ ' . $variation->dpp_inc_tax
                ];

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

                $output = [
                    'success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                    'msg_massive' => '(' . $variation->sub_sku . ') ' . $product->name . ' -- ' . __('product.new_cost') . ': $ ' . $variation->default_purchase_price . ' - $ ' . $variation->dpp_inc_tax
                ];
            }

            return $output;
        }
    }

    /**
     * Get product accounts location
     * @param int $product_id
     * @author Arquímides Martínez
     */
    public function getProductAccountsLocation(Request $request, $product_id) {
        $business_id = $request->user()->business_id;

        //$product_accounts = ProductAccountsLocation::

        $locations = BusinessLocation::forDropdown($business_id);
        $business = Business::find($business_id);
        $product = Product::where('id', $product_id)->select('id', 'name', 'clasification')->first();

        /** inputs */
        $product_accounts_input =
            BusinessLocation::leftJoin('product_accounts_locations as pal', 'business_locations.id', 'pal.location_id')
                ->leftJoin('catalogues as c', 'pal.catalogue_id', 'c.id')
                ->where('pal.type', 'input')
                ->where('pal.product_id', $product_id)
                ->select(
                    'pal.id as pal_id',
                    'pal.type',
                    'pal.location_id',
                    'business_locations.name as location_name',
                    'pal.catalogue_id as account_id',
                    DB::raw('CONCAT(c.code, " ", c.name) as account_name')
                )->get()
                ->toArray();
        
        /** inventory */
        $product_accounts_inventory = collect();
        $product_accounts_cost = collect();

        if($product->clasification == 'product') {
            $product_accounts_inventory =
                BusinessLocation::leftJoin('product_accounts_locations as pal', 'business_locations.id', 'pal.location_id')
                    ->leftJoin('catalogues as c', 'pal.catalogue_id', 'c.id')
                    ->where('pal.type', 'inventory')
                    ->where('pal.product_id', $product_id)
                    ->select(
                        'pal.id as pal_id',
                        'pal.type',
                        'pal.location_id',
                        'business_locations.name as location_name',
                        'pal.catalogue_id as account_id',
                        DB::raw('CONCAT(c.code, " ", c.name) as account_name')
                    )->get()
                    ->toArray();

            $product_accounts_cost =
                BusinessLocation::leftJoin('product_accounts_locations as pal', 'business_locations.id', 'pal.location_id')
                    ->leftJoin('catalogues as c', 'pal.catalogue_id', 'c.id')
                    ->where('pal.type', 'cost')
                    ->where('pal.product_id', $product_id)
                    ->select(
                        'pal.id as pal_id',
                        'pal.type',
                        'pal.location_id',
                        'business_locations.name as location_name',
                        'pal.catalogue_id as account_id',
                        DB::raw('CONCAT(c.code, " ", c.name) as account_name')
                    )->get()
                    ->toArray();
        }

        $creditor_account_code = null;
        if(!is_null($business->accounting_creditor_result_id)){
            $catalogue = Catalogue::find($business->accounting_creditor_result_id);
            $creditor_account_code = !empty($catalogue) ? $catalogue->code : null;
        }

        return view('product.partials.product_accounts_location',
            compact(
                'product',
                'locations',
                'creditor_account_code',
                'product_accounts_input',
                'product_accounts_inventory',
                'product_accounts_cost'));
    }

    /**
     * Post product accounts by location
     * @param int $product_id
     * @author Arquímides Martínez
     */
    public function postProductAccountsLocation(Request $request, $product_id) {
        try {
            DB::beginTransaction();

            $input_accounts = $request->input('input_accounts');
            $inventory_accounts = $request->input('inventory_accounts') ?? [];
            $cost_accounts = $request->input('cost_accounts') ?? [];

            /** store input accounts */
            foreach ($input_accounts as $ia) {
                if(isset($ia['account_id'])) {
                    ProductAccountsLocation::updateOrCreate(
                        ['id' => $ia['pal_id']],
                        [
                            'type' => 'input',
                            'product_id' => $product_id,
                            'location_id' => $ia['location_id'],
                            'catalogue_id' => $ia['account_id']
                        ]
                    );
                }
            }

            /** store inventory accounts */
            foreach ($inventory_accounts as $ia) {
                if(isset($ia['account_id'])) {
                    ProductAccountsLocation::updateOrCreate(
                        ['id' => $ia['pal_id']],
                        [
                            'type' => 'inventory',
                            'product_id' => $product_id,
                            'location_id' => $ia['location_id'],
                            'catalogue_id' => $ia['account_id']
                        ]
                    );
                }
            }

            /** store cost accounts */
            foreach ($cost_accounts as $ca) {
                if(isset($ca['account_id'])) {
                    ProductAccountsLocation::updateOrCreate(
                        ['id' => $ca['pal_id']],
                        [
                            'type' => 'cost',
                            'product_id' => $product_id,
                            'location_id' => $ca['location_id'],
                            'catalogue_id' => $ca['account_id']
                        ]
                    );
                }
            }

            DB::commit();

            $output = [ 'success' => true, 'msg' => __('product.accounts_saved_successfully') ];

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
     * Show the form for recalculate cost.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getRecalculateCost()
    {
        return view('product.recalculate_product_cost');
    }

    /**
     * Recalculate average product cost based on transactions and update data.
     * 
     * @return \Illuminate\Http\Response
     */
    public function postRecalculateCost()
    {
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $start = request()->input('start', '');
                $end = request()->input('end', '');
                
                $variations = Variation::join('products', 'products.id', 'variations.product_id')
                    ->where('products.business_id', $business_id);

                if ($start != '' && $end != '') {
                    $variations = $variations->where('variations.id', '>=', $start)
                        ->where('variations.id', '<=', $end);
                }

                $variations = $variations->pluck('variations.id');

                $output = [
                    'success' => 1,
                    'variations' => $variations
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
    }
}
