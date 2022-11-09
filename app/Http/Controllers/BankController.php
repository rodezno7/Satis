<?php

namespace App\Http\Controllers;

use App\Bank;
use App\BankAccount;
use App\TypeBankTransaction;
use App\BankCheckbook;
use App\BankTransaction;
use App\Contact;
use App\Business;
use App\BusinessLocation;
use App\Catalogue;
use App\TypeEntrie;
use App\Shortcut;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;

use DataTables;
use DB;
use Validator;

class BankController extends Controller {

    protected $transactionUtil;

    /**
     * Constructor.
     * 
     * @param TransactionUtil $transactionUtil;
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

        $business_id = request()->session()->get('user.business_id');

        $contacts = Contact::select('id', 'supplier_business_name as name')
        ->where('business_id', $business_id)
        ->get();

        $accounts = Catalogue::with('padre')
        ->where('business_id', $business_id)
        ->where('status', 1)
        ->whereNOTIn('id', [DB::raw("select parent from catalogues")])
        ->orderBy('code', 'asc')
        ->get();

        $periods = DB::table('accounting_periods')
        ->join('fiscal_years', 'fiscal_years.id', '=', 'accounting_periods.fiscal_year_id')
        ->select('accounting_periods.*')
        ->where('accounting_periods.business_id', $business_id)
        ->where('status', 1)
        ->orderBy('fiscal_years.year', 'desc')
        ->orderBy('accounting_periods.month', 'desc')
        ->get();

        $periods_filter = DB::table('accounting_periods')
        ->join('fiscal_years', 'fiscal_years.id', '=', 'accounting_periods.fiscal_year_id')
        ->where('accounting_periods.business_id', $business_id)
        ->select('accounting_periods.*')
        ->orderBy('fiscal_years.year', 'desc')
        ->orderBy('accounting_periods.month', 'desc')
        ->get();

        $checkbooks = BankCheckbook::select('id', 'name')
        ->where('business_id', $business_id)
        ->where('status', 1)
        ->get();

        $types = TypeEntrie::pluck('name', 'id');
        $shortcuts = Shortcut::get();

        $configuration = Business::select('accounting_bank_id')->where('id', $business_id)->first();
        
        if ($configuration != null) {

            $bank_account = Catalogue::select('code')
            ->where('id', $configuration->accounting_bank_id)
            ->first();

            if ($bank_account != null) {
                
                $banks = Catalogue::select('id', DB::raw('CONCAT(code, " ", name) as full_name'))
                ->where('business_id', $business_id)
                ->where('code', 'like', '' . $bank_account->code . '%')
                ->where('code', '<>', $bank_account->code)
                ->pluck('full_name', 'id');

            } else {

                $banks = Catalogue::select('id', DB::raw('CONCAT(code, " ", name) as full_name'))
                ->where('business_id', $business_id)
                ->pluck('full_name', 'id');

            }

        } else {

            $banks = Catalogue::select('id', DB::raw('CONCAT(code, " ", name) as full_name'))
            ->where('business_id', $business_id)
            ->pluck('full_name', 'id');
        }


        $banks_ddl = Bank::select('name', 'id')
        ->get();
        
        $bank_accounts_ddl = BankAccount::select('name', 'id')
        ->where('business_id', $business_id)
        ->get();

        $bank_transaction_types_ddl = TypeBankTransaction::select('name', 'id')
        ->get();

        $business_locations_ddl = BusinessLocation::select('name', 'id')
        ->where('business_id', $business_id)
        ->get();
        
        $business = Business::where('id', $business_id)->first();

        $checkbook_formats = $this->transactionUtil->checkbook_formats();

        return view('banks.index', compact(
            'contacts',
            'accounts',
            'periods',
            'business_locations_ddl',
            'types',
            'banks',
            'banks_ddl',
            'bank_accounts_ddl',
            'bank_transaction_types_ddl',
            'periods_filter',
            'checkbooks',
            'business',
            'shortcuts',
            'checkbook_formats'
        ));
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

        $validateData = $request->validate(
            [
                'name' => 'required|unique:banks',
                'print_format' => 'required',
            ],
            [
                'name.required' => __('accounting.name_required'),
                'name.unique' => __('accounting.name_unique'),
                'print_format.required' => __('accounting.print_format_required'),
            ]
        );

        if ($request->ajax()) {

            $business_id = auth()->user()->business_id;
            $bank = new Bank();
            $bank->name = trim($request->name);
            $bank->print_format = $request->print_format;
            $bank->business_id = $business_id;
            $bank->save();

            return response()->json([
                "msj" => 'Created'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank) {

        return response()->json($bank);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\bank  $bank
     * @return \Illuminate\Http\Response
     */
    
    public function edit(Bank $bank) {

        return response()->json($bank);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank) {

        $id = $bank->id;
        $validateData = $request->validate(
            [
                'name' => 'required|unique:banks,name,' . $id,
                'print_format' => 'required',
            ],
            [
                'name.required' => __('accounting.name_required'),
                'name.unique' => __('accounting.name_unique'),
                'print_format.required' => __('accounting.print_format_required'),
            ]
        );

        if ($request->ajax()) {

            $bank->update($request->all());
            
            return response()->json([
                "msj" => 'Updated'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(bank $bank) {

        if (request()->ajax()) {
            
            try {

                $bankAccounts = BankAccount::where('bank_id', $bank->id)->count();

                if ($bankAccounts > 0) {
                    
                    $output = [
                        'success' => false,
                        'msg' =>  __('accounting.bank_has_accounts')
                    ];

                } else {

                    $bank->forceDelete();
                    
                    $output = [
                        'success' => true,
                        'msg' => __('accounting.bank_deleted')
                    ];
                }

            } catch (\Exception $e) {

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    public function getBanksData() {

        $banks = DB::table('banks as bank')
        ->select('bank.*');
        
        return DataTables::of($banks)->toJson();
    }

    public function getBanks() {

        $banks = Bank::select('id', 'name')->get();
        
        return response()->json($banks);
    }

    public function getCheckNumber($id) {

        $checkbook = BankCheckbook::findOrFail($id);
        $actual = $checkbook->actual_correlative;

        $output = [
            'number' => $actual
        ];

        return response()->json($output);
    }

    /**
     * Get bank account from bank
     * @param int $bank_id
     * @return json
     */
    public function getBankAccounts($bank_id) {

        $bank_accounts = BankAccount::where('bank_id', $bank_id)
        ->select('id', 'name')
        ->get();
        
        return response()->json($bank_accounts);
    }
}
