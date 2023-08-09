<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RrhhImportEmployeesController extends Controller
{
    public function create(){
        if (!auth()->user()->can('rrhh_import_employees.create')) {
            abort(403, 'Unauthorized action.');
        }
    
        return view('rrhh.import_employees.index');
    }

    /**
     * Check file to importer.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkFile(Request $request)
    {
        if (! auth()->user()->can('rrhh_import_employees.create')) {
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
                $col_no = 21;

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

                    /** sync product */
                    $this->productUtil->syncProduct($product->id, $product->sku, "store");
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
                        $sell_price = is_null($data['sales_price']) ? ($data['tax_type'] == 'exclusive' ? $update_variation->default_sell_price : $update_variation->sell_price_inc_tax) : $data['sales_price'];

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
