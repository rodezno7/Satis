<?php

namespace App\Utils;

use App\Unit;
use App\Brands;
use App\Product;
use App\TaxRate;
use App\TaxGroup;
use App\Category;
use App\Business;
use App\Variation;
use App\ProductRack;
use App\Transaction;
use App\DocumentType;
use App\PurchaseLine;
use App\TaxRateTaxGroup;
use App\BusinessLocation;
use App\KitHasProduct;
use App\ProductVariation;
use App\VariationTemplate;
use App\ProductHasSuppliers;
use App\VariationGroupPrice;
use App\TransactionSellLine;
use App\VariationValueTemplate;
use App\VariationLocationDetails;

use DB;

class ProductUtil extends Util
{

    /**
     * Returns the list of barcode types
     *
     * @return array
     */
    public function barcode_types()
    {
        $types = [ 'C128' => 'Code 128 (C128)', 'C39' => 'Code 39 (C39)', 'EAN13' => 'EAN-13', 'EAN8' => 'EAN-8', 'UPCA' => 'UPC-A', 'UPCE' => 'UPC-E'];

        return $types;
    }

    /**
     * Returns the default barcode.
     *
     * @return string
     */
    public function barcode_default()
    {
        return 'C128';
    }

    /**
     * Create single type product variation
     *
     * @param (int or object) $product
     * @param $sku
     * @param $purchase_price
     * @param $dpp_inc_tax (default purchase pric including tax)
     * @param $profit_percent
     * @param $selling_price
     *
     * @return boolean
     */
    public function createSingleProductVariation($product, $sku, $purchase_price, $dpp_inc_tax, $profit_percent, $selling_price, $selling_price_inc_tax)
    {
        if (!is_object($product)) {
            $product = Product::find($product);
        }

        //create product variations
        $product_variation_data = [
                                    'name' => 'DUMMY',
                                    'is_dummy' => 1
                                ];
        $product_variation = $product->product_variations()->create($product_variation_data);
                
        //create variations
        $variation_data = [
                'name' => 'DUMMY',
                'product_id' => $product->id,
                'sub_sku' => $sku,
                'default_purchase_price' => $this->num_uf($purchase_price),
                'dpp_inc_tax' => $this->num_uf($dpp_inc_tax),
                'profit_percent' => $this->num_uf($profit_percent),
                'default_sell_price' => $this->num_uf($selling_price),
                'sell_price_inc_tax' => $this->num_uf($selling_price_inc_tax)
            ];
        $product_variation->variations()->create($variation_data);

        return true;
    }

    /**
     * Create variable type product variation
     *
     * @param (int or object) $product
     * @param $input_variations
     *
     * @return boolean
     */
    public function createVariableProductVariations($product, $input_variations, $business_id = null)
    {
        if (!is_object($product)) {
            $product = Product::find($product);
        }

        //create product variations
        foreach ($input_variations as $key => $value) {
            $variation_template_name = !empty($value['name']) ? $value['name'] : null;
            $variation_template_id = !empty($value['variation_template_id']) ? $value['variation_template_id'] : null;

            if (empty($variation_template_id)) {
                if ($variation_template_name != 'DUMMY') {
                    $variation_template = VariationTemplate::where('business_id', $business_id)
                                                        ->whereRaw('LOWER(name)="' . strtolower($variation_template_name) . '"')
                                                        ->with(['values'])
                                                        ->first();
                    if (empty($variation_template)) {
                        $variation_template = VariationTemplate::create([
                            'name' => $variation_template_name,
                            'business_id' => $business_id
                        ]);
                    }
                    $variation_template_id = $variation_template->id;
                }
            } else {
                $variation_template = VariationTemplate::with(['values'])->find($value['variation_template_id']);
                $variation_template_id = $variation_template->id;
                $variation_template_name = $variation_template->name;
            }

            $product_variation_data = [
                                    'name' => $variation_template_name,
                                    'product_id' => $product->id,
                                    'is_dummy' => 0,
                                    'variation_template_id' => $variation_template_id
                                ];
            $product_variation = ProductVariation::create($product_variation_data);
            
            //create variations
            if (!empty($value['variations'])) {
                $variation_data = [];

                $c = Variation::withTrashed()
                    ->where('product_id', $product->id)
                    ->count() + 1;
                
                foreach ($value['variations'] as $k => $v) {
                    $sub_sku = empty($v['sub_sku'])? $this->generateSubSku($product->sku, $c, $product->barcode_type) :$v['sub_sku'];
                    $variation_value_id = !empty($v['variation_value_id']) ? $v['variation_value_id'] : null;
                    $variation_value_name = !empty($v['value']) ? $v['value'] : null;

                    if (!empty($variation_value_id)) {
                        $variation_value = $variation_template->values->filter(function ($item) use ($variation_value_id) {
                            return $item->id == $variation_value_id;
                        })->first();
                        $variation_value_name = $variation_value->name;
                    } else {
                        if (!empty($variation_template)) {
                            $variation_value =  VariationValueTemplate::where('variation_template_id', $variation_template->id)
                                ->whereRaw('LOWER(name)="' . $variation_value_name . '"')
                                ->first();
                            if (empty($variation_value)) {
                                $variation_value =  VariationValueTemplate::create([
                                    'name' => $variation_value_name,
                                    'variation_template_id' => $variation_template->id
                                ]);
                            }
                            $variation_value_id = $variation_value->id;
                            $variation_value_name = $variation_value->name;
                        } else {
                            $variation_value_id = null;
                            $variation_value_name = $variation_value_name;
                        }
                    }

                    $variation_data[] = [
                      'name' => $variation_value_name,
                      'variation_value_id' => $variation_value_id,
                      'product_id' => $product->id,
                      'sub_sku' => $sub_sku,
                      'default_purchase_price' => $this->num_uf($v['default_purchase_price']),
                      'dpp_inc_tax' => $this->num_uf($v['dpp_inc_tax']),
                      'profit_percent' => $this->num_uf($v['profit_percent']),
                      'default_sell_price' => $this->num_uf($v['default_sell_price']),
                      'sell_price_inc_tax' => $this->num_uf($v['sell_price_inc_tax'])
                    ];
                    $c++;
                }
                $product_variation->variations()->createMany($variation_data);
            }
        }
    }

