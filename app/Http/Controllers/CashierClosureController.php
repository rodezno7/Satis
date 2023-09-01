<?php

namespace App\Http\Controllers;

use App\Cashier;
use App\Business;
use App\TypeEntrie;
use App\Transaction;
use App\DocumentType;
use App\CashierClosure;
use App\BusinessLocation;
use App\DocumentCorrelative;
use App\AccountBusinessLocation;

use App\Utils\CashierUtil;
use App\Utils\AccountingUtil;
use DB;

use Illuminate\Http\Request;

class CashierClosureController extends Controller
{
    /**
     * All utils instances
     * 
     */
    protected $cashierUtil;
    protected $accountingUtil;

    /**
     * Constructor
     * @param CashierUtil $cashierUtil
     * @return void
     */
    public function __construct(CashierUtil $cashierUtil, AccountingUtil $accountingUtil){
        $this->cashierUtil = $cashierUtil;
        $this->accountingUtil = $accountingUtil;

        if (config('app.disable_sql_req_pk')) {
            DB::statement('SET SESSION sql_require_primary_key=0');
        }
    }

    /**
     * Return cashier closure information
     */
    public function getCashierClosure($cashier_closure_id = null){
        $cashier_id = request()->input('cashier_id', null);
        
        if(is_null($cashier_closure_id)){
            if(!is_null($cashier_id)){
                $cashier_closure_id = $this->cashierUtil->getCashierClosureActive($cashier_id);
            }
        }

        $cashier_closure = CashierClosure::find($cashier_closure_id);
        $closure_details = collect(DB::select('CALL getCashierClosureInfo(?)', [$cashier_closure_id]));

        $closure_details = $closure_details[0];
        return view('cashier.partials.closure')
            ->with(compact('closure_details', 'cashier_closure', 'cashier_id'));
    }

    /**
     * Store cashier closure
     */
    public function postCashierClosure(){
        try {
            $cashier_closure_id = request()->input('cashier_closure_id');
            $cashier_id = request()->input('cashier_id');
            $business_id = auth()->user()->business_id;
            $sale_accounting_entry = Business::find($business_id)->value('sale_accounting_entry_mode');
            $user_id = auth()->user()->id;

            DB::beginTransaction();
            /** Updating cashier closure records */
            $cashier_closure = CashierClosure::find($cashier_closure_id);
            $cashier_closure->total_system_amount = $this->cashierUtil->num_uf(request()->input('total_system_amount'));
            $cashier_closure->total_physical_amount = $this->cashierUtil->num_uf(request()->input('total_physical_amount'));
            $cashier_closure->total_cash_amount = $this->cashierUtil->num_uf(request()->input('total_cash_amount', 0));
            $cashier_closure->total_card_amount = $this->cashierUtil->num_uf(request()->input('total_card_amount', 0));
            $cashier_closure->total_check_amount = $this->cashierUtil->num_uf(request()->input('total_check_amount', 0));
            $cashier_closure->total_bank_transfer_amount = $this->cashierUtil->num_uf(request()->input('total_bank_transfer_amount', 0));
            $cashier_closure->total_credit_amount = $this->cashierUtil->num_uf(request()->input('total_credit_amount', 0));
            $cashier_closure->total_return_amount = $this->cashierUtil->num_uf(request()->input('total_return_amount', 0));
            $cashier_closure->differences = $this->cashierUtil->num_uf(request()->input('differences', 0));
            $cashier_closure->closing_note = request()->input('closing_note');
            $cashier_closure->closed_by = $user_id;
            $cashier_closure->close_date = \Carbon::now()->toDateTimeString();
            $cashier_closure->save();

            /** Updating cashier */
            $cashier = Cashier::find($cashier_id);
            $cashier->status = 'close';
            $cashier->last_close_by = $user_id;
            $cashier->last_close = $cashier_closure->close_date;
            $cashier->last_cashier_closure = $cashier_closure->id;
            $cashier->save();

            /** asign correlative */
            $document =
                DocumentCorrelative::join("document_types as dt", "dt.id", "document_correlatives.document_type_id")
                    ->where('dt.business_id', $business_id)
                    ->where("dt.is_active", 1)
                    ->where('document_correlatives.location_id', $cashier->business_location_id)
                    ->where('dt.short_name', 'Ticket')
                    ->where('document_correlatives.status', 'active')
                    ->select('document_correlatives.*')
                    ->first();

            if(!empty($document)) {
                if($document->actual < $document->final){
                    $cashier_closure->close_correlative = $document->actual;
                    $cashier_closure->save();

                    $document->actual += 1;
                    $document->save();

                } else if($document->actual == $document->final){
                    $cashier_closure->close_correlative = $document->actual;
                    $cashier_closure->save();
                    
                    $document->status = 'inactive';
                    $document->save();
                }
            }

            if ($sale_accounting_entry == 'cashier_closure') {
                /** generate sale accounting entry */
                $this->createSaleAccountingEntry($cashier_closure_id);
            }
            
            $output = [
                'success' => 1,
                'msg' => __('cash_register.close_success')
            ];

            DB::commit();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];

            DB::rollBack();
        }

