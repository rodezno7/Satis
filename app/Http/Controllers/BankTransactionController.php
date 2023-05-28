<?php

namespace App\Http\Controllers;

use App\BankTransaction;
use App\Contact;
use App\AccountingEntrie;
use App\AccountingPeriod;
use App\AccountingEntriesDetail;
use App\BankAccount;
use App\BankCheckbook;
use App\TypeBankTransaction;
use App\Business;
use App\BusinessLocation;
use App\Catalogue;
use App\Transaction;
use App\TypeEntrie;
use App\TransactionPayment;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;

use DB;
use Excel;
use Validator;
use DataTables;
use Carbon\Carbon;

use App\Exports\BankReconciliationReportExport;

class BankTransactionController extends Controller {

    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil) {

        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index() {

        return view('banks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        return view('banks.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $account_id = $request->input('account_id');
        $debe = $request->input('debe');
        $haber = $request->input('haber');
        $description = $request->input('description_line');
        $variable = $request->input('total_debe');

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        if ($business->allow_uneven_totals_entries == 1) {
            $validateData = $request->validate(
                [
                    "select-type-transaction" => "required",
                    "select-bank-account-id-transaction" => "required",
                    "txt-reference-transaction" => 'required',
                    "txt-date-transaction" => 'required|date',
                    "txt-description-transaction" => "required",
                    "txt-check-number-transaction" => "required|numeric",
                    'period_id' => 'required',

                    'debe.*' => ['required', 'numeric'],
                    'haber.*' => ['required', 'numeric'],
                    'total_debe' => 'required|numeric',
                    'total_haber' => 'required|numeric',
                    'haber.*' => ['different:debe.*'],
                    'business_location_id' => 'required',
                ],
                [
                    "select-bank-account-id-transaction.required" => __('accounting.account_required'),
                    "txt-reference-transaction.required" => __('accounting.reference_required'),
                    "txt-date-transaction.required" => __('accounting.date_required'),
                    
                    "txt-description-transaction.required" => __('accounting.description_required'),
                    "select-type-transaction.required" => __('accounting.type_required'),
                    

                    'txt-check-number-transaction.required' => __('accounting.check_number_required'),
                    'txt-check-number-transaction.numeric' => __('accounting.check_number_numeric'),
                    

                    'debe.*.required' => __('accounting.debit_required'),
                    'haber.*.required' => __('accounting.credit_required'),
                    'debe.*.numeric' => __('accounting.debit_numeric'),
                    'haber.*.numeric' => __('accounting.credit_numeric'),
                    'total_debe.required' => __('accounting.total_debit_required'),
                    'total_haber.required' => __('accounting.total_credit_required'),
                    'total_debe.numeric' => __('accounting.total_debit_numeric'),
                    'total_haber.numeric' => __('accounting.total_credit_numeric'),
                    'total_haber.in' => __('accounting.total_credit_in'),
                    'haber.*.different' => __('accounting.credit_different'),

                    'period_id.required' => __('accounting.period_id_required'),
                    "txt-date-transaction.date" => __('accounting.date_date'),
                    'business_location_id.required' => __('accounting.location_required'),
                ]
            );

        } else {

            $validateData = $request->validate(
                [
                    "select-type-transaction" => "required",
                    "select-bank-account-id-transaction" => "required",
                    "txt-reference-transaction" => 'required',
                    "txt-date-transaction" => 'required|date',
                    "txt-description-transaction" => "required",
                    "txt-check-number-transaction" => "required|numeric",
                    'period_id' => 'required',

                    'debe.*' => ['required', 'numeric'],
                    'haber.*' => ['required', 'numeric'],
                    'total_debe' => 'required|numeric',
                    'total_haber' => 'required|numeric|in:'.$variable,
                    'haber.*' => ['different:debe.*'],
                    'business_location_id' => 'required',
                ],
                [
                    "select-bank-account-id-transaction.required" => __('accounting.account_required'),
                    "txt-reference-transaction.required" => __('accounting.reference_required'),
                    "txt-date-transaction.required" => __('accounting.date_required'),

                    "txt-description-transaction.required" => __('accounting.description_required'),
                    "select-type-transaction.required" => __('accounting.type_required'),


                    'txt-check-number-transaction.required' => __('accounting.check_number_required'),
                    'txt-check-number-transaction.numeric' => __('accounting.check_number_numeric'),


                    'debe.*.required' => __('accounting.debit_required'),
                    'haber.*.required' => __('accounting.credit_required'),
                    'debe.*.numeric' => __('accounting.debit_numeric'),
                    'haber.*.numeric' => __('accounting.credit_numeric'),
                    'total_debe.required' => __('accounting.total_debit_required'),
                    'total_haber.required' => __('accounting.total_credit_required'),
                    'total_debe.numeric' => __('accounting.total_debit_numeric'),
                    'total_haber.numeric' => __('accounting.total_credit_numeric'),
                    'total_haber.in' => __('accounting.total_credit_in'),
                    'haber.*.different' => __('accounting.credit_different'),

                    'period_id.required' => __('accounting.period_id_required'),
                    "txt-date-transaction.date" => __('accounting.date_date'),
                    'business_location_id.required' => __('accounting.location_required'),
                ]
            );
        }
        
        if ($request->ajax()) {

            // Validation to allow the check amount not to match expenses total
            $check_amount = $this->transactionUtil->num_uf($request->input('txt-amount-transaction'));

            $expenses = $request->input('expenses', false);

            if ($this->transactionUtil->validateMatchCheckAndExpense($check_amount, $expenses, 'create')) {
                return [
                    'success' => false,
                    'msg' => __('accounting.match_check_n_expense_error')
                ];
            }

            $date = $request->input('txt-date-transaction');
            $business_id = request()->session()->get('user.business_id');
            $date_entrie = Carbon::parse($date);
            $mdate = $date_entrie->month;
            $ydate = $date_entrie->year;
            $config_numeration = Business::select('entries_numeration_mode')->where('id', $business_id)->first();
            $mode_numeration = $config_numeration->entries_numeration_mode;
            
            if($mode_numeration == 'month') {

                $count = AccountingEntrie::select(DB::raw('MAX(number) as last_number'))
                ->where('business_id', $business_id)
                ->whereMonth('date', $mdate)
                ->first();

                if($count->last_number == null) {

                    $code = 1;

                } else {

                    $code = $count->last_number + 1;

                }

            }
            
            if($mode_numeration == 'year') {

                $count = AccountingEntrie::select(DB::raw('MAX(number) as last_number'))
                ->where('business_id', $business_id)
                ->whereYear('date', $ydate)
                ->first();

                if($count->last_number == null) {

                    $code = 1;

                } else {

                    $code = $count->last_number + 1;

                }
            }
            
            if($mode_numeration == 'manual'){

                $code = 0;

            }

            try {

                $checkbook = BankCheckbook::where('id', $request->input("select-checkbook-transaction"))->first();
                
                if ($checkbook != null) {

                    $checkbook_initial = $checkbook->initial_correlative;
                    $checkbook_final = $checkbook->final_correlative;

                } else {

                    $checkbook_initial = 0;
                    $checkbook_final = 0;
                }

                if(($request->input('txt-check-number-transaction') < $checkbook_initial) || ($request->input('txt-check-number-transaction') > $checkbook_final)) {

                    $output = [
                        'success' => false,
                        'msg' => __("accounting.check_number_invalid")
                    ];
                    
                    return $output;
                }
                
                $period = DB::table('accounting_periods as period')
                ->join('fiscal_years as year', 'year.id', '=', 'period.fiscal_year_id')
                ->select('year.year', 'period.month')
                ->where('period.business_id', $business_id)
                ->where('period.id', $request->input('period_id'))
                ->first();

                $date = Carbon::parse($request->input('txt-date-transaction'));
                $mdate = $date->month;
                $ydate = $date->year;
                
                if($period->year != $ydate) {

                    $output = [
                        'success' => false,
                        'msg' => __("accounting.period_invalid")
                    ];
                    
                    return $output;
                }

                if($period->month != $mdate) {

                    $output = [
                        'success' => false,
                        'msg' => __("accounting.period_invalid")
                    ];

                    return $output;
                }

                $type_transaction_row = TypeBankTransaction::where('id', $request->input('select-type-transaction'))
                ->first();

                DB::beginTransaction();

                $entrie = new AccountingEntrie;
                $entrie->date = $request->input('txt-date-transaction');
                $entrie->number = $code;
                $entrie->description = $request->input('txt-description-transaction');
                $entrie->accounting_period_id = $request->input('period_id');
                $entrie->type_entrie_id = $type_transaction_row->type_entrie_id;
                $entrie->business_location_id = $request->input('business_location_id');
                $entrie->correlative = $code;
                $entrie->business_id = $business_id;

                $short_name_cont = str_pad($code, 5, "0", STR_PAD_LEFT);
                $type_q = TypeEntrie::where('id', $type_transaction_row->type_entrie_id)->first();
                $short_name_type = $type_q->short_name;
                
                if ($mdate < 10) {

                    $short_name_month = '0' . $mdate;

                } else {

                    $short_name_month = $mdate;

                }

                $short_name_year = $ydate;
                $short_name_full = $short_name_type . '-' . $short_name_year . $short_name_month . '-' . $short_name_cont;
                $entrie->short_name = $short_name_full;

                if ($business->enable_validation_entries == 1) {

                    $entrie->status = 0;

                } else {

                    $entrie->status = 1;
                    
                }

                $entrie->save();
                $cont = 0;
                
                if (!empty($account_id)) {

                    while($cont < count($account_id)) {

                        $detalle = new AccountingEntriesDetail;
                        $detalle->entrie_id = $entrie->id;
                        $detalle->account_id = $account_id[$cont];
                        $detalle->debit = $debe[$cont];
                        $detalle->credit = $haber[$cont];
                        $detalle->description = $description[$cont];
                        $detalle->save();
                        $cont = $cont + 1;

                    }
                }
                
                $transaction = new BankTransaction;
                $transaction->bank_account_id = $request->input('select-bank-account-id-transaction');
                $transaction->accounting_entrie_id =$entrie->id;
                $transaction->type_bank_transaction_id = $request->input("select-type-transaction");
                $transaction->bank_checkbook_id = $request->input("select-checkbook-transaction");
                $transaction->business_id = $business_id;

                if ($type_transaction_row->enable_checkbook == 0) {

                    $transaction->reference = $request->input('txt-reference-transaction');

                }

                $transaction->date = $request->input('txt-date-transaction');
                $transaction->amount = $request->input('txt-amount-transaction');
                $transaction->description = $request->input('txt-description-transaction');
                $transaction->headline = $request->input('txt-payment-to');
                
                if($request->input('txt-check-number-transaction') != "0") {

                    $transaction->check_number = $request->input('txt-check-number-transaction');
                    $actual_correlative = $checkbook->actual_correlative;
                    $checkbook->actual_correlative = $actual_correlative + 1;
                    $checkbook->save();

                    if ($transaction->check_number == $checkbook->final_correlative) {

                        $checkbook->status = 0;
                        $checkbook->save();
                    }
                }

                $transaction->save();

                // Save expenses
                $location_id = $request->input('business_location_id');
                
                if ($expenses) {
                    $i = 0;

                    foreach($expenses as $exp) {
                        $expense = new Transaction();
                        $expense->business_id = $business_id;
                        $expense->location_id = $location_id;
                        $expense->bank_transaction_id = $transaction->id;
                        $expense->type = "expense";
                        $expense->status = "final";
                        $expense->payment_status = "paid";
                        $expense->contact_id = $exp['_contact_id'];
                        $expense->expense_category_id = $exp['_expense_category_id'];
                        $expense->transaction_date = $this->transactionUtil->uf_date($exp['_transaction_date']);
                        $expense->document_types_id = $exp['_document_types_id'];
                        $expense->ref_no = $exp['_ref_no'];
                        $expense->payment_condition = $exp['_payment_condition'];
                        $expense->payment_term_id = $exp['_payment_term_id'];
                        $expense->total_before_tax = $this->transactionUtil->num_uf($exp['_total_before_tax']);
                        $expense->tax_id = $exp['_tax_group_id'];
                        $expense->tax_amount = $this->transactionUtil->num_uf($exp['_tax_amount']);
                        $expense->final_total = $this->transactionUtil->num_uf($exp['_final_total']);
                        $expense->additional_notes = $exp['_additional_notes'];
                        
                        $document = '[' . $i . ']' . '[_document]';
                        $document_name = $this->transactionUtil->uploadFile($request, $document, 'documents');
                        $expense->document = $document_name;

                        $expense->created_by = $request->session()->get('user.id');

                        $expense->save();

                        // Save payment
                        $bank_account = BankAccount::find($transaction->bank_account_id);

                        $payment = new TransactionPayment();
                        $payment->amount = $expense->final_total;
                        $payment->method = 'check';
                        $payment->paid_on = $transaction->date;
                        $payment->check_number = $transaction->check_number;
                        $payment->check_account = ! empty($bank_account) ? $bank_account->number : '';
                        $payment->check_bank = ! empty($bank_account) ? $bank_account->bank_id : null;
                        $payment->check_account_owner = null;
                        $payment->transaction_id = $expense->id;
                        $payment->created_by = $request->session()->get('user.id');
                        $payment->business_id = $business_id;

                        $prefix_type = 'expense_payment';
                        $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);

                        // Generate reference number
                        $payment->transfer_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                        $payment->save();

                        $i ++;
                    }
                }

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('accounting.bank_transaction_added')
                ];

            } catch(\Exception $e) {

                DB::rollBack();

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
     * Display the specified resource.
     *
     * @param  \App\BankTransaction  $bankTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(BankTransaction $bankTransaction) {

        $transaction = DB::table('bank_transactions as transaction')
        ->join('accounting_entries as entrie', 'entrie.id', '=', 'transaction.accounting_entrie_id')
        ->join('accounting_periods as period', 'period.id', '=', 'entrie.accounting_period_id')
        ->join('bank_accounts as bank_account', 'bank_account.id', '=', 'transaction.bank_account_id')
        ->select('transaction.*', 'entrie.number as partida', 'bank_account.name as banco', 'entrie.type_entrie_id', 'entrie.business_location_id', 'period.id as period_id', 'bank_account.catalogue_id as accounting_account')
        ->where('transaction.id', $bankTransaction->id)
        ->first();

        return response()->json($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankTransaction  $bankTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(BankTransaction $bankTransaction) {

        $transaction = DB::table('bank_transactions as transaction')
        ->join('accounting_entries as entrie', 'entrie.id', '=', 'transaction.accounting_entrie_id')
        ->select('transaction.*', 'entrie.accounting_period_id as period_value')
        ->where('transaction.id', $bankTransaction->id)
        ->first();
        
        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BankTransaction  $bankTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $bankTransaction = BankTransaction::findOrFail($id);
        $account_id = $request->input('account_id2');
        $debe = $request->input('debe2');
        $haber = $request->input('haber2');
        $description = $request->input('description_line2');
        $variable = $request->input('total_debe2');

        $business_id_selected = request()->session()->get('user.business_id');
        $business_selected = Business::where('id', $business_id_selected)->first();

        $entrie_selected = AccountingEntrie::where('id', $bankTransaction->accounting_entrie_id)->first();

        if (($business_selected->allow_uneven_totals_entries == 1) && ($entrie_selected->status == 0)) {

            $validateData = $request->validate(
                [
                    "eselect-type-transaction" => "required",
                    "eselect-bank-account-id-transaction" => "required",
                    "txt-ereference-transaction" => 'required',
                    "txt-edescription-transaction" => "required",
                    "txt-echeck-number-transaction" => "required|numeric",
                    'debe2.*' => ['required', 'numeric'],
                    'haber2.*' => ['required', 'numeric'],
                    'total_debe2' => 'required|numeric',
                    'total_haber2' => 'required|numeric',

                    'haber2.*' => ['different:debe2.*'],

                    'txt-edate-transaction' => 'required|date',
                    'eperiod_id' => 'required',
                    'ebusiness_location_id' => 'required',
                ],
                [
                    "eselect-bank-account-id-transaction.required" => __('accounting.account_required'),
                    "txt-ereference-transaction.required" => __('accounting.reference_required'),
                    "txt-edescription-transaction.required" => __('accounting.description_required'),
                    "eselect-type-transaction.required" => __('accounting.type_required'),
                    'txt-echeck-number-transaction.required' => __('accounting.check_number_required'),
                    'txt-echeck-number-transaction.numeric' => __('accounting.check_number_numeric'),
                    'debe2.*.required' => __('accounting.debit_required'),
                    'haber2.*.required' => __('accounting.credit_required'),
                    'debe2.*.numeric' => __('accounting.debit_numeric'),
                    'haber2.*.numeric' => __('accounting.credit_numeric'),
                    'total_debe2.required' => __('accounting.total_debit_required'),
                    'total_haber2.required' => __('accounting.total_credit_required'),
                    'total_debe2.numeric' => __('accounting.total_debit_numeric'),
                    'total_haber2.numeric' => __('accounting.total_credit_numeric'),
                    'total_haber2.in' => __('accounting.total_credit_in'),
                    'haber2.*.different' => __('accounting.credit_different'),

                    'eperiod_id.required' => __('accounting.period_id_required'),
                    "txt-edate-transaction.date" => __('accounting.date_date'),
                    'ebusiness_location_id.required' => __('accounting.location_required'),
                ]
            );

        } else {

            $validateData = $request->validate(
                [
                    "eselect-type-transaction" => "required",
                    "eselect-bank-account-id-transaction" => "required",
                    "txt-ereference-transaction" => 'required',
                    "txt-edescription-transaction" => "required",
                    "txt-echeck-number-transaction" => "required|numeric",
                    'debe2.*' => ['required', 'numeric'],
                    'haber2.*' => ['required', 'numeric'],
                    'total_debe2' => 'required|numeric',
                    'total_haber2' => 'required|numeric|in:'.$variable,

                    'haber2.*' => ['different:debe2.*'],

                    'txt-edate-transaction' => 'required|date',
                    'eperiod_id' => 'required',
                    'ebusiness_location_id' => 'required',
                ],
                [
                    "eselect-bank-account-id-transaction.required" => __('accounting.account_required'),
                    "txt-ereference-transaction.required" => __('accounting.reference_required'),
                    "txt-edescription-transaction.required" => __('accounting.description_required'),
                    "eselect-type-transaction.required" => __('accounting.type_required'),
                    'txt-echeck-number-transaction.required' => __('accounting.check_number_required'),
                    'txt-echeck-number-transaction.numeric' => __('accounting.check_number_numeric'),
                    'debe2.*.required' => __('accounting.debit_required'),
                    'haber2.*.required' => __('accounting.credit_required'),
                    'debe2.*.numeric' => __('accounting.debit_numeric'),
                    'haber2.*.numeric' => __('accounting.credit_numeric'),
                    'total_debe2.required' => __('accounting.total_debit_required'),
                    'total_haber2.required' => __('accounting.total_credit_required'),
                    'total_debe2.numeric' => __('accounting.total_debit_numeric'),
                    'total_haber2.numeric' => __('accounting.total_credit_numeric'),
                    'total_haber2.in' => __('accounting.total_credit_in'),
                    'haber2.*.different' => __('accounting.credit_different'),

                    'eperiod_id.required' => __('accounting.period_id_required'),
                    "txt-edate-transaction.date" => __('accounting.date_date'),
                    'ebusiness_location_id.required' => __('accounting.location_required'),
                ]
            );

        }

        
        if($request->ajax()) {

            $date = $request->input('txt-edate-transaction');
            $business_id = request()->session()->get('user.business_id');
            $date_entrie = Carbon::parse($date);
            $mdate = $date_entrie->month;
            $ydate = $date_entrie->year;
            $config_numeration = Business::select('entries_numeration_mode')->where('id', $business_id)->first();
            $mode_numeration = $config_numeration->entries_numeration_mode;
            
            if($mode_numeration == 'month') {

                $count = AccountingEntrie::select(DB::raw('MAX(number) as last_number'))
                ->where('business_id', $business_id)
                ->whereMonth('date', $mdate)
                ->first();

                if($count->last_number == null) {

                    $code = 1;

                } else {

                    $code = $count->last_number + 1;
                }

            }
            
            if($mode_numeration == 'year') {

                $count = AccountingEntrie::select(DB::raw('MAX(number) as last_number'))
                ->where('business_id', $business_id)
                ->whereYear('date', $ydate)
                ->first();

                if($count->last_number == null) {

                    $code = 1;

                } else {

                    $code = $count->last_number + 1;
                }
            }

            if($mode_numeration == 'manual') {

                $code = 0;
            }

            try {

                $entrie = AccountingEntrie::find($bankTransaction->accounting_entrie_id);
                
                $checkbook = BankCheckbook::where('id', $request->input("eselect-checkbook-transaction"))->first();
                
                if ($checkbook != null) {

                    $checkbook_initial = $checkbook->initial_correlative;
                    $checkbook_final = $checkbook->final_correlative;

                } else {

                    $checkbook_initial = 0;
                    $checkbook_final = 0;
                }

                if(($request->input('txt-echeck-number-transaction') < $checkbook_initial) || ($request->input('txt-echeck-number-transaction') > $checkbook_final)) {

                    $output = [
                        'success' => false,
                        'msg' => __("accounting.check_number_invalid")
                    ];

                    return $output;
                }

                $period = DB::table('accounting_periods as period')
                ->join('fiscal_years as year', 'year.id', '=', 'period.fiscal_year_id')
                ->select('year.year', 'period.month')
                ->where('period.id', $entrie->accounting_period_id)
                ->first();

                $date = Carbon::parse($request->input('txt-edate-transaction'));
                $mdate = $date->month;
                $ydate = $date->year;

                if($period->year != $ydate) {

                    $output = [
                        'success' => false,
                        'msg' => __("accounting.period_invalid")
                    ];

                    return $output;
                }   

                if($period->month != $mdate) {

                    $output = [
                        'success' => false,
                        'msg' => __("accounting.period_invalid")
                    ];
                    
                    return $output;

                }

                $type_transaction_row = TypeBankTransaction::findOrFail($request->input('eselect-type-transaction'));
                
                DB::beginTransaction();
                
                AccountingEntriesDetail::where('entrie_id', $bankTransaction->accounting_entrie_id)->forceDelete();

                $entrie->description = $request->input('txt-edescription-transaction');
                $entrie->type_entrie_id = $type_transaction_row->type_entrie_id;
                $entrie->date = $request->input('txt-edate-transaction');
                $entrie->accounting_period_id = $request->input('eperiod_id');
                $entrie->business_location_id = $request->input('ebusiness_location_id');
                $entrie->save();
                $cont = 0;
                
                if (!empty($account_id)) {

                    while($cont < count($account_id)) {

                        $detalle = new AccountingEntriesDetail;
                        $detalle->entrie_id = $entrie->id;
                        $detalle->account_id = $account_id[$cont];
                        $detalle->debit = $debe[$cont];
                        $detalle->credit = $haber[$cont];
                        $detalle->description = $description[$cont];
                        $detalle->save();
                        $cont = $cont + 1;
                    }
                }

                $bankTransaction->bank_account_id = $request->input('eselect-bank-account-id-transaction');
                $bankTransaction->accounting_entrie_id =$entrie->id;
                $bankTransaction->type_bank_transaction_id = $request->input("eselect-type-transaction");
                $bankTransaction->bank_checkbook_id = $request->input("eselect-checkbook-transaction");

                if ($type_transaction_row->enable_checkbook == 0) {

                    $bankTransaction->reference = $request->input('txt-ereference-transaction');
                }

                $bankTransaction->date = $request->input('txt-edate-transaction');
                $bankTransaction->amount = $request->input('txt-eamount-transaction');
                $bankTransaction->description = $request->input('txt-edescription-transaction');
                $bankTransaction->headline = $request->input('txt-epayment-to');
                
                if($request->input('txt-echeck-number-transaction') != "0") {

                    $bankTransaction->check_number = $request->input('txt-echeck-number-transaction');

                    if ($bankTransaction->check_number == $checkbook->final_correlative) {

                        $checkbook->status = 0;
                        $checkbook->save();
                    }
                }

                $bankTransaction->save();
                
                DB::commit();
                
                $output = [
                    'success' => true,
                    'msg' => __('accounting.bank_transaction_updated')
                ];

            } catch(\Exception $e) {

                DB::rollBack();

                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];

            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BankTransaction  $bankTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankTransaction $bankTransaction) {

        if(request()->ajax()) {

            try {

                $entrie = AccountingEntrie::findOrFail($bankTransaction->accounting_entrie_id);

                $payments = DB::table('transaction_payments as tp')
                ->where('tp.bank_transaction_id', $bankTransaction->id)
                ->count();

                if ($payments > 0) {

                    $output = [
                        'success' => false,
                        'msg' => __("accounting.transaction_has_dependencies")
                    ];
                    
                    return $output;
                }

                $entrie->forceDelete();
                $bankTransaction->forceDelete();

                $output = [
                    'success' => true,
                    'msg' => __("accounting.transaction_deleted")
                ];

            } catch(\Exception $e){

                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];

            }
            
            return $output;
        }
        
    }

    /**
     * Generate bank reconciliation report
     */
    public function getBankReconciliation(Request $request){

        if (!auth()->user()->can('bank_reconciliation')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->user()->business_id;
        $bank_id = $request->input('bank');
        $bank_account_id = $request->input('bank_account');
        $start_date = $this->transactionUtil->uf_date($request->input('start_date'));
        $end_date = $this->transactionUtil->uf_date($request->input('end_date'));
        $transaction_type = $request->input('transaction_type');

        /** get bank transactions */
        $bank_transactions =
        BankTransaction::where('bank_account_id', $bank_account_id)
        ->where('business_id', $business_id)
        ->whereRaw('DATE(date) BETWEEN ? AND ?', [$start_date, $end_date]);
        
        /** filter bank transaction by transaction type*/
        if($transaction_type != 'all') {

            $bank_transactions = $bank_transactions->where('type_bank_transaction_id', $transaction_type);

        }

        $bank_transactions =
        $bank_transactions->select('id',
            DB::raw('IF(check_number > 0, check_number, reference) as reference'),
            'date', 'description', 'amount')
        ->get();

        /** get transactions from uploaded file */
        $file = $request->file('bank_reconciliation_xlsx');
        $imported_file = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[0];
        unset($imported_file[0]); // header

        $transactions = collect();
        $date_time = new \DateTime();
        foreach($imported_file as $key => $value){
            $row_no = $key + 1;

            /** Transaction date */
            $transaction_date = trim($value[0]);
            if(!empty($transaction_date)) {
                if($date_time::createFromFormat('d/m/Y', $transaction_date) !== false) {
                    $transaction_date = $this->transactionUtil->uf_date($transaction_date);
                } else {
                    return __("lang_v1.transaction_date_invalid_format", ['row' => $row_no]);
                }
            } else {
                return __("lang_v1.transaction_date_required", ['row' => $row_no]);
            }

            /** Transaction reference */
            $transaction_reference = trim($value[1]);
            if(!empty($transaction_reference)){
                $transaction_reference = $transaction_reference;
            } else {
                return __("lang_v1.transaction_reference_required", ['row' => $row_no]);
            }

            /** Transaction description */
            $transaction_description = trim($value[2]);

            /** Transaction amount */
            $transaction_amount = trim($value[3]);
            if(!empty($transaction_amount)){
                if(is_numeric($transaction_amount) !== false){
                    $transaction_amount = $this->transactionUtil->num_uf($transaction_amount);
                } else{
                    return __("lang_v1.transaction_amount_nan", ['row' => $row_no]);
                }
            } else {
                return __("lang_v1.transaction_amount_required", ['row' => $row_no]);
            }

            /** compare system transaction versus transactions uploaded */
            $row_ref = $bank_transactions->where('reference', $transaction_reference)->first();
            $row_status = ''; $row_description = $transaction_description;
            $row_id = 0; $row_system_amount = null;

            if(!empty($row_ref)) {
                $row_id = $row_ref->id;
                $row_system_amount = $row_ref->amount;
                
                $row_amount =
                $bank_transactions
                ->where('reference', $transaction_reference)
                ->where('amount', $transaction_amount)
                ->first();
                
                $row_status = !empty($row_amount) ? 'green' : 'yellow';

                if(empty($transaction_description)) {
                    $row_description = $row_ref->description;
                }

            } else {
                $row_status = 'red';
            }

            $row = collect(
                [
                    'id' => $row_id,
                    'status' => $row_status,
                    'transaction_date' => $transaction_date,
                    'reference' => $transaction_reference,
                    'description' => $row_description,
                    'system' => $row_system_amount,
                    'bank' => $transaction_amount
                ]
            );

            $transactions->push($row);
        }

        /** merge with system bank transaction */
        $uploaded_ids = $transactions->pluck('id');
        $system_transaction = $bank_transactions->whereNotIn('id', $uploaded_ids);
        foreach($system_transaction as $st){
            $row = collect(
                [
                    'id' => $st->id,
                    'status' => 'red',
                    'transaction_date' => $st->date,
                    'reference' => $st->reference,
                    'description' => $st->description,
                    'system' => $st->amount,
                    'bank' => 0
                ]
            );

            $transactions->push($row);
        }

        $transactions = $transactions->sortBy('transaction_date');

        $business_name = Business::where('id', $business_id)
        ->first()->business_full_name;

        $bank_account_name = BankAccount::find($bank_account_id)->name;

        $report_name = __('accounting.bank_reconciliation') . " " . __('accounting.from') . " " .
        $this->transactionUtil->format_date($start_date) . " " . __('accounting.to') . " " . $this->transactionUtil->format_date($end_date);

        return Excel::download(new BankReconciliationReportExport($transactions, $business_name, $report_name, $bank_account_name, $this->transactionUtil), __('accounting.bank_reconciliation') . '.xlsx');
    }

    public function getBankTransactionsData($period, $type, $bank) {

        $business_id = request()->session()->get('user.business_id');

        $bankTransactions = DB::table('bank_transactions as transaction')
        ->join('accounting_entries as entrie', 'entrie.id', '=', 'transaction.accounting_entrie_id')
        ->join('bank_accounts', 'bank_accounts.id', '=', 'transaction.bank_account_id')
        ->join('type_bank_transactions as type', 'type.id', '=', 'transaction.type_bank_transaction_id')
        ->select(
            'transaction.*',
            'entrie.short_name as entrie',
            'bank_accounts.name as bank',
            'entrie.type_entrie_id',
            'entrie.business_location_id',
            'type.name as type_transaction',
            'type.type as type',
            'entrie.status as entrie_status',
            DB::raw('transaction.id as bank_transaction_query'),
            DB::raw('(select COUNT(id) from transaction_payments where bank_transaction_id = bank_transaction_query) as expenses')
        )
        ->where('transaction.business_id', $business_id);

        if($period != 0) {

            $bankTransactions->where('entrie.accounting_period_id', $period);
        }

        if($type != 0) {

            $bankTransactions->where('type_bank_transaction_id', $type);
        }

        if($bank != 0) {

            $bankTransactions->where('bank_account_id', $bank);
        }
        
        return DataTables::of($bankTransactions)->toJson();
    }

    public function getConfiguration() {

        $business_id = request()->session()->get('user.business_id');
        $config_transactions = Business::select('enable_sub_accounts_in_bank_transactions as config', 'accounting_supplier_id', 'accounting_customer_id')
        ->where('id', $business_id)
        ->first();
        return $config_transactions;

    }

    public function getDateValidation($type, $checkbook, $date) {

        $type_transaction_row = TypeBankTransaction::where('id', $type)->first();
        
        if ($type_transaction_row != null) {

            if ($type_transaction_row->enable_date_constraint == 1) {

                $count = BankTransaction::where('date', '>', $date)
                ->where('bank_checkbook_id', $checkbook)
                ->count();
                
                if ($count > 0) {

                    $output = [
                        'success' => false,
                        'msg' => __("accounting.invalid_check_date")
                    ];
                    
                    return $output;

                } else {

                    $output = [
                        'success' => true,
                        'msg' => "OK"
                    ];
                }

            } else {

                $output = [
                    'success' => true,
                    'msg' => "OK"
                ];
            }

        } else {

            $output = [
                'success' => true,
                'msg' => "OK"
            ];
        }        
        
        return $output;
    }
    
    public function getDateByPeriod($id) {

        $period = DB::table('accounting_periods')
        ->join('fiscal_years', 'fiscal_years.id', '=', 'accounting_periods.fiscal_year_id')
        ->select('accounting_periods.month as month', 'fiscal_years.year as year')
        ->where('accounting_periods.id', $id)
        ->first();
        
        $date = Carbon::createFromDate($period->year, $period->month);
        
        $output = [
            'date' => $date->format('Y-m-d'),
            'year' => $period->year,
            'month' => $period->month
        ];
        
        return $output;
    }

    public function validateDate($id, $dat) {

        $period = DB::table('accounting_periods as period')
        ->join('fiscal_years as year', 'year.id', '=', 'period.fiscal_year_id')
        ->select('year.year', 'period.month')
        ->where('period.id', $id)
        ->first();

        $date = Carbon::parse($dat);
        $mdate = $date->month;
        $ydate = $date->year;
        
        if(($period->year != $ydate) || ($period->month != $mdate)) {

            $output = [
                'success' => false,
                'msg' => __("accounting.period_invalid")
            ];

        } else {

            $output = [
                'success' => true,
                'msg' => 'OK'
            ];
        }

        return $output;
    }

    public function cancelCheck($id) {

        try {

            $payments = DB::table('transaction_payments as tp')
            ->where('tp.bank_transaction_id', $id)
            ->count();

            if ($payments > 0) {

                $output = [
                    'success' => false,
                    'msg' => __("accounting.transaction_has_dependencies")
                ];

                return $output;
            }

            $business_id = request()->session()->get('user.business_id');
            $business_numeration_entries = Business::select('entries_numeration_mode')->where('id', $business_id)->first();
            $numeration = $business_numeration_entries->entries_numeration_mode;
            $transaction = BankTransaction::findOrFail($id);
            $entrie = AccountingEntrie::where('id', $transaction->accounting_entrie_id)->first();

            if($transaction->status == 1) {

                $transaction->status = 0;

                $entrie->status_bank_transaction = 0;
                $entrie->status = 0;
                $entrie->number = 0;
                $entrie->correlative = 0;
                

            } else {

                $transaction->status = 1;
                $entrie->status_bank_transaction = 1;
            }

            $transaction->save();
            $entrie->save();


            $output = [
                'success' => true,
                'msg' => __('accounting.updated_successfully')
            ];

        } catch(\Exception $e) {

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];

        }
        
        return $output;

    }

    /**
     * Print check and entrie.
     * 
     * @param  int  $id
     * @param  int  $print
     * @return \Illuminate\Http\Response
     */
    public function printCheck($id, $print) {

        $business_id = request()->session()->get('user.business_id');

        $business = Business::find($business_id);
        
        switch ($business->check_format_kit) {
            case 1:
            return $this->printCheckFormat1($id, $print);
            break;

            case 2:
            return $this->printCheckFormat2($id, $print);
            break;
        }
    }

    /**
     * Print check and entrie. (Nuves format)
     * 
     * @param  int  $id
     * @param  int  $print
     * @return \Illuminate\Http\Response
     */
    public function printCheckFormat1($id, $print) {

        // ----- CHECK -----

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $transaction = BankTransaction::findOrFail($id);

        $entrie_type = mb_strtoupper($transaction->entrie->type->description);
        $entrie_no = $transaction->entrie->correlative == 0 ? $transaction->entrie->number : $transaction->entrie->correlative;

        $checkbook = BankCheckbook::findOrFail($transaction->bank_checkbook_id);

        $bank = BankAccount::findOrFail($transaction->bank_account_id);

        $bank_name = $bank->bank->name;

        $account = Catalogue::findOrFail($bank->catalogue_id);

        $amount_check_q = DB::table('accounting_entries_details')
        ->select('accounting_entries_details.credit')
        ->where('entrie_id', $transaction->accounting_entrie_id)
        ->where('account_id', $account->id)
        ->first();

        $format = $bank->bank->print_format;
        
        $place = mb_strtoupper($business->state->name) . ', ';

        $date = Carbon::parse($transaction->date);

        $months = array(
            __('accounting.january'),
            __('accounting.february'),
            __('accounting.march'),
            __('accounting.april'),
            __('accounting.may'),
            __('accounting.june'),
            __('accounting.july'),
            __('accounting.august'),
            __('accounting.september'),
            __('accounting.october'),
            __('accounting.november'),
            __('accounting.december')
        );

        $day = $date->format('d').' ';
        $of = __('accounting.of'). " ";
        $month = $months[($date->format('n')) - 1]." ";
        $year = $date->format('Y');
        $sub_year = substr($year, -2);

        $amount = number_format($amount_check_q->credit, 2);
        $person = utf8_decode($transaction->headline);

        $letters = $this->convertir($amount);
        $letters2 = utf8_decode(strtolower($letters));
        $letters3 = substr($letters2, 0, 1);
        $letters4 = mb_strtoupper($letters3);
        $letters5 = mb_strtoupper(substr($letters2, 1));
        $value_letters = $letters4 . $letters5;

        if ($format == 'credomatic' || $format == 'default') {
            $place_date = $place.$day.$of.$month.$of.$year;
            $place_date_x = 3;
            $place_date_y = 1.65;

            $amount_x = 11.4;
            $amount_y = 1.65;

            $person_x = 2.7;
            $person_y = 2.25;

            $value_letters_x = 2.3;
            $value_letters_y = 2.8;
        }

        if ($format == 'agricola') {
            $place_date = $place.$day.$of.$month.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$year;
            $place_date_x = 2.7;
            $place_date_y = 1.8;

            $amount_x = 11.3;
            $amount_y = 1.8;

            $person_x = 2.5;
            $person_y = 2.4;

            $value_letters_x = 2.5;
            $value_letters_y = 3;
        }

        if ($format == 'promerica') {
            $place_date = $place.$day.$of.$month.$of.$year;
            $place_date_x = 2.2;
            $place_date_y = 1.85;

            $amount_x = 11.4;
            $amount_y = 1.85;

            $person_x = 3.2;
            $person_y = 2.45;

            $value_letters_x = 2.3;
            $value_letters_y = 3.15;
        }

        if ($format == 'azul') {
            $place_date = $place.$day.$of.$month.$of.$year;
            $place_date_x = 3;
            $place_date_y = 1.7;

            $amount_x = 11.1;
            $amount_y = 1.7;

            $person_x = 3.3;
            $person_y = 2.5;

            $value_letters_x = 2.4;
            $value_letters_y = 3.1;
        }

        $check_number = $transaction->check_number;

        $description = $transaction->description;

        $date_exp = explode('-', $transaction->date);

        $day = $date_exp[2];
        $month = $date_exp[1];
        $year = $date_exp[0];

        # ----- ENTRIE -----

        $business_name = mb_strtoupper($business->business_full_name);
        $accountant = mb_strtoupper($business->accountant);
        $enable_description_line = $business->enable_description_line_entries_report;

        $numero = 0;

        $entries = DB::table('accounting_entries as ae')
        ->leftJoin('type_entries as te', 'ae.type_entrie_id', 'te.id')
        ->select('ae.id', 'ae.correlative', 'ae.date', 'ae.description', 'te.name as type_entrie')
        ->where('ae.id', $transaction->accounting_entrie_id)
        ->orderBy('ae.correlative', 'asc')
        ->get();

        $entrie_details = DB::table('accounting_entries_details as detalle')
        ->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')
        ->join('accounting_entries as partida', 'partida.id', '=', 'detalle.entrie_id')
        ->select('detalle.entrie_id', 'detalle.account_id', 'detalle.debit', 'detalle.credit', 'detalle.description', 'cuenta.code', 'cuenta.name')
        ->where('partida.id', $transaction->accounting_entrie_id)
        ->orderBy('cuenta.code', 'asc')
        ->get();

        $grupos = array();
        $elementos = array();
        $detalles = array();

        $digits = $business->ledger_digits;

        foreach ($entrie_details as $detail) {

            $mayor = substr($detail->code, 0, $digits);
            $id_partida = $detail->entrie_id;

            if ($detail->debit != 0) {

                $columna = "D";

            }

            if ($detail->credit != 0) {
                $columna = "H";
            }

            $elemento_grupos = $columna . '.' . $id_partida . '.' . $mayor;

            if (!in_array($elemento_grupos, $grupos)) {

                array_push($grupos, $elemento_grupos);

                $debe = 0;
                $haber = 0;

                $cuenta = DB::table('catalogues')
                ->select('name')
                ->where('business_id', $business_id)
                ->where('code', $mayor)
                ->first();

                $nombre = $cuenta->name;

                if (($id_partida == $detail->entrie_id)) {

                    $valor = $this->getHigherEntrieBalance($id_partida, $mayor);
                    $debe = $valor->debe;
                    $haber = $valor->haber;
                }

                $item_elemento = array(
                 'partida' => $id_partida,
                 'mayor' => $mayor,
                 'nombre' => $nombre,
                 'columna' => $columna,
                 'debe' => $debe,
                 'haber' => $haber,
             );

                array_push($elementos, $item_elemento);
            }

            $item_detalle = array(
                'entrie_id' => $detail->entrie_id,
                'debe' => $detail->debit,
                'haber' => $detail->credit,
                'mayor' => $mayor,
                'code' => $detail->code,
                'name' => $detail->name,
                'description' => $detail->description,
            );

            array_push($detalles, $item_detalle);
        }

        $elements = json_decode(json_encode($elementos), FALSE);
        $details = json_decode(json_encode($detalles), FALSE);
        $partidas = array();

        foreach ($entries as $entrie) {

            $grupos_debe = array();
            $grupos_haber = array();
            $total_debe = 0;
            $total_haber = 0;

            foreach ($elements as $elemento) {

                $items_debe = array();
                $items_haber = array();

                foreach ($details as $detalle) {

                    if (($entrie->id == $detalle->entrie_id) && ($elemento->partida == $detalle->entrie_id) && ($elemento->mayor == $detalle->mayor)) {

                        if ($detalle->debe != 0 && $elemento->columna == "D") {

                            $elemento_items_debe = array(
                                'code' => $detalle->code,
                                'name' => $detalle->name,
                                'valor' => $detalle->debe,
                                'description_line' => $detalle->description,
                            );

                            array_push($items_debe, $elemento_items_debe);
                        }

                        if ($detalle->haber != 0 && $elemento->columna == "H") {

                            $elemento_items_haber = array(
                                'code' => $detalle->code,
                                'name' => $detalle->name,
                                'valor' => $detalle->haber,
                                'description_line' => $detalle->description,
                            );

                            array_push($items_haber, $elemento_items_haber);
                        }
                    }
                }

                if (($entrie->id == $elemento->partida) && ($elemento->columna == "D")) {

                    $elemento_grupo_debe = array(
                      'mayor' => $elemento->mayor,
                      'nombre' => $elemento->nombre,
                      'debe' => $elemento->debe,
                      'items' => $items_debe,
                  );

                    array_push($grupos_debe, $elemento_grupo_debe);

                    $total_debe = $total_debe + $elemento->debe;
                }

                if (($entrie->id == $elemento->partida) && ($elemento->columna == "H")) {

                    $elemento_grupo_haber = array(
                      'mayor' => $elemento->mayor,
                      'nombre' => $elemento->nombre,
                      'haber' => $elemento->haber,
                      'items' => $items_haber,
                  );

                    array_push($grupos_haber, $elemento_grupo_haber);

                    $total_haber = $total_haber + $elemento->haber;
                }
            }

            $elemento_partidas = array(
                'id' => $entrie->id,
                'correlative' => $entrie->correlative,
                'date' => $entrie->date,
                'total_debe' => $total_debe,
                'total_haber' => $total_haber,
                'description' => $entrie->description,
                'grupos_debe' => $grupos_debe,
                'grupos_haber' => $grupos_haber,
                'accountant' => $accountant,
                'type_entrie' => $entrie->type_entrie,
            );

            array_push($partidas, $elemento_partidas);
        }

        $datos = json_decode(json_encode($partidas), FALSE);

        $pdf = \PDF::loadView('banks.receipts.check_1', compact(
            'place_date_x',
            'place_date_y',
            'place_date',
            'amount_x',
            'amount_y',
            'amount',
            'person_x',
            'person_y',
            'person',
            'value_letters_x',
            'value_letters_y',
            'value_letters',
            'check_number',
            'description',
            'day',
            'month',
            'year',
            'enable_description_line',
            'datos',
            'numero',
            'business_name',
            'bank_name',
            'print',
            'entrie_type',
            'entrie_no'
        ));

        $pdf->setPaper('letter');

        return $pdf->stream('check.pdf');
    }

    /**
     * Print check and entrie. (Recielsa format)
     * 
     * @param  int  $id
     * @param  int  $print
     * @return \Illuminate\Http\Response
     */
    public function printCheckFormat2($id, $print) {

        // ----- CHECK -----

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $transaction = BankTransaction::findOrFail($id);

        $entrie_type = mb_strtoupper($transaction->entrie->type->description);
        $entrie_no = $transaction->entrie->correlative == 0 ? $transaction->entrie->number : $transaction->entrie->correlative;

        $checkbook = BankCheckbook::findOrFail($transaction->bank_checkbook_id);

        $bank = BankAccount::findOrFail($transaction->bank_account_id);

        $bank_name = $bank->bank->name;

        $account = Catalogue::findOrFail($bank->catalogue_id);

        $amount_check_q = DB::table('accounting_entries_details')
        ->select('accounting_entries_details.credit')
        ->where('entrie_id', $transaction->accounting_entrie_id)
        ->where('account_id', $account->id)
        ->first();

        $format = $bank->bank->print_format;
        
        $place = mb_strtoupper($business->state->name) . ', ';

        $date = Carbon::parse($transaction->date);

        $months = array(
            __('accounting.january'),
            __('accounting.february'),
            __('accounting.march'),
            __('accounting.april'),
            __('accounting.may'),
            __('accounting.june'),
            __('accounting.july'),
            __('accounting.august'),
            __('accounting.september'),
            __('accounting.october'),
            __('accounting.november'),
            __('accounting.december')
        );

        $day = mb_strtoupper($date->format('d')) . ' ';
        $of = mb_strtoupper(__('accounting.of')) . ' ';
        $month = mb_strtoupper($months[($date->format('n')) - 1]) . ' ';
        $year = $date->format('Y');
        $sub_year = substr($year, -2);

        $amount = number_format($amount_check_q->credit, 2);
        $person = mb_strtoupper($transaction->headline);

        $letters = $this->convertir($amount);
        $letters2 = utf8_decode(strtolower($letters));
        $letters3 = substr($letters2, 0, 1);
        $letters4 = mb_strtoupper($letters3);
        $letters5 = mb_strtoupper(substr($letters2, 1));
        $value_letters = $letters4 . $letters5;

        if ($format == 'credomatic' || $format == 'default' || is_null($format)) {

            $place_date = $place . $day . $of . $month . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $year;
            $place_date_x = 3.3;
            $place_date_y = 2.3;

            $amount_x = 12.3;
            $amount_y = 2.3;

            $person_x = 1.8;
            $person_y = 3.5;

            $value_letters_x = 1.8;
            $value_letters_y = 4.3;

            $asterisks_x = 0.5;
            $asterisks_y = 5.1;

            $person_check = str_pad('**' . $person, 60, '*', STR_PAD_RIGHT);
            $letters_check = str_pad('**' . $value_letters, 60, '*', STR_PAD_RIGHT);
            $asterisks = str_pad('', 22, '*', STR_PAD_RIGHT);

            $flag_labels = 1;
            $show_check = 1;

            $entrie_width = 15;
            $entrie_left = 0.6;
            $entrie_top = 10.8;
            $show_table = 1;
        }

        if ($format == 'agricola') {
            $place_date = $place . $day . $of . $month . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $year;
            $place_date_x = 2.5;
            $place_date_y = 2.5;

            $amount_x = 12.3;
            $amount_y = 2.5;

            $person_x = 1.8;
            $person_y = 3.3;

            $value_letters_x = 1.8;
            $value_letters_y = 4.1;

            $asterisks_x = 0.5;
            $asterisks_y = 5.0;

            $person_check = str_pad('**' . $person, 60, '*', STR_PAD_RIGHT);
            $letters_check = str_pad('**' . $value_letters, 60, '*', STR_PAD_RIGHT);
            $asterisks = str_pad('', 22, '*', STR_PAD_RIGHT);

            $flag_labels = 0;
            $show_check = 0;

            $entrie_width = 15;
            $entrie_left = 0.4;
            $entrie_top = 11.5;
            $show_table = 0;
        }

        if ($format == 'promerica') {
            $place_date = $place . $day . $of . $month . $of . $year;
            $place_date_x = 1.7;
            $place_date_y = 2.45;

            $amount_x = 12.2;
            $amount_y = 2.45;

            $person_x = 1.7;
            $person_y = 3.25;

            $value_letters_x = 1.7;
            $value_letters_y = 4.1;

            $asterisks_x = 0.5;
            $asterisks_y = 5.0;

            $person_check = str_pad('**' . $person, 70, '*', STR_PAD_RIGHT);
            $letters_check = str_pad('**' . $value_letters, 70, '*', STR_PAD_RIGHT);
            $asterisks = str_pad('', 25, '*', STR_PAD_RIGHT);

            $flag_labels = 1;
            $show_check = 1;

            $entrie_width = 14.5;
            $entrie_left = 0.4;
            $entrie_top = 10.2;
            $show_table = 1;
        }

        if ($format == 'azul') {
            $place_date = $place . $day . $of . $month . $of . $year;
            $place_date_x = 3.0;
            $place_date_y = 2.5;

            $amount_x = 11.5;
            $amount_y = 2.5;

            $person_x = 2.5;
            $person_y = 3.3;

            $value_letters_x = 1.0;
            $value_letters_y = 4.2;

            $asterisks_x = 0.5;
            $asterisks_y = 5.0;

            $person_check = str_pad('**' . $person, 60, '*', STR_PAD_RIGHT);
            $letters_check = str_pad('**' . $value_letters, 68, '*', STR_PAD_RIGHT);
            $asterisks = str_pad('', 22, '*', STR_PAD_RIGHT);

            $flag_labels = 1;
            $show_check = 1;

            $entrie_width = 15;
            $entrie_left = 0.6;
            $entrie_top = 10.5;
            $show_table = 1;
        }

        if ($format == 'cuscatlan') {
            $place_date = $place . $day . $of . $month . $of . $year;
            $place_date_x = 2.7;
            $place_date_y = 2.5;

            $amount_x = 12.5;
            $amount_y = 2.5;

            $person_x = 2.7;
            $person_y = 3.3;

            $value_letters_x = 2.7;
            $value_letters_y = 4.1;

            $asterisks_x = 0.5;
            $asterisks_y = 4.9;

            $person_check = str_pad('**' . $person, 66, '*', STR_PAD_RIGHT);
            $letters_check = str_pad('**' . $value_letters, 66, '*', STR_PAD_RIGHT);
            $asterisks = str_pad('', 25, '*', STR_PAD_RIGHT);

            $flag_labels = 0;
            $show_check = 0;

            $entrie_width = 15;
            $entrie_left = 0.4;
            $entrie_top = 10.5;
            $show_table = 0;
        }

        if ($format == 'davivienda') {
            $place_date = $place . $day . $of . $month . $of . $year;
            $place_date_x = 3.3;
            $place_date_y = 2.5;

            $amount_x = 12.3;
            $amount_y = 2.5;

            $person_x = 1.8;
            $person_y = 3.2;

            $value_letters_x = 1.8;
            $value_letters_y = 4.0;

            $asterisks_x = 0.5;
            $asterisks_y = 4.8;

            $person_check = str_pad('**' . $person, 68, '*', STR_PAD_RIGHT);
            $letters_check = str_pad('**' . $value_letters, 68, '*', STR_PAD_RIGHT);
            $asterisks = str_pad('', 22, '*', STR_PAD_RIGHT);

            $flag_labels = 1;
            $show_check = 1;

            $entrie_width = 15;
            $entrie_left = 0.4;
            $entrie_top = 10;
            $show_table = 1;
        }

        if ($format == 'hipotecario') {
            $place_date = $place . $day . $of . $month . $of . $year;
            $place_date_x = 3.0;
            $place_date_y = 2.6;

            $amount_x = 12.1;
            $amount_y = 2.6;

            $person_x = 1.6;
            $person_y = 3.4;

            $value_letters_x = 1.2;
            $value_letters_y = 4.25;

            $asterisks_x = 0.5;
            $asterisks_y = 5.1;

            $person_check = str_pad('**' . $person, 60, '*', STR_PAD_RIGHT);
            $letters_check = str_pad('**' . $value_letters, 68, '*', STR_PAD_RIGHT);
            $asterisks = str_pad('', 22, '*', STR_PAD_RIGHT);

            $flag_labels = 0;
            $show_check = 0;

            $entrie_width = 14.5;
            $entrie_left = 0.4;
            $entrie_top = 10.6;
            $show_table = 0;
        }

        if ($format == 'constelacion') {
            $place_date = $place . $day . $of . $month . $of . $year;
            $place_date_x = 2.1;
            $place_date_y = 2.5;

            $amount_x = 11.8;
            $amount_y = 2.5;

            $person_x = 2.4;
            $person_y = 3.4;

            $value_letters_x = 0.9;
            $value_letters_y = 4.25;

            $asterisks_x = 0.5;
            $asterisks_y = 4.95;

            $person_check = str_pad('**' . $person, 60, '*', STR_PAD_RIGHT);
            $letters_check = str_pad('**' . $value_letters, 68, '*', STR_PAD_RIGHT);
            $asterisks = str_pad('', 22, '*', STR_PAD_RIGHT);

            $flag_labels = 0;
            $show_check = 1;

            $entrie_width = 14.5;
            $entrie_left = 0.4;
            $entrie_top = 9.9;
            $show_table = 1;
        }

        $check_number = $transaction->check_number;

        $description = $transaction->description;

        $date_exp = explode('-', $transaction->date);

        $day = $date_exp[2];
        $month = $date_exp[1];
        $year = $date_exp[0];

        # ----- ENTRIE -----

        $business_name = mb_strtoupper($business->business_full_name);
        $accountant = mb_strtoupper($business->accountant);
        $enable_description_line = $business->enable_description_line_entries_report;

        $numero = 0;

        $entries = DB::table('accounting_entries as ae')
        ->leftJoin('type_entries as te', 'ae.type_entrie_id', 'te.id')
        ->select('ae.id', 'ae.correlative', 'ae.date', 'ae.description', 'te.name as type_entrie')
        ->where('ae.id', $transaction->accounting_entrie_id)
        ->orderBy('ae.correlative', 'asc')
        ->get();

        $entrie_details = DB::table('accounting_entries_details as detalle')
        ->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')
        ->join('accounting_entries as partida', 'partida.id', '=', 'detalle.entrie_id')
        ->select('detalle.entrie_id', 'detalle.account_id', 'detalle.debit', 'detalle.credit', 'detalle.description', 'cuenta.code', 'cuenta.name')
        ->where('partida.id', $transaction->accounting_entrie_id)

        ->orderBy('cuenta.code', 'asc')
        ->get();

        $grupos = array();
        $elementos = array();
        $detalles = array();


        $digits = $business->ledger_digits;

        foreach ($entrie_details as $detail) {

            $mayor = substr($detail->code, 0, $digits);
            $id_partida = $detail->entrie_id;

            if ($detail->debit != 0) {
                $columna = "D";
            }

            if ($detail->credit != 0) {
                $columna = "H";
            }

            $elemento_grupos = $columna . '.' . $id_partida . '.' . $mayor;

            if (!in_array($elemento_grupos, $grupos)) {

                array_push($grupos, $elemento_grupos);

                $debe = 0;
                $haber = 0;

                $cuenta = DB::table('catalogues')
                ->select('name')
                ->where('code', $mayor)
                ->first();

                $nombre = $cuenta->name;

                if (($id_partida == $detail->entrie_id)) {

                    $valor = $this->getHigherEntrieBalance($id_partida, $mayor);
                    $debe = $valor->debe;
                    $haber = $valor->haber;
                }

                $item_elemento = array(

                    'partida' => $id_partida,
                    'mayor' => $mayor,
                    'nombre' => $nombre,
                    'columna' => $columna,
                    'debe' => $debe,
                    'haber' => $haber,
                );

                array_push($elementos, $item_elemento);
            }

            $item_detalle = array(
                'entrie_id' => $detail->entrie_id,
                'debe' => $detail->debit,
                'haber' => $detail->credit,
                'mayor' => $mayor,
                'code' => $detail->code,
                'name' => $detail->name,
                'description' => $detail->description,
            );

            array_push($detalles, $item_detalle);
        }

        $elements = json_decode(json_encode($elementos), FALSE);
        $details = json_decode(json_encode($detalles), FALSE);
        $partidas = array();

        foreach ($entries as $entrie) {

            $grupos_debe = array();
            $grupos_haber = array();
            $total_debe = 0;
            $total_haber = 0;

            foreach ($elements as $elemento) {

                $items_debe = array();
                $items_haber = array();

                foreach ($details as $detalle) {

                    if (($entrie->id == $detalle->entrie_id) && ($elemento->partida == $detalle->entrie_id) && ($elemento->mayor == $detalle->mayor)) {

                        if ($detalle->debe != 0 && $elemento->columna == "D") {

                            $elemento_items_debe = array(


                                'code' => $detalle->code,
                                'name' => $detalle->name,
                                'valor' => $detalle->debe,
                                'description_line' => $detalle->description,
                            );

                            array_push($items_debe, $elemento_items_debe);
                        }

                        if ($detalle->haber != 0 && $elemento->columna == "H") {

                            $elemento_items_haber = array(
                                'code' => $detalle->code,
                                'name' => $detalle->name,
                                'valor' => $detalle->haber,
                                'description_line' => $detalle->description,
                            );

                            array_push($items_haber, $elemento_items_haber);
                        }
                    }
                }

                if (($entrie->id == $elemento->partida) && ($elemento->columna == "D")) {

                    $elemento_grupo_debe = array(
                        'mayor' => $elemento->mayor,
                        'nombre' => $elemento->nombre,
                        'debe' => $elemento->debe,
                        'items' => $items_debe,
                    );

                    array_push($grupos_debe, $elemento_grupo_debe);

                    $total_debe = $total_debe + $elemento->debe;
                }

                if (($entrie->id == $elemento->partida) && ($elemento->columna == "H")) {

                    $elemento_grupo_haber = array(

                        'mayor' => $elemento->mayor,
                        'nombre' => $elemento->nombre,
                        'haber' => $elemento->haber,
                        'items' => $items_haber,
                    );

                    array_push($grupos_haber, $elemento_grupo_haber);

                    $total_haber = $total_haber + $elemento->haber;
                }
            }

            $elemento_partidas = array(

                'id' => $entrie->id,
                'correlative' => $entrie->correlative,
                'date' => $entrie->date,
                'total_debe' => $total_debe,
                'total_haber' => $total_haber,
                'description' => $entrie->description,
                'grupos_debe' => $grupos_debe,
                'grupos_haber' => $grupos_haber,
                'accountant' => $accountant,
                'type_entrie' => $entrie->type_entrie,
            );

            array_push($partidas, $elemento_partidas);
        }

        $datos = json_decode(json_encode($partidas), FALSE);

        $pdf = \PDF::loadView('banks.receipts.check_2', compact(
            'place_date_x',
            'place_date_y',
            'place_date',
            'amount_x',
            'amount_y',
            'amount',
            'person_x',
            'person_y',
            'person',
            'person_check',
            'value_letters_x',
            'value_letters_y',
            'value_letters',
            'letters_check',
            'check_number',
            'description',
            'day',
            'month',
            'year',
            'enable_description_line',
            'datos',
            'numero',
            'business_name',
            'bank_name',
            'print',
            'entrie_type',
            'entrie_no',
            'asterisks_x',
            'asterisks_y',
            'asterisks',
            'format',
            'flag_labels',
            'show_check',
            'entrie_width',
            'entrie_top',
            'show_table', 
            'entrie_left'
        ));

        $pdf->setPaper('letter');

        return $pdf->stream('check.pdf');
    }

    /**
     * Get the balance of the entrie.
     * 
     * @param  int  $id
     * @param  string  $code
     * @return collect
     */
    protected function getHigherEntrieBalance($id, $code) {

        $valor = DB::table('accounting_entries_details as detalle')
        ->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')
        ->select(DB::raw('SUM(detalle.debit) debe, SUM(detalle.credit) haber'))
        ->where('detalle.entrie_id', $id)
        ->where('cuenta.code', 'like', '' . $code . '%')
        ->first();

        return $valor;
    }

    protected function unidad($numuero) {


        switch ($numuero)
        {
            case 9:
            {
                $numu = "NUEVE";
                break;
            }
            case 8:
            {
                $numu = "OCHO";
                break;
            }
            case 7:
            {
                $numu = "SIETE";
                break;
            }
            case 6:
            {
                $numu = "SEIS";
                break;
            }
            case 5:
            {
                $numu = "CINCO";
                break;
            }
            case 4:
            {
                $numu = "CUATRO";
                break;
            }
            case 3:
            {
                $numu = "TRES";
                break;
            }
            case 2:
            {
                $numu = "DOS";
                break;
            }
            case 1:
            {
                $numu = "UNO";
                break;
            }
            case 0:
            {
                $numu = "";
                break;
            }
        }
        return $numu;
    }

    protected function decena($numdero) {

        if ($numdero >= 90 && $numdero <= 99)
        {
            $numd = "NOVENTA ";
            if ($numdero > 90)
                $numd = $numd."Y ".($this->unidad($numdero - 90));
        }
        else if ($numdero >= 80 && $numdero <= 89)
        {
            $numd = "OCHENTA ";
            if ($numdero > 80)
                $numd = $numd."Y ".($this->unidad($numdero - 80));
        }
        else if ($numdero >= 70 && $numdero <= 79)
        {
            $numd = "SETENTA ";
            if ($numdero > 70)
                $numd = $numd."Y ".($this->unidad($numdero - 70));
        }
        else if ($numdero >= 60 && $numdero <= 69)
        {
            $numd = "SESENTA ";
            if ($numdero > 60)
                $numd = $numd."Y ".($this->unidad($numdero - 60));
        }
        else if ($numdero >= 50 && $numdero <= 59)
        {
            $numd = "CINCUENTA ";
            if ($numdero > 50)
                $numd = $numd."Y ".($this->unidad($numdero - 50));
        }
        else if ($numdero >= 40 && $numdero <= 49)
        {
            $numd = "CUARENTA ";
            if ($numdero > 40)
                $numd = $numd."Y ".($this->unidad($numdero - 40));
        }
        else if ($numdero >= 30 && $numdero <= 39)
        {
            $numd = "TREINTA ";
            if ($numdero > 30)
                $numd = $numd."Y ".($this->unidad($numdero - 30));
        }
        else if ($numdero >= 20 && $numdero <= 29)
        {
            if ($numdero == 20)
                $numd = "VEINTE ";
            else
                $numd = "VEINTI".($this->unidad($numdero - 20));
        }
        else if ($numdero >= 10 && $numdero <= 19)
        {
            switch ($numdero){
                case 10:
                {
                    $numd = "DIEZ ";
                    break;
                }
                case 11:
                {
                    $numd = "ONCE ";
                    break;
                }
                case 12:
                {
                    $numd = "DOCE ";
                    break;
                }
                case 13:
                {
                    $numd = "TRECE ";
                    break;
                }
                case 14:
                {
                    $numd = "CATORCE ";
                    break;
                }
                case 15:
                {
                    $numd = "QUINCE ";
                    break;
                }
                case 16:
                {
                    $numd = "DIECISEIS ";
                    break;
                }
                case 17:
                {
                    $numd = "DIECISIETE ";
                    break;
                }
                case 18:
                {
                    $numd = "DIECIOCHO ";
                    break;
                }
                case 19:
                {
                    $numd = "DIECINUEVE ";
                    break;
                }
            }
        }
        else
            $numd = $this->unidad($numdero);
        return $numd;
    }

    protected function centena($numc) {

        if ($numc >= 100)
        {
            if ($numc >= 900 && $numc <= 999)
            {
                $numce = "NOVECIENTOS ";
                if ($numc > 900)
                    $numce = $numce.($this->decena($numc - 900));
            }
            else if ($numc >= 800 && $numc <= 899)
            {
                $numce = "OCHOCIENTOS ";
                if ($numc > 800)
                    $numce = $numce.($this->decena($numc - 800));
            }
            else if ($numc >= 700 && $numc <= 799)
            {
                $numce = "SETECIENTOS ";
                if ($numc > 700)
                    $numce = $numce.($this->decena($numc - 700));
            }
            else if ($numc >= 600 && $numc <= 699)
            {
                $numce = "SEISCIENTOS ";
                if ($numc > 600)
                    $numce = $numce.($this->decena($numc - 600));
            }
            else if ($numc >= 500 && $numc <= 599)
            {
                $numce = "QUINIENTOS ";
                if ($numc > 500)
                    $numce = $numce.($this->decena($numc - 500));
            }
            else if ($numc >= 400 && $numc <= 499)
            {
                $numce = "CUATROCIENTOS ";
                if ($numc > 400)
                    $numce = $numce.($this->decena($numc - 400));
            }
            else if ($numc >= 300 && $numc <= 399)
            {
                $numce = "TRESCIENTOS ";
                if ($numc > 300)
                    $numce = $numce.($this->decena($numc - 300));
            }
            else if ($numc >= 200 && $numc <= 299)
            {
                $numce = "DOSCIENTOS ";
                if ($numc > 200)
                    $numce = $numce.($this->decena($numc - 200));
            }
            else if ($numc >= 100 && $numc <= 199)
            {
                if ($numc == 100)
                    $numce = "CIEN ";
                else
                    $numce = "CIENTO ".($this->decena($numc - 100));
            }
        }
        else
            $numce = $this->decena($numc);

        return $numce;
    }

    protected function miles($nummero){
        if ($nummero >= 1000 && $nummero < 2000){
            $numm = "MIL ".($this->centena($nummero%1000));
        }
        if ($nummero >= 2000 && $nummero <10000){
            $numm = $this->unidad(Floor($nummero/1000))." MIL ".($this->centena($nummero%1000));
        }
        if ($nummero < 1000)
            $numm = $this->centena($nummero);

        return $numm;
    }

    protected function decmiles($numdmero){
        if ($numdmero == 10000)
            $numde = "DIEZ MIL";
        if ($numdmero > 10000 && $numdmero <20000){
            $numde = $this->decena(Floor($numdmero/1000))."MIL ".($this->centena($numdmero%1000));
        }
        if ($numdmero >= 20000 && $numdmero <100000){
            $numde = $this->decena(Floor($numdmero/1000))." MIL ".($this->miles($numdmero%1000));
        }
        if ($numdmero < 10000)
            $numde = $this->miles($numdmero);

        return $numde;
    }

    protected function cienmiles($numcmero){
        if ($numcmero == 100000)
            $num_letracm = "CIEN MIL";
        if ($numcmero >= 100000 && $numcmero <1000000){
            $num_letracm = $this->centena(Floor($numcmero/1000))." MIL ".($this->centena($numcmero%1000));
        }
        if ($numcmero < 100000)
            $num_letracm = $this->decmiles($numcmero);
        return $num_letracm;
    }

    protected function millon($nummiero){
        if ($nummiero >= 1000000 && $nummiero <2000000){
            $num_letramm = "UN MILLON ".($this->cienmiles($nummiero%1000000));
        }
        if ($nummiero >= 2000000 && $nummiero <10000000){
            $num_letramm = $this->unidad(Floor($nummiero/1000000))." MILLONES ".($this->cienmiles($nummiero%1000000));
        }
        if ($nummiero < 1000000)
            $num_letramm = $this->cienmiles($nummiero);

        return $num_letramm;
    }

    protected function decmillon($numerodm){
        if ($numerodm == 10000000)
            $num_letradmm = "DIEZ MILLONES";
        if ($numerodm > 10000000 && $numerodm <20000000){
            $num_letradmm = $this->decena(Floor($numerodm/1000000))."MILLONES ".($this->cienmiles($numerodm%1000000));
        }
        if ($numerodm >= 20000000 && $numerodm <100000000){
            $num_letradmm = $this->decena(Floor($numerodm/1000000))." MILLONES ".($this->millon($numerodm%1000000));
        }
        if ($numerodm < 10000000)
            $num_letradmm = $this->millon($numerodm);

        return $num_letradmm;
    }

    protected function cienmillon($numcmeros){
        if ($numcmeros == 100000000)
            $num_letracms = "CIEN MILLONES";
        if ($numcmeros >= 100000000 && $numcmeros <1000000000){
            $num_letracms = $this->centena(Floor($numcmeros/1000000))." MILLONES ".($this->millon($numcmeros%1000000));
        }
        if ($numcmeros < 100000000)
            $num_letracms = $this->decmillon($numcmeros);
        return $num_letracms;
    }

    protected function milmillon($nummierod){
        if ($nummierod >= 1000000000 && $nummierod <2000000000){
            $num_letrammd = "MIL ".($this->cienmillon($nummierod%1000000000));
        }
        if ($nummierod >= 2000000000 && $nummierod <10000000000){
            $num_letrammd = $this->unidad(Floor($nummierod/1000000000))." MIL ".($this->cienmillon($nummierod%1000000000));
        }
        if ($nummierod < 1000000000)
            $num_letrammd = $this->cienmillon($nummierod);

        return $num_letrammd;
    }

    /**
     * Convert quantity to letters.
     * 
     * @param  string  $numero
     * @return string
     */
    protected function convertir($numero){
        $num = str_replace(",","",$numero);
        $num = number_format($num,2,'.','');
        $cents = substr($num,strlen($num)-2,strlen($num)-1);
        $num = (int)$num;
        $numf = $this->milmillon($num);

        return $numf . " " . $cents . "/100";
    }
}
