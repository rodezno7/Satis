<?php

namespace App\Http\Controllers;

use App\CashDetail;
use DB;
use App\Cashier;
use App\Transaction;
use App\CashRegister;
use App\CashierClosure;
use App\CashRegisterTransaction;
use App\Utils\CashierUtil;
use App\Utils\ProductUtil;
use App\DocumentCorrelative;
use Illuminate\Http\Request;
use App\Utils\CashRegisterUtil;

class CashRegisterController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $cashRegisterUtil;
    protected $productUtil;
    protected $cashierUtil;

    /**
     * Constructor
     *
     * @param CashRegisterUtil $cashRegisterUtil
     * @return void
     */
    public function __construct(
        CashRegisterUtil $cashRegisterUtil,
        ProductUtil $productUtil,
        CashierUtil $cashierUtil
    ) {
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->productUtil = $productUtil;
        $this->cashierUtil = $cashierUtil;
        $this->payment_types = ['cash' => 'Cash', 'card' => 'Card', 'check' => 'Check', 'bank_transfer' => 'Bank Transfer'];

        // Binnacle data
        $this->module_name = 'cash_register';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('cash_register.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        // Check if there is a open register, if yes then redirect to POS screen.
        if (config('app.business') == 'optics') {
            if ($this->cashRegisterUtil->countOpenedRegister() != 0) {
                return redirect()->action('SellPosController@create');
            }

        } else {
            if ($this->cashierUtil->countOpenedCashier() != 0) {
                return redirect()->action('SellPosController@create');
            }
        }

        $cashiers = Cashier::forDropdown($business_id, false);

        return view('cash_register.create')
            ->with(compact('cashiers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (config('app.business') == 'optics') {
            return $this->storeCashRegister($request);

        } else {
            return $this->storeCashier($request);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCashier(Request $request)
    {
        $cashier_closure_id = null;
        $opening_receipt = true;

        if ($this->cashierUtil->countOpenedCashier() != 0) {
            return redirect()->action('SellPosController@create');
        }

        try {
            $user_id = $request->session()->get('user.id');
            $business_id = $request->session()->get('user.business_id');
            $initial_cash_amount = $request->input('amount') ? $request->input('amount') : 0;
            $cashier_id = $request->input('cashier');

            DB::beginTransaction();

            /** Update cashier */
            $cashier = Cashier::find($cashier_id);
            $cashier->status = 'open';
            $cashier->last_open_by = $user_id;
            $cashier->last_open = \Carbon::now()->toDateTimeString();
            $cashier->save();

            /** Create a new cashier closure record */
            $cashier_closure = new CashierClosure();
            $cashier_closure->cashier_id = $cashier_id;
            $cashier_closure->initial_cash_amount = $initial_cash_amount;
            $cashier_closure->opened_by = $user_id;
            $cashier_closure->open_date = \Carbon::now()->toDateTimeString();

            /** get correlative */
            $document =
                DocumentCorrelative::join("document_types as dt", "dt.id", "document_correlatives.document_type_id")
                    ->where('dt.business_id', $cashier->business_id)
                    ->where("dt.is_active", 1)
                    ->where('document_correlatives.location_id', $cashier->business_location_id)
                    ->where('dt.short_name', 'Ticket')
                    ->where('document_correlatives.status', 'active')
                    ->select('document_correlatives.*')
                    ->first();
            /** Asign and increment correlative */
            if(!empty($document)){
                if($document->actual < $document->final){
                    $cashier_closure->open_correlative = $document->actual;

                    $document->actual += 1;
                    $document->save();

                } else if($document->actual == $document->final){
                    $cashier_closure->open_correlative = $document->actual;

                    $document->status = 'inactive';
                    $document->save();
                }
            } else {
                $opening_receipt = false;
            }

            $cashier_closure->save();

            $cashier_closure_id = $cashier_closure->id;

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }

        if($opening_receipt){
            return redirect()->action('SellPosController@create', ['cashier_closure_id' => $cashier_closure_id]);
        } else {
            return redirect()->action('SellPosController@create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCashRegister(Request $request)
    {
        try {
            DB::beginTransaction();

            $initial_amount = 0;

            if (! empty($request->input('amount'))) {
                $initial_amount = $this->cashRegisterUtil->num_uf($request->input('amount'));
            }

            $user_id = $request->session()->get('user.id');
            $business_id = $request->session()->get('user.business_id');
            $cashier_id = $request->input('cashier');

            $data = [
                'business_id' => $business_id,
                'cashier_id' => $cashier_id,
                'status' => 'open'
            ];

            $today = \Carbon::now()->toDateString();

            $register = CashRegister::where('business_id', $business_id)
                ->where('cashier_id', $cashier_id)
                ->where('date', $today)
                ->where('status', 'close')
                ->first();

            // Open cash register again
            if (! empty($register)) {
                $data['date'] = null;

                // Clone record before action
                $register_old = clone $register;

                $register->fill($data);

                $register->save();

                // Store binnacle
                $this->cashRegisterUtil->registerBinnacle(
                    $this->module_name,
                    'reopen',
                    null,
                    $register_old,
                    $register
                );

                $crt = CashRegisterTransaction::where('cash_register_id', $register->id)
                    ->where('transaction_type', 'initial')
                    ->first();

                $crt->amount = $initial_amount;
                $crt->save();

            // Open cash register for the first time
            } else {
                $data['user_id'] = $user_id;

                $register = CashRegister::create($data);

                // Store binnacle
                $this->cashRegisterUtil->registerBinnacle(
                    $this->module_name,
                    'open',
                    null,
                    $register
                );

                $crt = $register->cash_register_transactions()->create([
                    'amount' => $initial_amount,
                    'pay_method' => 'cash',
                    'type' => 'credit',
                    'transaction_type' => 'initial'
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();

            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());
        }

        return redirect()->action('SellPosController@create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $cash_register = CashRegister::where('id', $id)
            ->first();
        
        $cashier_id = $cash_register->cashier_id;
        $close_date = $cash_register->date;
        $closing_note = $cash_register->closing_note;
        $user_id = $cash_register->user_id;

        $start = $cash_register->date . " 00:01:00";
        $end = $cash_register->date . " 23:59:00";

        $opening_date = date_format(date_create($start), 'Y-m-d H:i:s');
        $closing_date = date_format(date_create($end), 'Y-m-d H:i:s');

        // Sales
        $register_details =  $this->cashRegisterUtil->getRegisterDetails($cashier_id, $opening_date, $closing_date, $close_date);

        // Payments and entries
        $payment_details = $this->cashRegisterUtil->getRegisterDetailsWithPayments($cashier_id, $opening_date, $closing_date);

        // Cash in hand
        $initial = $this->cashRegisterUtil->getInitialAmount($cashier_id, $opening_date, $closing_date);

        // Inflows and outflows
        $inflow_outflow = $this->cashRegisterUtil->getInflowOutflow($cashier_id, $opening_date, $closing_date);

        return view('cash_register.register_details')
            ->with(compact(
                'register_details',
                'close_date',
                'closing_note',
                'payment_details',
                'initial',
                'inflow_outflow'
            ));
    }

    /**
     * Shows register details modal.
     *
     * @param  void
     * @return \Illuminate\Http\Response
     */
    public function getRegisterDetails()
    {

        $register_details =  $this->cashRegisterUtil->getRegisterDetails(null, null, null, null);

        $user_id = auth()->user()->id;
        $open_time = $register_details['open_time'];
        $close_time = \Carbon::now()->toDateTimeString();
        $details = $this->cashRegisterUtil->getRegisterTransactionDetails($user_id, $open_time, $close_time);

        return view('cash_register.register_details')
            ->with(compact('register_details', 'details'));
    }

    /**
     * Shows close register form.
     *
     * @param  void
     * @return \Illuminate\Http\Response
     */
    public function getCloseRegister()
    {
        $close_date = request()->input('close_date');
        $cashier_id = request()->input('cashier_id');
        $business_id = request()->session()->get('user.business_id');
        $location_id = request()->input('location_id');

        $trans_date = substr($close_date, 0, 10); // Get date only
        $close_date = $this->productUtil->uf_date($trans_date, false);
        $start = $trans_date . " 00:01";
        $end = $trans_date . " 23:59";
        
        $opening_date = $this->productUtil->uf_date($start, true);
        $closing_date = $this->productUtil->uf_date($end, true);

        // Sales
        $register_details =  $this->cashRegisterUtil->getRegisterDetails($cashier_id, $opening_date, $closing_date, $close_date);

        // Payments and entries
        $payment_details = $this->cashRegisterUtil->getRegisterDetailsWithPayments($cashier_id, $opening_date, $closing_date);

        // Cash in hand
        $initial = $this->cashRegisterUtil->getInitialAmount($cashier_id, $opening_date, $closing_date);

        // Inflows and outflows
        $inflow_outflow = $this->cashRegisterUtil->getInflowOutflow($cashier_id, $opening_date, $closing_date);

        // Reservations
        $reservations = $this->cashRegisterUtil->getReservationPayments($cashier_id, $opening_date, $closing_date, $close_date);

        // Reservation payments
        $reservation_pays = $this->cashRegisterUtil->getReservationPays($cashier_id, $opening_date, $closing_date, $close_date);

        // Reservations to sales
        $reservation_to_sale = $this->cashRegisterUtil->getReservationsToSales($cashier_id, $opening_date, $closing_date, $close_date);

        $sum_sales = Transaction::where('cashier_id', $cashier_id)
            ->whereBetween('transaction_date', [$opening_date, $closing_date])
            ->sum('final_total');

        $is_closed = CashRegister::where('cashier_id', $cashier_id)
            ->where('business_id', $business_id)
            ->where('date', $close_date)
            ->where('status', 'close')
            ->count();

        $today = \Carbon::now()->format('Y-m-d');

        if ($close_date == $today || $is_closed <= 0) {
            $total_credit = $register_details->total_credit;
            $total_sell = $register_details->total_sale;
        } else {
            $total_credit = $register_details->total_credit + $register_details->total_sell - $register_details->total_sale - $register_details->total_sale_refund;
            $total_sell = $register_details->total_sell - $register_details->total_sale_refund;
        }

        return view('cash_register.close_register_modal')
            ->with(compact(
                'register_details',
                'close_date',
                'cashier_id',
                'is_closed',
                'payment_details',
                'initial',
                'location_id',
                'inflow_outflow',
                'reservations',
                'reservation_pays',
                'total_credit',
                'total_sell',
                'reservation_to_sale',
                'sum_sales'
            ));
    }

    /**
     * Closes currently opened register.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postCloseRegister(Request $request)
    {
        try {
            // Disable in demo
            if (config('app.env') == 'demo') {
                $output = [
                    'success' => 0,
                    'msg' => 'Feature disabled in demo!!'
                ];

                return redirect()->action('HomeController@index')->with('status', $output);
            }
            
            $input = $request->only([
                'total_amount_cash',
                'total_amount_card',
                'total_amount_check',
                'total_amount_transfer',
                'total_amount_credit',
                'closing_note'
            ]);

            $input['total_amount_cash'] = $this->cashRegisterUtil->num_uf($input['total_amount_cash']);
            $input['total_amount_card'] = $this->cashRegisterUtil->num_uf($input['total_amount_card']);
            $input['total_amount_check'] = $this->cashRegisterUtil->num_uf($input['total_amount_check']);
            $input['total_amount_transfer'] = $this->cashRegisterUtil->num_uf($input['total_amount_transfer']);
            $input['total_amount_credit'] = $this->cashRegisterUtil->num_uf($input['total_amount_credit']);

            $trans_date = substr($request->input('close_date'), 0, 10);
            $input['date'] = $this->productUtil->uf_date($trans_date, false);
            $input['status'] = 'close';
            
            $cashier_id = $request->input('cashier_id');
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');
            $location_id = $request->input('location_id');

            $cash_register = CashRegister::where('cashier_id', $cashier_id)
                ->where('business_id', $business_id)
                ->where('status', 'open')
                ->first();
            
            // Create or update cash register
            if (! empty($cash_register)) {
                // Clone record before action
                $cash_register_old = clone $cash_register;

                $cash_register->update($input);

                // Store binnacle
                $this->cashRegisterUtil->registerBinnacle(
                    $this->module_name,
                    'close',
                    null,
                    $cash_register_old,
                    $cash_register
                );

            } else {
                $input['business_id'] = $business_id;
                $input['cashier_id'] = $cashier_id;
                $input['user_id'] = auth()->user()->id;
                
                $cash_register = CashRegister::create($input);

                // Store binnacle
                $this->cashRegisterUtil->registerBinnacle(
                    $this->module_name,
                    'open',
                    null,
                    $cash_register
                );

                $this->cashRegisterUtil->registerBinnacle(
                    $this->module_name,
                    'close',
                    null,
                    $cash_register
                );
            }

            // Cash detail store
            $cash_detail_input = $request->only([
                'one_cent',
                'five_cents',
                'ten_cents',
                'twenty_five_cents',
                'one_dollar',
                'five_dollars',
                'ten_dollars',
                'twenty_dollars',
                'fifty_dollars',
                'one_hundred_dollars'
            ]);

            $cash_detail = CashDetail::where('cash_register_id', $cash_register->id)
                ->where('cashier_id', $cashier_id)
                ->where('business_id', $business_id)
                ->where('location_id', $location_id)
                ->first();

            if (! empty($cash_detail)) {
                $cash_detail->update($cash_detail_input);

            } else {
                $cash_detail_input['cash_register_id'] = $cash_register->id;
                $cash_detail_input['cashier_id'] = $cashier_id;
                $cash_detail_input['business_id'] = $business_id;
                $cash_detail_input['location_id'] = $location_id;
                
                $cash_detail = CashDetail::create($cash_detail_input);
            }

            $output = [
                'success' => 1,
                'msg' => __('cash_register.close_success')
            ];

        } catch (\Exception $e) {
            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->action('HomeController@index')->with('status', $output);
    }
}
