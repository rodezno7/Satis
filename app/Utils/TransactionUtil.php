<?php

namespace App\Utils;

use App\User;
use App\Quote;

use App\Kardex;

use App\Cashier;
use App\Contact;
use App\Product;
use App\Suplies;
use App\TaxRate;
use App\Business;
use App\Currency;
use App\Customer;
use App\TaxGroup;
use App\Variation;
use App\Transaction;
use App\DocumentType;
use App\MovementType;
use App\PurchaseLine;
use App\InvoiceScheme;
use App\KitHasProduct;
use App\Utils\TaxUtil;
use App\BusinessLocation;
use App\CashierClosure;
use App\get_sub_products;
use App\Utils\Contacttil;
use App\TransactionPayment;

use App\DocumentCorrelative;
use App\Employees;
use App\Restaurant\ResTable;
use App\TransactionSellLine;
use App\TransactionTaxDetail;
use App\VariationLocationDetails;
use Illuminate\Support\Facades\DB;
use App\Events\TransactionPaymentAdded;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\PurchaseSellMismatch;
use App\Events\TransactionPaymentDeleted;
use App\Events\TransactionPaymentUpdated;
use App\QuoteLine;
use App\TransactionHasImportExpense;
use App\TransactionSellLinesPurchaseLines;

class TransactionUtil extends Util
{
    protected $taxUtil;
    protected $contactUtil;

    /**
     * Constructor
     *
     * @param TaxUtil $taxUtil
     * @return void
     */
    public function __construct(TaxUtil $taxUtil, ContactUtil $contactUtil){
        $this->taxUtil = $taxUtil;
        $this->contactUtil = $contactUtil;

        $this->ticket_print_format = 'ticket';
        $this->cfc_print_format = 'fiscal_credit';

        // Short names of document types
        $this->document_name = ['FCF', 'CCF'];

        // Number of decimal places to store and use in calculations
        $this->price_precision = config('app.price_precision');

        // Number of decimal places to show on invoices
        $this->quantity_precision = 2;
    }

    /**
     * Add Sell transaction
     *
     * @param int $business_id
     * @param array $input
     * @param float $invoice_total
     * @param int $user_id
     * @param  suplies
     * @return boolean
     */
    public function createSellTransaction($business_id, $input, $invoice_total, $user_id)
    {
        $invoice_no = !empty($input['invoice_no']) ? $input['invoice_no'] : $this->getInvoiceNumber($business_id, $input['status'], $input['location_id']);
        $transaction = Transaction::create([
            'business_id' => $business_id,
            'location_id' => $input['location_id'],
            'warehouse_id' => $input['warehouse_id'],
            'cashier_id' => isset($input['cashier_id']) ? $input['cashier_id'] : null,
            'type' => 'sell',
            'status' => $input['status'],
            'cashier_closure_id' => isset($input['cashier_closure_id']) ? $input['cashier_closure_id'] : null,
            'customer_id' => $input['customer_id'],
            'customer_name' => mb_strtoupper($input['customer_name']),
            'customer_dui' => isset($input['customer_dui']) ? $input['customer_dui'] : null,
            'customer_group_id' => $input['customer_group_id'],
            'invoice_no' => $invoice_no,
            'ref_no' => '',
            'total_before_tax' => $this->num_uf($input['subtotal']),// $invoice_total['total_before_tax'],
            'transaction_date' => $input['transaction_date'],
            'tax_id' => $input['tax_group_id'] > 0 ? $input['tax_group_id'] : null, //'tax_id' => $input['tax_rate_id'],
            'discount_type' => $input['discount_type'],
            'discount_amount' => $this->num_uf($input['discount_amount']),
            'tax_amount' => $this->num_uf($input['withheld']), //Withheld amount //$invoice_total['tax'],
            'final_total' => $this->num_uf($input['final_total']),
            'additional_notes' => $input['sale_note'],
            'staff_note' => !empty($input['staff_note']) ? $input['staff_note'] : null,
            'created_by' => $user_id,
            'is_direct_sale' => !empty($input['is_direct_sale']) ? $input['is_direct_sale'] : 0,
            'commission_agent' => $input['commission_agent'],
            'is_quotation' => isset($input['is_quotation']) ? $input['is_quotation'] : 0,
            'shipping_details' => isset($input['shipping_details']) ? $input['shipping_details'] : null,
            'shipping_charges' => isset($input['shipping_charges']) ? $this->num_uf($input['shipping_charges']) : 0,
            'exchange_rate' => !empty($input['exchange_rate']) ?
                                $this->num_uf($input['exchange_rate']) : 1,
            'selling_price_group_id' => isset($input['selling_price_group_id']) ? $input['selling_price_group_id'] : null,
            'payment_condition' => isset($input['payment_condition']) ? $input['payment_condition'] : null,
            'pay_term_number' => isset($input['pay_term_number']) ? $input['pay_term_number'] : null,
            'pay_term_type' => isset($input['pay_term_type']) ? $input['pay_term_type'] : null,
            'is_suspend' => !empty($input['is_suspend']) ? 1 : 0,
            'document_types_id' => $input['document_types_id'],
            'correlative' => $input['correlatives'],
            'serie' => isset($input['serie']) ? $input['serie'] : null,
            'resolution' => isset($input['resolution']) ? $input['resolution'] : null,
            'return_parent_id' => $input['return_parent_id'] ?? null,
            'parent_correlative' => $input['parent_correlative'] ?? null,
            'delivered_by' => $input['delivered_by'],
            'delivered_by_dui' => $input['delivered_by_dui'],
            'delivered_by_passport' => $input['delivered_by_passport'],
            'received_by' => $input['received_by'],
            'received_by_dui' => $input['received_by_dui'],
            'document_correlative_id' => isset($input['document_correlative_id']) ? $input['document_correlative_id'] : null,
            'customer_vehicle_id' => isset($input['customer_vehicle_id']) ? $input['customer_vehicle_id'] : null,
        ]);

        return $transaction;
    }

    /**
     * Add Sell transaction
     *
     * @param mixed $transaction_id
     * @param int $business_id
     * @param array $input
     * @param float $invoice_total
     * @param int $user_id
     *
     * @return boolean
     */
    public function updateSellTransaction($transaction_id, $business_id, $input, $invoice_total, $user_id)
    {
        $transaction = $transaction_id;

        if (!is_object($transaction)) {
            $transaction = Transaction::where('id', $transaction_id)
                        ->where('business_id', $business_id)
                        ->firstOrFail();
        }
        
        //Update invoice number if changed from draft to finalize or vice-versa
        $invoice_no = $transaction->invoice_no;
        if ($transaction->status != $input['status']) {
            $invoice_no = $this->getInvoiceNumber($business_id, $input['status'], $transaction->location_id);
        }

        $update_date = [
            'status' => $input['status'],
            'invoice_no' => $invoice_no,
            // 'contact_id' => $input['contact_id'],
            'customer_id' => $input['customer_id'],
            // 'cashier_id' => isset($input['cashier_id']) ? $input['cashier_id'] : null,
            'customer_name' => isset($input['customer_name']) ? $input['customer_name'] : $transaction->customer_name,
            'customer_dui' => isset($input['customer_dui']) ? $input['customer_dui'] : $transaction->customer_dui,
            'customer_group_id' => $input['customer_group_id'],
            'total_before_tax' => $this->num_uf($input['subtotal']),
            // 'tax_id' => $input['tax_rate_id'],
            'discount_type' => $input['discount_type'],
            'discount_amount' => $this->num_uf($input['discount_amount']),
            'tax_amount' => $this->num_uf($input['withheld']),
            'final_total' => $this->num_uf($input['final_total']),
            'additional_notes' => $input['sale_note'],
            'staff_note' => !empty($input['staff_note']) ? $input['staff_note'] : null,
            'commission_agent' => $input['commission_agent'],
            'is_quotation' => isset($input['is_quotation']) ? $input['is_quotation'] : 0,
            'shipping_details' => isset($input['shipping_details']) ? $input['shipping_details'] : null,
            'shipping_charges' => isset($input['shipping_charges']) ? $this->num_uf($input['shipping_charges']) : 0,
            'exchange_rate' => !empty($input['exchange_rate']) ?
                                $this->num_uf($input['exchange_rate']) : 1,
            'selling_price_group_id' => isset($input['selling_price_group_id']) ? $input['selling_price_group_id'] : null,
            'pay_term_number' => isset($input['pay_term_number']) ? $input['pay_term_number'] : null,
            'pay_term_type' => isset($input['pay_term_type']) ? $input['pay_term_type'] : null,
            'is_suspend' => !empty($input['is_suspend']) ? 1 : 0,
            'document_correlative_id' => isset($input['document_correlative_id']) ? $input['document_correlative_id'] : null,
            'customer_vehicle_id' => isset($input['customer_vehicle_id']) ? $input['customer_vehicle_id'] : $transaction->customer_vehicle_id,
        ];

        if (!empty($input['transaction_date'])) {
            $update_date['transaction_date'] = $input['transaction_date'];
        }
        
        $transaction->fill($update_date);
        $transaction->update();

        return $transaction;
    }

    /**
     * Add/Edit transaction sell lines
     *
     * @param object/int $transaction
     * @param array $products
     * @param array $location_id
     * @param boolean $return_deleted = false
     * @param array $extra_line_parameters = []
     *   Example: ['database_trasnaction_linekey' => 'products_line_key'];
     *
     * @return boolean/object
     */

    public function CreateOrUpdateSuplies($products,$business_id)
    {

       $new_elements = [];
          /*  foreach ($products as $product) 
            {
                $id = $product['product_id'];

                $var_discount = get_sub_products::where('principal_product','=',$id)
                                               ->where('business_id','=',$business_id); 

               if(count($var_discount) >0)
               {
                 foreach ($var_discount as $key => $elements)
                 {
                       $update_total = Suplies::WHERE('id','=',$elements['productid'])->SELECT('id','quantity','business_id');
                       $decreased = $product['quantity']*$elements['quantity'];

                       $new_elements['id'] = $update_total['id'];
                       $new_elements['business_id'] = $update_total['business_id'];
                       $new_elements['quantity'] =  ($update_total['quantity']-$decreased);
                 }
               }

            }

            return $new_elements;*/
    }
    
    public function createOrUpdateSellLines($transaction, $products, $location_id, $return_deleted = false, $status_before = null, $extra_line_parameters = [], $adjust_qty = true)
    {
        $lines_formatted = [];
        $modifiers_array = [];
        $edit_ids = [0];
        $modifiers_formatted = [];

        $tax_exempt = DocumentType::where('id', $transaction->document_types_id)->first();

        if (!empty($tax_exempt)) {
            $tax_exempt = $tax_exempt->tax_exempt ? true : false;
        } else {
            $tax_exempt = false;
        }

        foreach ($products as $product) {
            $variation = Variation::find($product['variation_id']);

            $product['unit_cost_exc_tax'] = isset($product['unit_cost_exc_tax']) ? $product['unit_cost_exc_tax'] : $variation->default_purchase_price;
            $product['unit_cost_inc_tax'] = isset($product['unit_cost_inc_tax']) ? $product['unit_cost_inc_tax'] : $variation->dpp_inc_tax;

            // Check if transaction_sell_lines_id is set.
            if (! empty($product['transaction_sell_lines_id'])) {
                $edit_ids[] = $product['transaction_sell_lines_id'];
                $this->editSellLine($product, $location_id, $status_before, $adjust_qty);

                //update or create modifiers for existing sell lines
                if ($this->isModuleEnabled('modifiers')) {
                    if (!empty($product['modifier'])) {
                        foreach ($product['modifier'] as $key => $value) {
                            if (!empty($product['modifier_sell_line_id'][$key])) {
                                // Dont delete modifier sell line if exists
                                $edit_ids[] = $product['modifier_sell_line_id'][$key];

                            } else {
                                if (!empty($product['modifier_price'][$key])) {
                                    $this_price = $this->num_uf($product['modifier_price'][$key]);
                                    $modifiers_formatted[] = new TransactionSellLine([
                                        'product_id' => $product['modifier_set_id'][$key],
                                        'variation_id' => $value,
                                        'quantity' => 1,
                                        'unit_price_before_discount' => $this_price,
                                        'unit_price' => $this_price,
                                        'unit_price_inc_tax' => $this_price,
                                        'parent_sell_line_id' => $product['transaction_sell_lines_id']
                                    ]);
                                }
                            }
                        }
                    }
                }

            } else {
                // Calculate unit price and unit price before discount
                $unit_price_before_discount = $this->num_uf($product['unit_price']);
                $unit_price = $unit_price_before_discount;

                if (!empty($product['line_discount_type']) && $product['line_discount_amount']) {
                    $discount_amount = $this->num_uf($product['line_discount_amount']);

                    if ($product['line_discount_type'] == 'fixed') {
                        $unit_price = $unit_price_before_discount - $discount_amount;
                    } elseif ($product['line_discount_type'] == 'percentage') {
                        $unit_price = ((100 - $discount_amount) * $unit_price_before_discount) / 100;
                    }
                }

                $unit_price_exc_tax = $this->num_uf($product['unit_price_exc_tax']);

                $tax_percent = isset($product['tax_group_id']) ? $this->taxUtil->getTaxPercent($product['tax_group_id']) : null;

                $tax_amount = $tax_percent > 0 ? (!$tax_exempt ? ($unit_price_exc_tax * $tax_percent) : 0) : 0;

                $line = [
                    'product_id' => $product['product_id'],
                    'variation_id' => $product['variation_id'],
                    'quantity' => $this->num_uf($product['quantity']),
                    'unit_price_before_discount' => $this->num_uf($product['u_price_exc_tax']), // $unit_price_before_discount,
                    'unit_price' => $this->num_uf($product['u_price_inc_tax']), // $unit_price,
                    'line_discount_type' => !empty($product['line_discount_type']) ? $product['line_discount_type'] : null,
                    'line_discount_amount' => !empty($product['line_discount_amount']) ? $this->num_uf($product['line_discount_amount']) : 0,
                    // 'item_tax' => null, //$this->num_uf($product['item_tax']),
                    'tax_id' => isset($product['tax_group_id']) ? $product['tax_group_id'] : null, // $product['tax_id'],
                    'tax_amount' => $this->num_uf($tax_amount),
                    'unit_price_exc_tax' => $this->num_uf($product['unit_price_exc_tax']),
                    'unit_price_inc_tax' => $this->num_uf($product['unit_price_inc_tax']),
                    'sell_line_note' => !empty($product['sell_line_note']) ? $product['sell_line_note'] : '',
                    'service_parent_id' => ! empty($product['service_parent_id']) ? $product['service_parent_id'] : null,
                    'unit_cost_exc_tax' => $this->num_uf($product['unit_cost_exc_tax']),
                    'unit_cost_inc_tax' => $this->num_uf($product['unit_cost_inc_tax']),
                    'sale_price' => isset($product['sale_price']) ? $this->num_uf($product['sale_price']) : null
                ];

                foreach ($extra_line_parameters as $key => $value) {
                    $line[$key] = !empty($product[$value]) ? $product[$value] : '';
                }

                if (session()->has('business') && request()->session()->get('business.enable_lot_number') == 1 && !empty($product['lot_no_line_id'])) {
                    $line['lot_no_line_id'] = $product['lot_no_line_id'];
                }

                // Check if restaurant module is enabled then add more data related to that.
                if ($this->isModuleEnabled('modifiers')) {
                    $sell_line_modifiers = [];

                    if (!empty($product['modifier'])) {
                        foreach ($product['modifier'] as $key => $value) {
                            if (!empty($product['modifier_price'][$key])) {
                                $this_price = $this->num_uf($product['modifier_price'][$key]);
                                $sell_line_modifiers[] = [
                                    'product_id' => $product['modifier_set_id'][$key],
                                    'variation_id' => $value,
                                    'quantity' => 1,
                                    'unit_price_before_discount' => $this_price,
                                    'unit_price' => $this_price,
                                    'unit_price_inc_tax' => $this_price
                                ];
                            }
                        }
                    }

                    $modifiers_array[] = $sell_line_modifiers;
                }

                $lines_formatted[] = new TransactionSellLine($line);
            }
        }

        if (!is_object($transaction)) {
            $transaction = Transaction::findOrFail($transaction);
        }

        // Delete the products removed and increment product stock.
        $deleted_lines = [];

        if (!empty($edit_ids)) {
            $deleted_lines = TransactionSellLine::where('transaction_id', $transaction->id)
                ->whereNotIn('id', $edit_ids)
                ->select('id')
                ->get()
                ->toArray();

            $this->deleteSellLines($deleted_lines, $location_id, $transaction->warehouse_id, $adjust_qty);
        }

        if (!empty($lines_formatted)) {
            $sell_lines = $transaction->sell_lines()->saveMany($lines_formatted);

            // Add corresponding modifier sell lines if exists
            if ($this->isModuleEnabled('modifiers')) {
                foreach ($lines_formatted as $key => $value) {
                    if (!empty($modifiers_array[$key])) {
                        foreach ($modifiers_array[$key] as $modifier) {
                            $modifier['parent_sell_line_id'] = $value->id;
                            $modifiers_formatted[] = new TransactionSellLine($modifier);
                        }
                    }
                }
            }
        }

        // Create transaction tax details
        //$this->createTransactionTaxDetail($sell_lines);

        if (!empty($modifiers_formatted)) {
            $transaction->sell_lines()->saveMany($modifiers_formatted);
        }

        if ($return_deleted) {
            return $deleted_lines;
        }

        return true;
    }

    /**
     * create transaction tax detail
     *
     * @param TransactionSellLine $sell_lines
     *
     * @return void
     */
    private function createTransactionTaxDetail($sell_lines){
        if(!empty($sell_lines)){
            foreach($sell_lines as $sl){
                $tax_rate = TaxGroup::find($sl->tax_id);

                if(!empty($tax_rate)){
                    foreach($tax_rate->tax_rates as $tr){
                        $trans_tax_detail = new TransactionTaxDetail();
    
                        $trans_tax_detail->sell_line_id = $sl->id;
                        $trans_tax_detail->tax_group_id = $sl->tax_id;
                        $trans_tax_detail->tax_rate_id = $tr->id;
                        $trans_tax_detail->transaction_type = 'sell';
                        
                        $amount = $sl->unit_price_exc_tax * ($tr->percent / 100);
                        $trans_tax_detail->tax_amount = $amount;
    
                        $trans_tax_detail->save();
                    }
                }
            }
        }
    }

    /**
     * Update transaction tax details
     *
     * @param TransactionSellLine $sell_lines
     *
     * @return void
     */
    private function updateTransactionTaxDetail($sell_line){
        if(!empty($sell_line)){
            $tax_rate = TaxGroup::find($sell_line["tax_id"]);

            foreach($tax_rate->tax_rates as $tr){
                $trans_tax_detail = TransactionTaxDetail::where('sell_line_id', $sell_line["transaction_sell_lines_id"])
                    ->first();

                $trans_tax_detail->tax_group_id = $sell_line['tax_id'];
                $trans_tax_detail->tax_rate_id = $tr->id;
                $trans_tax_detail->transaction_type = 'sell';
                
                $amount = $sell_line['unit_price_exc_tax'] * ($tr->percent / 100);
                $trans_tax_detail->tax_amount = $amount;

                $trans_tax_detail->save();
            }
        }
    }

    /**
     * Delete transaction tax tax details
     * 
     * @param TransactionSellLine $sell_line
     * @return void
     */

    public function deleteTransactionTaxDetail($sell_line){
        if(!empty($sell_line)){
            foreach($sell_line as $sl){
                $trans_tax_detail = TransactionTaxDetail::
                    where('sell_line_id', $sl->id)
                    ->first();
                if(!empty($trans_tax_detail)){
                    $trans_tax_detail->delete();
                }
            }
        }
    }

    /**
     * Edit transaction sell line
     *
     * @param  array  $product
     * @param  int  $location_id
     * @param  bool  $adjust_qty
     * @return boolean
     */
    public function editSellLine($product, $location_id, $status_before, $adjust_qty = true)
    {
        // Get the old order quantity
        $sell_line = TransactionSellLine::find($product['transaction_sell_lines_id']);

        // Adjust quantity
        if ($status_before != 'draft' && $adjust_qty) {
            $difference = $sell_line->quantity - $this->num_uf($product['quantity']);
            $this->adjustQuantity($location_id, $product['product_id'], $product['variation_id'], $difference);
        }
       
        $unit_price_before_discount = $this->num_uf($product['unit_price']);
        $unit_price = $unit_price_before_discount;

        if (!empty($product['line_discount_type']) && $product['line_discount_amount']) {
            $discount_amount = $this->num_uf($product['line_discount_amount']);

            if ($product['line_discount_type'] == 'fixed') {
                $unit_price = $unit_price_before_discount - $discount_amount;
            } elseif ($product['line_discount_type'] == 'percentage') {
                $unit_price = ((100 - $discount_amount) * $unit_price_before_discount) / 100;
            }
        }

        // Update sell lines.
        $sell_line->fill([
            'product_id' => $product['product_id'],
            'variation_id' => $product['variation_id'],
            'quantity' => $this->num_uf($product['quantity']),
            'unit_price_before_discount' => $unit_price_before_discount,
            'unit_price' => $unit_price,
            'line_discount_type' => !empty($product['line_discount_type']) ? $product['line_discount_type'] : null,
            'line_discount_amount' => !empty($product['line_discount_amount']) ? $this->num_uf($product['line_discount_amount']) : 0,
            // 'item_tax' => $this->num_uf($product['item_tax']),
            'tax_id' => isset($product['tax_group_id']) ? $product['tax_group_id'] : null,
            'unit_price_inc_tax' => $this->num_uf($product['unit_price_inc_tax']),
            'sell_line_note' => !empty($product['sell_line_note']) ? $product['sell_line_note'] : '',
            'service_parent_id' => ! empty($product['service_parent_id']) ? $product['service_parent_id'] : null,
            'unit_cost_exc_tax' => $this->num_uf($product['unit_cost_exc_tax']),
            'unit_cost_inc_tax' => $this->num_uf($product['unit_cost_inc_tax'])
        ]);

        $sell_line->save();
    }

    /**
     * Delete the products removed and increment product stock.
     *
     * @param  array  $transaction_line_ids
     * @param  int  $location_id
     * @param  bool  $adjust_qty
     * @return boolean
     */
    public function deleteSellLines($transaction_line_ids, $location_id, $warehouse_id = null, $adjust_qty = true)
    {
        if (!empty($transaction_line_ids)) {
            $sell_lines = TransactionSellLine::whereIn('id', $transaction_line_ids)
                ->get();

            // Adjust quantity
            if ($adjust_qty) {
                foreach ($sell_lines as $line) {
                    if (!empty($warehouse_id)) {
                        $this->adjustQuantity($location_id, $line->product_id, $line->variation_id, $line->quantity, $warehouse_id);
                    } else {
                        $this->adjustQuantity($location_id, $line->product_id, $line->variation_id, $line->quantity);
                    }
                }
            }

            TransactionSellLine::whereIn('id', $transaction_line_ids)
                ->delete();
        }
    }

    /**
     * Delete the products removed and increment product stock. Includes kits.
     *
     * @param  array  $transaction_line_ids
     * @param  int  $location_id
     * @param  int  $warehouse_id
     * @return boolean
     */
    public function deleteSaleLines($transaction_line_ids, $location_id, $warehouse_id)
    {
        if (! empty($transaction_line_ids)) {
            $sell_lines = TransactionSellLine::whereIn('id', $transaction_line_ids)
                ->get();

            // Adjust quanity
            foreach ($sell_lines as $line) {
                $this->adjustStock(
                    $location_id,
                    $warehouse_id,
                    $line->product_id,
                    $line->variation_id,
                    $line->quantity
                );
            }

            TransactionSellLine::whereIn('id', $transaction_line_ids)
                ->delete();
        }
    }

    /**
     * Adjust the quantity of product and its variation
     *
     * @param int $location_id
     * @param int $product_id
     * @param int $variation_id
     * @param float $increment_qty
     *
     * @return boolean
     */
    public function adjustQuantity($location_id, $product_id, $variation_id, $increment_qty, $warehouse_id = null)
    {
        if ($increment_qty != 0) {
            $enable_stock = Product::find($product_id)->enable_stock;

            if ($enable_stock == 1) {
                // Adjust Quantity in variations location table
                if (!empty($warehouse_id)) {
                    VariationLocationDetails::where('variation_id', $variation_id)
                        ->where('product_id', $product_id)
                        ->where('location_id', $location_id)
                        ->where('warehouse_id', $warehouse_id)
                        ->increment('qty_available', $increment_qty);
                } else {
                    VariationLocationDetails::where('variation_id', $variation_id)
                        ->where('product_id', $product_id)
                        ->where('location_id', $location_id)
                        ->increment('qty_available', $increment_qty);
                }

                // TODO:Update quantity in products table
                // Product::where('id', $product_id)
                //     ->increment('total_qty_available', $increment_qty);
            }
        }
    }

    /**
     * Adjust the quantity of product and its variation. Includes kits.
     *
     * @param  int  $location_id
     * @param  int  $$warehouse_id
     * @param  int  $product_id
     * @param  int  $variation_id
     * @param  float  $increment_qty
     * @return void
     */
    public function adjustStock($location_id, $warehouse_id, $product_id, $variation_id, $increment_qty)
    {
        if ($increment_qty != 0) {
            $product = Product::find($product_id);

            // Adjust quantity in variations location table
            if ($product->clasification == 'kits') {
                $childrens = KitHasProduct::where('parent_id', $product->id)->get();

                foreach ($childrens as $item) {
                    $variation_q = Variation::where('id', $item->children_id)->first();

                    VariationLocationDetails::where('product_id', $variation_q->product_id)
                        ->where('variation_id', $item->children_id)
                        ->where('location_id', $location_id)
                        ->where('warehouse_id', $warehouse_id)
                        ->increment('qty_available', $item->quantity * $increment_qty);
                }

            } elseif ($product->clasification == 'product' || $product->clasification == 'material') {

                if ($product->enable_stock == 1) {
                    VariationLocationDetails::where('variation_id', $variation_id)
                        ->where('product_id', $product_id)
                        ->where('location_id', $location_id)
                        ->where('warehouse_id', $warehouse_id)
                        ->increment('qty_available', $increment_qty);
                }
            }
        }
    }

    //Funcion NUEVA editada para Traslados
    private function adjustQuantityTransfer($location_id, $warehouse_id, $product_id, $variation_id, $increment_qty)
    {
        if ($increment_qty != 0) {
            $enable_stock = Product::find($product_id)->enable_stock;

            if ($enable_stock == 1) {
                //Adjust Quantity in variations location table
                VariationLocationDetails::where('variation_id', $variation_id)
                ->where('product_id', $product_id)
                ->where('location_id', $location_id)
                ->where('warehouse_id', $warehouse_id)
                ->increment('qty_available', $increment_qty);

                //TODO:Update quantity in products table
                // Product::where('id', $product_id)
                //     ->increment('total_qty_available', $increment_qty);
            }
        }
    }

