<?php

namespace App\Http\Controllers;

use App\AccountingEntrie;
use Illuminate\Http\Request;
use App\Catalogue;
use App\Shortcut;
use App\AccountingEntriesDetail;
use App\BankAccount;
use App\TypeBankTransaction;
use App\TypeEntrie;
use App\BankTransaction;
use App\AccountingPeriod;
use App\Business;
use App\Contact;
use App\FiscalYear;
use App\BusinessLocation;
use DataTables;
use DB;
use Validator;
use Carbon\Carbon;

class AccountingEntrieController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (!auth()->user()->can('entries')) {
            return redirect('home');
        }

        $business_id = request()->session()->get('user.business_id');

        $accounts = Catalogue::with('padre')
        ->where('business_id', $business_id)
        ->where('status', 1)
        ->whereNOTIn('id', [DB::raw("select parent from catalogues")])
        ->orderBy('code', 'asc')
        ->get();

        $months = AccountingPeriod::join('fiscal_years', 'fiscal_years.id', 'accounting_periods.fiscal_year_id')
        ->where('accounting_periods.business_id', $business_id)
        ->where('status', 1)        
        ->orderBy('fiscal_years.year', 'desc')
        ->orderBy('accounting_periods.month', 'desc')
        ->pluck('accounting_periods.name', 'accounting_periods.id');

        $years = FiscalYear::where('business_id', $business_id)
        ->pluck('year', 'id');

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
        ->select('accounting_periods.*')
        ->where('accounting_periods.business_id', $business_id)
        ->orderBy('fiscal_years.year', 'desc')
        ->orderBy('accounting_periods.month', 'desc')
        ->get();

        $types = DB::table('type_entries')
        ->select('type_entries.*')
        ->get();

        $shortcuts = Shortcut::get();

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::select('name', 'id')
        ->where('business_id', $business_id)
        ->get();

        $business_locations_filter = BusinessLocation::where('business_id', $business_id)
        ->pluck('name', 'id');

        $business_numeration_entries = Business::select('entries_numeration_mode')
        ->where('id', $business_id)
        ->first();
        
        $business = Business::where('id', $business_id)
        ->first();

        $bank_accounts_ddl = BankAccount::select('name', 'id')
        ->where('business_id', $business_id)
        ->get();

        $bank_transaction_types_ddl = TypeBankTransaction::select('name', 'id')
        ->get();

        $business_locations_ddl = BusinessLocation::select('name', 'id')
        ->where('business_id', $business_id)
        ->get();

        $contacts = Contact::select('id', 'name')
        ->where('business_id', $business_id)
        ->get();

        return view('entries.index', compact('contacts', 'accounts', 'periods', 'business_locations', 'business_locations_filter', 'business_numeration_entries', 'bank_accounts_ddl', 'bank_transaction_types_ddl', 'business_locations_ddl', 'periods_filter', 'types', 'business', 'shortcuts', 'months', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        if(!auth()->user()->can('entries.create')) {
            return redirect('home');
        }

        return view('entries.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if(!auth()->user()->can('entries.create')) {
            return redirect('home');
        }

        $fecha = $request->input('date');
        $concepto = $request->input('description');
        $cuenta = $request->input('account_id');
        $account_id = $request->input('account_id');
        $description = $request->input('description_line');
        $debe = $request->input('debe');
        $haber = $request->input('haber');
        $variable = $request->input('total_debe');
        $period_id = $request->input('period_id');
        $number = $request->input('number');
        $type_entrie_id = $request->input('type_entrie_id');
        $business_location_id = $request->input('business_location_id');

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        if ($business->allow_uneven_totals_entries == 1) {
            $validateData = $request->validate(
                [
                    'date' => 'required|date',
                    'description' => 'required',
                    'period_id' => 'required',
                    'number' => 'required|integer',
                    'debe.*' => ['required', 'numeric'],
                    'haber.*' => ['required', 'numeric'],
                    'total_debe' => 'required|numeric',
                    'total_haber' => 'required|numeric',
                    'haber.*' => ['different:debe.*'],
                    'type_entrie_id' => 'required',
                    'business_location_id' => 'required',
                ]
            );

        } else {

            $validateData = $request->validate(
                [
                    'date' => 'required|date',
                    'description' => 'required',
                    'period_id' => 'required',
                    'number' => 'required|integer',
                    'debe.*' => ['required', 'numeric'],
                    'haber.*' => ['required', 'numeric'],
                    'total_debe' => 'required|numeric',
                    'total_haber' => 'required|numeric|in:'.$variable,
                    'haber.*' => ['different:debe.*'],
                    'type_entrie_id' => 'required',
                    'business_location_id' => 'required',
                ]
            );

        }
        
        if($request->ajax()) {

            try {

                $period = DB::table('accounting_periods as period')
                ->join('fiscal_years as year', 'year.id', '=', 'period.fiscal_year_id')
                ->select('year.year', 'period.month')
                ->where('period.id', $period_id)
                ->where('period.business_id', $business_id)
                ->first();

                $date = Carbon::parse($fecha);
                $mdate = $date->month;
                $ydate = $date->year;

                if($period->year != $ydate) {

                    $output = [
                        'success' => false,
                        'msg' => __("accounting.period_invalid")
                    ];

                } else {

                    if($period->month != $mdate) {

                        $output = [
                            'success' => false,
                            'msg' => __("accounting.period_invalid")
                        ];

                    } else {

                        DB::beginTransaction();
                        
                        $entrie = new AccountingEntrie;
                        $entrie->date = $fecha;
                        $entrie->number = $number;
                        $entrie->correlative = $number;
                        $entrie->business_id = $business_id;

                        $short_name_cont = str_pad($number, 5, "0", STR_PAD_LEFT);
                        $type_q = TypeEntrie::where('id', $type_entrie_id)->first();
                        $short_name_type = $type_q->short_name;

                        if($mdate < 10) {

                            $short_name_month = '0'.$mdate;

                        } else {

                            $short_name_month = $mdate;

                        }
                        
                        $short_name_year = $ydate;
                        $short_name_full = $short_name_type.'-'.$short_name_year.$short_name_month.'-'.$short_name_cont;

                        $entrie->accounting_period_id = $period_id;
                        $entrie->description = $concepto;
                        $entrie->type_entrie_id = $type_entrie_id;
                        $entrie->business_location_id = $business_location_id;
                        $entrie->short_name = $short_name_full;

                        if ($business->enable_validation_entries == 1) {

                            $entrie->status = 0;

                        } else {

                            $entrie->status = 1;                           
                            
                        }

                        $entrie->save();
                        $cont = 0;                
                        
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

                        DB::commit();
                        
                        $output = [
                            'success' => true,
                            'msg' => __("accounting.entrie_added")
                        ];
                    }
                }
                
            } catch(\Exception $e){

                DB::rollback();
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
     * @param  \App\AccountingEntrie  $accountingEntrie
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        if(!auth()->user()->can('entries.view')) {
            return redirect('home');
        }

        $entrie = AccountingEntrie::where('id', $id)->first();

        return response()->json($entrie);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AccountingEntrie  $accountingEntrie
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        if(!auth()->user()->can('entries.update')) {
            return redirect('home');
        }
        
        $entrie = AccountingEntrie::where('id', $id)->first();
        return response()->json($entrie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AccountingEntrie  $accountingEntrie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        if(!auth()->user()->can('entries.update')) {
            return redirect('home');
        }

        $entrie = AccountingEntrie::find($id);
        
        $validateData = $request->validate(
            [
                'date' => 'required|date',
                'description' => 'required',
            ],
            [
                'date.required' => 'La fecha es requerida',
                'date.date' => 'El formato de la fecha no es correcto',
                'description.required' => 'La descripción es requerida',
            ]);
        
        if($request->ajax()) {

            $entrie->fill($request->all());
            $entrie->save();
            
            return response()->json([
                "mensaje" => 'Actualizado'
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AccountingEntrie  $accountingEntrie
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        if(!auth()->user()->can('entries.delete')) {
            return redirect('home');
        }

        $entrie = AccountingEntrie::findOrFail($id);
        
        try {

            $count = BankTransaction::where('accounting_entrie_id', $entrie->id)
            ->count();
            
            if($count > 0) {

                $output = [
                    'success' => false,
                    'msg' => __("accounting.entrie_has_transaction")
                ];

            } else {

                $entrie->forceDelete();
                $output = [
                    'success' => true,
                    'msg' => __("accounting.entrie_deleted")
                ];
            }

        } catch (\Exception $e) {

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];

        }
        
        return $output;        
    }

    //Additional Functions
    public function search($id) {

        $cuenta = Catalogue::where('id', $id)
        ->first();

        if ($cuenta == null) {

            $datos = array(
                'id' => 'nothing',
                'code' => 'nothing',
                'name' => 'nothing',
                'parent' => 'nothing',
                'status' => 'nothing'
            );
            return response()->json($datos);

        } else {

            $datos = array(
                'id' => $cuenta->id,
                'code' => $cuenta->code,
                'name' => $cuenta->name,
                'parent' => $cuenta->parent,
                'status' => $cuenta->status                
            );

            return response()->json($datos);
        }
    }

    public function getEntries($type, $location, $period) {

        $business_id = request()->session()->get('user.business_id');

        $entries = DB::table('accounting_entries as entrie')
        ->leftJoin('accounting_periods as period', 'period.id', '=', 'entrie.accounting_period_id')
        ->leftJoin('business_locations as location', 'location.id', '=', 'entrie.business_location_id')
        ->leftJoin('type_entries as type', 'type.id', '=', 'entrie.type_entrie_id')
        ->leftJoin('bank_transactions as bank_transaction', 'bank_transaction.accounting_entrie_id', '=', 'entrie.id')
        ->select('entrie.*', 'period.name as period_name', 'location.name as name_location', 'type.name as name_type')
        ->where('entrie.business_id', $business_id);
        

        if($type != 0) {

            $entries->where('type_entrie_id', $type);
        }

        if($location != 0) {

            $entries->where('business_location_id', $location);
        }

        if($period != 0) {

            $entries->where('accounting_period_id', $period);
        }

        
        return DataTables::of($entries)->toJson();
    }

    public function getDetails($id) {

        $detalles = DB::table('accounting_entries_details as detalle')
        ->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')        
        ->select(['detalle.id', 'detalle.entrie_id', 'detalle.account_id', 'detalle.debit', 'detalle.credit', 'detalle.description', 'cuenta.code', 'cuenta.name'])
        ->where('detalle.entrie_id', $id)
        ->orderBy('detalle.id', 'asc')
        ->get();

        return response()->json($detalles);

    }

    public function cloneEntrie($id) {


        try {

            $business_id = request()->session()->get('user.business_id');

            $entrie_q = AccountingEntrie::findOrFail($id);
            
            $details = DB::table('accounting_entries_details as aed')
            ->join('catalogues as c', 'c.id', '=', 'aed.account_id')
            ->select('c.id', 'c.code', 'c.name', 'aed.debit', 'aed.credit', 'aed.description')
            ->where('aed.entrie_id', $id)
            ->orderBy('aed.id', 'ASC')
            ->get();
            
            $date = Carbon::now();
            $mdate = $date->month;
            $ydate = $date->year;

            $accounting_period = DB::table('accounting_periods as ap')
            ->select('ap.*')
            ->join('fiscal_years as fy', 'fy.id', '=', 'ap.fiscal_year_id')
            ->where('fy.year', $ydate)
            ->where('ap.month', $mdate)
            ->where('ap.business_id', $business_id)
            ->first();

            $entrie = array(

                'date' => $date->format('Y-m-d'),
                'description' => $entrie_q->description,
                'accounting_period_id' => $accounting_period->id,
                'type_entrie_id' => $entrie_q->type_entrie_id,
                'business_location_id' => $entrie_q->business_location_id,
                'details' => $details
            );

            $output = [
                'success' => true,
                'data' => $entrie
            ];

        } catch(\Exception $e) {

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];

        }

        return $output;

        

    }

    public function getPeriods() {

        $business_id = request()->session()->get('user.business_id');

        $periods = AccountingPeriod::select('id', 'name')
        ->where('business_id', $business_id)
        ->where('status', 1)
        ->get();
        
        return response()->json($periods);
    }

    public function createPeriod() {

        $date = Carbon::now();
        $mdate = $date->month;
        $ydate = $date->year;

        $business_id = request()->session()->get('user.business_id');

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

        $month = $months[($mdate) - 1];

        try {

            DB::beginTransaction();

            $accounting_period = DB::table('accounting_periods as ap')
            ->select('ap.*')
            ->join('fiscal_years as fy', 'fy.id', '=', 'ap.fiscal_year_id')
            ->where('fy.year', $ydate)
            ->where('ap.month', $mdate)
            ->where('ap.status', 0)
            ->where('ap.business_id', $business_id)
            ->first();

            if($accounting_period) {

                $period = AccountingPeriod::findOrFail($accounting_period->id);
                $period->status = 1;
                $period->save();

            } else {

                $fiscalYear = FiscalYear::firstOrCreate([
                    'year' => $ydate,
                    'business_id' => $business_id
                ]);

                $accounting_period = AccountingPeriod::firstOrCreate([
                    'name' => $month.'/'.$ydate,
                    'fiscal_year_id' => $fiscalYear->id,
                    'month' => $mdate,
                    'status' => '1',
                    'business_id' => $business_id
                ]);

            }            

            DB::commit();

            $output = [
                'success' => true,
                'msg' => 'Succesfully'
            ];

        } catch(\Exception $e) {
            DB::rollback();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => true,
                'msg' => 'Error'
            ];

        }

        return $output;
    }

    public function searchPeriod() {

        $date = Carbon::now();
        $mdate = $date->month;
        $ydate = $date->year;

        $business_id = request()->session()->get('user.business_id');

        $accounting_period = DB::table('accounting_periods as ap')
        ->join('fiscal_years as fy', 'fy.id', '=', 'ap.fiscal_year_id')
        ->where('fy.year', $ydate)
        ->where('ap.month', $mdate)
        ->where('ap.status', 1)
        ->where('ap.business_id', $business_id)
        ->first();

        if ($accounting_period) {

            $output = [
                'success' => true,
                'msg' => 'Found'
            ];

        } else {

            $output = [
                'success' => false,
                'msg' => 'Not found'
            ];
        }

        return $output;
    }

    public function getTotalEntrie($id) {

        $totales = DB::table('accounting_entries_details')
        ->select(DB::raw("SUM(debit) as debe, SUM(credit) as haber"))
        ->where('entrie_id', $id)
        ->first();

        $datos = array(
            'debe' => $totales->debe,
            'haber' => $totales->haber,
        );

        return $datos;

    }

    public function getEntrieDetailsDebe($id) {

        $detalles = DB::table('accounting_entries_details as detalle')
        ->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')        
        ->select(['detalle.id', 'detalle.entrie_id', 'detalle.account_id', 'cuenta.code', 'cuenta.name', 'detalle.debit', 'detalle.credit', 'detalle.description'])
        ->where('detalle.entrie_id', $id)
        ->where('detalle.debit', '<>', 0.00)
        ->orderBy('detalle.id', 'asc')
        ->get();

        return response()->json($detalles);
    }

    public function getEntrieDetailsHaber($id) {

        $detalles = DB::table('accounting_entries_details as detalle')
        ->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')        
        ->select(['detalle.id', 'detalle.entrie_id', 'detalle.account_id', 'cuenta.code', 'cuenta.name', 'detalle.debit', 'detalle.credit', 'detalle.description'])
        ->where('detalle.entrie_id', $id)
        ->where('detalle.credit', '<>', 0.00)
        ->orderBy('detalle.id', 'asc')
        ->get();
        
        return response()->json($detalles);
    }

    public function getEntrieDetails($id) {

        $detalles = DB::table('accounting_entries_details as detalle')
        ->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')        
        ->select(['detalle.id', 'detalle.entrie_id', 'detalle.account_id', 'cuenta.code', 'cuenta.name', 'detalle.debit', 'detalle.credit', 'detalle.description'])
        ->where('detalle.entrie_id', $id)
        ->orderBy('detalle.id', 'asc')
        ->get();
        
        return response()->json($detalles);
    }

    public function editEntrie(Request $request) {

        $date = $request->input('date2');
        $description_head = $request->input('description2');
        $cuenta = $request->input('account_id2');
        $account_id = $request->input('account_id2');
        $debe = $request->input('debe2');
        $haber = $request->input('haber2');
        $description = $request->input('description_line2');
        $variable = $request->input('total_debe2');
        $id_partida = $request->input('id_partida');

        $business_id_selected = request()->session()->get('user.business_id');
        $business_selected = Business::where('id', $business_id_selected)->first();

        $entrie_selected = AccountingEntrie::where('id', $id_partida)->first();

        if (($business_selected->allow_uneven_totals_entries == 1) && ($entrie_selected->status == 0)) {

            $validateData = $request->validate(
                [
                    'description2' => 'required',
                    'debe2.*' => ['required', 'numeric'],
                    'haber2.*' => ['required', 'numeric'],
                    'total_debe2' => 'required|numeric',
                'total_haber2' => 'required|numeric',//|in:'.$variable,
                'haber2.*' => ['different:debe2.*'],
                'number2' => 'required|integer',
                'date2' => 'required|date',
            ]
        );

        } else {

            $validateData = $request->validate(
                [
                    'description2' => 'required',
                    'debe2.*' => ['required', 'numeric'],
                    'haber2.*' => ['required', 'numeric'],
                    'total_debe2' => 'required|numeric',
                    'total_haber2' => 'required|numeric|in:'.$variable,
                    'haber2.*' => ['different:debe2.*'],
                    'number2' => 'required|integer',
                    'date2' => 'required|date',
                ]
            );

        }
        
        if($request->ajax()) {

            try {

                DB::beginTransaction();
                AccountingEntriesDetail::where('entrie_id', $id_partida)->forceDelete();
                $entrie = AccountingEntrie::find($id_partida);
                $entrie->description = $description_head;
                $entrie->date = $date;
                $entrie->accounting_period_id = $request->input('period_id2');
                $entrie->type_entrie_id = $request->input('etype_entrie_id');
                $entrie->business_location_id = $request->input('ebusiness_location_id');
                $entrie->save();
                $cont = 0;
                
                while($cont < count($account_id)) {

                    $detalle = new AccountingEntriesDetail;
                    $detalle->entrie_id = $id_partida;
                    $detalle->account_id = $account_id[$cont];
                    $detalle->debit = $debe[$cont];
                    $detalle->credit = $haber[$cont];
                    $detalle->description = $description[$cont];
                    $detalle->save();
                    $cont = $cont + 1;

                }

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('accounting.updated_successfully')
                ];

            } catch(\Exception $e){

                DB::rollback();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    public function searchBankTransaction($id) {

        $count = BankTransaction::where('accounting_entrie_id', $id)
        ->count();

        if ($count > 0) {

            $transaction = BankTransaction::where('accounting_entrie_id', $id)->first();
            $id = $transaction->id;

        } else {

            $id = 0;

        }
        
        $output = [
            'count' => $count,
            'id' => $id
        ];
        
        return $output;
    }

    public function getNumberEntrie($date) {

        $business_id = request()->session()->get('user.business_id');
        $date_entrie = Carbon::parse($date);
        $mdate = $date_entrie->month;
        $ydate = $date_entrie->year;
        $config_numeration = Business::select('entries_numeration_mode')->where('id', $business_id)->first();
        $mode_numeration = $config_numeration->entries_numeration_mode;
        $business_id = request()->session()->get('user.business_id');

        if($mode_numeration == 'month') {

            $count = AccountingEntrie::select(DB::raw('MAX(number) as last_number'))
            ->whereMonth('date', $mdate)
            ->whereYear('date', $ydate)
            ->where('business_id', $business_id)
            ->first();

            if($count->last_number == null) {

                $code = 1;

            } else {

                $code = $count->last_number + 1;
            }
        }

        if($mode_numeration == 'year') {

            $count = AccountingEntrie::select(DB::raw('MAX(number) as last_number'))
            ->whereYear('date', $ydate)
            ->where('business_id', $business_id)
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

        return response()->json([
            "number" => $code
        ]);
    }

    public function getCorrelativeEntrie($date) {

        $business_id = request()->session()->get('user.business_id');
        $date_entrie = Carbon::parse($date);
        $mdate = $date_entrie->month;
        $ydate = $date_entrie->year;
        $config_numeration = Business::select('entries_numeration_mode')->where('id', $business_id)->first();
        $mode_numeration = $config_numeration->entries_numeration_mode;
        
        if($mode_numeration == 'month') {

            $count = AccountingEntrie::select(DB::raw('MAX(correlative) as last_number'))
            ->whereMonth('date', $mdate)
            ->whereYear('date', $ydate)
            ->where('business_id', $business_id)
            ->first();

            if($count->last_number == null) {

                $code = 1;

            } else {

                $code = $count->last_number + 1;

            }
        }

        if($mode_numeration == 'year') {

            $count = AccountingEntrie::select(DB::raw('MAX(correlative) as last_number'))
            ->whereYear('date', $ydate)
            ->where('business_id', $business_id)
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

        return response()->json([
            "number" => $code
        ]);
    }

    public function changeStatus($id, $number) {

        try {

            $business_id = request()->session()->get('user.business_id');

            $entrie = AccountingEntrie::findOrFail($id);
            $transaction = BankTransaction::where('accounting_entrie_id', $entrie->id)
            ->first();
            
            $date = $entrie->date;
            
            $current_status = $entrie->status;
            
            if ($current_status == 1) {
                $entrie->status = 0;
                
            } else {

                $entrie->status = 1;               

                if($transaction != null) {

                    $transaction->status = 1;
                    $transaction->save();

                }
            }

            $entrie->save();


            $output = [
                'success' => true,
                'msg' => __("accounting.updated_successfully")
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

    public function getResultCreditorAccounts($date) {

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $account_creditor = Catalogue::where('id', $business->accounting_creditor_result_id)
        ->first();

        $accounts_credit = DB::table('catalogues as catalogue')
        ->leftJoin('accounting_entries_details as detail', 'detail.account_id', '=', 'catalogue.id')
        ->select(DB::raw("catalogue.code as code_query"), 'catalogue.id', 'catalogue.code', 'catalogue.name', DB::raw("(select (SUM(credit) - SUM(debit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code = code_query and accounting_entries.status = 1 and accounting_entries.date <= '".$date."') as balance"))
        ->whereNOTIn('catalogue.id', [DB::raw("select parent from catalogues")])
        ->whereIn('catalogue.id', [DB::raw("select account_id from accounting_entries_details")])
        ->where('catalogue.code', 'like', ''.$account_creditor->code.'%')
        ->where('catalogue.business_id', $business_id)
        ->orderBy('catalogue.code', 'asc')
        ->groupBy('catalogue.id')
        ->get();

        return response()->json($accounts_credit);

    }

    public function getResultDebtorAccounts($date) {

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $account_debtor = Catalogue::where('id', $business->accounting_debtor_result_id)
        ->first();

        $accounts_debit = DB::table('catalogues as catalogue')
        ->leftJoin('accounting_entries_details as detail', 'detail.account_id', '=', 'catalogue.id')
        ->select(DB::raw("catalogue.code as code_query"), 'catalogue.id', 'catalogue.code', 'catalogue.name', DB::raw("(select (SUM(debit) - SUM(credit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code = code_query and accounting_entries.status = 1 and accounting_entries.date <= '".$date."') as balance"))
        ->whereNOTIn('catalogue.id', [DB::raw("select parent from catalogues")])
        ->whereIn('catalogue.id', [DB::raw("select account_id from accounting_entries_details")])
        ->where('catalogue.code', 'like', ''.$account_debtor->code.'%')
        ->where('catalogue.business_id', $business_id)
        ->orderBy('catalogue.code', 'asc')
        ->groupBy('catalogue.id')
        ->get();

        return response()->json($accounts_debit);
    }

    public function getProfitAndLossAccount() {

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $account = DB::table('catalogues as catalogue')
        ->select('catalogue.id', 'catalogue.code', 'catalogue.name')
        ->where('catalogue.id', $business->accounting_profit_and_loss_id)
        ->where('catalogue.business_id', $business_id)
        ->first();

        return response()->json($account);
    }

    /**
     * Assign a short name to approved entries that do not have it.
     * 
     * @return string
     */
    public function assignShortName() {

        try {

            DB::beginTransaction();

            $entries = AccountingEntrie::where('status', 1)
            ->where('business_id', $business_id)
            ->whereNull('short_name')
            ->get();

            foreach ($entries as $entrie) {

                $date = Carbon::parse($entrie->date);
                $mdate = $date->month;
                $ydate = $date->year;

                $type_q = TypeEntrie::where('id', $entrie->type_entrie_id)->first();
                $short_name_type = $type_q->short_name;

                if ($mdate < 10) {

                    $short_name_month = '0' . $mdate;

                } else {

                    $short_name_month = $mdate;
                
                }

                $code = $entrie->correlative;
                $short_name_cont = str_pad($code, 5, "0", STR_PAD_LEFT);

                $short_name_full = $short_name_type . '-' . $ydate . $short_name_month . '-' . $short_name_cont;

                $entrie->short_name = $short_name_full;

                $entrie->save();

                \Log::info("ENTRIE: $entrie->id - SHORT NAME: $entrie->short_name");
            }

            DB::commit();

            return 'SUCCESS';

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            return 'FAIL';
        }
    }

    public function setNumeration($mode, $period) {

        try {

            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');

            if ($mode == 'month') {

                $entries = DB::table('accounting_entries as entrie')
                ->select('entrie.*')
                ->where('entrie.accounting_period_id', $period)
                ->where('entrie.business_id', $business_id)
                ->orderBy('entrie.date', 'ASC')
                ->orderBy('entrie.id', 'ASC')
                ->get();

            }

            if ($mode == 'year') {

                $entries = DB::table('accounting_entries as entrie')
                ->join('accounting_periods as period', 'period.id', '=', 'entrie.accounting_period_id')
                ->select('entrie.*')
                ->where('entrie.business_id', $business_id)
                ->where('period.fiscal_year_id', $period)                
                ->orderBy('entrie.date', 'ASC')
                ->orderBy('entrie.id', 'ASC')
                ->get();

            }

            $number = 1;

            foreach($entries as $entrie) {

                $entrie = AccountingEntrie::where('id', $entrie->id)
                ->first();
                
                $entrie->number = $number;
                $entrie->correlative = $number;

                $date = Carbon::parse($entrie->date);
                $mdate = $date->month;
                $ydate = $date->year;

                $short_name_cont = str_pad($number, 5, "0", STR_PAD_LEFT);
                $type_q = TypeEntrie::where('id', $entrie->type_entrie_id)->first();
                $short_name_type = $type_q->short_name;
                
                if ($mdate < 10) {

                    $short_name_month = '0' . $mdate;
                
                } else {
                    
                    $short_name_month = $mdate;
                
                }

                $short_name_year = $ydate;
                $short_name_full = $short_name_type . '-' . $short_name_year . $short_name_month . '-' . $short_name_cont;


                $entrie->short_name = $short_name_full;
                $entrie->save();

                $number = $number + 1;
            }

            DB::commit();           

            $output = [
                'success' => true,
                'msg' => __('accounting.success_true'),
                'data' => $entries
            ];

        } catch(\Exception $e) {

            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('accounting.success_false')
            ];
        }

        return $output;
    }

    public function getApertureDebitAccounts($date) {

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $accounts_debit = DB::table('catalogues as catalogue')
        ->leftJoin('accounting_entries_details as detail', 'detail.account_id', '=', 'catalogue.id')
        ->select(
            DB::raw("catalogue.code as code_query"),
            'catalogue.id',
            'catalogue.code',
            'catalogue.name',
            DB::raw("(select (SUM(debit) - SUM(credit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code = code_query and accounting_entries.status = 1 and accounting_entries.date < '".$date."') as balance")
        )
        ->where('catalogue.business_id', $business_id)
        ->whereNOTIn('catalogue.id', [DB::raw("select parent from catalogues")])
        ->whereIn('catalogue.id', [DB::raw("select account_id from accounting_entries_details")])
        ->where('catalogue.code', 'like', '1%')
        ->groupBy('catalogue.id')
        ->orderByRaw('CONVERT(catalogue.code, CHAR) asc')
        ->get();

        return response()->json($accounts_debit);
    }

    public function getApertureCreditAccounts($date) {

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $accounts_credit = DB::table('catalogues as catalogue')
        ->leftJoin('accounting_entries_details as detail', 'detail.account_id', '=', 'catalogue.id')
        ->select(
            DB::raw("catalogue.code as code_query"),
            'catalogue.id',
            'catalogue.code',
            'catalogue.name',
            DB::raw("(select (SUM(credit) - SUM(debit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code = code_query and accounting_entries.status = 1 and accounting_entries.date < '".$date."') as balance")
        )
        ->where('catalogue.business_id', $business_id)
        ->whereNOTIn('catalogue.id', [DB::raw("select parent from catalogues")])
        ->whereIn('catalogue.id', [DB::raw("select account_id from accounting_entries_details")])
        ->where('catalogue.code', 'like', '2%')
        ->orWhere('catalogue.code', 'like', '3%')
        ->groupBy('catalogue.id')
        ->orderByRaw('CONVERT(catalogue.code, CHAR) asc')
        ->get();

        return response()->json($accounts_credit);
    }
}