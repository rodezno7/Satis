<?php

namespace App\Http\Controllers\Optics;

use App\Contact;
use App\DocumentType;
use App\Employees;
use App\ExpenseCategory;
use App\Transaction;
use App\TransactionPayment;
use App\Optics\FlowReason;
use App\Optics\InflowOutflow;
use App\Utils\CashRegisterUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class InflowOutflowController extends Controller
{
    /**
     * Constructor.
     *
     * @param  TransactionUtil  $transactionUtil
     * @param  ProductUtil  $productUtil
     * @param  CashRegisterUtil  $cashRegisterUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, CashRegisterUtil $cashRegisterUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('inflow_outflow.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            
            $inflow_outflow = InflowOutflow::leftJoin('employees', 'employees.id', 'inflow_outflows.employee_id')
                ->leftJoin('flow_reasons', 'flow_reasons.id', 'inflow_outflows.flow_reason_id')
                ->leftJoin('cashiers', 'cashiers.id', 'inflow_outflows.cashier_id')
                ->where('inflow_outflows.business_id', $business_id)
                ->select([
                    'inflow_outflows.type',
                    'flow_reasons.reason',
                    'cashiers.name as cashier',
                    'inflow_outflows.amount',
                    DB::raw("CONCAT(COALESCE(employees.first_name, ''), ' ', COALESCE(employees.last_name, '')) as employee"),
                    'inflow_outflows.id'
                ]);

            return Datatables::of($inflow_outflow)
                ->filterColumn('employee', function($query, $keyword) {
                    $query->whereRaw('CONCAT(employees.first_name, " ", employees.last_name) LIKE ?', ["%{$keyword}%"]);
                })
                ->addColumn(
                    'action',
                    '@can("inflow_outflow.update")
                    <button data-href="{{ action(\'Optics\InflowOutflowController@edit\', [$id]) }}"
                        class="btn btn-xs btn-primary edit_inflow_outflows_button">
                        <i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")
                    </button>
                    &nbsp;
                    @endcan
                    @can("inflow_outflow.delete")
                    <button data-href="{{ action(\'Optics\InflowOutflowController@destroy\', [$id]) }}"
                        class="btn btn-xs btn-danger delete_inflow_outflows_button">
                        <i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")
                    </button>
                    @endcan'
                )
                ->editColumn(
                    'type',
                    '@if($type == "input")
                    <span class="badge" style="background-color: #5cb85c;">{{ __("inflow_outflow." . $type) }}</span>
                    @else
                    <span class="badge" style="background-color: #d9534f;">{{ __("inflow_outflow." . $type) }}</span>
                    @endif'
                )
                ->removeColumn('id')
                ->rawColumns(['type', 'action'])
                ->toJson();
        }

        return view('optics.inflow_outflow.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        if (! auth()->user()->can('inflow_outflow.create')) {
            abort(403, 'Unauthorized action.');
        }

        $cashier_id = request()->input('cashier_id');

        $business_id = request()->session()->get('user.business_id');

        $location_id = request()->input('location_id');

        $document_types = DocumentType::forDropdown($business_id, false, false);

        $flow_reasons = FlowReason::forDropdown($business_id);

        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');

        return view('optics.inflow_outflow.create')
            ->with(compact('type', 'document_types', 'cashier_id', 'flow_reasons', 'expense_categories', 'location_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('inflow_outflow.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            # Store inflow/outflow
            $inflow_outflow_data = $request->only([
                'amount',
                'type',
                'supplier_id',
                'document_type_id',
                'document_no',
                'cashier_id',
                'flow_reason_id',
                'employee_id',
                'expense_category_id',
                'description',
                'location_id'
            ]);

            $business_id = $request->session()->get('user.business_id');
            $amount = $this->transactionUtil->num_uf($request->input('amount'));
            $user_id = $request->session()->get('user.id');

            $inflow_outflow_data['amount'] = $amount;
            $inflow_outflow_data['business_id'] = $business_id;
            $inflow_outflow_data['created_by'] = $user_id;
            $inflow_outflow_data['updated_by'] = $user_id;
            
            # Inflow
            if ($inflow_outflow_data['type'] == 'input') {
                $inflow_outflow = InflowOutflow::create($inflow_outflow_data);

                $msg = __('inflow_outflow.input_added_success');

                $success = true;

            # Outflow
            } else {
                # Total cash inflow
                $date_format = session('business.date_format');
                $close_date = \Carbon::now();
                $close_date = $close_date->format($date_format);
                $cashier_id = request()->input('cashier_id');

                $trans_date = substr($close_date, 0, 10);
                $start = $close_date . ' 00:01';
                $end = $close_date . ' 23:59';

                $opening_date = $this->productUtil->uf_date($start, true);
                $closing_date = $this->productUtil->uf_date($end, true);

                $c_date = $this->productUtil->uf_date($trans_date, false);

                # Sales
                $register_details =  $this->cashRegisterUtil->getRegisterDetails($cashier_id, $opening_date, $closing_date, $c_date);

                # Payments and entries
                $payment_details = $this->cashRegisterUtil->getRegisterDetailsWithPayments($cashier_id, $opening_date, $closing_date);

                # Cash in hand
                $initial = $this->cashRegisterUtil->getInitialAmount($cashier_id, $opening_date, $closing_date);

                # Inflows and outflows
                $inflow_outflow_reg = $this->cashRegisterUtil->getInflowOutflow($cashier_id, $opening_date, $closing_date);

                # Reservations
                $reservations = $this->cashRegisterUtil->getReservationPayments($cashier_id, $opening_date, $closing_date, $c_date);

                # Reservation payments
                $reservation_pays = $this->cashRegisterUtil->getReservationPays($cashier_id, $opening_date, $closing_date, $c_date);

                $total_cash_inflow =
                    $initial +
                    $register_details->total_cash +
                    $payment_details->total_cash +
                    $reservations->total_cash +
                    $reservation_pays->total_cash +
                    $inflow_outflow_reg->inflow -
                    $register_details->total_cash_refund -
                    $payment_details->total_cash_refund -
                    $reservations->total_cash_refund -
                    $inflow_outflow_reg->outflow;

                if ($total_cash_inflow > $amount) {
                    $inflow_outflow = InflowOutflow::create($inflow_outflow_data);

                    $msg = __('inflow_outflow.output_added_success');

                    # Store expense
                    $expense_for = Employees::where('id', $inflow_outflow_data['employee_id'])->first()->user_id;

                    $expense_data = [
                        'ref_no' => $inflow_outflow_data['document_no'],
                        'transaction_date' => $inflow_outflow->created_at,
                        'location_id' => $request->input('location_id'),
                        'final_total' => $this->transactionUtil->num_uf($inflow_outflow_data['amount']),
                        'expense_for' => $expense_for,
                        'additional_notes' => $inflow_outflow_data['description'],
                        'expense_category_id' => $inflow_outflow_data['expense_category_id'],
                        'business_id' => $business_id,
                        'created_by' => $user_id,
                        'type' => 'expense',
                        'status' => 'final',
                        'payment_status' => 'paid'
                    ];

                    # Update reference count
                    $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');
                    
                    # Generate reference number
                    if (empty($expense_data['ref_no'])) {
                        $expense_data['ref_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);
                    }

                    $transaction = Transaction::create($expense_data);

                    # Store payment
                    $payment_data = [
                        'transaction_id' => $transaction->id,
                        'business_id' => $business_id,
                        'amount' => $amount,
                        'method' => 'cash',
                        'paid_on' => \Carbon::now()->toDateTimeString(),
                        'created_by' => $user_id
                    ];

                    # Update reference count
                    $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense_payment');

                    # Generate reference number
                    $payment_data['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber('expense_payment', $ref_count);

                    TransactionPayment::create($payment_data);

                    $success = true;

                } else {
                    $msg = __('inflow_outflow.outflow_validation');

                    $success = false;
                }

            }

            DB::commit();

            $output = [
                'success' => $success,
                'msg' => $msg
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
     * @param  \App\InflowOutflow  $inflowOutflow
     * @return \Illuminate\Http\Response
     */
    public function show(InflowOutflow $inflowOutflow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('inflow_outflow.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $inflow_outflow = InflowOutflow::findOrFail($id);

            $business_id = request()->session()->get('user.business_id');

            $document_types = DocumentType::forDropdown($business_id, false, false);

            $flow_reasons = FlowReason::forDropdown($business_id);

            return view('optics.inflow_outflow.edit')
                ->with(compact('inflow_outflow', 'document_types', 'flow_reasons'));
        }
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
        if (!auth()->user()->can('inflow_outflow.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only([
                    'amount',
                    'supplier_id',
                    'document_type_id',
                    'document_no',
                    'flow_reason_id',
                    'employee_id'
                ]);

                $input['amount'] = $this->transactionUtil->num_uf($input['amount']);

                $input['updated_by'] = $request->session()->get('user.id');

                $inflow_outflow = InflowOutflow::findOrFail($id);
                $inflow_outflow->fill($input);
                $inflow_outflow->save();

                $msg = $inflow_outflow->type == 'input' ? __('inflow_outflow.input_updated_success') : __('inflow_outflow.output_updated_success');

                $output = [
                    'success' => true,
                    'msg' => $msg
                ];

            } catch (\Exception $e) {
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('inflow_outflow.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $inflow_outflow = InflowOutflow::findOrFail($id);

                $type = $inflow_outflow->type;

                $msg = $type == 'input' ? __('inflow_outflow.input_deleted_success') : __('inflow_outflow.output_deleted_success');

                $inflow_outflow->delete();

                $output = [
                    'success' => true,
                    'msg' => $msg
                ];

            } catch (\Exception $e) {
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
     * Retrieves supliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');

            $query = Contact::where('business_id', $business_id);
            
            $suppliers = $query->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term .'%')
                    ->orWhere('supplier_business_name', 'like', '%' . $term .'%')
                    ->orWhere('contacts.contact_id', 'like', '%' . $term .'%');
                })
                ->select('contacts.id', 'name as text', 'supplier_business_name as business_name', 'contacts.contact_id')
                ->onlySuppliers()
                ->get();

            return json_encode($suppliers);
        }
    }

     /**
     * Retrieves employees list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmployees()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');

            $query = Employees::where('business_id', $business_id);
            
            $employees = $query->where(function ($query) use ($term) {
                $query->where('first_name', 'like', '%' . $term .'%')
                    ->orWhere('last_name', 'like', '%' . $term .'%');
                })
                ->select(
                    'id',
                    DB::raw("CONCAT(COALESCE(first_name, '' ), ' ', COALESCE(last_name, '')) as text")
                )
                ->get();

            return json_encode($employees);
        }
    }
}
