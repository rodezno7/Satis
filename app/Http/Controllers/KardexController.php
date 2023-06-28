<?php

namespace App\Http\Controllers;

use App\Apportionment;
use App\ApportionmentHasTransaction;
use DB;
use Excel;
use App\Kardex;
use App\Product;
use App\TaxRate;
use App\Business;
use App\Variation;
use App\Warehouse;
use App\Transaction;
use App\MovementType;
use App\PurchaseLine;
use App\Utils\TaxUtil;
use App\PhysicalInventory;
use App\Utils\ProductUtil;
use App\StockAdjustmentLine;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use App\Exports\KardexReportExport;
use App\KitHasProduct;
use App\Optics\LabOrder;
use App\Optics\LabOrderDetail;
use App\PhysicalInventoryLine;
use App\VariationLocationDetails;
use Yajra\DataTables\Facades\DataTables;

class KardexController extends Controller
{
    public function __construct(TransactionUtil $transactionUtil, TaxUtil $taxUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->taxUtil = $taxUtil;
        $this->productUtil = $productUtil;

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
        if (! auth()->user()->can('kardex.view')) {
            abort(403, "Unauthorized action.");
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);

        if (request()->ajax()) {
            // Warehouse filter
            $warehouse_id = ! empty(request()->input('warehouse_id')) ? request()->input('warehouse_id') : 0;

            // Variation filter
            $variation_id = ! empty(request()->input('variation_id')) ? request()->input('variation_id') : 0;

            // Date filter
            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
            } else {
                $start = '';
                $end =  '';
            }

            $statement = DB::statement('SET @running_sum = 0');

            $kardex = DB::select("
                SELECT
                    k.id,
                    k.date_time,
                    mt.name AS movement_type,
                    mt.type,
                    k.reference,
                    k.transaction_id,
                    k.balance - k.inputs_quantity + k.outputs_quantity AS initial_stock,
                    k.inputs_quantity,
                    k.outputs_quantity,
                    k.balance,
                    k.total_cost_inputs,
                    k.total_cost_outputs,
                    @running_sum := @running_sum + total_cost_inputs - total_cost_outputs AS balance_cost
                FROM kardexes AS k
                LEFT JOIN movement_types AS mt ON k.movement_type_id = mt.id
                WHERE k.business_id = $business_id
                    AND ('$warehouse_id' = 'all' OR k.warehouse_id = $warehouse_id)
                    AND ('$variation_id' = 'all' OR k.variation_id = $variation_id)
                    AND (('$start' = '' AND '$end' = '') OR (DATE(k.date_time) BETWEEN '$start' AND '$end'))
                ORDER BY k.date_time
            ");

            $transactionUtil = $this->transactionUtil;

            return Datatables::of($kardex)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (auth()->user()->can("purchase.view")) {
                        if($row->type == 'input'){
                            $html .= '<li><a href="#" class="print-invoice" data-href="' . action('KardexController@printInvoicePurchase', [$row->transaction_id, $row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        }else{
                            $html .= '<li><a href="#" class="print-invoice" data-href="' . action('SellPosController@printInvoice',  [$row->transaction_id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        }
                    }

                    $html .= '</ul></div>';
                    return $html;
                })
                ->editColumn(
                    'movement_type', function($row) {
                        return __("movement_type." . $row->movement_type);
                    }
                )
                ->editColumn(
                    'type',
                    '@if($type == "input")
                    <span class="badge" style="background-color: #5cb85c;">{{ __("movement_type." . $type) }}</span>
                    @else
                    <span class="badge" style="background-color: #d9534f;">{{ __("movement_type." . $type) }}</span>
                    @endif'
                )
                ->editColumn(
                    'balance_cost', function($row) use ($transactionUtil) {
                        return number_format($transactionUtil->num_uf($row->balance_cost), 6);
                    }
                )
                ->rawColumns(['type', 'action'])
                ->make(true);
        }

        // Data for selects
        $warehouses = Warehouse::forDropdown($business_id, false, false);

        return view('kardex.index')->with(compact('warehouses', 'business'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kardex  $kardex
     * @return \Illuminate\Http\Response
     */
    public function show(Kardex $kardex)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kardex  $kardex
     * @return \Illuminate\Http\Response
     */
    public function edit(Kardex $kardex)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kardex  $kardex
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kardex $kardex)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kardex  $kardex
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kardex $kardex)
    {
        //
    }

    /**
     * Show the form for kardex generation.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getRegisterKardex()
    {
        if(! auth()->user()->can('kardex.register')) {
			abort(403, "Unauthorized action.");
		}

        return view('kardex.register');
    }

    /**
     * Kardex generation.
     * 
     * @return array
     */
    public function postRegisterKardex()
    {
        if(! auth()->user()->can('kardex.register')) {
			abort(403, "Unauthorized action.");
		}

        if (request()->ajax()) {
            
            try {
                # Set maximum PHP execution time
                ini_set('max_execution_time', 0);

                DB::beginTransaction();

                \Log::info('--- START ---');

                # Delete all kardex lines
                DB::table('kardexes')->where('id', '>', 0)->delete();
                
                # Delete all movement types
                DB::table('movement_types')->where('id', '>', 0)->delete();

                # Create movement types
                $movement_types = [
                    ['name' => 'purchase', 'type' => 'input'],
                    ['name' => 'purchase', 'type' => 'output'],

                    ['name' => 'sell', 'type' => 'input'],
                    ['name' => 'sell', 'type' => 'output'],

                    ['name' => 'expense', 'type' => 'input'],
                    ['name' => 'expense', 'type' => 'output'],

                    ['name' => 'stock_adjustment', 'type' => 'input'],
                    ['name' => 'stock_adjustment', 'type' => 'output'],

                    ['name' => 'sell_transfer', 'type' => 'input'],
                    ['name' => 'sell_transfer', 'type' => 'output'],

                    ['name' => 'purchase_transfer', 'type' => 'input'],
                    ['name' => 'purchase_transfer', 'type' => 'output'],

                    ['name' => 'opening_stock', 'type' => 'input'],
                    ['name' => 'opening_stock', 'type' => 'output'],

                    ['name' => 'sell_return', 'type' => 'input'],
                    ['name' => 'sell_return', 'type' => 'output'],

                    ['name' => 'opening_balance', 'type' => 'input'],
                    ['name' => 'opening_balance', 'type' => 'output'],

                    ['name' => 'purchase_return', 'type' => 'input'],
                    ['name' => 'purchase_return', 'type' => 'output'],
                ];

                if (config('app.business') == 'optics') {
                    $movement_types[] = ['name' => 'lab_order', 'type' => 'input'];
                    $movement_types[] = ['name' => 'lab_order', 'type' => 'output'];
                }

                $business = Business::all();

                foreach ($movement_types as $mt) {

                    foreach ($business as $b) {
                        $mt['business_id'] = $b->id;

                        MovementType::create($mt);
                    }
                }

                \Log::info('--- START TRANSACTIONS ---');

                # Create kardex lines for transactions
                $transactions = Transaction::all();

                foreach ($transactions as $transaction) {

                    switch ($transaction->type) {
                        case 'opening_stock':
                            $this->kardexForTransactions($transaction, 'input', 'OS' . $transaction->id, 'opening_stock');
                            break;

                        case 'sell':
                            if ($transaction->status == 'final') {
                                if (! empty($transaction->document_type)) {
                                    $reference = $transaction->document_type->short_name . $transaction->correlative;
                                } else {
                                    $reference = $transaction->correlative;
                                }
                                $this->kardexForTransactions($transaction, 'output', $reference, 'sell');
                            }
                            break;
                        
                        case 'purchase':
                            if ($transaction->status == 'received') {
                                $this->kardexForTransactions($transaction, 'input', $transaction->ref_no, 'purchase');
                            }
                            break;

                        case 'sell_transfer':
                            $this->kardexForTransactions($transaction, 'output', $transaction->ref_no, 'sell');
                            break;

                        case 'purchase_transfer':
                            $this->kardexForTransactions($transaction, 'input', $transaction->ref_no, 'purchase');
                            break;

                        case 'stock_adjustment':
                            $this->kardexForTransactions($transaction, 'input', $transaction->ref_no, 'stock_adjustment');
                            break;
                        
                        case 'purchase_return':
                            $this->kardexForTransactions($transaction, 'output', $transaction->ref_no, 'purchase_return');
                            break;

                        case 'sell_return':
                            $this->kardexForTransactions($transaction, 'input', $transaction->invoice_no, 'sell_return');
                            break;

                        case 'physical_inventory':
                        case null:
                            if ($transaction->status == 'received') {
                                $physical_inventory = PhysicalInventory::where('code', $transaction->ref_no)->first();
    
                                foreach ($physical_inventory->physical_inventory_lines as $item) {
                                    if ($item->difference > 0) {
                                        $mov_type = 'input';
                                    } else if ($item->difference < 0) {
                                        $mov_type = 'output';
                                    } else {
                                        $mov_type = null;
                                    }
    
                                    $business_id = $physical_inventory->business_id;
                                    $date = $physical_inventory->end_date ?? $physical_inventory->updated_at;
                                    $user_id = $physical_inventory->finished_by;
                    
                                    if (! is_null($mov_type)) {
                                        # Update kardex
                                        $movement_type = MovementType::where('name', 'stock_adjustment')
                                            ->where('type', $mov_type)
                                            ->where('business_id', $business_id)
                                            ->first();
                        
                                        # Check if movement type is set else create it
                                        if (empty($movement_type)) {
                                            $movement_type = MovementType::create([
                                                'name' => 'stock_adjustment',
                                                'type' => $mov_type,
                                                'business_id' => $business_id
                                            ]);
                                        }
                    
                                        # Calculate balance
                                        $balance = $this->transactionUtil->calculateBalance(
                                            $item->product,
                                            $item->variation_id,
                                            $item->difference,
                                            $business_id,
                                            $physical_inventory->location_id,
                                            $physical_inventory->warehouse_id,
                                            $date
                                        );
                    
                                        # Store kardex
                                        $kardex = new Kardex;
                                        $kardex->movement_type_id = $movement_type->id;
                                        $kardex->business_location_id = $physical_inventory->location_id;
                                        $kardex->warehouse_id = $physical_inventory->warehouse_id;
                                        $kardex->product_id = $item->product_id;
                                        $kardex->variation_id = $item->variation_id;
                                        $kardex->physical_inventory_id = $physical_inventory->id;
                                        $kardex->balance = $balance;
                                        $kardex->reference = $physical_inventory->code;
                                        $kardex->date_time = $date;
                                        $kardex->business_id = $business_id;
                                        $kardex->created_by = $user_id;
                                        $kardex->updated_by = $user_id;
                    
                                        if ($movement_type->type == 'input') {
                                            $kardex->inputs_quantity = $this->productUtil->num_uf(abs($item->difference));
                                            $kardex->unit_cost_inputs = $this->productUtil->num_uf($item->variation->default_purchase_price);
                                            $kardex->total_cost_inputs = $this->productUtil->num_uf(abs($item->difference) * $item->variation->default_purchase_price);
                                        } else {
                                            $kardex->outputs_quantity = $this->productUtil->num_uf(abs($item->difference));
                                            $kardex->unit_cost_outputs = $this->productUtil->num_uf($item->variation->default_purchase_price);
                                            $kardex->total_cost_outputs = $this->productUtil->num_uf(abs($item->difference) * $item->variation->default_purchase_price);
                                        }
                    
                                        $kardex->save();
                                    }
                                }
                            }

                            break;
                    }
                }

                \Log::info('--- END ---');

                \Log::info('--- START RECALCULATE BALANCE ---');

                # Recalcule balance
                $kardex = Kardex::all();

                foreach ($kardex as $k) {
                    $balance = $this->transactionUtil->calculateBalance(
                        $k->product,
                        $k->variation_id,
                        0,
                        $k->business_id,
                        $k->business_location_id,
                        $k->warehouse_id,
                        $k->date_time
                    );

                    $k->balance = $balance;
                    $k->save();

                    \Log::info('Record: ' . $k->id);
                }

                \Log::info('--- END ---');

                DB::commit();
        
                $output = [
                    'success' => true,
                    'msg' => __('kardex.kardex_successfully_generated')
                ];

            } catch (\Exception $e) {
                DB::rollBack();
                
                \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    /**
     * Create kardex lines for transactions.
     * 
     * @param  \App\Transaction  $transaction
     * @param  string  $movement
     * @param  string  $reference
     * @param  string  $type
     * 
     * @return void
     */
    public function kardexForTransactions($transaction, $movement, $reference, $type)
    {
        # Data to create kardex lines
        $movement_type = MovementType::where('name', $transaction->type)
            ->where('type', $movement)
            ->where('business_id', $transaction->business_id)
            ->first();

        # Store kardex by type
        switch ($type) {
            case 'sell':
                $lines = TransactionSellLine::where('transaction_id', $transaction->id)->get();
                $this->transactionUtil->createOrUpdateOutputLines($movement_type, $transaction, $reference, $lines, null, 1);
                break;
            
            case 'purchase':
                $lines = PurchaseLine::where('transaction_id', $transaction->id)->get();
                $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $reference, $lines, null, 1);
                break;

            case 'opening_stock':
                $lines = PurchaseLine::where('transaction_id', $transaction->id)->get();
                $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $reference, $lines, null, 1);
                break;
            
            case 'stock_adjustment':
                # Auxiliary movement type
                $movement_type_aux = MovementType::where('name', 'stock_adjustment')
                    ->where('type', 'output')
                    ->where('business_id', $transaction->business_id)
                    ->first();

                if ($transaction->adjustment_type == 'normal') {
                    $lines = PurchaseLine::where('transaction_id', $transaction->id)->get();

                    if (empty($lines)) {
                        $lines = StockAdjustmentLine::where('transaction_id', $transaction->id)->get();
                        $this->transactionUtil->createOrUpdateOutputLines($movement_type_aux, $transaction, $reference, $lines, null, 1, true);

                    } else {
                        $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $reference, $lines, null, 1);
                    }
    
                } else {
                    $old_stock_adjustment = false;

                    $lines = TransactionSellLine::where('transaction_id', $transaction->id)->get();

                    if (empty($lines)) {
                        $lines = StockAdjustmentLine::where('transaction_id', $transaction->id)->get();
                        $old_stock_adjustment = true;
                    }

                    $this->transactionUtil->createOrUpdateOutputLines($movement_type_aux, $transaction, $reference, $lines, null, 1, $old_stock_adjustment);
                }

                break;

            case 'purchase_return':
                $transaction_parent = Transaction::find($transaction->return_parent_id);

                if (! empty($transaction_parent)) {
                    $lines = PurchaseLine::where('transaction_id', $transaction_parent->id)->get();
                    $this->transactionUtil->createOrUpdateOutputLines($movement_type, $transaction, $reference, $lines, null, 1);
                }

                break;

            case 'sell_return':
                $transaction_parent = Transaction::find($transaction->return_parent_id);

                if (! empty($transaction_parent)) {
                    $lines = TransactionSellLine::where('transaction_id', $transaction_parent->id)
                        ->where('quantity_returned', '>', 0)
                        ->get();
                    $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $reference, $lines, null, 1);
                }

                break;
        }
    }

    /**
     * Retrieves products list.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getProducts()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');

            $products = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->where('status', 'active')
                ->where('products.business_id', $business_id)
                ->whereIn('products.clasification', ['product', 'material'])
                ->whereNull('variations.deleted_at');

            # Include search
            if (! empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                });
            }

            $products->select(
                'products.id as product_id',
                'products.name as text',
                'products.type',
                'variations.id as id',
                'variations.name as variation',
                'variations.sub_sku',
                'products.sku'
            );

            $result = $products->orderBy('products.name')->get();

            return json_encode($result);
        }
    }

    /**
     * Generates cost of sale detail report in PDF or Excel.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request)
    {
        if (! auth()->user()->can('kardex.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Params
        $business_id = $request->session()->get('user.business_id');
        $warehouse_id = $request->input('warehouse');
        $variation_id = $request->input('product');
        $size = $request->input('size');
        $report_type = $request->input('report_type');
        $start = null;
        $end = null;

        // Date filter
        if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            $start = $request->input('start_date');
            $end =  $request->input('end_date');
        } else {
            $start = '';
            $end =  '';
        }

        $statement = DB::statement('SET @running_sum = 0');

        // Query
        $kardex = DB::select("
                SELECT
                    k.id,
                    k.date_time,
                    mt.name AS movement_type,
                    mt.type,
                    k.reference,
                    k.transaction_id,
                    k.balance - k.inputs_quantity + k.outputs_quantity AS initial_stock,
                    k.inputs_quantity,
                    k.outputs_quantity,
                    k.balance,
                    k.total_cost_inputs,
                    k.total_cost_outputs,
                    @running_sum := @running_sum + total_cost_inputs - total_cost_outputs AS balance_cost
                FROM kardexes AS k
                LEFT JOIN movement_types AS mt ON k.movement_type_id = mt.id
                WHERE k.business_id = $business_id
                    AND ('$warehouse_id' = 'all' OR k.warehouse_id = $warehouse_id)
                    AND ('$variation_id' = 'all' OR k.variation_id = $variation_id)
                    AND (('$start' = '' AND '$end' = '') OR (DATE(k.date_time) BETWEEN '$start' AND '$end'))
                ORDER BY k.date_time
            ");

        $business = Business::find($business_id);
        $warehouse = Warehouse::find($warehouse_id);
        $variation = Variation::find($variation_id);

        // Generates report
        if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.kardex_report_pdf',
				compact('kardex', 'size', 'start', 'end', 'business', 'warehouse', 'variation'));
            $pdf->setPaper('letter', 'landscape');

			return $pdf->stream(__('kardex.kardex') . '.pdf');

		} else {
			return Excel::download(
                new KardexReportExport($kardex, $start, $end, $business, $warehouse, $variation),
                __('kardex.kardex') . '.xlsx'
            );
		}
    }

    public function printInvoicePurchase($transaction_id, $kardex_id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $taxes = $this->taxUtil->getTaxGroups($business_id, 'products')
                ->pluck('name', 'id');
            $purchase = Transaction::where('business_id', $business_id)
                ->where('id', $transaction_id)
                ->first();

            $purchase_lines = PurchaseLine::where('transaction_id', $purchase->id)->where('quantity', '>', 0)->get();
            
            $kardex = Kardex::leftJoin('movement_types as mt', 'mt.id', 'kardexes.movement_type_id')
                    ->where('kardexes.id', $kardex_id)
                    ->first();
            $payment_methods = $this->productUtil->payment_types();
            $purchase_taxes = $this->taxUtil->getTaxDetailsTransaction($purchase->id);
            //Se busca el nombre del impuesto de la compra
            $name_tax_purchase = "";
            foreach($taxes as $key =>  $t){
                if($key == $purchase->tax_id){
                    $name_tax_purchase = $t;
                }
            }

            /**Percent of tax products */
            $percent = $this->taxUtil->getTaxes($purchase->tax_id);

            $output = ['success' => 1, 'receipt' => []];
            $output['receipt']['html_content'] = view('kardex.partials.show_details_purchase', compact('taxes', 'purchase', 'payment_methods',
                'purchase_taxes', 'name_tax_purchase', 'percent', 'kardex', 'purchase_lines'))->render();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Refresh balance.
     *
     * @param  int  $warehouse_id
     * @param  int  $variation_id
     * @return string
     */
    public function refreshBalance($warehouse_id, $variation_id)
    {
        try {
            if ($warehouse_id != 0 && $variation_id != 0) {
                $warehouse = Warehouse::find($warehouse_id);
    
                $kardex = Kardex::where('business_location_id', $warehouse->business_location_id)
                    ->where('warehouse_id', $warehouse_id)
                    ->where('variation_id', $variation_id)
                    ->orderBy('date_time')
                    ->get();
    
                $prev_item = null;
        
                foreach ($kardex as $item) {
                    if (! is_null($prev_item)) {
                        $item->balance = $prev_item->balance + $item->inputs_quantity - $item->outputs_quantity;
                        $item->save();
    
                    } else {
                        $item->balance = $item->inputs_quantity - $item->outputs_quantity;
                        $item->save();
                    }
        
                    $prev_item = $item;
                }
            }
            $output = [
                'success' => true,
                'msg' => 'Success'
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

    /**
     * Refresh all balances.
     * 
     * @return void
     */
    public function refreshAllBalances()
    {
        try {
            // Set maximum PHP execution time
            ini_set('max_execution_time', 0);

            DB::beginTransaction();

            $warehouses = Warehouse::all();
            $variations = Variation::all();

            \Log::info('--- START ---');

            foreach ($warehouses as $warehouse) {
                foreach ($variations as $variation) {
                    $vld = VariationLocationDetails::where('warehouse_id', $warehouse->id)
                        ->where('variation_id', $variation->id)
                        ->first();

                    if (! empty($vld)) {
                        $this->refreshBalance($warehouse->id, $variation->id);
                    }
                }
            }

            \Log::info('--- END ---');

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    public function createKardexLines($variation_id)
    {
        try {
            DB::beginTransaction();

            $sell_lines = TransactionSellLine::where('variation_id', $variation_id)->get();
            $purchase_lines = PurchaseLine::where('variation_id', $variation_id)->get();

            \Log::info("--- START ---");

            \Log::info("--- PURCHASE LINES ---");

            $i = 1;

            foreach ($purchase_lines as $line) {
                $movement_type = MovementType::where('name', $line->transaction->type)
                    ->where('business_id', $line->transaction->business_id)
                    ->where('type', 'input')
                    ->first();

                if ($movement_type->name == 'sell_return') {
                    $quantity = $line->quantity_returned;
                } else {
                    $quantity = $line->quantity;
                }

                Kardex::create([
                    'movement_type_id' => $movement_type->id,
                    'business_location_id' => $line->transaction->location_id,
                    'warehouse_id' => $line->transaction->warehouse_id,
                    'product_id' => $line->product_id,
                    'variation_id' => $line->variation_id,
                    'transaction_id' => $line->transaction_id,
                    'inputs_quantity' => $quantity,
                    'unit_cost_inputs' => $line->variations->default_purchase_price,
                    'total_cost_inputs' => $quantity * $line->variations->default_purchase_price,
                    'balance' => $quantity, // Fix later
                    'reference' => $this->getReference($line->transaction),
                    'date_time' => $line->transaction->transaction_date,
                    'business_id' => $line->transaction->business_id,
                    'created_by' => $line->transaction->created_by
                ]);

                \Log::info("PL: $i++");
            }

            \Log::info("--- SELL LINES ---");

            $i = 1;

            foreach ($sell_lines as $line) {
                $movement_type = MovementType::where('name', $line->transaction->type)
                    ->where('business_id', $line->transaction->business_id)
                    ->where('type', 'output')
                    ->first();

                if ($movement_type->name == 'purchase_return') {
                    $quantity = $line->quantity_returned;
                } else {
                    $quantity = $line->quantity;
                }

                Kardex::create([
                    'movement_type_id' => $movement_type->id,
                    'business_location_id' => $line->transaction->location_id,
                    'warehouse_id' => $line->transaction->warehouse_id,
                    'product_id' => $line->product_id,
                    'variation_id' => $line->variation_id,
                    'transaction_id' => $line->transaction_id,
                    'outputs_quantity' => $quantity,
                    'unit_cost_outputs' => $line->variations->default_purchase_price,
                    'total_cost_outputs' => $quantity * $line->variations->default_purchase_price,
                    'balance' => $quantity, // Fix later
                    'reference' => $this->getReference($line->transaction),
                    'date_time' => $line->transaction->transaction_date,
                    'business_id' => $line->transaction->business_id,
                    'created_by' => $line->transaction->created_by
                ]);

                \Log::info("SL: $i++");
            }

            \Log::info("--- END ---");

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    public function getReference($transaction)
    {
        switch ($transaction->type) {
            case 'opening_stock':
                $reference = 'OS' . $transaction->id;
                break;

            case 'sell':
                $reference = $transaction->document_type->short_name . $transaction->correlative;
                break;
            
            case 'purchase':
                $reference = $transaction->ref_no;
                break;

            case 'sell_transfer':
                $reference = $transaction->ref_no;
                break;

            case 'purchase_transfer':
                $reference = $transaction->ref_no;
                break;

            case 'stock_adjustment':
                $reference = $transaction->ref_no;
                break;
            
            case 'purchase_return':
                $reference = $transaction->ref_no;
                break;

            case 'sell_return':
                $reference = $transaction->invoice_no;
                break;
        }

        return $reference;
    }

    /**
     * Substitute in the kardex the cost of the variable for the cost of the
     * sale or purchase line.
     * 
     * @param  int  $variation_id
     * @return string
     */
    public function updateCost($variation_id)
    {
        try {
            DB::beginTransaction();

            $kardex_lines = Kardex::where('variation_id', $variation_id)->get();

            foreach ($kardex_lines as $line) {
                if (! is_null($line->transaction_id)) {
                    $movement_type = MovementType::find($line->movement_type_id)->type;

                    if ($movement_type == 'input') {
                        $pl = PurchaseLine::where('variation_id', $variation_id)
                            ->where('transaction_id', $line->transaction_id)
                            ->first();
    
                        $line->unit_cost_inputs = $pl->purchase_price;
                        $line->total_cost_inputs = $pl->purchase_price * $line->inputs_quantity;
                        $line->save();
    
                    } else {
                        $tsl = TransactionSellLine::where('variation_id', $variation_id)
                            ->where('transaction_id', $line->transaction_id)
                            ->first();
    
                        $line->unit_cost_outputs = $tsl->unit_price_before_discount;
                        $line->total_cost_outputs = $tsl->unit_price_before_discount * $line->outputs_quantity;
                        $line->save();
                    }
                }
            }

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    /**
     * Generate kardex of the product in the selected warehouse.
     * 
     * @return json
     */
    public function generateProductKardex()
    {
        if (! auth()->user()->can('kardex.generate_product_kardex')) {
			abort(403, 'Unauthorized action.');
		}

        if (request()->ajax()) {
            try {
                // Set maximum PHP execution time
                ini_set('max_execution_time', 0);

                // Parameters
                $variation_id = request()->input('variation_id', null);
                $warehouse_id = request()->input('warehouse_id', null);
                $business_id = request()->session()->get('user.business_id');

                \Log::info('VARIATION ID: ' . $variation_id);
                \Log::info('WAREHOUSE ID: ' . $warehouse_id);
                \Log::info('BUSINESS ID: ' . $business_id);

                DB::beginTransaction();

                \Log::info('--- START ---');

                // Create movement types
                $movement_types = [
                    ['name' => 'purchase', 'type' => 'input'],
                    ['name' => 'purchase', 'type' => 'output'],

                    ['name' => 'sell', 'type' => 'input'],
                    ['name' => 'sell', 'type' => 'output'],

                    ['name' => 'expense', 'type' => 'input'],
                    ['name' => 'expense', 'type' => 'output'],

                    ['name' => 'stock_adjustment', 'type' => 'input'],
                    ['name' => 'stock_adjustment', 'type' => 'output'],

                    ['name' => 'sell_transfer', 'type' => 'input'],
                    ['name' => 'sell_transfer', 'type' => 'output'],

                    ['name' => 'purchase_transfer', 'type' => 'input'],
                    ['name' => 'purchase_transfer', 'type' => 'output'],

                    ['name' => 'opening_stock', 'type' => 'input'],
                    ['name' => 'opening_stock', 'type' => 'output'],

                    ['name' => 'sell_return', 'type' => 'input'],
                    ['name' => 'sell_return', 'type' => 'output'],

                    ['name' => 'opening_balance', 'type' => 'input'],
                    ['name' => 'opening_balance', 'type' => 'output'],

                    ['name' => 'purchase_return', 'type' => 'input'],
                    ['name' => 'purchase_return', 'type' => 'output'],
                ];

                if (config('app.business') == 'optics') {
                    $movement_types[] = ['name' => 'lab_order', 'type' => 'input'];
                    $movement_types[] = ['name' => 'lab_order', 'type' => 'output'];
                }

                foreach ($movement_types as $mt) {
                    $movement_type = MovementType::where('name', $mt['name'])
                        ->where('type', $mt['type'])
                        ->where('business_id', $business_id)
                        ->first();

                    if (empty($movement_type)) {
                        $mt['business_id'] = $business_id;
                        MovementType::create($mt);
                    }
                }

                $this->__generateProductKardex($variation_id, $warehouse_id, true, true);

                \Log::info('--- END ---');

                DB::commit();
        
                $output = [
                    'success' => 1,
                    'msg' => __('kardex.kardex_successfully_generated')
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

    /**
     * Create kardex lines for transactions.
     * 
     * @param  \App\Transaction  $transaction
     * @param  string  $movement
     * @param  string  $reference
     * @param  string  $type
     * @param  int  $varation_id
     * @return void
     */
    public function kardexForTransactionLines($transaction, $movement, $reference, $type, $variation_id)
    {
        // Data to create kardex lines
        $movement_type = MovementType::where('name', $transaction->type)
            ->where('type', $movement)
            ->where('business_id', $transaction->business_id)
            ->first();

        $kit_ids = KitHasProduct::where('children_id', $variation_id)->pluck('parent_id');

        // Store kardex by type
        switch ($type) {
            case 'sell':
                if (! empty($kit_ids) && count($kit_ids) > 0) {
                    $lines = TransactionSellLine::where('transaction_id', $transaction->id)
                        ->where(function ($query) use ($variation_id, $kit_ids) {
                            $query->where('variation_id', $variation_id)
                                ->orWhereIn('product_id', $kit_ids);
                        })
                        ->get();

                } else {
                    $lines = TransactionSellLine::where('transaction_id', $transaction->id)
                        ->where('variation_id', $variation_id)
                        ->get();
                }

                $this->transactionUtil->createOrUpdateOutputLines($movement_type, $transaction, $reference, $lines, null, 1);

                break;
            
            case 'purchase':
                $lines = PurchaseLine::where('transaction_id', $transaction->id)
                    ->where('variation_id', $variation_id)
                    ->get();

                $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $reference, $lines, null, 1);

                break;

            case 'opening_stock':
                $lines = PurchaseLine::where('transaction_id', $transaction->id)
                    ->where('variation_id', $variation_id)
                    ->get();

                $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $reference, $lines, null, 1);

                break;
            
            case 'stock_adjustment':
                // Auxiliary movement type
                $movement_type_aux = MovementType::where('name', 'stock_adjustment')
                    ->where('type', 'output')
                    ->where('business_id', $transaction->business_id)
                    ->first();

                if ($transaction->adjustment_type == 'normal') {
                    $lines = PurchaseLine::where('transaction_id', $transaction->id)
                        ->where('variation_id', $variation_id)
                        ->get();

                    if (empty($lines) || count($lines) == 0) {
                        $lines = StockAdjustmentLine::where('transaction_id', $transaction->id)
                            ->where('variation_id', $variation_id)
                            ->get();

                        $this->transactionUtil->createOrUpdateOutputLines($movement_type_aux, $transaction, $reference, $lines, null, 1, true);

                    } else {
                        $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $reference, $lines, null, 1);
                    }
    
                } else {
                    $old_stock_adjustment = false;

                    if (! empty($kit_ids) && count($kit_ids) > 0) {
                        $lines = TransactionSellLine::where('transaction_id', $transaction->id)
                            ->where(function ($query) use ($variation_id, $kit_ids) {
                                $query->where('variation_id', $variation_id)
                                    ->orWhereIn('product_id', $kit_ids);
                            })
                            ->get();

                    } else {
                        $lines = TransactionSellLine::where('transaction_id', $transaction->id)
                            ->where('variation_id', $variation_id)
                            ->get();
                    }

                    if (empty($lines) || count($lines) == 0) {
                        $lines = StockAdjustmentLine::where('transaction_id', $transaction->id)
                            ->where('variation_id', $variation_id)
                            ->get();

                        $old_stock_adjustment = true;
                    }

                    $this->transactionUtil->createOrUpdateOutputLines($movement_type_aux, $transaction, $reference, $lines, null, 1, $old_stock_adjustment);
                }

                break;

            case 'purchase_return':
                $transaction_parent = Transaction::find($transaction->return_parent_id);

                if (! empty($transaction_parent)) {
                    $lines = PurchaseLine::where('transaction_id', $transaction_parent->id)
                        ->where('variation_id', $variation_id)
                        ->get();

                    $this->transactionUtil->createOrUpdateOutputLines($movement_type, $transaction, $reference, $lines, null, 1);
                }

                break;

            case 'sell_return':
                $transaction_parent = Transaction::find($transaction->return_parent_id);

                if (! empty($transaction_parent)) {
                    if (! empty($kit_ids) && count($kit_ids) > 0) {
                        $lines = TransactionSellLine::where('transaction_id', $transaction_parent->id)
                            ->where('quantity_returned', '>', 0)
                            ->where(function ($query) use ($variation_id, $kit_ids) {
                                $query->where('variation_id', $variation_id)
                                    ->orWhereIn('product_id', $kit_ids);
                            })
                            ->get();

                    } else {
                        $lines = TransactionSellLine::where('transaction_id', $transaction_parent->id)
                            ->where('quantity_returned', '>', 0)
                            ->where('variation_id', $variation_id)
                            ->get();
                    }

                    $this->transactionUtil->createOrUpdateInputLines($movement_type, $transaction, $reference, $lines, null, 1);
                }

                break;
        }
    }

    public function fixRepeatedTransfer($transfer_id, $warehouse_id, $param_variation_id = null)
    {
        try {
            DB::beginTransaction();

            $warehouse = Warehouse::find($warehouse_id);

            // Identificar productos incluidos en el traslado
            if (is_null($param_variation_id)) {
                $variations = Variation::join('transaction_sell_lines as tsl', 'tsl.variation_id', 'variations.id')
                    ->where('tsl.transaction_id', $transfer_id)
                    ->select('variations.id')
                    ->distinct()
                    ->pluck('id');

            } else {
                $variations = Variation::join('transaction_sell_lines as tsl', 'tsl.variation_id', 'variations.id')
                    ->where('tsl.transaction_id', $transfer_id)
                    ->where('tsl.variation_id', $param_variation_id)
                    ->select('variations.id')
                    ->distinct()
                    ->pluck('id');
            }

            $total_records = 0;
            $total_bad = 0;

            \Log::info("--- START ---");

            // Recorrer cada producto del traslado
            foreach ($variations as $variation_id) {
                $total_records++;

                $variation = Variation::find($variation_id);

                $var_loc_det = VariationLocationDetails::where('variation_id', $variation_id)
                    ->where('warehouse_id', $warehouse_id)
                    ->first();

                if (! $this->areQuantitiesEqual($variation, $warehouse) || $var_loc_det->qty_reserved < 0) {
                    $total_bad++;

                    $kardex = Kardex::where('variation_id', $variation_id)
                        ->where('warehouse_id', $warehouse_id)
                        ->where('transaction_id', $transfer_id)
                        ->get();

                    // Borrar registros de kardex repetidos
                    foreach ($kardex as $i => $k) {
                        if ($i != 0) {
                            $k->delete();
                        }
                    }

                    // Borrar lineas de venta repetidas
                    $sell_lines = TransactionSellLine::join('transactions as t', 't.id', 'transaction_sell_lines.transaction_id')
                        ->where('transaction_sell_lines.variation_id', $variation_id)
                        ->where('t.warehouse_id', $warehouse_id)
                        ->where('transaction_sell_lines.transaction_id', $transfer_id)
                        ->select('transaction_sell_lines.*')
                        ->get();

                    foreach ($sell_lines as $j => $sl) {
                        if ($j != 0) {
                            TransactionSellLine::find($sl->id)->delete();
                        }
                    }

                    $qty_sold = TransactionSellLine::join('transactions as t', 't.id', 'transaction_sell_lines.transaction_id')
                        ->where('transaction_sell_lines.variation_id', $variation_id)
                        ->where('t.warehouse_id', $warehouse_id)
                        ->sum('transaction_sell_lines.quantity');

                    $purchase_lines = PurchaseLine::join('transactions as t', 't.id', 'purchase_lines.transaction_id')
                        ->where('purchase_lines.variation_id', $variation_id)
                        ->where('t.warehouse_id', $warehouse_id);

                    $qty_sold_pl = $purchase_lines->sum('purchase_lines.quantity_sold');

                    $purchase_lines = $purchase_lines->select('purchase_lines.*')
                        ->orderBy('purchase_lines.id', 'desc')
                        ->get();

                    if ($qty_sold != $qty_sold_pl) {
                        $difference = $qty_sold - $qty_sold_pl;

                        foreach ($purchase_lines as $pl) {
                            if ($difference > 0) {
                                if (($pl->quantity - $pl->quantity_sold) >= $difference) {
                                    $update_pl = PurchaseLine::find($pl->id);
                                    $update_pl->quantity_sold += $difference;
                                    $update_pl->save();

                                    $difference = 0;

                                } else {
                                    $difference -= ($pl->quantity - $pl->quantity_sold);
                                    
                                    $update_pl = PurchaseLine::find($pl->id);
                                    $update_pl->quantity_sold = $pl->quantity;
                                    $update_pl->save();
                                }

                            } else if ($difference < 0) {
                                if ($pl->quantity_sold >= ($difference * -1)) {
                                    $update_pl = PurchaseLine::find($pl->id);
                                    $update_pl->quantity_sold += $difference;
                                    $update_pl->save();

                                    $difference = 0;

                                } else {
                                    $difference += $pl->quantity_sold;

                                    $update_pl = PurchaseLine::find($pl->id);
                                    $update_pl->quantity_sold = 0;
                                    $update_pl->save();
                                }
                            }
                        }
                    }

                    $this->refreshBalance($warehouse_id, $variation_id);

                    // Actualizar cantidad de variation_location_details
                    $new_kardex = Kardex::where('variation_id', $variation_id)
                        ->where('warehouse_id', $warehouse_id)
                        ->orderBy('date_time', 'desc')
                        ->first();

                    $vld = VariationLocationDetails::where('variation_id', $variation_id)
                        ->where('warehouse_id', $warehouse_id)
                        ->first();

                    $vld->qty_available = $new_kardex->balance;
                    $vld->save();

                    // Actualizar cantidad reservada a cero
                    if ($vld->qty_reserved < 0) {
                        $vld->qty_reserved = 0;
                        $vld->save();
                    }

                    \Log::info('RECORD: sku -> ' . $variation->sub_sku . ' variation_id -> ' . $variation->id . ' location_id -> ' . $warehouse->business_location_id . ' warehouse_id -> ' . $warehouse->id);

                    $print_pl = PurchaseLine::join('transactions as t', 't.id', 'purchase_lines.transaction_id')
                        ->where('purchase_lines.variation_id', $variation_id)
                        ->where('t.warehouse_id', $warehouse_id);

                    $print_tsl = TransactionSellLine::join('transactions as t', 't.id', 'transaction_sell_lines.transaction_id')
                        ->where('transaction_sell_lines.variation_id', $variation_id)
                        ->where('t.warehouse_id', $warehouse_id);

                    \Log::info('PURCHASE LINE: ' . $print_pl->sum('purchase_lines.quantity') . ' - ' . $print_pl->sum('purchase_lines.quantity_sold') . ' SELL LINE: ' . $print_tsl->sum('transaction_sell_lines.quantity'));
                }
            }

            \Log::info("--- END ---");

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;    
    }

    /**
     * Compare the current stock of a product with the last record in the kardex.
     * 
     * @return string
     */
    public function compareStockAndKardex()
    {
        // Set maximum PHP execution time
        ini_set('max_execution_time', 0);

        $warehouses = Warehouse::all();

        $variations = Variation::all();

        foreach ($warehouses as $warehouse) {
            foreach ($variations as $variation) {
                $vld = VariationLocationDetails::where('variation_id', $variation->id)
                    ->where('location_id', $warehouse->business_location_id)
                    ->where('warehouse_id', $warehouse->id)
                    ->first();

                $kardex = Kardex::where('variation_id', $variation->id)
                    ->where('business_location_id', $warehouse->business_location_id)
                    ->where('warehouse_id', $warehouse->id)
                    ->orderBy('date_time', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if (! empty($vld) && ! empty($kardex)) {
                    if ($vld->qty_available != $kardex->balance) {
                        \Log::info('ERROR: sku -> ' . $variation->sub_sku . ' variation_id -> ' . $variation->id . ' location_id -> ' . $warehouse->business_location_id . ' warehouse_id -> ' . $warehouse->id);
                    }
                }
            }
        }

        return 'SUCCESS';
    }

    /**
     * Compare the current stock of a product with the last record in the
     * kardex and match purchase and sales.
     * 
     * @return string
     */
    public function compareStockAndKardexStrict()
    {
        // Set maximum PHP execution time
        ini_set('max_execution_time', 0);

        $warehouses = Warehouse::all();

        $variations = Variation::all();

        foreach ($warehouses as $warehouse) {
            foreach ($variations as $variation) {
                $vld = VariationLocationDetails::where('variation_id', $variation->id)
                    ->where('location_id', $warehouse->business_location_id)
                    ->where('warehouse_id', $warehouse->id)
                    ->first();

                $kardex = Kardex::where('variation_id', $variation->id)
                    ->where('business_location_id', $warehouse->business_location_id)
                    ->where('warehouse_id', $warehouse->id)
                    ->orderBy('date_time', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $calculated = $this->calculateStock($variation->id, $warehouse->id);

                if (! empty($vld) && ! empty($kardex) && ! empty($calculated)) {
                    if ($vld->qty_available != $calculated || $kardex->balance != $calculated) {
                        \Log::info('ERROR: sku -> ' . $variation->sub_sku . ' variation_id -> ' . $variation->id . ' location_id -> ' . $warehouse->business_location_id . ' warehouse_id -> ' . $warehouse->id);
                        \Log::info('--- vld -> ' . $vld->qty_available . ' kardex -> ' . $kardex->balance . ' calculated -> ' . $calculated . ' ---');
                    }
                }
            }
        }

        return 'SUCCESS';
    }

    /**
     * Calculate stock with purchase, sales and stock adjustment lines.
     * 
     * @param  int  $variation_id
     * @param  int  $warehouse_id
     * @return float
     */
    public function calculateStock($variation_id, $warehouse_id)
    {
        $kit_ids = KitHasProduct::where('children_id', $variation_id)->pluck('parent_id');

        $pl = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
            ->where('transactions.warehouse_id', $warehouse_id)
            ->where('purchase_lines.variation_id', $variation_id)
            ->where(function ($query) use ($variation_id, $kit_ids) {
                $query->where('purchase_lines.variation_id', $variation_id)
                    ->orWhereIn('purchase_lines.product_id', $kit_ids);
            })
            ->sum('purchase_lines.quantity');

        $tsl = TransactionSellLine::join('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->where('transactions.warehouse_id', $warehouse_id)
            ->where(function ($query) use ($variation_id, $kit_ids) {
                $query->where('transaction_sell_lines.variation_id', $variation_id)
                    ->orWhereIn('transaction_sell_lines.product_id', $kit_ids);
            })
            ->where('transactions.status', '!=', 'annulled')
            ->sum('transaction_sell_lines.quantity');

        if (config('app.business') == 'optics') {
            $sal = StockAdjustmentLine::join('transactions', 'transactions.id', 'stock_adjustment_lines.transaction_id')
                ->where('transactions.warehouse_id', $warehouse_id)
                ->where(function ($query) use ($variation_id, $kit_ids) {
                    $query->where('stock_adjustment_lines.variation_id', $variation_id)
                        ->orWhereIn('stock_adjustment_lines.product_id', $kit_ids);
                })
                ->sum('stock_adjustment_lines.quantity');

            $lod = LabOrderDetail::where('warehouse_id', $warehouse_id)
                ->where('variation_id', $variation_id)
                ->sum('quantity');

        } else {
            $sal = 0;
            $lod = 0;
        }

        $stock = $pl - $tsl - $sal - $lod;

        return $stock;
    }

    /**
     * Create kardex lines for lab orders.
     * 
     * @param  \App\LabOrder  $lab_order
     * 
     * @return void
     */
    public function kardexForLabOrders($lab_order)
    {
        /** Data to create kardex lines */
        $lines = LabOrderDetail::where('lab_order_id', $lab_order->id)->get();
        
        /** Store kardex */
        $this->transactionUtil->createOrUpdateLabOrderLines(
            $lab_order->transaction_id,
            $lab_order->no_order,
            $lines,
            null,
            1
        );
    }

    /**
     * Create kardex lines for lab orders.
     * 
     * @param  \App\LabOrder  $lab_order
     * @return void
     */
    public function kardexForLabOrderLines($lab_order, $warehouse_id, $variation_id)
    {
        // Data to create kardex lines
        $lines = LabOrderDetail::where('lab_order_id', $lab_order->id)
            ->where('warehouse_id', $warehouse_id)
            ->where('variation_id', $variation_id)
            ->get();
        
        // Store kardex
        $this->transactionUtil->createOrUpdateLabOrderLines(
            $lab_order->transaction_id,
            $lab_order->no_order,
            $lines,
            null,
            1
        );
    }

    /**
     * Compare the current stock of a product with the last record in the kardex.
     * 
     * @return string
     */
    public function compareAndGenerateProductKardex2()
    {
        try {
            // Set maximum PHP execution time
            ini_set('max_execution_time', 0);

            $business_id = request()->session()->get('user.business_id');

            // Create movement types
            $movement_types = [
                ['name' => 'purchase', 'type' => 'input'],
                ['name' => 'purchase', 'type' => 'output'],

                ['name' => 'sell', 'type' => 'input'],
                ['name' => 'sell', 'type' => 'output'],

                ['name' => 'expense', 'type' => 'input'],
                ['name' => 'expense', 'type' => 'output'],

                ['name' => 'stock_adjustment', 'type' => 'input'],
                ['name' => 'stock_adjustment', 'type' => 'output'],

                ['name' => 'sell_transfer', 'type' => 'input'],
                ['name' => 'sell_transfer', 'type' => 'output'],

                ['name' => 'purchase_transfer', 'type' => 'input'],
                ['name' => 'purchase_transfer', 'type' => 'output'],

                ['name' => 'opening_stock', 'type' => 'input'],
                ['name' => 'opening_stock', 'type' => 'output'],

                ['name' => 'sell_return', 'type' => 'input'],
                ['name' => 'sell_return', 'type' => 'output'],

                ['name' => 'opening_balance', 'type' => 'input'],
                ['name' => 'opening_balance', 'type' => 'output'],

                ['name' => 'purchase_return', 'type' => 'input'],
                ['name' => 'purchase_return', 'type' => 'output'],
            ];

            if (config('app.business') == 'optics') {
                $movement_types[] = ['name' => 'lab_order', 'type' => 'input'];
                $movement_types[] = ['name' => 'lab_order', 'type' => 'output'];
            }

            foreach ($movement_types as $mt) {
                $movement_type = MovementType::where('name', $mt['name'])
                    ->where('type', $mt['type'])
                    ->where('business_id', $business_id)
                    ->first();

                if (empty($movement_type)) {
                    $mt['business_id'] = $business_id;
                    MovementType::create($mt);
                }
            }

            $warehouses = Warehouse::all();

            $variations = Variation::all();

            foreach ($warehouses as $warehouse) {
                foreach ($variations as $variation) {
                    if (! $this->areQuantitiesEqual($variation, $warehouse)) {
                        $this->__generateProductKardex($variation->id, $warehouse->id);

                        if (! $this->areQuantitiesEqual($variation, $warehouse)) {
                            \Log::info('ERROR: variation_id -> ' . $variation->id . ' location_id -> ' . $warehouse->business_location_id . ' warehouse_id -> ' . $warehouse->id);
                        }
                    }
                }
            }

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    public function areQuantitiesEqual($variation, $warehouse)
    {
        $vld = VariationLocationDetails::where('variation_id', $variation->id)
            ->where('location_id', $warehouse->business_location_id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        $kardex = Kardex::where('variation_id', $variation->id)
            ->where('business_location_id', $warehouse->business_location_id)
            ->where('warehouse_id', $warehouse->id)
            ->orderBy('date_time', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if (! empty($vld) && ! empty($kardex)) {
            $result = $vld->qty_available == $kardex->balance ? true : false;
        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * Generate kardex of the product in the selected warehouse.
     * 
     * @param  int  $variation_id
     * @param  int  $warehouse_id
     * @param  bool  $update_vld
     * @param  bool  $show_messages
     * @return void
     */
    public function __generateProductKardex($variation_id, $warehouse_id, $update_vld = true, $show_messages = false)
    {
        $business_id = request()->session()->get('user.business_id');

        // Delete kardex lines
        DB::table('kardexes')->where('variation_id', $variation_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('business_id', $business_id)
            ->delete();

        if ($show_messages) { \Log::info('--- START TRANSACTIONS ---'); }

        // Create kardex lines for transactions
        $transactions = Transaction::where('warehouse_id', $warehouse_id)->get();

        $kit_ids = KitHasProduct::where('children_id', $variation_id)->pluck('parent_id');

        $transaction_sells = Transaction::join('transaction_sell_lines as tsl', 'tsl.transaction_id', 'transactions.id')
            ->where('transactions.warehouse_id', $warehouse_id)
            ->where(function ($query) use ($variation_id, $kit_ids) {
                $query->where('tsl.variation_id', $variation_id)
                    ->orWhereIn('tsl.product_id', $kit_ids);
            })
            ->select('transactions.*')
            ->groupBy('transactions.id')
            ->get();

        $transaction_purchases = Transaction::join('purchase_lines as pl', 'pl.transaction_id', 'transactions.id')
            ->where('transactions.warehouse_id', $warehouse_id)
            ->where(function ($query) use ($variation_id, $kit_ids) {
                $query->where('pl.variation_id', $variation_id)
                    ->orWhereIn('pl.product_id', $kit_ids);
            })
            ->select('transactions.*')
            ->groupBy('transactions.id')
            ->get();

        $transaction_adjustments = Transaction::join('stock_adjustment_lines as sal', 'sal.transaction_id', 'transactions.id')
            ->where('transactions.warehouse_id', $warehouse_id)
            ->where(function ($query) use ($variation_id, $kit_ids) {
                $query->where('sal.variation_id', $variation_id)
                    ->orWhereIn('sal.product_id', $kit_ids);
            })
            ->select('transactions.*')
            ->groupBy('transactions.id')
            ->get();

        $transactions = collect();

        foreach ($transaction_sells as $item) {
            $transactions->push($item);
        }

        foreach ($transaction_purchases as $item) {
            $transactions->push($item);
        }

        foreach ($transaction_adjustments as $item) {
            $transactions->push($item);
        }

        $transactions = $transactions->sortBy('id');

        foreach ($transactions as $transaction) {
            switch ($transaction->type) {
                case 'opening_stock':
                    $this->kardexForTransactionLines($transaction, 'input', 'OS' . $transaction->id, 'opening_stock', $variation_id);
                    break;

                case 'sell':
                    if ($transaction->status == 'final') {
                        if (! empty($transaction->document_type)) {
                            $reference = $transaction->document_type->short_name . $transaction->correlative;
                        } else {
                            $reference = $transaction->correlative;
                        }
                        $this->kardexForTransactionLines($transaction, 'output', $reference, 'sell', $variation_id);
                    }
                    break;
                
                case 'purchase':
                    if ($transaction->status == 'received') {
                        $this->kardexForTransactionLines($transaction, 'input', $transaction->ref_no, 'purchase', $variation_id);
                    }
                    break;

                case 'sell_transfer':
                    $this->kardexForTransactionLines($transaction, 'output', $transaction->ref_no, 'sell', $variation_id);
                    break;

                case 'purchase_transfer':
                    $this->kardexForTransactionLines($transaction, 'input', $transaction->ref_no, 'purchase', $variation_id);
                    break;

                case 'stock_adjustment':
                    $this->kardexForTransactionLines($transaction, 'input', $transaction->ref_no, 'stock_adjustment', $variation_id);
                    break;
                
                case 'purchase_return':
                    $this->kardexForTransactionLines($transaction, 'output', $transaction->ref_no, 'purchase_return', $variation_id);
                    break;

                case 'sell_return':
                    $this->kardexForTransactionLines($transaction, 'input', $transaction->invoice_no, 'sell_return', $variation_id);
                    break;

                case 'physical_inventory':
                case null:
                    if ($transaction->status == 'received') {
                        $physical_inventory = PhysicalInventory::where('code', $transaction->ref_no)->first();

                        $physical_inventory_lines = PhysicalInventoryLine::where('physical_inventory_id', $physical_inventory->id)
                            ->where('variation_id', $variation_id)
                            ->get();

                        foreach ($physical_inventory_lines as $item) {
                            if ($item->difference > 0) {
                                $mov_type = 'input';
                            } else if ($item->difference < 0) {
                                $mov_type = 'output';
                            } else {
                                $mov_type = null;
                            }

                            $business_id = $physical_inventory->business_id;
                            $date = $physical_inventory->end_date ?? $physical_inventory->updated_at;
                            $user_id = $physical_inventory->finished_by;
            
                            if (! is_null($mov_type)) {
                                // Update kardex
                                $movement_type = MovementType::where('name', 'stock_adjustment')
                                    ->where('type', $mov_type)
                                    ->where('business_id', $business_id)
                                    ->first();
                
                                // Check if movement type is set else create it
                                if (empty($movement_type)) {
                                    $movement_type = MovementType::create([
                                        'name' => 'stock_adjustment',
                                        'type' => $mov_type,
                                        'business_id' => $business_id
                                    ]);
                                }
            
                                // Calculate balance
                                $balance = $this->transactionUtil->calculateBalance(
                                    $item->product,
                                    $item->variation_id,
                                    $item->difference,
                                    $business_id,
                                    $physical_inventory->location_id,
                                    $physical_inventory->warehouse_id,
                                    $date
                                );
            
                                // Store kardex
                                $kardex = new Kardex;
                                $kardex->movement_type_id = $movement_type->id;
                                $kardex->business_location_id = $physical_inventory->location_id;
                                $kardex->warehouse_id = $physical_inventory->warehouse_id;
                                $kardex->product_id = $item->product_id;
                                $kardex->variation_id = $item->variation_id;
                                $kardex->physical_inventory_id = $physical_inventory->id;
                                $kardex->balance = $balance;
                                $kardex->reference = $physical_inventory->code;
                                $kardex->date_time = $date;
                                $kardex->business_id = $business_id;
                                $kardex->created_by = $user_id;
                                $kardex->updated_by = $user_id;
            
                                if ($movement_type->type == 'input') {
                                    $kardex->inputs_quantity = $this->productUtil->num_uf(abs($item->difference));
                                    $kardex->unit_cost_inputs = $this->productUtil->num_uf($item->variation->default_purchase_price);
                                    $kardex->total_cost_inputs = $this->productUtil->num_uf(abs($item->difference) * $item->variation->default_purchase_price);
                                } else {
                                    $kardex->outputs_quantity = $this->productUtil->num_uf(abs($item->difference));
                                    $kardex->unit_cost_outputs = $this->productUtil->num_uf($item->variation->default_purchase_price);
                                    $kardex->total_cost_outputs = $this->productUtil->num_uf(abs($item->difference) * $item->variation->default_purchase_price);
                                }
            
                                $kardex->save();
                            }
                        }
                    }

                    break;
            }

            if ($show_messages) { \Log::info('TRANSACTION: ' . $transaction->id); }
        }

        if ($show_messages) { \Log::info('--- END TRANSACTIONS ---'); }

        if (config('app.business') == 'optics') {
            if ($show_messages) { \Log::info('--- START LAB ORDERS ---'); }
            
            // Create kardex lines for lab orders
            $lab_orders = LabOrder::join('lab_order_details as lod', 'lod.lab_order_id', 'lab_orders.id')
                ->where('lod.warehouse_id', $warehouse_id)
                ->where('lod.variation_id', $variation_id)
                ->select('lab_orders.*')
                ->groupBy('lab_orders.id')
                ->get();

            foreach ($lab_orders as $lab_order) {
                $this->kardexForLabOrderLines($lab_order, $warehouse_id, $variation_id);
                if ($show_messages) { \Log::info('LAB ORDER: ' . $lab_order->id); }
            }

            if ($show_messages) { \Log::info('--- END LAB ORDERS ---'); }
        }

        if ($show_messages) { \Log::info('--- START RECALCULATE BALANCE ---'); }

        // Recalcule balance
        $warehouse = Warehouse::find($warehouse_id);
    
        $kardex = Kardex::where('business_location_id', $warehouse->business_location_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('variation_id', $variation_id)
            ->orderBy('date_time')
            ->get();

        $prev_item = null;

        foreach ($kardex as $item) {
            if (! is_null($prev_item)) {
                $item->balance = $prev_item->balance + $item->inputs_quantity - $item->outputs_quantity;
                $item->save();

            } else {
                $item->balance = $item->inputs_quantity - $item->outputs_quantity;
                $item->save();
            }

            $prev_item = $item;
        }

        if ($show_messages) { \Log::info('--- END RECALCULATE BALANCE ---'); }

        // Update variation_location_details record
        if ($update_vld) {
            $stock = Kardex::where('variation_id', $variation_id)
                ->where('warehouse_id', $warehouse_id)
                ->orderBy('date_time', 'desc')
                ->first();
    
            $vld = VariationLocationDetails::where('warehouse_id', $warehouse_id)
                ->where('variation_id', $variation_id)
                ->first();
    
            $vld->qty_available = $stock->balance;
            $vld->save();
        }
    }

    /**
     * Compare the current stock of a product with the last record in the kardex.
     * 
     * @return string
     */
    public function compareAndGenerateProductKardex($warehouse_id = null, $variation_initial = null, $variation_final = null)
    {
        try {
            // Set maximum PHP execution time
            ini_set('max_execution_time', 0);

            if (is_null($warehouse_id)) {
                $warehouses = Warehouse::all();
            } else {
                $warehouses = Warehouse::where('id', $warehouse_id)->get();
            }

            if (is_null($variation_initial) || is_null($variation_final)) {
                $variations = Variation::all();
            } else {
                $variations = Variation::whereBetween('id', [$variation_initial, $variation_final])->get();
            }

            $total_records = 0;
            $total_bad = 0;
            $total_fix = 0;

            DB::beginTransaction();

            \Log::debug('--- START ---');

            foreach ($warehouses as $warehouse) {
                foreach ($variations as $variation) {
                    $total_records++;

                    if (! $this->areQuantitiesEqual($variation, $warehouse)) {
                        $total_bad++;

                        $this->__generateProductKardex($variation->id, $warehouse->id);

                        if (! $this->areQuantitiesEqual($variation, $warehouse)) {
                            \Log::info('ERROR: sku -> ' . $variation->sub_sku . ' variation_id -> ' . $variation->id . ' location_id -> ' . $warehouse->business_location_id . ' warehouse_id -> ' . $warehouse->id);

                        } else {
                            $total_fix++;
                        }
                    }
                }
            }

            \Log::info('TOTAL RECORDS: ' . $total_records);
            \Log::info('TOTAL BAD: ' . $total_bad);
            \Log::info('TOTAL FIX: ' . $total_fix);

            \Log::debug('--- END ---');

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    /**
     * Compare the current stock of a product with the last record in the kardex.
     * 
     * @return string
     */
    public function compareAndRefreshBalance($warehouse_id = null, $variation_initial = null, $variation_final = null)
    {
        try {
            // Set maximum PHP execution time
            ini_set('max_execution_time', 0);

            if (is_null($warehouse_id)) {
                $warehouses = Warehouse::all();
            } else {
                $warehouses = Warehouse::where('id', $warehouse_id)->get();
            }

            if (is_null($variation_initial) || is_null($variation_final)) {
                $variations = Variation::all();
            } else {
                $variations = Variation::whereBetween('id', [$variation_initial, $variation_final])->get();
            }

            $total_records = 0;
            $total_bad = 0;
            $total_fix = 0;

            DB::beginTransaction();

            \Log::debug('--- START ---');

            foreach ($warehouses as $warehouse) {
                foreach ($variations as $variation) {
                    $total_records++;

                    if (! $this->areQuantitiesEqual($variation, $warehouse)) {
                        $total_bad++;

                        $this->refreshBalance($warehouse->id, $variation->id);

                        if (! $this->areQuantitiesEqual($variation, $warehouse)) {
                            \Log::info('ERROR: sku -> ' . $variation->sub_sku . ' variation_id -> ' . $variation->id . ' location_id -> ' . $warehouse->business_location_id . ' warehouse_id -> ' . $warehouse->id);

                        } else {
                            $total_fix++;
                        }
                    }
                }
            }

            \Log::info('TOTAL RECORDS: ' . $total_records);
            \Log::info('TOTAL BAD: ' . $total_bad);
            \Log::info('TOTAL FIX: ' . $total_fix);

            \Log::debug('--- END ---');

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    public function fixVariationLocationDetail($warehouse_id = null, $variation_initial = null, $variation_final = null)
    {
        try {
            // Set maximum PHP execution time
            ini_set('max_execution_time', 0);

            if (is_null($warehouse_id)) {
                $warehouses = Warehouse::all();
            } else {
                $warehouses = Warehouse::where('id', $warehouse_id)->get();
            }

            if (is_null($variation_initial) || is_null($variation_final)) {
                $variations = Variation::all();
            } else {
                $variations = Variation::whereBetween('id', [$variation_initial, $variation_final])->get();
            }

            $total_records = 0;
            $total_bad = 0;
            $total_fix = 0;

            DB::beginTransaction();

            \Log::info('--- START ---');

            foreach ($warehouses as $warehouse) {
                foreach ($variations as $variation) {
                    $total_records++;

                    if (! $this->areQuantitiesEqual($variation, $warehouse)) {
                        $total_bad++;

                        $kit_ids = KitHasProduct::where('children_id', $variation->id)->pluck('parent_id');

                        $pl = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
                            ->where('transactions.warehouse_id', $warehouse->id)
                            ->where('purchase_lines.variation_id', $variation->id)
                            ->where(function ($query) use ($variation, $kit_ids) {
                                $query->where('purchase_lines.variation_id', $variation->id)
                                    ->orWhereIn('purchase_lines.product_id', $kit_ids);
                            })
                            ->sum('purchase_lines.quantity');

                        $tsl = TransactionSellLine::join('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->where('transactions.warehouse_id', $warehouse->id)
                            ->where(function ($query) use ($variation, $kit_ids) {
                                $query->where('transaction_sell_lines.variation_id', $variation->id)
                                    ->orWhereIn('transaction_sell_lines.product_id', $kit_ids);
                            })
                            ->where('transactions.status', '!=', 'annulled')
                            ->sum('transaction_sell_lines.quantity');

                        if (config('app.business') == 'optics') {
                            $sal = StockAdjustmentLine::join('transactions', 'transactions.id', 'stock_adjustment_lines.transaction_id')
                                ->where('transactions.warehouse_id', $warehouse->id)
                                ->where('stock_adjustment_lines.variation_id', $variation->id)
                                ->sum('stock_adjustment_lines.quantity');

                            $lod = LabOrderDetail::where('warehouse_id', $warehouse->id)
                                ->where('variation_id', $variation->id)
                                ->sum('quantity');

                        } else {
                            $sal = 0;
                            $lod = 0;
                        }

                        $stock = $pl - $tsl - $sal - $lod;

                        \Log::info("CANTIDADES: $pl -- $tsl -- $sal -- $lod");

                        $kardex = Kardex::where('variation_id', $variation->id)
                            ->where('warehouse_id', $warehouse->id)
                            ->orderBy('date_time', 'desc')
                            ->first();

                        \Log::info('Stock: ' . $stock . ' - Kardex: ' . $kardex->balance);

                        if ($stock == $kardex->balance) {
                            $vld = VariationLocationDetails::where('warehouse_id', $warehouse->id)
                                ->where('variation_id', $variation->id)
                                ->first();

                            $vld->qty_available = $stock;
                            $vld->save();
                        }

                        if (! $this->areQuantitiesEqual($variation, $warehouse)) {
                            \Log::info('ERROR: variation_id -> ' . $variation->id . ' location_id -> ' . $warehouse->business_location_id . ' warehouse_id -> ' . $warehouse->id);

                        } else {
                            $total_fix++;
                        }
                    }
                }
            }

            \Log::info('TOTAL RECORDS: ' . $total_records);
            \Log::info('TOTAL BAD: ' . $total_bad);
            \Log::info('TOTAL FIX: ' . $total_fix);

            \Log::info("--- END ---");

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }
    
        return $output; 
    }

    /**
     * Store records from table stock_adjustment_lines in kardex.
     * 
     * @param  int  $variation_id
     * @param  int  $location_id
     * @param  int  $warehouse_id
     */
    public function fixStockAdjustments($variation_id, $location_id, $warehouse_id)
    {
        try {
            DB::beginTransaction();

            $transactions = Transaction::join('stock_adjustment_lines as sal', 'transactions.id', 'sal.transaction_id')
                ->where('sal.variation_id', $variation_id)
                ->where('transactions.location_id', $location_id)
                ->where('transactions.warehouse_id', $warehouse_id)
                ->select('transactions.*')
                ->get();

            foreach ($transactions as $transaction) {
                $movement_type = MovementType::where('name', 'stock_adjustment')
                    ->where('type', 'output')
                    ->where('business_id', $transaction->business_id)
                    ->first();

                $lines = StockAdjustmentLine::where('transaction_id', $transaction->id)
                    ->where('variation_id', $variation_id)
                    ->get();
                
                $this->transactionUtil->createOrUpdateOutputLines($movement_type, $transaction, $transaction->ref_no, $lines, null, 1);
            }

            DB::commit();

            return 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            return 'FAIL';
        }
    }

    /**
     * Show the form for recalculate cost.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getRecalculateCost()
    {
        return view('kardex.recalculate-kardex-cost');
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

        // Set maximum PHP execution time
        ini_set('max_execution_time', 0);

        $variation = Variation::find($variation_id);
        $product = Product::find($variation->product_id);

        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                \Log::info('--- VARIATION: ' . $variation_id . ' ---');

                // Calculate costs
                $business_id = $product->business_id;

                $purchases = Transaction::join('purchase_lines', 'purchase_lines.transaction_id', 'transactions.id')
                    ->where(function ($query) {
                        $query->whereIn('transactions.type', ['opening_stock', 'purchase'])
                            ->orWhere('transactions.type', 'stock_adjustment')
                            ->where('transactions.adjustment_type', 'abnormal');
                    })
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

                $array = [];

                foreach ($purchases as $purchase) {
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
                                if (($stock + $purchase_line->quantity) != 0) {
                                    $variation->default_purchase_price = (($purchase_price * $stock) + ($purchase_line_purchase_price * $purchase_line->quantity)) / ($stock + $purchase_line->quantity);
                                } else {
                                    $variation->default_purchase_price = $purchase_line_purchase_price;
                                }
                        
                                // Set default purchase price inc. tax
                                $variation->dpp_inc_tax = $this->productUtil->calc_percentage($variation->default_purchase_price, $tax_rate, $variation->default_purchase_price);

                                // Set profit margin
                                $variation->profit_percent = $this->productUtil->get_percent($variation->default_purchase_price, $variation->default_sell_price);

                                // Only if prices are wrong
                                // if ($product->tax_type == 'inclusive') {
                                //     $variation->default_sell_price = $this->productUtil->calc_percentage_base($variation->sell_price_inc_tax, $tax_rate);
                                // } else {
                                //     $variation->sell_price_inc_tax = $this->productUtil->calc_percentage($variation->default_sell_price, $tax_rate, $variation->default_sell_price);
                                // }

                                $variation->save();

                                $purchase_price = $variation->default_purchase_price;

                                $data = [
                                    'variation_id' => $variation_id,
                                    'date' => $transaction_date,
                                    'avg_unit_cost_exc_tax' => $variation->default_purchase_price,
                                    'avg_unit_cost_inc_tax' => $variation->dpp_inc_tax,
                                    'unit_cost_exc_tax' => $variation->default_purchase_price,
                                    'unit_cost_inc_tax' => $variation->dpp_inc_tax
                                ];

                                array_push($array, $data);
                            }
                        }
                    }
                }

                // Save costs in transaction_sell_lines and kardex of sales records
                for ($i = 0; $i < count($array); $i++) {
                    $unit_cost_exc_tax = $this->productUtil->num_f($array[$i]['unit_cost_exc_tax'], false, 6);
                    $unit_cost_inc_tax = $this->productUtil->num_f($array[$i]['unit_cost_inc_tax'], false, 6);

                    $transaction_sell_lines = TransactionSellLine::join('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
                        ->where('transactions.business_id', $business_id)
                        ->whereIn('transactions.type', ['sell', 'sell_transfer', 'stock_adjustment'])
                        ->where('transaction_sell_lines.variation_id', $variation_id);

                    $physical_inventory_lines = PhysicalInventoryLine::join('physical_inventories', 'physical_inventories.id', 'physical_inventory_lines.physical_inventory_id')
                        ->where('physical_inventories.business_id', $business_id)
                        ->where('physical_inventory_lines.variation_id', $variation_id);

                    $purchase_lines = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
                        ->where('transactions.business_id', $business_id)
                        ->where(function ($query) {
                            $query->where('transactions.type', 'purchase_transfer')
                                ->orWhere('transactions.type', 'stock_adjustment');
                        })
                        ->where('purchase_lines.variation_id', $variation_id);

                    if ($i == count($array) - 1 && count($array) >= 1) {
                        $transaction_sell_lines = $transaction_sell_lines->where('transactions.transaction_date', '>=', $array[$i]['date']);
                        $physical_inventory_lines = $physical_inventory_lines->whereRaw('physical_inventories.end_date >= DATE(?)', [$array[$i]['date']]);
                        $purchase_lines = $purchase_lines->where('transactions.transaction_date', '>=', $array[$i]['date']);

                    } else {
                        $transaction_sell_lines = $transaction_sell_lines->whereBetween('transactions.transaction_date', [$array[$i]['date'], $array[$i + 1]['date']]);
                        $physical_inventory_lines = $physical_inventory_lines->whereRaw('physical_inventories.end_date BETWEEN DATE(?) AND DATE(?)', [$array[$i]['date'], $array[$i + 1]['date']]);
                        $purchase_lines = $purchase_lines->whereBetween('transactions.transaction_date', [$array[$i]['date'], $array[$i + 1]['date']]);
                    }

                    $transaction_sell_lines = $transaction_sell_lines->select('transaction_sell_lines.*')->get();
                    $physical_inventory_lines = $physical_inventory_lines->select('physical_inventory_lines.*')->get();
                    $purchase_lines = $purchase_lines->select('purchase_lines.*')->get();

                    if (! empty($transaction_sell_lines)) {
                        foreach ($transaction_sell_lines as $tsl) {
                            $transaction = Transaction::find($tsl->transaction_id);

                            $tsl->sale_price = is_null($tsl->sale_price) ? $variation->sell_price_inc_tax : $tsl->sale_price;

                            if ($transaction->type == 'sell_transfer') {
                                $tsl->unit_price_before_discount = $unit_cost_exc_tax;
                                $tsl->unit_price = $unit_cost_exc_tax;
                                $tsl->unit_price_inc_tax = $unit_cost_exc_tax * $tsl->quantity;
                                $tsl->unit_price_exc_tax = $unit_cost_exc_tax * $tsl->quantity;
                            }

                            $tsl->unit_cost_exc_tax = $unit_cost_exc_tax;
                            $tsl->unit_cost_inc_tax = $unit_cost_inc_tax;
                            $tsl->save();

                            $kardex = Kardex::where('line_reference', $tsl->id)
                                ->where('transaction_id', $tsl->transaction_id)
                                ->where('variation_id', $variation_id)
                                ->first();

                            if (! empty($kardex)) {
                                $kardex->unit_cost_outputs = $unit_cost_exc_tax;
                                $kardex->total_cost_outputs = $unit_cost_exc_tax * $kardex->outputs_quantity;
                                $kardex->save();
                            }
                        }
                    }

                    if (! empty($physical_inventory_lines)) {
                        foreach ($physical_inventory_lines as $pil) {
                            $pil->price = $unit_cost_exc_tax;
                            $pil->save();

                            $kardex = Kardex::where('physical_inventory_id', $pil->physical_inventory_id)
                                ->where('variation_id', $variation_id)
                                ->first();

                            if (! empty($kardex)) {
                                if ($pil->difference > 0) {
                                    $kardex->unit_cost_inputs = $unit_cost_exc_tax;
                                    $kardex->total_cost_inputs = $unit_cost_exc_tax * $kardex->inputs_quantity;
                                    $kardex->save();

                                    $purchase_lines_pi = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
                                        ->where('transactions.ref_no', $kardex->reference)
                                        ->where(function ($query) {
                                            $query->whereNull('transactions.type')
                                                ->orWhere('transactions.type', 'physical_inventory');
                                        })
                                        ->select('purchase_lines.*')
                                        ->get();

                                    if (! empty($purchase_lines_pi)) {
                                        foreach ($purchase_lines_pi as $plpi) {
                                            $plpi->purchase_price = $unit_cost_exc_tax;
                                            $plpi->purchase_price_inc_tax = $unit_cost_exc_tax;
                                            $plpi->save();
                                        }
                                    }

                                } else {
                                    $kardex->unit_cost_outputs = $unit_cost_exc_tax;
                                    $kardex->total_cost_outputs = $unit_cost_exc_tax * $kardex->outputs_quantity;
                                    $kardex->save();

                                    $transaction_sell_lines_pi = TransactionSellLine::join('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
                                        ->where('transactions.ref_no', $kardex->reference)
                                        ->where(function ($query) {
                                            $query->whereNull('transactions.type')
                                                ->orWhere('transactions.type', 'physical_inventory');
                                        })
                                        ->select('transaction_sell_lines.*')
                                        ->get();

                                    if (! empty($transaction_sell_lines_pi)) {
                                        foreach ($transaction_sell_lines_pi as $tslpi) {
                                            $tslpi->sale_price = is_null($tslpi->sale_price) ? $variation->sell_price_inc_tax : $tslpi->sale_price;
                                            $tslpi->unit_price_before_discount = $unit_cost_exc_tax;
                                            $tslpi->unit_price = $unit_cost_inc_tax;
                                            $tslpi->unit_cost_exc_tax = $unit_cost_exc_tax;
                                            $tslpi->unit_cost_inc_tax = $unit_cost_exc_tax;
                                            $tslpi->save();
                                        }
                                    }
                                }

                                $kardex->save();
                            }
                        }
                    }

                    if (! empty($purchase_lines)) {
                        $transfers = [];
                        $adjustments = [];

                        foreach ($purchase_lines as $pl) {
                            $unit_cost_exc_tax = $this->productUtil->num_f($array[$i]['unit_cost_exc_tax'], false, 6);

                            $pl->purchase_price = $unit_cost_exc_tax;
                            $pl->purchase_price_inc_tax = $unit_cost_exc_tax;
                            $pl->save();

                            $kardex = Kardex::where('line_reference', $pl->id)
                                ->where('transaction_id', $pl->transaction_id)
                                ->where('variation_id', $variation_id)
                                ->first();

                            if (! empty($kardex)) {
                                $kardex->unit_cost_inputs = $unit_cost_exc_tax;
                                $kardex->total_cost_inputs = $unit_cost_exc_tax * $kardex->outputs_quantity;
                                $kardex->save();
                            }

                            $transaction = Transaction::find($pl->transaction_id);

                            if ($transaction->type == 'purchase_transfer') {
                                $parent_transaction = Transaction::find($transaction->transfer_parent_id);
                                array_push($transfers, $parent_transaction);

                                $tlines_pt = TransactionSellLine::where('variation_id', $variation_id)
                                    ->where('transaction_id', $parent_transaction->id)
                                    ->get();

                                if (! empty($tlines_pt)) {
                                    foreach ($tlines_pt as $tl_pt) {
                                        $tl_pt->sale_price = is_null($tl_pt->sale_price) ? $variation->sell_price_inc_tax : $tl_pt->sale_price;
                                        $tl_pt->unit_price_before_discount = $unit_cost_exc_tax;
                                        $tl_pt->unit_price = $unit_cost_exc_tax;
                                        $tl_pt->unit_price_exc_tax = $unit_cost_exc_tax * $tl_pt->quantity;
                                        $tl_pt->unit_price_inc_tax = $unit_cost_exc_tax * $tl_pt->quantity;
                                        $tl_pt->save();
                                    }

                                    $kardex = Kardex::where('line_reference', $tl_pt->id)
                                        ->where('transaction_id', $tl_pt->transaction_id)
                                        ->where('variation_id', $variation_id)
                                        ->first();
    
                                    if (! empty($kardex)) {
                                        $kardex->unit_cost_outputs = $unit_cost_exc_tax;
                                        $kardex->total_cost_outputs = $unit_cost_exc_tax * $kardex->outputs_quantity;
                                        $kardex->save();
                                    }
                                }

                            } else {
                                array_push($adjustments, $transaction);
                            }
                        }

                        if (! empty($transfers)) {
                            foreach ($transfers as $sell_transfer) {
                                $total = 0;

                                foreach ($sell_transfer->sell_lines as $line) {
                                    $total += $line->unit_price_exc_tax;
                                }

                                $sell_transfer->total_before_tax = $total;
                                $sell_transfer->final_total = $total;
                                $sell_transfer->save();

                                $purchase_transfer = Transaction::where('transfer_parent_id', $sell_transfer->id)->first();

                                $purchase_transfer->total_before_tax = $total;
                                $purchase_transfer->final_total = $total;
                                $purchase_transfer->save();
                            }
                        }

                        if (! empty($adjustments)) {
                            foreach ($adjustments as $adjustment) {
                                $total = 0;

                                foreach ($adjustment->purchase_lines as $line) {
                                    $total += ($line->purchase_price * $line->quantity);
                                }

                                $adjustment->total_before_tax = $total;
                                $adjustment->final_total = $total;
                                $adjustment->save();
                            }
                        }
                    }
                }

                // Update to 6 decimals the purchase_lines
                $purchase_lines = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->select('purchase_lines.*')
                    ->get();

                if (! empty($purchase_lines)) {
                    foreach ($purchase_lines as $pl) {
                        $kardex = Kardex::where('line_reference', $pl->id)
                            ->where('transaction_id', $pl->transaction_id)
                            ->where('variation_id', $variation_id)
                            ->first();

                        $pl_purchase_price = $pl->purchase_price;

                        $purchase_pl = Transaction::find($pl->transaction_id);

                        if ($purchase_pl->type == 'purchase' && $purchase_pl->purchase_type == 'international') {
                            $has_apportionment_pl = ApportionmentHasTransaction::where('transaction_id', $purchase_pl->id)->first();

                            if (! empty($has_apportionment_pl)) {
                                $apportionment = Apportionment::find($has_apportionment_pl->apportionment_id);

                                if ($apportionment->is_finished == 1) {
                                    $pl_purchase_price = $pl->purchase_price_inc_tax;
                                }
                            }
                        }

                        if (! empty($kardex)) {
                            $kardex->unit_cost_inputs = $pl_purchase_price;
                            $kardex->total_cost_inputs = $pl_purchase_price * $kardex->inputs_quantity;
                            $kardex->save();
                        }
                    }
                }

                // Update to 6 decimals the transaction_sell_lines
                $transaction_sell_lines = TransactionSellLine::join('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transaction_sell_lines.variation_id', $variation_id)
                    ->get();

                if (! empty($transaction_sell_lines)) {
                    foreach ($transaction_sell_lines as $tsl) {
                        $kardex = Kardex::where('line_reference', $tsl->id)
                            ->where('transaction_id', $tsl->transaction_id)
                            ->where('variation_id', $variation_id)
                            ->first();

                        if (! empty($kardex)) {
                            $kardex->unit_cost_outputs = $tsl->unit_cost_exc_tax;
                            $kardex->total_cost_outputs = $tsl->unit_cost_exc_tax * $kardex->outputs_quantity;
                            $kardex->save();
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
                    'msg_massive' => '> ACCIÓN REALIZADA CON ÉXITO: (' . $variation->sub_sku . ') ' . $product->name
                ];

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

                $output = [
                    'success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                    'msg_massive' => '> ERROR: (' . $variation->sub_sku . ') ' . $product->name . ' -- ARCHIVO: ' . $e->getFile() . ' LÍNEA: ' . $e->getLine() . ' MENSAJE: ' . $e->getMessage()
                ];
            }

            return $output;
        }
    }

    /**
     * Check if cost balance is negative.
     * 
     * @retur array
     */
    public function checkCostBalance()
    {
        // Set maximum PHP execution time
        ini_set('max_execution_time', 0);
        
        try {
            \Log::debug("--- START ---");

            $business_list = Business::all();

            foreach ($business_list as $business) {
                $warehouses = Warehouse::where('business_id', $business->id)->get();
                $variations = Variation::all();
        
                foreach ($warehouses as $warehouse) {
                    foreach ($variations as $variation) {
                        $statement = DB::statement('SET @running_sum = 0');
                
                        $kardex = DB::select("
                            SELECT
                                @running_sum := @running_sum + total_cost_inputs - total_cost_outputs AS balance_cost
                            FROM kardexes AS k
                            LEFT JOIN movement_types AS mt ON k.movement_type_id = mt.id
                            WHERE k.business_id = $business->id
                                AND k.warehouse_id = $warehouse->id
                                AND k.variation_id = $variation->id
                            ORDER BY k.date_time
                        ");

                        foreach ($kardex as $item) {
                            if ($item->balance_cost < 0) {
                                \Log::info("ERROR: WAREHOUSE -> ($warehouse->id) $warehouse->name - VARIATION -> ($variation->id) $variation->sub_sku");
                                break;
                            }
                        }
                    }
                }
            }

            \Log::debug("--- END ---");

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    /**
     * Fix kardex of products that are included in kits.
     * 
     * @return string
     */
    public function fixKitProducts($warehouse_id = 0)
    {
        try {
            if ($warehouse_id === 0) {
                $warehouses = Warehouse::all();
            } else {
                $warehouses = Warehouse::where('id', $warehouse_id)->get();
            }

            $variation_ids = KitHasProduct::distinct()
                ->get(['children_id'])
                ->pluck('children_id');

            $total_records = 0;

            DB::beginTransaction();

            \Log::info('--- START ---');
            
            foreach ($warehouses as $warehouse) {
                foreach ($variation_ids as $variation_id) {
                    $total_records++;

                    $variation = Variation::find($variation_id);

                    $vld = VariationLocationDetails::where('variation_id', $variation->id)
                        ->where('warehouse_id', $warehouse->id)
                        ->first();

                    if (! empty($vld)) {
                        $this->__generateProductKardex($variation->id, $warehouse->id);

                        \Log::info("RECALCULATE: sku -> $variation->sub_sku variation_id -> $variation->id location_id -> $warehouse->business_location_id warehouse_id -> $warehouse->id");
                    }
                }
            }

            \Log::info('TOTAL RECORDS: ' . $total_records); 

            \Log::info('--- END ---');

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    /**
     * Compare sale lines with purchase lines.
     * 
     * @param  int  $warehouse_id
     * @return string
     */
    public function compareSellAndPurchaseLines($warehouse_id) {
        try {
            ini_set('max_execution_time', 0);

            DB::beginTransaction();

            $sell_transfers = Transaction::where('type', 'sell_transfer')
                ->where('warehouse_id', $warehouse_id)
                ->get();

            $variation_ids = [];

            foreach ($sell_transfers as $sell_transfer) {
                $tsl_list = TransactionSellLine::where('transaction_id', $sell_transfer->id)->get();
                $tsl_count = $tsl_list->count();

                $purchase_transfer = Transaction::where('transfer_parent_id', $sell_transfer->id)->first();
                $pl_count = PurchaseLine::where('transaction_id', $purchase_transfer->id)->count();

                if ($tsl_count != $pl_count) {
                    \Log::debug("(1) SELL TRANSFER $sell_transfer->id - $sell_transfer->ref_no - $tsl_count --- PURCHASE TRANSFER $purchase_transfer->id - $purchase_transfer->ref_no - $pl_count");
                    // $v_ids = $this->fixPurchaseLines($sell_transfer->id);
                    break;
                }

                foreach ($tsl_list as $tsl) {
                    $pl = PurchaseLine::where('transaction_id', $purchase_transfer->id)
                        ->where('variation_id', $tsl->variation_id)
                        ->first();

                    if ($pl->quantity != $tsl->quantity || $pl->purchase_price != $tsl->unit_price_before_discount) {
                        \Log::debug("(2) SELL TRANSFER $sell_transfer->id - $sell_transfer->ref_no - $tsl_count --- PURCHASE TRANSFER $purchase_transfer->id - $purchase_transfer->ref_no - $pl_count");
                        // $v_ids = $this->fixPurchaseLines($sell_transfer->id);
                        break;
                    }
                }
                
                // if (count($v_ids)) {
                //     foreach ($v_ids as $v_id) {
                //         if (! in_array($v_id, $variation_ids)) {
                //             $variation_ids[] = $v_id;
                //         }
                //     }
                // }
            }

            // if (count($variation_ids)) {
            //     foreach ($variation_ids as $variation_id) {
            //         $this->__generateProductKardex($variation_id, $warehouse_id);
            //     }
            // }

            DB::commit();

            $output = 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }

    /**
     * Fix registration of purchase_lines to be the same as transaction_sell_lines.
     * 
     * @param  int  $sell_transfer_id
     * @param  int  $no_massive
     * @return mixed
     */
    public function fixPurchaseLines($sell_transfer_id, $no_massive = 0) {
        try {
            ini_set('max_execution_time', 0);

            $tsl_list = TransactionSellLine::where('transaction_id', $sell_transfer_id)->get();

            $purchase_transfer = Transaction::where('transfer_parent_id', $sell_transfer_id)->first();

            $pl_list = [];

            $variation_ids = [];

            DB::beginTransaction();

            \Log::info("-- START --");

            foreach ($tsl_list as $tsl) {
                $pl = PurchaseLine::where('transaction_id', $purchase_transfer->id)
                    ->where('variation_id', $tsl->variation_id)
                    ->first();

                $sub_sku = Variation::find($tsl->variation_id)->sub_sku;

                if (! empty($pl)) {
                    $pl_list[] = $pl->id;

                    if ($pl->quantity != $tsl->quantity || $pl->purchase_price != $tsl->unit_price_before_discount) {
                        $pl->quantity = $tsl->quantity;
                        $pl->purchase_price = $tsl->unit_price_before_discount;
                        $pl->purchase_price_inc_tax = $tsl->unit_price;
                        // $pl->sale_price = $tsl->sale_price;
        
                        $pl->save();
        
                        $variation_ids[] = $pl->variation_id;

                        \Log::info("MODIFY: variation_id -> $pl->variation_id sub_sku -> $sub_sku quantity -> $pl->quantity quantity_sold -> $pl->quantity_sold");
        
                        if ($pl->quantity < $pl->quantity_sold) {
                            \Log::info("ERROR: variation_id -> $pl->variation_id sub_sku -> $sub_sku quantity -> $pl->quantity quantity_sold -> $pl->quantity_sold");
                        }
                    }

                } else {
                    $pl = PurchaseLine::create([
                        'transaction_id' => $tsl->transaction_id,
                        'product_id' => $tsl->product_id,
                        'variation_id' => $tsl->variation_id,
                        'quantity' => $tsl->quantity,
                        'item_tax' => $tsl->item_tax,
                        'tax_id' => $tsl->tax_id,
                        'purchase_price' => $tsl->unit_price_before_discount,
                        'purchase_price_inc_tax' => $tsl->unit_price,
                        // 'sale_price' => $tsl->product_id
                    ]);

                    $variation_ids[] = $pl->variation_id;

                    $sub_sku = Variation::find($pl->variation_id)->sub_sku;
                    \Log::info("CREATE: variation_id -> $pl->variation_id sub_sku -> $sub_sku warehouse_id -> $purchase_transfer->warehouse_id");
                }
            }

            $delete_purchase_lines = PurchaseLine::where('transaction_id', $purchase_transfer->id)
                ->whereNotIn('id', $pl_list)
                ->get();

            if ($delete_purchase_lines->count()) {
                foreach ($delete_purchase_lines as $delete_purchase_line) {
                    $pl = PurchaseLine::find($delete_purchase_line->id);

                    $variation_ids[] = $pl->variation_id;

                    if (! empty($pl)) {
                        $sub_sku = Variation::find($pl->variation_id)->sub_sku;
                        \Log::info("DELETE: variation_id -> $pl->variation_id sub_sku -> $sub_sku warehouse_id -> $purchase_transfer->warehouse_id");
                        $pl->delete();
                    }
                }
            }

            if ($no_massive == 1) {
                if (count($variation_ids)) {
                    foreach ($variation_ids as $variation_id) {
                        $sub_sku = Variation::find($variation_id)->sub_sku;
                        \Log::info("START - $sub_sku");
                        $this->__generateProductKardex($variation_id, $purchase_transfer->warehouse_id);
                        \Log::info("RECALCULATE: variation_id -> $variation_id sub_sku -> $sub_sku warehouse_id -> $purchase_transfer->warehouse_id");
                        \Log::info("END - $sub_sku");
                    }
                }

                $output = 'FINISH';

            } else {
                $output = $variation_ids;
            }

            DB::commit();

            \Log::info("-- END --");

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = 'FAIL';
        }

        return $output;
    }
}
