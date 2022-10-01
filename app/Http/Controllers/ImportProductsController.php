<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use App\Brands;
use App\Category;
use App\Unit;
use App\TaxRate;
use App\VariationTemplate;
use App\ProductVariation;
use App\Variation;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\MovementType;
use App\Optics\MaterialType;
use App\Transaction;
use App\VariationValueTemplate;
use App\ProductHasSuppliers;
use App\PurchaseLine;
use App\TaxGroup;
use App\Utils\ProductUtil;
use App\Utils\TaxUtil;
use App\Utils\TransactionUtil;
use App\Warehouse;
use Excel;
use DB;

class ImportProductsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $taxUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TaxUtil $taxUtil, TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->taxUtil = $taxUtil;
        $this->transactionUtil = $transactionUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();

        // Tax amount default
        $this->tax_amount_default = 13;
    }

    /**
     * Display import product screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexOld()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = ['success' => 0,
                            'msg' => 'Please install/enable PHP Zip archive for import'
                        ];

            return view('import_products.index')
                ->with('notification', $output);
        } else {
            return view('import_products.index');
        }
    }

    /**
     * Display import product screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        $errors = [];

        // Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => __('messages.install_enable_zip')
            ];

            return view('import_products.index')->with([
                'notification' => $output,
                'errors' => $errors
            ]);

        } else {
            return view('import_products.index', compact('errors'));
        }
    }

    /**
     * Imports the uploaded file to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');
                //$imported_data = Excel::load($file->getRealPath())->noHeading()->skipRows(1)->get()->toArray();
                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::TSV)[0];
                unset($imported_data[0]); // header
                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $default_profit_percent = $request->session()->get('business.default_profit_percent');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';
                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    
                    $row_no = $key + 1;
                    $product_array = [];
                    $product_array['business_id'] = $business_id;
                    $product_array['created_by'] = $user_id;
                    
                    //Add name
                    $product_name = trim($value[0]);
                    if (!empty($product_name)) {
                        $product_array['name'] = $product_name;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Product name is required in row no. $row_no";
                        break;
                    }

                    //Add clasificacion
                    $product_clasification = strtolower(trim($value[34]));
                    if (!empty($product_clasification)) {
                        if (in_array($product_clasification, ['kits', 'product', 'service', 'material'])) {
                            $product_array['clasification'] = $product_clasification;
                        } else {
                            $is_valid =  false;
                            $error_msg = "Invalid value for product clasification in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid =  false;
                        $error_msg = "Product clasification is required in row no. $row_no";
                        break;
                    }
                    
                    //image name
                    $image_name = trim($value[28]);
                    if (!empty($image_name)) {
                        $product_array['image'] = $image_name;
                    } else {
                        $product_array['image'] = '';
                    }

                    $product_array['product_description'] = isset($value[29]) ? $value[29] : null;

                    //Custom fields
                    if (isset($value[30])) {
                        $product_array['product_custom_field1'] = trim($value[30]);
                    } else {
                        $product_array['product_custom_field1'] = '';
                    }
                    if (isset($value[31])) {
                        $product_array['product_custom_field2'] = trim($value[31]);
                    } else {
                        $product_array['product_custom_field2'] = '';
                    }
                    if (isset($value[32])) {
                        $product_array['product_custom_field3'] = trim($value[32]);
                    } else {
                        $product_array['product_custom_field3'] = '';
                    }
                    if (isset($value[33])) {
                        $product_array['product_custom_field4'] = trim($value[33]);
                    } else {
                        $product_array['product_custom_field4'] = '';
                    }

                    /** DAI */
                    if (isset($value[41])) {
                        $product_array['dai'] = $this->productUtil->num_uf(trim($value[41]));
                    } else {
                        $product_array['dai'] = 0;
                    }

                    /** Warranty */
                    if (isset($value[42])) {
                        $product_array['warranty'] = trim($value[42]);
                    } else {
                        $product_array['warranty'] = 0;
                    }

                    //Add enable stock
                    $enable_stock = trim($value[7]);
                    if (in_array($enable_stock, [0,1])) {
                        $product_array['enable_stock'] = $enable_stock;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Invalid value for MANAGE STOCK in row no. $row_no";
                        break;
                    }

                    //Add product type
                    $product_type = strtolower(trim($value[13]));
                    if (in_array($product_type, ['single','variable'])) {
                        $product_array['type'] = $product_type;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Invalid value for PRODUCT TYPE in row no. $row_no";
                        break;
                    }

                    //Add unit
                    $unit_name = trim($value[2]);
                    if (!empty($unit_name)) {
                        $unit = Unit::where('business_id', $business_id)
                                    ->where(function ($query) use ($unit_name) {
                                        $query->where('short_name', $unit_name)
                                            ->orWhere('actual_name', $unit_name);
                                    })->first();
                        if (!empty($unit)) {
                            $product_array['unit_id'] = $unit->id;
                        } else {
                            $is_valid = false;
                            $error_msg = "UNIT not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid =  false;
                        $error_msg = "UNIT is required in row no. $row_no";
                        break;
                    }

                    //Add barcode type
                    $barcode_type = strtoupper(trim($value[6]));
                    if (empty($barcode_type)) {
                        $product_array['barcode_type'] = 'C128';
                    } elseif (array_key_exists($barcode_type, $this->barcode_types)) {
                        $product_array['barcode_type'] = $barcode_type;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for BARCODE TYPE in row no. $row_no";
                        break;
                    }

                    //Add Tax
                    $tax_name = trim($value[11]);
                    $tax_amount = 13;
                    if (!empty($tax_name)) {
                        $tax = TaxGroup::where('business_id', $business_id)
                                        ->where('description', $tax_name)
                                        ->first();
                        if (!empty($tax)) {
                            $product_array['tax'] = $tax->id;
                            $tax_amount = ($this->taxUtil->getTaxPercent($tax->id)) * 100;
                        } else {
                            $is_valid = false;
                            $error_msg = "Invalid value for APPLICABLE TAX in row no. $row_no";
                            break;
                        }
                    }

                    //Add tax type
                    $tax_type = strtolower(trim($value[12]));
                    if (in_array($tax_type, ['inclusive', 'exclusive'])) {
                        $product_array['tax_type'] = $tax_type;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for Selling Price Tax Type in row no. $row_no";
                        break;
                    }

                    //Add alert quantity
                    $product_array['alert_quantity'] = ($product_array['enable_stock'] == 1) ?
                    $this->productUtil->num_uf($value[8]) : 0;

                    //Add brand
                    //Check if brand exists else create new
                    $brand_name = trim($value[1]);
                    if (!empty($brand_name)) {
                        $brand = Brands::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $brand_name],
                            ['created_by' => $user_id]
                        );
                        $product_array['brand_id'] = $brand->id;
                    }

                    //Add Category
                    //Check if category exists else create new
                    $category_name = trim($value[3]);
                    if (!empty($category_name)) {
                        $category = Category::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $category_name],
                            ['created_by' => $user_id, 'parent_id' => 0]
                        );
                        $product_array['category_id'] = $category->id;
                    }

                    //Add Sub-Category
                    $sub_category_name = trim($value[4]);
                    if (!empty($sub_category_name)) {
                        $sub_category = Category::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $sub_category_name],
                            ['created_by' => $user_id, 'parent_id' => $category->id]
                        );
                        $product_array['sub_category_id'] = $sub_category->id;
                    }

                    //Add SKU
                    $sku = trim($value[5]);
                    if (!empty($sku)) {
                        $product_array['sku'] = $sku;
                        //Check if product with same SKU already exist
                        $is_exist = Product::where('sku', $product_array['sku'])
                                        ->where('business_id', $business_id)
                                        ->exists();
                        if ($is_exist) {
                            $is_valid = false;
                            $error_msg = "$sku SKU already exist in row no. $row_no";
                            break;
                        }
                    } else {
                        $product_array['sku'] = ' ';
                    }

                    //Add product expiry
                    $expiry_period = $this->productUtil->num_uf(trim($value[9]));
                    $expiry_period_type = strtolower(trim($value[10]));
                    if (!empty($expiry_period) && in_array($expiry_period_type, ['months', 'days'])) {
                        $product_array['expiry_period'] = $expiry_period;
                        $product_array['expiry_period_type'] = $expiry_period_type;
                    } else {
                        //If Expiry Date is set then make expiry_period 12 months.
                        if (!empty($value[22])) {
                            $product_array['expiry_period'] = 12;
                            $product_array['expiry_period_type'] = 'months';
                        }
                    }

                    //Enable IMEI or Serial Number
                    $enable_sr_no = trim($value[23]);
                    if (in_array($enable_sr_no, [0,1])) {
                        $product_array['enable_sr_no'] = $enable_sr_no;
                    } elseif (empty($enable_sr_no)) {
                        $product_array['enable_sr_no'] = 0;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Invalid value for ENABLE IMEI OR SERIAL NUMBER  in row no. $row_no";
                        break;
                    }

                    //Product supplier
                    if(isset($value[35])){
                        $supplier = Contact::where('business_id', $business_id)
                            ->where('contact_id', trim($value[35]))
                            ->first();

                        if (empty($supplier)) {
                            $is_valid = false;
                            $error_msg = "No supplier with contact id '$value[35]' found in row no. $row_no";
                            break;
                        }

                        $product_array['supplier']['supplier_id'] = $supplier->id;
                        $product_array['supplier']['catalog'] = trim($value[36]);
                        $product_array['supplier']['uxc'] = trim($value[37]);
                        $product_array['supplier']['weight'] = trim($value[38]);
                        $product_array['supplier']['dimensions'] = trim($value[39]);
                        $product_array['supplier']['custom_field'] = trim($value[40]);
                    }

                    //Weight
                    if (isset($value[24])) {
                        $product_array['weight'] = trim($value[24]);
                    } else {
                        $product_array['weight'] = '';
                    }

                    if ($product_array['type'] == 'single') {
                        //Calculate profit margin
                        $profit_margin = trim($value[18]);
                        if (empty($profit_margin)) {
                            $profit_margin = $default_profit_percent;
                        } else {
                            $profit_margin = $this->productUtil->num_uf(trim($value[18]));
                        }
                        $product_array['variation']['profit_percent'] = $profit_margin;

                        //Calculate purchase price
                        $dpp_inc_tax = trim($value[16]);
                        $dpp_exc_tax = trim($value[17]);
                        if (empty($dpp_inc_tax) && empty($dpp_exc_tax)) {
                            $is_valid = false;
                            $error_msg = "PURCHASE PRICE is required in row no. $row_no";
                            break;
                        } else {
                            $dpp_inc_tax = !empty($dpp_inc_tax) ? $this->productUtil->num_uf($dpp_inc_tax) : 0;
                            $dpp_exc_tax = !empty($dpp_exc_tax) ? $this->productUtil->num_uf($dpp_exc_tax) : 0;
                        }

                        //Calculate Selling price
                        $selling_price = !empty(trim($value[19])) ? $this->productUtil->num_uf(trim($value[19])) : 0 ;

                        //Calculate product prices
                        $product_prices = $this->calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $profit_margin);
                        
                        //Assign Values
                        $product_array['variation']['dpp_inc_tax'] = $product_prices['dpp_inc_tax'];
                        $product_array['variation']['dpp_exc_tax'] = $product_prices['dpp_exc_tax'];
                        $product_array['variation']['dsp_inc_tax'] = $product_prices['dsp_inc_tax'];
                        $product_array['variation']['dsp_exc_tax'] = $product_prices['dsp_exc_tax'];
                        
                        //Opening stock
                        if (!empty($value[20]) && $enable_stock == 1) {
                            $product_array['opening_stock_details']['quantity'] = $this->productUtil->num_uf(trim($value[20]));

                            /** Change business location by warehouse */
                            if (!empty(trim($value[21]))) {
                                $location_name = trim($value[21]);
                                /* $location = BusinessLocation::where('name', $location_name)
                                                            ->where('business_id', $business_id)
                                                            ->first(); */
                                $location =  Warehouse::where('code', $location_name)
                                    ->where('business_id', $business_id)
                                    ->first();
                                if (!empty($location)) {
                                    $product_array['opening_stock_details']['location_id'] = $location->id;
                                } else {
                                    $is_valid = false;
                                    $error_msg = "No location with name '$location_name' found in row no. $row_no";
                                    break;
                                }
                            } else {
                                /* $location = BusinessLocation::where('business_id', $business_id)->first(); */
                                $location = Warehouse::where('business_id', $business_id)->first();
                                $product_array['opening_stock_details']['location_id'] = $location->id;
                            }

                            $product_array['opening_stock_details']['expiry_date'] = null;

                            //Stock expiry date
                            if (!empty($value[22])) {
                                $product_array['opening_stock_details']['exp_date'] = \Carbon::createFromFormat('m-d-Y', trim($value[22]))->format('Y-m-d');
                            } else {
                                $product_array['opening_stock_details']['exp_date'] = null;
                            }
                        }
                    } elseif ($product_array['type'] == 'variable') {
                        $variation_name = trim($value[14]);
                        if (empty($variation_name)) {
                            $is_valid = false;
                            $error_msg = "VARIATION NAME is required in row no. $row_no";
                            break;
                        }
                        $variation_values_string = trim($value[15]);
                        if (empty($variation_values_string)) {
                            $is_valid = false;
                            $error_msg = "VARIATION VALUES are required in row no. $row_no";
                            break;
                        }

                        $dpp_inc_tax_string = trim($value[16]);
                        $dpp_exc_tax_string = trim($value[17]);
                        $selling_price_string = trim($value[19]);
                        $profit_margin_string = trim($value[18]);

                        if (empty($dpp_inc_tax_string) && empty($dpp_exc_tax_string)) {
                            $is_valid = false;
                            $error_msg = "PURCHASE PRICE is required in row no. $row_no";
                            break;
                        }

                        //Variation values
                        $variation_values = array_map('trim', explode(
                            '|',
                            $variation_values_string
                        ));

                        //Map Purchase price with variation values
                        $dpp_inc_tax = [];
                        if (!empty($dpp_inc_tax_string)) {
                            $dpp_inc_tax = array_map([$this->productUtil, 'num_uf'], array_map('trim', explode(
                                '|',
                                $dpp_inc_tax_string
                            )));
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $dpp_inc_tax[$k] = 0;
                            }
                        }
                        
                        $dpp_exc_tax = [];
                        if (!empty($dpp_exc_tax_string)) {
                            $dpp_exc_tax = array_map([$this->productUtil, 'num_uf'], array_map('trim', explode(
                                '|',
                                $dpp_exc_tax_string
                            )));
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $dpp_exc_tax[$k] = 0;
                            }
                        }

                        //Map Selling price with variation values
                        $selling_price = [];
                        if (!empty($selling_price_string)) {
                            $selling_price = array_map(
                                [$this->productUtil, 'num_uf'],
                                array_map('trim', explode(
                                    '|',
                                    $selling_price_string
                                ))
                            );
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $selling_price[$k] = 0;
                            }
                        }

                        //Map profit margin with variation values
                        $profit_margin = [];
                        if (!empty($profit_margin_string)) {
                            $profit_margin = array_map(
                                [$this->productUtil, 'num_uf'],
                                array_map('trim', explode(
                                    '|',
                                    $profit_margin_string
                                ))
                            );
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $profit_margin[$k] = $default_profit_percent;
                            }
                        }

                        //Check if length of prices array is equal to variation values array length
                        $array_lengths_count = [count($variation_values), count($dpp_inc_tax), count($dpp_exc_tax), count($selling_price), count($profit_margin)];
                        $same = array_count_values($array_lengths_count);

                        if (count($same) != 1) {
                            $is_valid = false;
                            $error_msg = "Prices mismatched with VARIATION VALUES in row no. $row_no";
                            break;
                        }
                        $product_array['variation']['name'] = $variation_name;

                        //Check if variation exists or create new
                        $variation = $this->productUtil->createOrNewVariation($business_id, $variation_name);
                        $product_array['variation']['variation_template_id'] = $variation->id;

                        foreach ($variation_values as $k => $v) {
                            $variation_prices = $this->calculateVariationPrices($dpp_exc_tax[$k], $dpp_inc_tax[$k], $selling_price[$k], $tax_amount, $tax_type, $profit_margin[$k]);

                            //get variation value
                            $variation_value = $variation->values->filter(function ($item) use ($v) {
                                  return strtolower($item->name) == strtolower($v);
                            })->first();

                            if (empty($variation_value)) {
                                $variation_value = VariationValueTemplate::create([
                                  'name' => $v,
                                  'variation_template_id' => $variation->id
                                ]);
                            }
                            
                            //Assign Values
                            $product_array['variation']['variations'][] = [
                                'value' => $v,
                                'variation_value_id' => $variation_value->id,
                                'default_purchase_price' => $variation_prices['dpp_exc_tax'],
                                'dpp_inc_tax' => $variation_prices['dpp_inc_tax'],
                                'profit_percent' => $this->productUtil->num_f($profit_margin[$k]),
                                'default_sell_price' => $variation_prices['dsp_exc_tax'],
                                'sell_price_inc_tax' => $variation_prices['dsp_inc_tax']
                            ];
                        }

                        //Opening stock
                        if (!empty($value[20]) && $enable_stock == 1) {
                            $variation_os = array_map([$this->productUtil, 'num_uf'], array_map('trim', explode('|', $value[20])));

                            //$product_array['opening_stock_details']['quantity'] = $variation_os;

                            //Check if count of variation and opening stock is matching or not.
                            if (count($product_array['variation']['variations']) != count($variation_os)) {
                                $is_valid = false;
                                $error_msg = "Opening Stock mismatched with VARIATION VALUES in row no. $row_no";
                                break;
                            }

                            /** Change business location by warehouse */
                            if (!empty(trim($value[21]))) {
                                $location_name = trim($value[21]);
                                /* $location = BusinessLocation::where('name', $location_name)
                                                            ->where('business_id', $business_id)
                                                            ->first(); */
                                $location =  Warehouse::where('code', $location_name)
                                    ->where('business_id', $business_id)
                                    ->first();
                                if (empty($location)) {
                                    $is_valid = false;
                                    $error_msg = "No location with name '$location_name' found in row no. $row_no";
                                    break;
                                }
                            } else {
                                /* $location = BusinessLocation::where('business_id', $business_id)->first(); */
                                $location = Warehouse::where('business_id', $business_id)->first();
                            }
                            $product_array['variation']['opening_stock_location'] = $location->id;

                            foreach ($variation_os as $k => $v) {
                                $product_array['variation']['variations'][$k]['opening_stock'] = $v;
                                $product_array['variation']['variations'][$k]['opening_stock_exp_date'] = null;
                                
                                if (!empty($value[22])) {
                                    $product_array['variation']['variations'][$k]['opening_stock_exp_date'] = \Carbon::createFromFormat('m-d-Y', trim($value[22]))->format('Y-m-d');
                                } else {
                                    $product_array['variation']['variations'][$k]['opening_stock_exp_date'] = null;
                                }
                            }
                        }
                    }
                    //Assign to formated array
                    $formated_data[] = $product_array;
                }

                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }

                if (!empty($formated_data)) {
                    foreach ($formated_data as $index => $product_data) {
                        $variation_data = $product_data['variation'];
                        //return $product_data['supplier'];
                        $supplier = [];
                        if(!empty($product_data['supplier'])){
                            $supplier = $product_data['supplier'];
                            unset($product_data['supplier']);
                        }
                        
                        unset($product_data['variation']);

                        $opening_stock = null;
                        if (!empty($product_data['opening_stock_details'])) {
                            $opening_stock = $product_data['opening_stock_details'];
                        }
                        if (isset($product_data['opening_stock_details'])) {
                            unset($product_data['opening_stock_details']);
                        }

                        //Create new product
                        $product = Product::create($product_data);
                        //If auto generate sku generate new sku
                        if ($product->sku == ' ') {
                            $sku = $this->productUtil->generateProductSku($product->id);
                            $product->sku = $sku;
                            $product->save();
                        }

                        /** create product supplier */
                        if(count($supplier) > 0){
                            $product_supplier = new ProductHasSuppliers;
                            $product_supplier->product_id = $product->id;
                            $product_supplier->contact_id = $supplier['supplier_id'];
                            $product_supplier->catalogue = $supplier['catalog'];
                            $product_supplier->uxc = $supplier['uxc'];
                            $product_supplier->weight = $supplier['weight'];
                            $product_supplier->dimensions = $supplier['dimensions'];
                            $product_supplier->custom_field = $supplier['custom_field'];
                            $product_supplier->save();
                        }

                        //Rack, Row & Position.
                        /** $index + 1 because row 0 (header) is removed */
                        $this->rackDetails(
                            $imported_data[$index + 1][25],
                            $imported_data[$index + 1][26],
                            $imported_data[$index + 1][27],
                            $business_id,
                            $product->id,
                            $index+1
                        );

                        //Create single product variation
                        if ($product->type == 'single') {
                            $this->productUtil->createSingleProductVariation(
                                $product,
                                $product->sku,
                                $variation_data['dpp_exc_tax'],
                                $variation_data['dpp_inc_tax'],
                                $variation_data['profit_percent'],
                                $variation_data['dsp_exc_tax'],
                                $variation_data['dsp_inc_tax']
                            );
                            if (!empty($opening_stock)) {
                                $this->addOpeningStock($opening_stock, $product, $business_id);
                            }
                        } elseif ($product->type == 'variable') {
                            //Create variable product variations
                            $this->productUtil->createVariableProductVariations(
                                $product,
                                [$variation_data],
                                $business_id
                            );

                            if (!empty($value[20]) && $enable_stock == 1) {
                                $this->addOpeningStockForVariable($variation_data, $product, $business_id);
                            }
                        }
                    }
                }
            }
            
            $output = ['success' => 1,
                            'msg' => __('product.file_imported_successfully')
                        ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => $e->getMessage()
                        ];
            return redirect('import-products')->with('notification', $output);
        }

        return redirect('import-products')->with('status', $output);
    }

    private function calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $margin)
    {

        //Calculate purchase prices
        if ($dpp_inc_tax == 0) {
            $dpp_inc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $tax_amount,
                $dpp_exc_tax
            );
        }

        if ($dpp_exc_tax == 0) {
            $dpp_exc_tax = $this->productUtil->calc_percentage_base($dpp_inc_tax, $tax_amount);
        }

        if ($selling_price != 0) {
            if ($tax_type == 'inclusive') {
                $dsp_inc_tax = $selling_price;
                $dsp_exc_tax = $this->productUtil->calc_percentage_base(
                    $dsp_inc_tax,
                    $tax_amount
                );
            } elseif ($tax_type == 'exclusive') {
                $dsp_exc_tax = $selling_price;
                $dsp_inc_tax = $this->productUtil->calc_percentage(
                    $selling_price,
                    $tax_amount,
                    $selling_price
                );
            }
        } else {
            $dsp_exc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $margin,
                $dpp_exc_tax
            );
            $dsp_inc_tax = $this->productUtil->calc_percentage(
                $dsp_exc_tax,
                $tax_amount,
                $dsp_exc_tax
            );
        }

        return [
            'dpp_exc_tax' => $this->productUtil->num_f($dpp_exc_tax, false, 6),
            'dpp_inc_tax' => $this->productUtil->num_f($dpp_inc_tax, false, 6),
            'dsp_exc_tax' => $this->productUtil->num_f($dsp_exc_tax, false, 6),
            'dsp_inc_tax' => $this->productUtil->num_f($dsp_inc_tax, false, 6)
        ];
    }

    /**
     * Adds opening stock of a single product
     *
     * @param array $opening_stock
     * @param obj $product
     * @param int $business_id
     * @return void
     */
    private function addOpeningStock($opening_stock, $product, $business_id)
    {

        $user_id = request()->session()->get('user.id');
        
        $variation = Variation::where('product_id', $product->id)
            ->first();

        $total_before_tax = $opening_stock['quantity'] * $variation->dpp_inc_tax;

        $transaction_date = request()->session()->get("financial_year.start");
        $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();

        /** Gets business location from warehouse */
        $warehouse = Warehouse::find($opening_stock['location_id']);

        //Add opening stock transaction
        $transaction = Transaction::create(
            [
                                'type' => 'opening_stock',
                                'opening_stock_product_id' => $product->id,
                                'status' => 'received',
                                'business_id' => $business_id,
                                'transaction_date' => $transaction_date,
                                'total_before_tax' => $total_before_tax,
                                // 'location_id' => $opening_stock['location_id'],
                                'location_id' => $warehouse->business_location_id,
                                'warehouse_id' => $warehouse->id,
                                'final_total' => $total_before_tax,
                                'payment_status' => 'paid',
                                'created_by' => $user_id
                            ]
        );
        //Get product tax
        $tax_percent = !empty($product->product_tax->amount) ? $product->product_tax->amount : 0;
        $tax_id = !empty($product->product_tax->id) ? $product->product_tax->id : null;

        $item_tax = $this->productUtil->calc_percentage($variation->default_purchase_price, $tax_percent);

        //Create purchase line
        $transaction->purchase_lines()->create([
                        'product_id' => $product->id,
                        'variation_id' => $variation->id,
                        'quantity' => $opening_stock['quantity'],
                        'item_tax' => $item_tax,
                        'tax_id' => $tax_id,
                        'pp_without_discount' => $variation->default_purchase_price,
                        'purchase_price' => $variation->default_purchase_price,
                        'purchase_price_inc_tax' => $variation->dpp_inc_tax,
                        'exp_date' => !empty($opening_stock['exp_date']) ? $opening_stock['exp_date'] : null
                    ]);
        //Update variation location details
        // $this->productUtil->updateProductQuantity($opening_stock['location_id'], $product->id, $variation->id, $opening_stock['quantity']);
        $this->productUtil->updateProductQuantity($warehouse->business_location_id, $product->id, $variation->id, $opening_stock['quantity'], 0, null, $warehouse->id);

        // Data to create or update kardex lines
        $lines = PurchaseLine::where('transaction_id', $transaction->id)->get();

        $movement_type = MovementType::where('name', 'opening_stock')
            ->where('type', 'input')
            ->where('business_id', $business_id)
            ->first();

        // Check if movement type is set else create it
        if (empty($movement_type)) {
            $movement_type = MovementType::create([
                'name' => 'opening_stock',
                'type' => 'input',
                'business_id' => $business_id
            ]);
        }

        // Store kardex
        $this->transactionUtil->createOrUpdateInputLines(
            $movement_type,
            $transaction,
            'OS' . $transaction->id,
            $lines
        );
    }


    private function addOpeningStockForVariable($variations, $product, $business_id)
    {
        $user_id = request()->session()->get('user.id');

        $transaction_date = request()->session()->get("financial_year.start");
        $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();

        $total_before_tax = 0;

        /** Gets business location from warehouse */
        $warehouse = Warehouse::find($variations['opening_stock_location']);
        //$location_id = $variations['opening_stock_location'];
        $location_id = $warehouse->business_location_id;

        if (isset($variations['variations'][0]['opening_stock'])) {
            //Add opening stock transaction
            $transaction = Transaction::create(
                [
                                'type' => 'opening_stock',
                                'opening_stock_product_id' => $product->id,
                                'status' => 'received',
                                'business_id' => $business_id,
                                'transaction_date' => $transaction_date,
                                'total_before_tax' => $total_before_tax,
                                'location_id' => $location_id,
                                'warehouse_id' => $warehouse->id,
                                'final_total' => $total_before_tax,
                                'payment_status' => 'paid',
                                'created_by' => $user_id
                            ]
            );

            foreach ($variations['variations'] as $variation_os) {
                if (!empty($variation_os['opening_stock'])) {
                    $variation = Variation::where('product_id', $product->id)
                                    ->where('name', $variation_os['value'])
                                    ->first();
                    if (!empty($variation)) {
                        $opening_stock = [
                            'quantity' => $variation_os['opening_stock'],
                            'exp_date' => $variation_os['opening_stock_exp_date'],
                        ];

                        $total_before_tax = $total_before_tax + ($variation_os['opening_stock'] * $variation->dpp_inc_tax);
                    }

                    //Get product tax
                    $tax_percent = !empty($product->product_tax->amount) ? $product->product_tax->amount : 0;
                    $tax_id = !empty($product->product_tax->id) ? $product->product_tax->id : null;

                    $item_tax = $this->productUtil->calc_percentage($variation->default_purchase_price, $tax_percent);

                    //Create purchase line
                    $transaction->purchase_lines()->create([
                                    'product_id' => $product->id,
                                    'variation_id' => $variation->id,
                                    'quantity' => $opening_stock['quantity'],
                                    'item_tax' => $item_tax,
                                    'tax_id' => $tax_id,
                                    'purchase_price' => $variation->default_purchase_price,
                                    'purchase_price_inc_tax' => $variation->dpp_inc_tax,
                                    'exp_date' => !empty($opening_stock['exp_date']) ? $opening_stock['exp_date'] : null
                                ]);
                    //Update variation location details
                    // $this->productUtil->updateProductQuantity($location_id, $product->id, $variation->id, $opening_stock['quantity']);
                    $this->productUtil->updateProductQuantity($location_id, $product->id, $variation->id, $opening_stock['quantity'], 0, null, $warehouse->id);
                }
            }

            $transaction->total_before_tax = $total_before_tax;
            $transaction->final_total = $total_before_tax;
            $transaction->save();
        }
    }

    private function rackDetails($rack_value, $row_value, $position_value, $business_id, $product_id, $row_no)
    {

        if (!empty($rack_value) || !empty($row_value) || !empty($position_value)) {
            $locations = BusinessLocation::forDropdown($business_id);
            $loc_count = count($locations);

            $racks = explode('|', $rack_value);
            $rows = explode('|', $row_value);
            $position = explode('|', $position_value);

            if (count($racks) > $loc_count) {
                $error_msg = "Invalid value for RACK in row no. $row_no";
                throw new \Exception($error_msg);
            }

            if (count($rows) > $loc_count) {
                $error_msg = "Invalid value for ROW in row no. $row_no";
                throw new \Exception($error_msg);
            }

            if (count($position) > $loc_count) {
                $error_msg = "Invalid value for POSITION in row no. $row_no";
                throw new \Exception($error_msg);
            }

            $rack_details = [];
            $counter = 0;
            foreach ($locations as $key => $value) {
                $rack_details[$key]['rack'] = isset($racks[$counter]) ? $racks[$counter] : '';
                $rack_details[$key]['row'] = isset($rows[$counter]) ? $rows[$counter] : '';
                $rack_details[$key]['position'] = isset($position[$counter]) ? $position[$counter] : '';
                $counter += 1;
            }

            if (!empty($rack_details)) {
                $this->productUtil->addRackDetails($business_id, $product_id, $rack_details);
            }
        }
    }

    /**
     * Check file to importer.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkFile(Request $request)
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            // Errors list
            $error_msg = [];

            // Product lines
            $products = [];

            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');
            $default_profit_percent = $request->session()->get('business.default_profit_percent');
            $exception = 0;

            if ($request->hasFile('products_xlsx')) {
                $file = $request->file('products_xlsx');

                /**
                 * ------------------------------------------------------------
                 * PRODUCT SHEET
                 * ------------------------------------------------------------
                 */

                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[1];

                // Removing the header
                unset($imported_data[0]);
                unset($imported_data[1]);
                unset($imported_data[2]);
                unset($imported_data[3]);

                // Columns number
                $col_no = 17;

                // Default data
                $type = 'single';
                $enable_stock = 1;
                $barcode_type = 'C128';

                // Process file
                foreach ($imported_data as $key => $value) {
                    // Check columns number
                    if (count($value) != $col_no) {
                        $error_line = [
                            'row' => 'N/A',
                            'sheet' => __('product.products'),
                            'msg' => __('product.number_of_columns_mismatch', ['number' => $col_no - 1])
                        ];

                        array_push($error_msg, $error_line);
                    }

                    // Row number
                    $row_no = $key + 1;

                    // Row
                    $row = [
                        'product_name' => trim($value[0]),
                        'sku' => trim($value[1]),
                        'status' => trim($value[2]),
                        'brand_name' => trim($value[3]),
                        'unit_name' => trim($value[4]),
                        'quantity' => trim($value[5]),
                        'category_name' => trim($value[6]),
                        'sub_category_name' => trim($value[7]),
                        'min_inventory' => trim($value[8]),
                        'has_warranty' => trim($value[9]),
                        'warranty' => trim($value[10]),
                        'sales_tax' => trim($value[11]),
                        'applied_tax' => trim($value[12]),
                        'cost_without_tax' => trim($value[13]),
                        'sales_price_without_tax' => trim($value[14]),
                        'clasification' => trim($value[15]),
                        'product_description' => null,
                    ];

                    // Default data
                    $default_data = [
                        'type' => $type,
                        'enable_stock' => $enable_stock,
                        'barcode_type' => $barcode_type
                    ];

                    $result = $this->checkRow($row, $row_no, $default_data);

                    // Product result
                    array_push($products, $result['product']);

                    // Error messages result
                    foreach ($result['error_msg'] as $item) {
                        $item['sheet'] = __('product.products');
                        array_push($error_msg, $item);
                    }
                }

                /**
                 * ------------------------------------------------------------
                 * SERVICE SHEET
                 * ------------------------------------------------------------
                 */

                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[2];

                // Removing the header
                unset($imported_data[0]);
                unset($imported_data[1]);
                unset($imported_data[2]);
                unset($imported_data[3]);

                // Columns number
                $col_no = 14;

                // Default data
                $type = 'single';
                $enable_stock = 0;
                $barcode_type = 'C128';

                // Process file
                foreach ($imported_data as $key => $value) {
                    // Check columns number
                    if (count($value) != $col_no) {
                        $error_line = [
                            'row' => 'N/A',
                            'sheet' => __('product.services'),
                            'msg' => __('product.number_of_columns_mismatch', ['number' => $col_no - 1])
                        ];

                        array_push($error_msg, $error_line);
                    }

                    // Row number
                    $row_no = $key + 1;

                    // Row
                    $row = [
                        'product_name' => trim($value[0]),
                        'sku' => trim($value[1]),
                        'status' => trim($value[2]),
                        'category_name' => trim($value[3]),
                        'sub_category_name' => trim($value[4]),
                        'product_description' => trim($value[5]),
                        'has_warranty' => trim($value[6]),
                        'warranty' => trim($value[7]),
                        'sales_tax' => trim($value[8]),
                        'applied_tax' => trim($value[9]),
                        'cost_without_tax' => trim($value[10]),
                        'sales_price_without_tax' => trim($value[11]),
                        'clasification' => trim($value[12]),
                        'brand_name' => null,
                        'unit_name' => null,
                        'quantity' => null,
                        'min_inventory' => null,
                    ];

                    // Default data
                    $default_data = [
                        'type' => $type,
                        'enable_stock' => $enable_stock,
                        'barcode_type' => $barcode_type
                    ];

                    $result = $this->checkRow($row, $row_no, $default_data);

                    // Product result
                    array_push($products, $result['product']);

                    // Error messages result
                    foreach ($result['error_msg'] as $item) {
                        $item['sheet'] = __('product.services');
                        array_push($error_msg, $item);
                    }
                }
            }

            $status = [
                'success' => 1,
                'msg' => __('customer.successful_verified_file')
            ];

        } catch (\Exception $e) {
            $exception = 1;

            $error_line = [
                'row' => 'N/A',
                'msg' => $e->getMessage()
            ];

            array_push($error_msg, $error_line);

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $status = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        // Session variables 
        session(['products' => $products]);

        $errors = $error_msg;

        if (count($error_msg) == 0 && $exception == 0) {
            $flag = true;
        } else {
            $flag = false;
        }

        return view('import_products.index')
            ->with(compact(
                'errors',
                'status',
                'flag',
                'exception'
            ));

        return redirect('import-products')->with('status', $status);
    }

    /**
     * Check row data.
     * 
     * @param  array  $row
     * @param  int  $row_no
     * @param  array  $default_data
     * @return array
     */
    public function checkRow($row, $row_no, $default_data = null)
    {
        $product = [
            // Product
            'product_name' => null,
            'business_id' => null,
            'type' => null,
            'unit_id' => null,
            'brand_name' => null,
            'category_name' => null,
            'sub_category_name' => null,
            'tax' => null,
            'tax_type' => null,
            'enable_stock' => null,
            'alert_quantity' => null,
            'sku' => null,
            'barcode_type' => null,
            'product_description' => null,
            'warranty' => null,
            'discount_card' => null,
            'add_products' => null,
            'status' => null,
            'clasification' => null,
            'has_warranty' => null,
            'created_by' => null,

            // Variation
            'variation_name' =>  null,
            'product_id' =>  null,
            'sub_sku' =>  null,
            'product_variation_id' =>  null,
            'variation_value_id' =>  null,
            'default_purchase_price' =>  null,
            'dpp_inc_tax' =>  null,
            'profit_percent' =>  null,
            'default_sell_price' =>  null,
            'sell_price_inc_tax' =>  null,

            // Opening stock
            'quantity' => null,
        ];

        // Errors list
        $error_msg = [];

        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');

        // ----- CLASIFICATION -----

        // Check empty
        if (! empty($row['clasification'])) {
            $clasification = mb_strtolower($row['clasification']);

            // Check invalid value
            if (in_array($clasification, ['product', 'service', 'kits', 'material', 'producto', 'servicio'])) {
                if (in_array($clasification, ['product', 'producto'])) {
                    $product['clasification'] = 'product';

                } else if (in_array($clasification, ['service', 'servicio'])) {
                    $product['clasification'] = 'service';

                } else {
                    $product['clasification'] = $clasification;
                }
                
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.clasification_invalid')
                ];

                array_push($error_msg, $error_line);
            }

        } else {
            $error_line = [
                'row' => $row_no,
                'msg' => __('product.clasification_empty')
            ];

            array_push($error_msg, $error_line);
        }

        // ----- NAME -----

        $name_error = false;

        // Check empty
        if (empty($row['product_name'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('product.product_name_empty')
            ];

            array_push($error_msg, $error_line);

            $name_error = true;

        } else {
            // Check length
            if (strlen($row['product_name']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.product_name_length')
                ];
    
                array_push($error_msg, $error_line);
    
                $name_error = true;
            }
        }

        if (! $name_error) {
            $product['product_name'] = $row['product_name'];
        }

        // ----- BUSINESS ID -----
        $product['business_id'] = $business_id;

        // ----- TYPE -----
        $product['type'] = is_null($default_data) ? null : (isset($default_data['type']) ? $default_data['type'] : null);

        // ----- UNIT ID -----

        if ($product['clasification'] == 'product') {
            $unit_error = false;
    
            // Check empty
            if (empty($row['unit_name'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.unit_empty')
                ];
    
                array_push($error_msg, $error_line);
    
                $unit_error = true;
                
            } else {
                // Check length
                if (strlen($row['unit_name']) > 50) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.unit_length')
                    ];
    
                    array_push($error_msg, $error_line);
    
                    $unit_error = true;
                }
    
                // Check exist
                $unit = Unit::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(short_name) = UPPER(?)', [$row['unit_name']])
                            ->orWhereRaw('UPPER(actual_name) = UPPER(?)', [$row['unit_name']]);
                    })
                    ->first();
    
                if (empty($unit)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.unit_exist')
                    ];
    
                    array_push($error_msg, $error_line);
    
                    $unit_error = true;
                }
            }
    
            if (! $unit_error) {
                $product['unit_id'] = $unit->id;
            }
        }

        // ----- BRAND NAME -----

        if (! empty($row['brand_name'])) {
            // Check length
            if (strlen($row['brand_name']) > 50) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.brand_length')
                ];

                array_push($error_msg, $error_line);

            } else {
                $product['brand_name'] = $row['brand_name'];
            }
        }

        // ----- CATEGORY NAME -----

        $category_error = true;

        if (! empty($row['category_name'])) {
            // Check length
            if (strlen($row['category_name']) > 100) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.category_length')
                ];

                array_push($error_msg, $error_line);

            } else {
                $product['category_name'] = $row['category_name'];

                $category_error = false;
            }
        }

        // ----- SUB CATEGORY ID -----

        if (! empty($row['sub_category_name'])) {
            // Check exist category
            if (! $category_error) {
                // Check length
                if (strlen($row['sub_category_name']) > 100) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.sub_category_length')
                    ];
    
                    array_push($error_msg, $error_line);
    
                } else {
                    $product['sub_category_name'] = $row['sub_category_name'];
                }

            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sub_category_empty_category')
                ];

                array_push($error_msg, $error_line);
            }
        }

        // ----- TAX -----

        if (! empty($row['applied_tax'])) {
            $tax_error = false;

            // Check length
            if (strlen($row['applied_tax']) > 25) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.tax_length')
                ];

                array_push($error_msg, $error_line);

                $tax_error = true;
            }

            // Check exist
            $tax = TaxGroup::where('business_id', $business_id)
                ->where('description', $row['applied_tax'])
                ->first();

            if (empty($tax)) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.tax_exist')
                ];

                array_push($error_msg, $error_line);

                $tax_error = true;
            }

            if (! $tax_error) {
                $product['tax'] = $tax->id;
            }
        }

        // ----- TAX TYPE -----

        if (! empty($row['sales_tax'])) {
            $tax_type = mb_strtolower($row['sales_tax']);

            // Check invalid value
            if (in_array($tax_type, ['inclusive', 'exclusive', 'incluido', 'no incluido'])) {
                if (in_array($tax_type, ['inclusive', 'incluido'])) {
                    $product['tax_type'] = 'inclusive';
                } else {
                    $product['tax_type'] = 'exclusive';
                }
                
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.tax_type_invalid')
                ];

                array_push($error_msg, $error_line);
            }
        }

        // ----- ENABLE STOCK -----

        $product['enable_stock'] = is_null($default_data) ? null : (isset($default_data['enable_stock']) ? $default_data['enable_stock'] : null);

        // ----- ALERT QUANTITY -----

        if ($product['enable_stock'] == 1) {
            $min_inventory_error = false;
            
            // Check empty
            if (is_null($row['min_inventory'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.min_inventory_empty')
                ];

                array_push($error_msg, $error_line);

                $min_inventory_error = true;

            } else {
                // Check numeric
                if (! is_numeric($row['min_inventory'])) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.min_inventory_numeric')
                    ];
        
                    array_push($error_msg, $error_line);
        
                    $min_inventory_error = true;

                } else {
                    // Check zero
                    if ($row['min_inventory'] < 0) {
                        $error_line = [
                            'row' => $row_no,
                            'msg' => __('product.min_inventory_zero')
                        ];
            
                        array_push($error_msg, $error_line);
            
                        $min_inventory_error = true;
                    }
                }
            }

            if (! $min_inventory_error) {
                $product['alert_quantity'] = $row['min_inventory'];
            }

        } else {
            $product['alert_quantity'] = 0;
        }

        // ----- SKU -----

        if (! empty($row['sku'])) {
            $sku_error = false;

            // Check length
            if (strlen($row['sku']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sku_length')
                ];

                array_push($error_msg, $error_line);

                $sku_error = true;
            }

            // Check unique
            $is_exist = Product::where('business_id', $business_id)
                ->whereRaw('UPPER(sku) = UPPER(?)', [$row['sku']])
                ->exists();

            if ($is_exist) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sku_unique')
                ];

                array_push($error_msg, $error_line);

                $sku_error = true;
            }

            if (! $sku_error) {
                $product['sku'] = $row['sku'];
            }
        }

        // ----- BARCODE TYPE -----

        $product['barcode_type'] = is_null($default_data) ? null : (isset($default_data['barcode_type']) ? $default_data['barcode_type'] : null);

        // ----- PRODUCT DESCRIPTION -----

        if (! empty($row['product_description'])) {
            // Check length
            if (strlen($row['product_description']) > 255) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.product_description_length')
                ];

                array_push($error_msg, $error_line);

            } else {
                $product['product_description'] = $row['product_description'];
            }
        }

        // ----- WARRANTY -----

        if (! empty($row['warranty'])) {
            // Check length
            if (strlen($row['warranty']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.warranty_length')
                ];

                array_push($error_msg, $error_line);

            } else {
                $product['warranty'] = $row['warranty'];
            }
        }

        // ----- STATUS -----

        // Check empty
        if (! empty($row['status'])) {
            $status = mb_strtolower($row['status']);

            // Check invalid value
            if (in_array($status, ['active', 'inactive', 'activo', 'inactivo'])) {
                if (in_array($status, ['active', 'activo'])) {
                    $product['status'] = 'active';

                } else {
                    $product['status'] = 'inactive';
                }
                
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.status_invalid')
                ];

                array_push($error_msg, $error_line);
            }

        } else {
            $error_line = [
                'row' => $row_no,
                'msg' => __('product.status_empty')
            ];

            array_push($error_msg, $error_line);
        }

        // ----- HAS WARRANTY -----

        // Check empty
        if (! empty($row['has_warranty'])) {
            $has_warranty = mb_strtolower($row['has_warranty']);

            // Check invalid value
            if (in_array($has_warranty, ['yes', 'no', 'si', 'sí'])) {
                if (in_array($has_warranty, ['yes', 'si', 'sí'])) {
                    $product['has_warranty'] = 1;

                } else {
                    $product['has_warranty'] = 0;
                }
                
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.has_warranty_invalid')
                ];

                array_push($error_msg, $error_line);
            }
        }

        // ----- CREATED BY -----

        $product['created_by'] = $user_id;

        /**
         * ------------------------------------------------------------
         * VARIATION
         * ------------------------------------------------------------
         */

        if (! empty($row['cost_without_tax'])) {
            // Check numeric
            if (! is_numeric($row['cost_without_tax'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.cost_without_tax_numeric')
                ];
    
                array_push($error_msg, $error_line);

            } else {
                // Check zero
                if ($row['cost_without_tax'] < 0) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.cost_without_tax_zero')
                    ];
        
                    array_push($error_msg, $error_line);
    
                } else {
                    $product['default_purchase_price'] = $this->productUtil->num_uf($row['cost_without_tax']);
                }
            }

        } else {
            $product['default_purchase_price'] = null;
        }

        // ----- DEFAULT SELL PRICE -----

        if (! empty($row['sales_price_without_tax'])) {
            // Check numeric
            if (! is_numeric($row['sales_price_without_tax'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sales_price_without_tax_numeric')
                ];
    
                array_push($error_msg, $error_line);

            } else {
                // Check zero
                if ($row['sales_price_without_tax'] < 0) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.sales_price_without_tax_zero')
                    ];
        
                    array_push($error_msg, $error_line);

                } else {
                    $product['default_sell_price'] = $this->productUtil->num_uf($row['sales_price_without_tax']);
                }
            }

        } else {
            $product['default_sell_price'] = null;
        }

        /**
         * ------------------------------------------------------------
         * OPENING STOCK
         * ------------------------------------------------------------
         */

        // ----- QUANTITY -----

        if (! empty($row['quantity'])) {
            // Check numeric
            if (! is_numeric($row['quantity'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.quantity_numeric')
                ];
    
                array_push($error_msg, $error_line);

            } else {
                // Check zero
                if ($row['quantity'] < 0) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.quantity_zero')
                    ];
        
                    array_push($error_msg, $error_line);

                } else {
                    $product['quantity'] = $this->productUtil->num_uf($row['quantity']);
                }
            }
        }

        $result = [
            'product' => $product,
            'error_msg' => $error_msg,
        ];

        return $result;
    }

    /**
     * Imports the uploaded file to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            $business_id = auth()->user()->business_id;
            $user_id = auth()->user()->id;

            $default_profit_percent = request()->session()->get('business.default_profit_percent');

            // Session variables
            $products = session('products');

            DB::beginTransaction();

            if (! empty($products)) {
                foreach ($products as $data) {
                    $new_product = [
                        'name' => $data['product_name'],
                        'business_id' => $data['business_id'],
                        'type' => $data['type'],
                        'unit_id' => $data['unit_id'],
                        'tax' => $data['tax'],
                        'tax_type' => $data['tax_type'],
                        'enable_stock' => $data['enable_stock'],
                        'alert_quantity' => $data['alert_quantity'],
                        'sku' => $data['sku'],
                        'barcode_type' => $data['barcode_type'],
                        'product_description' => $data['product_description'],
                        'warranty' => $data['warranty'],
                        'discount_card' => 0,
                        'status' => $data['status'],
                        'clasification' => $data['clasification'],
                        'dai' => 0,
                        'has_warranty' => $data['has_warranty'],
                        'created_by' => $data['created_by'],
                    ];

                    // SKU
                    if (is_null($data['sku'])) {
                        $new_product['sku'] = ' ';
                    }
                    
                    // Brand
                    if (! is_null($data['brand_name'])) {
                        $brand = Brands::where('business_id', $business_id)
                            ->whereRaw('UPPER(name) = UPPER(?)', [$data['brand_name']])
                            ->first();

                        if (empty($brand)) {
                            $brand = Brands::create([
                                'business_id' => $business_id,
                                'name' => $data['brand_name'],
                                'created_by' => $user_id
                            ]);
                        }

                        $new_product['brand_id'] = $brand->id;
                    }

                    // Category
                    if (! is_null($data['category_name'])) {
                        $category = Category::where('business_id', $business_id)
                            ->whereRaw('UPPER(name) = UPPER(?)', [$data['category_name']])
                            ->first();

                        if (empty($category)) {
                            $category = Category::create([
                                'business_id' => $business_id,
                                'name' => $data['category_name'],
                                'created_by' => $user_id,
                                'parent_id' => 0
                            ]);
                        }

                        $new_product['category_id'] = $category->id;

                        // Sub category
                        if (! is_null($data['sub_category_name'])) {
                            $sub_category = Category::where('business_id', $business_id)
                                ->where('parent_id', $category->id)
                                ->whereRaw('UPPER(name) = UPPER(?)', [$data['sub_category_name']])
                                ->first();

                            if (empty($sub_category)) {
                                $sub_category = Category::create([
                                    'business_id' => $business_id,
                                    'name' => $data['sub_category_name'],
                                    'created_by' => $user_id,
                                    'parent_id' => $category->id
                                ]);
                            }

                            $new_product['sub_category_id'] = $sub_category->id;
                        }
                    }

                    // Tax
                    $tax_amount = $this->tax_amount_default;

                    if (! empty($data['tax'])) {
                        $tax = TaxGroup::find($data['tax']);

                        if (! empty($tax)) {
                            $tax_amount = ($this->taxUtil->getTaxPercent($tax->id)) * 100;
                        }
                    }

                    if ($data['type'] == 'single') {
                        // Calculate profit margin
                        if ($data['default_purchase_price'] > 0 && ! is_null($data['default_sell_price'])) {
                            if ($data['tax_type'] == 'inclusive') {
                                $default_purchase_price = $data['default_purchase_price'] * (1 + ($tax_amount / 100));
                            } else {
                                $default_purchase_price = $data['default_purchase_price'];
                            }

                            $profit_margin = $this->productUtil->get_percent($default_purchase_price, $data['default_sell_price']);

                        } else {
                            $profit_margin = $default_profit_percent;
                        }

                        // Calculate purchase price
                        $purchase_price = ! is_null($data['default_purchase_price']) ? $data['default_purchase_price'] : 0;

                        // Calculate sell price
                        $sell_price = ! is_null($data['default_sell_price']) ? $data['default_sell_price'] : 0;

                        // Calculate product prices
                        $product_prices = $this->calculateVariationPrices($purchase_price, 0, $sell_price, $tax_amount, $data['tax_type'], $profit_margin);

                        $new_variation = [
                            'name' =>  null,
                            'product_id' =>  null,
                            'sub_sku' =>  null,
                            'product_variation_id' =>  null,
                            'variation_value_id' =>  null,
                            'default_purchase_price' =>  $product_prices['dpp_exc_tax'],
                            'dpp_inc_tax' =>  $product_prices['dpp_inc_tax'],
                            'profit_percent' =>  $profit_margin,
                            'default_sell_price' =>  $product_prices['dsp_exc_tax'],
                            'sell_price_inc_tax' =>  $product_prices['dsp_inc_tax'],
                        ];

                        $opening_stock = null;

                        // Opening stock
                        if ($data['enable_stock'] == 1 && ! empty($data['quantity'])) {
                            $warehouse = Warehouse::where('business_id', $business_id)->first();

                            $opening_stock = [
                                'quantity' => $data['quantity'],
                                'location_id' => $warehouse->id,
                                'exp_date' => null,
                            ];
                        }
                    }

                    //Create new product
                    $product = Product::create($new_product);

                    // If auto generate sku generate new sku
                    if ($product->sku == ' ') {
                        $sku = $this->productUtil->generateProductSku($product->id);
                        $product->sku = $sku;
                        $product->save();
                    }

                    // Create single product variation
                    if ($product->type == 'single') {
                        $this->productUtil->createSingleProductVariation(
                            $product,
                            $product->sku,
                            $new_variation['default_purchase_price'],
                            $new_variation['dpp_inc_tax'],
                            $new_variation['profit_percent'],
                            $new_variation['default_sell_price'],
                            $new_variation['sell_price_inc_tax']
                        );

                        if (! empty($opening_stock)) {
                            $this->addOpeningStock($opening_stock, $product, $business_id);
                        }
                    }
                }
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('product.file_imported_successfully')
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('import-products')->with('status', $output);
    }

    /**
     * Display edit products screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        $errors = [];

        // Check if zip extension it loaded or not
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => __('messages.install_enable_zip')
            ];

            return view('import_products.index')->with([
                'notification' => $output,
                'errors' => $errors
            ]);

        } else {
            return view('import_products.edit', compact('errors'));
        }
    }

    /**
     * Check file to importer.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkEditFile(Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            // Errors list
            $error_msg = [];

            // Product lines
            $products = [];

            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');
            $default_profit_percent = $request->session()->get('business.default_profit_percent');
            $exception = 0;

            if ($request->hasFile('products_xlsx')) {
                $file = $request->file('products_xlsx');

                // Check malformed file
                try {
                    // Product sheet
                    $imported_data_1 = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[1];

                    // Service sheet
                    $imported_data_2 = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[2];

                    // Kit sheet
                    $imported_data_3 = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[3];

                    if (config('app.business') == 'optics') {
                        // Material sheet
                        $imported_data_4 = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[4];
                    }

                } catch (\Exception $e) {
                    $exception = 1;

                    $error_line = [
                        'row' => 'N/A',
                        'sheet' => 'N/A',
                        'msg' => __('lang_v1.malformed_file')
                    ];

                    array_push($error_msg, $error_line);

                    \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
                    
                    $status = [
                        'success' => 0,
                        'msg' => __('lang_v1.malformed_file')
                    ];

                    $errors = $error_msg;

                    if (count($error_msg) == 0 && $exception == 0) {
                        $flag = true;
                    } else {
                        $flag = false;
                    }

                    return view('import_products.edit')
                        ->with(compact(
                            'errors',
                            'status',
                            'flag',
                            'exception'
                        ));
                }

                /**
                 * ------------------------------------------------------------
                 * PRODUCT SHEET
                 * ------------------------------------------------------------
                 */

                // Removing the header
                unset($imported_data_1[0]);
                unset($imported_data_1[1]);
                unset($imported_data_1[2]);
                unset($imported_data_1[3]);

                // Columns number
                $col_no = config('app.business') == 'optics' ? 22 : 21;

                // Process file
                foreach ($imported_data_1 as $key => $value) {
                    // Check columns number
                    if (count($value) != $col_no) {
                        $error_line = [
                            'row' => 'N/A',
                            'sheet' => __('product.products'),
                            'msg' => __('product.number_of_columns_mismatch', ['number' => $col_no])
                        ];

                        array_push($error_msg, $error_line);
                    }

                    // Row number
                    $row_no = $key + 1;

                    // Row
                    if (config('app.business') == 'optics') {
                        $row = [
                            'sku' => trim($value[0]),
                            'name' => trim($value[1]),
                            'status' => trim($value[2]),
                            'model' => trim($value[3]),
                            'measurement' => trim($value[4]),
                            'material' => trim($value[5]),
                            'category' => trim($value[6]),
                            'subcategory' => trim($value[7]),
                            'barcode_type' => trim($value[8]),
                            'brand' => trim($value[9]),
                            'unit' => trim($value[10]),
                            'alert_quantity' => trim($value[11]),
                            'description' => trim($value[12]),
                            'has_warranty' => trim($value[13]),
                            'warranty' => trim($value[14]),
                            'enable_imei' => trim($value[15]),
                            'weight' => trim($value[16]),
                            'sales_tax' => trim($value[17]),
                            'applied_tax' => trim($value[18]),
                            'cost_without_tax' => trim($value[19]),
                            'sales_price' => trim($value[20]),
                            'image' => trim($value[21]),
                            'clasification' => 'product',
                            'type' => 'single',
                        ];

                    } else {
                        $row = [
                            'sku' => trim($value[0]),
                            'name' => trim($value[1]),
                            'status' => trim($value[2]),
                            'category' => trim($value[3]),
                            'subcategory' => trim($value[4]),
                            'barcode_type' => trim($value[5]),
                            'brand' => trim($value[6]),
                            'unit' => trim($value[7]),
                            'alert_quantity' => trim($value[8]),
                            'provider_code' => trim($value[9]),
                            'drive_unit' => trim($value[10]),
                            'description' => trim($value[11]),
                            'has_warranty' => trim($value[12]),
                            'warranty' => trim($value[13]),
                            'enable_imei' => trim($value[14]),
                            'weight' => trim($value[15]),
                            'sales_tax' => trim($value[16]),
                            'applied_tax' => trim($value[17]),
                            'cost_without_tax' => trim($value[18]),
                            'sales_price' => trim($value[19]),
                            'clasification' => 'product',
                            'type' => 'single',
                        ];
                    }

                    $result = $this->checkEditRow($row, $row_no);

                    // Product result
                    array_push($products, $result['product']);

                    // Error messages result
                    foreach ($result['error_msg'] as $item) {
                        $item['sheet'] = __('product.products');
                        array_push($error_msg, $item);
                    }
                }

                /**
                 * ------------------------------------------------------------
                 * SERVICE SHEET
                 * ------------------------------------------------------------
                 */

                // Removing the header
                unset($imported_data_2[0]);
                unset($imported_data_2[1]);
                unset($imported_data_2[2]);
                unset($imported_data_2[3]);

                // Columns number
                $col_no = config('app.business') == 'optics' ? 14 : 13;

                // Process file
                foreach ($imported_data_2 as $key => $value) {
                    // Check columns number
                    if (count($value) != $col_no) {
                        $error_line = [
                            'row' => 'N/A',
                            'sheet' => __('product.services'),
                            'msg' => __('product.number_of_columns_mismatch', ['number' => $col_no])
                        ];

                        array_push($error_msg, $error_line);
                    }

                    // Row number
                    $row_no = $key + 1;

                    // Row
                    if (config('app.business') == 'optics') {
                        $row = [
                            'sku' => trim($value[0]),
                            'name' => trim($value[1]),
                            'status' => trim($value[2]),
                            'category' => trim($value[3]),
                            'subcategory' => trim($value[4]),
                            'ar' => trim($value[5]),
                            'description' => trim($value[6]),
                            'has_warranty' => trim($value[7]),
                            'warranty' => trim($value[8]),
                            'sales_tax' => trim($value[9]),
                            'applied_tax' => trim($value[10]),
                            'cost_without_tax' => trim($value[11]),
                            'sales_price' => trim($value[12]),
                            'image' => trim($value[13]),
                            'clasification' => 'service',
                            'type' => 'single',
                        ];

                    } else {
                        $row = [
                            'sku' => trim($value[0]),
                            'name' => trim($value[1]),
                            'status' => trim($value[2]),
                            'category' => trim($value[3]),
                            'subcategory' => trim($value[4]),
                            'description' => trim($value[5]),
                            'has_warranty' => trim($value[6]),
                            'warranty' => trim($value[7]),
                            'sales_tax' => trim($value[8]),
                            'applied_tax' => trim($value[9]),
                            'cost_without_tax' => trim($value[10]),
                            'sales_price' => trim($value[11]),
                            'clasification' => 'service',
                            'type' => 'single',
                        ];
                    }

                    $result = $this->checkEditRow($row, $row_no);

                    // Product result
                    array_push($products, $result['product']);

                    // Error messages result
                    foreach ($result['error_msg'] as $item) {
                        $item['sheet'] = __('product.services');
                        array_push($error_msg, $item);
                    }
                }

                /**
                 * ------------------------------------------------------------
                 * KIT SHEET
                 * ------------------------------------------------------------
                 */
    
                // Removing the header
                unset($imported_data_3[0]);
                unset($imported_data_3[1]);
                unset($imported_data_3[2]);
                unset($imported_data_3[3]);
    
                // Columns number
                $col_no = 13;
    
                // Process file
                foreach ($imported_data_3 as $key => $value) {
                    // Check columns number
                    if (count($value) != $col_no) {
                        $error_line = [
                            'row' => 'N/A',
                            'sheet' => __('product.kits'),
                            'msg' => __('product.number_of_columns_mismatch', ['number' => $col_no])
                        ];
    
                        array_push($error_msg, $error_line);
                    }
    
                    // Row number
                    $row_no = $key + 1;
    
                    // Row
                    $row = [
                        'sku' => trim($value[0]),
                        'name' => trim($value[1]),
                        'status' => trim($value[2]),
                        'category' => trim($value[3]),
                        'subcategory' => trim($value[4]),
                        'barcode_type' => trim($value[5]),
                        'description' => trim($value[6]),
                        'has_warranty' => trim($value[7]),
                        'warranty' => trim($value[8]),
                        'sales_tax' => trim($value[9]),
                        'applied_tax' => trim($value[10]),
                        'cost_without_tax' => trim($value[11]),
                        'sales_price' => trim($value[12]),
                        'image' => trim($value[13]),
                        'clasification' => 'product',
                        'type' => 'single',
                    ];
    
                    $result = $this->checkEditRow($row, $row_no);
    
                    // Product result
                    array_push($products, $result['product']);
    
                    // Error messages result
                    foreach ($result['error_msg'] as $item) {
                        $item['sheet'] = __('product.kits');
                        array_push($error_msg, $item);
                    }
                }

                /**
                 * ------------------------------------------------------------
                 * MATERIAL SHEET
                 * ------------------------------------------------------------
                 */

                if (config('app.business') == 'optics') {
                    // Removing the header
                    unset($imported_data_4[0]);
                    unset($imported_data_4[1]);
                    unset($imported_data_4[2]);
                    unset($imported_data_4[3]);

                    // Columns number
                    $col_no = 19;

                    // Process file
                    foreach ($imported_data_4 as $key => $value) {
                        // Check columns number
                        if (count($value) != $col_no) {
                            $error_line = [
                                'row' => 'N/A',
                                'sheet' => __('material.materials'),
                                'msg' => __('product.number_of_columns_mismatch', ['number' => $col_no])
                            ];

                            array_push($error_msg, $error_line);
                        }

                        // Row number
                        $row_no = $key + 1;

                        // Row
                        $row = [
                            'sku' => trim($value[0]),
                            'name' => trim($value[1]),
                            'status' => trim($value[2]),
                            'category' => trim($value[3]),
                            'subcategory' => trim($value[4]),
                            'material_type' => trim($value[5]),
                            'brand' => trim($value[6]),
                            'unit' => trim($value[7]),
                            'alert_quantity' => trim($value[8]),
                            'description' => trim($value[9]),
                            'has_warranty' => trim($value[10]),
                            'warranty' => trim($value[11]),
                            'enable_imei' => trim($value[12]),
                            'weight' => trim($value[13]),
                            'sales_tax' => trim($value[14]),
                            'applied_tax' => trim($value[15]),
                            'cost_without_tax' => trim($value[16]),
                            'sales_price' => trim($value[17]),
                            'image' => trim($value[18]),
                            'clasification' => 'material',
                            'type' => 'single',
                        ];

                        $result = $this->checkEditRow($row, $row_no);

                        // Product result
                        array_push($products, $result['product']);

                        // Error messages result
                        foreach ($result['error_msg'] as $item) {
                            $item['sheet'] = __('material.materials');
                            array_push($error_msg, $item);
                        }
                    }
                }
            }

            $status = [
                'success' => 1,
                'msg' => __('customer.successful_verified_file')
            ];

        } catch (\Exception $e) {
            $exception = 1;

            $error_line = [
                'row' => 'N/A',
                'sheet' => 'N/A',
                'msg' => $e->getMessage()
            ];

            array_push($error_msg, $error_line);

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $status = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        // Session variables 
        session(['products' => $products]);

        $errors = $error_msg;

        if (count($error_msg) == 0 && $exception == 0) {
            $flag = true;
        } else {
            $flag = false;
        }

        return view('import_products.edit')
            ->with(compact(
                'errors',
                'status',
                'flag',
                'exception'
            ));
    }

    /**
     * Check row data.
     * 
     * @param  array  $row
     * @param  int  $row_no
     * @return array
     */
    public function checkEditRow($row, $row_no)
    {
        $product = [
            // Product
            'product_id' => null,
            'name' => null,
            'unit_id' => null,
            'brand_id' => null,
            'category_id' => null,
            'sub_category_id' => null,
            'tax' => null,
            'tax_type' => null,
            'alert_quantity' => null,
            'sku' => null,
            'barcode_type' => null,
            'enable_sr_no' => null,
            'weight' => null,
            'product_description' => null,
            'warranty' => null,
            'status' => null,
            'has_warranty' => null,
            'clasification' => $row['clasification'],
            'type' => $row['type'],

            // Variation
            'variation_id' => null,
            'default_purchase_price' =>  null,
            'sales_price' =>  null,
        ];

        if (config('app.business') == 'optics') {
            $product['model'] = null;
            $product['measurement'] = null;
            $product['ar'] = null;
            $product['material_id'] = null;
            $product['material_type_id'] = null;
            $product['image'] = null;

        } else {
            $product['provider_code'] = null;
            $product['drive_unit'] = null;
        }

        // Errors list
        $error_msg = [];

        $business_id = request()->session()->get('user.business_id');

        // ----- SKU & PRODUCT ID -----

        $product_id = 0;
        $sku_error = false;

        // Check empty
        if (! empty($row['sku'])) {
            // Check length
            if (strlen($row['sku']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sku_length')
                ];

                array_push($error_msg, $error_line);

                $sku_error = true;
            }

            // Check exist
            $product = Product::where('business_id', $business_id)
                ->whereRaw('UPPER(sku) = UPPER(?)', [$row['sku']])
                ->first();

            if (! empty($product)) {
                $product_id = $product->id;

            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sku_exist')
                ];

                array_push($error_msg, $error_line);

                $sku_error = true;
            }

        } else {
            $error_line = [
                'row' => $row_no,
                'msg' => __('product.sku_empty')
            ];

            array_push($error_msg, $error_line);

            $sku_error = true;
        }

        if (! $sku_error && $product_id != 0) {
            $product['sku'] = $row['sku'];
            $product['product_id'] = $product_id;
        }

        // ----- NAME -----

        $name_error = false;

        if (! empty($row['name'])) {
            // Check length
            if (strlen($row['name']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.product_name_length')
                ];
    
                array_push($error_msg, $error_line);
    
                $name_error = true;
            }
        }

        if (! $name_error) {
            $product['name'] = $row['name'];
        }

        // ----- UNIT ID -----

        $unit_id = 0;
        $unit_error = false;

        if ($product['clasification'] == 'product') {
            if (! empty($row['unit'])) {
                // Check length
                if (strlen($row['unit']) > 191) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.unit_length')
                    ];

                    array_push($error_msg, $error_line);

                    $unit_error = true;
                }
                
                // Check exist
                $unit = Unit::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(short_name) = UPPER(?)', [$row['unit']])
                            ->orWhereRaw('UPPER(actual_name) = UPPER(?)', [$row['unit']]);
                    })
                    ->first();

                if (! empty($unit)) {
                    $unit_id = $unit->id;

                } else {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.unit_exist')
                    ];

                    array_push($error_msg, $error_line);

                    $unit_error = true;
                }
            }
    
            if (! $unit_error && $unit_id != 0) {
                $product['unit_id'] = $unit_id;
            }
        }

        // ----- BRAND ID -----

        $brand_id = 0;
        $brand_error = false;

        if (! empty($row['brand'])) {
            // Check length
            if (strlen($row['brand']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.brand_length')
                ];

                array_push($error_msg, $error_line);

                $brand_error = true;
            }
            
            // Check exist
            $brand = Brands::where('business_id', $business_id)
                ->whereRaw('UPPER(name) = UPPER(?)', [$row['brand']])
                ->first();

            if (! empty($brand)) {
                $brand_id = $brand->id;

            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.brand_exist')
                ];

                array_push($error_msg, $error_line);

                $brand_error = true;
            }
        }

        if (! $brand_error && $brand_id != 0) {
            $product['brand_id'] = $brand_id;
        }

        // ----- CATEGORY ID -----

        $category_id = 0;
        $category_error = false;

        if (! empty($row['category'])) {
            // Check length
            if (strlen($row['category']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.category_length')
                ];

                array_push($error_msg, $error_line);

                $category_error = true;
            }
            
            // Check exist
            $category = Category::where('business_id', $business_id)
                ->whereRaw('UPPER(name) = UPPER(?)', [$row['category']])
                ->where('parent_id', 0)
                ->first();

            if (! empty($category)) {
                $category_id = $category->id;

            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.category_exist')
                ];

                array_push($error_msg, $error_line);

                $category_error = true;
            }
        }

        if (! $category_error && $category_id != 0) {
            $product['category_id'] = $category_id;
        }

        // ----- SUB CATEGORY ID -----

        $sub_category_id = 0;
        $sub_category_error = false;

        if (! empty($row['subcategory'])) {
            // Check length
            if (strlen($row['subcategory']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sub_category_length')
                ];

                array_push($error_msg, $error_line);

                $sub_category_error = true;
            }
            
            // Check exist
            $sub_category = Category::where('business_id', $business_id)
                ->whereRaw('UPPER(name) = UPPER(?)', [$row['sub_category']])
                ->where('parent_id', '>', 0)
                ->first();

            if (! empty($sub_category)) {
                $sub_category_id = $sub_category->id;

            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sub_category_exist')
                ];

                array_push($error_msg, $error_line);

                $sub_category_error = true;
            }
        }

        if (! $sub_category_error && $sub_category_id != 0) {
            $product['sub_category_id'] = $sub_category_id;
        }

        // ----- TAX -----

        $tax_id = 0;
        $tax_error = false;

        if (! empty($row['applied_tax'])) {
            // Check length
            if (strlen($row['applied_tax']) > 25) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.tax_length')
                ];

                array_push($error_msg, $error_line);

                $tax_error = true;
            }

            // Check exist
            $tax = TaxGroup::where('business_id', $business_id)
                ->where('description', $row['applied_tax'])
                ->first();

            if (! empty($tax)) {
                $tax_id = $tax->id;

            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.tax_exist')
                ];

                array_push($error_msg, $error_line);

                $tax_error = true;
            }
        }

        if (! $tax_error && $tax_id != 0) {
            $product['tax'] = $tax_id;
        }

        // ----- TAX TYPE -----

        if (! empty($row['sales_tax'])) {
            $tax_type = mb_strtolower($row['sales_tax']);

            // Check invalid value
            if (in_array($tax_type, ['inclusive', 'exclusive', 'incluido', 'no incluido'])) {
                if (in_array($tax_type, ['inclusive', 'incluido'])) {
                    $product['tax_type'] = 'inclusive';
                } else {
                    $product['tax_type'] = 'exclusive';
                }
                
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.tax_type_invalid')
                ];

                array_push($error_msg, $error_line);
            }
        }

        // ----- ALERT QUANTITY -----

        $alert_quantity_error = false;
        
        // Check empty
        if (! empty($row['alert_quantity'])) {
            // Check numeric
            if (! is_numeric($row['alert_quantity'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.alert_quantity_numeric')
                ];
    
                array_push($error_msg, $error_line);
    
                $alert_quantity_error = true;

            } else {
                // Check zero
                if ($row['alert_quantity'] < 0) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.alert_quantity_zero')
                    ];
        
                    array_push($error_msg, $error_line);
        
                    $alert_quantity_error = true;
                }
            }
        }

        if (! $alert_quantity_error) {
            $product['alert_quantity'] = $row['alert_quantity'];
        }

        // ----- BARCODE TYPE -----

        $barcode_type = null;
        $barcode_type_error = false;

        if (! empty($row['barcode_type'])) {
            $barcode_type = mb_strtoupper($row['barcode_type']);

            // Check invalid value
            if (! in_array($barcode_type, ['C39', 'C128', 'EAN13', 'EAN8', 'UPCA', 'UPCE'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.barcode_type_invalid')
                ];

                array_push($error_msg, $error_line);

                $barcode_type_error = true;
            }
        }

        if (! $barcode_type_error && is_null($barcode_type)) {
            $barcode_type_error['barcode_type'] = $barcode_type;
        }

        // ----- ENABLE SR NO -----

        // Check empty
        if (! empty($row['enable_imei'])) {
            $enable_sr_no = mb_strtolower($row['enable_imei']);

            // Check invalid value
            if (in_array($enable_sr_no, ['yes', 'no', 'si', 'sí'])) {
                if (in_array($enable_sr_no, ['yes', 'si', 'sí'])) {
                    $product['enable_sr_no'] = 1;

                } else {
                    $product['enable_sr_no'] = 0;
                }
                
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.enable_sr_no_invalid')
                ];

                array_push($error_msg, $error_line);
            }
        }

        // ----- WEIGHT -----

        $weight_error = false;

        if (! empty($row['weight'])) {
            // Check length
            if (strlen($row['weight']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.weight_length')
                ];
    
                array_push($error_msg, $error_line);
    
                $weight_error = true;
            }
        }

        if (! $weight_error) {
            $product['weight'] = $row['weight'];
        }

        // ----- PRODUCT DESCRIPTION -----

        if (! empty($row['product_description'])) {
            // Check length
            if (strlen($row['product_description']) > 255) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.product_description_length')
                ];

                array_push($error_msg, $error_line);

            } else {
                $product['product_description'] = $row['product_description'];
            }
        }

        // ----- WARRANTY -----

        if (! empty($row['warranty'])) {
            // Check length
            if (strlen($row['warranty']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.warranty_length')
                ];

                array_push($error_msg, $error_line);

            } else {
                $product['warranty'] = $row['warranty'];
            }
        }

        // ----- STATUS -----

        // Check empty
        if (! empty($row['status'])) {
            $status = mb_strtolower($row['status']);

            // Check invalid value
            if (in_array($status, ['active', 'inactive', 'activo', 'inactivo'])) {
                if (in_array($status, ['active', 'activo'])) {
                    $product['status'] = 'active';

                } else {
                    $product['status'] = 'inactive';
                }
                
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.status_invalid')
                ];

                array_push($error_msg, $error_line);
            }
        }

        // ----- HAS WARRANTY -----

        // Check empty
        if (! empty($row['has_warranty'])) {
            $has_warranty = mb_strtolower($row['has_warranty']);

            // Check invalid value
            if (in_array($has_warranty, ['yes', 'no', 'si', 'sí'])) {
                if ($has_warranty == 'no') {
                    $product['has_warranty'] = 0;

                } else {
                    $product['has_warranty'] = 1;
                }
                
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.has_warranty_invalid')
                ];

                array_push($error_msg, $error_line);
            }
        }

        if (config('app.business') == 'optics') {
            // ----- MODEL -----
            if (! empty($row['model'])) {
                // Check length
                if (strlen($row['model']) > 191) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.model_length')
                    ];
    
                    array_push($error_msg, $error_line);
    
                } else {
                    $product['model'] = $row['model'];
                }
            }

            // ----- MEASUREMENT -----
            if (! empty($row['measurement'])) {
                // Check length
                if (strlen($row['measurement']) > 191) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.measurement_length')
                    ];
    
                    array_push($error_msg, $error_line);
    
                } else {
                    $product['measurement'] = $row['measurement'];
                }
            }

            // ----- AR -----

            // Check empty
            if (! empty($row['ar'])) {
                $ar = mb_strtolower($row['ar']);

                // Check invalid value
                if (in_array($ar, ['green', 'blue', 'premium'])) {
                    $product['ar'] = $ar;
                    
                } else {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.ar_invalid')
                    ];

                    array_push($error_msg, $error_line);
                }
            }

            // ----- MATERIAL ID -----

            $material_id = 0;
            $material_error = false;

            if (! empty($row['material'])) {
                // Check length
                if (strlen($row['material']) > 191) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.material_length')
                    ];

                    array_push($error_msg, $error_line);

                    $material_error = true;
                }
                
                // Check exist
                $material = Product::where('business_id', $business_id)
                    ->whereRaw('UPPER(sku) = UPPER(?)', [$row['material']])
                    ->first();

                if (! empty($material)) {
                    $material_id = $material->id;

                } else {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.material_exist')
                    ];

                    array_push($error_msg, $error_line);

                    $material_error = true;
                }
            }

            if (! $material_error && $material_id != 0) {
                $product['material_id'] = $material_id;
            }

            // ----- MATERIAL TYPE ID -----

            $material_type_id = 0;
            $material_type_error = false;

            if (! empty($row['material_type'])) {
                // Check length
                if (strlen($row['material_type']) > 191) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.material_type_length')
                    ];

                    array_push($error_msg, $error_line);

                    $material_type_error = true;
                }
                
                // Check exist
                $material_type = MaterialType::where('business_id', $business_id)
                    ->whereRaw('UPPER(name) = UPPER(?)', [$row['material_type']])
                    ->first();

                if (! empty($material_type)) {
                    $material_type_id = $material_type->id;

                } else {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.material_type_exist')
                    ];

                    array_push($error_msg, $error_line);

                    $material_type_error = true;
                }
            }

            if (! $material_type_error && $material_type_id != 0) {
                $product['material_type_id'] = $material_type_id;
            }

            // ----- IMAGE -----
            if (! empty($row['image'])) {
                // Check length
                if (strlen($row['image']) > 191) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.image_length')
                    ];
    
                    array_push($error_msg, $error_line);
    
                } else {
                    $product['image'] = $row['image'];
                }
            }

        } else {
            // ----- PROVIDER CODE -----
            if (! empty($row['provider_code'])) {
                // Check length
                if (strlen($row['provider_code']) > 191) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.provider_code_length')
                    ];
    
                    array_push($error_msg, $error_line);
    
                } else {
                    $product['provider_code'] = $row['provider_code'];
                }
            }

            // ----- DRIVE UNIT -----
            if (! empty($row['drive_unit'])) {
                // Check length
                if (strlen($row['drive_unit']) > 191) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.drive_unit_length')
                    ];
    
                    array_push($error_msg, $error_line);
    
                } else {
                    $product['drive_unit'] = $row['drive_unit'];
                }
            }
        }

        /**
         * ------------------------------------------------------------
         * VARIATION
         * ------------------------------------------------------------
         */

        // ----- SUB SKU & VARIATION ID -----

        $variation_id = 0;
        $sub_sku_error = false;

        // Check empty
        if (! is_null($product['product_id']) && $product['type'] == 'single') {
            // Check exist
            $variation = Variation::where('product_id', $product['product_id'])
                ->whereRaw('UPPER(sub_sku) = UPPER(?)', [$row['sku']])
                ->first();

            if (! empty($variation)) {
                $variation_id = $variation->id;

            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sub_sku_exist')
                ];

                array_push($error_msg, $error_line);

                $sub_sku_error = true;
            }
        }

        if (! $sub_sku_error && $product_id != 0) {
            $product['variation_id'] = $variation_id;
        }

        // ----- DEFAULT PURCHASE PRICE -----

        if (! empty($row['cost_without_tax'])) {
            // Check numeric
            if (! is_numeric($row['cost_without_tax'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.cost_without_tax_numeric')
                ];
    
                array_push($error_msg, $error_line);

            } else {
                // Check zero
                if ($row['cost_without_tax'] < 0) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.cost_without_tax_zero')
                    ];
        
                    array_push($error_msg, $error_line);
    
                } else {
                    $product['default_purchase_price'] = $this->productUtil->num_uf($row['cost_without_tax']);
                }
            }
        }

        // ----- DEFAULT SELL PRICE -----

        if (! empty($row['sales_price'])) {
            // Check numeric
            if (! is_numeric($row['sales_price'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('product.sales_price_without_tax_numeric')
                ];
    
                array_push($error_msg, $error_line);

            } else {
                // Check zero
                if ($row['sales_price'] < 0) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('product.sales_price_without_tax_zero')
                    ];
        
                    array_push($error_msg, $error_line);

                } else {
                    $product['sales_price'] = $this->productUtil->num_uf($row['sales_price']);
                }
            }
        }

        $result = [
            'product' => $product,
            'error_msg' => $error_msg,
        ];

        return $result;
    }

    /**
     * Imports the uploaded file to database.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            $default_profit_percent = request()->session()->get('business.default_profit_percent');

            // Session variables
            $products = session('products');

            DB::beginTransaction();

            if (! empty($products)) {
                foreach ($products as $data) {
                    // Product
                    $update_product = Product::find($data['product_id']);

                    $update_product->name = is_null($data['name']) ? $update_product->name : $data['name'];
                    $update_product->unit_id = is_null($data['unit_id']) ? $update_product->unit_id : $data['unit_id'];
                    $update_product->brand_id = is_null($data['brand_id']) ? $update_product->brand_id : $data['brand_id'];
                    $update_product->category_id = is_null($data['category_id']) ? $update_product->category_id : $data['category_id'];
                    $update_product->sub_category_id = is_null($data['sub_category_id']) ? $update_product->sub_category_id : $data['sub_category_id'];
                    $update_product->tax = is_null($data['tax']) ? $update_product->tax : $data['tax'];
                    $update_product->tax_type = is_null($data['tax_type']) ? $update_product->tax_type : $data['tax_type'];
                    $update_product->alert_quantity = is_null($data['alert_quantity']) ? $update_product->alert_quantity : $data['alert_quantity'];
                    $update_product->sku = is_null($data['sku']) ? $update_product->sku : $data['sku'];
                    $update_product->barcode_type = is_null($data['barcode_type']) ? $update_product->barcode_type : $data['barcode_type'];
                    $update_product->enable_sr_no = is_null($data['enable_sr_no']) ? $update_product->enable_sr_no : $data['enable_sr_no'];
                    $update_product->weight = is_null($data['weight']) ? $update_product->weight : $data['weight'];
                    $update_product->product_description = is_null($data['product_description']) ? $update_product->product_description : $data['product_description'];
                    $update_product->warranty = is_null($data['warranty']) ? $update_product->warranty : $data['warranty'];
                    $update_product->status = is_null($data['status']) ? $update_product->status : $data['status'];
                    $update_product->has_warranty = is_null($data['has_warranty']) ? $update_product->has_warranty : $data['has_warranty'];

                    if (config('app.business') == 'optics') {
                        $update_product->model = is_null($data['model']) ? $update_product->model : $data['model'];
                        $update_product->measurement = is_null($data['measurement']) ? $update_product->measurement : $data['measurement'];
                        $update_product->ar = is_null($data['ar']) ? $update_product->ar : $data['ar'];
                        $update_product->material_id = is_null($data['material_id']) ? $update_product->material_id : $data['material_id'];
                        $update_product->material_type_id = is_null($data['material_type_id']) ? $update_product->material_type_id : $data['material_type_id'];
                        $update_product->image = is_null($data['image']) ? $update_product->image : $data['image'];

                    } else {
                        $update_product->provider_code = is_null($data['provider_code']) ? $update_product->provider_code : $data['provider_code'];
                        $update_product->drive_unit = is_null($data['drive_unit']) ? $update_product->drive_unit : $data['drive_unit'];
                    }

                    $update_product->save();

                    // Variation
                    $update_variation = Variation::find($data['variation_id']);

                    $tax_amount = $this->tax_amount_default;

                    if (! empty($data['tax'])) {
                        $tax = TaxGroup::find($data['tax']);

                        if (! empty($tax)) {
                            $tax_amount = ($this->taxUtil->getTaxPercent($tax->id)) * 100;
                        }
                    }

                    if ($update_product->type == 'single') {
                        // Calculate profit margin
                        if ($data['default_purchase_price'] > 0 && ! is_null($data['sales_price'])) {
                            if ($data['tax_type'] == 'inclusive') {
                                $default_purchase_price = $data['default_purchase_price'] * (1 + ($tax_amount / 100));
                            } else {
                                $default_purchase_price = $data['default_purchase_price'];
                            }

                            $profit_margin = $this->productUtil->get_percent($default_purchase_price, $data['sales_price']);

                        } else {
                            $profit_margin = $default_profit_percent;
                        }

                        // Calculate purchase price
                        $purchase_price = is_null($data['default_purchase_price']) ? $update_variation->default_purchase_price : $data['default_purchase_price'];

                        // Calculate sell price
                        $sell_price = is_null($data['sales_price']) ? ($data['tax_type'] == 'exclusive' ? $update_variation->default_purchase_price : $update_variation->dpp_inc_tax) : $data['sales_price'];

                        // Calculate product prices
                        $product_prices = $this->calculateVariationPrices($purchase_price, 0, $sell_price, $tax_amount, $data['tax_type'], $profit_margin);

                        $update_variation->default_purchase_price = $product_prices['dpp_exc_tax'];
                        $update_variation->dpp_inc_tax = $product_prices['dpp_inc_tax'];
                        $update_variation->profit_percent = $profit_margin;
                        $update_variation->default_sell_price = $product_prices['dsp_exc_tax'];
                        $update_variation->sell_price_inc_tax = $product_prices['dsp_inc_tax'];

                        $update_variation->save();
                    }
                }
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('product.file_imported_successfully')
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('import-products')->with('status', $output);
    }
}