    /**
     * Add line for payment
     *
     * @param  object/int  $transaction
     * @param  array  $payments
     * @param  int  $business_id
     * @param  int  $user_id
     * @param  string  $note
     * @return boolean
     */
    public function createOrUpdatePaymentLines($transaction, $payments, $business_id = null, $user_id = null, $note = null)
    {
        $payments_formatted = [];
        $edit_ids = [0];
        $account_transactions = [];
        
        if (!is_object($transaction)) {
            $transaction = Transaction::findOrFail($transaction);
        }

        //If status is draft don't add payment
        if ($transaction->status == 'draft') {
            return true;
        }
        $c = 0;
        foreach ($payments as $payment) {
            //Check if transaction_sell_lines_id is set.
            if (!empty($payment['payment_id'])) {
                $edit_ids[] = $payment['payment_id'];
                $this->editPaymentLine($payment, $transaction);
            } else {
                //If amount is 0 then skip.
                if ($this->num_uf($payment['amount']) != 0) {
                    $prefix_type = 'sell_payment';
                    if ($transaction->type == 'purchase') {
                        $prefix_type = 'purchase_payment';
                    }
                    $ref_count = $this->setAndGetReferenceCount($prefix_type, $business_id);
                    //Generate reference number
                    $payment_ref_no = $this->generateReferenceNumber($prefix_type, $ref_count, $business_id);

                    // Adjust payment from 2 to 6 decimal places
                    $payment['amount'] = $this->convertPayment($transaction->id, $this->num_uf($payment['amount']));

                    $payment_data = [
                        'amount' => $this->num_uf($payment['amount']),
                        'method' => $payment['method'],
                        'business_id' => $transaction->business_id,
                        'is_return' => isset($payment['is_return']) ? $payment['is_return'] : 0,
                        'card_holder_name' => isset($payment['card_holder_name']) ? $payment['card_holder_name'] : null,
                        'card_authotization_number' => isset($payment['card_authotization_number']) ? $payment['card_authotization_number'] : null,
                        'card_type' => isset($payment['card_type']) ? $payment['card_type'] : null,
                        'card_pos' => isset($payment['card_pos']) ? $payment['card_pos'] : null,
                        'check_number' => isset($payment['check_number']) ? $payment['check_number'] : null,
                        'check_account' => isset($payment['check_account']) ? $payment['check_account'] : null,
                        'check_bank' => isset($payment['check_bank']) ? $payment['check_bank'] : null,
                        'check_account_owner' => isset($payment['check_account_owner']) ? $payment['check_account_owner'] : null,
                        'transfer_ref_no' => isset($payment['transfer_ref_no']) ? $payment['transfer_ref_no'] : null,
                        'transfer_issuing_bank' => isset($payment['transfer_issuing_bank']) ? $payment['transfer_issuing_bank'] : null,
                        'transfer_destination_account' => isset($payment['transfer_destination_account']) ? $payment['transfer_destination_account'] : null,
                        'transfer_receiving_bank' => isset($payment['transfer_receiving_bank']) ? $payment['transfer_receiving_bank'] : null,
                        'note' => ! is_null($note) ? $note : $payment['note'],
                        'paid_on' => $transaction->transaction_date, //empty($payment['paid_on']) ? $payment['paid_on'] : \Carbon::now()->toDateTimeString(),
                        'created_by' => empty($user_id) ? auth()->user()->id : $user_id,
                        'payment_for' => $transaction->contact_id,
                        'payment_ref_no' => $payment_ref_no,
                        'account_id' => !empty($payment['account_id']) ? $payment['account_id'] : null,
                        'cashier_id' => !empty($transaction->cashier_id) ? $transaction->cashier_id : null
                    ];
                    /*if ($payment['method'] == 'custom_pay_1') {
                        $payment_data['transaction_no'] = $payment['transaction_no_1'];
                    } else if ($payment['method'] == 'custom_pay_2') {
                        $payment_data['transaction_no'] = $payment['transaction_no_2'];
                    } else if ($payment['method'] == 'custom_pay_3') {
                        $payment_data['transaction_no'] = $payment['transaction_no_3'];
                    }*/

                    $payments_formatted[] = new TransactionPayment($payment_data);

                    $account_transactions[$c] = [];

                    //create account transaction
                    if (!empty($payment['account_id'])) {
                        $payment_data['transaction_type'] = $transaction->type;
                        $account_transactions[$c] = $payment_data;
                    }

                    $c++;
                }
            }
        }

        //Delete the payment lines removed.
        if (!empty($edit_ids)) {
            $deleted_transaction_payments = $transaction->payment_lines()->whereNotIn('id', $edit_ids)->get();

            $transaction->payment_lines()->whereNotIn('id', $edit_ids)->delete();

            //Fire delete transaction payment event
            foreach ($deleted_transaction_payments as $deleted_transaction_payment) {
                if (!empty($deleted_transaction_payment->account_id)) {
                    event(new TransactionPaymentDeleted($deleted_transaction_payment->id, $deleted_transaction_payment->account_id));
                }
            }
        }

        if (!empty($payments_formatted)) {
            $transaction->payment_lines()->saveMany($payments_formatted);

            foreach ($transaction->payment_lines as $key => $value) {
                if (!empty($account_transactions[$key])) {
                    event(new TransactionPaymentAdded($value, $account_transactions[$key]));
                }
            }
        }

        return true;
    }

    /**
     * Delete lines for payment
     *
     * @param object $transaction
     * @return boolean
     */
    public function deletePaymentLines($transaction){
        $payment_lines = $transaction->payment_lines()->get();

        //Delete the payment lines removed.
        if (!empty($payment_lines)) {
            $deleted_transaction_payments = $payment_lines;

            $transaction->payment_lines()->delete();

            //Fire delete transaction payment event
            foreach ($deleted_transaction_payments as $deleted_transaction_payment) {
                if (!empty($deleted_transaction_payment->account_id)) {
                    event(new TransactionPaymentDeleted($deleted_transaction_payment->id, $deleted_transaction_payment->account_id));
                }
            }
        }

        return true;
    }

    /**
     * Edit transaction payment line
     *
     * @param array $product
     *
     * @return boolean
     */
    public function editPaymentLine($payment, $transaction = null)
    {
        $payment_id = $payment['payment_id'];
        unset($payment['payment_id']);

        if ($payment['method'] == 'custom_pay_1') {
            $payment['transaction_no'] = $payment['transaction_no_1'];
        } else if ($payment['method'] == 'custom_pay_2') {
            $payment['transaction_no'] = $payment['transaction_no_2'];
        } else if ($payment['method'] == 'custom_pay_3') {
            $payment['transaction_no'] = $payment['transaction_no_3'];
        }

        unset($payment['transaction_no_1'], $payment['transaction_no_2'], $payment['transaction_no_3']);
        
        $payment['amount'] = $this->num_uf($payment['amount']);

        $tp = TransactionPayment::where('id', $payment_id)->first();

        $transaction_type = ! empty($transaction->type) ? $transaction->type : null;

        if (empty($tp->transaction_id)) {
            $payment['transaction_id'] = $transaction->id;
        }
        
        $tp->update($payment);

        // Event
        event(new TransactionPaymentUpdated($tp, $transaction_type));

        return true;
    }

    /**
     * Get payment line for a transaction
     *
     * @param int $transaction_id
     *
     * @return boolean
     */
    public function getPaymentDetails($transaction_id)
    {
        $payment_lines = TransactionPayment::where('transaction_id', $transaction_id)
                    ->get()->toArray();

        return $payment_lines;
    }

    public function getFormatDetails($transaction_id, $invoice_layout, $business_id = null, $location_details = null){
        $il = $invoice_layout;

        $transaction = Transaction::find($transaction_id);
        $transaction_type = $transaction->type;
        $cashier_code = Cashier::where('id', $transaction->cashier_id)->value('code');
        $doc = DocumentCorrelative::where('document_type_id', $transaction->document_types_id)->first();
        $business = Business::where('id', $business_id)->first();

        // Number of decimals
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $precision = $product_settings['decimals_in_fiscal_documents'];

        //Customer info
        $customer = Customer::leftJoin("countries as cnt", "customers.country_id", "cnt.id")
            ->leftJoin("states as st", "customers.state_id", "st.id")
            ->leftJoin("cities as ct", "customers.city_id", "ct.id")
            ->where("customers.id", $transaction->customer_id)
            ->select(
                "cnt.name as country", "st.name as state", "ct.name as city",
                "customers.*"
                )
            ->first();
        // Seller name
        $employee = Quote::join('employees', 'employees.id', 'quotes.employee_id')
                ->where('quotes.transaction_id', $transaction_id)
                ->select("employees.first_name", "employees.last_name")
                ->first();
                
        $employee_first_name = "";
        $employee_last_name = "";
        if(!empty($employee)){
            $efn = explode(" ", $employee->first_name);
            $employee_first_name = $efn[0] ?? " ";
            $eln = explode(" ", $employee->last_name);
            $employee_last_name = $eln[0] ?? " ";
        }
        
        $output = [];
        // Business details
        $output['business_name'] = $business->business_full_name;
        $output['business_nrc'] = $business->nrc;
        $output['business_line'] = $business->line_of_business;
        $output['business_nit'] = $business->nit;
        $output['location_name'] = $location_details->name;
        $output['location_landmark'] = $location_details->landmark;
        $output['location_mobile'] = $location_details->mobile;

        // Commission agent or seller
        $commission_agent = User::find($transaction->commission_agent);
        $output['commission_agent'] = 
            !empty($commission_agent) ?
                $commission_agent->first_name . ' ' . $commission_agent->last_name :
                $this->getOrderSeller($transaction_id);

        // Documents details
        $output['document']['res_ticket'] = $doc->resolution ?? '';
        $output['document']['serie'] = $doc->serie ?? '';
        $output['document']['serie_correlative'] = ! empty($doc->serie) ? $doc->serie .'|' .$doc->initial .' AL ' . $doc->serie . '|'. $doc->final : '';
        $output['document']['serie_correlative_2'] = ! empty($doc->serie) ? 'DEL ' . $doc->serie . '-' . $doc->initial . ' AL ' . $doc->serie . '-' . $doc->final : '';
        
        $output['seller_name'] = $employee_first_name . ' '. $employee_last_name;
        $output['cashier_code'] = $cashier_code;
        $output['transaction_date'] = date('d/m/Y', strtotime($transaction->transaction_date));
        $output['transaction_hour'] = date('h:m:i', strtotime($transaction->transaction_date));
        $output['customer_name'] =  $customer->is_default ? $transaction->customer_name : ($customer->business_name ?? $customer->name);
        $output['customer_landmark'] = $customer->address;
        $output['customer_location'] =
            $customer->address ?
                $customer->address . ", " . implode(', ', array_filter([$customer->city, $customer->state, $customer->country])) :
                    implode(', ', array_filter([$customer->city, $customer->state, $customer->country]));
        $output['customer_city'] = $customer->city;
        $output['customer_state'] = $customer->state;
        $output['customer_tax_number'] = $customer->reg_number;
        $output['customer_dui'] = $customer->is_default ? $transaction->customer_dui : $customer->dni;
        $output['customer_nit'] =  $customer->tax_number;
        $output['customer_business_activity'] = $customer->business_line;
        $output['customer_employee_name'] = $this->contactUtil->getCustomerEmployeeName($customer->id);
        $output['customer_business_name'] = $customer->business_name;
        $output['customer_phone'] = $customer->telphone;
        $output['customer_id'] = $customer->id;
        $output['customer_seller'] = $this->getOrderSeller($transaction_id);
        
        // first name and lastname for ticket
        $name_m = explode (" ", $output['customer_name']);
        $count = count($name_m);
        $output['customer_short_name'] = $count == 1 ? $name_m[0] : ($name_m[0] . " ". $name_m[1] ?? "");
        
        //Invoice product lines
        $is_lot_number_enabled = request()->session()->get('business.enable_lot_number');
        $is_product_expiry_enabled = request()->session()->get('business.enable_product_expiry');

        /** Receipt lines */
        $output['lines'] = [];
        if ($transaction_type == 'sell') {
            $sell_line_relations = ['modifiers'];

            if ($is_lot_number_enabled == 1) {
                $sell_line_relations[] = 'lot_details';
            }

            $lines = $transaction->sell_lines()->whereNull('parent_sell_line_id')->with($sell_line_relations)->get();

            $details = $this->_receiptDetailsSellLines($lines, $il, $is_product_expiry_enabled, $is_lot_number_enabled);

            if (config('app.business') == 'workshop') {
                $product_lines = array();
                
                foreach ($details['lines'] as $detail) {
                    if ($detail['id'] == $detail['service_parent_id']) {
                        $product_line = $detail;
                        $spare_lines = array();

                        foreach ($details['lines'] as $det) {
                            if ($det['id'] != $det['service_parent_id'] && $product_line['id'] == $det['service_parent_id']) {
                                array_push($spare_lines, $det);
                            }
                        }

                        $product_line['spare_rows'] = $spare_lines;

                        array_push($product_lines, $product_line);

                    } else if (is_null($detail['service_parent_id'])) {
                        array_push($product_lines, $detail);
                    }
                }

                if (count($product_lines) > 0) {
                    $output['lines'] = $product_lines;
                } else {
                    $output['lines'] = $details['lines'];
                }

            } else {
                $output['lines'] = $details['lines'];
            }
            
        }  elseif ($transaction_type == 'sell_return') {
            $parent_sell = Transaction::find($transaction->return_parent_id);
            $lines = $parent_sell->sell_lines;
            $output['return_parent_correlative'] = $parent_sell->correlative;
            $output['tax_amount_returned'] = $this->taxUtil->getTaxAmount($parent_sell->id, 'sell_return');

            $details = $this->_receiptDetailsSellReturnLines($lines, $il, $is_product_expiry_enabled, $is_lot_number_enabled);
            $output['lines'] = $details['lines'];
            $output['taxes'] = [];
            foreach ($details['lines'] as $line) {
                if (!empty($line['group_tax_details'])) {
                    foreach ($line['group_tax_details'] as $tax_group_detail) {
                        if (!isset($output['taxes'][$tax_group_detail['name']])) {
                            $output['taxes'][$tax_group_detail['name']] = 0;
                        }
                        $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                    }
                }
            }
        }

        // Correlative
        $output['correlative'] = $transaction->correlative;
        // Transaction id
        $output['transaction_id'] = $transaction->id;
        //Shipping charges & details
        $output['shipping_charges'] = ($transaction->shipping_charges != 0) ? $transaction->shipping_charges : 0;
        $output['shipping_details'] = $transaction->shipping_details;
        // Find sell return
        // $output[''] = $transaction->return_parent->correlative;

        $document_type = DocumentType::find($transaction->document_types_id);
        //Total
        $output['total_before_tax'] = $transaction->total_before_tax;
        $output['discount_amount'] = $transaction->discount_amount > 0 ? $this->getDiscountValue($transaction->total_before_tax, $transaction->discount_type, $transaction->discount_amount) : 0;
        $output['discount_percent'] = '(' . $transaction->discount_amount . '%)';
        $output['total'] = $transaction->final_total;
        $output['total_letters'] = $this->getAmountLetters($transaction->final_total);
        $output['invoice_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', $transaction->transaction_date)->format('M d, Y H:i');
        $output['payment_condition'] = $transaction->payment_condition;

        // Taxes
        $output['tax_amount'] = $this->taxUtil->getTaxAmount($transaction->id, 'sell', $output['discount_amount']);
        $output['tax_group_amount'] = $transaction->tax_group_amount;
        
        $tax_type = $this->taxUtil->getTaxType($transaction->id);
        /** EXEMPT - WITHHELD - PERCEPTION */
        $output['exempt'] = $tax_type == 0 ? $transaction->final_total : "";
        $output['withheld'] = $tax_type == -1 ? $transaction->tax_amount : "";
        $output['perception'] = $tax_type == 1 ? $transaction->tax_amount : "";

        $output['delivered_by'] = $transaction->delivered_by;
        $output['delivered_by_dui'] = $transaction->delivered_by_dui;
        $output['delivered_by_passport'] = $transaction->delivered_by_passport;
        $output['received_by'] = $transaction->received_by;
        $output['received_by_dui'] = $transaction->received_by_dui;

        //Transaction payments
        $tp = $this->sumTransactionPayments($transaction->id);
        $output['payments']['cash'] = $tp->total_cash;
        $output['payments']['card'] = $tp->total_card;
        $output['payments']['check'] = $tp->total_check;
        $output['payments']['bank'] = $tp->total_bank;
        $output['payments']['cash_return'] = $tp->total_cash_return;
        $output['payments']['total_cash'] = $tp->total_cash_return + $tp->total_cash;
        $output['payments']['total'] = $tp->total_cash + $tp->total_card + $tp->total_check + $tp->total_bank;

        $output['is_exempt'] = $customer->is_exempt;

        //Additional notes
        $output['additional_notes'] = $transaction->additional_notes;

        // Way to pay
        $output['payment_term'] = ! empty($transaction->pay_term_number) ? 'FORMA DE PAGO: ' . $transaction->pay_term_number . ' DÍAS' : '';

        // Comment
        $output['staff_note'] = $transaction->staff_note;

        // Order taxes
        $tax_percent = $this->taxUtil->getLinesTaxPercent($transaction->id);
        $output['order_taxes'] =  ($transaction->total_before_tax - $output['discount_amount']) * $tax_percent;

        // Heading & invoice label, when quotation use the quotation heading.
        if ($transaction_type == 'sell_return') {
            $output['invoice_heading'] = $il->cn_heading;
            $output['invoice_no_prefix'] = $il->cn_no_label;
        }

        $output['discount_amount'] = $this->num_f($output['discount_amount'], false, $precision);

        // Months
        $months = array(
            '1' => ucfirst(strtolower(__('accounting.january'))),
            '2' => ucfirst(strtolower(__('accounting.february'))),
            '3' => ucfirst(strtolower(__('accounting.march'))),
            '4' => ucfirst(strtolower(__('accounting.april'))),
            '5' => ucfirst(strtolower(__('accounting.may'))),
            '6' => ucfirst(strtolower(__('accounting.june'))),
            '7' => ucfirst(strtolower(__('accounting.july'))),
            '8' => ucfirst(strtolower(__('accounting.august'))),
            '9' => ucfirst(strtolower(__('accounting.september'))),
            '10' => ucfirst(strtolower(__('accounting.october'))),
            '11' => ucfirst(strtolower(__('accounting.november'))),
            '12' => ucfirst(strtolower(__('accounting.december')))
        );

        $output['months'] = $months;
        
        return (object)$output;
    }

    /**
     * Get Order seller
     * 
     * @param int $transaction_id
     * @return string
     */
    public function getOrderSeller($transacion_id) {
        if (empty($transacion_id)) {
            return "";
        }

        $order_seller = Quote::where('transaction_id', $transacion_id)
            ->value('employee_id');

        $seller = Employees::where('id', $order_seller)
            ->select(DB::raw('CONCAT(first_name, " ", IFNULL(last_name, "")) as seller'))
            ->first();

        return $seller->seller ?? "";
    }

    public function getTicketDetails($transaction_id, $invoice_layout, $business_id = null, $location_details = null){
        $il = $invoice_layout;

        $transaction = Transaction::find($transaction_id);
        $transaction_type = $transaction->type;
        $cashier_code = Cashier::where('id', $transaction->cashier_id)->value('code');
        $doc = DocumentCorrelative::where('document_type_id', $transaction->document_types_id)->first();
        $business = Business::where('id', $business_id)->first();

        //Customer info
        $customer = Customer::leftJoin("countries as cnt", "customers.country_id", "cnt.id")
            ->leftJoin("states as st", "customers.state_id", "st.id")
            ->leftJoin("cities as ct", "customers.city_id", "ct.id")
            ->where("customers.id", $transaction->customer_id)
            ->select(
                "cnt.name as country", "st.name as state", "ct.name as city",
                "customers.*"
                )
            ->first();
        // Seller name
        // $user = User::where('id', $transaction->created_by)->select('first_name', 'last_name')->first();
        $employee = Quote::join('employees', 'employees.id', 'quotes.employee_id')
                ->where('quotes.transaction_id', $transaction_id)
                ->select("employees.first_name", "employees.last_name")
                ->first();
                
        $employee_first_name = "";
        $employee_last_name = "";
        if(!empty($employee)){
            $efn = explode(" ", $employee->first_name);
            $employee_first_name = $efn[0] ?? " ";
            $eln = explode(" ", $employee->last_name);
            $employee_last_name = $eln[0] ?? " ";
        }
        
        $output = [];
        // Business details
        $output['business_name'] = $business->business_full_name;
        $output['business_nrc'] = $business->nrc;
        $output['business_line'] = $business->line_of_business;
        $output['business_nit'] = $business->nit;
        $output['location_name'] = $location_details->name;
        $output['location_landmark'] = $location_details->landmark;
        $output['location_mobile'] = $location_details->mobile;

        // Commission agent or seller
        $commission_agent = User::find($transaction->commission_agent);
        $output['commission_agent'] = ! empty($commission_agent) ? $commission_agent->first_name . ' ' . $commission_agent->last_name : '';

        // Documents details
        $output['document']['res_ticket'] = $doc->resolution;
        $output['document']['serie'] = $doc->serie;
        $output['document']['serie_correlative'] = $doc->serie .'|' .$doc->initial .' AL ' . $doc->serie . '|'. $doc->final;
        $output['document']['serie_correlative_2'] = 'DEL ' . $doc->serie . '-' . $doc->initial . ' AL ' . $doc->serie . '-' . $doc->final;
        
        $output['seller_name'] = $employee_first_name . ' '. $employee_last_name;
        $output['cashier_code'] = $cashier_code;
        $output['transaction_date'] = date('d/m/Y', strtotime($transaction->transaction_date));
        $output['transaction_hour'] = date('h:m:i', strtotime($transaction->transaction_date));
        $output['customer_name'] =  $customer->is_default ? $transaction->customer_name : ($customer->business_name ?? $customer->name);
        $output['customer_landmark'] = $customer->address;
        $output['customer_location'] =
            $customer->address ?
                $customer->address . ", " . implode(', ', array_filter([$customer->city, $customer->state, $customer->country])) :
                    implode(', ', array_filter([$customer->city, $customer->state, $customer->country]));
        $output['customer_city'] = $customer->city;
        $output['customer_state'] = $customer->state;
        $output['customer_tax_number'] = $customer->reg_number;
        $output['customer_nit'] =
            $customer->is_default ? $transaction->customer_dui :
                ($customer->dni ? $customer->dni : $customer->tax_number );
        $output['customer_business_activity'] = $customer->business_line;
        $output['customer_employee_name'] = $this->contactUtil->getCustomerEmployeeName($customer->id);
        $output['customer_dui'] = $transaction->customer_dui ?? '';
        
        // first name and lastname for ticket
        $name_m = explode (" ", $output['customer_name']);
        $count = count($name_m);
        $output['customer_short_name'] = $count == 1 ? $name_m[0] : ($name_m[0] . " ". $name_m[1] ?? "");
        
        //Invoice product lines
        $is_lot_number_enabled = request()->session()->get('business.enable_lot_number');
        $is_product_expiry_enabled = request()->session()->get('business.enable_product_expiry');

        /** Receipt lines */
        $output['lines'] = [];
        if ($transaction_type == 'sell') {
            $sell_line_relations = ['modifiers'];

            if ($is_lot_number_enabled == 1) {
                $sell_line_relations[] = 'lot_details';
            }

            $lines = $transaction->sell_lines()->whereNull('parent_sell_line_id')->with($sell_line_relations)->get();

            $details = $this->_receiptDetailsSellLines($lines, $il, $is_product_expiry_enabled, $is_lot_number_enabled);
            
            $output['lines'] = $details['lines'];
        }  elseif ($transaction_type == 'sell_return') {
            $parent_sell = Transaction::find($transaction->return_parent_id);
            $lines = $parent_sell->sell_lines;

            $details = $this->_receiptDetailsSellReturnLines($lines, $il, $is_product_expiry_enabled, $is_lot_number_enabled);
            $output['lines'] = $details['lines'];

            $output['taxes'] = [];
            foreach ($details['lines'] as $line) {
                if (!empty($line['group_tax_details'])) {
                    foreach ($line['group_tax_details'] as $tax_group_detail) {
                        if (!isset($output['taxes'][$tax_group_detail['name']])) {
                            $output['taxes'][$tax_group_detail['name']] = 0;
                        }
                        $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                    }
                }
            }
        }

        // Correlative
        $output['correlative'] = $transaction->correlative;
        // Transaction id
        $output['transaction_id'] = $transaction->id;
        //Shipping charges & details
        $output['shipping_charges'] = ($transaction->shipping_charges != 0) ? $transaction->shipping_charges : 0;
        $output['shipping_details'] = $transaction->shipping_details;

        $document_type = DocumentType::find($transaction->document_types_id);
        //Total
        $output['total_before_tax'] = $transaction->total_before_tax;
        $output['discount_amount'] = $transaction->discount_amount > 0 ?
            $this->getDiscountValue($transaction->total_before_tax, $transaction->discount_type, $transaction->discount_amount) : 0;
        $output['total'] = $transaction->final_total;

        $output['invoice_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', $transaction->transaction_date)->format('M d, Y H:i');
        $output['payment_condition'] = $transaction->payment_condition;

        // Taxes
        $output['tax_amount'] = $this->taxUtil->getTaxAmount($transaction->id);
        
        $tax_type = $this->taxUtil->getTaxType($transaction->id);
        /** EXEMPT - WITHHELD - PERCEPTION */
        $output['exempt'] = $tax_type == 0 ? $transaction->final_total : "";
        $output['withheld'] = $tax_type == -1 ? $transaction->tax_amount : "";
        $output['perception'] = $tax_type == 1 ? $transaction->tax_amount : "";

        $output['delivered_by'] = $transaction->delivered_by;
        $output['delivered_by_dui'] = $transaction->delivered_by_dui;
        $output['delivered_by_passport'] = $transaction->delivered_by_passport;
        $output['received_by'] = $transaction->received_by;
        $output['received_by_dui'] = $transaction->received_by_dui;

        //Transaction payments
        $tp = $this->sumTransactionPayments($transaction->id);
        $output['payments']['cash'] = $tp->total_cash;
        $output['payments']['card'] = $tp->total_card;
        $output['payments']['check'] = $tp->total_check;
        $output['payments']['bank'] = $tp->total_bank;
        $output['payments']['cash_return'] = $tp->total_cash_return;
        $output['payments']['total_cash'] = $tp->total_cash_return + $tp->total_cash;
        $output['payments']['total'] = $tp->total_cash + $tp->total_card + $tp->total_check + $tp->total_bank;

        $output['is_exempt'] = $customer->is_exempt;
        
        return (object)$output;
    }