    /**
     * Update variable type product variation
     *
     * @param $product_id
     * @param $input_variations_edit
     *
     * @return boolean
     */
    public function updateVariableProductVariations($product_id, $input_variations_edit)
    {
        $product = Product::find($product_id);

        //Update product variations
        $product_variation_ids = [];
        foreach ($input_variations_edit as $key => $value) {
            $product_variation_ids[] = $key;

            $product_variation = ProductVariation::find($key);
            $product_variation->name = $value['name'];
            $product_variation->save();

            //Update existing variations
            $variations_ids = [];
            if (!empty($value['variations_edit'])) {
                foreach ($value['variations_edit'] as $k => $v) {
                    $data = [
                        'name' => $v['value'],
                        'default_purchase_price' => $this->num_uf($v['default_purchase_price']),
                        'dpp_inc_tax' => $this->num_uf($v['dpp_inc_tax']),
                        'profit_percent' => $this->num_uf($v['profit_percent']),
                        'default_sell_price' => $this->num_uf($v['default_sell_price']),
                        'sell_price_inc_tax' => $this->num_uf($v['sell_price_inc_tax'])
                    ];
                    if (!empty($v['sub_sku'])) {
                        $data['sub_sku'] = $v['sub_sku'];
                    }
                    Variation::where('id', $k)
                        ->where('product_variation_id', $key)
                        ->update($data);

                    $variations_ids[] = $k;
                }
            }
            Variation::whereNotIn('id', $variations_ids)
                    ->where('product_variation_id', $key)
                    ->delete();

            //Add new variations
            if (!empty($value['variations'])) {
                $variation_data = [];
                $c = Variation::withTrashed()
                                ->where('product_id', $product->id)
                                ->count()+1;

                foreach ($value['variations'] as $k => $v) {
                    $sub_sku = empty($v['sub_sku'])? $this->generateSubSku($product->sku, $c, $product->barcode_type) :$v['sub_sku'];

                    $variation_value_name = !empty($v['value'])? $v['value'] : null;
                    $variation_value_id = null;

                    if (!empty($product_variation->variation_template_id)) {
                        $variation_value =  VariationValueTemplate::where('variation_template_id', $product_variation->variation_template_id)
                                ->whereRaw('LOWER(name)="' . $v['value'] . '"')
                                ->first();
                        if (empty($variation_value)) {
                            $variation_value =  VariationValueTemplate::create([
                                'name' => $v['value'],
                                'variation_template_id' => $product_variation->variation_template_id
                            ]);
                        }
                        
                        $variation_value_id = $variation_value->id;
                    }

                    $variation_data[] = [
                      'name' => $variation_value_name,
                      'variation_value_id' => $variation_value_id,
                      'product_id' => $product->id,
                      'sub_sku' => $sub_sku,
                      'default_purchase_price' => $this->num_uf($v['default_purchase_price']),
                      'dpp_inc_tax' => $this->num_uf($v['dpp_inc_tax']),
                      'profit_percent' => $this->num_uf($v['profit_percent']),
                      'default_sell_price' => $this->num_uf($v['default_sell_price']),
                      'sell_price_inc_tax' => $this->num_uf($v['sell_price_inc_tax'])
                    ];
                    $c++;
                }
                $product_variation->variations()->createMany($variation_data);
            }
        }

        ProductVariation::where('product_id', $product_id)
                ->whereNotIn('id', $product_variation_ids)
                ->delete();
    }

    /**
     * Checks if products has manage stock enabled then Updates quantity for product and its
     * variations
     *
     * @param $location_id
     * @param $product_id
     * @param $variation_id
     * @param $new_quantity
     * @param $old_quantity = 0
     * @param $number_format = null
     * @param $warehouse_id = null
     * @param $type = 'available'
     * @return boolean
     */
    public function updateProductQuantity($location_id, $product_id, $variation_id, $new_quantity, $old_quantity = 0, $number_format = null, $warehouse_id = null, $type = 'available')
    {
        $qty_difference = $this->num_uf($new_quantity, $number_format) - $this->num_uf($old_quantity, $number_format);

        $product = Product::find($product_id);

        //Check if stock is enabled or not.
        if ($product->enable_stock == 1 && $qty_difference != 0) {
            $variation = Variation::where('id', $variation_id)
                            ->where('product_id', $product_id)
                            ->first();
            
            //Add quantity in VariationLocationDetails
            if(!empty($warehouse_id)){
                $variation_location_d = VariationLocationDetails::where('variation_id', $variation->id)
                    ->where('product_id', $product_id)
                    ->where('product_variation_id', $variation->product_variation_id)
                    ->where('location_id', $location_id)
                    ->where('warehouse_id', $warehouse_id)
                    ->first();
            } else{
                $variation_location_d = VariationLocationDetails::where('variation_id', $variation->id)
                    ->where('product_id', $product_id)
                    ->where('product_variation_id', $variation->product_variation_id)
                    ->where('location_id', $location_id)
                    ->first();
            }

            if (empty($variation_location_d)) {
                $variation_location_d = new VariationLocationDetails();
                $variation_location_d->variation_id = $variation->id;
                $variation_location_d->product_id = $product_id;
                $variation_location_d->location_id = $location_id;
                $variation_location_d->product_variation_id = $variation->product_variation_id;
                $variation_location_d->qty_available = 0;
                $variation_location_d->warehouse_id = $warehouse_id;
            }

            if ($type == 'available') {
                $variation_location_d->qty_available += $qty_difference;
            } else {
                $variation_location_d->qty_reserved += $qty_difference;
            }

            $variation_location_d->save();

            // TODO: Add quantity in products table
            // Product::where('id', $product_id)
            //     ->increment('total_qty_available', $qty_difference);
        }
        
        return true;
    }

    /**
     * Checks if products has manage stock enabled then Decrease quantity for product and its variations
     *
     * @param $product_id
     * @param $variation_id
     * @param $location_id
     * @param $new_quantity
     * @param $old_quantity = 0
     * @param $warehouse_id = null
     *
     * @return boolean
     */
    public function decreaseProductQuantity($product_id, $variation_id, $location_id, $new_quantity, $old_quantity = 0, $warehouse_id = null)
    {
        $qty_difference = $new_quantity - $old_quantity;
        
        $product = Product::find($product_id);
        
        // Check if stock is enabled or not.
        if ($product->enable_stock == 1) {
            // Decrement quantity in variations location table
            if (!empty($warehouse_id)) {
                VariationLocationDetails::where('variation_id', $variation_id)
                    ->where('product_id', $product_id)
                    ->where('location_id', $location_id)
                    ->where('warehouse_id', $warehouse_id)
                    ->decrement('qty_available', $qty_difference);

            } else {
                VariationLocationDetails::where('variation_id', $variation_id)
                    ->where('product_id', $product_id)
                    ->where('location_id', $location_id)
                    ->decrement('qty_available', $qty_difference);
            }

            
            // Variation::where('id', $variation_id)
            //     ->where('product_id', $product_id)
            //     ->decrement('qty_available', $qty_difference);

            // TODO: Decrement quantity in products table
            // Product::where('id', $product_id)
            //     ->decrement('total_qty_available', $qty_difference);
        }

        return true;
    }

    /**
     * Get all details for a product from its variation id
     *
     * @param int $variation_id
     * @param int $business_id
     * @param int $location_id
     * @param bool $check_qty (If false qty_available is not checked)
     *
     * @return obj
     */
    public function getDetailsFromVariation($variation_id, $business_id, $location_id = null, $warehouse_id = null, $check_qty = true)
    {
        $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
                ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
                ->leftJoin('variation_location_details AS vld', 'variations.id', '=', 'vld.variation_id')
                ->leftJoin('units', 'p.unit_id', '=', 'units.id')
                ->leftJoin('brands', function ($join) {
                    $join->on('p.brand_id', '=', 'brands.id')
                        ->whereNull('brands.deleted_at');
                })
                ->where('p.status', 'active') // exclude inactive products
                ->where('p.business_id', $business_id)
                ->where('variations.id', $variation_id);

        //Add condition for check of quantity. (if stock is not enabled or qty_available > 0)
        if ($check_qty) {
            $query->where(function ($query) use ($location_id) {
                $query->where('p.enable_stock', '!=', 1)
                    ->orWhere('vld.qty_available', '>', 0);
            });
        }
        
        if (!empty($location_id)) {
            //Check for enable stock, if enabled check for location id.
            $query->where(function ($query) use ($location_id) {
                        $query->where('p.enable_stock', '!=', 1)
                            ->orWhere('vld.location_id', $location_id);
            });
        }

        if (!empty($warehouse_id)) {
            //If enabled check for warehouse id.
            $query->where(function ($query) use ($warehouse_id) {
                    $query->where('p.enable_stock', '!=', 1)
                        ->orwhere('vld.warehouse_id', $warehouse_id);
                        
            });
        }
        
        $products = $query->select(
            DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, 
                    ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
            'p.id as product_id',
            'p.tax as tax_id',
            DB::raw('SUM(p.tax) as tax_percent'), //Propiedad agregada porque se sobreescribirá después
            'p.enable_stock',
            'p.enable_sr_no',
            'p.name as product_actual_name',
            'pv.name as product_variation_name',
            'pv.is_dummy as is_dummy',
            'variations.name as variation_name',
            'variations.sub_sku',
            'p.barcode_type',
            'vld.qty_available',
            'variations.default_sell_price',
            'variations.sell_price_inc_tax',
            'variations.id as variation_id',
            'units.short_name as unit',
            'units.allow_decimal as unit_allow_decimal',
            'brands.name as brand',
            'vld.qty_reserved',
            'p.clasification',
            'variations.default_purchase_price as last_purchased_price',
            'brands.logo as brand_logo'
            // DB::raw("(SELECT purchase_price_inc_tax FROM purchase_lines WHERE 
            //             variation_id=variations.id ORDER BY id DESC LIMIT 1) as last_purchased_price")
        )
                ->first();

        return $products;
    }