        return redirect()->action('HomeController@index')->with('status', $output);
    }

    /**
     * Show cashier closure report
     * @param int $location_id
     * @param int $cashier_closure_id | null
     * @param int $cashier_id | null
     */
    public function dailyZCutReport($location_id, $cashier_id = null, $cashier_closure_id = null){
        if(!auth()->user()->can('daily_z_cut_report.view')){
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get("user.business_id");

        /** if cashier closure id is null, get it */
        if(is_null($cashier_closure_id)){
            if(!is_null($cashier_id)){
                $cashier_closure_id = $this->cashierUtil->getLastCashierClosure($cashier_id);
            } else{
                return [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
        }

        /** cashier closure information */

        $cashier_closure = CashierClosure::find($cashier_closure_id);

        /** Get document information */
        $document_info =
            DocumentType::join("document_correlatives as dc", "document_types.id", "dc.document_type_id")
                ->where("dc.location_id", $location_id)
                ->where("document_types.is_active", 1)
                ->where("document_types.short_name", "Ticket")
                ->select(
                    "dc.resolution",
                    "dc.serie",
                    "dc.initial",
                    "dc.final"
                )->first();

        /** Get business information */
        $business_info =
            Business::where("id", $business_id)
                ->select(
                    "name",
                    "nit",
                    "nrc",
                    "business_full_name as business_full_name",
                    "line_of_business as line_of_business"
                )->first();

        /** Get location information */
        $location_info =
            BusinessLocation::where("id", $location_id)
                    ->select(
                        "name",
                        "mobile",
                        DB::raw("CONCAT(landmark, ', ', city, ', ', state) as address")
                    )->first();
        
        $cashier_closure = [
            'date' => \Carbon::createFromFormat('Y-m-d H:i:s', $cashier_closure->close_date)->format('d/m/y'),
            'time' => \Carbon::createFromFormat('Y-m-d H:i:s', $cashier_closure->close_date)->format('H:i:s'),
            'correlative' => $cashier_closure->close_correlative,
            'cashier_id' => $cashier_closure->cashier_id
        ];

        /** Get daily z cut report information */
        $daily_z_cut_report = collect(DB::select('CALL getDailyZCutReport(?)', [$cashier_closure_id]));
        $daily_z_cut_report = $daily_z_cut_report[0];

        $daily_z_cut_report_pdf =
            \PDF::loadView('cashier.partials.close_daily_z_cut_report_pdf',
			    compact('document_info', 'business_info', 'location_info', 'daily_z_cut_report', 'cashier_closure'));
		$daily_z_cut_report_pdf->setPaper([0, 0, 250, 450], 'portrait');

		return $daily_z_cut_report_pdf->stream(__('report.daily_z_cut_report') . '.pdf');
    }

     /**
     * Get Opening Cash Register Receipt
     * @param int $cashier_id
     */
    public function openingCashRegister($cashier_closure_id){
        $cashier_closure = CashierClosure::find($cashier_closure_id);
        $cashier = Cashier::find($cashier_closure->cashier_id);
        
        /** Get document information */
        $document_info =
        DocumentType::join("document_correlatives as dc", "document_types.id", "dc.document_type_id")
            ->where("dc.location_id", $cashier->business_location_id)
            ->where("document_types.is_active", 1)
            ->where("document_types.short_name", "Ticket")
            ->select(
                "dc.resolution",
                "dc.serie",
                "dc.initial",
                "dc.final"
            )->first();

        /** Get business information */
        $business_info =
        Business::where("id", $cashier->business_id)
            ->select(
                "name",
                "nit",
                "nrc",
                "business_full_name as business_full_name",
                "line_of_business as line_of_business",
                "show_open_daily_z_cut_amount"
            )->first();

        /** Get location information */
        $location_info =
        BusinessLocation::where("id", $cashier->business_location_id)
            ->select(
                "name",
                "mobile",
                DB::raw("CONCAT(landmark, ', ', city, ', ', state) as address")
            )->first();

        $opening_amount = $business_info->show_open_daily_z_cut_amount == 1 ? $cashier_closure->initial_cash_amount : 0;

        $cashier_closure = [
            'date' => \Carbon::createFromFormat('Y-m-d H:i:s', $cashier_closure->open_date)->format('d/m/y'),
            'time' => \Carbon::createFromFormat('Y-m-d H:i:s', $cashier_closure->open_date)->format('H:i:s'),
            'correlative' => $cashier_closure->open_correlative,
            'cashier_id' => $cashier->id
        ];

        $opening_cash_receipt = \PDF::loadView('cashier.partials.open_daily_z_cut_report_pdf',
            compact('document_info', 'business_info', 'location_info', 'cashier_closure', 'opening_amount'));
        
        $opening_cash_receipt->setPaper([0, 0, 250, 450], 'portrait');
        
		return $opening_cash_receipt->stream(__('cash_register.opening_cash_register') . '.pdf');
    }

    /**
     * Show daily z cut
     * @param int $cashier_closure_id
     */
    public function showDailyZCut($id){
        if(!auth()->user()->can('daily_z_cut_report.view')){
            abort(403, 'Unauthorized action.');
        }

        $closure_details =
            CashierClosure::join('cashiers AS c', 'cashier_closures.cashier_id', 'c.id')
                ->where('cashier_closures.id', $id)
                ->select('c.id as cashier_id', 'c.business_location_id as location_id', 'cashier_closures.*')
                ->first();

        return view('cashier.partials.show_daily_z_cut_report')
            ->with(compact('closure_details'));
    }

    /**
     * Recalculate Cashier Closure
     * 
     * @param int $cashier_closure_id
     * @param int $location_id
     * 
     * @return array
     */
    public function recalcCashierClosure($id, $location_id) {
        try {
            $cc = CashierClosure::findOrFail($id);
            $cc_date = date('Y-m-d', strtotime($cc->close_date));
            $business_id = auth()->user()->business_id;

            $transactions = Transaction::where('location_id', $location_id)
                ->where('business_id', $business_id)
                ->whereRaw('DATE(transaction_date) = DATE(?)', [$cc_date])
                ->whereIn('type', ['sell', 'sell_return'])
                ->select('id', 'cashier_closure_id')
                ->get();

            DB::beginTransaction();

            /** Update cashier closure on transactions */
            foreach ($transactions as $t) {
                if ($t->cashier_closure_id != $cc->id) {
                    $t->cashier_closure_id = $cc->id;
                    $t->save();
                }
            }

            $cc_info = collect(DB::select('CALL getCashierClosureInfo(?)', [$id]));

            if (!empty($cc_info)) {
                $cc->total_system_amount = ($cc_info[0]->final_total - $cc_info[0]->return_amount);
                $cc->total_cash_amount = $cc_info[0]->cash_amount;
                $cc->total_card_amount = $cc_info[0]->card_amount;
                $cc->total_credit_amount = $cc_info[0]->credit_amount;
                $cc->total_check_amount = $cc_info[0]->check_amount;
                $cc->total_bank_transfer_amount = $cc_info[0]->bank_transfer_amount;
                $cc->differences = ($cc->total_physical_amount - $cc->total_system_amount);
                $cc->save();
            }

            DB::commit();
            
            $output = [
                'success' => true,
                'msg' => __('cashier.cc_updated_successfully')
            ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            DB::rollback();

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Create sale accounting entry
     * @param int $cashier_closure_id
     */
    public function createSaleAccountingEntry($cashier_closure_id) {

        $business_id = auth()->user()->business_id;
        $cashier_closure = CashierClosure::find($cashier_closure_id);
        $location =
            BusinessLocation::join('cashiers as c', 'business_locations.id', 'c.business_location_id')
                ->where('c.id', $cashier_closure->cashier_id)
                ->select('business_locations.name', 'business_locations.id')
                ->first();

        try{

            $date = $this->accountingUtil->format_date($cashier_closure->close_date);
            $description = "VENTAS DEL DÍA " . $date . " EN " . mb_strtoupper($location->name);

            $entry = [
                'date' => $this->accountingUtil->uf_date($date),
                'description' => $description,
                'short_name' => null,
                'business_location_id' => $location->id,
                'business_id' => $business_id,
                'status_bank_transaction' => 1
            ];

            $entry_lines = $this->getSaleAccountingEntryLines($cashier_closure_id);

            $entry_type =
                TypeEntrie::where('name', 'Ingresos')
                    ->orWhere('name', 'Ingreso')
                    ->first();
            $entry['type_entrie_id'] = $entry_type->id;

            $output = $this->accountingUtil->createAccountingEntry($entry, $entry_lines, $entry['date']);
            
            /** generate cost accounting entry */
            $this->createCostAccountingEntry($cashier_closure_id);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Get sale accounting entry lines
     * @param int $cashier_closure_id
     */
    private function getSaleAccountingEntryLines($cashier_closure_id){
        $cashier_closure =
            CashierClosure::join('cashiers as c', 'cashier_closures.cashier_id', 'c.id')
                ->where('cashier_closures.id', $cashier_closure_id)
                ->select('c.business_location_id as location_id')
                ->first();
        $accounts_location =
            AccountBusinessLocation::where('location_id', $cashier_closure->location_id)->first();
            
        $transactions =
            Transaction::join('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
                ->join('document_types as dt', 'transactions.document_types_id', 'dt.id')
                ->where('transactions.cashier_closure_id', $cashier_closure_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->select(
                    DB::raw('SUM(IF(dt.short_name IN ("FCF", "Ticket", "CCF"), tsl.unit_price_exc_tax, 0)) as total_amount'),
                    DB::raw('SUM(IF(dt.short_name IN ("FCF", "Ticket"), tsl.tax_amount, 0)) as final_consumer_tax_amount'),
                    DB::raw('SUM(IF(dt.short_name = "CCF", tsl.tax_amount, 0)) as taxpayer_tax_amount')
                )->first();

        /** start payments */
        $payments = collect(DB::select('CALL getNewCashRegisterReport(?)', [$cashier_closure_id]));

        $cash_amount =
            ($payments->sum('cash_amount')  +
            $payments->sum('card_amount') +
            $payments->sum('check_amount') +
            $payments->sum('bank_transfer_amount')) -
            $payments->sum('return_amount');
        
        $credit_amount = $payments->sum('credit_amount');
        /** end payments */

        /** sell returns */
        $tax_taxpayer_return =
            $payments->where('doc_type', 'FCF')
                ->where('payment_condition', 'sell_return')
                ->sum('tax_amount');
        $tax_final_customer_returns =
            $payments->whereIn('doc_type', ['Ticket', 'FCF'])
                ->where('payment_condition', 'sell_return')
                ->sum('tax_amount');
        $taxpayer_returns =
            $payments->where('doc_type', 'FCF')
                ->where('payment_condition', 'sell_return')
                ->sum('return_amount') - $tax_taxpayer_return;
        $final_customer_returns =
            $payments->whereIn('doc_type', ['Ticket', 'FCF'])
                ->where('payment_condition', 'sell_return')
                ->sum('return_amount') - $tax_final_customer_returns;
        /** end sell returns */

        /** Withhelds */
        $withheld_amount = $payments->sum('withheld_amount');
        $withheld_account_id = Business::find(auth()->user()->business_id)->accounting_withheld_id;
        
        return [
            [
                'catalogue_id' => $accounts_location->general_cash_id,
                'amount' => $cash_amount,
                'type' => 'debit',
                'description' => 'VENTAS CON PAGOS EN EFECTIVO'
            ],
            [
                'catalogue_id' => $accounts_location->account_receivable_id,
                'amount' => $credit_amount,
                'type' => 'debit',
                'description' => 'VENTAS AL CRÉDITOS'
            ],
            [
                'catalogue_id' => $withheld_account_id,
                'amount' => $withheld_amount,
                'type' => 'debit',
                'description' => 'RETENCION IVA 1%'
            ],
            [
                'catalogue_id' => $accounts_location->vat_final_customer_id,
                'amount' => $transactions->final_consumer_tax_amount - $tax_final_customer_returns,
                'type' => 'credit',
                'description' => 'IVA DÉBITO FISCAL DE CONSUMIDOR FINAL'
            ],
            [
                'catalogue_id' => $accounts_location->vat_taxpayer_id,
                'amount' => $transactions->taxpayer_tax_amount - $tax_taxpayer_return,
                'type' => 'credit',
                'description' => 'IVA DÉBITO FISCAL DE CONTRIBUYENTE'
            ],
            [
                'catalogue_id' => $accounts_location->local_sale_id,
                'amount' => $transactions->total_amount - ($taxpayer_returns + $final_customer_returns),
                'type' => 'credit',
                'description' => 'VENTAS DEL DÍA'
            ]
        ];
    }

    /**
     * Create cost accounting entry
     * @param int $cashier_closure_id
     */
    private function createCostAccountingEntry($cashier_closure_id){
        $cashier_closure = CashierClosure::find($cashier_closure_id);
        $location =
            BusinessLocation::join('cashiers as c', 'business_locations.id', 'c.business_location_id')
                ->where('c.id', $cashier_closure->cashier_id)
                ->select('business_locations.name', 'business_locations.id')
                ->first();

        try{
            $date = $this->accountingUtil->format_date($cashier_closure->close_date);
            $description = "COSTO POR LA VENTA DEL DÍA " . $date . " EN " . mb_strtoupper($location->name);

            $entry = [
                'date' => $this->accountingUtil->uf_date($date),
                'description' => $description,
                'short_name' => null,
                'business_location_id' => $location->id,
                'status_bank_transaction' => 1
            ];

            $entry_lines = $this->getCostAccountingEntryLines($cashier_closure_id);

            $entry_type =
                TypeEntrie::where('name', 'Diarios')
                    ->orWhere('name', 'Diario')
                    ->first();
            $entry['type_entrie_id'] = $entry_type->id;

            $output = $this->accountingUtil->createAccountingEntry($entry, $entry_lines, $entry['date']);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Get cost accounting entry lines
     * @param int $cashier_closure_id
     */
    private function getCostAccountingEntryLines($cashier_closure_id){
        $cashier_closure =
            CashierClosure::join('cashiers as c', 'cashier_closures.cashier_id', 'c.id')
                ->where('cashier_closures.id', $cashier_closure_id)
                ->select('c.business_location_id as location_id')
                ->first();
        $accounts_location =
            AccountBusinessLocation::where('location_id', $cashier_closure->location_id)->first();
            
        $sales =
            Transaction::join('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
                ->join('variations as v', 'tsl.variation_id', 'v.id')
                ->where('transactions.cashier_closure_id', $cashier_closure_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->sum(DB::raw('tsl.quantity * v.default_purchase_price'));

        $returns =
            Transaction::join('transactions as rt', 'transactions.return_parent_id', 'rt.id')
                ->join('transaction_sell_lines as tsl', 'rt.id', 'tsl.transaction_id')
                ->join('variations as v', 'tsl.variation_id', 'v.id')
                ->where('transactions.cashier_closure_id', $cashier_closure_id)
                ->sum(DB::raw('tsl.quantity_returned * v.default_purchase_price'));

        $inventory_cost = $sales - $returns;
                
        return [
            [
                'catalogue_id' => $accounts_location->sale_cost_id,
                'amount' => $inventory_cost,
                'type' => 'debit',
                'description' => 'COSTO POR VENTA DEL DÍA'
            ],
            [
                'catalogue_id' => $accounts_location->inventory_account_id,
                'amount' => $inventory_cost,
                'type' => 'credit',
                'description' => 'SALIDA DE INVENTARIO POR VENTA DEL DÍA'
            ]
        ];
    }
}
