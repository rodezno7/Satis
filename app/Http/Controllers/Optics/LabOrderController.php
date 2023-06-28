<?php

namespace App\Http\Controllers\Optics;

use App\BusinessLocation;
use App\Customer;
use App\Employees;
use App\Optics\ExternalLab;
use App\Optics\GraduationCard;
use App\Optics\LabOrder;
use App\Optics\LabOrderDetail;
use App\Optics\Patient;
use App\Optics\StatusLabOrder;
use App\Optics\StatusLabOrderStep;
use App\Product;
use App\PurchaseLine;
use App\StockAdjustmentLine;
use App\Transaction;
use App\TransactionSellLine;
use App\Utils\TransactionUtil;
use App\Warehouse;
use App\Utils\Util;
use App\Variation;
use App\VariationLocationDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DB;
use stdClass;
use Yajra\DataTables\DataTables;

class LabOrderController extends Controller
{
    public function __construct(Util $util, TransactionUtil $transactionUtil)
    {
        $this->util = $util;
        $this->transactionUtil = $transactionUtil;

        $this->crystal_warehouse = 1;
        
        // Binnacle data
        $this->module_name = 'lab_order';

        if (config('app.disable_sql_req_pk')) {
            DB::statement('SET SESSION sql_require_primary_key=0');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! auth()->user()->can('lab_order.view')) {
            abort(403, "Unauthorized action.");
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            ini_set('memory_limit', '256M');

            // Set maximum php execution time
            if (request()->get('length') == -1) {
                ini_set('max_execution_time', 0);
            }

            // Parameters
            $params = [
                // Filters
                'location_id' => $request->input('location_id'),
                'status_id' => $request->input('status_id'),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date,

                // Datatable parameters
                'start_record' => request()->get('start'),
                'page_size' => request()->get('length'),
                'search' => request()->get('search'),
                'order' => request()->get('order')
            ];

            // Lab orders
            $lab_orders = $this->getLabOrders($params);

            $datatable = Datatables::of($lab_orders['data'])
                ->editColumn(
                    'correlative', function ($row) {
                        if ($row->is_annulled) {
                            $html = '<span style="color: red;">' . $row->correlative . '<br><small>' . $row->document . ' - ' . __('lab_order.annulled') . '</small></span>';
                        } else {
                            $html = $row->correlative . '<br><small>' . $row->document . '</small>';
                        }
                        return $html;
                    }
                )
                ->editColumn(
                    'customer',
                    '{{ $customer }}<br><small><strong>@lang("graduation_card.patient"):</strong> {{ $patient }}</small>'
                )
                ->editColumn(
                    'status', function ($row) {
                        if ($row->is_annulled) {
                            $html = '<i class="fa fa-circle" style="color: red;"></i>&nbsp; <span style="color: red;">' . __('lab_order.annulled') . '</span>';
                        } else {
                            $html = '<i class="fa fa-circle" style="color: ' . $row->color . ';"></i>&nbsp; ' . $row->status;
                        }
                        return $html;
                    }
                )
                ->editColumn(
                    'no_order', function ($row) {
                        if ($row->is_annulled) {
                            $html = '<span style="color: red;">' . $row->no_order . '</span>';
                            if ($row->number_times > 1) {
                                $html .= '<br><span style="color: red;"><small>' . __('lab_order.number_times_msg', ['number' => $row->number_times]) . '</small></span>';
                            }
                        } else {
                            $html = $row->no_order;
                            if ($row->number_times > 1) {
                                $html .= '<br><small>' . __('lab_order.number_times_msg', ['number' => $row->number_times]) . '</small>';
                            }
                        }
                        return $html;
                    }
                )
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs btn-actions" data-lab-order-id="{{ $id }}" data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            ' <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        </ul>
                    </div>'
                )
                ->addColumn(
                    'checkbox', function ($row) {
                        return "<input type='checkbox' class='row-select' value='$row->id'>";
                    }
                )
                ->rawColumns([
                    'correlative',
                    'customer',
                    'status',
                    'no_order',
                    'action',
                    'checkbox'
                ])
                ->setTotalRecords($lab_orders['count'])
                ->setFilteredRecords($lab_orders['count'])
                ->skipPaging()
                ->toJson();

            return $datatable;
        }
        
        $external_labs = ExternalLab::pluck('name', 'id');

        $business_locations = BusinessLocation::pluck('name', 'id');

        $default_location = null;
        
        // Warehouses
        $warehouses = Warehouse::select('id', 'name')
            ->where('status', 'active')
            ->pluck('name', 'id');

        $default_warehouse = $this->crystal_warehouse;
        
