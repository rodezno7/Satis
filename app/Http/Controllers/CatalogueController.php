<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Catalogue;
use App\AccountingEntrie;
use App\BankAccount;
use App\Category;
use App\AccountingEntriesDetail;
use App\Business;
use App\Http\Requests\CatalogueRequest;
use App\Imports\CatalogueImport;
use DataTables;
use DB;
use Excel;
use Validator;

class CatalogueController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (!auth()->user()->can('catalogue')) {
            return redirect('home');
        }
        
        $business_id = request()->session()->get('user.business_id');

        $catalogue = Catalogue::select(DB::raw("CONCAT(code, ' ', name) AS full_name, id"))
        ->where('business_id', $business_id)
        ->orderBy('code', 'asc')
        ->get();

        $clasifications = Catalogue::select('code', DB::raw("CONCAT(code, ' ', name) as full_name"))
        ->where('business_id', $business_id)
        ->whereRaw('LENGTH(code) = 1')
        ->get();

        return view('catalogue.index', compact('catalogue', 'clasifications'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        if (!auth()->user()->can('catalogue')) {
            return redirect('home');
        }
        
        return view('catalogue.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CatalogueRequest $request) {

        if (!auth()->user()->can('catalogue')) {
            return redirect('home');
        }

        if($request->ajax()) {

            try {

                $data = $request->all();
                $data['business_id'] = request()->session()->get('user.business_id');

                $account = Catalogue::create($data);
                $output = [
                    'success' => true,
                    'msg' => __('accounting.added_successfully')
                ];

            } catch(\Exception $e){

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
     * Display the specified resource.
     *
     * @param  \App\Catalogue  $catalogue
     * @return \Illuminate\Http\Response
     */
    public function show(Catalogue $catalogue) {

        if (!auth()->user()->can('catalogue')) {
            return redirect('home');
        }

        return response()->json($catalogue);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Catalogue  $catalogue
     * @return \Illuminate\Http\Response
     */
    public function edit(Catalogue $catalogue) {

        if (!auth()->user()->can('catalogue')) {
            return redirect('home');
        }

        return response()->json($catalogue);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Catalogue  $catalogue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Catalogue $catalogue) {

        if (!auth()->user()->can('catalogue')) {
            return redirect('home');
        }
        
        $business_id = request()->session()->get('user.business_id');

        $id=$catalogue->id;

        $parent = $request->input('parent');

        $prefijo = Catalogue::select('code')
        ->where('id', $parent)
        ->first();

        $code = $request->input('code');

        $buscar = Catalogue::select('id')
        ->where('business_id', $business_id)
        ->where('code', 'like', ''.$code.'')
        ->where('id', '<>', $parent)
        ->first();

        if($prefijo != null) {

            $prefijo = $prefijo->code;
            $validateData = $request->validate(
                [

                    'code' => 'required|integer|regex:/^('.$prefijo.')/',
                    'name' => 'required',
                    'parent' => 'unique:accounting_entries_details,account_id',

                ]
            );

        } else {

            $validateData = $request->validate(
                [
                    'code' => 'required|integer',
                    'name' => 'required',
                    'parent' => 'unique:accounting_entries_details,account_id',

                ]
            );
        }

        if($request->ajax()) {

            try {

                DB::beginTransaction();

                $old_code = $catalogue->code;
                $old_type = $catalogue->type;
                
                $catalogue->code = $request->input('code');
                $catalogue->name = $request->input('name');
                $catalogue->parent = $request->input('parent');
                $catalogue->type = $request->input('type');
                $catalogue->status = $request->input('status');
                $catalogue->save();


                if($old_code != $request->input('code')) {

                    $accounts = Catalogue::where('code', 'like', ''.$old_code.'%')
                    ->where('business_id', $business_id)
                    ->orderBy('code', 'asc')
                    ->get();

                    $length = mb_strlen($old_code);

                    foreach ($accounts as $account) {

                        $sufixx = substr($account->code, $length);
                        $new_code = $request->input('code').$sufixx;
                        $account->code = $new_code;
                        $account->save();
                    }
                }

                if($old_type != $request->input('type')) {

                    $accounts = Catalogue::where('code', 'like', ''.$request->input('code').'%')
                    ->where('business_id', $business_id)
                    ->orderBy('code', 'asc')
                    ->get();

                    $length = mb_strlen($old_code);
                    foreach ($accounts as $account) {

                        $account->type = $request->input('type');
                        $account->save();
                    }
                }                

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('accounting.updated_successfully')
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
     * @param  \App\Catalogue  $catalogue
     * @return \Illuminate\Http\Response
     */
    public function destroy(Catalogue $catalogue) {

        if (!auth()->user()->can('catalogue')) {
            return redirect('home');
        }
        
        try {

            $catalogue->forceDelete();
            $output = [
                'success' => true,
                'msg' => __('accounting.deleted_successfully')
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

    //Adittional Functions
    public function verifyDeleteAccount($id) {

        $business_id = request()->session()->get('user.business_id');

        $account = Catalogue::where('id', $id)
        ->first();

        $first_son = Catalogue::where('parent', $account->id)
        ->first();

        $first_code_son = Catalogue::where('code', 'like', ''.$account->code.'%')
        ->where('business_id', $business_id)
        ->where('id', '<>', $account->id)
        ->first();

        $first_entrie_details = AccountingEntriesDetail::where('account_id', $account->id)
        ->first();

        $first_bank_account =  BankAccount::where('catalogue_id', $account->id)
        ->first()
        ;
        $first_categorie =  Category::where('catalogue_id', $account->id)
        ->first();

        if(($first_son == null) && ($first_code_son == null) && ($first_entrie_details == null) && ($first_bank_account == null) && ($first_categorie == null)) {

            $result = 0;
        } else {

            $result = 1;
        }
        
        
        $datos = array(
            'result' => $result,
            'first_son' => $first_son,
            'first_code_son' => $first_code_son,
            'first_entrie_details' => $first_entrie_details,
            'first_bank_account' => $first_bank_account,
            'first_categorie' => $first_categorie
        );

        return response()->json($datos);
    }

    public function getAccounts() {

        $business_id = request()->session()->get('user.business_id');

        $catalogue = Catalogue::select(DB::raw("CONCAT(code, ' ', name) AS full_name, id"))
        ->where('business_id', $business_id)
        ->where('status', 1)
        ->orderBy('code', 'asc')
        ->get();
        
        return response()->json($catalogue);
    }

    /**
     * Get accounts for select2 ajax request
     * @param string $q
     * @param string $main_account|null
     */
    public function getAccountsForSelect2() {

        if(request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $q = request()->input("q", "");
            $main_account = request()->input("main_account", "");

            $query = Catalogue::where("status", 1)
            ->where('business_id', $business_id)
            ->orderBy("code", "ASC");

            if(!empty($main_account)){
                $query->where(function($query) use ($main_account){
                    $query->where("code", "LIKE", "{$main_account}%");
                });
            }

            if(!empty($q)){
                $query->where(function($query) use ($q){
                    $query->where("code", "LIKE", "{$q}%")
                    ->orWhere("name", "LIKE", "%{$q}%");
                });
            }

            $catalogue = $query
            ->select(DB::raw("CONCAT(code, ' ', name) as text"), "id")
            ->get();

            return json_encode($catalogue);
        }
    }

    public function getAccountsParents(Catalogue $account) {

        $business_id = request()->session()->get('user.business_id');
        $code = $account->code;
        $id = $account->id;
        
        $catalogue = Catalogue::select(DB::raw("CONCAT(code, ' ', name) AS full_name, id"))
        ->where('business_id', $business_id)
        ->where('status', 1)
        ->where('code', 'not like', ''.$code.'%')
        ->where('id', '<>', $id)
        ->orderBy('code', 'asc')
        ->get();

        return response()->json($catalogue);

    }

    public function verifyCode(Catalogue $account, $newCode) {

        $business_id = request()->session()->get('user.business_id');

        $old_code_length = mb_strlen($account->code);

        $newCode_trim = substr($newCode, 0, $old_code_length);

        $find = Catalogue::select('id')
        ->where('code', 'like', ''.$newCode_trim.'%')
        ->where('business_id', $business_id)
        ->first();

        $valor = 0;
        
        if($account->code == $newCode) {

            $valor = 1;
        }

        if(mb_strlen($newCode) < $old_code_length) {

            $valor = 1;
        }

        if($find == null) {

            $valor = 1;
        }

        if($account->parent == 0) {

            $valor = 1;
        }

        $resultado = array(
            'valor' => $valor,
        );

        return $resultado;
    }

    public function verifyClasif($code) {

        $business_id = request()->session()->get('user.business_id');
        $code_trim = substr($code, 0, 1);
        
        $find = Catalogue::select('id', 'code')
        ->where('business_id', $business_id)
        ->where('code', 'like', ''.$code_trim.'%')
        ->first();

        if($find == null) {

            $valor = 1;
        } else {

            if($code == $find->code) {

                $valor = 1;
            } else {

                $valor = 0;
            }
        }

        $resultado = array(
            'valor' => $valor,
        );

        return $resultado;
    }

    public function getTree() {

        $business_id = request()->session()->get('user.business_id');

        $data = Catalogue::select('code', 'id', DB::raw("CONCAT(code, ' ', name) AS text"))
        ->where('business_id', $business_id)
        ->where('parent', 0)
        ->with(['children' => function($query) {
            return $query->select('id', 'code', DB::raw("CONCAT(code, ' ', name) AS text"), 'parent');
        }])
        ->orderBy('code', 'asc')
        ->get()
        ->toArray();

        return response()->json($data);
    }

    public function getInfoAccount($id, $date) {

        $business_id = request()->session()->get('user.business_id');

        $info = Catalogue::select('id', 'code', 'name', 'type', 'level')
        ->where('business_id', $business_id)
        ->where('id', $id)
        ->first();

        $id = $info->id;
        $code = $info->code;
        $name = $info->name;
        
        $balances = DB::table('accounting_entries_details as detail')
        ->join('catalogues as account', 'detail.account_id', '=', 'account.id')
        ->join('accounting_entries as entrie', 'detail.entrie_id', '=', 'entrie.id')
        ->select(DB::raw('SUM(detail.debit) debit, SUM(detail.credit) credit'))
        ->where('account.business_id', $business_id)
        ->where('account.code', 'like', ''.$code.'%')
        ->where('entrie.date', '<=', $date)
        ->where('entrie.status', 1)
        ->first();

        $type = $info->type;
        
        if ($info->type == 'debtor') {

            $balance_type = __('accounting.debtor');
            $balance = $balances->debit - $balances->credit;

        } else {

            $balance_type = __('accounting.creditor');
            $balance = $balances->credit - $balances->debit;
        }

        $len = strlen($info->code);
        
        if ($len == 1) {

            $size = $len + 1;
        } else {

            $size = $len + 2;
        }

        $max = DB::table('catalogues')
        ->where('business_id', $business_id)
        ->select(DB::raw('MAX(code) as max'))
        ->where('code', 'like', ''.$info->code.'%')
        ->whereRaw('LENGTH(code) = '.$size.'')
        ->first();

        if ($max->max == null) {

            if ($len == 1) {
                $next = "".$info->code."1";
            } else {
                $next = "".$info->code."01";
            }
            
        } else {
            $next = $max->max + 1;
        }

        $datos = array(

            "id" => $id,
            "code" => $code,
            "name" => $name,
            "balance" => $balance,
            "type" => $balance_type,
            "type_account" => $info->type,
            "correlative" => $next,
            'level' => $info->level + 1,
            'level_account' => $info->level,
        );

        return $datos;
    }

    public function getCatalogueData($id) {

        $business_id = request()->session()->get('user.business_id');

        if($id == 0) {

            $accounts = Catalogue::select('code', 'name', 'parent', 'type', 'level', 'status')
            ->where('business_id', $business_id)
            ->orderByRaw('CONVERT(code, CHAR) asc')
            ->get();

        } else {

            $accounts = Catalogue::select('code', 'name', 'parent', 'type', 'level', 'status')
            ->where('business_id', $business_id)
            ->where('code', 'like', ''.$id.'%')
            ->orderByRaw('CONVERT(code, CHAR) asc')
            ->get();
        }

        return DataTables::of($accounts)->toJson();
    }

    public function importCatalogue(Request $request) {

        $business_id = request()->session()->get('user.business_id');

        $validateData = $request->validate(
            [
                'catalogue_file' => 'required',
            ]
        );

        if($request->ajax()) {

            try {

                $file = $request->file('catalogue_file');

                $import = new CatalogueImport;
                Excel::import($import, $file);
                $accounts = json_decode(json_encode ($import->data), FALSE);

                DB::beginTransaction();

                $cuentas = array();

                
                if (count($accounts) > 1) {

                    foreach ($accounts as $key => $value) {

                        if ($key > 0) {

                            $index = $key + 1;
                            $code = trim($value[0]);
                            $name = trim($value[1]);
                            $parent = trim($value[2]);
                            $type = trim($value[3]);

                            if (intval($code) <= 0) {

                                $output = [
                                    'success' => false,
                                    'msg' => __('accounting.catalogue_error_3', ['row' => $index])
                                ];
                                DB::rollBack();
                                break;
                            }

                            if (intval($parent) < 0) {

                                $output = [
                                    'success' => false,
                                    'msg' => __('accounting.catalogue_error_4', ['row' => $index])
                                ];
                                DB::rollBack();
                                break;
                            }

                            if (($name == '') || ($type == '')) {

                                $output = [
                                    'success' => false,
                                    'msg' => __('accounting.catalogue_error_5', ['row' => $index])
                                ];
                                DB::rollBack();
                                break;
                            }

                            if(($type != 'D') && ($type != 'A')) {


                                $output = [
                                    'success' => false,
                                    'msg' => __('accounting.catalogue_error_6', ['row' => $index])
                                ];
                                DB::rollBack();
                                break;
                            }

                            if ($type == 'D') {

                                $balance_type = 'debtor';
                            } else {

                                $balance_type = 'creditor';

                            }

                            $code_len = mb_strlen($code);

                            if(($code_len == 1) && (intval($parent) > 0)) {

                                $output = [
                                    'success' => false,
                                    'msg' => __('accounting.catalogue_error_7', ['row' => $index])
                                ];
                                DB::rollBack();
                                break;
                                
                            }

                            if(($code_len > 1) && (intval($parent) == 0)) {

                                $output = [
                                    'success' => false,
                                    'msg' => __('accounting.catalogue_error_8', ['row' => $index])
                                ];
                                DB::rollBack();
                                break;
                            }

                            $accounts_with_same_code = DB::table('catalogues as account')
                            ->where('business_id', $business_id)
                            ->where('account.code', $code)
                            ->count();

                            if ($accounts_with_same_code > 0) {

                                $output = [
                                    'success' => false,
                                    'msg' => __('accounting.catalogue_error_9', ['row' => $index])
                                ];
                                DB::rollBack();
                                break;
                            }

                            if(intval($parent) == 0) {


                                $accounts_with_prefix = DB::table('catalogues as account')
                                ->where('business_id', $business_id)
                                ->where('account.code', 'like', ''.$code.'%')
                                ->count();

                                if ($accounts_with_prefix > 0) {

                                    $output = [
                                        'success' => false,
                                        'msg' => __('accounting.catalogue_error_10', ['row' => $index])
                                    ];
                                    DB::rollBack();
                                    break;

                                } else {

                                    $account_details['code'] = $code;
                                    $account_details['name'] = $name;
                                    $account_details['parent'] = $parent;
                                    $account_details['type'] = $balance_type;
                                    $account_details['level'] = 1;
                                    $account_details['status'] = 1;
                                    $account_details['business_id'] = $business_id;

                                    $account = Catalogue::create($account_details);

                                    $output = [
                                        'success' => true,
                                        'msg' => __('accounting.catalogue_loaded_successfully')
                                    ];
                                }

                            } else {


                                $account_q = DB::table('catalogues as account')
                                ->where('business_id', $business_id)
                                ->select('account.*')
                                ->where('account.code', $parent)
                                ->first();

                                if (!$account_q) {


                                    $output = [
                                        'success' => false,
                                        'msg' => __('accounting.catalogue_error_11', ['row' => $index])
                                    ];

                                    DB::rollBack();
                                    
                                    break;

                                } else {


                                    $parent_len_code = mb_strlen($parent);
                                    $prefix_child_code = substr($code, 0, $parent_len_code);

                                    if ($prefix_child_code == $parent) {


                                        $account_details['code'] = $code;
                                        $account_details['name'] = $name;
                                        $account_details['parent'] = $account_q->id;
                                        $account_details['type'] = $balance_type;
                                        $account_details['level'] = $account_q->level + 1;
                                        $account_details['status'] = 1;
                                        $account_details['business_id'] = $business_id;

                                        $account = Catalogue::create($account_details);

                                        $output = [
                                            'success' => true,
                                            'msg' => __('accounting.catalogue_loaded_successfully')
                                        ];

                                    } else {

                                        $output = [
                                            'success' => false,
                                            'msg' => __('accounting.catalogue_error_13', ['row' => $index])
                                        ];
                                        DB::rollBack();
                                        break;
                                    }

                                }

                            }
                        }
                    }

                    DB::commit();

                } else {

                    $output = [
                        'success' => false,
                        'msg' => __('accounting.empty_file')
                    ];
                }

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
}