    /**
     * Return amounts letters
     * @param float
     */
    public function getAmountLetters($amount){
        function unidad($numuero){
            switch ($numuero)
            {
                case 9: { $numu = "NUEVE"; break; }
                case 8: { $numu = "OCHO"; break; }
                case 7: { $numu = "SIETE"; break; }
                case 6: { $numu = "SEIS"; break; }
                case 5: { $numu = "CINCO"; break; }
                case 4: { $numu = "CUATRO"; break; }
                case 3: { $numu = "TRES"; break; }
                case 2: { $numu = "DOS"; break; }
                case 1: { $numu = "UNO"; break; }
                case 0: { $numu = ""; break; }
            }

            return $numu;
        }
        
        function decena($numdero){
            if ($numdero >= 90 && $numdero <= 99) {
                $numd = "NOVENTA ";
                if ($numdero > 90) $numd = $numd."Y ".(unidad($numdero - 90));
            }
            else if ($numdero >= 80 && $numdero <= 89) {
                $numd = "OCHENTA ";
                if ($numdero > 80) $numd = $numd."Y ".(unidad($numdero - 80));
            }
            else if ($numdero >= 70 && $numdero <= 79) {
                $numd = "SETENTA ";
                if ($numdero > 70)
                    $numd = $numd."Y ".(unidad($numdero - 70));
            }
            else if ($numdero >= 60 && $numdero <= 69) {
                $numd = "SESENTA ";
                if ($numdero > 60) $numd = $numd."Y ".(unidad($numdero - 60));
            }
            else if ($numdero >= 50 && $numdero <= 59) {
                $numd = "CINCUENTA ";
                if ($numdero > 50) $numd = $numd."Y ".(unidad($numdero - 50));
            }
            else if ($numdero >= 40 && $numdero <= 49) {
                $numd = "CUARENTA ";
                if ($numdero > 40) $numd = $numd."Y ".(unidad($numdero - 40));
            }
            else if ($numdero >= 30 && $numdero <= 39) {
                $numd = "TREINTA ";
                if ($numdero > 30) $numd = $numd."Y ".(unidad($numdero - 30));
            }
            else if ($numdero >= 20 && $numdero <= 29) {
                if ($numdero == 20) $numd = "VEINTE ";
                else $numd = "VEINTI".(unidad($numdero - 20));
            }
            else if ($numdero >= 10 && $numdero <= 19) {
                switch ($numdero){
                    case 10: { $numd = "DIEZ "; break; }
                    case 11: { $numd = "ONCE "; break; }
                    case 12: { $numd = "DOCE "; break; }
                    case 13: { $numd = "TRECE "; break; }
                    case 14: { $numd = "CATORCE "; break; }
                    case 15: { $numd = "QUINCE "; break; }
                    case 16: { $numd = "DIECISEIS "; break; }
                    case 17: { $numd = "DIECISIETE "; break; }
                    case 18: { $numd = "DIECIOCHO "; break; }
                    case 19: { $numd = "DIECINUEVE "; break; }
                }
            }
            else
                $numd = unidad($numdero);
            return $numd;
        }
        
        function centena($numc){
            if ($numc >= 100) {
                if ($numc >= 900 && $numc <= 999) {
                    $numce = "NOVECIENTOS ";
                    if ($numc > 900) $numce = $numce.(decena($numc - 900));
                }
                else if ($numc >= 800 && $numc <= 899) {
                    $numce = "OCHOCIENTOS ";
                    if ($numc > 800) $numce = $numce.(decena($numc - 800));
                }
                else if ($numc >= 700 && $numc <= 799) {
                    $numce = "SETECIENTOS ";
                    if ($numc > 700) $numce = $numce.(decena($numc - 700));
                }
                else if ($numc >= 600 && $numc <= 699) {
                    $numce = "SEISCIENTOS ";
                    if ($numc > 600) $numce = $numce.(decena($numc - 600));
                }
                else if ($numc >= 500 && $numc <= 599) {
                    $numce = "QUINIENTOS ";
                    if ($numc > 500) $numce = $numce.(decena($numc - 500));
                }
                else if ($numc >= 400 && $numc <= 499) {
                    $numce = "CUATROCIENTOS ";
                    if ($numc > 400) $numce = $numce.(decena($numc - 400));
                }
                else if ($numc >= 300 && $numc <= 399) {
                    $numce = "TRESCIENTOS ";
                    if ($numc > 300) $numce = $numce.(decena($numc - 300));
                }
                else if ($numc >= 200 && $numc <= 299) {
                    $numce = "DOSCIENTOS ";
                    if ($numc > 200) $numce = $numce.(decena($numc - 200));
                }
                else if ($numc >= 100 && $numc <= 199) {
                    if ($numc == 100) $numce = "CIEN ";
                    else $numce = "CIENTO ".(decena($numc - 100));
                }
            }
            else $numce = decena($numc);
        
            return $numce;
        }
        
        function miles($nummero){
            if ($nummero >= 1000 && $nummero < 2000){
                $numm = "MIL ".(centena($nummero%1000));
            }
            if ($nummero >= 2000 && $nummero <10000){
                $numm = unidad(Floor($nummero/1000))." MIL ".(centena($nummero%1000));
            }
            if ($nummero < 1000) $numm = centena($nummero);
        
            return $numm;
        }
        
        function decmiles($numdmero){
            if ($numdmero == 10000)
                $numde = "DIEZ MIL";
            if ($numdmero > 10000 && $numdmero <20000){
                $numde = decena(Floor($numdmero/1000))."MIL ".(centena($numdmero%1000));
            }
            if ($numdmero >= 20000 && $numdmero <100000){
                $numde = decena(Floor($numdmero/1000))." MIL ".(miles($numdmero%1000));
            }
            
            if ($numdmero < 10000) $numde = miles($numdmero);
        
            return $numde;
        }
        function cienmiles($numcmero){
            if ($numcmero == 100000)
                $num_letracm = "CIEN MIL";
            if ($numcmero >= 100000 && $numcmero <1000000){
                $num_letracm = centena(Floor($numcmero/1000))." MIL ".(centena($numcmero%1000));
            }
            
            if ($numcmero < 100000) $num_letracm = decmiles($numcmero);
            
            return $num_letracm;
        }
        
        function millon($nummiero){
            if ($nummiero >= 1000000 && $nummiero <2000000) {
                $num_letramm = "UN MILLON ".(cienmiles($nummiero%1000000));
            }

            if ($nummiero >= 2000000 && $nummiero <10000000) {
                $num_letramm = unidad(Floor($nummiero/1000000))." MILLONES ".(cienmiles($nummiero%1000000));
            }
            
            if ($nummiero < 1000000) $num_letramm = cienmiles($nummiero);
        
            return $num_letramm;
        }
        
        function decmillon($numerodm){
            if ($numerodm == 10000000) $num_letradmm = "DIEZ MILLONES";

            if ($numerodm > 10000000 && $numerodm <20000000){
                $num_letradmm = decena(Floor($numerodm/1000000))."MILLONES ".(cienmiles($numerodm%1000000));
            }
            if ($numerodm >= 20000000 && $numerodm <100000000){
                $num_letradmm = decena(Floor($numerodm/1000000))." MILLONES ".(millon($numerodm%1000000));
            }
            
            if ($numerodm < 10000000) $num_letradmm = millon($numerodm);
        
            return $num_letradmm;
        }
        
        function cienmillon($numcmeros){
            if ($numcmeros == 100000000) $num_letracms = "CIEN MILLONES";

            if ($numcmeros >= 100000000 && $numcmeros <1000000000){
                $num_letracms = centena(Floor($numcmeros/1000000))." MILLONES ".(millon($numcmeros%1000000));
            }
            
            if ($numcmeros < 100000000) $num_letracms = decmillon($numcmeros);

            return $num_letracms;
        }
        
        function milmillon($nummierod){
            if ($nummierod >= 1000000000 && $nummierod <2000000000){
                $num_letrammd = "MIL ".(cienmillon($nummierod%1000000000));
            }
            
            if ($nummierod >= 2000000000 && $nummierod <10000000000){
                $num_letrammd = unidad(Floor($nummierod/1000000000))." MIL ".(cienmillon($nummierod%1000000000));
            }
            
            if ($nummierod < 1000000000) $num_letrammd = cienmillon($nummierod);
        
            return $num_letrammd;
        }

        $num = str_replace(",", "", $amount);
        $num = number_format($amount, 2, '.', '');
        $cents = substr($num, strlen($num)-2, strlen($num)-1);
        $num = (int)$num;
        $numf = milmillon($num);
        
        return $numf." ".$cents."/100 dólares";
    }

