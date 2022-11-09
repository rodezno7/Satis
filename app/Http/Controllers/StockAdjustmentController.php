<?php

namespace App\Http\Controllers;

use App\Business;
use App\Transaction;
use App\TransactionSellLine;

use Illuminate\Http\Request;

use App\BusinessLocation;
use App\MovementType;
use App\PurchaseLine;
use App\Warehouse;

use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;
use App\Variation;
use Datatables;
use DB;

class StockAdjustmentController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('stock_adjustment.view') && !auth()->user()->can('stock_adjustment.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            $stock_adjustments = Transaction::join('business_locations AS BL', 'transactions.location_id', 'BL.id')
                ->join('warehouses as w', 'transactions.warehouse_id', 'w.id')
                ->leftJoin('users', 'users.id', 'transactions.created_by')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'stock_adjustment')
                ->select(
                    'transactions.id',
                    'transaction_date',
                    'ref_no',
                    'w.name as warehouse_name',
                    'adjustment_type',
                    'final_total',
                    DB::raw(
                        "IF(transactions.adjustment_type = 'normal',
                        (SELECT SUM(pl.sale_price * pl.quantity) FROM purchase_lines AS pl JOIN transactions as t ON t.id = pl.transaction_id WHERE t.id = transactions.id),
                        (SELECT SUM(tsl.sale_price * tsl.quantity) FROM transaction_sell_lines AS tsl JOIN transactions as t ON t.id = tsl.transaction_id WHERE t.id = transactions.id))
                        AS final_total_price"
                    ),
                    'additional_notes',
                    'transactions.id as DT_RowId',
                    DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as responsable")
                );

            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $stock_adjustments->whereIn('transactions.location_id', $permitted_locations);
            }

            $hide = '';
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');

            if (!empty($start_date) && !empty($end_date)) {
                $stock_adjustments->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
                $hide = 'hide';
            }

            $location_id = request()->get('location_id');

            if (!empty($location_id)) {
                $stock_adjustments->where('transactions.location_id', $location_id);
            }

            // Number of decimals in inventories
            $business = Business::find($business_id);
            $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
            $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];
            
            return Datatables::of($stock_adjustments)
                ->filterColumn('responsable', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">
                            @lang("messages.actions")
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li>
                                <a href="#" class="view_stock_adjustment">
                                    <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                    @lang("stock_adjustment.view_detail")
                                </a>
                            </li>
                            <li>
                                <a href="#" class="print-invoice" data-href="{{ action("StockAdjustmentController@printInvoice", [$id]) }}">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    @lang("messages.print")
                                </a>
                            </li>
                            <li>
                                <a href="#" class="delete_stock_adjustment ' . $hide . '" data-href="{{ action("StockAdjustmentController@destroy", [$id]) }}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                    @lang("messages.delete")
                                </a>
                            </li>
                        </ul>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn('transaction_date', function ($row) {
                    return $this->transactionUtil->format_date($row->transaction_date);
                })
                ->editColumn(
                    'final_total', function ($row) use ($show_costs_or_prices) {
                        $final_total = $row->final_total;

                        if ($show_costs_or_prices == 'prices' && $row->final_total_price > 0) {
                            $final_total = $row->final_total_price;
                        }

                        return '<span class="display_currency" data-currency_symbol="true">' . $final_total . '</span>';
                    }
                )
                ->editColumn('adjustment_type', function ($row) {
                    return __('stock_adjustment.' . $row->adjustment_type);
                })
                ->rawColumns(['transaction_date', 'final_total', 'action'])
                ->make(true);
        }

        return view('stock_adjustment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('stock_adjustment.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('StockAdjustmentController@index'));
        }

        //$business_locations = BusinessLocation::forDropdown($business_id);
        /** Warehouses */
        $warehouses = Warehouse::forDropdown($business_id, false);

        # Reference count
        $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment', null, false);

        // Product settings
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];
        $decimals_in_inventories = is_null($product_settings) ? 2 : $product_settings['decimals_in_inventories'];

        return view('stock_adjustment.create')
                ->with(compact('warehouses', 'ref_count', 'show_costs_or_prices', 'decimals_in_inventories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('stock_adjustment.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input_data = $request->only([ 'location_id', 'warehouse_id', 'transaction_date', 'adjustment_type', 'additional_notes', 'total_amount_recovered', 'final_total', 'ref_no']);
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('StockAdjustmentController@index'));
            }
        
            $user_id = $request->session()->get('user.id');

            $input_data['type'] = 'stock_adjustment';
            $input_data['business_id'] = $business_id;
            $input_data['created_by'] = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date']);
            //$input_data['total_amount_recovered'] = $this->productUtil->num_uf($input_data['total_amount_recovered']);

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count);
            }

            $products = $request->input('products');

            if (!empty($products)) {
                $product_data = [];

                // Edit avarage cost
                // $enable_editing_avg_cost = $request->session()->get('business.enable_editing_avg_cost_from_purchase');

                foreach ($products as $product) {
                    $variation = Variation::find($product['variation_id']);

                    if ($input_data["adjustment_type"] == "normal") {
                        /**purchase_lines */
                        $adjustment_line = [
                            'product_id' => $product['product_id'],
                            'variation_id' => $product['variation_id'],
                            'quantity' => $this->productUtil->num_uf($product['quantity']),
                            'purchase_price' => $this->productUtil->num_uf($product['unit_price']),
                            'purchase_price_inc_tax' => $this->productUtil->num_uf($product['unit_price']),
                            'sale_price' => $variation->sell_price_inc_tax
                        ];

                        /** Increment quantity */
                        /*$this->transactionUtil->adjustQuantity(
                            $input_data['location_id'],
                            $product['product_id'],
                            $product['variation_id'],
                            $this->productUtil->num_uf($product['quantity']),
                            $input_data['warehouse_id']
                        );*/

                        // Edit average cost
                        // if ($enable_editing_avg_cost == 1) {
                        //     $this->productUtil->updateAverageCost(
                        //         $product['variation_id'],
                        //         $this->productUtil->num_uf($product['unit_price']),
                        //         $this->productUtil->num_uf($product['quantity'])
                        //     );
                        // }

                        $this->productUtil->updateProductQuantity(
                            $input_data['location_id'],
                            $product['product_id'],
                            $product['variation_id'],
                            $this->productUtil->num_uf($product['quantity']),
                            0,
                            null,
                            $input_data['warehouse_id']
                        );

                    } else if($input_data["adjustment_type"] == "abnormal") {
                        /** transactions_sell_lines */
                        $qty = $this->productUtil->num_uf($product['quantity']);
                        $unit_price = $this->productUtil->num_uf($product['unit_price']);

                        $adjustment_line = [
                            'product_id' => $product['product_id'],
                            'variation_id' => $product['variation_id'],
                            'quantity' => $this->productUtil->num_uf($product['quantity']),
                            'tax_group_id' => null,
                            'u_price_exc_tax' => $unit_price,
                            'unit_price' => $unit_price,
                            'u_price_inc_tax' => $unit_price,
                            'unit_price_inc_tax' => $unit_price * $qty,
                            'unit_price_exc_tax' => $unit_price * $qty,
                            'unit_cost_exc_tax' => $unit_price,
                            'unit_cost_inc_tax' => $unit_price,
                            'sale_price' => $variation->sell_price_inc_tax
                        ];

                        //Decrease available quantity
                        $this->productUtil->decreaseProductQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $input_data['location_id'],
                            $this->productUtil->num_uf($product['quantity']),
                            null,
                            $input_data['warehouse_id'],
                        );
                    }

                    $product_data[] = $adjustment_line;
                }

                $stock_adjustment = Transaction::create($input_data);

                if($input_data["adjustment_type"] == "normal"){
                    //$stock_adjustment->stock_adjustment_lines()->createMany($product_data);
                    $stock_adjustment->purchase_lines()->createMany($product_data);

                    # Data to create or update kardex lines
                    $lines = PurchaseLine::where('transaction_id', $stock_adjustment->id)->get();

                    $movement_type = MovementType::where('name', 'stock_adjustment')
                        ->where('type', 'input')
                        ->where('business_id', $business_id)
                        ->first();

                    # Check if movement type is set else create it
                    if (empty($movement_type)) {
                        $movement_type = MovementType::create([
                            'name' => 'stock_adjustment',
                            'type' => 'input',
                            'business_id' => $business_id
                        ]);
                    }

                    # Store kardex
                    $this->transactionUtil->createOrUpdateInputLines($movement_type, $stock_adjustment, $stock_adjustment->ref_no, $lines);

                } else if($input_data["adjustment_type"] == "abnormal"){
                    //$stock_adjustment->sell_lines()->saveMany($sell_lines);
                    $this->transactionUtil->createOrUpdateSellLines($stock_adjustment, $product_data, $input_data['location_id']);

                    # Data to create or update output lines
                    $lines = TransactionSellLine::where('transaction_id', $stock_adjustment->id)->get();

                    $movement_type = MovementType::where('name', 'stock_adjustment')
                        ->where('type', 'output')
                        ->where('business_id', $business_id)
                        ->first();

                    # Check if movement type is set else create it
                    if (empty($movement_type)) {
                        $movement_type = MovementType::create([
                            'name' => 'stock_adjustment',
                            'type' => 'output',
                            'business_id' => $business_id
                        ]);
                    }

                    # Store kardex
                    $this->transactionUtil->createOrUpdateOutputLines($movement_type, $stock_adjustment, $stock_adjustment->ref_no, $lines);
                    
                    $business = ['id' => $business_id,
                                'accounting_method' => $request->session()->get('business.accounting_method'),
                                'location_id' => $input_data['location_id']
                            ];
                    $this->transactionUtil->mapPurchaseSell($business, $stock_adjustment->sell_lines, 'purchase');
                }
            }

            # Get purchase_lines if it's input and transaction_sell_lines if it's output
            if ($stock_adjustment->adjustment_type == 'normal') {
                $lines = $stock_adjustment->purchase_lines;
            } else {
                $lines = $stock_adjustment->sell_lines;
            }

            $output = [
                'success' => 1,
                'msg' => __('stock_adjustment.stock_adjustment_added_successfully'),
                'receipt' => []
            ];

            // Product settings
            $business = Business::find($business_id);
            $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
            $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];
            $decimals_in_inventories = is_null($product_settings) ? 2 : $product_settings['decimals_in_inventories'];

            $output['receipt']['html_content'] = view('stock_adjustment.print',
                compact('stock_adjustment', 'lines', 'show_costs_or_prices', 'decimals_in_inventories'))->render();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
        # return redirect('stock-adjustments')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('stock_adjustment.view')) {
            abort(403, 'Unauthorized action.');
        }
        $stock_adjustment = Transaction::where("id", $id)->first();

        if($stock_adjustment->adjustment_type == 'normal'){
            $stock_adjustment_details = Transaction::
                    leftJoin('purchase_lines as pl', 'transactions.id', 'pl.transaction_id')
                    ->join('products as p', 'pl.product_id', 'p.id')
                    ->join('variations as v', 'pl.variation_id', 'v.id')
                    ->join('product_variations as pv', 'v.product_variation_id', 'pv.id')
                    ->where('transactions.id', $id)
                    ->where('transactions.type', 'stock_adjustment')
                    ->select(
                        'p.name as product',
                        'p.type as type',
                        'pv.name as product_variation',
                        'v.name as variation',
                        'v.sub_sku',
                        'pl.quantity',
                        'pl.purchase_price as unit_price',
                        'pl.lot_number',
                        'pl.exp_date',
                        'pl.sale_price'
                    )
                    ->groupBy('pl.id')
                    ->get();

        } else if($stock_adjustment->adjustment_type == 'abnormal'){
            $stock_adjustment_details = Transaction::
                    leftJoin('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
                    ->join('products as p', 'tsl.product_id', 'p.id')
                    ->join('variations as v', 'tsl.variation_id', 'v.id')
                    ->join('product_variations as pv', 'v.product_variation_id', 'pv.id')
                    ->where('transactions.id', $id)
                    ->where('transactions.type', 'stock_adjustment')
                    ->select(
                        'p.name as product',
                        'p.type as type',
                        'pv.name as product_variation',
                        'v.name as variation',
                        'v.sub_sku',
                        'tsl.quantity',
                        'tsl.unit_price',
                        'tsl.unit_price as lot_number', // Not will be used
                        'tsl.unit_price as exp_date', // Not will be used
                        'tsl.sale_price'
                    )
                    ->groupBy('tsl.id')
                    ->get();
        }

        $lot_n_exp_enabled = false;
        /*if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
            $lot_n_exp_enabled = true;
        }*/

        // Number of decimals in inventories
        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];
        $decimals_in_inventories = $product_settings['decimals_in_inventories'];

        return view('stock_adjustment.partials.details')
                ->with(compact('stock_adjustment_details', 'lot_n_exp_enabled', 'show_costs_or_prices', 'decimals_in_inventories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $stockAdjustment
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $stockAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $stockAdjustment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $stockAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('stock_adjustment.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (request()->ajax()) {
                DB::beginTransaction();

                $stock_adjustment = Transaction::where('id', $id)
                    ->where('type', 'stock_adjustment')
                    ->first();

                # Delete kardex lines
                $this->transactionUtil->deleteKardexByTransaction($stock_adjustment->id);

                if($stock_adjustment->adjustment_type == 'normal') {
                    $variation_ids = PurchaseLine::where('transaction_id', $stock_adjustment->id)->pluck('variation_id');

                    $delete_purchase_line_ids = [];
                    foreach ($stock_adjustment->purchase_lines as $purchase_line) {
                        $delete_purchase_line_ids[] = $purchase_line->id;
                        $this->productUtil->decreaseProductQuantity(
                            $purchase_line->product_id,
                            $purchase_line->variation_id,
                            $stock_adjustment->location_id,
                            $purchase_line->quantity,
                            null,
                            $stock_adjustment->warehouse_id
                        );
                    }
                    PurchaseLine::where('transaction_id', $stock_adjustment->id)
                                ->whereIn('id', $delete_purchase_line_ids)
                                ->delete();

                    //Update mapping of purchase & Sell.
                    $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($stock_adjustment->status, $stock_adjustment, $stock_adjustment->purchase_lines);

                    // Edit avarage cost
                    // $enable_editing_avg_cost = request()->session()->get('business.enable_editing_avg_cost_from_purchase');

                    // if ($enable_editing_avg_cost == 1) {
                    //     foreach ($variation_ids as $variation_id) {
                    //         $this->productUtil->recalculateProductCost($variation_id);
                    //     }
                    // }
        
                } else if($stock_adjustment->adjustment_type == 'abnormal'){
                    $deleted_sell_lines = $stock_adjustment->sell_lines;
                    $deleted_sell_lines_ids = $deleted_sell_lines->pluck('id')->toArray();
                    $this->transactionUtil->deleteSellLines(
                        $deleted_sell_lines_ids,
                        $stock_adjustment->location_id,
                        $stock_adjustment->warehouse_id
                    );

                    $stock_adjustment->status = 'draft';
                    $business = ['id' => $stock_adjustment->business_id,
                        'accounting_method' => request()->session()->get('business.accounting_method'),
                        'location_id' => $stock_adjustment->location_id
                    ];

                    $this->transactionUtil->adjustMappingPurchaseSell('final', $stock_adjustment, $business, $deleted_sell_lines_ids);
                }

                $stock_adjustment->delete();

                $output = ['success' => 1,
                            'msg' => __('stock_adjustment.delete_success')
                        ];

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }
        return $output;
    }

    /**
     * Return product rows
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getProductRow(Request $request)
    {
        if (request()->ajax()) {
            $row_index = $request->input('row_index');
            $variation_id = $request->input('variation_id');
            $location_id = $request->input('location_id');
            $warehouse_id = $request->input('warehouse_id');
            $check_qty_available = $request->input('check_qty_available');

            $business_id = $request->session()->get('user.business_id');
            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id, $warehouse_id, $check_qty_available);
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available - $product->qty_reserved);

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

            // Product settings
            $business = Business::find($business_id);
            $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
            $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];
            $decimals_in_inventories = is_null($product_settings) ? 2 : $product_settings['decimals_in_inventories'];
            
            return view('stock_adjustment.partials.product_table_row')
                ->with(compact('product', 'row_index', 'check_qty_available', 'show_costs_or_prices', 'decimals_in_inventories'));
        }
    }

    /**
     * Sets expired purchase line as stock adjustmnet
     *
     * @param int $purchase_line_id
     * @return json $output
     */
    public function removeExpiredStock($purchase_line_id)
    {

        if (!auth()->user()->can('stock_adjustment.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $purchase_line = PurchaseLine::where('id', $purchase_line_id)
                                    ->with(['transaction'])
                                    ->first();

            if (!empty($purchase_line)) {
                DB::beginTransaction();

                $qty_unsold = $purchase_line->quantity - $purchase_line->quantity_sold - $purchase_line->quantity_adjusted - $purchase_line->quantity_returned;
                $final_total = $purchase_line->purchase_price_inc_tax * $qty_unsold;

                $user_id = request()->session()->get('user.id');
                $business_id = request()->session()->get('user.business_id');

                //Update reference count
                $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');

                $stock_adjstmt_data = [
                    'type' => 'stock_adjustment',
                    'business_id' => $business_id,
                    'created_by' => $user_id,
                    'transaction_date' => \Carbon::now()->format('Y-m-d'),
                    'total_amount_recovered' => 0,
                    'location_id' => $purchase_line->transaction->location_id,
                    'adjustment_type' => 'normal',
                    'final_total' => $final_total,
                    'ref_no' => $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count)
                ];

                //Create stock adjustment transaction
                $stock_adjustment = Transaction::create($stock_adjstmt_data);

                $stock_adjustment_line = [
                    'product_id' => $purchase_line->product_id,
                    'variation_id' => $purchase_line->variation_id,
                    'quantity' => $qty_unsold,
                    'unit_price' => $purchase_line->purchase_price_inc_tax,
                    'removed_purchase_line' => $purchase_line->id
                ];

                //Create stock adjustment line with the purchase line
                $stock_adjustment->stock_adjustment_lines()->create($stock_adjustment_line);

                //Decrease available quantity
                $this->productUtil->decreaseProductQuantity(
                    $purchase_line->product_id,
                    $purchase_line->variation_id,
                    $purchase_line->transaction->location_id,
                    $qty_unsold
                );

                //Map Stock adjustment & Purchase.
                $business = ['id' => $business_id,
                                'accounting_method' => request()->session()->get('business.accounting_method'),
                                'location_id' => $purchase_line->transaction->location_id
                            ];
                $this->transactionUtil->mapPurchaseSell($business, $stock_adjustment->stock_adjustment_lines, 'stock_adjustment', false, $purchase_line->id);

                DB::commit();

                $output = ['success' => 1,
                            'msg' => __('lang_v1.stock_removed_successfully')
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }
        return $output;
    }

    /**
     * Generates stock adjustment report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            $stock_adjustment = Transaction::where('id', $id)
                ->where('business_id', $business_id)
                ->where('type', 'stock_adjustment')
                ->first();

            # Get purchase_lines if it's input and transaction_sell_lines if it's output
            if ($stock_adjustment->adjustment_type == 'normal') {
                $lines = $stock_adjustment->purchase_lines;
            } else {
                $lines = $stock_adjustment->sell_lines;
            }

            // Product settings
            $business = Business::find($business_id);
            $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
            $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];
            $decimals_in_inventories = is_null($product_settings) ? 2 : $product_settings['decimals_in_inventories'];

            $output = [
                'success' => 1,
                'receipt' => []
            ];

            $output['receipt']['html_content'] = view('stock_adjustment.print',
                compact('stock_adjustment', 'lines', 'show_costs_or_prices', 'decimals_in_inventories'))->render();

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Gets reference number for stock adjustment.
     * 
     * @param  int  $ref_count
     * @param  string  $type
     * @return json
     */
    public function getReference($ref_count, $type)
    {
        $reference = $this->productUtil->generateReferenceNumber($type, $ref_count);
        return ['reference' => $reference];
    }
}
