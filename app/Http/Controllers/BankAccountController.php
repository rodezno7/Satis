<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\BankTransaction;
use Illuminate\Http\Request;

use DataTables;
use DB;
use Validator;

class BankAccountController extends Controller
{
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

        $validateData = $request->validate(
            [
                'bank_id' => 'required',
                'catalogue_id' => 'required',
                'name' => 'required',
                'type' => 'required',
                'number' => 'required',
            ],
            [
                'bank_id.required' => __('accounting.bank_required'),
                'catalogue_id.required' => __('accounting.catalogue_required'),
                'name.required' => __('accounting.name_required'),
                'type.required' => __('accounting.type_required'),
                'number.required' => __('accounting.number_required'),
            ]
        );

        if($request->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $data = $request->all();
            $data['business_id'] = $business_id;

            $bank_account = BankAccount::create($data);

            return response()->json([
                "msj" => 'Created'
            ]);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount) {

        return response()->json($bankAccount);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bankAccount) {

        return response()->json($bankAccount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount) {

        $id=$bankAccount->id;
        $validateData = $request->validate(
            [
                'bank_id' => 'required',
                'catalogue_id' => 'required',
                'name' => 'required|unique:bank_accounts,name,'.$id,
                'type' => 'required',
                'number' => 'required',
            ],
            [
                'bank_id.required' => __('accounting.bank_required'),
                'catalogue_id.required' => __('accounting.catalogue_required'),
                'name.required' => __('accounting.name_required'),
                'name.unique' => __('accounting.name_unique'),
                'description.required' => __('accounting.description_required'),
                'type.required' => __('accounting.type_required'),
                'number.required' => __('accounting.number_required'),
            ]
        );
        
        if($request->ajax()) {

            $bankAccount->bank_id = $request->input('bank_id');
            $bankAccount->name = $request->input('name');
            $bankAccount->description = $request->input('description');
            $bankAccount->type = $request->input('type');
            $bankAccount->number = $request->input('number');
            $bankAccount->save();
            
            return response()->json([
                "msj" => 'updated'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount) {

        if (request()->ajax()) {
            
            try {

                $bankTransactions = BankTransaction::where('bank_account_id', $bankAccount->id)->count();

                if($bankTransactions > 0) {

                    $output = [
                        'success' => false,
                        'msg' =>  __('accounting.bank_account_has_transactions')
                    ];
                
                } else {

                    $bankAccount->forceDelete();
                    
                    $output = [
                        'success' => true,
                        'msg' => __('accounting.bank_account_deleted')
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

    public function getBankAccountsData() {

        $business_id = request()->session()->get('user.business_id');

        $bankAccounts = DB::table('bank_accounts as account')
        ->leftJoin('catalogues as catalogue', 'catalogue.id', '=', 'account.catalogue_id')
        ->leftJoin('banks as bank', 'bank.id', '=', 'account.bank_id')
        ->select('account.*', 'bank.name as bank_name', 'catalogue.code as catalogue_code')
        ->where('account.business_id', $business_id);
        
        return DataTables::of($bankAccounts)->toJson();
    }

    public function getBankAccounts() {

        $business_id = request()->session()->get('user.business_id');

        $bankAccounts = BankAccount::select('id', 'name', 'catalogue_id')
        ->where('business_id', $business_id)
        ->get();
        
        return response()->json($bankAccounts);
    }

    public function getBankAccountsById($id) {

        $business_id = request()->session()->get('user.business_id');

        $bankAccounts = BankAccount::select('id', 'name', 'catalogue_id')
        ->where('business_id', $business_id)
        ->where('bank_id', $id)
        ->where('type', 'Corriente')
        ->orWhere('type', 'Checking')
        ->get();

        return response()->json($bankAccounts);
    }
}
