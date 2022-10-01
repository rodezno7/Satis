<?php

namespace App\Http\Controllers;

use App\BankCheckbook;
use App\BankTransaction;
use Illuminate\Http\Request;
use DB;
use DataTables;

class BankCheckbookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('banks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('banks.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate(
            [
                'name' => 'required|unique:bank_checkbooks',
                'serie' => 'required',
                'initial_correlative' => 'required|integer',
                'final_correlative' => 'required|integer',
                'actual_correlative' => 'required|integer',
                'bank_account_id' => 'required'
            ],
            [
                'name.required' => __('accounting.name_required'),
                'name.unique' => __('accounting.name_unique'),
                'serie.required' => __('accounting.serie_required'),

                'initial_correlative.required' => __('accounting.initial_correlative_required'),
                'initial_correlative.integer' => __('accounting.initial_correlative_integer'),

                'final_correlative.required' => __('accounting.final_correlative_required'),
                'final_correlative.integer' => __('accounting.final_correlative_integer'),

                'actual_correlative.required' => __('accounting.actual_correlative_required'),
                'actual_correlative.integer' => __('accounting.actual_correlative_integer'),

                'bank_account_id.required' => __('accounting.account_required'),
            ]
        );

        if ($request->ajax()) {
            try {
                $checkbook = BankCheckbook::create($request->all());

                $output = [
                    'success' => true,
                    'msg' => __('accounting.added_successfully')
                ];

            } catch(\Exception $e){
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
     * @param  \App\BankCheckbook  $bankCheckbook
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bankCheckbook = BankCheckbook::findOrFail($id);
        return response()->json($bankCheckbook);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankCheckbook  $bankCheckbook
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bankCheckbook = DB::table('bank_checkbooks as checkbook')
        ->join('bank_accounts as account', 'account.id', '=', 'checkbook.bank_account_id')
        ->join('banks as bank', 'bank.id', '=', 'account.bank_id')
        ->select('checkbook.*', 'bank.id as bank')
        ->where('checkbook.id', $id)
        ->first();
        return response()->json($bankCheckbook);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BankCheckbook  $bankCheckbook
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bankCheckbook = BankCheckbook::findOrFail($id);
        $validateData = $request->validate(
            [
                'name' => 'required|unique:bank_checkbooks,name,'.$bankCheckbook->id,
                'description' => 'required',
                'serie' => 'required',
                'initial_correlative' => 'required|integer',
                'final_correlative' => 'required|integer',
                'actual_correlative' => 'required|integer',
                'bank_account_id' => 'required'
            ],
            [
                'name.required' => __('accounting.name_required'),
                'name.unique' => __('accounting.name_unique'),
                'description.required' => __('accounting.description_required'),
                'serie.required' => __('accounting.serie_required'),
                'initial_correlative.required' => __('accounting.initial_correlative_required'),
                'final_correlative.required' => __('accounting.final_correlative_required'),
                'initial_correlative.integer' => __('accounting.initial_correlative_integer'),
                'final_correlative.integer' => __('accounting.final_correlative_integer'),

                'actual_correlative.required' => __('accounting.actual_correlative_required'),
                'actual_correlative.integer' => __('accounting.actual_correlative_integer'),

                'bank_account_id.required' => __('accounting.account_required'),
            ]
        );

        if ($request->ajax()) {
            try {
                $bankCheckbook->update($request->all());

                $output = [
                    'success' => true,
                    'msg' => __('accounting.updated_successfully')
                ];

            } catch(\Exception $e){
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
     * Remove the specified resource from storage.
     *
     * @param  \App\BankCheckbook  $bankCheckbook
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bankCheckbook = BankCheckbook::findOrFail($id);
        if (request()->ajax()) {
            try{

                $bankTransactions = BankTransaction::where('bank_checkbook_id', $bankCheckbook->id)->count();

                if($bankTransactions > 0){
                    $output = [
                        'success' => false,
                        'msg' =>  __('accounting.checkbook_has_transactions')
                    ];
                }
                else{
                    $bankCheckbook->forceDelete();
                    $output = [
                        'success' => true,
                        'msg' => __('accounting.deleted_successfully')
                    ];
                }
            }
            catch (\Exception $e){
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    public function getBankCheckbooksData()
    {
        $checkbooks = DB::table('bank_checkbooks as checkbook')
        ->join('bank_accounts as account', 'account.id', '=', 'checkbook.bank_account_id')
        ->select('checkbook.*', 'account.name as account_name');
        
        return DataTables::of($checkbooks)->toJson();
    }

    public function getBankCheckbooks($id)
    {
        $checkbooks = DB::table('bank_checkbooks as checkbook')
        ->select('id', 'name')
        ->where('bank_account_id', $id)
        ->where('status', 1)
        ->get();
        return response()->json($checkbooks);
    }

    public function validateNumber($id, $number)
    {

        $count = BankTransaction::where('bank_checkbook_id', $id)
        ->where('check_number', $number)->count();

        if ($count > 0) {
            $result = false;
        }
        else
        {
            $result = true;
        }
        $output = [
            'success' => $result
        ];
        return $output;
    }

    public function validateRange($id, $number)
    {
        $checkbook = BankCheckbook::where('id', $id)->first();
        if ($checkbook != null) {
            $checkbook_initial = $checkbook->initial_correlative;
            $checkbook_final = $checkbook->final_correlative;
        }
        else {
            $checkbook_initial = 0;
            $checkbook_final = 0;
        }
        if(($number < $checkbook_initial) || ($number > $checkbook_final)){
            $output = [
                'success' => false,
                'msg' => __("accounting.check_number_invalid")
            ];
        }
        else {
            $output = [
                'success' => true,
                'msg' => 'OK'
            ];
        }
        return $output;
    }
}