    //Funcion NUEVA editada para Traslados
    public function getDetailsFromVariationTransfers($variation_id, $business_id, $warehouse_id = null, $check_qty = true)
    {
        $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
                ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
                ->leftjoin('variation_location_details AS vld', 'variations.id', '=', 'vld.variation_id')
                ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('brands', function ($join) {
                    $join->on('p.brand_id', '=', 'brands.id')
                        ->whereNull('brands.deleted_at');
                })
                ->where('p.status', 'active') // exclude inactive products
                ->where('p.business_id', $business_id)
                ->where('variations.id', $variation_id);

        //Add condition for check of quantity. (if stock is not enabled or qty_available > 0)
        if ($check_qty) {
            $query->where(function ($query) use ($warehouse_id) {
                $query->where('p.enable_stock', '!=', 1)
                    ->orWhere('vld.qty_available', '>', 0);
            });
        }
        
        if (!empty($warehouse_id)) {
            //Check for enable stock, if enabled check for location id.
            $query->where(function ($query) use ($warehouse_id) {
                        $query->where('p.enable_stock', '!=', 1)
                            ->orWhere('vld.warehouse_id', $warehouse_id);
            });
        }
        
        $products = $query->select(
            DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, 
                    ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
            'p.id as product_id',
            'p.tax as tax_id',
            'p.enable_stock',
            'p.enable_sr_no',
            'p.name as product_actual_name',
            'pv.name as product_variation_name',
            'pv.is_dummy as is_dummy',
            'variations.name as variation_name',
            'variations.sub_sku',
            'p.barcode_type',
            'vld.qty_available',
            'variations.default_sell_price',
            'variations.sell_price_inc_tax',
            'variations.id as variation_id',
            'units.short_name as unit',
            'units.allow_decimal as unit_allow_decimal',
            'brands.name as brand',
            'vld.qty_reserved',
            'variations.default_purchase_price as last_purchased_price'
            // DB::raw("(SELECT purchase_price_inc_tax FROM purchase_lines WHERE 
            //             variation_id=variations.id ORDER BY id DESC LIMIT 1) as last_purchased_price")
        )
                ->first();

        return $products;
    }

    /**
     * Calculates the total amount of invoice
     *
     * @param array $products
     * @param int $tax_id
     * @param array $discount['discount_type', 'discount_amount']
     *
     * @return Mixed (false, array)
     */
    public function calculateInvoiceTotal($products, $tax_percent, $discount = null)
    {

        if (empty($products)) {
            return false;
        }

        $output = ['total_before_tax' => 0, 'tax' => 0, 'discount' => 0, 'final_total' => 0];

        //Sub Total
        foreach ($products as $product) {
            $output['total_before_tax'] += ($this->num_uf($product['unit_price']) * $this->num_uf($product['quantity'])) / ($tax_percent + 1);

            //Add modifier price to total if exists
            if (!empty($product['modifier_price'])) {
                foreach ($product['modifier_price'] as $modifier_price) {
                    $output['total_before_tax'] += $this->num_uf($modifier_price);
                }
            }
        }

        //Calculate discount
        if (is_array($discount)) {
            if ($discount['discount_type'] == 'fixed') {
                $output['discount'] = $this->num_uf($discount['discount_amount']);
            } else {
                $output['discount'] = ($this->num_uf($discount['discount_amount'])/100)*$output['total_before_tax'];
            }
        }

        //Tax
        $output['tax'] = 0;
        if (!empty($tax_percent)) {
                $output['tax'] = $tax_percent * ($output['total_before_tax'] - $output['discount']);
        
        }
        
        //Calculate total
        $output['final_total'] = $output['total_before_tax'] + $output['tax'] - $output['discount'];
        
        return $output;
    }



    /**
     * Calculates the total amount of invoice
     *
     * @param array $products
     * @param int $tax_id
     * @param array $discount['discount_type', 'discount_amount']
     *
     * @return Mixed (false, array)
     */
    public function calculateInvoiceTotal_esa($products, $tax_id, $discount = null,$type_doc =null)
    {

        if (empty($products)) {
            return false;
        }

        /*new process*/
        $var_tax  = TaxRate::where('name','=', 'IVA')->get();
        $var_type_doc = DocumentType::find($type_doc);


        $output = ['total_before_tax' => 0, 'tax' => 0, 'discount' => 0, 'final_total' => 0];

        //Sub Total
        foreach ($products as $product) {
            $output['total_before_tax'] += $this->num_uf($product['unit_price_inc_tax']) * $this->num_uf($product['quantity']);

            //Add modifier price to total if exists
            if (!empty($product['modifier_price'])) {
                foreach ($product['modifier_price'] as $modifier_price) {
                    $output['total_before_tax'] += $this->num_uf($modifier_price);
                }
            }
        }

        if($var_type_doc->short_name == 'CCF')
          {
             $output['total_before_tax'] =  $output['total_before_tax'] /1.13;

          }else{

          }

        //Calculate discount
        if (is_array($discount)) {
            if ($discount['discount_type'] == 'fixed') {
                $output['discount'] = $this->num_uf($discount['discount_amount']);
            } else {
                $output['discount'] = ($this->num_uf($discount['discount_amount'])/100)*$output['total_before_tax'];
            }
        }

        if($var_type_doc->short_name == 'CCF')
          {
           
            $output['tax'] = 0;
              if (!empty($var_tax)) 
                {
                     $tax_details = TaxRate::find($var_tax[0]->id);
                     if(!empty($tax_details))
                     {
                        $output['tax_id'] = $tax_details->id;
                        $output['tax'] = ($tax_details->amount/100) * ($output['total_before_tax'] - $output['discount']);
                     }
                  

                }

          }
        else
          {
             //Tax
                $output['tax'] = 0;
                if (!empty($tax_id)) {
                  $tax_details = TaxRate::find($tax_id);
                    if (!empty($tax_details)) {
                      $output['tax_id'] = $tax_id;
                      $output['tax'] = ($tax_details->amount/100) * ($output['total_before_tax'] - $output['discount']);
                      }
                    }
          }
        //Calculate total
        $output['final_total'] = $output['total_before_tax'] + $output['tax'] - $output['discount'];
        
        return $output;
       
    }

