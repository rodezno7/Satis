<?php

namespace App\Http\Controllers;

use DB;

use Datatables;
use App\Business;
use App\Warehouse;
use App\Transaction;
use App\MovementType;
use App\PurchaseLine;
use Barryvdh\DomPDF\PDF;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use App\TransactionSellLinesPurchaseLines;
use App\TransferState;
use App\Variation;
use App\VariationLocationDetails;

class StockTransferController extends Controller
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
     * @param  \App\Utils\ProductUtils  $productUtil
     * @param  \App\Utils\TransactionUtil  $transactionUtil
     * @param  \App\Utils\ModuleUtil  $moduleUtil
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;

        // Binnacle data
        $this->module_name = 'stock_transfer';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('stock_transfer.view') && ! auth()->user()->can('stock_transfer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $edit_days = request()->session()->get('business.transaction_edit_days');
            $enable_remission_note = request()->session()->get('business.enable_remission_note');

            $stock_transfers = Transaction::join('transactions as t2', 't2.transfer_parent_id', 'transactions.id')
                ->join('warehouses AS w1', 'transactions.warehouse_id', 'w1.id')
                ->join('warehouses AS w2', 't2.warehouse_id', 'w2.id')
                ->leftJoin('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
                ->leftJoin('users', 'users.id', 'transactions.created_by')
                ->leftJoin('transfer_states', 'transfer_states.id', 'transactions.transfer_state_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell_transfer')
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.ref_no',
                    'w1.name as warehouse_from',
                    'w2.name as warehouse_to',
                    'transactions.final_total',
                    DB::raw(
                        "(SELECT SUM(tsl.sale_price * tsl.quantity) FROM transaction_sell_lines AS tsl JOIN transactions as t ON t.id = tsl.transaction_id WHERE t.id = transactions.id)
                        AS final_total_price"
                    ),
                    DB::raw('SUM(tsl.quantity) as quantity'),
                    'transactions.additional_notes',
                    'transactions.id as DT_RowId',
                    DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as responsable"),
                    'transfer_states.name as transfer_state'
                );

            // Warehouse filter
            $permitted_warehouses = Warehouse::permittedWarehouses();

            if ($permitted_warehouses != 'all') {
                $stock_transfers->where(function ($query) use ($permitted_warehouses) {
                    $query->whereIn('transactions.warehouse_id', $permitted_warehouses)
                        ->orWhereIn('t2.warehouse_id', $permitted_warehouses);
                });
            }

            if (request()->has('warehouse_id')) {
                $warehouse_id = request()->get('warehouse_id');

                if ($warehouse_id != 'all') {
                    $stock_transfers->where(function ($query) use ($warehouse_id) {
                        $query->where('transactions.warehouse_id', $warehouse_id)
                            ->orWhere('t2.warehouse_id', $warehouse_id);
                    });
                }
            }

            $stock_transfers->groupBy('transactions.id');

            // Number of decimals in inventories
            $business = Business::find($business_id);
            $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
            $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];

            return Datatables::of($stock_transfers)
                ->filterColumn('responsable', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('transfer_state', function ($query, $keyword) {
                    $query->whereRaw("COALESCE(transfer_states.name, '') like ?", ["%{$keyword}%"]);
                })
                ->addColumn('action', function ($row) use ($edit_days, $enable_remission_note) {
                    $html = 
                        '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                ' <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                    if (in_array($row->transfer_state, ['processed'])) {
                        $html .= '<li><a href="#" data-href="' . action("StockTransferController@receive", [$row->id]) . '" class="receive_stock_transfer"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i>' . __("lang_v1.receive") . '</a></li>';
                    }

                    if (in_array($row->transfer_state, ['received'])) {
                        $html .= '<li><a href="#" data-href="' . action("StockTransferController@count", [$row->id]) . '" class="count_stock_transfer"><i class="fa fa-cubes" aria-hidden="true"></i>' . __("lang_v1.count") . '</a></li>';
                    }

                    if (in_array($row->transfer_state, ['processed', 'received', 'accounted'])) {
                        $html .= '<li><a href="#" class="view_stock_transfer"><i class="fa fa-eye-slash" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';

                        if ($enable_remission_note) {
                            $html .= '<li><a href="' . action("StockTransferController@getRemissionNote", [$row->id]) . '" target="_blank"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        
                        } else {
                            $html .= '<li><a href="#" class="print-invoice" data-href="' . action('StockTransferController@printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        }
                    }

                    if (in_array($row->transfer_state, ['created'])) {
                        $html .= '<li><a href="' . action("StockTransferController@edit", [$row->id]) . '"><i class="fa  fa-pencil-square-o" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';

                        $date = \Carbon::parse($row->transaction_date)->addDays($edit_days);
                        $today = today();

                        if ($date->gte($today)) {
                            $html .= '<li><a href="#" data-href="' . action("StockTransferController@destroy", [$row->id]) . '" class="delete_stock_transfer"><i class="fa fa-trash" aria-hidden="true"></i>' . __("messages.delete") . '</a></li>';
                        }
                    }

                    $html .=  '</ul></div>';

                    return $html;
                })
                ->removeColumn('id')
                ->editColumn(
                    'final_total', function ($row) use ($show_costs_or_prices) {
                        $final_total = $row->final_total;

                        if ($show_costs_or_prices == 'prices' && $row->final_total_price > 0) {
                            $final_total = $row->final_total_price;
                        }

                        return '<span class="display_currency" data-currency_symbol="true">' . $final_total . '</span>';
                    }
                )
                ->editColumn(
                    'quantity',
                    '<span class="display_currency" data-currency_symbol="false">{{$quantity}}</span>'
                )
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('transfer_state', '{{ __("lang_v1.$transfer_state") }}')
                ->rawColumns(['final_total', 'action', 'quantity'])
                ->make(true);
        }

        // Locations
        $warehouses = Warehouse::forDropdown($business_id, false, false);

        $default_warehouse = null;

        // Access only to one warehouses
        if (count($warehouses) == 1) {
            foreach ($warehouses as $id => $name) {
                $default_warehouse = $id;
            }
            
        // Access to all warehouses
        } else if (Warehouse::permittedWarehouses() == 'all') {
            $warehouses = $warehouses->prepend(__("kardex.all_2"), 'all');
        }

        return view('stock_transfer.index')->with(compact('warehouses', 'default_warehouse'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('stock_transfer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
        }

        $warehouse_id = Warehouse::forDropdown($business_id);
        $to_warehouse_id = Warehouse::where('business_id', $business_id)->pluck('name', 'id');

        $enable_remission_note = request()->session()->get('business.enable_remission_note');

        // Product settings
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];
        $decimals_in_inventories = is_null($product_settings) ? 2 : $product_settings['decimals_in_inventories'];

        return view('stock_transfer.create')
            ->with(compact('warehouse_id', 'enable_remission_note', 'show_costs_or_prices', 'decimals_in_inventories', 'to_warehouse_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('stock_transfer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            // Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
            }

            DB::beginTransaction();

            $input_data = $request->only([
                'ref_no',
                'transaction_date',
                'additional_notes',
                'shipping_charges',
                'final_total'
            ]);

            $user_id = $request->session()->get('user.id');

            $input_data['final_total'] = $this->productUtil->num_uf($input_data['final_total']);
            $input_data['total_before_tax'] = $input_data['final_total'];

            $input_data['type'] = 'sell_transfer';
            $input_data['location_id'] = $request->input('from_location_id');
            $input_data['warehouse_id'] = $request->input('from_warehouse_id');
            $input_data['business_id'] = $business_id;
            $input_data['created_by'] = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date']);
            $input_data['shipping_charges'] = $this->productUtil->num_uf($input_data['shipping_charges']);
            $input_data['status'] = 'final';
            $input_data['payment_status'] = 'paid';

            $transfer_state = TransferState::where('name', 'created')->first();
            $input_data['transfer_state_id'] = ! empty($transfer_state) ? $transfer_state->id : null;

            // Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_transfer');

            // Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_transfer', $ref_count);
            }

            $products = $request->input('products');
            $sell_lines = [];
            $purchase_lines = [];

            if (! empty($products)) {
                foreach ($products as $product) {
                    $variation = Variation::find($product['variation_id']);

                    $sell_line_arr = [
                        'product_id' => $product['product_id'],
                        'variation_id' => $product['variation_id'],
                        'quantity' => $this->productUtil->num_uf($product['quantity']),
                        'item_tax' => 0,
                        'tax_id' => null
                    ];

                    $purchase_line_arr = $sell_line_arr;

                    // Sales lines
                    $sell_line_arr['u_price_exc_tax'] = $this->productUtil->num_uf($product['u_price_exc_tax']);
                    $sell_line_arr['u_price_inc_tax'] = $sell_line_arr['u_price_exc_tax'];
                    $sell_line_arr['unit_price'] = $sell_line_arr['u_price_exc_tax'];
                    $sell_line_arr['unit_price_exc_tax'] = $this->productUtil->num_uf($product['unit_price_exc_tax']);
                    $sell_line_arr['unit_price_inc_tax'] = $sell_line_arr['unit_price_exc_tax'];

                    $sell_line_arr['unit_cost_exc_tax'] = $sell_line_arr['u_price_exc_tax'];
                    $sell_line_arr['unit_cost_inc_tax'] = $sell_line_arr['u_price_inc_tax'];
                    $sell_line_arr['sale_price'] = $variation->sell_price_inc_tax;

                    // Purchase lines
                    $purchase_line_arr['purchase_price'] = $sell_line_arr['u_price_exc_tax'];
                    $purchase_line_arr['purchase_price_inc_tax'] = $sell_line_arr['u_price_exc_tax'];
                    $purchase_line_arr['sale_price'] = $variation->sell_price_inc_tax;

                    if (! empty($product['lot_no_line_id'])) {
                        // Add lot_no_line_id to sell line
                        $sell_line_arr['lot_no_line_id'] = $product['lot_no_line_id'];

                        // Copy lot number and expiry date to purchase line
                        $lot_details = PurchaseLine::find($product['lot_no_line_id']);
                        $purchase_line_arr['lot_number'] = $lot_details->lot_number;
                        $purchase_line_arr['mfg_date'] = $lot_details->mfg_date;
                        $purchase_line_arr['exp_date'] = $lot_details->exp_date;
                    }

                    $sell_lines[] = $sell_line_arr;
                    $purchase_lines[] = $purchase_line_arr;
                }
            }

            // Create sell transfer transaction
            $sell_transfer = Transaction::create($input_data);

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'create_out',
                $sell_transfer->ref_no,
                $sell_transfer
            );

            // Create purchase transfer at transfer location
            $input_data['type'] = 'purchase_transfer';
            $input_data['status'] = 'received';
            $input_data['location_id'] = $request->input('to_location_id');
            $input_data['warehouse_id'] = $request->input('to_warehouse_id');
            $input_data['transfer_parent_id'] = $sell_transfer->id;

            $purchase_transfer = Transaction::create($input_data);

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'create_in',
                $purchase_transfer->ref_no,
                $purchase_transfer
            );

            // Sell product from first location
            if (! empty($sell_lines)) {
                $this->transactionUtil->createOrUpdateSellLines($sell_transfer, $sell_lines, $sell_transfer->location_id);
            }

            // Purchase product in second location
            if (! empty($purchase_lines)) {
                $purchase_transfer->purchase_lines()->createMany($purchase_lines);
            }

            // Download inventory if shipped
            if ($request->get('download_product') == 1) {
                // Decrease product stock from sell location
                foreach ($products as $product) {
                    $this->send($sell_transfer, $product);
                }

                $transfer_state = TransferState::where('name', 'processed')->first();

                $sell_transfer->transfer_state_id = ! empty($transfer_state) ? $transfer_state->id : null;
                $sell_transfer->save();

                $purchase_transfer->transfer_state_id = ! empty($transfer_state) ? $transfer_state->id : null;
                $purchase_transfer->save();

                // Kardex output lines
                $output_lines = TransactionSellLine::where('transaction_id', $sell_transfer->id)->get();

                $output_movement_type = MovementType::where('name', 'sell_transfer')
                    ->where('type', 'output')
                    ->where('business_id', $business_id)
                    ->first();

                // Check if movement type is set else create it
                if (empty($output_movement_type)) {
                    $output_movement_type = MovementType::create([
                        'name' => 'sell_transfer',
                        'type' => 'output',
                        'business_id' => $business_id
                    ]);
                }

                // Store kardex
                $this->transactionUtil->createOrUpdateOutputLines(
                    $output_movement_type,
                    $sell_transfer,
                    $sell_transfer->ref_no,
                    $output_lines
                );

                // Map sell lines with purchase lines
                $business = [
                    'id' => $business_id,
                    'accounting_method' => $request->session()->get('business.accounting_method'),
                    'location_id' => $sell_transfer->location_id
                ];

                $this->transactionUtil->mapPurchaseSell($business, $sell_transfer->sell_lines, 'purchase');
               
            // Reserve inventory if not shipped
            } else {
                // Increse product quantity reserved from sell location
                foreach ($products as $product) {
                    $this->reserve($sell_transfer, $product);
                }
            }

            $enable_remission_note = request()->session()->get('business.enable_remission_note');

            if ($enable_remission_note) {
                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.stock_transfer_added_successfully'),
                    'sell_transfer_id' => $sell_transfer->id
                ];

            } else {
                $output = $this->printInvoice($sell_transfer->id);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('stock_transfer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $stock_adjustment_details = Transaction::join('transaction_sell_lines as sl', 'sl.transaction_id', 'transactions.id')
            ->join('products as p', 'sl.product_id', '=', 'p.id')
            ->join('variations as v', 'sl.variation_id', '=', 'v.id')
            ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
            ->where('transactions.id', $id)
            ->where('transactions.type', 'sell_transfer')
            ->leftjoin('purchase_lines as pl', 'sl.lot_no_line_id', '=', 'pl.id')
            ->select(
                'p.name as product',
                'p.type as type',
                'pv.name as product_variation',
                'v.name as variation',
                'v.sub_sku',
                'sl.quantity',
                'sl.unit_price',
                'pl.lot_number',
                'pl.exp_date',
                'sl.sale_price'
            )
            ->groupBy('sl.id')
            ->get();

        $lot_n_exp_enabled = false;
        
        if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
            $lot_n_exp_enabled = true;
        }

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('stock_transfer.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $warehouses = Warehouse::forDropdown($business_id);
        $to_warehouses = Warehouse::where('business_id', $business_id)->pluck('name', 'id');

        $sell_transfer = Transaction::find($id);
        $purchase_transfer = Transaction::where('transfer_parent_id', $id)->first();

        // Lines
        $products = collect();

        $purchase_lines = $purchase_transfer->purchase_lines;

        foreach ($sell_transfer->sell_lines as $key => $line) {
            $variation_id = $line->variations->id;
            $business_id = $line->transaction->business_id;
            $warehouse_id = $line->transaction->warehouse_id;
            $check_qty_available = 1;

            $product = $this->productUtil->getDetailsFromVariationTransfers($variation_id, $business_id, $warehouse_id, $check_qty_available);

            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);

            // Get lot number dropdown if enabled
            $lot_numbers = [];

            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariationTransfer($variation_id, $business_id, $warehouse_id, true);

                foreach ($lot_number_obj as $lot_number) {
                    $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                    $lot_numbers[] = $lot_number;
                }
            }

            $product->lot_numbers = $lot_numbers;

            $product->transaction_sell_lines_id = $line->id;
            $product->purchase_lines_id = $purchase_lines->get($key)->id;

            $product->quantity_ordered = $line->quantity;

            $products->push($product);
        }

        $enable_remission_note = request()->session()->get('business.enable_remission_note');

        // Product settings
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];
        $decimals_in_inventories = is_null($product_settings) ? 2 : $product_settings['decimals_in_inventories'];

        return view('stock_transfer.edit')
            ->with(compact(
                'warehouses',
                'sell_transfer',
                'purchase_transfer',
                'products',
                'enable_remission_note',
                'show_costs_or_prices',
                'decimals_in_inventories',
                'to_warehouses'
            ));
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
        if (! auth()->user()->can('stock_transfer.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            DB::beginTransaction();

            $sell_transfer = Transaction::find($id);
            $purchase_transfer = Transaction::where('transfer_parent_id', $id)->first();

            // Old data to update stock if warehouses are changed
            $sell_transfer_old = clone $sell_transfer;
            $sell_lines_old = $sell_transfer_old->sell_lines;

            $purchase_transfer_old = clone $purchase_transfer;
            $purchase_lines_old = $purchase_transfer_old->purchase_lines;

            $status_before =  $sell_transfer->status;

            $input_data = $request->only([
                'transaction_date',
                'additional_notes',
                'shipping_charges',
                'final_total'
            ]);

            $input_data['final_total'] = $this->productUtil->num_uf($input_data['final_total']);
            $input_data['total_before_tax'] = $input_data['final_total'];

            $input_data['location_id'] = $request->input('from_location_id');
            $input_data['warehouse_id'] = $request->input('from_warehouse_id');
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date']);
            $input_data['shipping_charges'] = $this->productUtil->num_uf($input_data['shipping_charges']);
            $input_data['status'] = 'final';
            $input_data['payment_status'] = 'paid';

            if (! empty($request->input('ref_no'))) {
                $input_data['ref_no'] =  $request->input('ref_no');
            }

            // Update sell transfer transaction
            $sell_transfer->update($input_data);

            $input_data['location_id'] = $request->input('to_location_id');
            $input_data['warehouse_id'] = $request->input('to_warehouse_id');
            $input_data['status'] = 'received';

            $purchase_transfer->update($input_data);

            $products = $request->input('products');
            $updated_sell_lines = [];
            $updated_purchase_lines = [];

            // Lines that were not deleted when editing
            $updated_sell_line_ids = [];
            $updated_purchase_line_ids = [];

            if (! empty($products)) {
                foreach ($products as $product) {
                    $variation = Variation::find($product['variation_id']);

                    if (isset($product['transaction_sell_lines_id'])) {
                        // Sell lines
                        $sell_line = TransactionSellLine::find($product['transaction_sell_lines_id']);
                        $sell_old_qty = $this->productUtil->num_f($sell_line->quantity);
                        $sell_line->quantity = $this->productUtil->num_uf($product['quantity']);
                        $updated_sell_line_ids[] = $sell_line->id;

                        // Purchase lines
                        $purchase_line = PurchaseLine::find($product['purchase_lines_id']);
                        $purchase_line->quantity = $this->productUtil->num_uf($product['quantity']);
                        $updated_purchase_line_ids[] = $purchase_line->id;

                        // Download inventory if shipped
                        if ($request->get('download_product') == 1) {
                            $this->send($sell_transfer, $product, 'update', $sell_old_qty);

                        } else {
                            $this->reserve($sell_transfer, $product, 'update', $sell_old_qty);
                        }

                    } else {
                        $sell_line = new TransactionSellLine();
                        $sell_line->product_id = $product['product_id'];
                        $sell_line->variation_id = $product['variation_id'];
                        $sell_line->quantity = $this->productUtil->num_uf($product['quantity']);
                        $sell_line->item_tax = 0;
                        $sell_line->tax_id = null;

                        $purchase_line = new PurchaseLine();
                        $purchase_line->product_id = $product['product_id'];
                        $purchase_line->variation_id = $product['variation_id'];
                        $purchase_line->quantity = $this->productUtil->num_uf($product['quantity']);
                        $purchase_line->item_tax = 0;
                        $purchase_line->tax_id = null;

                        // Download inventory if shipped
                        if ($request->get('download_product') == 1) {
                            $this->send($sell_transfer, $product);

                        } else {
                            $this->reserve($sell_transfer, $product);
                        }
                    }

                    $sell_line->u_price_exc_tax = $this->productUtil->num_uf($product['u_price_exc_tax']);
                    $sell_line->u_price_inc_tax = $this->productUtil->num_uf($product['u_price_exc_tax']);
                    $sell_line->unit_price = $this->productUtil->num_uf($product['u_price_exc_tax']);
                    $sell_line->unit_price_exc_tax = $this->productUtil->num_uf($product['unit_price_exc_tax']);
                    $sell_line->unit_price_inc_tax = $this->productUtil->num_uf($product['unit_price_exc_tax']);

                    $sell_line->unit_cost_exc_tax = $sell_line->u_price_exc_tax;
                    $sell_line->unit_cost_inc_tax = $sell_line->u_price_inc_tax;
                    $sell_line->sale_price = $variation->sell_price_inc_tax;

                    $purchase_line->purchase_price = $this->productUtil->num_uf($product['u_price_exc_tax']);
                    $purchase_line->purchase_price_inc_tax = $this->productUtil->num_uf($product['u_price_exc_tax']);
                    $purchase_line->sale_price = $variation->sell_price_inc_tax;

                    if (! empty($product['lot_no_line_id'])) {
                        // Add lot_no_line_id to sell line
                        $sell_line->lot_no_line_id = $product['lot_no_line_id'];

                        // Copy lot number and expiry date to purchase line
                        $lot_details = PurchaseLine::find($product['lot_no_line_id']);
                        $purchase_line->lot_number = $lot_details->lot_number;
                        $purchase_line->mfg_date = $lot_details->mfg_date;
                        $purchase_line->exp_date = $lot_details->exp_date;
                    }

                    $updated_sell_lines[] = $sell_line;
                    $updated_purchase_lines[] = $purchase_line;
                }
            }

            // Unset deleted sell lines
            $delete_sell_line_ids = [];
            $delete_purchase_line_ids = [];

            $delete_sell_lines = TransactionSellLine::where('transaction_id', $sell_transfer->id)
                ->whereNotIn('id', $updated_sell_line_ids)
                ->get();

            $delete_purchase_lines = PurchaseLine::where('transaction_id', $purchase_transfer->id)
                ->whereNotIn('id', $updated_purchase_line_ids)
                ->get();

            if ($sell_transfer->warehouse_id == $sell_transfer_old->warehouse_id) {
                if ($delete_sell_lines->count()) {
                    foreach ($delete_sell_lines as $delete_sell_line) {
                        $delete_sell_line_ids[] = $delete_sell_line->id;

                        // Update reserved quantity
                        $this->productUtil->updateProductQtyReserved(
                            $sell_transfer->location_id,
                            $delete_sell_line->product_id,
                            $delete_sell_line->variation_id,
                            0,
                            $delete_sell_line->quantity,
                            null,
                            $sell_transfer->warehouse_id
                        );
                    }
                }

                if ($delete_purchase_lines->count()) {
                    foreach ($delete_purchase_lines as $delete_purchase_line) {
                        $delete_purchase_line_ids[] = $delete_purchase_line->id;

                        $pl = PurchaseLine::find($delete_purchase_line->id);

                        if (! empty($pl)) {
                            $pl->delete();
                        }
                    }
                }
            }

            // Update inventory when there is a change of warehouse
            if ($sell_transfer->warehouse_id != $sell_transfer_old->warehouse_id) {
                foreach ($sell_lines_old as $line) {
                    // Update reserved quantity
                    $this->productUtil->updateProductQtyReserved(
                        $sell_transfer_old->location_id,
                        $line->product_id,
                        $line->variation_id,
                        0,
                        $line->quantity,
                        null,
                        $sell_transfer_old->warehouse_id
                    );
                }

                PurchaseLine::whereIn('id', $purchase_lines_old->pluck('id'))->delete();
                TransactionSellLine::whereIn('id', $sell_lines_old->pluck('id'))->delete();
            }

            // Update sell lines
            if (! empty($updated_sell_lines)) {
                $this->transactionUtil->createOrUpdateSellLines(
                    $sell_transfer,
                    $updated_sell_lines,
                    $sell_transfer->location_id,
                    false,
                    null,
                    [],
                    false
                );
            }

            // Update purchase lines
            if (! empty($updated_purchase_lines)) {
                $purchase_transfer->purchase_lines()->saveMany($updated_purchase_lines);
            }

            if ($request->get('download_product') == 1) {
                // Change tranfer state
                $transfer_state = TransferState::where('name', 'processed')->first();

                $sell_transfer->transfer_state_id = ! empty($transfer_state) ? $transfer_state->id : null;
                $sell_transfer->save();

                $purchase_transfer->transfer_state_id = ! empty($transfer_state) ? $transfer_state->id : null;
                $purchase_transfer->save();

                // Kardex output lines
                $lines = TransactionSellLine::where('transaction_id', $sell_transfer->id)->get();

                $movement_type = MovementType::where('name', 'sell_transfer')
                    ->where('type', 'output')
                    ->where('business_id', $business_id)
                    ->first();

                // Check if movement type is set else create it
                if (empty($movement_type)) {
                    $movement_type = MovementType::create([
                        'name' => 'sell_transfer',
                        'type' => 'output',
                        'business_id' => $business_id
                    ]);
                }

                // Store kardex lines
                $this->transactionUtil->createOrUpdateOutputLines($movement_type, $sell_transfer, $sell_transfer->ref_no, $lines);

                // Map sell lines with purchase lines
                $business = [
                    'id' => $business_id,
                    'accounting_method' => $request->session()->get('business.accounting_method'),
                    'location_id' => $sell_transfer->location_id
                ];

                $this->transactionUtil->adjustMappingPurchaseSell($status_before, $sell_transfer, $business, $delete_sell_lines);
            }

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'update_out',
                $sell_transfer->ref_no,
                $sell_transfer_old,
                $sell_transfer
            );

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'update_in',
                $purchase_transfer->ref_no,
                $purchase_transfer_old,
                $purchase_transfer
            );

            DB::commit();

            $enable_remission_note = request()->session()->get('business.enable_remission_note');

            if ($enable_remission_note) {
                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.stock_transfer_updated_successfully'),
                    'sell_transfer_id' => $sell_transfer->id
                ];

            } else {
                $output = $this->printInvoice($sell_transfer->id);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];

            return back()->with('status', $output);
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('stock_transfer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                // Get sell transfer transaction
                $sell_transfer = Transaction::where('id', $id)
                    ->where('type', 'sell_transfer')
                    ->with(['sell_lines'])
                    ->first();

                // Get purchase transfer transaction
                $purchase_transfer = Transaction::where('transfer_parent_id', $sell_transfer->id)
                    ->where('type', 'purchase_transfer')
                    ->with(['purchase_lines'])
                    ->first();

                DB::beginTransaction();

                // Get purchase lines from transaction_sell_lines_purchase_lines and decrease quantity_sold
                $sell_lines = $sell_transfer->sell_lines;
                $deleted_sell_purchase_ids = [];

                foreach ($sell_lines as $sell_line) {
                    $purchase_sell_line = TransactionSellLinesPurchaseLines::where('sell_line_id', $sell_line->id)->first();

                    if (! empty($purchase_sell_line)) {
                        // Decrease quntity sold from purchase line
                        PurchaseLine::where('id', $purchase_sell_line->purchase_line_id);

                        $deleted_sell_purchase_ids[] = $purchase_sell_line->id;
                    }
                }

                // Update reserved quantity in origin location
                if (! empty($sell_lines)) {
                    foreach ($sell_lines as $sell_line) {
                        $this->productUtil->updateProductQtyReserved(
                            $sell_transfer->location_id,
                            $sell_line->product_id,
                            $sell_line->variation_id,
                            0,
                            $sell_line->quantity,
                            null,
                            $sell_transfer->warehouse_id
                        );
                    }
                }

                // Delete sale line purchase line
                if (!empty($deleted_sell_purchase_ids)) {
                    TransactionSellLinesPurchaseLines::whereIn('id', $deleted_sell_purchase_ids)
                        ->delete();
                }

                // Delete both transactions
                $sell_transfer->delete();
                $purchase_transfer->delete();

                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.stock_transfer_delete_success')
                ];

                DB::commit();
            }

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
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            $sell_transfer = Transaction::where('business_id', $business_id)
                ->where('id', $id)
                ->where('type', 'sell_transfer')
                ->with(
                    'contact',
                    'sell_lines',
                    'sell_lines.product',
                    'sell_lines.variations',
                    'sell_lines.variations.product_variation',
                    'sell_lines.lot_details',
                    'location'
                )
                ->first();

            $purchase_transfer = Transaction::where('business_id', $business_id)
                ->where('transfer_parent_id', $sell_transfer->id)
                ->where('type', 'purchase_transfer')
                ->first();

            $location_details = ['sell' => $sell_transfer->location, 'purchase' => $purchase_transfer->location];

            $lot_n_exp_enabled = false;
            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_n_exp_enabled = true;
            }

            // Product settings
            $business = Business::find($business_id);
            $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
            $show_costs_or_prices = is_null($product_settings) ? 'costs' : $product_settings['show_costs_or_prices'];
            $decimals_in_inventories = is_null($product_settings) ? 2 : $product_settings['decimals_in_inventories'];

            $output = ['success' => 1, 'receipt' => []];
            $output['receipt']['html_content'] = view('stock_transfer.print', compact('sell_transfer', 'location_details', 'lot_n_exp_enabled', 'show_costs_or_prices', 'decimals_in_inventories'))->render();
            
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
     * Get product rows for stock transfer
     * @param Resquest
     * @return view
     */
    public function getProductRowTransfer(Request $request)
    {
        if (request()->ajax()) {
            $row_index = $request->input('row_index');
            $variation_id = $request->input('variation_id');
            $location_id = $request->input('location_id');
            $warehouse_id = $request->input('warehouse_id');
            $check_qty_available = $request->input('check_qty_available');

            $business_id = $request->session()->get('user.business_id');
            $product = $this->productUtil->getDetailsFromVariationTransfers($variation_id, $business_id, $warehouse_id, $check_qty_available);
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available - $product->qty_reserved);

            // Get lot number dropdown if enabled
            $lot_numbers = [];
            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariationTransfer($variation_id, $business_id, $warehouse_id, true);
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

            return view('stock_transfer.partials.product_table_row')
                ->with(compact('product', 'row_index', 'check_qty_available', 'show_costs_or_prices', 'decimals_in_inventories'));
        }
    }

    public function getRemissionNote($id)
    {
        if (!auth()->user()->can('cash_register_report.view')) {
            abort(403, "Unauthorized action.");
        }

        $business_id = Request()->session()->get('user.business_id');
        $business_name = Business::where('id', $business_id)->select('name')->first();

        $transfer = Transaction::join('transaction_sell_lines as sl', 'sl.transaction_id', 'transactions.id')
            ->join('products as p', 'sl.product_id', '=', 'p.id')
            ->join('variations as v', 'sl.variation_id', '=', 'v.id')
            ->where('transactions.id', $id)
            ->where('transactions.type', 'sell_transfer')
            ->leftjoin('purchase_lines as pl', 'sl.lot_no_line_id', '=', 'pl.id')
            ->select(
                'p.name as product',
                'v.name as variation',
                'v.sub_sku',
                'sl.quantity',
                'sl.unit_price',
            )
            ->groupBy('sl.id')
            ->get();


        $trans_ware = Transaction::join('transactions as t2', 't2.transfer_parent_id', 'transactions.id')
            ->join('warehouses as w', 'w.id', 'transactions.warehouse_id')
            ->join('warehouses AS w2', 't2.warehouse_id', 'w2.id')
            ->join('business_locations as bl', 'bl.id', 'w.business_location_id')
            ->where('transactions.id', $id)
            ->select(
                'bl.state',
                'transactions.transaction_date',
                'bl.landmark',
                'bl.city',
                'w2.name'
            )
            ->first();
        // dd($trans_ware);

        $total = 0;
        $total_letters = "";

        foreach ($transfer as $t) {
            $total += ($t->quantity * $t->unit_price);
        }

        $total_letters = $this->transactionUtil->getAmountLetters($total);
        $remission_note_pdf = \PDF::loadView('reports.remission_note', compact('business_name', 'transfer', 'trans_ware', 'total', 'total_letters'));
        $remission_note_pdf->setPaper('letter', 'portrait');

        return $remission_note_pdf->stream(__('reports.remission_note') . '.pdf');
    }

    /**
     * Reserve the product of the "from warehouse".
     * 
     * @param  \App\Transaction  $sell_transfer
     * @param  array  $product
     * @param  string  $type
     * @param  int  $old_qty
     * @return void
     */
    public function reserve($sell_transfer, $product, $type = 'create', $old_qty = 0)
    {
        if ($product['enable_stock']) {
            // Create view
            if ($type == 'create') {
                // Increse reserved quantity for existing products
                $this->productUtil->incrementProductQtyReserved(
                    $product['product_id'],
                    $product['variation_id'],
                    $sell_transfer->location_id,
                    $this->productUtil->num_uf($product['quantity']),
                    0,
                    $sell_transfer->warehouse_id
                );

            // Update view
            } else {
                // Update reserved quantity for existing products
                $this->productUtil->updateProductQtyReserved(
                    $sell_transfer->location_id,
                    $product['product_id'],
                    $product['variation_id'],
                    $this->productUtil->num_uf($product['quantity']),
                    $old_qty,
                    null,
                    $sell_transfer->warehouse_id
                );
            }
        }
    }

    /**
     * Download the product from the "from warehouse" and update the reserved
     * quantity.
     * 
     * @param  \App\Transaction  $sell_transfer
     * @param  array  $product
     * @param  string  $type
     * @param  int  $old_qty
     * @return void
     */
    public function send($sell_transfer, $product, $type = 'create', $old_qty = 0)
    {
        if ($product['enable_stock']) {
            // Decrese quantity for existing products
            $this->productUtil->decreaseProductQuantity(
                $product['product_id'],
                $product['variation_id'],
                $sell_transfer->location_id,
                $this->productUtil->num_uf($product['quantity']),
                0,
                $sell_transfer->warehouse_id
            );

            // Only update view
            if ($type == 'update') {
                // Update reserved quantity for existing products
                $this->productUtil->updateProductQtyReserved(
                    $sell_transfer->location_id,
                    $product['product_id'],
                    $product['variation_id'],
                    0,
                    $old_qty,
                    null,
                    $sell_transfer->warehouse_id
                );
            }
        }
    }

    /**
     * Load the product to the "to warehouse".
     * 
     * @param  int  $id
     * @return array
     */
    public function receive($id)
    {
        if (! auth()->user()->can('stock_transfer.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');

            $sell_transfer = Transaction::find($id);

            // Clone record before action
            $sell_transfer_old = clone $sell_transfer;

            $purchase_transfer = Transaction::where('transfer_parent_id', $id)->first();

            // Clone record before action
            $purchase_transfer_old = clone $purchase_transfer;

            // Take action only if the transfer is in the processed state
            $transfer_state = TransferState::where('name', 'processed')->first();

            if ($sell_transfer->transfer_state_id != $transfer_state->id || $purchase_transfer->transfer_state_id != $transfer_state->id) {
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];

                return $output;
            }

            // Edit avarage cost
            // $enable_editing_avg_cost = request()->session()->get('business.enable_editing_avg_cost_from_purchase');

            $purchase_lines = $purchase_transfer->purchase_lines;

            foreach ($purchase_lines as $line) {
                // Edit average cost
                // if ($enable_editing_avg_cost == 1) {
                //     $this->productUtil->updateAverageCost(
                //         $line->variation_id,
                //         $line->purchase_price,
                //         $line->quantity
                //     );
                // }

                $this->productUtil->updateProductQuantity(
                    $purchase_transfer->location_id,
                    $line->product_id,
                    $line->variation_id,
                    $this->productUtil->num_uf($line->quantity),
                    0,
                    null,
                    $purchase_transfer->warehouse_id
                );
            }

            // Kardex input lines
            $input_lines = PurchaseLine::where('transaction_id', $purchase_transfer->id)->get();

            $input_movement_type = MovementType::where('name', 'purchase_transfer')
                ->where('type', 'input')
                ->where('business_id', $business_id)
                ->first();

            // Check if movement type is set else create it
            if (empty($input_movement_type)) {
                $input_movement_type = MovementType::create([
                    'name' => 'purchase_transfer',
                    'type' => 'input',
                    'business_id' => $business_id
                ]);
            }

            // Store kardex
            $this->transactionUtil->createOrUpdateInputLines(
                $input_movement_type,
                $purchase_transfer,
                $purchase_transfer->ref_no,
                $input_lines
            );

            // Change tranfer state
            $transfer_state = TransferState::where('name', 'received')->first();

            $sell_transfer->transfer_state_id = ! empty($transfer_state) ? $transfer_state->id : null;
            $sell_transfer->save();

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'update_out',
                $sell_transfer->ref_no,
                $sell_transfer_old,
                $sell_transfer
            );
            
            $purchase_transfer->transfer_state_id = ! empty($transfer_state) ? $transfer_state->id : null;
            $purchase_transfer->save();

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'update_in',
                $purchase_transfer->ref_no,
                $purchase_transfer_old,
                $purchase_transfer
            );

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.stock_transfer_updated_successfully')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Create accounting entrie from stock transfer.
     * 
     * @param  int  $id
     * @return array
     */
    public function count($id)
    {
        try {
            DB::beginTransaction();

            DB::select('CALL count_stock_transfer(?)', [$id]);

            // Change tranfer state
            $transfer_state = TransferState::where('name', 'received')->first();

            $sell_transfer = Transaction::find($id);
            $sell_transfer->transfer_state_id = ! empty($transfer_state) ? $transfer_state->id : null;
            $sell_transfer->save();
            
            $purchase_transfer = Transaction::where('transfer_parent_id', $id)->first();
            $purchase_transfer->transfer_state_id = ! empty($transfer_state) ? $transfer_state->id : null;
            $purchase_transfer->save();

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __("lang_v1.entrie_added_successfully")
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Add price instead of cost to transfers.
     * 
     * @param  int  $transaction_id
     * @return string
     */
    public function fixTransfer($transaction_id)
    {
        try {
            DB::beginTransaction();

            // Sell transfer
            $sell_transfer = Transaction::find($transaction_id);
            
            $total = 0;

            foreach ($sell_transfer->sell_lines as $line) {
                $price = $line->variations->sell_price_inc_tax;
                $subtotal = $line->quantity * $price;

                $line->unit_price_before_discount = $price;
                $line->unit_price = $price;
                $line->unit_price_inc_tax = $subtotal;
                $line->unit_price_exc_tax = $subtotal;

                $line->save();

                $total += $subtotal;
            }

            $sell_transfer->total_before_tax = $total;
            $sell_transfer->final_total = $total;

            $sell_transfer->save();

            // Purchase transfer
            $purchase_transfer = Transaction::where('transfer_parent_id', $transaction_id)->first();
            
            $total = 0;

            foreach ($purchase_transfer->purchase_lines as $line) {
                $price = $line->variations->sell_price_inc_tax;
                $subtotal = $line->quantity * $price;

                $line->purchase_price = $price;
                $line->purchase_price_inc_tax = $price;

                $line->save();

                $total += $subtotal;
            }

            $purchase_transfer->total_before_tax = $total;
            $purchase_transfer->final_total = $total;

            $purchase_transfer->save();
            
            DB::commit();

            return 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            return 'FAIL';
        }
    }
}