    /**
     * Gives the receipt details in proper format.
     *
     * @param int $transaction_id
     * @param int $location_id
     * @param object $invoice_layout
     * @param object $business_details
     * @param array $receipt_details
     * @param string $receipt_printer_type
     *
     * @return array
     */
    public function getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type)
    {
        $il = $invoice_layout;

        $transaction = Transaction::find($transaction_id);
        $transaction_type = $transaction->type;

        $output = [
            'header_text' => isset($il->header_text) ? $il->header_text : '',
            'business_name' => ($il->show_business_name == 1) ? $business_details->name : '',
            'location_name' => ($il->show_location_name == 1) ? $location_details->name : '',
            'sub_heading_line1' => trim($il->sub_heading_line1),
            'sub_heading_line2' => trim($il->sub_heading_line2),
            'sub_heading_line3' => trim($il->sub_heading_line3),
            'sub_heading_line4' => trim($il->sub_heading_line4),
            'sub_heading_line5' => trim($il->sub_heading_line5),
            'table_product_label' => $il->table_product_label,
            'table_qty_label' => $il->table_qty_label,
            'table_unit_price_label' => $il->table_unit_price_label,
            'table_subtotal_label' => $il->table_subtotal_label
        ];

        // Commission agent or seller
        $commission_agent = User::find($transaction->commission_agent);
        $output['commission_agent'] = ! empty($commission_agent) ? $commission_agent->first_name . ' ' . $commission_agent->last_name : '';

        // Business details
        $output['business_nrc'] = $business_details->nrc;
        $output['business_line'] = $business_details->line_of_business;
        $output['business_nit'] = $business_details->nit;
        $output['location_name'] = $location_details->name;
        $output['location_landmark'] = $location_details->landmark;
        $output['location_mobile'] = $location_details->mobile;

        $employee = Quote::join('employees', 'employees.id', 'quotes.employee_id')
            ->where('quotes.transaction_id', $transaction_id)
            ->select("employees.first_name", "employees.last_name")
            ->first();
                
        $employee_first_name = "";
        $employee_last_name = "";

        if (! empty($employee)) {
            $efn = explode(" ", $employee->first_name);
            $employee_first_name = $efn[0] ?? " ";
            $eln = explode(" ", $employee->last_name);
            $employee_last_name = $eln[0] ?? " ";
        }

        $cashier_code = Cashier::where('id', $transaction->cashier_id)->value('code');

        $output['seller_name'] = $employee_first_name . ' '. $employee_last_name;
        $output['cashier_code'] = $cashier_code;
        $output['transaction_date'] = date('d/m/Y', strtotime($transaction->transaction_date));
        $output['transaction_hour'] = date('h:m:i', strtotime($transaction->transaction_date));

        // Documents details
        $doc = DocumentCorrelative::where('document_type_id', $transaction->document_types_id)->first();

        $res = ! empty($doc) ? $doc->resolution : '';
        $serie = ! empty($doc) ? $doc->serie : '';
        $initial_c = ! empty($doc) ? $doc->initial : '';
        $final_c = ! empty($doc) ? $doc->final : '';

        $output['document']['res_ticket'] = $res;
        $output['document']['serie'] = $serie;
        $output['document']['serie_correlative'] = $serie .'|' . $initial_c .' AL ' . $serie . '|'. $final_c;
        $output['document']['serie_correlative_2'] = 'DEL ' . $doc->serie . '-' . $doc->initial . ' AL ' . $doc->serie . '-' . $doc->final;

        // Customer info
        $customer = Customer::leftJoin("countries as cnt", "customers.country_id", "cnt.id")
            ->leftJoin("states as st", "customers.state_id", "st.id")
            ->leftJoin("cities as ct", "customers.city_id", "ct.id")
            ->where("customers.id", $transaction->customer_id)
            ->select(
                "cnt.name as country", "st.name as state", "ct.name as city",
                "customers.*"
            )
            ->first();

        $output['customer_name'] =  $customer->is_default ? $transaction->customer_name : ($customer->business_name ?? $customer->name);
        $output['customer_dui'] = $transaction->customer_dui ?? '';

        // First name and lastname for ticket
        $name_m = explode (" ", $output['customer_name']);
        $count = count($name_m);
        $output['customer_short_name'] = $count == 1 ? $name_m[0] : ($name_m[0] . " ". $name_m[1] ?? "");

        // Correlative
        $output['correlative'] = $transaction->correlative;

        // Total
        $output['total_before_tax'] = $transaction->total_before_tax;
        $output['total'] = $transaction->final_total;

        //Transaction payments
        $tp = $this->sumTransactionPayments($transaction->id);
        $output['payments']['cash'] = $tp->total_cash;
        $output['payments']['card'] = $tp->total_card;
        $output['payments']['check'] = $tp->total_check;
        $output['payments']['bank'] = $tp->total_bank;
        $output['payments']['cash_return'] = $tp->total_cash_return;
        $output['payments']['total_cash'] = $tp->total_cash_return + $tp->total_cash;
        $output['payments']['total'] = $tp->total_cash + $tp->total_card + $tp->total_check + $tp->total_bank;

        //Display name
        $output['display_name'] = $output['business_name'];
        if (!empty($output['location_name'])) {
            if (!empty($output['display_name'])) {
                $output['display_name'] .= ', ';
            }
            $output['display_name'] .= $output['location_name'];
        }

        //Logo
        $output['logo'] = $il->show_logo != 0 && !empty($il->logo) && file_exists(public_path('uploads/invoice_logos/' . $il->logo)) ? asset('uploads/invoice_logos/' . $il->logo) : false;

        //Add Email
        if(!empty($location_details->email))
        {
            $output['email'] = $location_details->email;    
        }   
        else
        {
             $output['email'] = ' ';
        }
        
        //Address
        $output['address'] = '';
        $temp = [];
        if ($il->show_landmark == 1) {
            $output['address'] .= $location_details->landmark . "\n";
        }
        if ($il->show_city == 1 &&  !empty($location_details->city)) {
            $temp[] = $location_details->city;
        }
        if ($il->show_state == 1 &&  !empty($location_details->state)) {
            $temp[] = $location_details->state;
        }
        if ($il->show_zip_code == 1 &&  !empty($location_details->zip_code)) {
            $temp[] = $location_details->zip_code;
        }
        if ($il->show_country == 1 &&  !empty($location_details->country)) {
            $temp[] = $location_details->country;
        }
        if (!empty($temp)) {
            $output['address'] .= implode(',', $temp);
        }

        $output['website'] = $location_details->website;
        $output['location_custom_fields'] = '';
        $temp = [];
        if (!empty($location_details->custom_field1)) {
            $temp[] = $location_details->custom_field1;
        }
        if (!empty($location_details->custom_field2)) {
            $temp[] = $location_details->custom_field2;
        }
        if (!empty($location_details->custom_field3)) {
            $temp[] = $location_details->custom_field3;
        }
        if (!empty($location_details->custom_field4)) {
            $temp[] = $location_details->custom_field4;
        }
        if (!empty($temp)) {
            $output['location_custom_fields'] .= implode(', ', $temp);
        }

        //Tax Info
        if ($il->show_tax_1 == 1 && !empty($business_details->tax_number_1)) {
            $output['tax_label1'] = !empty($business_details->tax_label_1) ? $business_details->tax_label_1 . ': ' : '';

            $output['tax_info1'] = $business_details->tax_number_1;
        }
        if ($il->show_tax_2 == 1 && !empty($business_details->tax_number_2)) {
            if (!empty($output['tax_info1'])) {
                $output['tax_info1'] .= ', ';
            }

            $output['tax_label2'] = !empty($business_details->tax_label_2) ? $business_details->tax_label_2 . ': ' : '';

            $output['tax_info2'] = $business_details->tax_number_2;
        }

        //Shop Contact Info
        $output['contact'] = '';
        if ($il->show_mobile_number == 1 && !empty($location_details->mobile)) {
            $output['contact'] .= 'Mobile: ' . $location_details->mobile;
        }
        if ($il->show_alternate_number == 1 && !empty($location_details->alternate_number)) {
            if (empty($output['contact'])) {
                $output['contact'] .= 'Mobile: ' . $location_details->alternate_number;
            } else {
                $output['contact'] .= ', ' . $location_details->alternate_number;
            }
        }
        if ($il->show_email == 1 && !empty($location_details->email)) {
            if (!empty($output['contact'])) {
                $output['contact'] .= "\n";
            }
            $output['contact'] .= 'Email: ' . $location_details->email;
        }

        //Customer show_customer
        //$customer = Contact::find($transaction->contact_id);
        $customer = Customer::leftJoin("countries as cnt", "customers.country_id", "cnt.id")
            ->leftJoin("states as st", "customers.state_id", "st.id")
            ->leftJoin("cities as ct", "customers.city_id", "ct.id")
            ->where("customers.id", $transaction->customer_id)
            ->select(
                "customers.name", "customers.telphone", "customers.address",
                "cnt.name as country", "st.name as state", "ct.name as city"
                )->first();

        $output['customer_info'] = '';
        $output['customer_tax_number'] = '';
        $output['customer_tax_label'] = '';
        $output['customer_custom_fields'] = '';
        if ($il->show_customer == 1) {

                // new custum field
                $output['custumer_name'] = !empty($customer->name) ? $customer->name: '';
                $output['custumer_phone'] = !empty($customer->telphone) ? $customer->telphone: '-';

                $output['custumer_landmark'] = $customer->address;
                $output['custumer_landmark'] .= ', ' .implode(',', array_filter([$customer->city, $customer->state, $customer->country]));

                //

            $output['customer_label'] = !empty($il->customer_label) ? $il->customer_label : '';
            
            $output['customer_info'] .= !empty($customer->name) ? $customer->name: '';
            if (!empty($output['customer_info']) && $receipt_printer_type != 'printer') {
                $output['customer_info'] .= ', ' . $customer->landmark;
                $output['customer_info'] .= ', ' . implode(',', array_filter([$customer->city, $customer->state, $customer->country]));
                $output['customer_info'] .= ', ' . $customer->telphone;
            }

            $output['customer_tax_number'] = $customer->reg_number;
            $output['customer_tax_label'] = !empty($il->client_tax_label) ? $il->client_tax_label : '';

            $temp = [];
            if (!empty($customer->custom_field1)) {
                $temp[] = $customer->custom_field1;
            }
            if (!empty($customer->custom_field2)) {
                $temp[] = $customer->custom_field2;
            }
            if (!empty($customer->custom_field3)) {
                $temp[] = $customer->custom_field3;
            }
            if (!empty($customer->custom_field4)) {
                $temp[] = $customer->custom_field4;
            }
            if (!empty($temp)) {
                $output['customer_custom_fields'] .= implode(',', $temp);
            }
        }

        $output['client_id'] = '';
        $output['client_id_label'] = '';
        if ($il->show_client_id == 1) {
            $output['client_id_label'] = !empty($il->client_id_label) ? $il->client_id_label : '';
            $output['client_id'] = !empty($customer->contact_id) ? $customer->contact_id : '';
        }

        //Sales person info
        $output['sales_person'] = '';
        $output['sales_person_label'] = '';
        if ($il->show_sales_person == 1) {
            $output['sales_person_label'] = !empty($il->sales_person_label) ? $il->sales_person_label : '';
            $output['sales_person'] = !empty($transaction->sales_person->user_full_name) ? $transaction->sales_person->user_full_name : '';
        }

        //Invoice info
        $output['invoice_no'] = $transaction->invoice_no;

        //Heading & invoice label, when quotation use the quotation heading.
        if ($transaction_type == 'sell_return') {
            $output['invoice_heading'] = $il->cn_heading;
            $output['invoice_no_prefix'] = $il->cn_no_label;
        } elseif ($transaction->status == 'draft' && $transaction->is_quotation == 1) {
            $output['invoice_heading'] = $il->quotation_heading;
            $output['invoice_no_prefix'] = $il->quotation_no_prefix;
        } else {
            $output['invoice_no_prefix'] = $il->invoice_no_prefix;
            $output['invoice_heading'] = $il->invoice_heading;
            if ($transaction->payment_status == 'paid' && !empty($il->invoice_heading_paid)) {
                $output['invoice_heading'] .= ' ' . $il->invoice_heading_paid;
            } elseif (in_array($transaction->payment_status, ['due', 'partial']) && !empty($il->invoice_heading_not_paid)) {
                $output['invoice_heading'] .= ' ' . $il->invoice_heading_not_paid;
            }
        }

        $output['date_label'] = $il->date_label;
        if ($il->show_time == 1) {
             $output['invoice_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', $transaction->transaction_date)->format('M d, Y H:i');
        } else {
            $output['invoice_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', $transaction->transaction_date)->toFormattedDateString();
        }
        
        $show_currency = true;
        if ($receipt_printer_type == 'printer' && trim(session('currency')['symbol']) != '$') {
            $show_currency = false;
        }

        //Invoice product lines
        $is_lot_number_enabled = request()->session()->get('business.enable_lot_number');
        $is_product_expiry_enabled = request()->session()->get('business.enable_product_expiry');

        $output['lines'] = [];
        if ($transaction_type == 'sell') {
            $sell_line_relations = ['modifiers'];

            if ($is_lot_number_enabled == 1) {
                $sell_line_relations[] = 'lot_details';
            }

            $lines = $transaction->sell_lines()->whereNull('parent_sell_line_id')->with($sell_line_relations)->get();

            $details = $this->_receiptDetailsSellLines($lines, $il, $is_product_expiry_enabled, $is_lot_number_enabled);
            
            $output['lines'] = $details['lines'];
            $output['taxes'] = [];
            foreach ($details['lines'] as $line) {
                if (!empty($line['group_tax_details'])) {
                    foreach ($line['group_tax_details'] as $tax_group_detail) {
                        if (!isset($output['taxes'][$tax_group_detail['name']])) {
                            $output['taxes'][$tax_group_detail['name']] = 0;
                        }
                        $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                    }
                }
            }
        } elseif ($transaction_type == 'sell_return') {
            $parent_sell = Transaction::find($transaction->return_parent_id);
            $lines = $parent_sell->sell_lines;

            $details = $this->_receiptDetailsSellReturnLines($lines, $il, $is_product_expiry_enabled, $is_lot_number_enabled);
            $output['lines'] = $details['lines'];

            $output['taxes'] = [];
            foreach ($details['lines'] as $line) {
                if (!empty($line['group_tax_details'])) {
                    foreach ($line['group_tax_details'] as $tax_group_detail) {
                        if (!isset($output['taxes'][$tax_group_detail['name']])) {
                            $output['taxes'][$tax_group_detail['name']] = 0;
                        }
                        $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                    }
                }
            }
        }

        //show cat code
        $output['show_cat_code'] = $il->show_cat_code;
        $output['cat_code_label'] = $il->cat_code_label;

        //Subtotal
        $output['subtotal_label'] = $il->sub_total_label . ':';
        $output['subtotal'] = ($transaction->total_before_tax != 0) ? $this->num_f($transaction->total_before_tax, $show_currency) : 0;
        $output['subtotal_unformatted'] = ($transaction->total_before_tax != 0) ? $transaction->total_before_tax : 0;

        //Discount
        $output['line_discount_label'] = $invoice_layout->discount_label;
        $output['discount_label'] = $invoice_layout->discount_label;
        $output['discount_label'] .= ($transaction->discount_type == 'percentage') ? ' (' . $transaction->discount_amount . '%) :' : '';

        if ($transaction->discount_type == 'percentage') {
            $discount = ($transaction->discount_amount/100) * $transaction->total_before_tax;
        } else {
            $discount = $transaction->discount_amount;
        }
        $output['discount'] = ($discount != 0) ? $this->num_f($discount, $show_currency) : 0;

        //Format tax
        if (!empty($output['taxes'])) {
            foreach ($output['taxes'] as $key => $value) {
                $output['taxes'][$key] = $this->num_f($value, $show_currency);
            }
        }

        //Order Tax
        $tax = $transaction->tax;
        $output['tax_label'] = $invoice_layout->tax_label;
        $output['line_tax_label'] = $invoice_layout->tax_label;
        if (!empty($tax) && !empty($tax->name)) {
            $output['tax_label'] .= ' (' . $tax->name . ')';
        }
        $output['tax_label'] .= ':';
        $output['tax'] = ($transaction->tax_amount != 0) ? $this->num_f($transaction->tax_amount, $show_currency) : 0;

        if ($transaction->tax_amount != 0 && $tax->is_tax_group) {
            $transaction_group_tax_details = $this->groupTaxDetails($tax, $transaction->tax_amount);

            $output['group_tax_details'] = [];
            foreach ($transaction_group_tax_details as $value) {
                $output['group_tax_details'][$value['name']] = $this->num_f($value['calculated_tax'], $show_currency);
            }
        }

        //Shipping charges
        $output['shipping_charges'] = ($transaction->shipping_charges != 0) ? $this->num_f($transaction->shipping_charges, $show_currency) : 0;
        $output['shipping_charges_label'] = trans("sale.shipping_charges");
        //Shipping details
        $output['shipping_details'] = $transaction->shipping_details;
        $output['shipping_details_label'] = trans("sale.shipping_details");

        //Total
        if ($transaction_type == 'sell_return') {
            $output['total_label'] = $invoice_layout->cn_amount_label . ':';
            $output['total'] = $this->num_f($transaction->final_total, $show_currency);
        } else {
            $output['total_label'] = $invoice_layout->total_label . ':';
            $output['total'] = $this->num_f($transaction->final_total, $show_currency);
        }
        
        //Paid & Amount due, only if final
        if ($transaction_type == 'sell' && $transaction->status == 'final') {
            $paid_amount = $this->getTotalPaid($transaction->id);
            $due = $transaction->final_total - $paid_amount;

            $output['total_paid'] = ($paid_amount == 0) ? 0 : $this->num_f($paid_amount, $show_currency);
            $output['total_paid_label'] = $il->paid_label;
            $output['total_due'] = ($due == 0) ? 0 : $this->num_f($due, $show_currency);
            $output['total_due_label'] = $il->total_due_label;

            //Get payment details
            $output['payments'] = [];
            if ($il->show_payments == 1) {
                $payments = $transaction->payment_lines->toArray();
                if (!empty($payments)) {
                    foreach ($payments as $value) {
                        if ($value['method'] == 'cash') {
                            $output['payments'][] =
                                ['method' => trans("lang_v1.cash") . ($value['is_return'] == 1 ? ' (' . trans("lang_v1.change_return") . ')(-)' : ''),
                                'amount' => $this->num_f($value['amount'], true),
                                'date' => $this->format_date($value['paid_on'])
                                ];
                            if ($value['is_return'] == 1) {
                            }
                        } elseif ($value['method'] == 'card') {
                            $output['payments'][] =
                                ['method' => trans("lang_v1.card") . (!empty($value['card_transaction_number']) ? (', Transaction Number:' . $value['card_transaction_number']) : ''),
                                'amount' => $this->num_f($value['amount'], true),
                                'date' => $this->format_date($value['paid_on'])
                                ];
                        } elseif ($value['method'] == 'cheque') {
                            $output['payments'][] =
                                ['method' => trans("lang_v1.cheque") . (!empty($value['cheque_number']) ? (', Cheque Number:' . $value['cheque_number']) : ''),
                                'amount' => $this->num_f($value['amount'], true),
                                'date' => $this->format_date($value['paid_on'])
                                ];
                        } elseif ($value['method'] == 'bank_transfer') {
                            $output['payments'][] =
                                ['method' => trans("lang_v1.bank_transfer") . (!empty($value['bank_account_number']) ? (', Account Number:' . $value['bank_account_number']) : ''),
                                'amount' => $this->num_f($value['amount'], true),
                                'date' => $this->format_date($value['paid_on'])
                                ];
                        } elseif ($value['method'] == 'other') {
                            $output['payments'][] =
                                ['method' => trans("lang_v1.other"),
                                'amount' => $this->num_f($value['amount'], true),
                                'date' => $this->format_date($value['paid_on'])
                                ];
                        } elseif ($value['method'] == 'custom_pay_1') {
                            $output['payments'][] =
                                ['method' => trans("lang_v1.custom_payment_1") . (!empty($value['transaction_no']) ? (', ' . trans("lang_v1.transaction_no") . ':' . $value['transaction_no']) : ''),
                                'amount' => $this->num_f($value['amount'], true),
                                'date' => $this->format_date($value['paid_on'])
                                ];
                        } elseif ($value['method'] == 'custom_pay_2') {
                            $output['payments'][] =
                                ['method' => trans("lang_v1.custom_payment_2") . (!empty($value['transaction_no']) ? (', ' . trans("lang_v1.transaction_no") . ':' . $value['transaction_no']) : ''),
                                'amount' => $this->num_f($value['amount'], true),
                                'date' => $this->format_date($value['paid_on'])
                                ];
                        } elseif ($value['method'] == 'custom_pay_3') {
                            $output['payments'][] =
                                ['method' => trans("lang_v1.custom_payment_3") . (!empty($value['transaction_no']) ? (', ' . trans("lang_v1.transaction_no") . ':' . $value['transaction_no']) : ''),
                                'amount' => $this->num_f($value['amount'], true),
                                'date' => $this->format_date($value['paid_on'])
                                ];
                        }
                    }
                }
            }
        }

        //Check for barcode
        $output['barcode'] = ($il->show_barcode == 1) ? $transaction->invoice_no : false;

        //Additional notes
        $output['additional_notes'] = $transaction->additional_notes;
        $output['footer_text'] = $invoice_layout->footer_text;
        
        //Barcode related information.
        $output['show_barcode'] = !empty($il->show_barcode) ? true : false;

        //Module related information.
        $il->module_info = !empty($il->module_info) ? json_decode($il->module_info, true) : [];
        if (!empty($il->module_info['tables']) && $this->isModuleEnabled('tables')) {
            //Table label & info
            $output['table_label'] = null;
            $output['table'] = null;
            if (isset($il->module_info['tables']['show_table'])) {
                $output['table_label'] = !empty($il->module_info['tables']['table_label']) ? $il->module_info['tables']['table_label'] : '';
                if (!empty($transaction->res_table_id)) {
                    $table = ResTable::find($transaction->res_table_id);
                }
                
                //res_table_id
                $output['table'] = !empty($table->name) ? $table->name : '';
            }
        }

        if (!empty($il->module_info['service_staff']) && $this->isModuleEnabled('service_staff')) {
            //Waiter label & info
            $output['service_staff_label'] = null;
            $output['service_staff'] = null;
            if (isset($il->module_info['service_staff']['show_service_staff'])) {
                $output['service_staff_label'] = !empty($il->module_info['service_staff']['service_staff_label']) ? $il->module_info['service_staff']['service_staff_label'] : '';
                if (!empty($transaction->res_waiter_id)) {
                    $waiter = \App\User::find($transaction->res_waiter_id);
                }
                
                //res_table_id
                $output['service_staff'] = !empty($waiter->id) ? implode(' ', [$waiter->first_name, $waiter->last_name]) : '';
            }
        }

        $output['design'] = $il->design;
        $output['table_tax_headings'] = !empty($il->table_tax_headings) ? array_filter(json_decode($il->table_tax_headings), 'strlen') : null;

        // Months
        $months = array(
            '1' => ucfirst(strtolower(__('accounting.january'))),
            '2' => ucfirst(strtolower(__('accounting.february'))),
            '3' => ucfirst(strtolower(__('accounting.march'))),
            '4' => ucfirst(strtolower(__('accounting.april'))),
            '5' => ucfirst(strtolower(__('accounting.may'))),
            '6' => ucfirst(strtolower(__('accounting.june'))),
            '7' => ucfirst(strtolower(__('accounting.july'))),
            '8' => ucfirst(strtolower(__('accounting.august'))),
            '9' => ucfirst(strtolower(__('accounting.september'))),
            '10' => ucfirst(strtolower(__('accounting.october'))),
            '11' => ucfirst(strtolower(__('accounting.november'))),
            '12' => ucfirst(strtolower(__('accounting.december')))
        );

        $output['months'] = $months;
        
        return (object)$output;
    }

    /**
     * Returns each line details for sell invoice display
     *
     * @return array
     */
    protected function _receiptDetailsSellLines($lines, $il, $is_product_expiry_enabled, $is_lot_number_enabled)
    {
        // Number of decimals
        $business_id = request()->session()->get('user.business_id');$business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $precision = $product_settings['decimals_in_fiscal_documents'];

        $output_lines = [];
        //$output_taxes = ['taxes' => []];
        foreach ($lines as $line) {
            $product = $line->product;
            $variation = $line->variations;
            $unit = $line->product->unit;
            $brand = $line->product->brand;
            $cat = $line->product->category;
            $tax_details = TaxRate::find($line->tax_id);

            $line_array = [
                'code' => $product->sku,
                //Field for 1st column
                'name' => $product->name,
                'sub_sku' => !empty($variation->sub_sku) ? $variation->sub_sku : '',
                'variation' => (empty($variation->name) || $variation->name == 'DUMMY') ? '' : $variation->name,
                //Field for 2nd column
                'quantity_uf' => $line->quantity,
                'quantity' => $this->num_f($line->quantity, false, $this->quantity_precision),
                'units' => !empty($unit->short_name) ? $unit->short_name : '',

                'sell_line_note' => !empty($line->sell_line_note) ? $line->sell_line_note : '',

                'unit_price_exc' => $this->num_f($line->unit_price_before_discount, false, $precision),
                'unit_price' => $this->num_f($line->unit_price, false, $precision),
                'tax_amount' => $this->num_f($line->tax_amount, false, $precision),
                'tax_unformatted' => $line->item_tax,
                'tax_name' => !empty($tax_details) ? $tax_details->name : null,
                'tax_percent' => !empty($tax_details) ? $tax_details->amount : null,

                //Field for 3rd column
                'unit_price_inc_tax' => $this->num_f($line->unit_price_inc_tax, false, $precision),
                'unit_price_exc_tax' => $this->num_f($line->unit_price_exc_tax, false, $precision),
                'unit_price_before_discount' => $this->num_f($line->unit_price_before_discount, false, $precision),
                'line_total_exc_tax' => $this->num_f($line->quantity * $line->unit_price_before_discount, false, $precision),

                //Fields for 4th column
                'line_total' => $this->num_f($line->unit_price * $line->quantity, false, $precision),

                'id' => $variation->id,

                'service_parent_id' => $line->service_parent_id
            ];

            //Group product taxes by name.
            if (!empty($tax_details)) {
                if ($tax_details->is_tax_group) {
                    $group_tax_details = $this->groupTaxDetails($tax_details, $line->quantity * $line->item_tax);

                    $line_array['group_tax_details'] = $group_tax_details;

                    // foreach ($group_tax_details as $key => $value) {
                    //     if (!isset($output_taxes['taxes'][$key])) {
                    //         $output_taxes['taxes'][$key] = 0;
                    //     }
                    //     $output_taxes['taxes'][$key] += $value;
                    // }
                }
                // else {
                //     $tax_name = $tax_details->name;
                //     if (!isset($output_taxes['taxes'][$tax_name])) {
                //         $output_taxes['taxes'][$tax_name] = 0;
                //     }
                //     $output_taxes['taxes'][$tax_name] += ($line->quantity * $line->item_tax);
                // }
            }

            $line_array['line_discount'] = method_exists($line, 'get_discount_amount') ? $this->num_f($line->get_discount_amount()) : 0;
            if ($line->line_discount_type == 'percentage') {
                $line_array['line_discount'] .= ' (' . $this->num_f($line->line_discount_amount) . '%)';
            }

            if ($il->show_brand == 1) {
                $line_array['brand'] = !empty($brand->name) ? $brand->name : '';
            }
            if ($il->show_sku == 1) {
                $line_array['sub_sku'] = !empty($variation->sub_sku) ? $variation->sub_sku : '' ;
            }
            if ($il->show_cat_code == 1) {
                $line_array['cat_code'] = !empty($cat->short_code) ? $cat->short_code : '';
            }
            if ($il->show_sale_description == 1) {
                $line_array['sell_line_note'] = !empty($line->sell_line_note) ? $line->sell_line_note : '';
            }
            if ($is_lot_number_enabled == 1 && $il->show_lot == 1) {
                $line_array['lot_number'] = !empty($line->lot_details->lot_number) ? $line->lot_details->lot_number : null;
                $line_array['lot_number_label'] = __('lang_v1.lot');
            }

            if ($is_product_expiry_enabled == 1 && $il->show_expiry == 1) {
                $line_array['product_expiry'] = !empty($line->lot_details->exp_date) ? $this->format_date($line->lot_details->exp_date) : null;
                $line_array['product_expiry_label'] = __('lang_v1.expiry');
            }

            //If modifier is set set modifiers line to parent sell line
            if (!empty($line->modifiers)) {
                foreach ($line->modifiers as $modifier_line) {
                    $product = $modifier_line->product;
                    $variation = $modifier_line->variations;
                    $unit = $modifier_line->product->unit;
                    $brand = $modifier_line->product->brand;
                    $cat = $modifier_line->product->category;

                    $modifier_line_array = [
                        //Field for 1st column
                        'name' => $product->name,
                        'variation' => (empty($variation->name) || $variation->name == 'DUMMY') ? '' : $variation->name,
                        //Field for 2nd column
                        'quantity' => $this->num_f($modifier_line->quantity),
                        'units' => !empty($unit->short_name) ? $unit->short_name : '',

                        //Field for 3rd column
                        'unit_price_inc_tax' => $this->num_f($modifier_line->unit_price_inc_tax, false, $precision),
                        'unit_price_exc_tax' => $this->num_f($modifier_line->unit_price, false, $precision),
                        'price_exc_tax' => $modifier_line->quantity * $modifier_line->unit_price,

                        //Fields for 4th column
                        'line_total' => $this->num_f($modifier_line->unit_price_inc_tax * $line->quantity, false, $precision),
                    ];
                    
                    if ($il->show_sku == 1) {
                        $modifier_line_array['sub_sku'] = !empty($variation->sub_sku) ? $variation->sub_sku : '' ;
                    }
                    if ($il->show_cat_code == 1) {
                        $modifier_line_array['cat_code'] = !empty($cat->short_code) ? $cat->short_code : '';
                    }
                    if ($il->show_sale_description == 1) {
                        $modifier_line_array['sell_line_note'] = !empty($line->sell_line_note) ? $line->sell_line_note : '';
                    }

                    $line_array['modifiers'][] = $modifier_line_array;
                }
            }

            // Sell line note
            $line_array['sell_line_note'] = $line->sell_line_note;

            $output_lines[] = $line_array;
        }

        return ['lines' => $output_lines];
    }

    /**
     * Returns each line details for sell return invoice display
     *
     * @return array
     */
    protected function _receiptDetailsSellReturnLines($lines, $il, $is_product_expiry_enabled, $is_lot_number_enabled)
    {
        // Number of decimals
        $business_id = request()->session()->get('user.business_id');$business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $precision = isset($product_settings['decimals_in_fiscal_documents']) ? $product_settings['decimals_in_fiscal_documents'] : 6;

        $output_lines = [];
        $output_taxes = ['taxes' => []];
        foreach ($lines as $line) {
            //Group product taxes by name.
            $tax_details = TaxRate::find($line->tax_id);
            // if (!empty($tax_details)) {
            //     if ($tax_details->is_tax_group) {
            //         $group_tax_details = $this->groupTaxDetails($tax_details, $line->quantity_returned * $line->item_tax);
            //         foreach ($group_tax_details as $key => $value) {
            //             if (!isset($output_taxes['taxes'][$key])) {
            //                 $output_taxes['taxes'][$key] = 0;
            //             }
            //             $output_taxes['taxes'][$key] += $value;
            //         }
            //     } else {
            //         $tax_name = $tax_details->name;
            //         if (!isset($output_taxes['taxes'][$tax_name])) {
            //             $output_taxes['taxes'][$tax_name] = 0;
            //         }
            //         $output_taxes['taxes'][$tax_name] += ($line->quantity_returned * $line->item_tax);
            //     }
            // }

            $product = $line->product;
            $variation = $line->variations;
            $unit = $line->product->unit;
            $brand = $line->product->brand;
            $cat = $line->product->category;

            $line_array = [
                //Field for 1st column
                'code' => $product->sku,
                'name' => $product->name,
                'sub_sku' => !empty($variation->sub_sku) ? $variation->sub_sku : '',
                'variation' => (empty($variation->name) || $variation->name == 'DUMMY') ? '' : $variation->name,
                //Field for 2nd column
                'quantity_uf' => $line->quantity_returned,
                'quantity' => $this->num_f($line->quantity_returned, false, $this->quantity_precision),
                'units' => !empty($unit->short_name) ? $unit->short_name : '',

                'sell_line_note' => !empty($line->sell_line_note) ? $line->sell_line_note : '',
                'unit_price_exc' => $this->num_f($line->unit_price_before_discount, false, $precision),
                'unit_price' => $this->num_f($line->unit_price, false, $precision),
                'tax_amount' => $this->num_f($line->quantity_returned * $line->tax_amount, false, $precision),
                'tax_name' => !empty($tax_details) ? $tax_details->name: null,

                //Field for 3rd column
                'unit_price_inc_tax' => $this->num_f($line->unit_price_inc_tax, false, $precision),
                'unit_price_exc_tax' => $this->num_f($line->unit_price, false, $precision),
                'line_total_exc_tax' => $this->num_f($line->quantity_returned * $line->unit_price_before_discount, false, $precision),

                //Fields for 4th column
                'line_total' => $this->num_f($line->unit_price * $line->quantity_returned, false, $precision)
            ];
            $line_array['line_discount'] = 0;

            //Group product taxes by name.
            if (!empty($tax_details)) {
                if ($tax_details->is_tax_group) {
                    $group_tax_details = $this->groupTaxDetails($tax_details, $line->quantity * $line->item_tax);

                    $line_array['group_tax_details'] = $group_tax_details;

                    // foreach ($group_tax_details as $key => $value) {
                    //     if (!isset($output_taxes['taxes'][$key])) {
                    //         $output_taxes['taxes'][$key] = 0;
                    //     }
                    //     $output_taxes['taxes'][$key] += $value;
                    // }
                }
                // else {
                //     $tax_name = $tax_details->name;
                //     if (!isset($output_taxes['taxes'][$tax_name])) {
                //         $output_taxes['taxes'][$tax_name] = 0;
                //     }
                //     $output_taxes['taxes'][$tax_name] += ($line->quantity * $line->item_tax);
                // }
            }

            if ($il->show_brand == 1) {
                $line_array['brand'] = !empty($brand->name) ? $brand->name : '';
            }

            if ($il->show_sku == 1) {
                $line_array['sub_sku'] = !empty($variation->sub_sku) ? $variation->sub_sku : '' ;
            }

            if ($il->show_cat_code == 1) {
                $line_array['cat_code'] = !empty($cat->short_code) ? $cat->short_code : '';
            }

            if ($il->show_sale_description == 1) {
                $line_array['sell_line_note'] = !empty($line->sell_line_note) ? $line->sell_line_note : '';
            }

            if ($is_lot_number_enabled == 1 && $il->show_lot == 1) {
                $line_array['lot_number'] = !empty($line->lot_details->lot_number) ? $line->lot_details->lot_number : null;
                $line_array['lot_number_label'] = __('lang_v1.lot');
            }

            if ($is_product_expiry_enabled == 1 && $il->show_expiry == 1) {
                $line_array['product_expiry'] = !empty($line->lot_details->exp_date) ? $this->format_date($line->lot_details->exp_date) : null;
                $line_array['product_expiry_label'] = __('lang_v1.expiry');
            }

            $output_lines[] = $line_array;
        }

        return ['lines' => $output_lines, 'taxes' => $output_taxes];
    }

    /**
     * Gives the invoice number for a Final/Draft invoice
     *
     * @param int $business_id
     * @param string $status
     * @param string $location_id
     *
     * @return string
     */
    public function getInvoiceNumber($business_id, $status, $location_id)
    {
        if ($status == 'final') {
            $scheme = $this->getInvoiceScheme($business_id, $location_id);
            
            if ($scheme->scheme_type == 'blank') {
                $prefix = $scheme->prefix;
            } else {
                $prefix = date('Y') . '-';
            }

            //Count
            $count = $scheme->start_number + $scheme->invoice_count;
            $count = str_pad($count, $scheme->total_digits, '0', STR_PAD_LEFT);

            //Prefix + count
            $invoice_no = $prefix . $count;

            //Increment the invoice count
            $scheme->invoice_count = $scheme->invoice_count + 1;
            $scheme->save();

            return $invoice_no;
        } else {
            return str_random(5);
        }
    }

    /**
     * Check if the correlative exists.
     * @param int $location
     * @param  int  $document
     * @param  string  $correlative
     * @return array
     */
    public function validateCorrelative($location, $document, $correlative, $transaction_id = 0)
    {
        $business_id = request()->session()->get('user.business_id');

        $document_correlative = DocumentCorrelative::where('document_type_id', $document)
            ->where('business_id', $business_id)
            ->where('status', 'active')
            ->where('location_id', $location)
            ->first();

        $transaction = Transaction::where('business_id', $business_id)
            ->where('id', '!=', $transaction_id)
            ->where('correlative', $correlative)
            ->where('location_id', $location)
            ->where('document_types_id', $document)
            ->where('serie', $document_correlative->serie)
            ->first();

        /** validate cashier closure open and close correlative */
        $cashier_closure = CashierClosure::join('cashiers as c', 'cashier_closures.cashier_id', 'c.id')
            ->where('c.business_location_id', $location)
            ->where(function($query) use ($correlative) {
                $query->where('cashier_closures.open_correlative', $correlative)
                    ->orWhere('cashier_closures.close_correlative', $correlative);
            })->count();
        
        $doc_type = DocumentType::where('id', $document)
            ->where('short_name', 'Ticket')
            ->count();

        if (!empty($transaction) || ($cashier_closure > 0 && $doc_type > 0)) {
            $output = ['flag' => true];
        } else {
            $output = ['flag' => false];
        }

        if (config('app.business') == 'optics') {
            $output = ['flag' => false];
        }

        return $output;
    }

    private function getInvoiceScheme($business_id, $location_id)
    {
        $scheme_id = BusinessLocation::where('business_id', $business_id)
                    ->where('id', $location_id)
                    ->first()
                    ->invoice_scheme_id;
        if (!empty($scheme_id) && $scheme_id != 0) {
            $scheme = InvoiceScheme::find($scheme_id);
        }

        //Check if scheme is not found then return default scheme
        if (empty($scheme)) {
            $scheme = InvoiceScheme::where('business_id', $business_id)
                    ->where('is_default', 1)
                    ->first();
        }

        return $scheme;
    }

    /**
     * Gives the list of products for a purchase transaction
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return array
     */
    public function getPurchaseProducts($business_id, $transaction_id)
    {
        $products = Transaction::join('purchase_lines as pl', 'transactions.id', '=', 'pl.transaction_id')
                            ->leftjoin('products as p', 'pl.product_id', '=', 'p.id')
                            ->leftjoin('variations as v', 'pl.variation_id', '=', 'v.id')
                            ->where('transactions.business_id', $business_id)
                            ->where('transactions.id', $transaction_id)
                            ->where('transactions.type', 'purchase')
                            ->select('p.id as product_id', 'p.name as product_name', 'v.id as variation_id', 'v.name as variation_name', 'pl.quantity as quantity')
                            ->get();
        return $products;
    }

    /**
     * Gives the total purchase amount for a business within the date range passed
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return array
     */
    public function getPurchaseTotals($business_id, $start_date = null, $end_date = null, $location_id = null)
    {
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->select(
                DB::raw("IF(purchase_type = 'international', total_after_expense, final_total) as final_total"),
                DB::raw("(final_total - tax_amount) as total_exc_tax"),
                DB::raw("SUM((SELECT SUM(tp.amount) FROM transaction_payments as tp WHERE tp.transaction_id=transactions.id)) as total_paid"),
                DB::raw("IF(purchase_type = 'international', total_after_expense, total_before_tax) as total_before_tax"),
            )
            ->groupBy('transactions.id');
        
        // Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
        }

        // Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }

        $purchase_details = $query->get();

        $output['total_purchase_inc_tax'] = $purchase_details->sum('final_total');
        //$output['total_purchase_exc_tax'] = $purchase_details->sum('total_exc_tax');
        $output['total_purchase_exc_tax'] = $purchase_details->sum('total_before_tax');
        $output['purchase_due'] = $purchase_details->sum('final_total') - $purchase_details->sum('total_paid');

        // Show values ​​including or excluding taxes
        $business = Business::find($business_id);
        $dashboard_settings = empty($business->dashboard_settings) ? null : json_decode($business->dashboard_settings, true);
        $output['box_exc_tax'] = is_null($dashboard_settings) ? 0 : $dashboard_settings['box_exc_tax'];

        return $output;
    }

    /**
     * Gives the total sell amount for a business within the date range passed
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return array
     */
    public function getSellTotals($business_id, $start_date = null, $end_date = null, $location_id = null, $created_by = null)
    {
        $query = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->select(
                'transactions.id',
                'final_total',
                DB::raw("(final_total - tax_amount) as total_exc_tax"),
                DB::raw('(SELECT SUM(IF(tp.is_return = 1, -1*tp.amount, tp.amount)) FROM transaction_payments as tp WHERE tp.transaction_id = transactions.id) as total_paid'),
                DB::raw('SUM(total_before_tax) as total_before_tax'),
                'shipping_charges'
            )
            ->groupBy('transactions.id');

        $sell_return = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell_return')
            ->where('transactions.status', 'final')
            ->select('final_total')
            ->groupBy('transactions.id');

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
            $sell_return->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            $sell_return->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
            $sell_return->whereDate('transaction_date', '<=', $end_date);
        }

        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
            $sell_return->where('transactions.location_id', $location_id);
        }

        if (!empty($created_by)) {
            $query->where('transactions.created_by', $created_by);
            $sell_return->where('transactions.created_by', $created_by);
        }

        $sell_details = $query->get();
        $sell_return_details = $sell_return->get();

        $output['total_sell_inc_tax'] = $sell_details->sum('final_total');
        //$output['total_sell_exc_tax'] = $sell_details->sum('total_exc_tax');
        $output['total_sell_exc_tax'] = $sell_details->sum('total_before_tax');
        $output['invoice_due'] = $sell_details->sum('final_total') - $sell_details->sum('total_paid');
        $output['total_shipping_charges'] = $sell_details->sum('shipping_charges');

        // Show values ​​including or excluding taxes
        $business = Business::find($business_id);
        $dashboard_settings = empty($business->dashboard_settings) ? null : json_decode($business->dashboard_settings, true);
        $output['box_exc_tax'] = is_null($dashboard_settings) ? 0 : $dashboard_settings['box_exc_tax'];

        if ($dashboard_settings['subtract_sell_return']) {
            $output['invoice_due'] -= $sell_return_details->sum('final_total');
        }

        return $output;
    }

    /**
     * Gives the total input tax for a business within the date range passed
     *
     * @param int $business_id
     * @param string $start_date default null
     * @param string $end_date default null
     *
     * @return float
     */
    public function getInputTax($business_id, $start_date = null, $end_date = null, $location_id = null)
    {
        $query1 = Transaction::where('transactions.business_id', $business_id)
                        ->leftjoin('tax_rates as T', 'transactions.tax_id', '=', 'T.id')
                        ->whereIn('type', ['purchase', 'purchase_return'])
                        ->whereNotNull('transactions.tax_id')
                        ->select(
                            DB::raw("SUM( IF(type='purchase', transactions.tax_amount, -1 * transactions.tax_amount) ) as transaction_tax"),
                            'T.name as tax_name',
                            'T.id as tax_id',
                            'T.is_tax_group'
                        );

        $query2 = Transaction::where('transactions.business_id', $business_id)
                        ->leftjoin('purchase_lines as pl', 'transactions.id', '=', 'pl.transaction_id')
                        ->leftjoin('tax_rates as T', 'pl.tax_id', '=', 'T.id')
                        ->where('type', 'purchase')
                        ->whereNotNull('pl.tax_id')
                        ->select(
                            DB::raw("SUM( pl.quantity * pl.item_tax ) as product_tax"),
                            'T.name as tax_name',
                            'T.id as tax_id',
                            'T.is_tax_group'
                        );

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query1->whereIn('transactions.location_id', $permitted_locations);
            $query2->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $query1->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            $query2->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $query1->where('transactions.location_id', $location_id);
            $query2->where('transactions.location_id', $location_id);
        }

        $transaction_tax_details = $query1->groupBy('T.id')
                                    ->get();

        $product_tax_details = $query2->groupBy('T.id')
                                    ->get();
        $tax_details = [];
        foreach ($transaction_tax_details as $transaction_tax) {
            $tax_details[$transaction_tax->tax_id]['tax_name'] = $transaction_tax->tax_name;
            $tax_details[$transaction_tax->tax_id]['tax_amount'] = $transaction_tax->transaction_tax;

            $tax_details[$transaction_tax->tax_id]['is_tax_group'] = false;
            if ($transaction_tax->is_tax_group == 1) {
                $tax_details[$transaction_tax->tax_id]['is_tax_group'] = true;
            }
        }

        foreach ($product_tax_details as $product_tax) {
            if (!isset($tax_details[$product_tax->tax_id])) {
                $tax_details[$product_tax->tax_id]['tax_name'] = $product_tax->tax_name;
                $tax_details[$product_tax->tax_id]['tax_amount'] = $product_tax->product_tax;

                $tax_details[$product_tax->tax_id]['is_tax_group'] = false;
                if ($product_tax->is_tax_group == 1) {
                    $tax_details[$product_tax->tax_id]['is_tax_group'] = true;
                }
            } else {
                $tax_details[$product_tax->tax_id]['tax_amount'] += $product_tax->product_tax;
            }
        }

        //If group tax add group tax details
        foreach ($tax_details as $key => $value) {
            if ($value['is_tax_group']) {
                $tax_details[$key]['group_tax_details'] = $this->groupTaxDetails($key, $value['tax_amount']);
            }
        }

        $output['tax_details'] = $tax_details;
        $output['total_tax'] = $transaction_tax_details->sum('transaction_tax') + $product_tax_details->sum('product_tax');

        return $output;
    }

    /**
     * Gives the total output tax for a business within the date range passed
     *
     * @param int $business_id
     * @param string $start_date default null
     * @param string $end_date default null
     *
     * @return float
     */
    public function getOutputTax($business_id, $start_date = null, $end_date = null, $location_id = null)
    {
        $query1 = Transaction::where('transactions.business_id', $business_id)
                        ->leftjoin('tax_rates as T', 'transactions.tax_id', '=', 'T.id')
                        ->whereIn('type', ['sell', 'sell_return'])
                        ->whereNotNull('transactions.tax_id')
                        ->where('transactions.status', '=', 'final')
                        ->select(
                            DB::raw("SUM( IF(type='sell', transactions.tax_amount, -1 * transactions.tax_amount) ) as transaction_tax"),
                            'T.name as tax_name',
                            'T.id as tax_id',
                            'T.is_tax_group'
                        );

        $query2 = Transaction::where('transactions.business_id', $business_id)
                        ->leftjoin('transaction_sell_lines as tsl', 'transactions.id', '=', 'tsl.transaction_id')
                        ->leftjoin('tax_rates as T', 'tsl.tax_id', '=', 'T.id')
                        ->where('type', 'sell')
                        ->whereNotNull('tsl.tax_id')
                        ->where('transactions.status', '=', 'final')
                        ->select(
                            DB::raw("SUM( tsl.quantity * tsl.item_tax ) as product_tax"),
                            'T.name as tax_name',
                            'T.id as tax_id',
                            'T.is_tax_group'
                        );

        ///Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query1->whereIn('transactions.location_id', $permitted_locations);
            $query2->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $query1->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            $query2->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $query1->where('transactions.location_id', $location_id);
            $query2->where('transactions.location_id', $location_id);
        }

        $transaction_tax_details = $query1->groupBy('T.id')
                                    ->get();

        $product_tax_details = $query2->groupBy('T.id')
                                    ->get();
        $tax_details = [];
        foreach ($transaction_tax_details as $transaction_tax) {
            $tax_details[$transaction_tax->tax_id]['tax_name'] = $transaction_tax->tax_name;
            $tax_details[$transaction_tax->tax_id]['tax_amount'] = $transaction_tax->transaction_tax;

            $tax_details[$transaction_tax->tax_id]['is_tax_group'] = false;
            if ($transaction_tax->is_tax_group == 1) {
                $tax_details[$transaction_tax->tax_id]['is_tax_group'] = true;
            }
        }

        foreach ($product_tax_details as $product_tax) {
            if (!isset($tax_details[$product_tax->tax_id])) {
                $tax_details[$product_tax->tax_id]['tax_name'] = $product_tax->tax_name;
                $tax_details[$product_tax->tax_id]['tax_amount'] = $product_tax->product_tax;

                $tax_details[$product_tax->tax_id]['is_tax_group'] = false;
                if ($product_tax->is_tax_group == 1) {
                    $tax_details[$product_tax->tax_id]['is_tax_group'] = true;
                }
            } else {
                $tax_details[$product_tax->tax_id]['tax_amount'] += $product_tax->product_tax;
            }
        }

        //If group tax add group tax details
        foreach ($tax_details as $key => $value) {
            if ($value['is_tax_group']) {
                $tax_details[$key]['group_tax_details'] = $this->groupTaxDetails($key, $value['tax_amount']);
            }
        }

        $output['tax_details'] = $tax_details;
        $output['total_tax'] = $transaction_tax_details->sum('transaction_tax') + $product_tax_details->sum('product_tax');

        return $output;
    }

    /**
     * Gives total sells of last 30 days day-wise
     *
     * @param int $business_id
     * @param array $filters
     *
     * @return Obj
     */
    public function getSellsLast30Days($business_id)
    {
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween(DB::raw('date(transaction_date)'), [\Carbon::now()->subDays(30), \Carbon::now()]);

        // Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        $sells = $query->select(
                DB::raw("DATE_FORMAT(transaction_date, '%Y-%m-%d') as date"),
                DB::raw("SUM(final_total) as total_sells"),
                DB::raw("SUM(total_before_tax) as total_sells_exc_tax"),
            )
            ->groupBy(DB::raw('Date(transaction_date)'))
            ->get();

        // Show values ​​including or excluding taxes
        $business = Business::find($business_id);
        $dashboard_settings = empty($business->dashboard_settings) ? null : json_decode($business->dashboard_settings, true);

        if ($dashboard_settings['box_exc_tax']) {
            $sells = $sells->pluck('total_sells_exc_tax', 'date');
        } else {
            $sells = $sells->pluck('total_sells', 'date');
        }

        return $sells;
    }

    /**
     * Gives total sells of current FY month-wise
     *
     * @param int $business_id
     * @param string $start
     * @param string $end
     *
     * @return Obj
     */
    public function getSellsCurrentFy($business_id, $start, $end)
    {
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween(DB::raw('date(transaction_date)'), [$start, $end]);

        // Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        
        $sells = $query->groupBy(DB::raw("DATE_FORMAT(transaction_date, '%Y-%m')"))
            ->select(
                DB::raw("DATE_FORMAT(transaction_date, '%m-%Y') as yearmonth"),
                DB::raw("SUM( final_total ) as total_sells"),
                DB::raw("SUM(total_before_tax) as total_sells_exc_tax"),
            )
            ->get();

        // Show values ​​including or excluding taxes
        $business = Business::find($business_id);
        $dashboard_settings = empty($business->dashboard_settings) ? null : json_decode($business->dashboard_settings, true);

        if ($dashboard_settings['box_exc_tax']) {
            $sells = $sells->pluck('total_sells_exc_tax', 'yearmonth');
        } else {
            $sells = $sells->pluck('total_sells', 'yearmonth');
        }

        return $sells;
    }

    /**
     * Retrives expense report
     *
     * @param int $business_id
     * @param array $filters
     * @param string $type = by_category (by_category or total)
     *
     * @return Obj
     */
    public function getExpenseReport(
        $business_id,
        $filters = [],
        $type = 'by_category'
    ) {
    
        $query = Transaction::leftjoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
                            ->where('transactions.business_id', $business_id)
                            ->where('type', 'expense')
                            ->where('payment_status', 'paid');

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!empty($filters['location_id'])) {
            $query->where('transactions.location_id', $filters['location_id']);
        }

        if (!empty($filters['expense_for'])) {
            $query->where('transactions.expense_for', $filters['expense_for']);
        }

        if (!empty($filters['category'])) {
            $query->where('ec.id', $filters['category']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$filters['start_date'],
                $filters['end_date']]);
        }

        //Check tht type of report and return data accordingly
        if ($type == 'by_category') {
            $expenses = $query->select(
                DB::raw("SUM( final_total ) as total_expense"),
                'ec.name as category'
            )
                        ->groupBy('expense_category_id')
                        ->get();
        } elseif ($type == 'total') {
            $expenses = $query->select(
                DB::raw("SUM( final_total ) as total_expense")
            )
                        ->first();
        }
        
        return $expenses;
    }

    /**
     * Get total paid amount for a transaction
     *
     * @param int $transaction_id
     *
     * @return int
     */
    public function getTotalPaid($transaction_id)
    {
        $total_paid = TransactionPayment::where('transaction_id', $transaction_id)
                ->select(DB::raw('SUM(IF( is_return = 0, amount, amount*-1))as total_paid'))
                ->first()
                ->total_paid;

        return $total_paid;
    }

    /**
     * Calculates the payment status and returns back.
     *
     * @param int $transaction_id
     * @param float $final_amount = null
     *
     * @return string
     */
    public function calculatePaymentStatus($transaction_id, $final_amount = null)
    {
        $transaction = Transaction::find($transaction_id);

        if ($transaction->type == 'sell') {
            $total_paid = $transaction->payment_balance;
        } else {
            $total_paid = $this->getTotalPaid($transaction_id);
        }

        if (is_null($final_amount)) {
            $final_amount = $transaction->purchase_type == 'international' ? $transaction->total_after_expense : $transaction->final_total;
        }

        $status = 'due';

        if ($final_amount <= $total_paid) {
            $status = 'paid';
        } else if ($total_paid > 0 && $final_amount > $total_paid) {
            $status = 'partial';
        }

        return $status;
    }

    /**
     * Update the payment status for purchase or sell transactions. Returns
     * the status
     *
     * @param int $transaction_id
     *
     * @return string
     */
    public function updatePaymentStatus($transaction_id, $final_amount = null)
    {
        $status = $this->calculatePaymentStatus($transaction_id, $final_amount);

        Transaction::where('id', $transaction_id)->update(['payment_status' => $status]);

        return $status;
    }

    /**
     * Purchase currency details
     *
     * @param int $business_id
     *
     * @return object
     */
    public function purchaseCurrencyDetails($business_id)
    {
        $business = Business::find($business_id);
        $output = ['purchase_in_diff_currency' => false,
                    'p_exchange_rate' => 1,
                    'decimal_seperator' => '.',
                    'thousand_seperator' => ',',
                    'symbol' => '',
                ];

        //Check if diff currency is used or not.
        if ($business->purchase_in_diff_currency == 1) {
            $output['purchase_in_diff_currency'] = true;
            $output['p_exchange_rate'] = $business->p_exchange_rate;

            $currency_id = $business->purchase_currency_id;
        } else {
            $output['purchase_in_diff_currency'] = false;
            $output['p_exchange_rate'] = 1;
            $currency_id = $business->currency_id;
        }

        $currency = Currency::find($currency_id);
        $output['thousand_separator'] = $currency->thousand_separator;
        $output['decimal_separator'] = $currency->decimal_separator;
        $output['symbol'] = $currency->symbol;
        $output['code'] = $currency->code;
        $output['name'] = $currency->currency;

        return (object)$output;
    }

    /**
     * Pay contact due at once
     *
     * @param obj $parent_payment, string $type
     *
     * @return void
     */
    public function payAtOnce($parent_payment, $type)
    {

        //Get all unpaid transaction for the contact
        $types = ['opening_balance', $type];
        
        if ($type == 'purchase_return') {
            $types = [$type];
        }

        $due_transactions = Transaction::where('contact_id', $parent_payment->payment_for)
                                ->whereIn('type', $types)
                                ->where('payment_status', '!=', 'paid')
                                ->orderBy('transaction_date', 'asc')
                                ->get();
        $total_amount = $parent_payment->amount;

        $tranaction_payments = [];
        if ($due_transactions->count()) {
            foreach ($due_transactions as $transaction) {
                if ($total_amount > 0) {
                    $total_paid = $this->getTotalPaid($transaction->id);
                    $due = $transaction->final_total - $total_paid;

                    $now = \Carbon::now()->toDateTimeString();

                    $array = [
                            'transaction_id' => $transaction->id,
                            'business_id' => $parent_payment->business_id,
                            'method' => $parent_payment->method,
                            // 'transaction_no' => $parent_payment->method,
                            // 'card_transaction_number' => $parent_payment->card_transaction_number,
                            // 'card_number' => $parent_payment->card_number,
                            'card_type' => $parent_payment->card_type,
                            'card_holder_name' => $parent_payment->card_holder_name,
                            // 'card_month' => $parent_payment->card_month,
                            // 'card_year' => $parent_payment->card_year,
                            // 'card_security' => $parent_payment->card_security,
                            'card_pos' => $parent_payment->card_pos,
                            'card_authotization_number' => $parent_payment->card_authotization_number,
                            'check_number' => $parent_payment->check_number,
                            'check_account' => $parent_payment->check_account,
                            'check_bank' => $parent_payment->check_bank,
                            'check_account_owner' => $parent_payment->check_account_owner,
                            'paid_on' => $parent_payment->paid_on,
                            'transfer_ref_no' => $parent_payment->transfer_ref_no,
                            'transfer_issuing_bank' => $parent_payment->transfer_issuing_bank,
                            'transfer_destination_account' => $parent_payment->transfer_destination_account,
                            'transfer_receiving_bank' => $parent_payment->transfer_receiving_bank,
                            'created_by' => $parent_payment->created_by,
                            'payment_for' => $parent_payment->payment_for,
                            'parent_id' => $parent_payment->id,
                            'created_at' => $now,
                            'updated_at' => $now
                        ];

                    $prefix_type = 'purchase_payment';
                    if (in_array($transaction->type, ['sell', 'sell_return'])) {
                        $prefix_type = 'sell_payment';
                    }
                    $ref_count = $this->setAndGetReferenceCount($prefix_type);
                    //Generate reference number
                    $payment_ref_no = $this->generateReferenceNumber($prefix_type, $ref_count);
                    $array['payment_ref_no'] = $payment_ref_no;

                    if ($due <= $total_amount) {
                        $array['amount'] = $due;
                        $tranaction_payments[] = $array;

                        //Update transaction status to paid
                        $transaction->payment_status = 'paid';
                        $transaction->save();

                        $total_amount = $total_amount - $due;
                    } else {
                        $array['amount'] = $total_amount;
                        $tranaction_payments[] = $array;

                        //Update transaction status to partial
                        $transaction->payment_status = 'partial';
                        $transaction->save();
                        break;
                    }
                }
            }

            //Insert new transaction payments
            if (!empty($tranaction_payments)) {
                TransactionPayment::insert($tranaction_payments);
            }
        }
    }

    /**
     * Add a mapping between purchase & sell lines.
     * NOTE: Don't use request variable here, request variable don't exist while adding
     * dummybusiness via command line
     *
     * @param array $business
     * @param array $transaction_lines
     * @param string $mapping_type = purchase (purchase or stock_adjustment)
     * @param boolean $check_expiry = true
     * @param int $purchase_line_id (default: null)
     *
     * @return object
     */
    public function mapPurchaseSell($business, $transaction_lines, $mapping_type = 'purchase', $check_expiry = true, $purchase_line_id = null, $warehouse_id = null)
    {
        if (empty($transaction_lines)) {
            return false;
        }

        //Set flag to check for expired items during SELLING only.
        $stop_selling_expired = false;
        if ($check_expiry) {
            if (session()->has('business') && request()->session()->get('business')['enable_product_expiry'] == 1 && request()->session()->get('business')['on_product_expiry'] == 'stop_selling') {
                if ($mapping_type == 'purchase') {
                    $stop_selling_expired = true;
                }
            }
        }

        $qty_selling = null;
        foreach ($transaction_lines as $line) {
            //Check if stock is not enabled then no need to assign purchase & sell
            $product = Product::find($line->product_id);
            if ($product->enable_stock != 1) {
                continue;
            }

            //Get purchase lines, only for products with enable stock.
            $query = Transaction::join('purchase_lines AS PL', 'transactions.id', '=', 'PL.transaction_id')
                ->where('transactions.business_id', $business['id'])
                ->where('transactions.location_id', $business['location_id'])
                // ->whereIn('transactions.type', ['purchase', 'purchase_transfer',
                //     'opening_stock', 'stock_adjustment'])
                ->where('transactions.status', 'received')
                ->whereRaw('(PL.quantity_sold + PL.quantity_adjusted + PL.quantity_returned) < PL.quantity')
                ->where('PL.product_id', $line->product_id)
                ->where('PL.variation_id', $line->variation_id)
                ->where(function($query) {
                    $query->whereIn('transactions.type', ['purchase', 'purchase_transfer', 'opening_stock', 'stock_adjustment', 'physical_inventory']);
                    $query->orWhereNull('transactions.type');
                });
            
            if(!is_null($warehouse_id)){
                $query->where('transactions.warehouse_id', $warehouse_id);
            }

            //If product expiry is enabled then check for on expiry conditions
            if ($stop_selling_expired && empty($purchase_line_id)) {
                $stop_before = request()->session()->get('business')['stop_selling_before'];
                $expiry_date = \Carbon::today()->addDays($stop_before)->toDateString();
                $query->whereRaw('PL.exp_date IS NULL OR PL.exp_date > ?', [$expiry_date]);
            }

            //If lot number present consider only lot number purchase line
            if (!empty($line->lot_no_line_id)) {
                $query->where('PL.id', $line->lot_no_line_id);
            }

            //If purchase_line_id is given consider only that purchase line
            if (!empty($purchase_line_id)) {
                $query->where('PL.id', $purchase_line_id);
            }

            //Sort according to LIFO or FIFO
            if ($business['accounting_method'] == 'lifo') {
                $query = $query->orderBy('transaction_date', 'desc');
            } else {
                $query = $query->orderBy('transaction_date', 'asc');
            }

            $rows = $query->select(
                        'PL.id as purchase_lines_id',
                        DB::raw('(PL.quantity - (PL.quantity_sold + PL.quantity_adjusted +PL.quantity_returned)) AS quantity_available'),
                        'PL.quantity_sold as quantity_sold',
                        'PL.quantity_adjusted as quantity_adjusted',
                        'PL.quantity_returned as quantity_returned',
                        'transactions.invoice_no'
                    )->get();

            $purchase_sell_map = [];

            //Iterate over the rows, assign the purchase line to sell lines.
            $qty_selling = $line->quantity;
            foreach ($rows as $k => $row) {
                $qty_allocated = 0;

                //Check if qty_available is more or equal
                if ($qty_selling <= $row->quantity_available) {
                    $qty_allocated = $qty_selling;
                    $qty_selling = 0;
                } else {
                    $qty_selling = $qty_selling - $row->quantity_available;
                    $qty_allocated = $row->quantity_available;
                }

                //Check for sell mapping or stock adjsutment mapping
                if ($mapping_type == 'stock_adjustment') {
                    //Mapping of stock adjustment
                    $purchase_adjustment_map[] =
                        ['stock_adjustment_line_id' => $line->id,
                            'purchase_line_id' => $row->purchase_lines_id,
                            'quantity' => $qty_allocated,
                            'created_at' => \Carbon::now(),
                            'updated_at' => \Carbon::now()
                        ];

                    //Update purchase line
                    if($adjust_type == "normal"){
                        PurchaseLine::where('id', $row->purchase_lines_id)
                            ->update(['quantity_adjusted' => $row->quantity_adjusted - $qty_allocated]);
                    } else if($adjust_type == "abnormal"){
                        PurchaseLine::where('id', $row->purchase_lines_id)
                            ->update(['quantity_adjusted' => $row->quantity_adjusted + $qty_allocated]);
                    }
                } elseif ($mapping_type == 'purchase') {
                    //Mapping of purchase
                    $purchase_sell_map[] = ['sell_line_id' => $line->id,
                            'purchase_line_id' => $row->purchase_lines_id,
                            'quantity' => $qty_allocated,
                            'created_at' => \Carbon::now(),
                            'updated_at' => \Carbon::now()
                        ];

                    //Update purchase line
                    PurchaseLine::where('id', $row->purchase_lines_id)
                        ->update(['quantity_sold' => $row->quantity_sold + $qty_allocated]);
                }

                if ($qty_selling == 0) {
                    break;
                }
            }

            if (! ($qty_selling == 0 || is_null($qty_selling))) {
                $variation = Variation::find($line->variation_id);
                $mismatch_name = $product->name;
                if (!empty($variation->sub_sku)) {
                    $mismatch_name .= ' ' . 'SKU: ' . $variation->sub_sku;
                }
                if (!empty($qty_selling)) {
                    $mismatch_name .= ' ' . 'Quantity: ' . abs($qty_selling);
                }
                
                if ($mapping_type == 'purchase') {
                    $mismatch_error = trans(
                        "messages.purchase_sell_mismatch_exception",
                        ['product' => $mismatch_name]
                    );

                    if ($stop_selling_expired) {
                        $mismatch_error .= __('lang_v1.available_stock_expired');
                    }
                } elseif ($mapping_type == 'stock_adjustment') {
                    $mismatch_error = trans(
                        "messages.purchase_stock_adjustment_mismatch_exception",
                        ['product' => $mismatch_name]
                    );
                }

                $business_name = optional(Business::find($business['id']))->name;
                $location_name = optional(BusinessLocation::find($business['location_id']))->name;
                \Log::emergency($mismatch_error . ' Business: ' . $business_name . ' Location: ' . $location_name);
                throw new PurchaseSellMismatch($mismatch_error);
            }

            //Insert the mapping
            if (!empty($purchase_adjustment_map)) {
                TransactionSellLinesPurchaseLines::insert($purchase_adjustment_map);
            }
            if (!empty($purchase_sell_map)) {
                TransactionSellLinesPurchaseLines::insert($purchase_sell_map);
            }
        }
    }

    /**
     * Add a mapping between purchase & sell lines for kits
     * 
     */
    public function mapPurchaseSellKit($variation_id, $sell_line_id, $quantity, $location, $check_expiry = true){
        if(empty($variation_id) || empty($quantity) || empty($location)){
            return false;
        }

        //Set flag to check for expired items during SELLING only.
        $stop_selling_expired = false;
        if ($check_expiry) {
            if ($location['enable_product_expiry'] == 1 && $location['on_product_expiry'] == 'stop_selling') {
                $stop_selling_expired = true;
            }
        }

        $transactions =
            Transaction::join('purchase_lines AS PL', 'transactions.id', '=', 'PL.transaction_id')
                ->where('transactions.business_id', $location['business_id'])
                ->where('transactions.location_id', $location['location_id'])
                ->where('transactions.warehouse_id', $location['warehouse_id'])
                ->whereIn('transactions.type', ['purchase', 'purchase_transfer', 'opening_stock', 'stock_adjustment'])
                ->where('transactions.status', 'received')
                ->whereRaw('(PL.quantity_sold + PL.quantity_adjusted + PL.quantity_returned) < PL.quantity')
                ->where('PL.variation_id', $variation_id);

        //If product expiry is enabled then check for on expiry conditions
        if ($stop_selling_expired && empty($purchase_line_id)) {
            $expiry_date = \Carbon::today()->addDays($location['stop_selling_before'])->toDateString();
            $transactions->whereRaw('PL.exp_date IS NULL OR PL.exp_date > ?', [$expiry_date]);
        }

        //Sort according to LIFO or FIFO
        if ($location['accounting_method'] == 'lifo') {
            $transactions = $transactions->orderBy('transactions.transaction_date', 'desc');
        } else {
            $transactions = $transactions->orderBy('transactions.transaction_date', 'asc');
        }

        $rows = $transactions->select(
            'PL.id as purchase_lines_id',
            DB::raw('(PL.quantity - (PL.quantity_sold + PL.quantity_adjusted +PL.quantity_returned)) AS quantity_available'),
            'PL.quantity_sold as quantity_sold',
            'PL.quantity_adjusted as quantity_adjusted',
            'PL.quantity_returned as quantity_returned',
            'transactions.invoice_no'
        )->get();

        //Iterate over the rows, assign the purchase line to sell lines.
        $qty_selling = $quantity;
        foreach ($rows as $k => $row) {
            $qty_allocated = 0;

            //Check if qty_available is more or equal
            if ($qty_selling <= $row->quantity_available) {
                $qty_allocated = $qty_selling;
                $qty_selling = 0;
            } else {
                $qty_selling = $qty_selling - $row->quantity_available;
                $qty_allocated = $row->quantity_available;
            }

            //Mapping of purchase
            $purchase_sell_map[] = [
                    'sell_line_id' => $sell_line_id,
                    'purchase_line_id' => $row->purchase_lines_id,
                    'quantity' => $qty_allocated,
                    'created_at' => \Carbon::now(),
                    'updated_at' => \Carbon::now()
                ];

            //Update purchase line
            PurchaseLine::where('id', $row->purchase_lines_id)
                ->update(['quantity_sold' => $row->quantity_sold + $qty_allocated]);
            

            if ($qty_selling == 0) {
                break;
            }
        }

        if (! ($qty_selling == 0 || is_null($qty_selling))) {
            $variation =
                Variation::where("id", $variation_id)
                    ->with("product")
                    ->first();

            $mismatch_name = $variation->product->name;
            if (!empty($variation->sub_sku)) {
                $mismatch_name .= ' ' . 'SKU: ' . $variation->sub_sku;
            }
            if (!empty($qty_selling)) {
                $mismatch_name .= ' ' . 'Quantity: ' . abs($qty_selling);
            }
            
            $mismatch_error = trans(
                "messages.purchase_sell_mismatch_exception",
                ['product' => $mismatch_name]
            );

            if ($stop_selling_expired) {
                $mismatch_error .= __('lang_v1.available_stock_expired');
            }

            $business_name = optional(Business::find($location['business_id']))->name;
            $location_name = optional(BusinessLocation::find($location['location_id']))->name;
            \Log::emergency($mismatch_error . ' Business: ' . $business_name . ' Location: ' . $location_name);
            throw new PurchaseSellMismatch($mismatch_error);
        }

        //Insert the mapping
        if (!empty($purchase_sell_map)) {
            TransactionSellLinesPurchaseLines::insert($purchase_sell_map);
        }
    }

    /**
     * F => D (Delete all mapping lines, decrease the qty sold.)
     * D => F (Call the mapPurchaseSell function)
     * F => F (Check for quantity of existing product, call mapPurchase for new products.)
     *
     * @param  string $status_before
     * @param  object $transaction
     * @param  array $business
     * @param  array $deleted_line_ids = [] //deleted sell lines ids.
     *
     * @return void
     */
    public function adjustMappingPurchaseSell(
        $status_before,
        $transaction,
        $business,
        $deleted_line_ids = []
    ) {

        if ($status_before == 'final' && $transaction->status == 'draft') {
            //Get sell lines used for the transaction.
            $sell_purchases = Transaction::join('transaction_sell_lines AS SL', 'transactions.id', '=', 'SL.transaction_id')
                    ->join('transaction_sell_lines_purchase_lines as TSP', 'SL.id', '=', 'TSP.sell_line_id')
                    ->where('transactions.id', $transaction->id)
                    ->select('TSP.purchase_line_id', 'TSP.quantity', 'TSP.id')
                    ->get()
                    ->toArray();

            //Included the deleted sell lines
            if (!empty($deleted_line_ids)) {
                $deleted_sell_purchases = TransactionSellLinesPurchaseLines::whereIn('sell_line_id', $deleted_line_ids)
                            ->select('purchase_line_id', 'quantity', 'id')
                            ->get()
                            ->toArray();

                $sell_purchases = $sell_purchases + $deleted_sell_purchases;
            }

            //TODO: Optimize the query to take our of loop.
            $sell_purchase_ids = [];
            if (!empty($sell_purchases)) {
                //Decrease the quantity sold of products
                foreach ($sell_purchases as $row) {
                    PurchaseLine::where('id', $row['purchase_line_id'])
                        ->decrement('quantity_sold', $row['quantity']);

                    $sell_purchase_ids[] = $row['id'];
                }

                //Delete the lines.
                TransactionSellLinesPurchaseLines::whereIn('id', $sell_purchase_ids)
                    ->delete();
            }
        } elseif ($status_before == 'draft' && $transaction->status == 'final') {
            $this->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');
        } elseif ($status_before == 'final' && $transaction->status == 'final') {
            //Handle deleted line
            if (!empty($deleted_line_ids)) {
                $deleted_sell_purchases = TransactionSellLinesPurchaseLines::whereIn('sell_line_id', $deleted_line_ids)
                            ->select('sell_line_id', 'quantity')
                            ->get();
                if (!empty($deleted_sell_purchases)) {
                    foreach ($deleted_sell_purchases as $value) {
                        $this->mapDecrementPurchaseQuantity($value->sell_line_id, $value->quantity);
                    }
                }
            }

            //Check for update quantity, new added rows, deleted rows.
            $sell_purchases = Transaction::join('transaction_sell_lines AS SL', 'transactions.id', '=', 'SL.transaction_id')
                    ->leftjoin('transaction_sell_lines_purchase_lines as TSP', 'SL.id', '=', 'TSP.sell_line_id')
                    ->where('transactions.id', $transaction->id)
                    ->select(
                        'TSP.purchase_line_id',
                        'TSP.quantity AS tsp_quantity',
                        'TSP.id as tsp_id',
                        'SL.*'
                    )
                    ->get();

            $deleted_sell_lines = [];
            $new_sell_lines = [];
            $processed_sell_lines = [];

            foreach ($sell_purchases as $line) {
                if (empty($line->purchase_line_id)) {
                    $new_sell_lines[] = $line;
                } else {
                    //Skip if already processed.
                    if (in_array($line->purchase_line_id, $processed_sell_lines)) {
                        continue;
                    }

                    $processed_sell_lines[] = $line->purchase_line_id;

                    $total_sold_entry = TransactionSellLinesPurchaseLines::where('sell_line_id', $line->id)
                        ->select(DB::raw('SUM(quantity) AS quantity'))
                        ->first();

                    if ($total_sold_entry->quantity != $line->quantity) {
                        if ($line->quantity > $total_sold_entry->quantity) {
                            //If quantity is increased add it to new sell lines by decreasing tsp_quantity
                            $line_temp = $line;
                            $line_temp->quantity = $line_temp->quantity - $total_sold_entry->quantity;
                            $new_sell_lines[] = $line_temp;
                        } elseif ($line->quantity < $total_sold_entry->quantity) {
                            $decrement_qty = $total_sold_entry->quantity - $line->quantity;

                            $this->mapDecrementPurchaseQuantity($line->id, $decrement_qty);
                        }
                    }
                }
            }

            //Add mapping for new sell lines and for incremented quantity
            if (!empty($new_sell_lines)) {
                $this->mapPurchaseSell($business, $new_sell_lines);
            }
        }
    }

    /**
     * Decrease the purchase quantity from
     * transaction_sell_lines_purchase_lines and purchase_lines.quantity_sold
     *
     * @param  int $sell_line_id
     * @param  int $decrement_qty
     *
     * @return void
     */
    private function mapDecrementPurchaseQuantity($sell_line_id, $decrement_qty)
    {

        $sell_purchase_line = TransactionSellLinesPurchaseLines::
                                where('sell_line_id', $sell_line_id)
                                ->orderBy('id', 'desc')
                                ->get();

        foreach ($sell_purchase_line as $row) {
            if ($row->quantity > $decrement_qty) {
                PurchaseLine::where('id', $row->purchase_line_id)
                    ->decrement('quantity_sold', $decrement_qty);

                $row->quantity = $row->quantity - $decrement_qty;
                $row->save();
                $decrement_qty = 0;
            } else {
                PurchaseLine::where('id', $row->purchase_line_id)
                    ->decrement('quantity_sold', $decrement_qty);
                $row->delete();
            }

            $decrement_qty = $decrement_qty - $row->quantity;
            if ($decrement_qty <= 0) {
                break;
            }
        }
    }

    /**
     * Decrement quantity adjusted in product line according to
     * transaction_sell_lines_purchase_lines
     * Used in delete of stock adjustment
     *
     * @param  array $line_ids
     *
     * @return boolean
     */
     public function mapPurchaseQuantityForDeleteStockAdjustment($line_ids)
    {

        if (empty($line_ids)) {
            return true;
        }

        $map_line = TransactionSellLinesPurchaseLines::whereIn('stock_adjustment_line_id', $line_ids)
                            ->orderBy('id', 'desc')
                            ->get();

        foreach ($map_line as $row) {
            PurchaseLine::where('id', $row->purchase_line_id)
                ->decrement('quantity_adjusted', $row->quantity);
        }

        //Delete the tslp line.
        TransactionSellLinesPurchaseLines::whereIn('stock_adjustment_line_id', $line_ids)
            ->delete();

        return true;
    }

    /**
     * Adjust the existing mapping between purchase & sell on edit of
     * purchase
     *
     * @param  string $before_status
     * @param  object $transaction
     * @param  object $delete_purchase_lines
     *
     * @return void
     */
    public function adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines)
    {

        if ($before_status == 'received' && $transaction->status == 'received') {
            //Check if there is some irregularities between purchase & sell and make appropiate adjustment.

            //Get all purchase line having irregularities.
            $purchase_lines = Transaction::join(
                'purchase_lines AS PL',
                'transactions.id',
                '=',
                'PL.transaction_id'
            )
                    ->join(
                        'transaction_sell_lines_purchase_lines AS TSPL',
                        'PL.id',
                        '=',
                        'TSPL.purchase_line_id'
                    )
                    ->groupBy('TSPL.purchase_line_id')
                    ->where('transactions.id', $transaction->id)
                    ->havingRaw('SUM(TSPL.quantity) > MAX(PL.quantity)')
                    ->select(['TSPL.purchase_line_id AS id',
                            DB::raw('SUM(TSPL.quantity) AS tspl_quantity'),
                            DB::raw('MAX(PL.quantity) AS pl_quantity')
                        ])
                    ->get()
                    ->toArray();
        } elseif ($before_status == 'received' && $transaction->status != 'received') {
            //Delete sell for those & add new sell or throw error.
            $purchase_lines = Transaction::join(
                'purchase_lines AS PL',
                'transactions.id',
                '=',
                'PL.transaction_id'
            )
                    ->join(
                        'transaction_sell_lines_purchase_lines AS TSPL',
                        'PL.id',
                        '=',
                        'TSPL.purchase_line_id'
                    )
                    ->groupBy('TSPL.purchase_line_id')
                    ->where('transactions.id', $transaction->id)
                    ->select(['TSPL.purchase_line_id AS id',
                        DB::raw('MAX(PL.quantity) AS pl_quantity')
                    ])
                    ->get()
                    ->toArray();
        } else {
            return true;
        }

        //Get detail of purchase lines deleted
        if (!empty($delete_purchase_lines)) {
            $purchase_lines = $delete_purchase_lines->toArray() + $purchase_lines;
        }

        //All sell lines & Stock adjustment lines.
        $sell_lines = [];
        $stock_adjustment_lines = [];
        foreach ($purchase_lines as $purchase_line) {
            $tspl_quantity = isset($purchase_line['tspl_quantity']) ? $purchase_line['tspl_quantity'] : 0;
            $pl_quantity = isset($purchase_line['pl_quantity']) ? $purchase_line['pl_quantity'] : $purchase_line['quantity'];


            $extra_sold = abs($tspl_quantity - $pl_quantity);

            //Decrease the quantity from transaction_sell_lines_purchase_lines or delete it if zero
            $tspl = TransactionSellLinesPurchaseLines::where('purchase_line_id', $purchase_line['id'])
                ->leftjoin(
                    'transaction_sell_lines AS SL',
                    'transaction_sell_lines_purchase_lines.sell_line_id',
                    '=',
                    'SL.id'
                )
                ->leftjoin(
                    'stock_adjustment_lines AS SAL',
                    'transaction_sell_lines_purchase_lines.stock_adjustment_line_id',
                    '=',
                    'SAL.id'
                )
                ->orderBy('transaction_sell_lines_purchase_lines.id', 'desc')
                ->select(['SL.product_id AS sell_product_id',
                        'SL.variation_id AS sell_variation_id',
                        'SL.id AS sell_line_id',
                        'SAL.product_id AS adjust_product_id',
                        'SAL.variation_id AS adjust_variation_id',
                        'SAL.id AS adjust_line_id',
                        'transaction_sell_lines_purchase_lines.quantity',
                        'transaction_sell_lines_purchase_lines.purchase_line_id', 'transaction_sell_lines_purchase_lines.id as tslpl_id'])
                ->get();

            foreach ($tspl as $row) {
                if ($row->quantity <= $extra_sold) {
                    if (!empty($row->sell_line_id)) {
                        $sell_lines[] = (object)['id' => $row->sell_line_id,
                                'quantity' => $row->quantity,
                                'product_id' => $row->sell_product_id,
                                'variation_id' => $row->sell_variation_id,
                            ];
                        PurchaseLine::where('id', $row->purchase_line_id)
                            ->decrement('quantity_sold', $row->quantity);
                    } else {
                        $stock_adjustment_lines[] =
                            (object)['id' => $row->adjust_line_id,
                                'quantity' => $row->quantity,
                                'product_id' => $row->adjust_product_id,
                                'variation_id' => $row->adjust_variation_id,
                            ];
                        PurchaseLine::where('id', $row->purchase_line_id)
                            ->decrement('quantity_adjusted', $row->quantity);
                    }

                    $extra_sold = $extra_sold - $row->quantity;
                    TransactionSellLinesPurchaseLines::where('id', $row->tslpl_id)->delete();
                } else {
                    if (!empty($row->sell_line_id)) {
                        $sell_lines[] = (object)['id' => $row->sell_line_id,
                                'quantity' => $extra_sold,
                                'product_id' => $row->sell_product_id,
                                'variation_id' => $row->sell_variation_id,
                            ];
                        PurchaseLine::where('id', $row->purchase_line_id)
                            ->decrement('quantity_sold', $extra_sold);
                    } else {
                        $stock_adjustment_lines[] =
                            (object)['id' => $row->adjust_line_id,
                                'quantity' => $extra_sold,
                                'product_id' => $row->adjust_product_id,
                                'variation_id' => $row->adjust_variation_id,
                            ];

                        PurchaseLine::where('id', $row->purchase_line_id)
                            ->decrement('quantity_adjusted', $extra_sold);
                    }

                    TransactionSellLinesPurchaseLines::where('id', $row->tslpl_id)->update(['quantity' => $row->quantity - $extra_sold]);
                    
                    $extra_sold = 0;
                }

                if ($extra_sold == 0) {
                    break;
                }
            }
        }

        $business = Business::find($transaction->business_id)->toArray();
        $business['location_id'] = $transaction->location_id;

        //Allocate the sold lines to purchases.
        if (!empty($sell_lines)) {
            $sell_lines = (object)$sell_lines;
            $this->mapPurchaseSell($business, $sell_lines, 'purchase');
        }

        //Allocate the stock adjustment lines to purchases.
        if (!empty($stock_adjustment_lines)) {
            $stock_adjustment_lines = (object)$stock_adjustment_lines;
            $this->mapPurchaseSell($business, $stock_adjustment_lines, 'stock_adjustment');
        }
    }

    /**
     * Check if transaction can be edited based on business     transaction_edit_days
     *
     * @param  int/object $transaction
     * @param  int $edit_duration
     *
     * @return boolean
     */
    public function canBeEdited($transaction, $edit_duration)
    {

        if (!is_object($transaction)) {
            $transaction = Transaction::find($transaction);
        }
        if (empty($transaction)) {
            return false;
        }

        $date = \Carbon::parse($transaction->transaction_date)
                    ->addDays($edit_duration);

        $today = today();

        if ($date->gte($today)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Calculates total stock on the given date
     *
     * @param int $business_id
     * @param string $date
     * @param int $location_id
     * @param boolean $is_opening = false
     *
     * @return float
     */
    public function getOpeningClosingStock($business_id, $date, $location_id, $is_opening = false)
    {

        $query = PurchaseLine::join(
            'transactions as purchase',
            'purchase_lines.transaction_id',
            '=',
            'purchase.id'
        )
                    ->where('purchase.business_id', $business_id);

        //If opening
        if ($is_opening) {
            $next_day = \Carbon::createFromFormat('Y-m-d', $date)->addDay()->format('Y-m-d');
            
            $query->where(function ($query) use ($date, $next_day) {
                $query->whereRaw("date(transaction_date) <= '$date'")
                    ->orWhereRaw("date(transaction_date) = '$next_day' AND type='opening_stock' ");
            });
        } else {
            $query->whereRaw("date(transaction_date) <= '$date'");
        }
                    
        $query->select(
            DB::raw("SUM(
                            (purchase_lines.quantity -
                            (SELECT COALESCE(SUM(tspl.quantity - tspl.qty_returned), 0) FROM 
                            transaction_sell_lines_purchase_lines AS tspl
                            JOIN transaction_sell_lines as tsl ON 
                            tspl.sell_line_id=tsl.id 
                            JOIN transactions as sale ON 
                            tsl.transaction_id=sale.id 
                            WHERE tspl.purchase_line_id = purchase_lines.id AND 
                            date(sale.transaction_date) <= '$date') ) * (purchase_lines.purchase_price + 
                            COALESCE(purchase_lines.item_tax, 0))
                        ) as stock")
        );

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('purchase.location_id', $permitted_locations);
        }

        if (!empty($location_id)) {
            $query->where('purchase.location_id', $location_id);
        }

        $details = $query->first();
        return $details->stock;
    }

    /**
     * Calculates total discount on the given date
     *
     * @param int $business_id
     * @param string $transaction_type
     * @param string $start_date
     * @param string $end_date
     * @param int $location_id = null
     *
     * @return float
     */
    public function getTotalDiscounts($business_id, $transaction_type, $start_date, $end_date, $location_id = null)
    {

        $query = Transaction::where('business_id', $business_id)
                    ->where('type', $transaction_type);

        //Date filter
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        //Location filter
        if (!empty($location_id)) {
            $query->where('location_id', $location_id);
        }

        $query->select(
            DB::raw("SUM(IF(discount_type = 'percentage', COALESCE(discount_amount, 0)*total_before_tax/100, COALESCE(discount_amount, 0))) as discount")
        );

        $details = $query->first();
        return $details->discount;
    }

    /**
     * Calculates total expense for a business
     *
     * @param  int $business_id
     * @param  string $start_date
     * @param  string $end_date
     * @param  int $location_id
     *
     * @return boolean
     */
    public function getTotalExpense($business_id, $start_date = null, $end_date = null, $location_id = null)
    {

        //Get Total Expense
        $q = Transaction::where('business_id', $business_id)
                        ->where('type', 'expense');

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $q->whereIn('location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $q->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (!empty($location_id)) {
            $q->where('location_id', $location_id);
        }
        $expenses = $q->get();
        $total_expense = $expenses->sum('final_total');

        return $total_expense;
    }

    /**
     * Calculates total stock adjustment for a business
     *
     * @param  int $business_id
     * @param  string $start_date
     * @param  string $end_date
     * @param  int $location_id
     *
     * @return obj
     */
    public function getTotalStockAdjustment($business_id, $start_date = null, $end_date = null, $location_id = null)
    {

        //Get Total Expense
        $q = Transaction::where('business_id', $business_id)
                        ->where('type', 'stock_adjustment')
                        ->select(
                            DB::raw("SUM(final_total) as total_adjustment"),
                            DB::raw("SUM(total_amount_recovered) as total_recovered")
                        );

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $q->whereIn('location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $q->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (!empty($location_id)) {
            $q->where('location_id', $location_id);
        }
        
        $total_adjustment = $q->first();

        return $total_adjustment;
    }

    /**
     * Gives the total sell commission for a commission agent within the date range passed
     *
     * @param int $business_id
     * @param string $start_date
     * @param string $end_date
     * @param int $location_id
     * @param int $commission_agent
     *
     * @return array
     */
    public function getTotalSellCommission($business_id, $start_date = null, $end_date = null, $location_id = null, $commission_agent = null)
    {
        $query = Transaction::where('business_id', $business_id)
                        ->where('type', 'sell')
                        ->where('status', 'final')
                        ->select('final_total');

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('location_id', $permitted_locations);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        //Filter by the location
        if (!empty($location_id)) {
            $query->where('location_id', $location_id);
        }

        if (!empty($commission_agent)) {
            $query->where('commission_agent', $commission_agent);
        }

        $sell_details = $query->get();

        $output['total_sales_with_commission'] = $sell_details->sum('final_total');

        return $output;
    }

    /**
     * Calculates total stock adjustment for a business
     *
     * @param  int $business_id
     * @param  string $start_date
     * @param  string $end_date
     * @param  int $location_id
     *
     * @return boolean
     */
    public function getTotalTransferShippingCharges($business_id, $start_date = null, $end_date = null, $location_id = null)
    {

        //Get Total Transfer Shipping charge
        $q = Transaction::where('business_id', $business_id)
                        ->where('type', 'sell_transfer')
                        ->select(DB::raw("SUM(shipping_charges) as total_shipping_charges"));

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $q->whereIn('location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $q->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (!empty($location_id)) {
            $q->where('location_id', $location_id);
        }
        
        return $q->first()->total_shipping_charges;
    }

    /**
     * Add Sell transaction
     *
     * @param int $business_id
     * @param array $input
     * @param float $invoice_total
     * @param int $user_id
     *
     * @return boolean
     */
    public function createSellReturnTransaction($business_id, $input, $invoice_total, $user_id)
    {
        $transaction = Transaction::create([
            'business_id' => $business_id,
            'location_id' => $input['location_id'],
            'type' => 'sell_return',
            'status' => 'final',
            'contact_id' => $input['contact_id'],
            'customer_group_id' => $input['customer_group_id'],
            'ref_no' => $input['ref_no'],
            'total_before_tax' => $invoice_total['total_before_tax'],
            'transaction_date' => $input['transaction_date'],
            'tax_id' => null,
            'discount_type' => $input['discount_type'],
            'discount_amount' => $this->num_uf($input['discount_amount']),
            'tax_amount' => $invoice_total['tax'],
            'final_total' => $this->num_uf($input['final_total']),
            'additional_notes' => !empty($input['additional_notes']) ? $input['additional_notes'] : null,
            'created_by' => $user_id,
            'is_quotation' => isset($input['is_quotation']) ? $input['is_quotation'] : 0
        ]);

        return $transaction;
    }

    /**
     * Get document type print format from a transaction
     * @param int $transaction_id
     * @return string
     */
    public function getDocumentTypePrintFormat($transaction_id){
        if(empty($transaction_id)) return "";

        $transaction = Transaction::find($transaction_id);
        if(empty($transaction)) return "";

        $print_format = DocumentType::find($transaction->document_types_id);
        
        if(!empty($print_format)){
            return $print_format->print_format;
        } else{
            return "";
        }
    }

    public function groupTaxDetails($tax, $amount)
    {
        if (!is_object($tax)) {
            $tax = TaxRate::find($tax);
        }

        if (!empty($tax)) {
            $sub_taxes = $tax->sub_taxes;

            $sum = $tax->sub_taxes->sum('amount');

            $details = [];
            foreach ($sub_taxes as $sub_tax) {
                $details[] = [
                        'id' => $sub_tax->id,
                        'name' => $sub_tax->name,
                        'amount' => $sub_tax->amount,
                        'calculated_tax' => ($amount / $sum) * $sub_tax->amount,
                    ];
            }

            return $details;
        } else {
            return [];
        }
    }

    public function sumGroupTaxDetails($group_tax_details)
    {
        $output = [];

        foreach ($group_tax_details as $group_tax_detail) {
            if (!isset($output[$group_tax_detail['name']])) {
                $output[$group_tax_detail['name']] = 0;
            }
            $output[$group_tax_detail['name']] += $group_tax_detail['calculated_tax'];
        }

        return $output;
    }

    /**
     * Retrieves all available lot numbers of a product from variation id
     *
     * @param  int $variation_id
     * @param  int $business_id
     * @param  int $location_id
     *
     * @return boolean
     */
    public function getLotNumbersFromVariation($variation_id, $business_id, $location_id, $exclude_empty_lot = false)
    {

        $query = PurchaseLine::join(
            'transactions as T',
            'purchase_lines.transaction_id',
            '=',
            'T.id'
        )
            ->where('T.business_id', $business_id)
            ->where('T.location_id', $location_id)
            ->where('purchase_lines.variation_id', $variation_id);

        //If expiry is disabled
        if (request()->session()->get('business.enable_product_expiry') == 0) {
            $query->whereNotNull('purchase_lines.lot_number');
        }
        if ($exclude_empty_lot) {
            $query->whereRaw('(purchase_lines.quantity_sold + purchase_lines.quantity_adjusted + purchase_lines.quantity_returned) < purchase_lines.quantity');
        } else {
            $query->whereRaw('(purchase_lines.quantity_sold + purchase_lines.quantity_adjusted + purchase_lines.quantity_returned) <= purchase_lines.quantity');
        }

        $purchase_lines = $query->select('purchase_lines.id as purchase_line_id', 'lot_number', 'purchase_lines.exp_date as exp_date', DB::raw('(purchase_lines.quantity - (purchase_lines.quantity_sold + purchase_lines.quantity_adjusted + purchase_lines.quantity_returned)) AS qty_available'))->get();
        return $purchase_lines;
    }

    //Funcion NUEVA editada para Traslados
    public function getLotNumbersFromVariationTransfer($variation_id, $business_id, $warehouse_id, $exclude_empty_lot = false)
    {

        $query = PurchaseLine::join('transactions as T', 'purchase_lines.transaction_id', 'T.id')
                            ->where('T.business_id', $business_id)
                            ->where('T.warehouse_id', $warehouse_id)
                            ->where('purchase_lines.variation_id', $variation_id);

        //If expiry is disabled
        if (request()->session()->get('business.enable_product_expiry') == 0) {
            $query->whereNotNull('purchase_lines.lot_number');
        }
        if ($exclude_empty_lot) {
            $query->whereRaw('(purchase_lines.quantity_sold + purchase_lines.quantity_adjusted + purchase_lines.quantity_returned) < purchase_lines.quantity');
        } else {
            $query->whereRaw('(purchase_lines.quantity_sold + purchase_lines.quantity_adjusted + purchase_lines.quantity_returned) <= purchase_lines.quantity');
        }

        $purchase_lines = $query->select('purchase_lines.id as purchase_line_id', 'lot_number', 'purchase_lines.exp_date as exp_date', DB::raw('(purchase_lines.quantity - (purchase_lines.quantity_sold + purchase_lines.quantity_adjusted + purchase_lines.quantity_returned)) AS qty_available'))->get();
        return $purchase_lines;
    }

    /**
     * Checks if credit limit of a customer is exceeded
     *
     * @param  array $input
     * @param  int $exclude_transaction_id (For update sell)
     *
     * @return mixed
     * if exceeded returns credit_limit else false
     */
    public function isCustomerCreditLimitExeeded($input, $exclude_transaction_id = null) {
        //$credit_limit = Contact::find($input['contact_id'])->credit_limit;
        $credit_limit = Customer::find($input['customer_id'])->credit_limit;

        if ($credit_limit == null) {
            return false;
        }

        $query = Customer::where('customers.id', $input['customer_id'])
                ->join('transactions AS t', 'customers.id', '=', 't.customer_id')
                ->where('t.status', 'final');

        //Exclude transaction id if update transaction
        if (!empty($exclude_transaction_id)) {
            $query->where('t.id', '!=', $exclude_transaction_id);
        }
                                    
        $credit_details = $query->select(
            DB::raw("SUM(IF(t.type = 'sell', final_total, 0)) as total_invoice"),
            DB::raw("SUM(IF(t.type = 'sell', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_paid")
        )->first();

        $total_invoice = !empty($credit_details->total_invoice) ? $credit_details->total_invoice : 0;
        $invoice_paid = !empty($credit_details->invoice_paid) ? $credit_details->invoice_paid : 0;

        $final_total = $this->num_uf($input['final_total']);
        $curr_total_payment = 0;
        if(!empty($input['payment'])){
            foreach ($input['payment'] as $payment) {
                $curr_total_payment += $this->num_uf($payment['amount']);
            }
        }
        $curr_due = $final_total - $curr_total_payment;

        $total_due = $total_invoice - $invoice_paid + $curr_due;
        if ($total_due <= $credit_limit) {
            return false;
        }

        //return $credit_limit;
        return $total_due;
    }

    /**
     * Creates a new opening balance transaction for a contact
     *
     * @param  int $business_id
     * @param  int $contact_id
     * @param  int $amount
     *
     * @return void
     */
    public function createOpeningBalanceTransaction($business_id, $contact_id, $amount, $customer_id = null)
    {
        $business_location = BusinessLocation::where('business_id', $business_id)
                                                        ->first();
        $final_amount = $this->num_uf($amount);
        $ob_data = [
                    'business_id' => $business_id,
                    'location_id' => $business_location->id,
                    'type' => 'opening_balance',
                    'status' => 'final',
                    'payment_status' => 'due',
                    'contact_id' => $contact_id,
                    'customer_id' => $customer_id,
                    'transaction_date' => \Carbon::now(),
                    'total_before_tax' => $final_amount,
                    'final_total' => $final_amount,
                    'created_by' => request()->session()->get('user.id')
                ];
        //Update reference count
        $ob_ref_count = $this->setAndGetReferenceCount('opening_balance');
        //Generate reference number
        $ob_data['ref_no'] = $this->generateReferenceNumber('opening_balance', $ob_ref_count);
        //Create opening balance transaction
        Transaction::create($ob_data);
    }

    /**
     * Updates quantity sold in purchase line for sell return
     *
     * @param  obj $sell_line
     * @param  decimal $new_quantity
     * @param  decimal $old_quantity
     *
     * @return void
     */
    public function updateQuantitySoldFromSellLine($sell_line, $new_quantity, $old_quantity)
    {
        $qty_difference = $this->num_uf($new_quantity) - $this->num_uf($old_quantity);

        if ($qty_difference != 0) {
            $qty_left_to_update = $qty_difference;
            $sell_line_purchase_lines = TransactionSellLinesPurchaseLines::where('sell_line_id', $sell_line->id)->get();
            
            //Return from each purchase line
            foreach ($sell_line_purchase_lines as $tslpl) {
                //If differnce is +ve decrease quantity sold
                if ($qty_difference > 0) {
                    if ($tslpl->qty_returned < $tslpl->quantity) {
                        //Quantity that can be returned from sell line purchase line
                        $tspl_qty_left_to_return = $tslpl->quantity - $tslpl->qty_returned;

                        $purchase_line = PurchaseLine::find($tslpl->purchase_line_id);
                        if ($qty_left_to_update <= $tspl_qty_left_to_return) {
                            $purchase_line->quantity_sold -= $qty_left_to_update;
                            $purchase_line->save();

                            $tslpl->qty_returned += $qty_left_to_update;
                            $tslpl->save();
                            break;
                        } else {
                            $purchase_line->quantity_sold -= $tspl_qty_left_to_return;
                            $purchase_line->save();

                            $tslpl->qty_returned += $tspl_qty_left_to_return;
                            $tslpl->save();
                            $qty_left_to_update -= $tspl_qty_left_to_return;
                        }
                    }
                } //If differnce is -ve increase quantity sold
                elseif ($qty_difference < 0) {
                    $purchase_line = PurchaseLine::find($tslpl->purchase_line_id);
                    $tspl_qty_to_return = $tslpl->qty_returned + $qty_left_to_update;
                    if ($tspl_qty_to_return >= 0) {
                        $purchase_line->quantity_sold -= $qty_left_to_update;
                        $purchase_line->save();

                        $tslpl->qty_returned += $qty_left_to_update;
                        $tslpl->save();
                        break;
                    } else {
                        $purchase_line->quantity_sold += $tslpl->quantity;
                        $purchase_line->save();

                        $tslpl->qty_returned = 0;
                        $tslpl->save();
                        $qty_left_to_update += $tslpl->quantity;
                    }
                }
            }
        }
    }

    /**
     * Check if return exist for a particular purchase or sell
     * @param id $transacion_id
     *
     * @return boolean
     */
    public function isReturnExist($transacion_id)
    {
        return Transaction::where('return_parent_id', $transacion_id)->exists();
    }

    public function getTotalPurchaseReturn($business_id, $location_id, $start_date = null, $end_date = null)
    {
        $query = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase_return')
                        ->select(
                            'final_total',
                            'total_before_tax'
                        )
                        ->groupBy('transactions.id');
        
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
        }

        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }

        $purchase_return_details = $query->get();

        $output['total_purchase_return_inc_tax'] = $purchase_return_details->sum('final_total');
        $output['total_purchase_return_exc_tax'] = $purchase_return_details->sum('total_before_tax');
        
        return $output;
    }

    public function getTotalSellReturn($business_id, $location_id, $start_date = null, $end_date = null)
    {
        $query = Transaction::where('business_id', $business_id)
                        ->where('type', 'sell_return')
                        ->select(
                            'final_total',
                            'total_before_tax'
                        )
                        ->groupBy('transactions.id');
        
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
        }

        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }

        $sell_return_details = $query->get();

        $output['total_sell_return_inc_tax'] = $sell_return_details->sum('final_total');
        $output['total_sell_return_exc_tax'] = $sell_return_details->sum('total_before_tax');
        
        return $output;
    }

    /**
     * Check if lot number is used in any sell
     * @param obj $transaction
     *
     * @return boolean
     */
    public function isLotUsed($transaction)
    {
        foreach ($transaction->purchase_lines as $purchase_line) {
            $exists = TransactionSellLine::where('lot_no_line_id', $purchase_line->id)->exists();
            if($exists) {
                return true;
            } 
        }

        return false;
    }

    /**
     * Return discount value from a amount given
     * @param double $amount
     * @param string $discount_type
     * @param double $discount_amount
     * 
     * @return double
     */
    public function getDiscountValue($amount, $discount_type, $discount_amount){
        if(empty($discount_type) || empty($discount_amount)){
            return 0;
        }

        if($discount_type == 'fixed'){
            return $discount_amount;

        } else if($discount_type == 'percentage'){
            return ($amount * ($discount_amount / 100));

        } else {
            return 0;
        }
    }

    /**
     * Get customer information
     * @param int $customer_id
     * @return Customer
     */
    public function getCustomerInfo($customer_id){
        if(empty($customer_id)){
            return null;
        }

        $customer = Customer::leftJoin("countries as cnt", "customers.country_id", "cnt.id")
            ->leftJoin("states as st", "customers.state_id", "st.id")
            ->leftJoin("cities as ct", "customers.city_id", "ct.id")
            ->where("customers.id", $customer_id)
            ->select(
                "cnt.name as country", "st.name as state", "ct.name as city",
                "customers.*"
            )->first();

        return $customer;
    }

    /**
     * Create or update kardex output lines.
     * 
     * @param  \App\MovementType  $movement_type
     * @param  \App\Transaction  $transaction
     * @param  string  $reference
     * @param  \App\TransactionSellLine  $lines
     * @param  \App\TransactionSellLine  $lines_before
     * @param  int  $calculate_balance
     * @return void
     */
    public function createOrUpdateOutputLines(
        $movement_type, $transaction, $reference, $lines,
        $lines_before = null, $calculate_balance = null, $old_stock_adjustment = false)
    {
        foreach ($lines as $line) {
            $product = Product::find($line->product_id);

            // Check if it's a kit
            if ($product->clasification == 'kits') {
                $childrens = KitHasProduct::where('parent_id', $product->id)->get();
                
                foreach ($childrens as $item) {
                    $variation = Variation::where('id', $item->children_id)->first();

                    $prod = Product::find($variation->product_id);

                    // Choose quantity
                    if ($movement_type->name == 'purchase_return') {
                        $quantity = $line->quantity_returned;
                    } else {
                        $quantity = $line->quantity;
                    }

                    if ($calculate_balance === 1) {
                        $balance = 1;

                    } else {
                        $balance = null;
                    }

                    $this->saveKardexLine(
                        $movement_type,
                        $transaction,
                        $prod,
                        $quantity * $item->quantity,
                        $variation->default_purchase_price,
                        $quantity * $item->quantity * $variation->default_purchase_price,
                        $item->children_id,
                        $reference,
                        $line->id,
                        $balance
                    );
                }

            } else {
                // Choose quantity
                if ($movement_type->name == 'purchase_return') {
                    $quantity = $line->quantity_returned;
                } else {
                    $quantity = $line->quantity;
                }

                if ($calculate_balance === 1) {
                    $balance = 1;

                } else {
                    $balance = null;
                }

                if ($old_stock_adjustment) {
                    $unit_cost = $line->unit_price;
                } else {
                    $unit_cost = $line->unit_cost_exc_tax;
                }

                $this->saveKardexLine(
                    $movement_type,
                    $transaction,
                    $product,
                    $quantity,
                    $unit_cost,
                    $quantity * $unit_cost,
                    $line->variation_id,
                    $reference,
                    $line->id,
                    $balance
                );
            }
        }

        // Delete kardex lines whose sell lines no longer exist
        if ((! is_null($lines_before)) || (! empty($lines_before))) {
            
            foreach ($lines_before as $line_before) {
                // Check that the sell line still exists
                $line_exist = $lines->where('id', $line_before->id)->first();
                
                if (empty($line_exist)) {
                    $product = Product::find($line_before->product_id);

                    // Check if it's a kit
                    if ($product->clasification == 'kits') {
                        $childrens = KitHasProduct::where('parent_id', $product->id)->get();
                        
                        foreach ($childrens as $item) {
                            $variation = Variation::where('id', $item->children_id)->first();

                            $prod = Product::find($variation->product_id);

                            $this->deleteKardexLine(
                                $transaction->location_id,
                                $transaction->warehouse_id,
                                $prod,
                                $transaction->id,
                                $item->children_id
                            );
                        }

                    } else {
                        $this->deleteKardexLine(
                            $transaction->location_id,
                            $transaction->warehouse_id,
                            $product,
                            $transaction->id,
                            $line_before->variation_id
                        );
                    }
                }
            }
        }
    }

    /**
     * Create or update kardex input lines.
     * 
     * @param  \App\MovementType  $movement_type
     * @param  \App\Transaction  $transaction
     * @param  string  $reference
     * @param  \App\PurchaseLine  $lines
     * @param  \App\PurchaseLine  $lines_before
     * @param  int  $calculate_balance
     * @return void
     */
    public function createOrUpdateInputLines(
        $movement_type, $transaction, $reference, $lines,
        $lines_before = null, $calculate_balance = null)
    {
        foreach ($lines as $line) {
            $product = Product::find($line->product_id);

            // Check if it's a kit
            if ($product->clasification == 'kits') {
                $childrens = KitHasProduct::where('parent_id', $product->id)->get();
                
                foreach ($childrens as $item) {
                    $variation = Variation::where('id', $item->children_id)->first();

                    $prod = Product::find($variation->product_id);

                    // Choose quantity
                    if ($movement_type->name == 'sell_return') {
                        $quantity = $line->quantity_returned;
                        $cost = $line->unit_cost_exc_tax;

                    } else {
                        $quantity = $line->quantity;
                        $cost = $line->purchase_price;
                    }

                    if ($calculate_balance === 1) {
                        $balance = 1;

                    } else {
                        $balance = null;
                    }

                    $this->saveKardexLine(
                        $movement_type,
                        $transaction,
                        $prod,
                        $quantity * $item->quantity,
                        $line->purchase_price,
                        $quantity * $item->quantity * $line->purchase_price,
                        $item->children_id,
                        $reference,
                        $line->id,
                        $balance
                    );
                }

            } else {
                // Choose quantity
                if ($movement_type->name == 'sell_return') {
                    $quantity = $line->quantity_returned;
                    $cost = $line->unit_cost_exc_tax;

                } else {
                    $quantity = $line->quantity;
                    $cost = $line->purchase_price;
                }

                if ($calculate_balance === 1) {
                    $balance = 1;

                } else {
                    $balance = null;
                }

                $this->saveKardexLine(
                    $movement_type,
                    $transaction,
                    $product,
                    $quantity,
                    $cost,
                    $quantity * $cost,
                    $line->variation_id,
                    $reference,
                    $line->id,
                    $balance
                );
            }
        }

        // Delete kardex lines whose purchase lines no longer exist
        if ((! is_null($lines_before)) || (! empty($lines_before))) {
            
            foreach ($lines_before as $line_before) {
                // Check that the purchase line still exists
                $line_exist = $lines->where('id', $line_before->id)->first();
                
                if (empty($line_exist)) {
                    $product = Product::find($line_before->product_id);

                    $this->deleteKardexLine(
                        $transaction->location_id,
                        $transaction->warehouse_id,
                        $product,
                        $transaction->id,
                        $line_before->variation_id
                    );
                }
            }
        }
    }

    /**
     * Create or update a kardex line.
     * 
     * @param  \App\MovementType  $movement_type_id
     * @param  \App\Transaction  $transaction
     * @param  \App\Product  $product
     * @param  float  $quantity
     * @param  float  $unit_cost
     * @param  float  $total_cost
     * @param  int  $variation_id
     * @param  string  $reference
     * @param  int  $line_id
     * @param  float  $balance
     * @return void
     */
    public function saveKardexLine(
        $movement_type, $transaction, $product, $quantity, $unit_cost,
        $total_cost, $variation_id, $reference, $line_id, $balance = null)
    {
        $flag = false;

        if (config('app.business') == 'optics') {
            if (($product->clasification == 'product' || $product->clasification == 'material') &&
                ($product->enable_stock == 1)) {
                $flag = true;
            }

        } else {
            if ($product->clasification == 'product' && $product->enable_stock == 1) {
                $flag = true;
            }
        }
        
        // Check if the product has stock control
        if ($flag) {
            // Auxiliary data
            $user_id = $transaction->created_by;
            $business_id = $transaction->business_id;
            $date_time = $transaction->transaction_date;

            // Add time when transaction_date ends at 00:00:00
            $hour = substr($date_time, 11, 18);

            if ($hour == '00:00:00' || $hour == '') {
                $date_time = substr($date_time, 0, 10) . ' ' . substr($transaction->created_at, 11, 18);
            }

            $vld = VariationLocationDetails::where('variation_id', $variation_id)
                ->where('location_id', $transaction->location_id)
                ->where('warehouse_id', $transaction->warehouse_id)
                ->first();
    
            // Kardex data
            $data = [
                'movement_type_id' => $movement_type->id,
                'business_location_id' => $transaction->location_id,
                'warehouse_id' => $transaction->warehouse_id,
                'product_id' => $product->id,
                'transaction_id' => $transaction->id,
                'reference' => $reference,
                'date_time' => $date_time,
                'business_id' => $business_id,
                'variation_id' => $variation_id,
                'line_reference' => $line_id
            ];

            // Check if it input or output
            if ($movement_type->type == 'input') {
                $data['inputs_quantity'] = $this->num_uf($quantity);
                $data['unit_cost_inputs'] = $this->num_uf($unit_cost);
                $data['total_cost_inputs'] = $this->num_uf($total_cost);
            } else {
                $data['outputs_quantity'] = $this->num_uf($quantity);
                $data['unit_cost_outputs'] = $this->num_uf($unit_cost);
                $data['total_cost_outputs'] = $this->num_uf($total_cost);
            }

            if ($movement_type->name == 'opening_stock') {
                $data['balance'] = $this->num_uf($quantity);
            } else if (! is_null($balance)) {
                $data['balance'] = $this->num_uf($balance);
            } else {
                $data['balance'] = $this->num_uf($vld->qty_available);
            }
    
            // Get kardex line if it exists
            $kardex = Kardex::where('business_location_id', $transaction->location_id)
                ->where('warehouse_id', $transaction->warehouse_id)
                ->where('variation_id', $variation_id)
                ->where('transaction_id', $transaction->id)
                ->where('line_reference', $line_id)
                ->first();
    
            $data['updated_by'] = $user_id;

            // Check if kardex line is set
            if (! empty($kardex)) {
                $kardex->fill($data);

            } else {
                $data['created_by'] = $user_id;
                $kardex = new Kardex($data);
            }
    
            $kardex->save();

            if (is_null($balance)) {
                $this->recalculateBalances($kardex);
            }

            // \Log::info('Transaction: ' . $transaction->id);
        }
    }

    /**
     * Delete a kardex line.
     * 
     * @param  int  $location_id
     * @param  int  $warehouse_id
     * @param  \App\Product  $product
     * @param  int  $transaction_id
     * @param  int  $variation_id
     * @return void
     */
    public function deleteKardexLine($location_id, $warehouse_id, $product, $transaction_id, $variation_id)
    {
        $flag = false;

        if (config('app.business') == 'optics') {
            if (($product->clasification == 'product' || $product->clasification == 'material') &&
                ($product->enable_stock == 1)) {
                $flag = true;
            }

        } else {
            if ($product->clasification == 'product' && $product->enable_stock == 1) {
                $flag = true;
            }
        }

        // Check if the product has stock control
        if ($flag) {
            // Get kardex line if it exists
            $kardex = Kardex::where('business_location_id', $location_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('variation_id', $variation_id)
                ->where('transaction_id', $transaction_id)
                ->first();
            
            // Check if output line is set
            if (! empty($kardex)) {
                $kardex->delete();
            }
        }
    }

    /**
     * Delete kardex line by transaction.
     * 
     * @param  int  $id
     * @param  boolean  $is_physical_inventory
     * @return void
     */
    public function deleteKardexByTransaction($id, $is_physical_inventory = false)
    {
        if (config('app.business') == 'optics') {
            // Do not take kardex lines of the lab order type
            $lines = Kardex::join('movement_types as mt', 'kardexes.movement_type_id', 'mt.id')
                ->where('mt.name', '!=', 'lab_order')
                ->where('kardexes.transaction_id', $id)
                ->select('kardexes.*')
                ->get();

        } else {
            if ($is_physical_inventory) {
                $lines = Kardex::where('physical_inventory_id', $id)->get();

            } else {
                $lines = Kardex::where('transaction_id', $id)->get();
            }
        }

        foreach ($lines as $line) {
            $line->inputs_quantity = 0;
            $line->outputs_quantity = 0;
            $line->balance = 0;
            $line->save();

            $this->recalculateBalances($line);

            $line->delete();
        }
    }

    /**
     * Calculate balance for kardex generation.
     * 
     * @param  \App\Product  $product
     * @param  int  $variation_id
     * @param  float  $quantity
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  int  $warehouse_id
     * @param  Date  $date_time
     * @param  int  $kardex_id
     * @return float
     */
    public function calculateBalance(
        $product, $variation_id, $quantity, $business_id, $location_id, $warehouse_id,
        $date_time, $kardex_id = null)
    {
        $balance = null;

        $flag = false;

        if (config('app.business') == 'optics') {
            if (($product->clasification == 'product' || $product->clasification == 'material') &&
                ($product->enable_stock == 1)) {
                $flag = true;
            }

        } else {
            if ($product->clasification == 'product' && $product->enable_stock == 1) {
                $flag = true;
            }
        }

        if ($flag) {
            // Get movement types
            $input_types = MovementType::where('type', 'input')
                // ->where('business_id', $business_id)
                ->pluck('id')
                ->toArray();
    
            $output_types = MovementType::where('type', 'output')
                // ->where('business_id', $business_id)
                ->pluck('id')
                ->toArray();

            // Get quantities
            $input_sum = Kardex::where('business_location_id', $location_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('variation_id', $variation_id)
                ->where('date_time', '<=', $date_time)
                ->whereIn('movement_type_id', $input_types);
                
            $output_sum = Kardex::where('business_location_id', $location_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('variation_id', $variation_id)
                ->where('date_time', '<=', $date_time)
                ->whereIn('movement_type_id', $output_types);
            
            // If there is more than one record with the same time
            if (! is_null($kardex_id)) {
                $input_sum = $input_sum->where('id', '<=', $kardex_id);
                $output_sum = $output_sum->where('id', '<=', $kardex_id);
            }

            $input_sum = $input_sum->sum('inputs_quantity');
            $output_sum = $output_sum->sum('outputs_quantity');

            $balance = $input_sum - $output_sum + $quantity;
        }

        return $balance;
    }

    /**
     * Recalculate balance from last record saved.
     * 
     * @param  \App\Kardex  $kardex
     * @return void
     */
    public function recalculateBalances($kardex)
    {
        $lines = Kardex::where('business_location_id', $kardex->business_location_id)
            ->where('warehouse_id', $kardex->warehouse_id)
            ->where('variation_id', $kardex->variation_id)
            ->where('date_time', '>=', $kardex->date_time)
            ->get();

        foreach ($lines as $line) {
            $product = Product::find($kardex->product_id);

            // Check if there is more than one record with the same time
            $equal_time = Kardex::where('business_location_id', $line->business_location_id)
                ->where('warehouse_id', $line->warehouse_id)
                ->where('variation_id', $line->variation_id)
                ->where('date_time', $line->date_time)
                ->count();

            if ($equal_time > 1) {
                $kardex_id = $line->id;
            } else {
                $kardex_id = null;
            }

            $line->balance = $this->calculateBalance(
                $product,
                $line->variation_id,
                0,
                $line->business_id,
                $line->business_location_id,
                $line->warehouse_id,
                $line->date_time,
                $kardex_id
            );

            $line->save();
        }
    }

    /**
     * update the payment methods, if the method is changed then it is deleted and the new fields are saved
     * 
     * @param int/object $id
     * @param  object  $payment
     * 
     * @return void
     */
    public function updatePaymentsMethod($id, $payment)
    {
        $transaction_payment = TransactionPayment::find($id);

        $payment_data = [
            'amount' => $this->num_uf($payment->amount),
            'method' => $payment->method,
            // 'is_return' => isset($payment->is_return) ? $payment->is_return : 0,
            'card_holder_name' => isset($payment->card_holder_name) ? $payment->card_holder_name : null,
            'card_authotization_number' => isset($payment->card_authotization_number) ? $payment->card_authotization_number : null,
            // 'card_type' => isset($payment->card_type) ? $payment->card_type : null,
            'card_pos' => isset($payment->card_pos) ? $payment->card_pos : null,
            'check_number' => isset($payment->check_number) ? $payment->check_number : null,
            'check_account' => isset($payment->check_account) ? $payment->check_account : null,
            'check_bank' => isset($payment->check_bank) ? $payment->check_bank : null,
            'check_account_owner' => isset($payment->check_account_owner) ? $payment->check_account_owner : null,
            'transfer_ref_no' => isset($payment->transfer_ref_no) ? $payment->transfer_ref_no : null,
            'transfer_issuing_bank' => isset($payment->transfer_issuing_bank) ? $payment->transfer_issuing_bank : null,
            'transfer_destination_account' => isset($payment->transfer_destination_account) ? $payment->transfer_destination_account : null,
            'transfer_receiving_bank' => isset($payment->transfer_receiving_bank) ? $payment->transfer_receiving_bank : null,
            'note' => isset($payment->note) ? $payment->note : $transaction_payment->note
        ];

        $transaction_payment->update($payment_data);
    }
    
    /**
     *  All payments per transaction
     * @param int $transaction_id
     * @param int $business_id = null
     * @return object
     */
    public function sumTransactionPayments($transaction_id, $business_id = null){
        $tp = TransactionPayment::where('transaction_id', $transaction_id)
            ->select(
                DB::raw("SUM(IF(method = 'cash', IF(is_return = 0, amount, amount*-1), 0)) AS total_cash"),
                DB::raw("SUM(IF(method = 'card', IF(is_return = 0, amount, amount*-1), 0)) AS total_card"),
                DB::raw("SUM(IF(method = 'check', IF(is_return = 0, amount, amount*-1), 0)) AS total_check"),
                DB::raw("SUM(IF(method = 'bank_transfer', IF(is_return = 0, amount, amount*-1), 0)) AS total_bank"),
                DB::raw("SUM(IF(method = 'cash', IF(is_return = 1, amount, 0), 0)) AS total_cash_return")
            );
        if($business_id){
            $tp->where('business_id', $business_id);
        }

        $tp = $tp->first();
        return $tp;
    }

    /**
     * Check if document type is ticket.
     * 
     * @param  int  $transaction_id
     * @return int
     */
    public function isTicket($transaction_id)
    {
        $document_type = Transaction::leftJoin('document_types as dt', 'dt.id', 'transactions.document_types_id')
            ->where('transactions.id', $transaction_id)
            ->select('dt.print_format')
            ->first();

        $is_ticket = 0;

        if ($document_type->print_format == $this->ticket_print_format) {
            $is_ticket = 1;
        }

        return $is_ticket;
    }

    /**
     * Check if document type is fiscal credit.
     * 
     * @param  int  $transaction_id
     * @return int
     */
    public function isCFC($transaction_id)
    {
        $document_type = Transaction::leftJoin('document_types as dt', 'dt.id', 'transactions.document_types_id')
            ->where('transactions.id', $transaction_id)
            ->select('dt.print_format')
            ->first();

        $is_cfc = 0;

        if ($document_type->print_format == $this->cfc_print_format) {
            $is_cfc = 1;
        }

        return $is_cfc;
    }

    /**
     * Validation to allow the check amount not to match expenses total.
     * 
     * @param  float  $check_amount
     * @param  mixed  $expenses
     * @param  string  $type
     * @return boolean
     */
    public function validateMatchCheckAndExpense($check_amount, $expenses, $type)
    {
        $validation = false;

        $match_check_n_expense = request()->session()->get('business.match_check_n_expense');

        if (! $match_check_n_expense) {
            if ($expenses) {
                $expenses_total = 0;

                foreach ($expenses as $exp) {
                    if ($type == 'create') {
                        $expenses_total += $this->num_uf($exp['_final_total']);

                    } else if ($type == 'update') {
                        $e = Transaction::where('id', $exp)->first();
                        $expenses_total += $e->final_total;
                    }
                }

                if (($expenses_total - $check_amount) > 0.1) {
                    $validation = true;
                }
            }
        }

        return $validation;
    }

        /**
     * return true is month is closed
     * @param date $date
     * @return boolean
     */
    public function isClosed($transaction_date){    
        $date = \Carbon::parse($this->uf_date($transaction_date));

        /** Get the first and end date of month */
        $start_date = $date->format('Y-m-01');
        $end_date = $date->endOfMonth()->format('Y-m-d');

        $closed_trans =
            Transaction::whereIn('type', ['purchase', 'expense'])
                ->where('is_closed', 1)
                ->whereRaw('DATE(transaction_date) >= ?', [$start_date])
                ->whereRaw('DATE(transaction_date) <= ?', [$end_date])
                ->count();

        return $closed_trans;
    }

    /**
     * Add or remove milesimas that do not allow the total paid to match the final total.
     * 
     * @param  int  $transaction_id
     * @param  float  $new_pay
     * @param  float  $old_pay
     */
    public function convertPayment($transaction_id, $new_pay, $old_pay = 0)
    {
        // Payment to return
        $pay_return = $new_pay;
        
        // Calculate total paid
        $total_paid = $this->getTotalPaid($transaction_id) + $new_pay - $old_pay;

        // Calculate difference between final total and total paid
        $final_total = Transaction::find($transaction_id)->final_total;
        $difference = number_format($final_total - $total_paid, 6);

        if (abs($difference) >= 0.000001 && abs($difference) <= 0.009999) {
            $pay_return = $new_pay + $difference;
        }

        return $pay_return;
    }

    /**
     * Save tax and payment amounts.
     * 
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function saveTaxAndPayment($transaction)
    {
        $discount_amount = $this->getDiscountValue($transaction->total_before_tax, $transaction->discount_type, $transaction->discount_amount);
        $tax_group_rate = $this->taxUtil->getLinesTaxPercent($transaction->id);
        $tax_group_amount = ($transaction->total_before_tax - $discount_amount) * $tax_group_rate;

        $payment_balance = TransactionPayment::where('transaction_id', $transaction->id)->sum('amount');

        $transaction->tax_group_rate = $tax_group_rate;
        $transaction->tax_group_amount = $tax_group_amount;
        $transaction->payment_balance = $payment_balance;

        $transaction->save();
    }

    /**
     * Get the number of sales recorded per hour.
     * 
     * @param  int  $business_id
     * @param  mixed  $location_id
     * @param  string  $start
     * @param  string  $end
     * @return array
     */
    public function getPeakSalesHours($business_id, $location_id, $start, $end)
    {
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$start, $end]);

        if ($location_id != 'all') {
            $query->where('location_id', $location_id);
        }

        $sales = $query->groupBy(DB::raw("HOUR(transaction_date)"))
        ->select(
            DB::raw("HOUR(transaction_date) AS hours"),
            DB::raw("COUNT(*) AS sales")
        )
            ->orderBy('hours')
            ->pluck('sales', 'hours');

        return $sales;
    }

    /**
     * Create reservation.
     *
     * @param  int  $business_id
     * @param  array  $input
     * @param  int  $user_id
     * @return \App\Quote
     */
    public function createReservation($business_id, $input, $user_id)
    {
        $quote = Quote::create([
            'customer_id' => $input['customer_id'],
            'employee_id' => $input['commission_agent'],
            'user_id' => $user_id,
            'business_id' => $business_id,
            'document_type_id' => $input['document_type_id'],
            'quote_date' => $input['quote_date'],
            // due_date' => $input['quote_date'],
            'type' => 'reservation',
            // 'status' => 'opened',
            'quote_ref_no' => $input['ref_no'],
            'customer_name' => $input['customer_name'],
            // 'contact_name',
            // 'email',
            // 'mobile',
            // 'address',
            'payment_condition' => $input['payment_condition'],
            // 'tax_detail',
            // 'validity',
            // 'delivery_time',
            'note' => $input['note'],
            // 'terms_conditions',
            'discount_type' => $input['discount_type'],
            'discount_amount' => $this->num_uf($input['discount_amount']),
            'total_before_tax' => $input['subtotal'],
            'tax_amount' => $this->num_uf($input['withheld']),
            'total_final' => $this->num_uf($input['final_total']),
            'created_by' => $user_id,
            'selling_price_group_id' => isset($input['selling_price_group_id']) ? $input['selling_price_group_id'] : null,
            'cashier_id' => isset($input['cashier_id']) ? $input['cashier_id'] : null,
            'location_id' => $input['location_id'],
            'warehouse_id' => $input['location_id']
        ]);

        return $quote;
    }

    /**
     * Create quote line.
     * 
     * @param  \App\Quote  $quote
     * @param  array  $quote_lines
     * @param  int  $location_id
     * @return boolean
     */
    public function createQuoteLines($quote, $quote_lines, $location_id)
    {
        foreach ($quote_lines as $ql) {
            // Calculate unit price and unit price before discount
            $unit_price_before_discount = $this->num_uf($ql['unit_price']);
            $unit_price = $unit_price_before_discount;

            if (! empty($ql['line_discount_type']) && $ql['line_discount_amount']) {
                $discount_amount = $this->num_uf($ql['line_discount_amount']);

                if ($ql['line_discount_type'] == 'fixed') {
                    $unit_price = $unit_price_before_discount - $discount_amount;

                } elseif ($ql['line_discount_type'] == 'percentage') {
                    $unit_price = ((100 - $discount_amount) * $unit_price_before_discount) / 100;
                }
            }

            $tax_amount = isset($ql['unit_price_exc_tax']) ? $ql['unit_price_inc_tax'] - $ql['unit_price_exc_tax'] : 0;

            $quote_line = new QuoteLine();
            $quote_line->quote_id = $quote->id;
            $quote_line->variation_id = $ql['variation_id'];
            $quote_line->warehouse_id = $location_id;
            $quote_line->quantity = $this->num_uf($ql['quantity']);
            // $quote_line->unit_price_exc_tax = isset($ql['unit_price_exc_tax']) ? $this->num_uf($ql['unit_price_exc_tax']) : 0;
            $quote_line->unit_price_exc_tax = isset($ql['u_price_exc_tax']) ? $this->num_uf($ql['u_price_exc_tax']) : 0;
            // $quote_line->unit_price_inc_tax = isset($ql['unit_price_inc_tax']) ? $this->num_uf($ql['unit_price_inc_tax']) : 0;
            $quote_line->unit_price_inc_tax = isset($ql['u_price_inc_tax']) ? $this->num_uf($ql['u_price_inc_tax']) : $unit_price;
            $quote_line->discount_type = $ql['line_discount_type'];
            $quote_line->discount_amount = $this->num_uf($ql['line_discount_amount']);
            $quote_line->tax_amount = $tax_amount;
            $quote_line->save();
        }

        return true;
    }

    /**
     * Add line for payment.
     *
     * @param  \App\Quote/int  $quote
     * @param  array  $payments
     * @param  int  $contacts_id
     * @param  int  $cashier_id
     * @param  int  $user_id
     * @param  string  $quote_date
     * @param  string  $note
     * @return boolean
     */
    public function createOrUpdatePaymentLinesToQuote(
        $quote, $payments, $contact_id = null, $cashier_id = null, $user_id = null, $quote_date = null, $note = null)
    {
        $payments_formatted = [];
        $edit_ids = [0];
        $account_transactions = [];
        
        if (! is_object($quote)) {
            $quote = Quote::findOrFail($quote);
        }

        $c = 0;

        foreach ($payments as $payment) {
            // Check if quote_line_id is set
            if (! empty($payment['payment_id'])) {
                $edit_ids[] = $payment['payment_id'];
                $this->editPaymentLine($payment);

            } else {
                // If amount is 0 then skip
                if ($this->num_uf($payment['amount']) != 0) {
                    $prefix_type = 'reservation_payment';
                    
                    $ref_count = $this->setAndGetReferenceCount($prefix_type, $quote->business_id);
                    
                    // Generate reference number
                    $payment_ref_no = $this->generateReferenceNumber($prefix_type, $ref_count, $quote->business_id);

                    $payment_data = [
                        'amount' => $this->num_uf($payment['amount']),
                        'method' => $payment['method'],
                        'business_id' => $quote->business_id,
                        'is_return' => isset($payment['is_return']) ? $payment['is_return'] : 0,
                        'card_holder_name' => isset($payment['card_holder_name']) ? $payment['card_holder_name'] : null,
                        'card_authotization_number' => isset($payment['card_authotization_number']) ? $payment['card_authotization_number'] : null,
                        'card_type' => isset($payment['card_type']) ? $payment['card_type'] : null,
                        'card_pos' => isset($payment['card_pos']) ? $payment['card_pos'] : null,
                        'check_number' => isset($payment['check_number']) ? $payment['check_number'] : null,
                        'check_account' => isset($payment['check_account']) ? $payment['check_account'] : null,
                        'check_bank' => isset($payment['check_bank']) ? $payment['check_bank'] : null,
                        'check_account_owner' => isset($payment['check_account_owner']) ? $payment['check_account_owner'] : null,
                        'transfer_ref_no' => isset($payment['transfer_ref_no']) ? $payment['transfer_ref_no'] : null,
                        'transfer_issuing_bank' => isset($payment['transfer_issuing_bank']) ? $payment['transfer_issuing_bank'] : null,
                        'transfer_destination_account' => isset($payment['transfer_destination_account']) ? $payment['transfer_destination_account'] : null,
                        'transfer_receiving_bank' => isset($payment['transfer_receiving_bank']) ? $payment['transfer_receiving_bank'] : null,
                        'note' => $note,
                        'paid_on' => is_null($quote_date) ? \Carbon::now()->toDateTimeString() : $quote_date,
                        'created_by' => empty($user_id) ? auth()->user()->id : $user_id,
                        'payment_for' => $contact_id,
                        'payment_ref_no' => $payment_ref_no,
                        'account_id' => ! empty($payment['account_id']) ? $payment['account_id'] : null,
                        'cashier_id' => ! empty($cashier_id) ? $cashier_id : null
                    ];

                    $payments_formatted[] = new TransactionPayment($payment_data);

                    $account_transactions[$c] = [];

                    // Create account transaction
                    if (!empty($payment['account_id'])) {
                        $account_transactions[$c] = $payment_data;
                    }

                    $c++;
                }
            }
        }

        // Delete the payment lines removed
        if (! empty($edit_ids)) {
            $deleted_transaction_payments = $quote->payment_lines()->whereNotIn('id', $edit_ids)->get();

            $quote->payment_lines()->whereNotIn('id', $edit_ids)->delete();

            // Fire delete transaction payment event
            foreach ($deleted_transaction_payments as $deleted_transaction_payment) {
                // Store binnacle
                $this->registerBinnacle($user_id, 'payment', 'delete', $deleted_transaction_payment);

                if (! empty($deleted_transaction_payment->account_id)) {
                    event(new TransactionPaymentDeleted($deleted_transaction_payment->id, $deleted_transaction_payment->account_id));
                }
            }
        }

        if (! empty($payments_formatted)) {
            $quote->payment_lines()->saveMany($payments_formatted);

            // Store binnacle
            foreach ($payments_formatted as $pf) {
                // $this->registerBinnacle($user_id, 'payment', 'create', $pf);
            }

            foreach ($quote->payment_lines as $key => $value) {
                if (! empty($account_transactions[$key])) {
                    event(new TransactionPaymentAdded($value, $account_transactions[$key]));
                }
            }
        }

        return true;
    }

    /**
     * Get payment line for a quote.
     *
     * @param  int  $quote_id
     * @return boolean
     */
    public function getPaymentDetailsToQuotes($quote_id)
    {
        $payment_lines = TransactionPayment::where('quote_id', $quote_id)
            ->get()
            ->toArray();

        return $payment_lines;
    }

    /**
     * Get total paid amount for a quote
     *
     * @param  int  $quote_id
     * @return float
     */
    public function getTotalPaidToQuotes($quote_id)
    {
        $total_paid = TransactionPayment::where('quote_id', $quote_id)
            ->select(DB::raw('SUM(IF(is_return = 0, amount, amount * -1)) as total_paid'))
            ->first()
            ->total_paid;

        return $total_paid;
    }

    /**
     * Calculates the payment status and returns back.
     *
     * @param  int  $quote_id
     * @param  float  $final_amount
     * @return string
     */
    public function calculatePaymentStatusToQuotes($quote_id, $final_amount = null)
    {
        $total_paid = $this->getTotalPaidToQuotes($quote_id);

        if (is_null($final_amount)) {
            $final_amount = Quote::find($quote_id)->total_final;
        }

        $status = 'due';

        if ($final_amount <= $total_paid) {
            $status = 'paid';

        } else if ($total_paid > 0 && $final_amount > $total_paid) {
            $status = 'partial';
        }

        return $status;
    }

    /**
     * Delete the products removed and increment product stock. Includes kits.
     *
     * @param array $transaction_line_ids
     * @param int $location_id
     * @return boolean
     */
    public function deleteQuoteLines($quote_line_ids, $location_id, $warehouse_id)
    {
        if (! empty($quote_line_ids)) {
            $quote_lines = QuoteLine::whereIn('id', $quote_line_ids)
                ->get();

            // Adjust quantity
            foreach ($quote_lines as $line) {
                $this->adjustQuantityReserved(
                    $location_id,
                    $warehouse_id,
                    $line->variation_id,
                    $line->quantity
                );
            }

            QuoteLine::whereIn('id', $quote_lines)->delete();
        }
    }

    /**
     * Adjust the quantity of product and its variation. Includes kits.
     *
     * @param int $location_id
     * @param int $product_id
     * @param int $variation_id
     * @param float $increment_qty
     * @return boolean
     */
    public function adjustQuantityReserved($location_id, $warehouse_id, $variation_id, $decrement_qty)
    {
        if ($decrement_qty != 0) {
            $variation = Variation::where('id', $variation_id)->first();

            $product = Product::where('id', $variation->product_id)->first();

            $flag = false;

            if (config('app.business') == 'optics') {
                if ($product->clasification == 'product' || $product->clasification == 'material') {
                    $flag = true;
                }

            } else {
                if ($product->clasification == 'product') {
                    $flag = true;
                }
            }

            // Adjust quantity in variations location table
            if ($product->clasification == 'kits') {
                $childrens = KitHasProduct::where('parent_id', $product->id)->get();

                foreach ($childrens as $item) {
                    $variation_q = Variation::where('id', $item->children_id)->first();

                    VariationLocationDetails::where('variation_id', $variation_q->product_id)
                        ->where('product_id', $item->children_id)
                        ->where('location_id', $location_id)
                        ->where('warehouse_id', $warehouse_id)
                        ->decrement('qty_reserved', $item->quantity * $decrement_qty);
                }

            } else if ($flag) {
                if ($product->enable_stock == 1) {
                    VariationLocationDetails::where('variation_id', $variation_id)
                        ->where('product_id', $product->id)
                        ->where('location_id', $location_id)
                        ->where('warehouse_id', $warehouse_id)
                        ->decrement('qty_reserved', $decrement_qty);
                }
            }
        }
    }

    /**
     * Add Sell transaction
     *
     * @param mixed $transaction_id
     * @param int $business_id
     * @param array $input
     * @param float $invoice_total
     * @param int $user_id
     * @return boolean
     */
    public function updateReservation($quote_id, $business_id, $input, $invoice_total, $user_id)
    {
        $quote = $quote_id;

        if (! is_object($quote)) {
            $quote = Quote::where('id', $quote_id)->first();
        }

        // Clone record before action
        $quote_old = clone $quote;

        $update_data = [
            'customer_id' => $input['customer_id'],
            'cashier_id' => isset($input['cashier_id']) ? $input['cashier_id'] : $quote->cashier_id,
            'customer_name' => $input['customer_name'],
            'total_before_tax' => $input['subtotal'],
            'discount_type' => $input['discount_type'],
            'discount_amount' => $this->num_uf($input['discount_amount']),
            'tax_amount' => $this->num_uf($input['withheld']), //Withheld amount //$invoice_total['tax'],
            'total_final' => $this->num_uf($input['final_total']),
            // 'employee_id' => $input['commission_agent'],
            // 'shipping_details' => isset($input['shipping_details']) ? $input['shipping_details'] : null,
            // 'shipping_charges' => isset($input['shipping_charges']) ? $this->num_uf($input['shipping_charges']) : 0,
            'selling_price_group_id' => isset($input['selling_price_group_id']) ? $input['selling_price_group_id'] : null,
            'document_type_id' => isset($input['document_type_id']) ? $input['document_type_id'] : $quote->document_type_id
        ];

        if (! empty($input['transaction_date'])) {
            $update_data['transaction_date'] = $input['transaction_date'];
        }
        
        $quote->fill($update_data);
        $quote->update();

        // Store binnacle
        // $this->registerBinnacle($user_id, 'reservation', 'update', $quote_old, $quote);

        return $quote;
    }

    public function createOrUpdateQuoteLines($quote, $products, $location_id, $return_deleted = false, $status_before = null, $extra_line_parameters = [])
    {
        $lines_formatted = [];
        $modifiers_array = [];
        $edit_ids = [0];
        $modifiers_formatted = [];

        foreach ($products as $product) {
            // Check if quote_lines_id is set
            if (! empty($product['transaction_sell_lines_id'])) {
                $edit_ids[] = $product['transaction_sell_lines_id'];
                $this->editQuoteLine($product, $location_id, $status_before);

            } else {
                // Calculate unit price and unit price before discount
                $unit_price_before_discount = $this->num_uf($product['unit_price']);
                $unit_price = $unit_price_before_discount;

                if (! empty($product['line_discount_type']) && $product['line_discount_amount']) {
                    $discount_amount = $this->num_uf($product['line_discount_amount']);

                    if ($product['line_discount_type'] == 'fixed') {
                        $unit_price = $unit_price_before_discount - $discount_amount;

                    } else if ($product['line_discount_type'] == 'percentage') {
                        $unit_price = ((100 - $discount_amount) * $unit_price_before_discount) / 100;
                    }
                }

                $tax_amount = isset($product['unit_price_exc_tax']) ? $product['unit_price_inc_tax'] - $product['unit_price_exc_tax'] : 0;

                $line = [
                    // 'product_id' => $product['product_id'],
                    'variation_id' => $product['variation_id'],
                    'quantity' => $this->num_uf($product['quantity']),
                    // 'unit_price_before_discount' => isset($product['u_price_exc_tax']) ? $this->num_uf($product['u_price_exc_tax']) : $unit_price_before_discount,
                    // 'unit_price' => isset($product['u_price_inc_tax']) ? $this->num_uf($product['u_price_inc_tax']) : $unit_price,
                    'discount_type' => ! empty($product['line_discount_type']) ? $product['line_discount_type'] : null,
                    'discount_amount' => ! empty($product['line_discount_amount']) ? $this->num_uf($product['line_discount_amount']) : 0,
                    // 'item_tax' => null, //$this->num_uf($product['item_tax']),
                    // 'tax_id' => isset($product['tax_group_id']) ? $product['tax_group_id'] : null, //$product['tax_id'],
                    'tax_amount' => number_format($tax_amount, 4),
                    'unit_price_exc_tax' => isset($product['unit_price_exc_tax']) ? $this->num_uf($product['unit_price_exc_tax']) : 0,
                    'unit_price_inc_tax' => isset($product['unit_price_inc_tax']) ? $this->num_uf($product['unit_price_inc_tax']) : 0,
                    // 'sell_line_note' => !empty($product['sell_line_note']) ? $product['sell_line_note'] : ''
                ];

                foreach ($extra_line_parameters as $key => $value) {
                    $line[$key] = ! empty($product[$value]) ? $product[$value] : '';
                }

                $lines_formatted[] = new QuoteLine($line);
            }
        }

        if (! is_object($quote)) {
            $quote = Quote::findOrFail($quote);
        }

        // Delete the products removed and increment product stock
        $deleted_lines = [];

        if (! empty($edit_ids)) {
            $deleted_lines = QuoteLine::where('quote_id', $quote->id)
                ->whereNotIn('id', $edit_ids)
                ->select('id')
                ->get()
                ->toArray();
                
            $this->deleteQuoteLines($deleted_lines, $location_id, $location_id);
        }

        if (! empty($lines_formatted)) {
            $quote_lines = $quote->quote_lines()->saveMany($lines_formatted);
            
            // Create transaction tax details
            // $this->createTransactionTaxDetail($sell_lines);
        }

        if ($return_deleted) {
            return $deleted_lines;
        }

        return true;
    }

    /**
     * Edit transaction sell line
     *
     * @param array $product
     * @param int $location_id
     * @return boolean
     */
    public function editQuoteLine($product, $location_id, $status_before)
    {
        // Get the old reservation quantity
        $quote_line = QuoteLine::find($product['transaction_sell_lines_id']);

        // Adjust quantity
        $difference = $quote_line->quantity - $this->num_uf($product['quantity']);
        $this->adjustQuantityReserved($location_id, $location_id, $product['variation_id'], $difference);
       
        $unit_price_before_discount = $this->num_uf($product['unit_price']);

        $unit_price = $unit_price_before_discount;

        if (! empty($product['line_discount_type']) && $product['line_discount_amount']) {
            $discount_amount = $this->num_uf($product['line_discount_amount']);

            if ($product['line_discount_type'] == 'fixed') {
                $unit_price = $unit_price_before_discount - $discount_amount;
            } else if ($product['line_discount_type'] == 'percentage') {
                $unit_price = ((100 - $discount_amount) * $unit_price_before_discount) / 100;
            }
        }

        // Update sell lines
        $quote_line->fill([
            'variation_id' => $product['variation_id'],
            'quantity' => $this->num_uf($product['quantity']),
            // 'unit_price_before_discount' => $unit_price_before_discount,
            'unit_price_exc_tax' => $unit_price,
            'unit_price_inc_tax' => $this->num_uf($product['unit_price_inc_tax']),
            'discount_type' => ! empty($product['line_discount_type']) ? $product['line_discount_type'] : null,
            'discount_amount' => ! empty($product['line_discount_amount']) ? $this->num_uf($product['line_discount_amount']) : 0
        ]);

        $quote_line->save();
    }

    /**
     * Create or update kardex lines for lab orders.
     * 
     * @param  int  $transaction_id
     * @param  string  $reference
     * @param  \App\LabOrderDetail  $lines
     * @param  \App\LabOrderDetail  $lines_before
     * @param  int  $calculate_balance
     * @return void
     */
    public function createOrUpdateLabOrderLines(
        $transaction_id, $reference, $lines,
        $lines_before = null, $calculate_balance = null)
    {
        // Save kardex lines
        foreach ($lines as $line) {
            $product = Product::find($line->variation->product_id);

            $movement_type = MovementType::where('name', 'lab_order')
                ->where('type', 'output')
                ->where('business_id', $line->location->business_id)
                ->first();

            // Check if movement type is set else create it
            if (empty($movement_type)) {
                $movement_type = MovementType::create([
                    'name' => 'lab_order',
                    'type' => 'output',
                    'business_id' => $line->location->business_id
                ]);
            }

            if ($calculate_balance === 1) {
                $balance = 1;

            } else {
                $balance = null;
            }

            $this->saveKardexLineForLabOrder(
                $movement_type,
                $transaction_id,
                $line,
                $product,
                $line->quantity,
                $line->variation->default_purchase_price,
                $line->quantity * $line->variation->default_purchase_price,
                $reference,
                $balance
            );
        }

        // Delete kardex lines whose lab order details no longer exist
        if ((! is_null($lines_before)) || (! empty($lines_before))) {
            foreach ($lines_before as $line_before) {
                // Check that the lab order detail still exists
                $line_exist = $lines->where('id', $line_before->id)->first();
                
                if (empty($line_exist)) {
                    $product = Product::find($line_before->variation->product_id);

                    $movement_type = MovementType::where('name', 'lab_order')
                        ->where('type', 'output')
                        ->where('business_id', $line_before->location->business_id)
                        ->first();

                    // Check if movement type is set else create it
                    if (empty($movement_type)) {
                        $movement_type = MovementType::create([
                            'name' => 'lab_order',
                            'type' => 'output',
                            'business_id' => $line_before->location->business_id
                        ]);
                    }
                    
                    $this->deleteKardexLineForLabOrder(
                        $line_before->location_id,
                        $line_before->warehouse_id,
                        $product,
                        $line_before->lab_order_id,
                        $line_before->variation_id
                    );
                }
            }
        }
    }

    /**
     * Create or update a kardex line for lab order.
     * 
     * @param  \App\MovementType  $movement_type
     * @param  int  $transaction_id
     * @param  \App\LabOrderDetail  $lod
     * @param  \App\Product  $product
     * @param  float  $quantity
     * @param  float  $unit_cost
     * @param  float  $total_cost
     * @param  string  $reference
     * @param  float  $balance
     * @return void
     */
    public function saveKardexLineForLabOrder(
        $movement_type, $transaction_id, $lod, $product, $quantity,
        $unit_cost, $total_cost, $reference, $balance = null)
    {    
        // Check if the product has stock control
        if (($product->clasification == 'product' || $product->clasification == 'material') &&
            ($product->enable_stock == 1)) {
            
            // Auxiliary data
            $user_id = request()->session()->get('user.id');
            $business_id = $lod->location->business_id;
            $date_time = $lod->created_at;

            $vld = VariationLocationDetails::where('variation_id', $lod->variation_id)
                ->where('location_id', $lod->location_id)
                ->where('warehouse_id', $lod->warehouse_id)
                ->first();
    
            // Kardex data
            $data = [
                'movement_type_id' => $movement_type->id,
                'business_location_id' => $lod->location_id,
                'warehouse_id' => $lod->warehouse_id,
                'product_id' => $product->id,
                'transaction_id' => $transaction_id,
                'reference' => $reference,
                'date_time' => $date_time,
                'business_id' => $business_id,
                'lab_order_id' => $lod->lab_order_id,
                'line_reference' => $lod->id,
                'variation_id' => $lod->variation_id
            ];

            // Check if it input or output
            if ($movement_type->type == 'input') {
                $data['inputs_quantity'] = $this->num_uf($quantity);
                $data['unit_cost_inputs'] = $this->num_uf($unit_cost);
                $data['total_cost_inputs'] = $this->num_uf($total_cost);
            } else {
                $data['outputs_quantity'] = $this->num_uf($quantity);
                $data['unit_cost_outputs'] = $this->num_uf($unit_cost);
                $data['total_cost_outputs'] = $this->num_uf($total_cost);
            }

            if (! is_null($balance)) {
                $data['balance'] = $this->num_uf($balance);
            } else {
                $data['balance'] = $this->num_uf($vld->qty_available);
            }
    
            // Get kardex line if it exists
            $kardex = Kardex::where('business_location_id', $lod->location_id)
                ->where('warehouse_id', $lod->warehouse_id)
                ->where('variation_id', $lod->variation_id)
                ->where('lab_order_id', $lod->lab_order_id)
                ->where('line_reference', $lod->id)
                ->first();
    
            $data['updated_by'] = $user_id;

            // Check if kardex line is set
            if (! empty($kardex)) {
                $kardex->fill($data);

            } else {
                $data['created_by'] = $user_id;
                $kardex = new Kardex($data);
            }
    
            $kardex->save();

            if (is_null($balance)) {
                $this->recalculateBalances($kardex);
            }
    
            // \Log::info('Lab order: ' . $lod->lab_order_id);
        }
    }

    /**
     * Delete a kardex line for lab order.
     * 
     * @param  int  $location_id
     * @param  int  $warehouse_id
     * @param  \App\Product  $product
     * @param  int  $lab_order_id
     * @param  int  $variation_id
     * @return void
     */
    public function deleteKardexLineForLabOrder($location_id, $warehouse_id, $product, $lab_order_id, $variation_id)
    {
        // Check if the product has stock control
        if (($product->clasification == 'product' || $product->clasification == 'material') &&
            ($product->enable_stock == 1)) {
    
            // Get kardex line if it exists
            $kardex = Kardex::where('business_location_id', $location_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('variation_id', $variation_id)
                ->where('lab_order_id', $lab_order_id)
                ->first();
            
            // Check if output line is set
            if (! empty($kardex)) {
                $kardex->delete();
            }
        }
    }

    /**
     * Delete kardex line by lab order.
     * 
     * @param  int  $id
     * @return void
     */
    public function deleteKardexByLabOrder($id)
    {
        $lines = Kardex::where('lab_order_id', $id)->get();

        foreach ($lines as $line) {
            $line->inputs_quantity = 0;
            $line->outputs_quantity = 0;
            $line->balance = 0;
            $line->save();

            $this->recalculateBalances($line);

            $line->delete();
        }
    }

    /**
     * Adjust the quantity of product and its variation. Includes kits.
     *
     * @param int $location_id
     * @param int $product_id
     * @param int $variation_id
     * @param float $increment_qty
     * @return boolean
     */
    public function adjustStockToQuote($location_id, $variation_id, $decrement_qty)
    {
        if ($decrement_qty != 0) {
            $variation = Variation::find($variation_id);

            $product = Product::find($variation->product_id);

            // Adjust quantity in variations location table
            if ($product->clasification == 'kits') {
                $childrens = KitHasProduct::where('parent_id', $product->id)->get();

                foreach ($childrens as $item) {
                    $variation_q = Variation::where('id', $item->children_id)->first();

                    VariationLocationDetails::where('variation_id', $variation_q->product_id)
                        ->where('product_id', $item->children_id)
                        ->where('location_id', $location_id)
                        ->where('warehouse_id', $location_id)
                        ->decrement('qty_reserved', $item->quantity * $decrement_qty);
                }

            } else if ($product->clasification == 'product' || $product->clasification == 'material') {
                if ($product->enable_stock == 1) {
                    VariationLocationDetails::where('variation_id', $variation_id)
                        ->where('product_id', $variation->product_id)
                        ->where('location_id', $location_id)
                        ->where('warehouse_id', $location_id)
                        ->decrement('qty_reserved', $decrement_qty);
                }
            }
        }
    }

    /**
     * Update import data.
     * 
     * @param  int  $transaction_id
     * @return void
     */
    public function updateImportData($transaction_id)
    {
        $transaction = Transaction::find($transaction_id);

        if ($transaction->purchase_type == 'international') {
            $purchase_lines = PurchaseLine::where('transaction_id', $transaction_id)->get();

            // Purchase expense total
            $thie = TransactionHasImportExpense::where('transaction_id', $transaction_id)->sum('amount');

            // Total before expense
            $transaction->total_before_expense = $purchase_lines->sum(function ($query) {
                $price = is_null($query->initial_purchase_price) ? $query->purchase_price : $query->initial_purchase_price;
                return round($query->quantity * $price, 6);
            });

            // Purchase expense amount
            $purchase_expense_amount = 0;

            if ($transaction->distributing_base == 'weight') {
                $total = $purchase_lines->sum('weight_kg');

                foreach ($purchase_lines as $pl) {
                    if ($total != 0) {
                        $purchase_expense = $pl->weight_kg * $thie / $total;
                        $purchase_expense_amount += $purchase_expense;
                    }
                }

            } else {
                $total = $purchase_lines->sum(function ($query) {
                    $price = is_null($query->initial_purchase_price) ? $query->purchase_price : $query->initial_purchase_price;
                    return round($query->quantity * $price, 6);
                });

                foreach ($purchase_lines as $pl) {
                    if ($total != 0) {
                        $price = is_null($pl->initial_purchase_price) ? $pl->purchase_price : $pl->initial_purchase_price;
                        $purchase_expense = ($price * $pl->quantity) * $thie / $total;
                        $purchase_expense_amount += $purchase_expense;
                    }
                }
            }

            $transaction->purchase_expense_amount = $purchase_expense_amount;

            // Total after expense
            $transaction->total_after_expense = $transaction->total_before_expense + $transaction->purchase_expense_amount;
            
            // Apportionment expense amount
            $transaction->apportionment_expense_amount = $purchase_lines->sum('import_expense_amount');
            
            // DAI amount
            $transaction->dai_amount = $purchase_lines->sum('dai_amount');
            
            // VAT amount
            $transaction->tax_amount = $purchase_lines->sum('tax_amount');

            // Total before tax
            $transaction->total_before_tax = $purchase_lines->sum(function ($query) {
                $price = is_null($query->initial_purchase_price) ? $query->purchase_price : $query->initial_purchase_price;
                return round($query->quantity * $price, $this->price_precision) + $query->import_expense_amount;
            }) + $purchase_expense_amount;

            // Final total
            $transaction->final_total = $purchase_lines->sum(function ($query) {
                $price = is_null($query->initial_purchase_price) ? $query->purchase_price : $query->initial_purchase_price;
                return round($query->quantity * $price, $this->price_precision) + $query->import_expense_amount;
            }) + $purchase_expense_amount + $transaction->tax_amount + $transaction->dai_amount;

            $transaction->save();

            // Update payment status
            $this->updatePaymentStatus($transaction->id);
        }
    }

    /**
     * Gives total purchases of last 30 days day-wise
     *
     * @param  int  $business_id
     * @return Illuminate\Support\Collection
     */
    public function getPurchasesLast30Days($business_id)
    {
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->where('status', 'received')
            ->whereBetween(DB::raw('date(transaction_date)'), [\Carbon::now()->subDays(30), \Carbon::now()]);

        // Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        $purchases = $query->select(
                DB::raw("DATE_FORMAT(transaction_date, '%Y-%m-%d') as date"),
                DB::raw("SUM(final_total) as total_purchases_inc_tax"),
                DB::raw("SUM(total_before_tax) as total_purchases_exc_tax"),
            )
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->get();

        // Show values ​​including or excluding taxes
        $business = Business::find($business_id);
        $dashboard_settings = empty($business->dashboard_settings) ? null : json_decode($business->dashboard_settings, true);

        if ($dashboard_settings['box_exc_tax']) {
            $purchases = $purchases->pluck('total_purchases_exc_tax', 'date');
        } else {
            $purchases = $purchases->pluck('total_purchases_inc_tax', 'date');
        }

        return $purchases;
    }

    /**
     * Gives total purchases of current fiscal year month-wise
     *
     * @param  int  $business_id
     * @param  string  $start
     * @param  string  $end
     * @return Illuminate\Support\Collection
     */
    public function getPurchasesCurrentFy($business_id, $start, $end)
    {
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->where('status', 'received')
            ->whereBetween(DB::raw('date(transaction_date)'), [$start, $end]);

        // Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        
        $purchases = $query->groupBy(DB::raw("DATE_FORMAT(transaction_date, '%Y-%m')"))
            ->select(
                DB::raw("DATE_FORMAT(transaction_date, '%m-%Y') as yearmonth"),
                DB::raw("SUM(final_total) as total_purchases_inc_tax"),
                DB::raw("SUM(total_before_tax) as total_purchases_exc_tax"),
            )
            ->get();

        // Show values ​​including or excluding taxes
        $business = Business::find($business_id);
        $dashboard_settings = empty($business->dashboard_settings) ? null : json_decode($business->dashboard_settings, true);

        if ($dashboard_settings['box_exc_tax']) {
            $purchases = $purchases->pluck('total_purchases_exc_tax', 'yearmonth');
        } else {
            $purchases = $purchases->pluck('total_purchases_inc_tax', 'yearmonth');
        }

        return $purchases;
    }

    /**
     * Gives total stock of last 30 days day-wise
     *
     * @param  int  $business_id
     * @return array
     */
    public function getStockLast30Days($business_id)
    {
        $location_id = 0;

        // Get data
        $result = collect(DB::select(
            'CALL monetary_total_stock_per_days(?, ?, ?)',
            [$business_id, $location_id, \Carbon::now()->subDays(29)]
        ))
        ->pluck('total', 'full_date');

        return $result;
    }

    /**
     * Gives total purchases of current fiscal year month-wise
     *
     * @param  int  $business_id
     * @param  string  $start
     * @return array
     */
    public function getStockCurrentFy($business_id, $start)
    {
        $location_id = 0;

        // Get data
        $result = collect(DB::select(
            'CALL monetary_total_stock_per_months(?, ?, ?)',
            [$business_id, $location_id, $start]
        ))
        ->pluck('total', 'full_date');

        return $result;
    }
}