    /**
     * Generates product sku
     *
     * @param string $string
     *
     * @return generated sku (string)
     */
    public function generateProductSku($string)
    {

        $business_id = request()->session()->get('user.business_id');
        $sku_prefix = Business::where('id', $business_id)->value('sku_prefix');

        return $sku_prefix . str_pad($string, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Gives list of trending products
     *
     * @param int $business_id
     * @param array $filters
     *
     * @return Obj
     */
    public function getTrendingProducts($business_id, $filters = [])
    {
        $query = Transaction::join(
            'transaction_sell_lines as tsl',
            'transactions.id',
            '=',
            'tsl.transaction_id'
        )
                    ->join('products as p', 'tsl.product_id', '=', 'p.id')
                    ->leftjoin('units as u', 'u.id', '=', 'p.unit_id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final');

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($filters['location_id'])) {
            $query->where('transactions.location_id', $filters['location_id']);
        }
        if (!empty($filters['category'])) {
            $query->where('p.category_id', $filters['category']);
        }
        if (!empty($filters['sub_category'])) {
            $query->where('p.sub_category_id', $filters['sub_category']);
        }
        if (!empty($filters['brand'])) {
            $query->where('p.brand_id', $filters['brand']);
        }
        if (!empty($filters['unit'])) {
            $query->where('p.unit_id', $filters['unit']);
        }
        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        } else {
            $query->limit(5);
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$filters['start_date'],
                $filters['end_date']]);
        }

        $sell_return_query = "(SELECT SUM(TPL.quantity) FROM transactions AS T JOIN purchase_lines AS TPL ON T.id=TPL.transaction_id WHERE TPL.product_id=tsl.product_id AND T.type='sell_return'";
        if ($permitted_locations != 'all') {
            $sell_return_query .= ' AND T.location_id IN ('
             . implode(',', $permitted_locations) . ') ';
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sell_return_query .= ' AND date(T.transaction_date) BETWEEN \'' . $filters['start_date'] . '\' AND \'' . $filters['end_date'] . '\'';
        }
        $sell_return_query .= ')';

        $products = $query->select(
            DB::raw("(SUM(tsl.quantity) - COALESCE($sell_return_query, 0)) as total_unit_sold"),
            'p.name as product',
            'u.short_name as unit'
        )
                        ->groupBy('tsl.product_id')
                        ->orderBy('total_unit_sold', 'desc')
                        ->get();
        return $products;
    }

    /**
     * Gives list of products based on products id and variation id
     *
     * @param int $business_id
     * @param int $product_id
     * @param int $variation_id = null
     *
     * @return Obj
     */
    public function getDetailsFromProduct($business_id, $product_id, $variation_id = null)
    {
        $product = Product::leftjoin('variations as v', 'products.id', '=', 'v.product_id')
                        ->whereNull('v.deleted_at')
                        ->where('products.business_id', $business_id);

        if (!is_null($variation_id) && $variation_id !== '0') {
            $product->where('v.id', $variation_id);
        }

        $product->where('products.id', $product_id);

        $products = $product->select(
            'products.id as product_id',
            'products.name as product_name',
            'v.id as variation_id',
            'v.name as variation_name'
        )
                    ->get();

        return $products;
    }

    /**
     * F => D (Previous product Increase)
     * D => F (All product decrease)
     * F => F (Newly added product drerease)
     *
     * @param  object $transaction_before
     * @param  object  $transaction
     * @param  array  $input
     *
     * @return void
     */
    public function adjustProductStockForInvoice($status_before, $transaction, $input)
    {

        if ($status_before == 'final' && $transaction->status == 'draft') {
            foreach ($input['products'] as $product) {
                if (!empty($product['transaction_sell_lines_id'])) {
                    $this->updateProductQuantity($input['location_id'], $product['product_id'], $product['variation_id'], $product['quantity']);
                }
            }
        } elseif ($status_before == 'draft' && $transaction->status == 'final') {
            foreach ($input['products'] as $product) {
                $this->decreaseProductQuantity(
                    $product['product_id'],
                    $product['variation_id'],
                    $input['location_id'],
                    $this->num_uf($product['quantity'])
                );
            }
        } elseif ($status_before == 'final' && $transaction->status == 'final') {
            foreach ($input['products'] as $product) {
                if (empty($product['transaction_sell_lines_id'])) {
                    $this->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $input['location_id'],
                        $this->num_uf($product['quantity'])
                    );
                }
            }
        }
    }

    /**
     * Updates variation from purchase screen
     *
     * @param array $variation_data
     *
     * @return void
     */
    public function updateProductFromPurchase($variation_data)
    {
        $variation_details = Variation::where('id', $variation_data['variation_id'])
                                        ->with(['product', 'product.product_tax'])
                                        ->first();
        $tax_rate = 0;
        if (!empty($variation_details->product->product_tax->amount)) {
            $tax_rate = $variation_details->product->product_tax->amount;
        }

        if (($variation_details->default_purchase_price != $variation_data['pp_without_discount']) ||
            ($variation_details->default_sell_price != $variation_data['default_sell_price'])
            ) {
            //Set default purchase price exc. tax
            $variation_details->default_purchase_price = $variation_data['pp_without_discount'];

            //Set default purchase price inc. tax
            $variation_details->dpp_inc_tax = $this->calc_percentage($variation_details->default_purchase_price, $tax_rate, $variation_details->default_purchase_price);
       
            //Set default sell price exc. tax
            $variation_details->default_sell_price = $variation_data['default_sell_price'];

            //set profit margin
            $variation_details->profit_percent = $this->get_percent($variation_details->default_purchase_price, $variation_details->default_sell_price);

            //set sell price inc. tax
            $variation_details->sell_price_inc_tax = $this->calc_percentage($variation_details->default_sell_price, $tax_rate, $variation_details->default_sell_price);
            
            $variation_details->save();
        }
    }

    /**
     * Generated SKU based on the barcode type.
     *
     * @param string $sku
     * @param string $c
     * @param string $barcode_type
     *
     * @return void
     */
    public function generateSubSku($sku, $c, $barcode_type)
    {
        $sub_sku = $sku . $c;

        if (in_array($barcode_type, ['C128', 'C39'])) {
            $sub_sku = $sku . '-' . $c;
        }

        return $sub_sku;
    }

    /**
     * Add rack details.
     *
     * @param int $business_id
     * @param int $product_id
     * @param array $product_racks
     * @param array $product_racks
     *
     * @return void
     */
    public function addRackDetails($business_id, $product_id, $product_racks)
    {

        if (!empty($product_racks)) {
            $data = [];
            foreach ($product_racks as $location_id => $detail) {
                $data[] = ['business_id' => $business_id,
                        'location_id' => $location_id,
                        'product_id' => $product_id,
                        'rack' => !empty($detail['rack']) ? $detail['rack'] : null,
                        'row' => !empty($detail['row']) ? $detail['row'] : null,
                        'position' => !empty($detail['position']) ? $detail['position'] : null,
                        'created_at' => \Carbon::now()->toDateTimeString(),
                        'updated_at' => \Carbon::now()->toDateTimeString()
                    ];
            }

            ProductRack::insert($data);
        }
    }

    /**
     * Get rack details.
     *
     * @param int $business_id
     * @param int $product_id
     *
     * @return void
     */
    public function getRackDetails($business_id, $product_id, $get_location = false)
    {

        $query = ProductRack::where('product_racks.business_id', $business_id)
                    ->where('product_id', $product_id);

        if ($get_location) {
            $racks = $query->join('business_locations AS BL', 'product_racks.location_id', '=', 'BL.id')
                ->select(['product_racks.rack',
                        'product_racks.row',
                        'product_racks.position',
                        'BL.name'])
                ->get();
        } else {
            $racks = collect($query->select(['rack', 'row', 'position', 'location_id'])->get());

            $racks = $racks->mapWithKeys(function ($item, $key) {
                return [$item['location_id'] => $item->toArray()];
            })->toArray();
        }

        return $racks;
    }

    /**
     * Update rack details.
     *
     * @param int $business_id
     * @param int $product_id
     * @param array $product_racks
     *
     * @return void
     */
    public function updateRackDetails($business_id, $product_id, $product_racks)
    {

        if (!empty($product_racks)) {
            foreach ($product_racks as $location_id => $details) {
                ProductRack::where('business_id', $business_id)
                    ->where('product_id', $product_id)
                    ->where('location_id', $location_id)
                    ->update(['rack' => !empty($details['rack']) ? $details['rack'] : null,
                            'row' => !empty($details['row']) ? $details['row'] : null,
                            'position' => !empty($details['position']) ? $details['position'] : null
                        ]);
            }
        }
    }

    /**
     * Retrieves selling price group price for a product variation.
     *
     * @param int $variation_id
     * @param int $price_group_id
     * @param int $tax_id
     *
     * @return decimal
     */
    public function getVariationGroupPrice($variation_id, $price_group_id, $tax_value)
    {
        $price_inc_tax = VariationGroupPrice::where('variation_id', $variation_id)
                        ->where('price_group_id', $price_group_id)
                        ->value('price_inc_tax');

        $price_exc_tax = $price_inc_tax;
        if (!empty($price_inc_tax) && !empty($tax_value)) {
             $price_exc_tax = $price_inc_tax / (1 + $tax_value);

        //     //$tax_amount = TaxRate::where('id', $tax_id)->value('amount');
        //     //$price_exc_tax = $this->calc_percentage_base($price_inc_tax, $tax_amount);
        }
        return [
            'price_inc_tax' => $price_inc_tax,
            'price_exc_tax' => $price_exc_tax
        ];
    }

    /**
     * Creates new variation if not exists.
     *
     * @param int $business_id
     * @param string $name
     *
     * @return obj
     */
    public function createOrNewVariation($business_id, $name)
    {
        $variation = VariationTemplate::where('business_id', $business_id)
                                    ->where('name', 'like', $name)
                                    ->with(['values'])
                                    ->first();

        if (empty($variation)) {
            $variation = VariationTemplate::create([
            'business_id' => $business_id,
            'name' => $name
            ]);
        }
        return $variation;
    }

    /**
     * Adds opening stock to a single product.
     *
     * @param int $business_id
     * @param obj $product
     * @param array $input
     * @param obj $transaction_date
     * @param int $user_id
     *
     * @return void
     */
    public function addSingleProductOpeningStock($business_id, $product, $input, $transaction_date, $user_id)
    {
      $locations = BusinessLocation::forDropdown($business_id)->toArray();

      $tax_percent = !empty($product->product_tax->amount) ? $product->product_tax->amount : 0;
      $tax_id = !empty($product->product_tax->id) ? $product->product_tax->id : null;

      foreach ($input as $key => $value) {
        $location_id = $key;
        $purchase_total = 0;
        //Check if valid location
        if (array_key_exists($location_id, $locations)) {
            $purchase_lines = [];

            $purchase_price = $this->num_uf(trim($value['purchase_price']));
            $item_tax = $this->calc_percentage($purchase_price, $tax_percent);
            $purchase_price_inc_tax = $purchase_price + $item_tax;
            $qty = $this->num_uf(trim($value['quantity']));

            $exp_date = null;
            if (!empty($value['exp_date'])) {
                $exp_date = \Carbon::createFromFormat('d-m-Y', $value['exp_date'])->format('Y-m-d');
            }

            $lot_number = null;
            if (!empty($value['lot_number'])) {
                $lot_number = $value['lot_number'];
            }

            if ($qty > 0) {
              $qty_formated = $this->num_f($qty);
              //Calculate transaction total
              $purchase_total += ($purchase_price_inc_tax * $qty);
              $variation_id = $product->variations->first()->id;

              $purchase_line = new PurchaseLine();
              $purchase_line->product_id = $product->id;
              $purchase_line->variation_id = $variation_id;
              $purchase_line->item_tax = $item_tax;
              $purchase_line->tax_id = $tax_id;
              $purchase_line->quantity = $qty;
              $purchase_line->pp_without_discount = $purchase_price;
              $purchase_line->purchase_price = $purchase_price;
              $purchase_line->purchase_price_inc_tax = $purchase_price_inc_tax;
              $purchase_line->exp_date = $exp_date;
              $purchase_line->lot_number = $lot_number;
              $purchase_lines[] = $purchase_line;

              $this->updateProductQuantity($location_id, $product->id, $variation_id, $qty_formated);
            }

            //create transaction & purchase lines
            if (!empty($purchase_lines)) {
              $transaction = Transaction::create(
                [
                  'type' => 'opening_stock',
                  'opening_stock_product_id' => $product->id,
                  'status' => 'received',
                  'business_id' => $business_id,
                  'transaction_date' => $transaction_date,
                  'total_before_tax' => $purchase_total,
                  'location_id' => $location_id,
                  'final_total' => $purchase_total,
                  'payment_status' => 'paid',
                  'created_by' => $user_id
                ]
              );
              $transaction->purchase_lines()->saveMany($purchase_lines);
            }

        }
      }
    }

    /**
     * Updates average cost and related data.
     *
     * @param  int  $variation_id
     * @param  float  $new_unit_cost
     * @param  float  $quantity
     * @param  bool  $subtract_qty
     * @return void
     */
    public function updateAverageCost($variation_id, $new_unit_cost, $quantity, $subtract_qty = false, $additional_data = null)
    {
        $variation = Variation::find($variation_id);

        $business_id = request()->session()->get('user.business_id');

        if (is_null($additional_data)) {
            $stock = VariationLocationDetails::where('variation_id', $variation_id)->sum('qty_available');
    
            if ($subtract_qty) {
                $stock -= $quantity;
            }

        } else {
            $result = DB::select(
                'CALL get_stock_before_a_specific_time(?, ?, ?, ?, ?)',
                [$business_id, $variation_id, $additional_data['purchase_line_id'], $additional_data['transaction_date'], $additional_data['flag_line']]
            );

            $stock = $result[0]->stock;
        }

        $tax_rate = 13;

        if (! empty($variation->product->tax)) {
            $tax_rate = $this->getTaxPercent($variation->product->tax);
        }

        if ($variation->default_purchase_price != $new_unit_cost) {
            // Set default purchase price exc. tax
            $variation->default_purchase_price = (($variation->default_purchase_price * $stock) + ($new_unit_cost * $quantity)) / ($stock + $quantity);
    
            // Set default purchase price inc. tax
            $variation->dpp_inc_tax = $this->calc_percentage($variation->default_purchase_price, $tax_rate, $variation->default_purchase_price);

            // Set profit margin
            $variation->profit_percent = $this->get_percent($variation->default_purchase_price, $variation->default_sell_price);

            $variation->save();
        }
    }

    /**
     * Checks if products has manage stock enabled then increases quantity
     * reserved for product and its variations.
     *
     * @param  int  $product_id
     * @param  int  $variation_id
     * @param  int  $location_id
     * @param  float  $new_quantity
     * @param  float  $old_quantity = 0
     * @param  int  $warehouse_id = null
     * @return boolean
     */
    public function incrementProductQtyReserved(
        $product_id,
        $variation_id,
        $location_id,
        $new_quantity,
        $old_quantity = 0,
        $warehouse_id = null
    ) {
        $qty_difference = $new_quantity - $old_quantity;

        $product = Product::find($product_id);

        # Check if stock is enabled or not
        if ($product->enable_stock == 1) {
            # Increment quantity in variations location table
            if(! empty($warehouse_id)) {
                VariationLocationDetails::where('variation_id', $variation_id)
                    ->where('product_id', $product_id)
                    ->where('location_id', $location_id)
                    ->where('warehouse_id', $warehouse_id)
                    ->increment('qty_reserved', $qty_difference);

            } else{
                VariationLocationDetails::where('variation_id', $variation_id)
                    ->where('product_id', $product_id)
                    ->where('location_id', $location_id)
                    ->increment('qty_reserved', $qty_difference);
            }
        }

        return true;
    }

    /**
     * Checks if products has manage stock enabled then Updates quantity for
     * product and its variations.
     *
     * @param  int  $location_id
     * @param  int  $product_id
     * @param  int  $variation_id
     * @param  float  $new_quantity
     * @param  float  $old_quantity = 0
     * @param  string  $number_format = null
     * @param  int  $warehouse_id = null
     * @return boolean
     */
    public function updateProductQtyReserved(
        $location_id,
        $product_id,
        $variation_id,
        $new_quantity,
        $old_quantity = 0,
        $number_format = null,
        $warehouse_id = null
    ) {
        $qty_difference = $this->num_uf($new_quantity, $number_format) - $this->num_uf($old_quantity, $number_format);

        $product = Product::find($product_id);

        // Check if stock is enabled or not
        if ($product->enable_stock == 1 && $qty_difference != 0) {
            $variation = Variation::where('id', $variation_id)
                ->where('product_id', $product_id)
                ->first();
            
            // Add quantity in variation_location_details
            if (! empty($warehouse_id)) {
                $variation_location_d = VariationLocationDetails::where('variation_id', $variation->id)
                    ->where('product_id', $product_id)
                    ->where('product_variation_id', $variation->product_variation_id)
                    ->where('location_id', $location_id)
                    ->where('warehouse_id', $warehouse_id)
                    ->first();

            } else {
                $variation_location_d = VariationLocationDetails::where('variation_id', $variation->id)
                    ->where('product_id', $product_id)
                    ->where('product_variation_id', $variation->product_variation_id)
                    ->where('location_id', $location_id)
                    ->first();
            }

            if (empty($variation_location_d)) {
                $variation_location_d = new VariationLocationDetails();
                $variation_location_d->variation_id = $variation->id;
                $variation_location_d->product_id = $product_id;
                $variation_location_d->location_id = $location_id;
                $variation_location_d->product_variation_id = $variation->product_variation_id;
                $variation_location_d->qty_available = 0;
                $variation_location_d->warehouse_id = $warehouse_id;
            }

            $variation_location_d->qty_reserved += $qty_difference;
            $variation_location_d->save();
        }
        
        return true;
    }

    /**
     * Checks if products has manage stock enabled then increase reservated quantity
     * for product and its variations.
     *
     * @param  int  $product_id
     * @param  int  $variation_id
     * @param  int  $location_id
     * @param  int  $warehouse_id
     * @param  int/float  $quantity
     * @return boolean
     */
    public function increaseReservedQuantity($product_id, $variation_id, $location_id, $warehouse_id, $quantity)
    {
        $product = Product::where('id', $product_id)->first();

        // Check if stock is enabled or not
        if ($product->enable_stock == 1) {
            // Increment reserved quantity in variation location details table
            $a = VariationLocationDetails::where('variation_id', $variation_id)
                ->where('product_id', $product_id)
                ->where('location_id', $location_id)
                ->where('warehouse_id', $warehouse_id)
                ->increment('qty_reserved', $quantity);
        }

        return true;
    }

    /**
     * @param  array  $input
     * @return void
     */
    public function updateQuantityReserved($input)
    {
        foreach ($input['products'] as $product) {
            $vld = VariationLocationDetails::where('variation_id', $product['variation_id'])
                ->where('location_id', $input['location_id'])
                ->where('warehouse_id', $input['warehouse_id'])
                ->first();

            $this->updateProductQuantity(
                $input['location_id'],
                $product['product_id'],
                $product['variation_id'],
                $product['quantity'],
                ! empty($vld) ? $vld->qty_reserved : 0,
                null,
                $input['warehouse_id'],
                'reserved'
            );
        }
    }

    /**
     * Recalculate average product cost based on transactions and update data.
     * 
     * @param  int  $variation_id
     */
    public function recalculateProductCost($variation_id)
    {
        $variation = Variation::find($variation_id);

        \Log::info('--- RECALCULATE COST - VARIATION: ' . $variation_id . ' ---');

        $business_id = request()->session()->get('user.business_id');

        $purchases = Transaction::join('purchase_lines', 'purchase_lines.transaction_id', 'transactions.id')
            ->where(function ($query) {
                $query->whereIn('transactions.type', ['opening_stock', 'purchase', 'purchase_transfer'])
                    ->orWhere('transactions.type', 'stock_adjustment')
                    ->where('transactions.adjustment_type', 'normal');
            })
            // ->whereIn('transactions.type', ['opening_stock', 'purchase'])
            ->where('transactions.business_id', $business_id)
            ->where('purchase_lines.variation_id', $variation_id)
            ->select('transactions.*')
            ->orderBy('transactions.transaction_date')
            ->orderBy('transactions.id')
            ->groupBy('transactions.id')
            ->get();

        $sells = Transaction::join('transaction_sell_lines', 'transaction_sell_lines.transaction_id', 'transactions.id')
            ->where('transactions.type', 'sell_return')
            ->where('transactions.business_id', $business_id)
            ->where('transaction_sell_lines.variation_id', $variation_id)
            ->select('transactions.*')
            ->orderBy('transactions.transaction_date')
            ->orderBy('transactions.id')
            ->groupBy('transactions.id')
            ->get();

        $transactions = collect();

        foreach ($purchases as $purchase) {
            $transactions->push($purchase);
        }

        foreach ($sells as $sell) {
            $transactions->push($sell);
        }

        $transactions = $transactions->sortBy('transaction_date');

        $tax_rate = 13;

        if (! empty($variation->product->tax)) {
            $tax_rate = $this->getTaxPercent($variation->product->tax);
        }

        $stock = 0;
        $purchase_price = 0;

        foreach ($transactions as $transaction) {
            \Log::info('TRANSACTION: ' . $transaction->id);

            // Allow recalculation of product cost
            $flag = false;

            // Purchase date
            $transaction_date = $transaction->transaction_date;

            // Add time when transaction_date ends at 00:00:00
            $hour = substr($transaction_date, 11, 18);

            if ($hour == '00:00:00' || $hour == '') {
                $transaction_date = substr($transaction_date, 0, 10) . ' ' . substr($transaction->created_at, 11, 18);
            }

            if ($transaction->type == 'purchase' && $transaction->purchase_type == 'international') {
                $has_apportionment = ApportionmentHasTransaction::where('transaction_id', $transaction->id)->first();

                if (! empty($has_apportionment)) {
                    $apportionment = Apportionment::find($has_apportionment->apportionment_id);
                    $flag = $apportionment->is_finished == 0 ? false : true;
                }

            } else {
                $flag = true;
            }

            if ($flag) {
                if ($transaction->type == 'sell_return') {
                    $lines = TransactionSellLine::join('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
                        ->where('transaction_sell_lines.transaction_id', $transaction->id)
                        ->where('transactions.business_id', $business_id)
                        ->where('transaction_sell_lines.variation_id', $variation_id)
                        ->select('transaction_sell_lines.*')
                        ->orderBy('transaction_sell_lines.id')
                        ->get();

                } else {
                    $lines = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
                        ->where('purchase_lines.transaction_id', $transaction->id)
                        ->where('transactions.business_id', $business_id)
                        ->where('purchase_lines.variation_id', $variation_id)
                        ->select('purchase_lines.*')
                        ->orderBy('purchase_lines.id')
                        ->get();
                }

                // Check if there are several lines of the same product in the transaction
                $flag_line = $lines->count() > 1 ? 1 : 0;

                foreach ($lines as $line) {
                    \Log::info('LINE: ' . $line->id);

                    $result = DB::select(
                        'CALL get_stock_before_a_specific_time(?, ?, ?, ?, ?)',
                        [$business_id, $variation_id, $line->id, $transaction_date, $flag_line]
                    );

                    $stock = $result[0]->stock;

                    if ($transaction->type == 'purchase_transfer') {
                        $stock -= $line->quantity;
                    }

                    $line_price = $transaction->type == 'sell_return' ? $line->unit_cost_exc_tax : $line->purchase_price;

                    if ($purchase_price != $line_price) {
                        // Set default purchase price exc. tax
                        if (($stock + $line->quantity) != 0) {
                            $variation->default_purchase_price = (($purchase_price * $stock) + ($line_price * $line->quantity)) / ($stock + $line->quantity);
                        } else {
                            $variation->default_purchase_price = $line_price;
                        }
                
                        // Set default purchase price inc. tax
                        $variation->dpp_inc_tax = $this->calc_percentage($variation->default_purchase_price, $tax_rate, $variation->default_purchase_price);

                        // Set profit margin
                        $variation->profit_percent = $this->get_percent($variation->default_purchase_price, $variation->default_sell_price);

                        $variation->save();

                        $purchase_price = $variation->default_purchase_price;
                    }
                }
            }
        }
    }

    /**
     * Verify that sku is unique.
     * 
     * @param  string  $sku
     * @param  int  $product_id
     * @param  int  $business_id
     * @return boolean
     */
    public function checkSkuUnique($sku, $product_id, $business_id)
    {
        $sku = mb_strtoupper($sku);

        $products = Product::whereRaw('UPPER(sku) = UPPER(?)', [$sku])
            ->where('business_id', $business_id);

        if ($product_id > 0) {
            $products = $products->where('id', '!=', $product_id);
        }

        $products = $products->get();

        $result = 0;

        if (count($products) == 0) {
            $result = 1;
        }

        return $result;
    }

    /**
     * Sync category. Create, update or delete category for others business
     * 
     * @param int $id
     * @param string $name
     * @param App\Category $delete
     * @param App\Utils\TransactionUtil $transactionUtil
     * @param string $module_name
     * 
     * @return void
     * @author Arquímides Martínez
     */
    public function syncCategory($id, $name = "", $delete = null, $transactionUtil = null, $module_name = "") {
        /** Get others business */
        $business_id = auth()->user()->business_id;
        $business = Business::where('id', '!=', $business_id)
            ->select('id')->get();

        /** If there is not more business, exit */
        if (empty($business)) {
            return true;
        }

        /** Start delete category */
        if (!is_null($delete)) {
            foreach ($business as $b) {
                $category = Category::where('name', $delete->name)
                    ->where('business_id', $b->id)
                    ->first();

                /** delete category */
                if (!empty($category)) {
                    $category->delete();

                    // Store binnacle
                    $transactionUtil->registerBinnacle(
                        $module_name,
                        'delete',
                        $delete->name,
                        $delete
                    );
                }
            }

            return true; // exit
        }
        /** End delete category */

        /** Start create or update category */
        $category = Category::findOrFail($id)->toArray();

        /** remove needless columns */
        unset(
            $category['id'],
            $category['deleted_at'],
            $category['created_at'],
            $category['updated_at'],
            $category['catalogue_id']
        );

        foreach ($business as $b) {

            /** New business */
            $category['business_id'] = $b->id;

            /** Create or update cloned category */
            $cloned_cat = Category::updateOrCreate(
                [
                    'name' => $name,
                    'business_id' => $b->id
                ],
                $category
            );

            /** If is subcategory */
            if ($cloned_cat->parent_id) {
                $parent_cat = Category::find($cloned_cat->parent_id);

                /** Find this business category */
                if (!empty($parent_cat)) {
                    $parent_cat_id = Category::where('name', $parent_cat->name)
                        ->where('business_id', $b->id)
                        ->value('id');
                        
                    /** If category exists */
                    if ($parent_cat_id) {
                        $cloned_cat->parent_id = $parent_cat_id;
                        $cloned_cat->save();
                    }
                }
            }
        }
        /** End create or update category */
    }

    /**
     * Sync unit. Create, update or delete unit for others business
     * 
     * @param int $id
     * @param string $name
     * @param App\Unit $delete
     * 
     * @return void
     * @author Arquímides Martínez
     */
    public function syncUnit($id, $name = "", $delete = null) {
        /** Get others business */
        $business_id = auth()->user()->business_id;
        $business = Business::where('id', '!=', $business_id)
            ->select('id')->get();

        /** If there is not more business, exit */
        if (empty($business)) {
            return true;
        }

        /** Start delete unit */
        if (!is_null($delete)) {
            foreach ($business as $b) {
                $unit = Unit::where('actual_name', $delete->actual_name)
                    ->where('business_id', $b->id)
                    ->first();

                /** delete unit */
                if (!empty($unit)) {
                    $unit->delete();
                }
            }

            return true; // exit
        }
        /** End delete unit */

        /** Start create or update unit */
        $unit = Unit::findOrFail($id)->toArray();
     
        /** remove needless columns */
        unset(
            $unit['id'],
            $unit['deleted_at'],
            $unit['created_at'],
            $unit['updated_at']
        );

        foreach ($business as $b) {
            /** New business */
            $unit['business_id'] = $b->id;

            /** Create or update cloned unit */
            $cloned_unit = Unit::updateOrCreate(
                [
                    'actual_name' => $name,
                    'business_id' => $b->id
                ],
                $unit
            );
        }
    }

    /**
     * Sync brand. Create, update or delete brand for others business
     * 
     * @param int $id
     * @param string $name
     * @param App\Brands $delete
     * 
     * @return void
     * @author Arquímides Martínez
     */
    public function syncBrand($id, $name = "", $delete = null) {
        /** Get others business */
        $business_id = auth()->user()->business_id;
        $business = Business::where('id', '!=', $business_id)
            ->select('id')->get();

        /** If there is not more business, exit */
        if (empty($business)) {
            return true;
        }

        /** Start delete brand */
        if (!is_null($delete)) {
            foreach ($business as $b) {
                $brand = Brands::where('name', $delete->name)
                    ->where('business_id', $b->id)
                    ->first();

                /** delete brand */
                if (!empty($brand)) {
                    $brand->delete();
                }
            }

            return true; // exit
        }
        /** End delete brand */

        /** Start create or update brand */
        $brand = Brands::findOrFail($id)->toArray();
     
        /** remove needless columns */
        unset(
            $brand['id'],
            $brand['deleted_at'],
            $brand['created_at'],
            $brand['updated_at']
        );

        foreach ($business as $b) {
            /** New business */
            $brand['business_id'] = $b->id;

            /** Create or update cloned brand */
            $cloned_brand = Brands::updateOrCreate(
                [
                    'name' => $name,
                    'business_id' => $b->id
                ],
                $brand
            );
        }
    }

    /**
     * Sync tax group. Create, update or delete taxes for others business
     * 
     * @param int $id
     * @param string $description
     * @param App\TaxGroup $delete
     * 
     * @return void
     * @author Arquímides Martínez
     */
    public function syncTaxGroup($id, $description = "", $delete = null) {
        /** Get others business */
        $business_id = auth()->user()->business_id;
        $business = Business::where('id', '!=', $business_id)
            ->select('id')->get();

        /** If there is not more business, exit */
        if (empty($business)) {
            return true;
        }

        /** Start delete tax group */
        if (!is_null($delete)) {
            foreach ($business as $b) {
                $tax = TaxGroup::where('description', $delete->description)
                    ->where('business_id', $b->id)
                    ->first();

                /** delete group */
                if (!empty($tax)) {
                    $tax->tax_rates()->detach();

                    $tax->delete();
                }
            }

            return true; // exit
        }
        /** End delete brand */

        /** Start create or update brand */
        $tax = TaxGroup::findOrFail($id)->toArray();
     
        /** remove needless columns */
        unset(
            $tax['id'],
            $tax['deleted_at'],
            $tax['created_at'],
            $tax['updated_at']
        );

        foreach ($business as $b) {
            /** New business */
            $tax['business_id'] = $b->id;

            /** Create or update cloned tax */
            $cloned_tax = TaxGroup::updateOrCreate(
                [
                    'description' => $description,
                    'business_id' => $b->id
                ],
                $tax
            );

            $tax_rates = $this->syncTaxRate($id, $b->id);

            $cloned_tax->tax_rates()->sync($tax_rates);
        }
    }

    /**
     * Sync taxes. Create and update taxes for others business
     * 
     * @param int $id
     * @param int $cloned_business_id
     * 
     * @return array
     * @author Arquímides Martínez
     */
    private function syncTaxRate($id, $cloned_business_id) {
        $business_id = auth()->user()->business_id;
        $business = Business::where('id', '!=', $business_id)
            ->select('id')->get();

        $taxes = TaxRateTaxGroup::where('tax_group_id', $id)->get();
    
        $ids = [];
        foreach ($taxes as $t) {
            $tax = TaxRate::findOrFail($t->tax_rate_id)->toArray();

            /** remove needless columns */
            unset(
                $tax['id'],
                $tax['deleted_at'],
                $tax['created_at'],
                $tax['updated_at'],
            );

            $tax['business_id'] = $cloned_business_id;

            $cloned_tax = TaxRate::updateOrCreate(
                [
                    'name' => $tax['name'],
                    'type' => $tax['type'],
                    'business_id' => $cloned_business_id
                ],
                $tax
            );

            array_push($ids, $cloned_tax->id);
        }

        return $ids;
    }

    /**
     * Sync product for all businees
     * 
     * @param int $id
     * @author Arquímides Martínez
     */
    public function syncProduct($id, $sku, $type) {
        $business_id = auth()->user()->business_id;

        $business = Business::where('id', '!=', $business_id)
            ->select('id')->get();

        /** If there is not more than one business, exit */
        if (empty($business)) {
            return true;
        }

        /** Star delete product */
            // TODO
        /** Star delete product */

        /** Start create o update product */
        $product = collect();
        foreach ($business as $b) {
            $product = Product::findOrFail($id)->toArray();
            $product['business_id'] = $b->id;

            /** Remove needless columns */
            unset(
                $product['id'],
                $product['sku'],
                $product['created_at'],
                $product['updated_at']
            );

            /** Brand */
            $brand = Brands::find($product['brand_id']);
            
            if (!empty($brand)) {
                /** Sync brand */
                $this->syncBrand($brand->id, $brand->name);

                $brand = Brands::where('name', $brand->name)
                    ->where('business_id', $b->id)
                    ->first();

                $product['brand_id'] = !empty($brand) ? $brand->id : null;
            }

            /** Unit */
            $unit = Unit::find($product['unit_id']);

            if (!empty($unit)) {
                /** Sync unit */
                $this->syncUnit($unit->id, $unit->actual_name);

                $unit = Unit::where('actual_name', $unit->actual_name)
                    ->where('business_id', $b->id)
                    ->first();

                $product['unit_id'] = !empty($unit) ? $unit->id : null;
            }

            /** Category */
            $category = Category::find($product['category_id']);

            if (!empty($category)) {
                /** Sync category */
                $this->syncCategory($category->id, $category->name);

                $category = Category::where('name', $category->name)
                    ->where('business_id', $b->id)
                    ->first();

                $product['category_id'] = !empty($category) ? $category->id : null;
            }

            /** Subcategory */
            $sub_category = Category::find($product['sub_category_id']);

            if (!empty($sub_category)) {
                /** Sync subcategory */
                $this->syncCategory($sub_category->id, $sub_category->name);

                $sub_category = Category::where('name', $sub_category->name)
                    ->where('business_id', $b->id)
                    ->where('parent_id', '!=', '0')
                    ->first();

                $product['sub_category_id'] = !empty($sub_category) ? $sub_category->id : null;
            }

            /** Tax */
            $tax = TaxGroup::find($product['tax']);

            if (!empty($tax)) {
                /** Sync tax groups */
                $this->syncTaxGroup($tax->id, $tax->description);

                $tax = TaxGroup::where('description', $tax->description)
                    ->where('business_id', $b->id)
                    ->where('type', $tax->type)
                    ->first();

                $product['tax'] = !empty($tax) ? $tax->id : null;
            }

            $cloned_product = Product::updateOrCreate(
                [
                    'sku' => $sku,
                    'business_id' => $product['business_id']
                ],
                $product
            );

            if ($product['clasification'] == 'product') {
                $suppliers = ProductHasSuppliers::where('product_id', $id)->get();

                $old_suppliers = [];
                foreach ($suppliers as $s) {
                    $prod_supplier = ProductHasSuppliers::updateOrCreate(
                        [
                            'product_id' => $cloned_product->id,
                            'contact_id' => $s->contact_id
                        ],
                        [
                            'catalogue' => $s->catalogue,
                            'uxc' => $s->uxc,
                            'weight' => $s->weight,
                            'dimensions' => $s->dimensions,
                            'custom_field' => $s->custom_field
                        ]
                    );

                    array_push($old_suppliers, $prod_supplier->contact_id);
                }

                if (!empty($old_suppliers)) {
                    $deleted = ProductHasSuppliers::where('product_id', $cloned_product->id)
                        ->whereNotIn('contact_id', [$old_suppliers])
                        ->get();
                } else {
                    $deleted = ProductHasSuppliers::where('product_id', $cloned_product->id)
                        ->get();
                }
                /** Delete product has suppliers */
                if (!empty($deleted)) {
                    foreach ($deleted as $d) {
                        $d->delete();
                    }
                }

            } else if ($product['clasification'] == 'kits') {
                $kits = KitHasProduct::where('parent_id', $id)->get();
                KitHasProduct::where('parent_id', $cloned_product->id)->forceDelete();

                $old_children = [];
                foreach ($kits as $k) {
                    $fields = [];
                    
                    /** Child product */
                    $child_prod_sku = Variation::where('id', $k->children_id)->value('sub_sku');
                    
                    if (!empty($child_prod_sku)) {
                        $child_prod = Product::join('variations as v', 'products.id', 'v.product_id')
                            ->where('products.business_id', $b->id)
                            ->where('v.sub_sku', $child_prod_sku)
                            ->select('v.id')
                            ->first();
                            
                        $fields['children_id'] = $child_prod->id;
                    } else {
                        return true;
                    }

                    $fields['quantity'] = $k->quantity;
                    
                    $unit = Unit::findOrFail($k->unit_id);

                    if (!empty($unit)) {
                        $unit = Unit::where('actual_name', $unit->actual_name)
                            ->where('business_id', $b->id)
                            ->first();

                        $fields['unit_id'] = !empty($unit) ? $unit->id : null;
                    }

                    $new_child = KitHasProduct::updateOrCreate(
                        [
                            'parent_id' => $cloned_product->id,
                            'children_id' => $fields['children_id']
                        ],
                        $fields
                    );
                }
            }

            /** create single and variations */
            if ($cloned_product->type == 'single') {
                if ($type == "store") {
                    $variation = Variation::where('product_id', $id)->first();
                    $this->createSingleProductVariation(
                        $cloned_product->id,
                        $cloned_product->sku,
                        $variation->default_purchase_price,
                        $variation->dpp_inc_tax,
                        $variation->profit_percent,
                        $variation->default_sell_price,
                        $variation->sell_price_inc_tax
                    );
                } else if ($type == "update") {
                    $variation = Variation::where('product_id', $id)
                        ->first();

                    if (!empty($variation)) {
                        $cloned_variation = Variation::where('product_id', $cloned_product->id)
                            ->first();

                        $cloned_variation->sub_sku = $cloned_product->sku;
                        $cloned_variation->default_purchase_price = $variation->default_purchase_price;
                        $cloned_variation->dpp_inc_tax = $variation->dpp_inc_tax;
                        $cloned_variation->profit_percent = $variation->profit_percent;
                        $cloned_variation->default_sell_price = $variation->default_sell_price;
                        $cloned_variation->sell_price_inc_tax = $variation->sell_price_inc_tax;
                        $cloned_variation->save();
                    }
                }

            } else if ($cloned_product->type == 'variable') {
                // TODO
            }
        }
        /** End create o update product */
    }
}