        $employees = Employees::select('id', DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"));
        $employees = $employees->pluck('full_name', 'id');

        // Header text and columns
        $auxiliar = 0;
        if (!empty(request()->get('opc'))) {
            $auxiliar = request()->get('opc');
        }

        // Locations
        if (auth()->user()->can('lab_order.update')) {
            $locations = BusinessLocation::all()->pluck('name', 'id');
            $locations = $locations->prepend(__("kardex.all_2"), 'all');

            $default_location = null;

        } else {
            $locations = BusinessLocation::forDropdown($business_id, false, false);

            $default_location = null;

            // Access only to one locations
            if (count($locations) == 1) {
                foreach ($locations as $id => $name) {
                    $default_location = $id;
                }
                
            // Access to all locations
            } else if (auth()->user()->permitted_locations() == 'all') {
                $locations = $locations->prepend(__("kardex.all_2"), 'all');
            }
        }

        // Status lab orders
        $status = StatusLabOrder::where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id');
        $status = $status->prepend(__('kardex.all'), 'all');

        // Status lab orders (change status)
        $change_status = StatusLabOrder::where('is_default', 0)
            ->where('print_order', 0)
            ->where('second_time', 0)
            ->where('material_download', 0)
            ->where('save_and_print', 0)
            ->where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id');
        $change_status = $change_status->prepend(__('messages.please_select'), '');

        return view('optics.lab_order.index')
            ->with(compact(
                'business_locations',
                'default_location',
                'warehouses',
                'external_labs',
                'employees',
                'auxiliar',
                'locations',
                'status',
                'default_warehouse',
                'change_status'
            ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('lab_order.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Data for form
        $business_id = request()->session()->get('user.business_id');

        $customers = Customer::where('business_id', $business_id)
            ->pluck('name', 'id');

        $patients = Patient::where('business_id', $business_id)
            ->pluck('full_name', 'id');

        $status_lab_orders = StatusLabOrder::where('business_id', $business_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        $external_labs = ExternalLab::where('business_id', $business_id)
            ->pluck('name', 'id');
        
        $products = Product::where('business_id', $business_id)
            ->pluck('name', 'id');

        $code = $this->util->generateLabOrderCode();

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $default_location = null;
        if (count($business_locations) == 1) {
            foreach ($business_locations as $id => $name) {
                $default_location = $id;
            }
        }
        $warehouses = Warehouse::select('id', 'name')
            ->where('status', 'active')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        return view('optics.lab_order.create_lab_order')
            ->with(compact(
                'customers',
                'patients',
                'status_lab_orders',
                'business_locations',
                'bl_attributes',
                'default_location',
                'warehouses',
                'code',
                'external_labs',
                'products'
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
        if (!auth()->user()->can('lab_order.create')) {
            abort(403, 'Unauthorized action.');
        }

        $validate = [
            'patient_id' => 'required',
            'hoop_type' => 'required',
            'delivery' => 'required',
            'lab_customer_id' => 'required',
            'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
        ];

        if (empty($request->input('transaction_id'))) {
            $validate['location_lo'] = 'required';

            if (!auth()->user()->can('lab_order.create_without_invoice')) {
                $validate['invoice_lo'] = 'required';
            }
        }

        if (!empty($request->input('is_reparation'))) {
            if ($request->input('is_reparation') != 1) {
                if (empty($request->input('di'))) {
                    $validate['dnsp_os'] = 'required';
                    $validate['dnsp_od'] = 'required';

                } elseif (empty($request->input('dnsp_os'))) {
                    if (empty($request->input('dnsp_od'))) {
                        $validate['di'] = 'required';

                    } else {
                        $validate['dnsp_os'] = 'required';
                    }

                } elseif (empty($request->input('dnsp_od'))) {
                    if (empty($request->input('dnsp_os'))) {
                        $validate['di'] = 'required';

                    } else {
                        $validate['dnsp_od'] = 'required';
                    }
                }
            }
        }

        $validate_msg = [
            'patient_id.required' => __('lab_order.patient_validation'),
            'hoop_type.required' => __('lab_order.hoop_type_validation'),
            'delivery.required' => __('lab_order.delivery_validation'),
            'lab_customer_id.required' => __('lab_order.customer_validation'),
            'location_lo.required' => __('lab_order.customer_validation'),
            'invoice_lo.required' => __('lab_order.customer_validation'),
            'dnsp_os.required' => __('lab_order.dnsp_os_validation'),
            'dnsp_od.required' => __('lab_order.dnsp_od_validation'),
            'di.required' => __('lab_order.di_validation'),
            'document.file' => __('lab_order.document_file_validation'),
            'document.max' => __('lab_order.document_max_validation', ['number' => config('constants.document_size_limit') / 1000])
        ];

        $request->validate($validate, $validate_msg);

        $msg_error = __("messages.something_went_wrong");

        try {
            $input = $request->only([
                'no_order',
                'lab_customer_id',
                'patient_id',
                'is_reparation',
                'size',
                'color',
                'glass',
                'ar',
                'job_type',
                'check_ext_lab',
                'external_lab_id',
                'is_urgent',
                'is_own_hoop',
                'hoop_type',
                'glass_os',
                'glass_od'
            ]);

            $input_graduation_card = $request->only([
                'patient_id',
                'sphere_os',
                'sphere_od',
                'cylindir_os',
                'cylindir_od',
                'axis_os',
                'axis_od',
                'base_os',
                'base_od',
                'addition_os',
                'addition_od',
                'di',
                'ao',
                'dnsp_os',
                'dnsp_od',
                'ap',
                'optometrist',
                'is_prescription',
                'balance_os',
                'balance_od'
            ]);

            $input_graduation_card['optometrist'] = $input_graduation_card['optometrist'] == 0 ? null : $input_graduation_card['optometrist'];

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input_graduation_card['business_id'] = $business_id;

            if (!empty($request->input('transaction_id'))) {
                $input['transaction_id'] = $request->input('transaction_id');

            } elseif (empty($request->input('invoice_lo'))) {
                $input['business_location_id'] = $request->input('location_lo');

            } else {
                $msg_error = __('lab_order.exception_location_correlative');

                $transaction = Transaction::where('location_id', $request->input('location_lo'))
                    ->where('correlative', $request->input('invoice_lo'))
                    ->where('customer_id', $input['lab_customer_id'])
                    // ->where('business_id', $business_id)
                    ->first();

                $input['transaction_id'] = $transaction->id;
            }

            $msg_error = __("messages.something_went_wrong");

            $delivery =  Carbon::createFromFormat('d/m/Y H:i', $request->input('delivery'));
            $input['delivery'] = $delivery;

            if (!empty($input['is_own_hoop'])) {
                if ($input['is_own_hoop'] == 1) {
                    $input['hoop_name'] = $request->input('hoop_name');
                }
            } else {
                $input['hoop'] = $request->input('hoop');
            }

            // Upload document
            $input_graduation_card['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            DB::beginTransaction();

            $user_id = $request->session()->get('user.id');

            // Graduation card
            $graduation_card = GraduationCard::create($input_graduation_card);

            // Store binnacle
            $this->util->registerBinnacle(
                'graduation_card',
                'create',
                null,
                $graduation_card
            );
            
            // Lab Order
            $input['graduation_card_id'] = $graduation_card->id;

            $input['customer_id'] = $input['lab_customer_id'];

            $slo = StatusLabOrder::where('is_default', 1)->first();

            if (! empty($slo)) {
                $input['status_lab_order_id'] = $slo->id;
            }

            $lab_order = LabOrder::create($input);

            // Store binnacle
            $this->util->registerBinnacle(
                $this->module_name,
                'create',
                $lab_order->no_order,
                $lab_order
            );

            // Lab Order Detail
            $variation_ids = $request->input('variation_id');
            $quantity = $request->input('quantity');
            $location_ids = $request->input('location_id');
            $warehouse_ids = $request->input('warehouse_id');
    
            if (!empty($variation_ids)) {
                $cont = 0;                
                while($cont < count($variation_ids))
                {
                    $detail = new LabOrderDetail;
                    $detail->lab_order_id = $lab_order->id;
                    $detail->variation_id = $variation_ids[$cont];
                    $detail->location_id = $location_ids[$cont];
                    $detail->warehouse_id = $warehouse_ids[$cont];
                    $detail->quantity = $quantity[$cont];
                    $detail->save();

                    $stock = VariationLocationDetails::where('variation_id', $variation_ids[$cont])
                        ->where('location_id', $location_ids[$cont])
                        ->where('warehouse_id', $warehouse_ids[$cont])
                        ->first();

                    $stock->qty_available = $stock->qty_available - $quantity[$cont];
                    $stock->save();

                    $cont = $cont + 1;
                } 
            }

            DB::commit();

            $output = [
                'success' => true,
                'data' => $lab_order,
                'msg' => __("lab_order.added_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => $msg_error
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LabOrder  $labOrder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $lab_order = DB::table('lab_orders as lo')
                ->leftJoin('graduation_cards as gc', 'gc.id', 'lo.graduation_card_id')
                ->leftJoin('variations as vh', 'vh.id', 'lo.hoop')
                ->leftJoin('variations as vg_os', 'vg_os.id', 'lo.glass_os')
                ->leftJoin('variations as vg_od', 'vg_od.id', 'lo.glass_od')
                ->leftJoin('variations as vg', 'vg.id', 'lo.glass')
                ->leftJoin('products as ph', 'ph.id', 'vh.product_id')
                ->leftJoin('products as pg_os', 'pg_os.id', 'vg_os.product_id')
                ->leftJoin('products as pg_od', 'pg_od.id', 'vg_od.product_id')
                ->leftJoin('products as pg', 'pg.id', 'vg.product_id')
                ->leftJoin('customers as c', 'c.id', 'lo.customer_id')
                ->leftJoin('patients as p', 'p.id', 'lo.patient_id')
                ->leftJoin('transactions as t', 'lo.transaction_id', 't.id')
                ->leftJoin('business_locations as bl', 't.location_id', 'bl.id')
                ->leftJoin('employees as e', 'e.id', 'gc.optometrist')
                ->leftJoin('status_lab_orders as slo', 'lo.status_lab_order_id', 'slo.id')
                ->select(
                    'lo.*',
                    'lo.id as loid',
                    'gc.*',
                    'ph.name as hoop_value',
                    'pg_os.name as glass_os_value',
                    'pg_od.name as glass_od_value',
                    'pg.name as glass_value',
                    DB::raw('DATE_FORMAT(delivery, "%d/%m/%Y %H:%i") as delivery_value'),
                    'c.name as customer_value',
                    'p.full_name as patient_value',
                    'ph.measurement',
                    'bl.name as location',
                    't.correlative',
                    't.customer_name',
                    DB::raw("CONCAT(COALESCE(e.first_name, ''), ' ', COALESCE(e.last_name, '')) as optometrist"),
                    'slo.name as status_value',
                    'slo.color as color_value'
                )
                ->where('lo.id', $id)
                ->first();

            return view('optics.lab_order.show')
                ->with(compact('lab_order'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LabOrder  $labOrder
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('lab_order.update')) {
            abort(403, 'Unauthorized action.');
        }

        $slo = StatusLabOrder::where('second_time', 1)->first();

        $slo_id = ! empty($slo) ? $slo->id : 0;

        $order = collect(DB::select('CALL get_lab_order(?, ?)', [$id, $slo_id]))->first();

        $hoop = collect(DB::select('CALL get_lab_order_products(?)', [$order->hoop]))->first();
        
        $glass = collect(DB::select('CALL get_lab_order_products(?)', [$order->glass]))->first();
        
        $glass_os = collect(DB::select('CALL get_lab_order_products(?)', [$order->glass_os]))->first();
        
        $glass_od = collect(DB::select('CALL get_lab_order_products(?)', [$order->glass_od]))->first();

        $result_array = [
            'loid' => $order->loid,
            'final_total' => $order->final_total,
            'patient_id' => $order->patient_id,
            'patient_name' => $order->patient_name,
            'no_order' => $order->no_order,
            'customer_name' => $order->customer_name,
            'customer_id' => $order->customer_id,
            'is_reparation' => $order->is_reparation,
            'is_prescription' => $order->is_prescription,
            'sphere_od' => $order->sphere_od,
            'sphere_os' => $order->sphere_os,
            'cylindir_od' => $order->cylindir_od,
            'cylindir_os' => $order->cylindir_os,
            'axis_od' => $order->axis_od,
            'axis_os' => $order->axis_os,
            'base_od' => $order->base_od,
            'base_os' => $order->base_os,
            'addition_od' => $order->addition_od,
            'addition_os' => $order->addition_os,
            'dnsp_od' => $order->dnsp_od,
            'dnsp_os' => $order->dnsp_os,
            'di' => $order->di,
            'ao' => $order->ao,
            'ap' => $order->ap,
            'is_own_hoop' => $order->is_own_hoop,
            'hoop_name' => $order->hoop_name,
            'hoop_id' => ! empty($order->hoop) ? $order->hoop : null,
            'hoop_value' => ! empty($order->hoop) ? $hoop->product_name : null,
            'size' => $order->size,
            'color' => $order->color,
            'hoop_type' => $order->hoop_type,
            'glass_id' => ! empty($order->glass) ? $order->glass : null,
            'glass_value' => ! empty($order->glass) ? $glass->product_name : null,
            'glass_os_id' => ! empty($order->glass_os) ? $order->glass_os : null,
            'glass_os_value' => ! empty($order->glass_os) ? $glass_os->product_name : null,
            'glass_od_id' => ! empty($order->glass_od) ? $order->glass_od : null,
            'glass_od_value' => ! empty($order->glass_od) ? $glass_od->product_name : null,
            'job_type' => $order->job_type,
            'check_ext_lab' => $order->check_ext_lab,
            'external_lab_id' => $order->external_lab_id,
            'ar' => $order->ar,
            'status_lab_order_id' => $order->status_lab_order_id,
            'delivery_value' => $order->delivery_value,
            'employee_id' => $order->employee_id,
            'optometrist' => $order->optometrist,
            'reason' => $order->reason,
            'transaction_id' => $order->transaction_id,
            'location_id' => $order->location_id,
            'correlative' => $order->correlative,
            'business_location_id' => $order->business_location_id,
            'balance_os' => $order->balance_os,
            'balance_od' => $order->balance_od,
            'show_fields' => $order->show_fields,
            'save_and_print' => $order->save_and_print
        ];

        $result = json_decode(json_encode($result_array), FALSE);

        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LabOrder  $labOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('lab_order.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $msg_error = __("messages.something_went_wrong");

            try {
                $lab_order = LabOrder::findOrFail($id);

                // Clone record before action
                $lab_order_old = clone $lab_order;

                // $lab_details['no_order'] = $request->input('eno_order');
                $lab_details['customer_id'] = $request->input('ecustomer_id');
                $lab_details['patient_id'] = $request->input('epatient_id');
                $lab_details['color_base_os'] = $request->input('ecolor_base_os');
                $lab_details['color_base_od'] = $request->input('ecolor_base_od');
                $lab_details['is_reparation'] = $request->input('eis_reparation');
                // $lab_details['hoop'] = $request->input('ehoop');
                $lab_details['size'] = $request->input('esize');
                $lab_details['color'] = $request->input('ecolor');
                // $lab_details['glass'] = $request->input('eglass');
                $lab_details['ar'] = $request->input('ear');
                $lab_details['job_type'] = $request->input('ejob_type');
                $lab_details['check_ext_lab'] = $request->input('echeck_ext_lab');
                $lab_details['external_lab_id'] = $request->input('eexternal_lab_id');
                $lab_details['is_urgent'] = $request->input('eis_urgent');
                $lab_details['is_own_hoop'] = $request->input('eis_own_hoop');
                $lab_details['employee_id'] = $request->input('eemployee_id');
                $lab_details['reason'] = $request->input('ereason');
                $lab_details['return_stock'] = !empty($request->input('ereturn_stock')) ? $request->input('ereturn_stock') : 0;

                $graduation_card = GraduationCard::findOrFail($lab_order->graduation_card_id);

                // Clone record before action
                $graduation_card_old = clone $graduation_card;

                $gc_details['patient_id'] = $request->input('epatient_id');
                $gc_details['optometrist'] = $request->input('eoptometrist');
                $gc_details['sphere_os'] = $request->input('esphere_os');
                $gc_details['sphere_od'] = $request->input('esphere_od');
                $gc_details['cylindir_os'] = $request->input('ecylindir_os');
                $gc_details['cylindir_od'] = $request->input('ecylindir_od');
                $gc_details['axis_os'] = $request->input('eaxis_os');
                $gc_details['axis_od'] = $request->input('eaxis_od');
                $gc_details['base_os'] = $request->input('ebase_os');
                $gc_details['base_od'] = $request->input('ebase_od');
                $gc_details['addition_os'] = $request->input('eaddition_os');
                $gc_details['addition_od'] = $request->input('eaddition_od');
                $gc_details['di'] = $request->input('edi');
                $gc_details['ao'] = $request->input('eao');
                $gc_details['dnsp_os'] = $request->input('ednsp_os');
                $gc_details['dnsp_od'] = $request->input('ednsp_od');
                $gc_details['ap'] = $request->input('eap');
                $gc_details['balance_os'] = !empty($request->input('ebalance_os')) ? $request->input('ebalance_os') : 0;
                $gc_details['balance_od'] = !empty($request->input('ebalance_od')) ? $request->input('ebalance_od') : 0;

                $slo = StatusLabOrder::find($request->input('estatus_lab_order_id'));

                if (! empty($slo)) {
                    $lab_details['status_lab_order_id'] = $request->input('estatus_lab_order_id');
                }

                if (empty($request->input('einvoice_lo'))) {
                    $lab_details['business_location_id'] = $request->input('elocation_lo');
    
                } else {
                    $msg_error = __('lab_order.exception_location_correlative');
    
                    $transaction = Transaction::where('location_id', $request->input('elocation_lo'))
                        ->where('correlative', $request->input('einvoice_lo'))
                        ->where('customer_id', $lab_details['customer_id'])
                        // ->where('business_id', $business_id)
                        ->first();
    
                    $lab_details['transaction_id'] = $transaction->id;
                }

                $msg_error = __("messages.something_went_wrong");

                $delivery =  Carbon::createFromFormat('d/m/Y H:i', $request->input('edelivery'));
                $lab_details['delivery'] = $delivery;

                // Upload document
                // $document_name = $this->transactionUtil->uploadFile($request, 'edocument', 'documents');

                // if (! empty($document_name)) {
                //     $gc_details['edocument'] = $document_name;
                // }

                DB::beginTransaction();

                $user_id = $request->session()->get('user.id');

                $graduation_card->update($gc_details);

                // Store binnacle
                $this->util->registerBinnacle(
                    'graduation_card',
                    'update',
                    null,
                    $graduation_card_old,
                    $graduation_card
                );

                $lab_order->update($lab_details);

                // Store binnacle
                $this->util->registerBinnacle(
                    $this->module_name,
                    'update',
                    $lab_order->no_order,
                    $lab_order_old,
                    $lab_order
                );

                // Lab Order Detail
                $variation_ids = $request->input('evariation_id');
                $quantity = $request->input('equantity');
                // $location_ids = $request->input('elocation_id');
                $warehouse_ids = $request->input('ewarehouse_id');
                $item_ids = $request->input('item_id');

                // Saber si devolver stock
                $auxiliar = true;

                if (empty($item_ids)) {
                    $auxiliar = false;

                // Return stock
                } elseif ($lab_details['return_stock'] == 1 && !empty($lab_details['status_lab_order_id'])) {
                    $status = StatusLabOrder::find($lab_details['status_lab_order_id']);

                    if (!empty($status)) {
                        if ($status->name == 'Anulado') {
                            $auxiliar = false;
                        }
                    }
                }

                // Data to create or update output lines
                $lines_before = LabOrderDetail::where('lab_order_id', $lab_order->id)->get();

                if ($auxiliar) {
                    $cont = 0;
                    $items = array();
                    $rows = array();

                    while($cont < count($item_ids)) {
                        $wh = Warehouse::find($warehouse_ids[$cont]);

                        // Items anteriores
                        if ($item_ids[$cont] > 0) {
                            $detail = array(
                                'id' => $item_ids[$cont],
                                'lab_order_id' => $lab_order->id,
                                'variation_id' => $variation_ids[$cont],
                                'location_id' => $wh->business_location_id,
                                'warehouse_id' => $warehouse_ids[$cont],
                                'quantity' => $quantity[$cont]
                            );

                            array_push($items, $item_ids[$cont]);

                            array_push($rows, $detail);

                        // Items nuevos
                        } else {
                            if (! $this->validateStock($warehouse_ids[$cont], $variation_ids[$cont], $quantity[$cont], 0)) {
                                $variation = Variation::find($variation_ids[$cont]);

                                $mismatch_error = trans(
                                    'messages.purchase_sell_mismatch_exception',
                                    ['product' => $variation->sub_sku]
                                );

                                $output = [
                                    'success' => false,
                                    'msg' => $mismatch_error
                                ];

                                return $output;
                            }

                            $detail = new LabOrderDetail;
                            $detail->lab_order_id = $lab_order->id;
                            $detail->variation_id = $variation_ids[$cont];
                            $detail->location_id = $wh->business_location_id;
                            $detail->warehouse_id = $warehouse_ids[$cont];
                            $detail->quantity = $quantity[$cont];
                            $detail->save();

                            $stock = VariationLocationDetails::where('variation_id', $variation_ids[$cont])
                                ->where('location_id', $wh->business_location_id)
                                ->where('warehouse_id', $warehouse_ids[$cont])
                                ->first();

                            $stock->qty_available = $stock->qty_available - $quantity[$cont];
                            $stock->save();

                            array_push($items, $detail->id);
                        }

                        $cont = $cont + 1;
                    }

                    // Borrar items ya existentes
                    if (!empty($items)) {
                        $deleted_items = DB::table('lab_order_details as item')
                            ->leftjoin('lab_orders as lo', 'lo.id', 'item.lab_order_id')
                            ->select('item.*', 'lo.no_order')
                            ->whereNotIn('item.id', $items)
                            ->where('item.lab_order_id', $lab_order->id)
                            ->get();

                        foreach ($deleted_items as $item) {
                            $stock = VariationLocationDetails::where('variation_id', $item->variation_id)
                                ->where('location_id', $item->location_id)
                                ->where('warehouse_id', $item->warehouse_id)
                                ->first();

                            $stock->qty_available = $stock->qty_available + $item->quantity;
                            $stock->save();

                            // Delete kardex line
                            DB::table('kardexes')->where('reference', $item->no_order)
                                ->where('variation_id', $item->variation_id)
                                ->where('warehouse_id', $item->warehouse_id)
                                ->where('line_reference', $item->id)
                                ->delete();

                            DB::table('lab_order_details')->where('id', $item->id)->delete();
                        }
                    }

                    $items = json_decode(json_encode($rows), FALSE);

                    // Actualizar items ya existentes
                    foreach ($items as $row) {
                        $item = LabOrderDetail::where('id', $row->id)
                            ->first();

                        if ($row->quantity > $item->quantity) {
                            if (! $this->validateStock($row->warehouse_id, $row->variation_id, $row->quantity, $item->quantity)) {
                                $variation = Variation::find($row->variation_id);

                                $mismatch_error = trans(
                                    'messages.purchase_sell_mismatch_exception',
                                    ['product' => $variation->sub_sku]
                                );

                                $output = [
                                    'success' => false,
                                    'msg' => $mismatch_error
                                ];

                                return $output;
                            }

                            $difference = $row->quantity - $item->quantity;

                            $stock = VariationLocationDetails::where('variation_id', $row->variation_id)
                                ->where('location_id', $row->location_id)
                                ->where('warehouse_id', $row->warehouse_id)
                                ->first();

                            $stock->qty_available = $stock->qty_available - $difference;

                            $item->lab_order_id = $lab_order->id;
                            $item->variation_id = $row->variation_id;
                            $item->location_id = $row->location_id;
                            $item->warehouse_id = $row->warehouse_id;
                            $item->quantity = $row->quantity;

                            $item->save();
                            $stock->save();
                        }

                        if ($row->quantity < $item->quantity) {
                            $difference = $item->quantity - $row->quantity;

                            $stock = VariationLocationDetails::where('variation_id', $row->variation_id)
                                ->where('location_id', $row->location_id)
                                ->where('warehouse_id', $row->warehouse_id)
                                ->first();

                            $stock->qty_available = $stock->qty_available + $difference;

                            $item->lab_order_id = $lab_order->id;
                            $item->variation_id = $row->variation_id;
                            $item->location_id = $row->location_id;
                            $item->warehouse_id = $row->warehouse_id;
                            $item->quantity = $row->quantity;

                            $item->save();
                            $stock->save();
                        }

                        // if ($row->quantity == $item->quantity) {
                        //     $difference = $item->quantity - $row->quantity;
                        //     $stock = VariationLocationDetails::where('variation_id', $row->variation_id)
                        //         ->where('location_id', $row->location_id)
                        //         ->where('warehouse_id', $row->warehouse_id)
                        //         ->first();

                        //     $item->lab_order_id = $lab_order->id;
                        //     $item->variation_id = $row->variation_id;
                        //     $item->location_id = $row->location_id;
                        //     $item->warehouse_id = $row->warehouse_id;
                        //     $item->quantity = $row->quantity;

                        //     $item->save();
                        // }
                    }
                } else {
                    $deleted_items = DB::table('lab_order_details')
                        ->leftjoin('lab_orders as lo', 'lo.id', 'lab_order_details.lab_order_id')
                        ->where('lab_order_details.lab_order_id', $lab_order->id)
                        ->select('lab_order_details.*', 'lo.no_order')
                        ->get();
                   
                    foreach ($deleted_items as $item) {
                        $stock = VariationLocationDetails::where('variation_id', $item->variation_id)
                            ->where('location_id', $item->location_id)
                            ->where('warehouse_id', $item->warehouse_id)
                            ->first();

                        $stock->qty_available = $stock->qty_available + $item->quantity;
                        $stock->save();

                        // Delete kardex line
                        DB::table('kardexes')->where('reference', $item->no_order)
                            ->where('variation_id', $item->variation_id)
                            ->where('warehouse_id', $item->warehouse_id)
                            ->where('line_reference', $item->id)
                            ->delete();
                    }

                    DB::table('lab_order_details')->where('lab_order_id', $lab_order->id)->delete();
                }

                // Data to create or update output lines
                $lines = LabOrderDetail::where('lab_order_id', $lab_order->id)->get();

                // Store kardex
                $this->transactionUtil->createOrUpdateLabOrderLines(
                    $lab_order->transaction_id,
                    $lab_order->no_order,
                    $lines,
                    $lines_before
                );

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __("lab_order.updated_success"),
                    'lab_order_id' => $lab_order->id
                ];

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => $msg_error
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LabOrder  $labOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('lab_order.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $lab_order = LabOrder::findOrFail($id);

                // Clone record before action
                $lab_order_old = clone $lab_order;

                // Stock adjustment
                $lod = LabOrderDetail::where('lab_order_id', $id)->get();

                foreach ($lod as $item) {
                    $stock = VariationLocationDetails::where('variation_id', $item->variation_id)
                        ->where('location_id', $item->location_id)
                        ->where('warehouse_id', $item->warehouse_id)
                        ->first();
    
                    $stock->qty_available = $stock->qty_available + $item->quantity;

                    $stock->save();

                    DB::table('lab_order_details')->where('id', $item->id)->delete();
                }

                // Delete kardex lines
                $this->transactionUtil->deleteKardexByLabOrder($lab_order->id);

                $lab_order->delete();

                // Store binnacle
                $this->util->registerBinnacle(
                    $this->module_name,
                    'delete',
                    $lab_order_old->no_order,
                    $lab_order_old
                );

                $output = [
                    'success' => true,
                    'msg' => __("lab_order.deleted_success")
                ];

            } catch (\Exception $e) {
                \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Retrieves hoops list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getHoops()
    {
        if (request()->ajax()) {
            $term = request()->q;

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');

            // TODO: Gets color
            $query = Product::leftJoin('variations as v', 'products.id', 'v.product_id')
                ->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term .'%');
                    $query->orWhere('sku', 'like', '%' . $term .'%');
                    $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                })
                ->where('business_id', $business_id)
                ->whereNull('v.deleted_at')
                ->select('v.id', 'products.name as text', 'products.size')
                ->get();

            return json_encode($query);
        }
    }

    public function addMaterial($variation_id, $warehouse_id)
    {
        $products = DB::table('variations as variation')
        ->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
        ->leftJoin('variation_location_details as VLD', 'VLD.variation_id', '=', 'variation.id')
        ->select('variation.id as variation_id', 'product.name as name_product', 'variation.name as name_variation', 'product.sku as sku', 'variation.sub_sku as sub_sku', 'variation.sell_price_inc_tax as price', 'VLD.qty_available')
        ->where('variation.id', $variation_id)
        ->where('VLD.warehouse_id', $warehouse_id)
        ->first();

        return response()->json($products);
    }

    public function addProduct($variation_id, $warehouse_id)
    {
        $products = DB::table('variations as variation')
            ->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
            ->leftJoin('variation_location_details as VLD', 'VLD.variation_id', '=', 'variation.id')
            ->select(
                'variation.id as variation_id',
                'product.name as name_product',
                'variation.name as name_variation',
                'product.sku as sku',
                'variation.sub_sku as sub_sku',
                'variation.sell_price_inc_tax as price',
                //'VLD.qty_available'
                DB::raw('IF(VLD.qty_available > 0, round(VLD.qty_available, 2), 0.00) as qty_available')
            )
            ->where('variation.id', $variation_id)
            ->where('VLD.warehouse_id', $warehouse_id)
            ->first();

        return response()->json($products);
    }

    public function getProductsByOrder($id)
    {   
        $products = $this->getMaterialsByOrder($id);

        return response()->json($products);
    }

    public function getReport(Request $request, $id) {
        if (!auth()->user()->can('lab_order.view')) {
            abort(403, "Unauthorized action.");
        }

        $lab_order = DB::table('lab_orders as lo')
            ->leftJoin('graduation_cards as gc', 'gc.id', 'lo.graduation_card_id')
            ->leftJoin('variations as vh', 'vh.id', 'lo.hoop')
            ->leftJoin('variations as vg_os', 'vg_os.id', 'lo.glass_os')
            ->leftJoin('variations as vg_od', 'vg_od.id', 'lo.glass_od')
            ->leftJoin('variations as vg', 'vg.id', 'lo.glass')
            ->leftJoin('products as ph', 'ph.id', 'vh.product_id')
            ->leftJoin('products as pg_os', 'pg_os.id', 'vg_os.product_id')
            ->leftJoin('products as pg_od', 'pg_od.id', 'vg_od.product_id')
            ->leftJoin('products as pg', 'pg.id', 'vg.product_id')
            ->leftJoin('customers as c', 'c.id', 'lo.customer_id')
            ->leftJoin('patients as p', 'p.id', 'lo.patient_id')
            ->leftJoin('external_labs as el', 'el.id', 'lo.external_lab_id')
            ->leftJoin('transactions as t', 'lo.transaction_id', 't.id')
            ->leftJoin('business_locations as bl', 't.location_id', 'bl.id')
            ->select(
                'lo.*',
                'lo.id as loid',
                'gc.*',
                'ph.name as hoop_value',
                'pg_os.name as glass_os_value',
                'pg_od.name as glass_od_value',
                'pg.name as glass_value',
                DB::raw('DATE_FORMAT(delivery, "%d/%m/%Y %H:%i") as delivery_value'),
                'c.name as customer_value',
                'p.full_name as patient_value',
                'el.name as ext_lab_value',
                'ph.measurement',
                'bl.name as location',
                't.correlative',
                't.customer_name'
            )
            ->where('lo.id', $id)
            ->first();

        $materials = $this->getMaterialsByOrder($id);

        #$pdf = \PDF::loadView('optics.lab_order.report', compact('lab_order', 'materials'));
        #$pdf->setPaper('letter', 'portrait');
        #return $pdf->stream();
        return view('optics.lab_order.report')
            ->with(compact('lab_order', 'materials'));
    }

    public function getOrdersExternalLab()
    {
        if (!auth()->user()->can('external_lab.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            
            $lab_order = LabOrder::leftJoin('contacts as c', 'lab_orders.contact_id', 'c.id')
                ->leftJoin('graduation_cards as gc', 'lab_orders.graduation_card_id', 'gc.id')
                ->leftJoin('patients as p', 'gc.patient_id', 'p.id')
                ->leftJoin('status_lab_orders as slo', 'lab_orders.status_lab_order_id', 'slo.id')
                ->where('business_id', $business_id)
                ->where('check_ext_lab', 1)
                ->select([
                    'lab_orders.no_order', 'c.name as customer',
                    'p.full_name as patient', 'slo.name',
                    DB::raw('DATE_FORMAT(lab_orders.delivery, "%d/%m/%Y - %h:%i %p") as delivery'),
                    'lab_orders.id'
                ]);

            return Datatables::of($lab_order)
                ->addColumn(
                    'action', function($row) {
                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        if (auth()->user()->can('lab_order.view')) {
                            $html .= '<li><a href="#" onClick="printOrder(' . $row->id . ')"><i class="fa fa-print"></i> ' . __("messages.print") . '</a></li>';
                        }
                        if (auth()->user()->can('lab_order.update')) {
                            $html .= '<li><a href="#" onClick="editOrder(' . $row->id . ')"><i class="fa fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('lab_order.delete')) {
                            $html .= '<li><a href="#" onClick="deleteOrder(' . $row->id . ')"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        return $html;
                    }
                )
                ->removeColumn('id')
                ->rawColumns([5])
                ->make(false);
        }

        return view('optics.lab_order.orders_ext_lab');
    }

    public function fillHoopFields($variation_id, $transaction_id)
    {
        $hoop_values = TransactionSellLine::join("variations as v", "transaction_sell_lines.variation_id", "v.id")
            ->join("products as p", "v.product_id", "p.id")
            ->join("categories as c", "p.category_id", "c.id")
            ->where("transaction_sell_lines.transaction_id", $transaction_id)
            ->where("v.id", $variation_id)
            ->where("c.name", "AROS")
            ->select(
                "v.id as id",
                "p.name as name",
                "p.measurement as size",
                DB::raw("(SELECT name FROM `variation_value_templates` WHERE code = (SUBSTRING(p.sku, (COUNT(p.sku)) - 4, 3))) as color")
            )
            ->first();
        
        return response()->json($hoop_values);
    }

    public function fillHoopFields2($variation_id)
    {
        $hoop_values = DB::table("variations as v")
            ->join("products as p", "v.product_id", "p.id")
            ->join("categories as c", "p.category_id", "c.id")
            ->where("v.id", $variation_id)
            ->where("c.name", "AROS")
            ->select(
                "v.id as id",
                "p.name as name",
                "p.measurement as size",
                DB::raw("(SELECT name FROM `variation_value_templates` WHERE code = (SUBSTRING(p.sku, (COUNT(p.sku)) - 4, 3))) as color")
            )
            ->first();
        
        return response()->json($hoop_values);
    }

    /**
     * Get lab order for transaction
     * @param int $transaction_id
     */
    public function createLabOrder(){
        if (request()->ajax()) {
            $business_id = request()->session()->get("user.business_id");

            // Optometrists
            $employees = Employees::select('id', DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"));
            $employees = $employees->pluck('full_name', 'id');

            // AR
            $has_ar = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->where("p.clasification", "service")
                ->where("p.business_id", $business_id)
                ->whereIn("p.ar", ["green", "blue", "premium"])
                ->where("p.status", "active")
                ->select("p.ar", "p.name");
            
            // Date
            $date_delivery = Carbon::now()->addDay(3)->format('d/m/Y H:i');

            // Business locations
            if (! auth()->user()->can('lab_order.create_without_invoice')) {
                $business_locations = BusinessLocation::forDropdown($business_id, false, true);
                $bl_attributes = $business_locations['attributes'];
                $business_locations = $business_locations['locations'];

            } else {
                $business_locations = BusinessLocation::pluck('name', 'id');
            }

            $code = $this->util->generateLabOrderCode();

            $default_location = null;

            if (count($business_locations) == 1) {
                foreach ($business_locations as $id => $name) {
                    $default_location = $id;
                    $code = $this->util->generateLabOrderCode($default_location);
                }
            }

            // Aux
            $ar_aux = 2;
            $status_lab_orders = null;
            $external_labs = null;
            $products = null;
            $warehouses = null;
            $transaction = null;
            $own_hoop_aux = null;
            $hoop_values = null;
            $patient_id = null;
            $transaction_id = null;

            return view("optics.lab_order.create_lab_order")
                ->with(compact(
                    "transaction",
                    "status_lab_orders",
                    "external_labs",
                    "products",
                    "code",
                    "warehouses",
                    "business_locations",
                    "default_location",
                    "has_ar",
                    "date_delivery",
                    "transaction_id",
                    "own_hoop_aux",
                    "hoop_values",
                    "employees",
                    "ar_aux",
                    "patient_id"
                ));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLabOrdersByLocation(Request $request)
    {
        if (! auth()->user()->can('sell.view')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $permitted_locations = [];
            $all_locations = BusinessLocation::where('business_id', $business_id)->get();

            foreach ($all_locations as $location) {
                if (auth()->user()->can('location.' . $location->id)) {
                    $permitted_locations[] = $location->id;
                }
            }

            $lab_order = LabOrder::leftJoin('customers as c', 'lab_orders.customer_id', 'c.id')
                ->leftJoin('patients as p', 'lab_orders.patient_id', 'p.id')
                ->leftJoin('status_lab_orders as slo', 'lab_orders.status_lab_order_id', 'slo.id')
                ->leftJoin('transactions as t', 'lab_orders.transaction_id', 't.id')
                ->leftJoin('business_locations as bl', 't.location_id', 'bl.id')
                ->whereIn('bl.id', $permitted_locations)
                ->select([
                    'lab_orders.no_order',
                    't.correlative',
                    'bl.name as location',
                    'c.name as customer',
                    'p.full_name as patient',
                    'slo.name as status_value',
                    'lab_orders.created_at',
                    //DB::raw('DATE_FORMAT(lab_orders.delivery, "%d/%m/%Y - %h:%i %p") as delivery'),
                    'lab_orders.delivery',
                    'lab_orders.id',
                    'slo.color'
                ])
                ->orderBy('lab_orders.created_at', 'desc');
            
            return Datatables::of($lab_order)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs btn-actions" data-lab-order-id="{{ $id }}" data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        ' <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">

                        @if (auth()->user()->can(\'lab_order.view\'))
                        <li>
                            <a href="#" onClick="viewOrder({{ $id }})"><i class="fa fa-eye"></i> @lang("messages.view")</a>
                        </li>
                        @endif
                    
                        @if (auth()->user()->can(\'lab_order.print\'))
                        <li>
                            <a href="#" class="print-order" data-href="{{ action(\'Optics\LabOrderController@print\', [$id]) }}">
                                <i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")
                            </a>
                        </li>
                        @endif
                        
                        @if (! empty($steps))
                        <hr style="margin-top: 3px; margin-bottom: 3px;">

                        @foreach ($steps as $step)
                        @if (auth()->user()->can(\'status_lab_order.\' . $step->step_id))
                        <li>
                            @if ($step->step->print_order == 1)
                            <a href="#" class="print-order" data-href="{{ action(\'Optics\LabOrderController@changeStatusAndPrint\', [$id, $step->step_id]) }}">
                            @elseif ($step->step->transfer_sheet == 1)
                            <a href="#" class="transfer-order" data-href="{{ action(\'Optics\LabOrderController@changeStatusAndTransfer\', [$id, $step->step_id]) }}">
                            @elseif ($step->step->second_time == 1)
                            <a href="#" class="copy-order" data-href="{{ action(\'Optics\LabOrderController@changeStatusAndCopy\', [$id, $step->step_id]) }}">
                            @else
                            <a href="#" class="status-lab-order-change" data-order-id="{{ $id }}" data-status-id="{{ $step->step_id }}">
                            @endif
                                <i class="fa fa-dot-circle-o" style="color: {{ $step->step->color }}"></i> {{ $step->step->name }}
                            </a>
                        </li>
                        @endif
                        @endforeach

                        @endif
                    </ul>
                    </div>'
                )
                ->editColumn(
                    'status_value', function($row) {
                        $html = '';
                        if (!empty($row->status_value)) {
                            $html .= '<i class="fa fa-circle" style="color:' . $row->color . ';"></i> ' . $row->status_value;
                        }
                        return $html;
                    }
                )
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return action('Optics\LabOrderController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }
                ])
                ->removeColumn('id', 'color')
                ->rawColumns([5, 8])
                ->make(false);
        }

        return view('optics.lab_order.by_location');
    }

    public function markPrinted($id) {
        if (!auth()->user()->can('lab_order.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $slo = StatusLabOrder::where('name', 'Impreso')->first();
            if (!empty($slo)) {
                $lab_order = LabOrder::find($id);
                if (!empty($lab_order)) {
                    $lab_order->status_lab_order_id = $slo->id;
                    $lab_order->save();
                }
            }

            $output = [
                'success' => true
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false
            ];
        }

        return $output;
    }

    public function createOrderSecondTime($id) {
        if (!auth()->user()->can('lab_order.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $lab_order = LabOrder::find($id);
                $lab_order_clone = $lab_order->replicate();
                
                $slo = StatusLabOrder::where('second_time', 1)->first();
                $lab_order_clone->status_lab_order_id = $slo->id;

                $lab_order_clone->save();

                $output = [
                    'success' => true,
                    'id' => $lab_order_clone->id
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = [
                    'success' => false
                ];
            }

            return $output;
        }
    }

    /**
     * Print lab order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        if (! auth()->user()->can('lab_order.print')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            try {
                # Get data
                $lab_order = DB::table('lab_orders as lo')
                    ->leftJoin('graduation_cards as gc', 'gc.id', 'lo.graduation_card_id')
                    ->leftJoin('variations as vh', 'vh.id', 'lo.hoop')
                    ->leftJoin('variations as vg_os', 'vg_os.id', 'lo.glass_os')
                    ->leftJoin('variations as vg_od', 'vg_od.id', 'lo.glass_od')
                    ->leftJoin('variations as vg', 'vg.id', 'lo.glass')
                    ->leftJoin('products as ph', 'ph.id', 'vh.product_id')
                    ->leftJoin('products as pg_os', 'pg_os.id', 'vg_os.product_id')
                    ->leftJoin('products as pg_od', 'pg_od.id', 'vg_od.product_id')
                    ->leftJoin('products as pg', 'pg.id', 'vg.product_id')
                    ->leftJoin('customers as c', 'c.id', 'lo.customer_id')
                    ->leftJoin('patients as p', 'p.id', 'lo.patient_id')
                    ->leftJoin('external_labs as el', 'el.id', 'lo.external_lab_id')
                    ->leftJoin('transactions as t', 'lo.transaction_id', 't.id')
                    ->leftJoin('business_locations as bl', 't.location_id', 'bl.id')
                    ->leftJoin('business_locations as blo', 'lo.business_location_id', 'blo.id')
                    ->select(
                        'lo.*',
                        'lo.id as loid',
                        'gc.*',
                        'ph.name as hoop_value',
                        'pg_os.name as glass_os_value',
                        'pg_od.name as glass_od_value',
                        'pg.name as glass_value',
                        DB::raw('DATE_FORMAT(delivery, "%d/%m/%Y %H:%i") as delivery_value'),
                        'c.name as customer_value',
                        'p.full_name as patient_value',
                        'el.name as ext_lab_value',
                        'ph.measurement',
                        'bl.name as location',
                        't.correlative',
                        't.customer_name',
                        'blo.name as blo_name'
                    )
                    ->where('lo.id', $id)
                    ->first();
        
                $materials = $this->getMaterialsByOrder($id);
                
                $output = [
                    'success' => 1,
                    'order' => []
                ];

                $output['order']['html_content'] = view('optics.lab_order.report',
                    compact('lab_order', 'materials'))->render();

            } catch (\Exception $e) {
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
     * Change status and print lab order.
     *
     * @param  int  $id
     * @param  int  $status_id
     * @return \Illuminate\Http\Response
     */
    public function changeStatusAndPrint($id, $status_id) {
        if (! auth()->user()->can('status_lab_order.' . $status_id)) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            try {
                // Change status
                $lo = LabOrder::find($id);

                // Clone record before action
                $lo_old = clone $lo;

                $lo->status_lab_order_id = $status_id;
                $lo->save();

                // Store binnacle
                $this->util->registerBinnacle(
                    $this->module_name,
                    'update',
                    $lo->no_order,
                    $lo_old,
                    $lo
                );
    
                // Get data to print
                $lab_order = DB::table('lab_orders as lo')
                    ->leftJoin('graduation_cards as gc', 'gc.id', 'lo.graduation_card_id')
                    ->leftJoin('variations as vh', 'vh.id', 'lo.hoop')
                    ->leftJoin('variations as vg_os', 'vg_os.id', 'lo.glass_os')
                    ->leftJoin('variations as vg_od', 'vg_od.id', 'lo.glass_od')
                    ->leftJoin('variations as vg', 'vg.id', 'lo.glass')
                    ->leftJoin('products as ph', 'ph.id', 'vh.product_id')
                    ->leftJoin('products as pg_os', 'pg_os.id', 'vg_os.product_id')
                    ->leftJoin('products as pg_od', 'pg_od.id', 'vg_od.product_id')
                    ->leftJoin('products as pg', 'pg.id', 'vg.product_id')
                    ->leftJoin('customers as c', 'c.id', 'lo.customer_id')
                    ->leftJoin('patients as p', 'p.id', 'lo.patient_id')
                    ->leftJoin('external_labs as el', 'el.id', 'lo.external_lab_id')
                    ->leftJoin('transactions as t', 'lo.transaction_id', 't.id')
                    ->leftJoin('business_locations as bl', 't.location_id', 'bl.id')
                    ->leftJoin('business_locations as blo', 'lo.business_location_id', 'blo.id')
                    ->select(
                        'lo.*',
                        'lo.id as loid',
                        'gc.*',
                        'ph.name as hoop_value',
                        'pg_os.name as glass_os_value',
                        'pg_od.name as glass_od_value',
                        'pg.name as glass_value',
                        DB::raw('DATE_FORMAT(delivery, "%d/%m/%Y %H:%i") as delivery_value'),
                        'c.name as customer_value',
                        'p.full_name as patient_value',
                        'el.name as ext_lab_value',
                        'ph.measurement',
                        'bl.name as location',
                        't.correlative',
                        't.customer_name',
                        'blo.name as blo_name'
                    )
                    ->where('lo.id', $id)
                    ->first();
        
                $materials = $this->getMaterialsByOrder($id);
                
                $output = [
                    'success' => 1,
                    'order' => []
                ];

                $output['order']['html_content'] = view('optics.lab_order.report',
                    compact('lab_order', 'materials'))
                    ->render();

            } catch (\Exception $e) {
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
     * Change lab order status.
     * 
     * @param  int  $order_id
     * @param  int  $status_id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($order_id, $status_id)
    {
        if (! auth()->user()->can('status_lab_order.' . $status_id)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $lab_order = LabOrder::find($order_id);

            // Change status
            if (! empty($lab_order)) {
                // Clone record before action
                $lab_order_old = clone $lab_order;

                $lab_order->status_lab_order_id = $status_id;
                $lab_order->save();

                // Store binnacle
                $this->util->registerBinnacle(
                    $this->module_name,
                    'update',
                    $lab_order->no_order,
                    $lab_order_old,
                    $lab_order
                );
            }

            $output = [
                'success' => true,
                'msg' => __('lab_order.updated_success')
            ];

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false
            ];
        }

        return $output;
    }

    public function getMaterialsByOrder($id)
    {
        $lod = LabOrderDetail::where('lab_order_id', $id)->get();

        $vld = [];
        foreach ($lod as $l) {
            $v = VariationLocationDetails::where('variation_id', $l->variation_id)
                ->where('warehouse_id', $l->warehouse_id)
                ->where('location_id', $l->location_id)
                ->first();
            
            array_push($vld, $v->id);
        }

        $materials = DB::table('lab_order_details as lod')
            ->leftJoin('variations as v', 'v.id', 'lod.variation_id')
            ->leftJoin('products as p', 'p.id', 'v.product_id')
            // ->leftJoin('variation_location_details as vld', 'vld.variation_id', 'v.id')
            ->leftJoin(
                'variation_location_details as vld',
                function($join) use ($vld) {
                    $join->on('vld.variation_id', '=', 'lod.variation_id');

                    $join->where(function($query) use ($vld) {
                        $query->whereIn('vld.id', $vld);
                    });
                }
            )
            ->where('lod.lab_order_id', $id)
            ->orderBy('lod.id', 'asc')
            ->select(
                'lod.*',
                'p.name as product_name',
                'v.name as variation_name',
                'p.sku as sku',
                'v.sub_sku as sub_sku',
                DB::raw('IF(vld.qty_available > 0, round(vld.qty_available, 2), 0.00) as qty_available')
            )
            ->get();
        
        return $materials;
    }

    /**
     * Get data for lab orders report.
     * 
     * @param  array  $params
     * @return array
     */
    public function getLabOrders($params)
    {
        // Location filter
        if (! empty($params['location_id']) && $params['location_id'] != 'all') {
            $location_id = $params['location_id'];
        } else {
            $location_id = 0;
        }

        // Status lab order filter
        if (! empty($params['status_id']) && $params['status_id'] != 'all') {
            $status_id = $params['status_id'];
        } else {
            $status_id = 0;
        }

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start = $params['start_date'];
            $end =  $params['end_date'];
        } else {
            $start = '';
            $end =  '';
        }

        // Datatable parameters
        $start_record = $params['start_record'];
        $page_size = $params['page_size'];
        $search_array = $params['search'];
        $search = ! is_null($search_array['value']) ? $search_array['value'] : '';
        $order = $params['order'];

        // Count lab orders
        $count = DB::select(
            'CALL count_all_lab_orders(?, ?, ?, ?, ?)',
            array(
                $location_id,
                $status_id,
                $start,
                $end,
                $search
            )
        );

        // Lab orders
        $lab_orders = DB::select(
            'CALL all_lab_orders(?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $location_id,
                $status_id,
                $start,
                $end,
                $search,
                $start_record,
                $page_size,
                $order[0]['column'],
                $order[0]['dir']
            )
        );

        $result = [
            'data' => $lab_orders,
            'count' => $count[0]->count
        ];

        return $result;
    }

    /**
     * Get sales togle dropdown.
     * 
     * @param  array  $params
     * @return @return \Illuminate\Http\Response
     */
    public function getToggleDropdown($id)
    {
        $output = [];

        try {
            $lab_order = LabOrder::where('id', $id)->first();

            $steps = StatusLabOrderStep::where('status_id', $lab_order->status_lab_order_id)->get();

            $graduation_card = GraduationCard::find($lab_order->graduation_card_id);

            $document = null;

            if (! empty($graduation_card)) {
                $document = $graduation_card->document;
            }

            $is_annulled = $lab_order->is_annulled;

            return view('optics.lab_order.partials.toggle_dropdown')
                ->with(compact(
                    'id',
                    'steps',
                    'document',
                    'is_annulled'
                ))
                ->render();
            
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = __('messages.something_went_wrong');
        }

        return $output;
    }

    /**
     * Change lab order status and print lab order.
     *
     * @param  int  $id
     * @param  int  $status_id
     * @return \Illuminate\Http\Response
     */
    public function changeStatusAndTransfer($id, $status_id) {
        if (! auth()->user()->can('status_lab_order.' . $status_id)) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            try {
                $lab_order = LabOrder::find($id);

                // Clone record before action
                $lab_order_old = clone $lab_order;

                // Change status
                $lab_order->status_lab_order_id = $status_id;
                
                // Transfer lab order
                $lab_order->transfer_date = \Carbon::now()->format('Y-m-d');

                $lab_order->save();

                // Store binnacle
                $this->util->registerBinnacle(
                    $this->module_name,
                    'update',
                    $lab_order->no_order,
                    $lab_order_old,
                    $lab_order
                );
                
                $output = [
                    'success' => true,
                    'msg' => __('lab_order.updated_success')
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
     * Change lab order status and copy lab order.
     *
     * @param  int  $id
     * @param  int  $status_id
     * @return \Illuminate\Http\Response
     */
    public function changeStatusAndCopy($id, $status_id) {
        if (! auth()->user()->can('status_lab_order.' . $status_id)) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            try {
                $lab_order = LabOrder::find($id);

                // Clone lab order
                $lab_order_clone = $lab_order->replicate();

                // Change status
                $lab_order_clone->status_lab_order_id = $status_id;

                // Change data
                $lab_order_clone->number_times = $lab_order->number_times + 1;
                $lab_order_clone->employee_id = null;
                $lab_order_clone->reason = null;

                $lab_order_clone->save();

                // Store binnacle
                $this->util->registerBinnacle(
                    $this->module_name,
                    'create',
                    $lab_order_clone->no_order,
                    $lab_order_clone
                );
                
                $output = [
                    'success' => true,
                    'id' => $lab_order_clone->id
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
     * Change lab order status and edit lab order.
     *
     * @param  int  $id
     * @param  int  $status_id
     * @return \Illuminate\Http\Response
     */
    public function changeStatusAndEdit($id, $status_id) {
        if (! auth()->user()->can('status_lab_order.' . $status_id)) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            try {
                $lab_order = LabOrder::find($id);

                // Clone record before action
                $lab_order_old = clone $lab_order;

                // Change status
                $lab_order->status_lab_order_id = $status_id;

                $lab_order->save();

                // Store binnacle
                $this->util->registerBinnacle(
                    $this->module_name,
                    'update',
                    $lab_order->no_order,
                    $lab_order_old,
                    $lab_order
                );
                
                $output = [
                    'success' => true,
                    'id' => $id
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

    public function validateStock($warehouse_id, $variation_id, $quantity, $previous_quantity)
    {
        $pl = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
            ->where('transactions.warehouse_id', $warehouse_id)
            ->where('purchase_lines.variation_id', $variation_id);

        $pl_quantity = $pl->sum('purchase_lines.quantity');
        $pl_quantity_returned = $pl->sum('purchase_lines.quantity_returned');

        $tsl = TransactionSellLine::join('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->where('transactions.warehouse_id', $warehouse_id)
            ->where('transaction_sell_lines.variation_id', $variation_id)
            ->where('transactions.status', '!=', 'annulled');

        $tsl_quantity = $tsl->sum('transaction_sell_lines.quantity');
        $tsl_quanity_returned = $tsl->sum('transaction_sell_lines.quantity_returned');

        $sal = StockAdjustmentLine::join('transactions', 'transactions.id', 'stock_adjustment_lines.transaction_id')
            ->where('transactions.warehouse_id', $warehouse_id)
            ->where('stock_adjustment_lines.variation_id', $variation_id)
            ->sum('stock_adjustment_lines.quantity');

        $lod = LabOrderDetail::where('warehouse_id', $warehouse_id)
            ->where('variation_id', $variation_id)
            ->sum('quantity');

        $stock = $pl_quantity + $tsl_quanity_returned + $previous_quantity - $pl_quantity_returned - $tsl_quantity - $sal - $lod;

        $result = false;

        if ($stock >= $quantity) {
            $result = true;
        }

        return $result;
    }

    /**
     * Change status to multiple lab order.
     * 
     * @param  int  $order_id
     * @param  int  $status_id
     * @return \Illuminate\Http\Response
     */
    public function multipleChangeStatus()
    {
        $lab_orders = request()->input('lab_orders');
        $status_id = request()->input('status_id');

        if (! auth()->user()->can('status_lab_order.' . $status_id)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $status = StatusLabOrder::find($status_id);

            foreach ($lab_orders as $lab_order_id) {
                $lab_order = LabOrder::find($lab_order_id);
    
                // Change status
                if (! empty($lab_order)) {
                    // Clone record before action
                    $lab_order_old = clone $lab_order;
    
                    $lab_order->status_lab_order_id = $status_id;

                    // Transfer lab order
                    if ($status->transfer_sheet == 1) {
                        $lab_order->transfer_date = \Carbon::now()->format('Y-m-d');
                    }

                    $lab_order->save();
    
                    // Store binnacle
                    $this->util->registerBinnacle(
                        $this->module_name,
                        'update',
                        $lab_order->no_order,
                        $lab_order_old,
                        $lab_order
                    );
                }
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('lab_order.multiple_updated_success')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => 0
            ];
        }

        return $output;
    }
}
