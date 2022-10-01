<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Kardex;
use App\MovementType;
use App\PhysicalInventory;
use App\PhysicalInventoryLine;
use App\Product;
use App\PurchaseLine;
use App\Transaction;
use App\TransactionSellLine;
use App\User;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\VariationLocationDetails;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class PhysicalInventoryController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor.
     *
     * @param  ProductUtil  $productUtil
     * @param  TransactionUtil  $transactionUtil
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;

        $this->status_list = [
            'new' => __('physical_inventory.new'),
            'process' => __('physical_inventory.process'),
            'review' => __('physical_inventory.review'),
            'authorized' => __('physical_inventory.authorized'),
            'finalized' => __('physical_inventory.finalized'),
        ];

        $this->can_edit = ['process', 'review'];

        $this->can_view = ['review', 'authorized', 'finalized'];

        $this->categories = [
            'full' => __('physical_inventory.full'),
            'product' => __('physical_inventory.product'),
            'material' => __('physical_inventory.material'),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('physical_inventory.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = $business_id = request()->session()->get('user.business_id');

            $physical_inventories = PhysicalInventory::leftJoin('warehouses', 'warehouses.id', 'physical_inventories.warehouse_id')
                ->leftJoin('business_locations', 'business_locations.id', 'physical_inventories.location_id')
                ->leftJoin('users', 'users.id', 'physical_inventories.responsible')
                ->where('physical_inventories.business_id', $business_id)
                ->select(
                    'physical_inventories.code',
                    'physical_inventories.name',
                    'physical_inventories.start_date',
                    'business_locations.name as location',
                    'warehouses.name as warehouse',
                    'physical_inventories.status',
                    DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as responsible"),
                    'physical_inventories.id'
                );

            return DataTables::of($physical_inventories)
                ->filterColumn('responsible', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->addColumn(
                    'action', function($row) {
                        $flag = false;

                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") .
                                ' <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        if (auth()->user()->can('physical_inventory.view') && in_array($row->status, $this->can_view)) {
                            $html .= '<li><a href="' . action('PhysicalInventoryController@show', [$row->id]) . '" class="show_physical_inventory_button"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                            $flag = true;
                        }

                        if (auth()->user()->can('physical_inventory.update') && in_array($row->status, $this->can_edit)) {
                            $html .= '<li><a href="' . action('PhysicalInventoryController@edit', [$row->id]) . '"><i class="fa fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                            $flag = true;
                        }

                        if (auth()->user()->can('physical_inventory.start') && $row->status == 'new' && now()->toDateString() >= $row->start_date) {
                            $html .= '<li><a href="' . action('PhysicalInventoryController@edit', [$row->id]) . '"><i class="fa fa-play-circle"></i> ' . __("physical_inventory.start") . '</a></li>';
                            $flag = true;
                        }

                        // $html .= '<li><a href="#" data-href="' . action('PhysicalInventoryController@destroy', [$row->id]) . '" class="delete_physical_inventory_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';

                        return $flag ? $html : '';
                    }
                )
                ->editColumn(
                    'status', function($row) {
                        return __('physical_inventory.' . $row->status);
                    }
                )
                ->editColumn(
                    'start_date', '{{ @format_date($start_date) }}'
                )
                ->rawColumns(['action', 'status'])
                ->toJson();
        }

        return view('physical_inventory.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('physical_inventory.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $users = User::forDropdown($business_id, false, true, false);

        $locations = BusinessLocation::forDropdown($business_id);

        // Code
        $ref_count = $this->productUtil->setAndGetReferenceCount('physical_inventory', null, false);
        $code = $this->productUtil->generateReferenceNumber('physical_inventory', $ref_count);

        // Categories
        $categories = $this->categories;

        // Product settings
        $business = Business::find($business_id);
        $product_settings = json_decode($business->product_settings, true);

        return view('physical_inventory.create')
            ->with(compact(
                'users',
                'locations',
                'code',
                'categories',
                'product_settings'
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
        if (! auth()->user()->can('physical_inventory.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            $input = $request->except('_token', 'start_date');

            $input['status'] = 'new';
            $input['start_date'] = $this->productUtil->uf_date($request->input('start_date'));
            $input['business_id'] = $business_id;
            $input['created_by'] = $user_id;
            $input['updated_by'] = $user_id;

            DB::beginTransaction();

            // Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('physical_inventory');

            // Generate code
            if (empty($input['code'])) {
                $input['code'] = $this->productUtil->generateReferenceNumber('physical_inventory', $ref_count);
            }

            $physical_inventory = PhysicalInventory::create($input);
            
            // Create physical inventory lines
            if ($request->get('autoload') == 1 || $request->get('autoload_rotation') == 1) {
                $location_id = $physical_inventory->location_id;
                $warehouse_id = $physical_inventory->warehouse_id;

                $variations = Variation::join('products as p', 'variations.product_id', 'p.id')
                    ->leftJoin('variation_location_details as vld',
                        function($join) use ($location_id, $warehouse_id) {
                            $join->on('variations.id', '=', 'vld.variation_id');

                            $join->where(function($query) use ($location_id, $warehouse_id) {
                                $query->where('vld.location_id', $location_id)
                                    ->where('vld.warehouse_id', $warehouse_id);
                            });
                        }
                    )
                    ->where('p.business_id', $business_id)
                    ->where('p.enable_stock', 1);

                if (config('app.business') == 'optics') {
                    // Only products
                    if ($physical_inventory->category == 'product') {
                        $variations = $variations->where('p.clasification', 'product');

                    // Only materials
                    } else if ($physical_inventory->category == 'material') {
                        $variations = $variations->where('p.clasification', 'material');

                    // Only products and materials
                    } else if ($physical_inventory->category == 'full') {
                        $variations = $variations->whereIn('p.clasification', ['product', 'material']);
                    }

                } else {
                    $variations = $variations->where('p.clasification', 'product');
                }

                // Product settings
                $business = Business::find($business_id);
                $product_settings = json_decode($business->product_settings, true);
                
                if ($request->get('autoload_rotation') == 1 && ! is_null($product_settings['product_rotation'])) {
                    $months = $product_settings['product_rotation'];

                    $variations = $variations->where(function ($query) use ($months, $location_id, $warehouse_id) {
                        $query->whereRaw("(
                                SELECT COUNT(tsl.id) FROM transaction_sell_lines AS tsl
                                INNER JOIN transactions AS t ON t.id = tsl.transaction_id
                                WHERE tsl.variation_id = variations.id
                                AND t.transaction_date BETWEEN DATE_SUB(DATE(NOW()), INTERVAL ? MONTH) AND DATE(NOW())
                                AND t.location_id = ?
                                AND t.warehouse_id = ?
                                ) > 0", [$months, $location_id, $warehouse_id]
                            )
                            ->orWhereRaw("(
                                SELECT COUNT(pl.id) FROM purchase_lines AS pl
                                INNER JOIN transactions AS t ON t.id = pl.transaction_id
                                WHERE pl.variation_id = variations.id
                                AND t.transaction_date BETWEEN DATE_SUB(DATE(NOW()), INTERVAL ? MONTH) AND DATE(NOW())
                                AND t.location_id = ?
                                AND t.warehouse_id = ?
                                ) > 0", [$months, $location_id, $warehouse_id]
                            );
                    });
                }

                $variations = $variations->select(
                        'variations.id as variation_id',
                        'p.id as product_id',
                        'variations.product_variation_id',
                        'vld.id as vld_id'
                    )
                    ->orderBy('variations.sub_sku', 'desc')
                    ->get();

                foreach ($variations as $item) {
                    PhysicalInventoryLine::create([
                        'physical_inventory_id' => $physical_inventory->id,
                        'product_id' => $item->product_id,
                        'variation_id' => $item->variation_id,
                        'quantity' => 0,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    ]);

                    // Create variation_location_details record if not exists
                    if (is_null($item->vld_id)) {
                        $vld = new VariationLocationDetails();
                        $vld->variation_id = $item->variation_id;
                        $vld->product_id = $item->product_id;
                        $vld->location_id = $physical_inventory->location_id;
                        $vld->product_variation_id = $item->product_variation_id;
                        $vld->qty_available = 0;
                        $vld->warehouse_id = $physical_inventory->warehouse_id;
                        $vld->save();
                    }
                }
            }

            DB::commit();

            $output = [
                'success' => true,
                'data' => $physical_inventory,
                'msg' => __('physical_inventory.physical_inventory_added_successfully')
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('physical_inventory.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business = Business::find($business_id);

        $physical_inventory = PhysicalInventory::find($id);

        if (request()->ajax()) {
            $datatable = $this->getData($id, 0);
            return $datatable;
        }

        // Disable fields
        $is_editable = 0;

        // Physical inventory record date
        $physical_inventory_record_date = $business->physical_inventory_record_date;

        return view('physical_inventory.edit')
            ->with(compact('physical_inventory', 'is_editable', 'physical_inventory_record_date'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('physical_inventory.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business = Business::find($business_id);

        $physical_inventory = PhysicalInventory::find($id);

        // Change state for the first time
        if ($physical_inventory->status == 'new') {
            $physical_inventory->status = 'process';
            $physical_inventory->processed_by = request()->session()->get('user.id');
            $physical_inventory->save();
        }

        if (request()->ajax()) {
            $datatable = $this->getData($id, 1);
            return $datatable;
        }

        // Enable fields
        $is_editable = 1;

        // Physical inventory record date
        $physical_inventory_record_date = $business->physical_inventory_record_date;

        return view('physical_inventory.edit')
            ->with(compact('physical_inventory', 'is_editable', 'physical_inventory_record_date'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PhysicalInventory  $physicalInventory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('physical_inventory.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $physical_inventory = PhysicalInventory::find($id);

                $transaction = Transaction::where('business_id', $physical_inventory->business_id)
                    ->where('location_id', $physical_inventory->location_id)
                    ->where('warehouse_id', $physical_inventory->warehouse_id)
                    ->where('ref_no', $physical_inventory->code)
                    ->where('type', 'physical_inventory')
                    ->first();

                if (! empty($transaction)) {
                    // Delete kardex lines
                    $this->transactionUtil->deleteKardexByTransaction($physical_inventory->id, true);
    
                    // Delete inputs
                    $delete_purchase_line_ids = [];

                    foreach ($transaction->purchase_lines as $purchase_line) {
                        $delete_purchase_line_ids[] = $purchase_line->id;

                        $this->productUtil->decreaseProductQuantity(
                            $purchase_line->product_id,
                            $purchase_line->variation_id,
                            $transaction->location_id,
                            $purchase_line->quantity,
                            null,
                            $transaction->warehouse_id
                        );
                    }

                    PurchaseLine::where('transaction_id', $transaction->id)
                        ->whereIn('id', $delete_purchase_line_ids)
                        ->delete();

                    // Update mapping of purchase and sell.
                    $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase(
                        $transaction->status,
                        $transaction,
                        $transaction->purchase_lines
                    );

                    // Delete outputs
                    $deleted_sell_lines = $transaction->sell_lines;

                    $deleted_sell_lines_ids = $deleted_sell_lines->pluck('id')->toArray();

                    $this->transactionUtil->deleteSellLines(
                        $deleted_sell_lines_ids,
                        $transaction->location_id,
                        $transaction->warehouse_id
                    );

                    $transaction->status = 'draft';

                    $business = [
                        'id' => $transaction->business_id,
                        'accounting_method' => request()->session()->get('business.accounting_method'),
                        'location_id' => $transaction->location_id
                    ];

                    $this->transactionUtil->adjustMappingPurchaseSell(
                        'final',
                        $transaction,
                        $business,
                        $deleted_sell_lines_ids
                    );

                    $transaction->delete();
                }

                DB::table('physical_inventory_lines')
                    ->where('physical_inventory_id', $physical_inventory->id)
                    ->delete();

                $physical_inventory->delete();

                $output = [
                    'success' => 1,
                    'msg' => __('physical_inventory.delete_success')
                ];

                DB::commit();

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
     * Change the status of the physical inventory.
     * 
     * @param  int  $id
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id, $status) {
        // Check status
        if ($status == 'review') {
            if (! auth()->user()->can('physical_inventory.send_to_review')) {
                abort(403, 'Unauthorized action.');
            }

            $msg = __('physical_inventory.successfully_submitted_for_review');

        } else {
            if (! auth()->user()->can('physical_inventory.authorize')) {
                abort(403, 'Unauthorized action.');
            }

            $msg = __('physical_inventory.successfully_authorized');
        }

        try {
            $user_id = request()->session()->get('user.user_id');

            $physical_inventory = PhysicalInventory::find($id);
            $physical_inventory->status = $status;
            $physical_inventory->updated_by = $user_id;

            if ($status == 'review') {
                $physical_inventory->reviewed_by = $user_id;
            } else {
                $physical_inventory->authorized_by = $user_id;
            }

            $physical_inventory->save();

            $output = [
                'success' => 1,
                'msg' => $msg
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('physical-inventory')->with('status', $output);
    }

    /**
     * Change physical inventory status to finished and run stock adjustment.
     * 
     * @param  int  $id
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function finalize($id)
    {
        if (! auth()->user()->can('physical_inventory.finalize')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $user_id = request()->session()->get('user.id');
            $business_id = request()->session()->get('user.business_id');

            $business = Business::find($business_id);

            $physical_inventory = PhysicalInventory::find($id);
            $physical_inventory->status = 'finalized';
            $physical_inventory->finished_by = $user_id;
            $physical_inventory->updated_by = $user_id;

            if ($business->physical_inventory_record_date == 'current_date' || is_null($physical_inventory->end_date)) {
                $physical_inventory->end_date = \Carbon::now()->format('Y-m-d');
            }

            $physical_inventory->save();

            $vld = [];

            foreach ($physical_inventory->physical_inventory_lines as $item) {
                $v = VariationLocationDetails::where('variation_id', $item->variation_id)
                    ->where('warehouse_id', $physical_inventory->warehouse_id)
                    ->where('location_id', $physical_inventory->location_id)
                    ->first();
                
                array_push($vld, $v->id);
            }

            $pil = PhysicalInventoryLine::
                leftJoin(
                    'variation_location_details',
                    function($join) use ($vld) {
                        $join->on('variation_location_details.variation_id', '=', 'physical_inventory_lines.variation_id');

                        $join->where(function($query) use ($vld) {
                            $query->whereIn('variation_location_details.id', $vld);
                        });
                    }
                )
                ->where('physical_inventory_id', $id)
                ->select(
                    'physical_inventory_lines.*',
                    'variation_location_details.qty_available as stock',
                    DB::raw("(SELECT purchase_price_inc_tax FROM purchase_lines WHERE variation_id = physical_inventory_lines.variation_id ORDER BY id DESC LIMIT 1) as price"),
                    DB::raw("physical_inventory_lines.quantity - variation_location_details.qty_available as difference")
                )
                ->get();

            foreach ($pil as $item) {
                $pil_upd = PhysicalInventoryLine::find($item->id);
                $pil_upd->stock = $item->stock;
                $pil_upd->difference = $item->difference;
                $pil_upd->price = $item->price;
                $pil_upd->updated_by = $user_id;
                $pil_upd->save();

                if ($item->difference > 0) {
                    $mov_type = 'input';
                } else if ($item->difference < 0) {
                    $mov_type = 'output';
                } else {
                    $mov_type = null;
                }

                if (! is_null($mov_type)) {
                    // Update stock
                    $this->productUtil->updateProductQuantity(
                        $physical_inventory->location_id,
                        $item->product_id,
                        $item->variation_id,
                        $item->quantity,
                        $item->stock,
                        null,
                        $physical_inventory->warehouse_id
                    );

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

                    // Execution date
                    if ($business->physical_inventory_record_date == 'current_date') {
                        $date = \Carbon::now()->format('Y-m-d H:i:s');

                    } else {
                        $end_date = \Carbon::createFromFormat('Y-m-d', $physical_inventory->end_date);
                        $date = $end_date->format('Y-m-d H:i:s');
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

            // Mapping and output
            $output = $this->mapping($id);
            
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('physical-inventory')->with('status', $output);
    }

    /**
     * Get data from physical inventory lines.
     * 
     * @param  int  $id
     * @param  int  $is_editable
     * @return \Illuminate\Http\Response
     */
    public function getData($id, $is_editable)
    {
        $pi = PhysicalInventory::find($id);

        $vld = [];

        foreach ($pi->physical_inventory_lines as $item) {
            $v = VariationLocationDetails::where('variation_id', $item->variation_id)
                ->where('warehouse_id', $pi->warehouse_id)
                ->where('location_id', $pi->location_id)
                ->first();

            if (is_null($v)) {
                $v = new VariationLocationDetails();
                $v->variation_id = $item->variation_id;
                $v->product_id = $item->product_id;
                $v->location_id = $pi->location_id;
                $v->product_variation_id = $item->variation->product_variation_id;
                $v->qty_available = 0;
                $v->warehouse_id = $pi->warehouse_id;
                $v->save();
            }

            array_push($vld, $v->id);
        }

        $lines = PhysicalInventoryLine::leftJoin('variations', 'variations.id', 'physical_inventory_lines.variation_id')
            ->leftJoin('products', 'products.id', 'physical_inventory_lines.product_id')
            ->leftJoin('physical_inventories', 'physical_inventories.id', 'physical_inventory_lines.physical_inventory_id')
            ->leftjoin('units', 'products.unit_id', 'units.id')
            ->leftJoin(
                'variation_location_details',
                function($join) use ($vld) {
                    $join->on('variation_location_details.variation_id', '=', 'physical_inventory_lines.variation_id');

                    $join->where(function($query) use ($vld) {
                        $query->whereIn('variation_location_details.id', $vld);
                    });
                }
            )
            ->join('product_variations', 'product_variations.id', 'variations.product_variation_id')
            ->where('physical_inventory_lines.physical_inventory_id', $id)
            ->select(
                'variations.sub_sku',
                DB::raw("IF(product_variations.is_dummy = 0, CONCAT(products.name, ' (', product_variations.name, ':', variations.name, ')'), products.name) as product_name"),
                DB::raw("IF(physical_inventory_lines.stock = 0, (IF(physical_inventories.status = 'finalized', physical_inventory_lines.stock, variation_location_details.qty_available)), physical_inventory_lines.stock) as qty_available"),
                DB::raw("(SELECT purchase_price_inc_tax FROM purchase_lines WHERE variation_id = variations.id ORDER BY id DESC LIMIT 1) as last_purchased_price"),
                'units.allow_decimal as unit_allow_decimal',
                'products.id as product_id',
                'variations.id as variation_id',
                'physical_inventory_lines.quantity as quantity',
                'physical_inventory_lines.id as id',
                DB::raw("IF(physical_inventory_lines.difference = 0, (IF(physical_inventories.status = 'finalized', physical_inventory_lines.difference, physical_inventory_lines.quantity - variation_location_details.qty_available)), physical_inventory_lines.difference) as difference"),
                'physical_inventories.status as status',
            )
            ->orderBy('physical_inventory_lines.id', 'desc');

        $datatable = Datatables::of($lines)
            ->filterColumn('product_name', function($lines, $keyword) {
                $lines->whereRaw("IF(product_variations.is_dummy = 0, CONCAT(products.name, ' (', product_variations.name, ':', variations.name, ')'), products.name) like ?", ["%{$keyword}%"]);
            })
            ->editColumn(
                'qty_available', function($row) {
                    return $this->productUtil->num_f($row->qty_available);
                }
            )
            ->editColumn(
                'difference', function($row) {
                    return $this->productUtil->num_f(abs($row->difference));
                }
            )
            ->editColumn(
                'last_purchased_price', function($row) {
                    return '<span class="display_currency" data-currency_symbol="true">' . $this->productUtil->num_f($row->last_purchased_price) . '</span>';
                }
            );

        if ($is_editable == 1) {
            // Edit view
            $datatable = $datatable->editColumn(
                    'quantity', function($row) {
                        $html = 
                        '<div class="input-group">
                        <input type="text" data-line-id="' . $row->id . '"
                            name="line-' . $row->id . '"
                            data-line-quantity="' . $this->productUtil->num_f($row->quantity) . '"
                            data-rule-allow-decimal="' . $row->unit_allow_decimal . '"
                            class="form-control input_number quantity_pil"
                            value="' . $this->productUtil->num_f($row->quantity) . '">
                        </div>';
                        
                        return $html;
                    }
                )
                ->addColumn(
                    'action', function($row) {
                        $html = '<i  data-line-id="' . $row->id . '" class="fa fa-trash cursor-pointer delete_pil" aria-hidden="true"></i>';

                        return $html;
                    }
                )
                ->rawColumns(['action', 'quantity', 'qty_available', 'difference', 'last_purchased_price'])
                ->toJson();

        } else {
            // Show view
            $datatable = $datatable->editColumn(
                    'quantity', function($row) {
                        return $this->productUtil->num_f($row->quantity);
                    }
                )
                ->addColumn('action', '')
                ->rawColumns(['quantity', 'qty_available', 'difference', 'last_purchased_price'])
                ->toJson();
        }

        return $datatable;
    }

    /**
     * Get produts for search bar.
     * 
     * @return json
     */
    public function getProducts()
    {
        if (request()->ajax()) {
            // Params
            $term = request()->input('term', '');
            $category = request()->input('category', 'full');

            $business_id = request()->session()->get('user.business_id');

            $products = Product::join('variations', 'products.id', 'variations.product_id')
                ->where('status', 'active')
                ->where('products.business_id', $business_id)
                ->whereNull('variations.deleted_at');

            if (config('app.business') == 'optics') {
                // Only products
                if ($category == 'product') {
                    $products = $products->where('products.clasification', 'product');
    
                // Only materials
                } else if ($category == 'material') {
                    $products = $products->where('products.clasification', 'material');
    
                // Only products and materials
                } else if ($category == 'full') {
                    $products = $products->whereIn('products.clasification', ['product', 'material']);
                }
            }

            // Include search
            if (! empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                });
            }

            $products->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'variations.id as variation_id',
                'variations.name as variation',
                'variations.sub_sku',
                'products.sku',
                'products.enable_stock'
            );

            $result = $products->orderBy('products.name')->get();

            return json_encode($result);
        }
    }

    /**
     * Add a mapping between purchase and sell lines.
     * 
     * @param  int  $id
     * @return array
     */
    public function mapping($id)
    {
        try {
            DB::beginTransaction();
            
            $physical_inventory = PhysicalInventory::find($id);

            $transaction = Transaction::create([
                'business_id' => $physical_inventory->business_id,
                'location_id' => $physical_inventory->location_id,
                'warehouse_id' => $physical_inventory->warehouse_id,
                'status' => 'received',
                'ref_no' => $physical_inventory->code,
                'transaction_date' => $physical_inventory->updated_by,
                'created_by' => $physical_inventory->created_by,
                'document_types_id' => 0,
                'type' => 'physical_inventory',
            ]);

            $sell_lines = collect();
            
            foreach ($physical_inventory->physical_inventory_lines as $item) {
                if ($item->difference > 0) {
                    // Purchase line
                    $purchase_line = PurchaseLine::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $item->product_id,
                        'variation_id' => $item->variation_id,
                        'quantity' => abs($item->difference),
                        'purchase_price' => $item->price,
                        'purchase_price_inc_tax' => $item->price
                    ]);

                } else if ($item->difference < 0) {
                    // Sell line
                    $sell_line = TransactionSellLine::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $item->product_id,
                        'variation_id' => $item->variation_id,
                        'quantity' => abs($item->difference),
                        'unit_price' => $item->price,
                        'unit_price_inc_tax' => $item->price,
                        'unit_price_exc_tax' => $item->price,
                        'tax_amount' => 0
                    ]);

                    $sell_lines->push($sell_line);
                }
            }

            if (! empty($sell_lines)) {
                $business = [
                    'id' => $physical_inventory->business_id,
                    'accounting_method' => request()->session()->get('business.accounting_method'),
                    'location_id' => $physical_inventory->location_id
                ];
                
                $this->transactionUtil->mapPurchaseSell($business, $sell_lines, 'purchase');
            }

            DB::commit();

            return [
                'success' => 1,
                'msg' => __('physical_inventory.successfully_finalized')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            return [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
    }

    /**
     * Update execution date.
     *
     * @return json
     */
    public function updateExecutionDate()
    {
        if (request()->ajax()) {
            $physical_inventory_id = request()->input('physical_inventory_id');
            $end_date = request()->input('end_date');

            try {
                $physical_inventory = PhysicalInventory::find($physical_inventory_id);
                $physical_inventory->end_date = $this->productUtil->uf_date($end_date);
                $physical_inventory->save();

                $output = [
                    'success' => true,
                    'msg' => __('physical_inventory.execution_date_successfully_updated')
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
    }

    /**
     * Update code.
     *
     * @return json
     */
    public function updateCode()
    {
        if (request()->ajax()) {
            $physical_inventory_id = request()->input('physical_inventory_id');
            $code = request()->input('code');

            try {
                $physical_inventory = PhysicalInventory::find($physical_inventory_id);
                $physical_inventory->code = $code;
                $physical_inventory->save();

                $output = [
                    'success' => true,
                    'msg' => __('physical_inventory.code_successfully_updated')
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
    }
}
