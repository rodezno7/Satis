<?php

namespace App\Http\Controllers;

use App\Apportionment;
use App\ApportionmentHasImportExpense;
use App\ApportionmentHasTransaction;
use App\Business;
use App\Kardex;
use App\PurchaseLine;
use App\Transaction;
use App\TransactionHasImportExpense;
use App\TransactionSellLine;
use App\Utils\ProductUtil;
use App\Utils\TaxUtil;
use App\Utils\TransactionUtil;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ApportionmentController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param  \App\TransactionUtil $transactionUtil
     * @param  \App\TaxUtil  $taxUtil
     * @param  \App\ProductUtil  $productUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, TaxUtil $taxUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->taxUtil = $taxUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('apportionment.view') && ! auth()->user()->can('apportionment.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $business_id = $business_id = request()->session()->get('user.business_id');
            
            $apportionments = Apportionment::where('business_id', $business_id)
                ->select('name', 'reference', 'is_finished', 'apportionment_date', 'id');

            return Datatables::of($apportionments)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '&nbsp;<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            @can("apportionment.view")
                            <li>
                                <a href="#" data-href="{{ action(\'ApportionmentController@show\', [$id]) }}"
                                    class="btn-modal"
                                    data-container=".view_modal">
                                    <i class="fa fa-eye"></i> @lang("messages.view")
                                </a>
                            </li>
                            @endcan
                            @can("apportionments.update")
                            <li>
                                @if (! $is_finished)
                                <a href="{{ action(\'ApportionmentController@edit\', [$id]) }}"
                                    class="edit_apportionments_button">
                                    <i class="fa fa-edit"></i> @lang("messages.edit")
                                </a>
                                @endif
                            </li>
                            @endcan
                            @can("apportionments.delete")
                            <li>
                                @if (! $is_finished)
                                <a href="#" data-href="{{ action(\'ApportionmentController@destroy\', [$id]) }}"
                                    class="delete_apportionments_button">
                                    <i class="fa fa-trash"></i> @lang("messages.delete")
                                </a>
                                @endif
                            </li>
                            @endcan
                        </ul>
                    </div>'
                )
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('apportionments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('apportionment.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        // Number of decimals in purchases
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $decimals_in_purchases = $product_settings['decimals_in_purchases'];

        return view('apportionments.create')->with(compact('currency_details', 'decimals_in_purchases'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('apportionment.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            // Save apportionment
            $input = $request->only(['name', 'reference', 'distributing_base', 'vat_amount', 'apportionment_date']);

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $input['apportionment_date'] = $this->productUtil->uf_date($input['apportionment_date']);

            $apportionment = Apportionment::create($input);

            // Save import expenses, purchases and purchase lines
            $this->save($apportionment, $request);

            if ($request->get('submit_type') == 'save_and_process') {
                // Edit avarage cost
                $enable_editing_avg_cost = $request->session()->get('business.enable_editing_avg_cost_from_purchase');
    
                // Calculate average cost
                if ($enable_editing_avg_cost == 1) {
                    $this->calculateAvgCost($apportionment->id);
                }

                // Finish apportionment
                $apportionment->is_finished = 1;
                $apportionment->save();
            }

            DB::commit();

            $output = [
                'success' => true,
                'data' => $apportionment,
                'msg' => __('apportionment.added_success')
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('apportionments')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('apportionment.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $apportionment = Apportionment::find($id);

        // Purchases
        $transactions = ApportionmentHasTransaction::join('transactions as t', 't.id', 'apportionment_has_transactions.transaction_id')
            ->join('purchase_lines as pl', 'pl.transaction_id', 't.id')
            ->leftJoin('contacts as c', 'c.id', 't.contact_id')
            ->leftJoin('document_types as dt', 'dt.id', 't.document_types_id')
            ->where('apportionment_has_transactions.apportionment_id', $id);

        $purchases = $transactions->select(
                't.ref_no',
                'c.name',
                DB::raw("SUM(IF(pl.initial_purchase_price IS NULL, pl.purchase_price * pl.quantity, pl.initial_purchase_price * pl.quantity)) as total"),
                't.id',
                'apportionment_has_transactions.id as aht_id',
                'dt.document_name'
            )
            ->groupBy('t.id')
            ->get();

        // Import expenses for apportionments
        $import_expenses_query = ApportionmentHasImportExpense::join('import_expenses as ie', 'ie.id', 'apportionment_has_import_expenses.import_expense_id')
            ->where('apportionment_has_import_expenses.apportionment_id', $id)
            ->select(
                'ie.*',
                'ie.id as import_expense_id',
                'apportionment_has_import_expenses.amount',
                'apportionment_has_import_expenses.id as id'
            );

        $import_expenses = $import_expenses_query->get();

        $import_expenses_total = $import_expenses_query->sum('apportionment_has_import_expenses.amount');

        // Product lines
        $purchase_ids = $transactions->groupBy('t.id')->pluck('t.id');

        // All lines of all purchases
        $lines = [];

        // Total purchase and import expenses of the transaction
        $purchases_p = [];

        // Product tax percentage
        $products_vat = [];

        // Weight totals
        $weight_totals = 0;

        // Cost totals
        $cost_totals = 0;

        foreach ($purchase_ids as $purchase_id) {
            $purchase_lines = PurchaseLine::where('transaction_id', $purchase_id)->get();

            // All lines of a purchase
            $all_lines = [];

            // Totals per purchase
            $total_weight = 0;
            $total_cost = 0;

            foreach ($purchase_lines as $line) {
                $purchase = Transaction::leftJoin('transaction_has_import_expenses as thie', 'thie.transaction_id', 'transactions.id')
                    ->join('purchase_lines as pl', 'pl.transaction_id', 'transactions.id')
                    ->where('transactions.id', $line->transaction_id)
                    ->select(
                        DB::raw("(SELECT SUM(initial_purchase_price * quantity) FROM purchase_lines WHERE transaction_id = transactions.id) as total_purchase"),
                        DB::raw("(SELECT SUM(amount) FROM transaction_has_import_expenses WHERE transaction_id = transactions.id) as total_import_expenses")
                    )
                    ->groupBy('transactions.id')
                    ->first();
    
                $all_lines[] = $line;
                
                $purchases_p[$line->id] = $purchase;

                $product_vat = $this->taxUtil->getTaxes($line->tax_id);
                $products_vat[$line->id] = $product_vat;

                $total_weight += number_format($line->weight_kg, 4);

                $purchase_price = is_null($line->initial_purchase_price) ? $line->purchase_price : $line->initial_purchase_price;
                $total_cost += round($line->quantity * $purchase_price, 4);
            }

            $lines[$purchase_id] = $all_lines;
            $weight_totals += $total_weight;
            $cost_totals += $total_cost;
        }

        // Import expenses of purchases
        $import_expenses_purchases = TransactionHasImportExpense::join('import_expenses as ie', 'ie.id', 'transaction_has_import_expenses.import_expense_id')
            ->whereIn('transaction_has_import_expenses.transaction_id', $purchase_ids)
            ->select(
                'ie.name',
                DB::raw("SUM(transaction_has_import_expenses.amount) as amount")
            )
            ->groupBy('transaction_has_import_expenses.import_expense_id')
            ->get();

        return view('apportionments.show')
            ->with(compact(
                'apportionment',
                'purchases',
                'lines',
                'currency_details',
                'import_expenses_total',
                'weight_totals',
                'cost_totals',
                'purchases_p',
                'import_expenses',
                'import_expenses_purchases'
            ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('apportionments.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $apportionment = Apportionment::find($id);

        // Purchases
        $transactions = ApportionmentHasTransaction::join('transactions as t', 't.id', 'apportionment_has_transactions.transaction_id')
            ->join('purchase_lines as pl', 'pl.transaction_id', 't.id')
            ->leftJoin('contacts as c', 'c.id', 't.contact_id')
            ->where('apportionment_has_transactions.apportionment_id', $id);

        $purchases = $transactions->select(
                't.ref_no',
                'c.name',
                DB::raw("SUM(IF(pl.initial_purchase_price IS NULL, pl.purchase_price * pl.quantity, pl.initial_purchase_price * pl.quantity)) as total"),
                't.id',
                'apportionment_has_transactions.id as aht_id'
            )
            ->groupBy('t.id')
            ->get();

        // Import expenses
        $import_expenses = ApportionmentHasImportExpense::join('import_expenses as ie', 'ie.id', 'apportionment_has_import_expenses.import_expense_id')
            ->where('apportionment_has_import_expenses.apportionment_id', $id)
            ->select(
                'ie.*',
                'ie.id as import_expense_id',
                'apportionment_has_import_expenses.amount',
                'apportionment_has_import_expenses.id as id'
            )
            ->get();

        // Product lines
        $purchase_ids = $transactions->groupBy('t.id')->pluck('t.id');

        $lines = [];
        $purchases_p = [];
        $products_vat = [];

        foreach ($purchase_ids as $purchase_id) {
            $purchase_lines = PurchaseLine::where('transaction_id', $purchase_id)->get();

            foreach ($purchase_lines as $line) {
                $purchase = Transaction::leftJoin('transaction_has_import_expenses as thie', 'thie.transaction_id', 'transactions.id')
                    ->join('purchase_lines as pl', 'pl.transaction_id', 'transactions.id')
                    ->where('transactions.id', $line->transaction_id)
                    ->select(
                        DB::raw("(SELECT SUM(initial_purchase_price * quantity) FROM purchase_lines WHERE transaction_id = transactions.id) as total_purchase"),
                        DB::raw("(SELECT SUM(amount) FROM transaction_has_import_expenses WHERE transaction_id = transactions.id) as total_import_expenses")
                    )
                    ->groupBy('transactions.id')
                    ->first();
    
                $product_vat = $this->taxUtil->getTaxes($line->tax_id);
    
                $lines[] = $line;
                $purchases_p[$line->id] = $purchase;
                $products_vat[$line->id] = $product_vat;
            }
        }

        // Disabled if finished
        $disabled = $apportionment->is_finished == 1 ? 'disabled' : '';

        // Number of decimals in purchases
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
        $decimals_in_purchases = $product_settings['decimals_in_purchases'];

        return view('apportionments.edit')->with(compact(
            'currency_details',
            'apportionment',
            'purchases',
            'import_expenses',
            'lines',
            'purchases_p',
            'products_vat',
            'disabled',
            'decimals_in_purchases'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Apportionment  $apportionment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('apportionments.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            // Save apportionment
            $input = $request->only(['name', 'reference', 'distributing_base', 'vat_amount', 'apportionment_date']);

            $apportionment = Apportionment::findOrFail($id);

            $input['apportionment_date'] = $this->productUtil->uf_date($input['apportionment_date']);

            $apportionment->fill($input);
            $apportionment->save();

            // Save import expenses, purchases and purchase lines
            $this->updateApportionment($apportionment, $request);

            if ($request->get('submit_type') == 'save_and_process') {
                // Edit avarage cost
                $enable_editing_avg_cost = $request->session()->get('business.enable_editing_avg_cost_from_purchase');
    
                // Calculate average cost
                if ($enable_editing_avg_cost == 1) {
                    $this->calculateAvgCost($apportionment->id);
                }

                // Finish apportionment
                $apportionment->is_finished = 1;
                $apportionment->save();
            }

            DB::commit();

            $output = [
                'success' => true,
                'data' => $apportionment,
                'msg' => __('apportionment.updated_success')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
        
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect('apportionments')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Apportionment  $apportionment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('apportionments.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $apportionment = Apportionment::find($id);

            if ($apportionment->is_finished == 0) {
                try {
                    DB::beginTransaction();

                    // Update purchases
                    $transaction_ids = ApportionmentHasTransaction::where('apportionment_id', $id)->pluck('transaction_id');
    
                    // Delete import expenses related to apportionment
                    DB::table('apportionment_has_import_expenses')->where('apportionment_id', $id)->delete();
    
                    // Delete transactions related to apportionment
                    DB::table('apportionment_has_transactions')->where('apportionment_id', $id)->delete();
    
                    $apportionment->delete();
    
                    if (! empty($transaction_ids)) {
                        foreach ($transaction_ids as $transaction_id) {
                            // Update purchase lines
                            $purchase_lines = PurchaseLine::where('transaction_id', $transaction_id)->get();

                            foreach ($purchase_lines as $purchase_line) {
                                $purchase_line->purchase_price = $purchase_line->initial_purchase_price;
                                $purchase_line->purchase_price_inc_tax = $purchase_line->initial_purchase_price;
                                $purchase_line->dai_amount = 0;
                                $purchase_line->dai_percent = 0;
                                $purchase_line->import_expense_amount = 0;
                                $purchase_line->tax_amount = 0;

                                $purchase_line->save();
                            }

                            // Update import data
                            $this->transactionUtil->updateImportData($transaction_id);
    
                            // Update transaction payment status
                            $this->transactionUtil->updatePaymentStatus($transaction_id);
                        }
                    }
    
                    $output = [
                        'success' => true,
                        'msg' => __('apportionment.deleted_success')
                    ];

                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
    
                    \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

                    $output = [
                        'success' => false,
                        'msg' => __('messages.something_went_wrong')
                    ];
                }

            } else {
                $output = [
                    'success' => false,
                    'msg' => __('apportionment.deleted_error')
                ];
            }

            return $output;
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductList()
    {
        if (request()->ajax()) {
            $id = request()->input('id');

            if (! empty($id)) {
                $lines = PurchaseLine::where('transaction_id', $id)
                    ->select('id')
                    ->get();

                return json_encode($lines);
            }
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function addProductRow()
    {
        if (request()->ajax()) {
            $id = request()->input('id');

            if (! empty($id)) {
                $row_count = request()->input('row_count_pr');

                $line = PurchaseLine::find($id);

                $purchase = Transaction::leftJoin('transaction_has_import_expenses as thie', 'thie.transaction_id', 'transactions.id')
                    ->where('transactions.id', $line->transaction_id)
                    ->select(
                        'transactions.total_before_tax as total_purchase',
                        DB::raw('SUM(thie.amount) as total_import_expenses'),
                        'transactions.purchase_expense_amount'
                    )
                    ->groupBy('transactions.id')
                    ->first();

                $product_vat = $this->taxUtil->getTaxes($line->tax_id);

                $business_id = auth()->user()->business_id;
                $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

                // Number of decimals in purchases
                $business = Business::find($business_id);
                $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
                $decimals_in_purchases = $product_settings['decimals_in_purchases'];

                return view('apportionments.partials.product_rows', compact(
                    'line',
                    'purchase',
                    'product_vat',
                    'row_count',
                    'currency_details',
                    'decimals_in_purchases'
                ));
            }
        }
    }

    /**
     * Save lines of import expenses, purchases and products.
     * 
     * @param  \App\Apportionment  $apportionment
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function save($apportionment, Request $request) {
        // Save import expenses
        $expenses = $request->input('import_expenses');
        $import_expenses = [];

        if (! empty($expenses)) {
            foreach ($expenses as $expense) {
                $new_expense = [
                    'amount' => $this->productUtil->num_uf($expense['import_expense_amount']),
                    'import_expense_id' => $expense['import_expense_id']
                ];

                $import_expenses[] = $new_expense;
            }

            $apportionment->import_expenses()->createMany($import_expenses);
        }

        // Save purchases
        $purchases = $request->input('purchases');
        $transactions = [];

        if (! empty($purchases)) {
            foreach ($purchases as $purchase) {
                $new_purchase = [
                    'transaction_id' => $purchase['transaction_id']
                ];

                $transactions[] = $new_purchase;
            }

            $apportionment->transactions()->createMany($transactions);
        }

        // Update purchase lines
        $product_lines = $request->input('products');

        if (! empty($product_lines)) {
            foreach ($product_lines as $line) {
                $purchase_line = PurchaseLine::find($line['purchase_line_id']);
                $purchase_line->purchase_price = $this->productUtil->num_uf($line['product_unit_cost_exc_tax']);
                $purchase_line->purchase_price_inc_tax = $this->productUtil->num_uf($line['product_unit_cost']);
                $purchase_line->dai_amount = $this->productUtil->num_uf($line['product_dai_amount']);
                $purchase_line->dai_percent = $this->productUtil->num_uf($line['product_dai_percent']);
                $purchase_line->initial_purchase_price = $this->productUtil->num_uf($line['initial_purchase_price']);
                $purchase_line->import_expense_amount = $this->productUtil->num_uf($line['product_import_expenses']);
                $purchase_line->tax_amount = $this->productUtil->num_uf($line['product_vat']);
                $purchase_line->save();
            }
        }

        // Update purchases
        if (! empty($purchases)) {
            foreach ($purchases as $purchase) {
                $transaction = Transaction::find($purchase['transaction_id']);

                // Update import data
                $this->transactionUtil->updateImportData($transaction->id);

                // Update transaction payment status
                $this->transactionUtil->updatePaymentStatus($transaction->id);
            }
        }
    }

    /**
     * Update lines of import expenses, purchases and products.
     * 
     * @param  \App\Apportionment  $apportionment
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function updateApportionment($apportionment, Request $request) {
        // Save import expenses
        $expenses = $request->input('import_expenses');

        if (! empty($expenses)) {
            $saved_ids = [];

            foreach ($expenses as $expense) {
                if (isset($expense['id'])) {
                    $updated_expense = ApportionmentHasImportExpense::find($expense['id']);
                    $updated_expense->amount = $this->productUtil->num_uf($expense['import_expense_amount']);
                    $updated_expense->save();

                    $saved_ids[] = $expense['id'];

                } else {
                    $new_expense = ApportionmentHasImportExpense::create([
                        'amount' => $this->productUtil->num_uf($expense['import_expense_amount']),
                        'import_expense_id' => $expense['import_expense_id'],
                        'apportionment_id' => $apportionment->id
                    ]);

                    $saved_ids[] = $new_expense->id;
                }
            }

            DB::table('apportionment_has_import_expenses')
                ->where('apportionment_id', $apportionment->id)
                ->whereNotIn('id', $saved_ids)
                ->delete();

        } else {
            DB::table('apportionment_has_import_expenses')
                ->where('apportionment_id', $apportionment->id)
                ->delete();
        }

        // Save purchases
        $purchases = $request->input('purchases');

        if (! empty($purchases)) {
            $saved_ids = [];

            foreach ($purchases as $purchase) {
                if (isset($purchase['aht_ids'])) {
                    $updated_purchase = ApportionmentHasTransaction::find($purchase['aht_ids']);
                    $updated_purchase->transaction_id = $purchase['transaction_id'];
                    $updated_purchase->save();

                    $saved_ids[] = $purchase['aht_ids'];

                } else {
                    $new_purchase = ApportionmentHasTransaction::create([
                        'transaction_id' => $purchase['transaction_id'],
                        'apportionment_id' => $apportionment->id
                    ]);

                    $saved_ids[] = $new_purchase->id;
                }
            }

            DB::table('apportionment_has_transactions')
                ->where('apportionment_id', $apportionment->id)
                ->whereNotIn('id', $saved_ids)
                ->delete();

        } else {
            DB::table('apportionment_has_transactions')
                ->where('apportionment_id', $apportionment->id)
                ->delete();
        }

        // Update purchase lines
        $product_lines = $request->input('products');

        if (! empty($product_lines)) {
            foreach ($product_lines as $line) {
                $purchase_line = PurchaseLine::find($line['purchase_line_id']);
                $purchase_line->purchase_price = $this->productUtil->num_uf($line['product_unit_cost_exc_tax']);
                $purchase_line->purchase_price_inc_tax = $this->productUtil->num_uf($line['product_unit_cost']);
                $purchase_line->dai_amount = $this->productUtil->num_uf($line['product_dai_amount']);
                $purchase_line->dai_percent = $this->productUtil->num_uf($line['product_dai_percent']);
                $purchase_line->initial_purchase_price = $this->productUtil->num_uf($line['initial_purchase_price']);
                $purchase_line->import_expense_amount = $this->productUtil->num_uf($line['product_import_expenses']);
                $purchase_line->tax_amount = $this->productUtil->num_uf($line['product_vat']);
                $purchase_line->save();
            }
        }

        // Update purchases
        if (! empty($purchases)) {
            foreach ($purchases as $purchase) {
                $transaction = Transaction::find($purchase['transaction_id']);

                // Update import data
                $this->transactionUtil->updateImportData($transaction->id);

                // Update transaction payment status
                $this->transactionUtil->updatePaymentStatus($transaction->id);
            }
        }
    }

    /**
     * Calculate the average cost of all prorated products.
     * 
     * @param  int  $apportionment_id
     * @return void
     */
    public function calculateAvgCost($apportionment_id)
    {
        $business_id = request()->session()->get('user.business_id');

        $purchases = ApportionmentHasTransaction::where('apportionment_id', $apportionment_id)->get();

        foreach ($purchases as $purchase) {
            $purchase_lines = PurchaseLine::where('transaction_id', $purchase->transaction_id)->get();

            // Purchase date
            $transaction_date = $purchase->transaction->transaction_date;

            // Add time when transaction_date ends at 00:00:00
            $hour = substr($transaction_date, 11, 18);

            if ($hour == '00:00:00' || $hour == '') {
                $transaction_date = substr($transaction_date, 0, 10) . ' ' . substr($purchase->transaction->created_at, 11, 18);
            }

            foreach ($purchase_lines as $purchase_line) {
                // Check if there are several lines of the same product in the purchase
                $pur_lines = PurchaseLine::where('purchase_lines.transaction_id', $purchase->transaction_id)
                    ->join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    ->where('purchase_lines.variation_id', $purchase_line->variation_id)
                    ->select('purchase_lines.*')
                    ->orderBy('purchase_lines.id')
                    ->get();

                $flag_line = $pur_lines->count() > 1 ? 1 : 0;

                $additional_data = [
                    'purchase_line_id' => $purchase_line->id,
                    'transaction_date' => $transaction_date,
                    'flag_line' => $flag_line
                ];

                $this->productUtil->updateAverageCost(
                    $purchase_line->variation_id,
                    $purchase_line->purchase_price_inc_tax,
                    $purchase_line->quantity,
                    true,
                    $additional_data
                );

                // Update purchase kardex
                $kardex = Kardex::where('line_reference', $purchase_line->id)
                    ->where('transaction_id', $purchase_line->transaction_id)
                    ->where('variation_id', $purchase_line->variation_id)
                    ->first();

                if (! empty($kardex)) {
                    $kardex->unit_cost_inputs = $purchase_line->purchase_price_inc_tax;
                    $kardex->total_cost_inputs = $purchase_line->purchase_price_inc_tax * $kardex->inputs_quantity;
                    $kardex->save();
                }

                // Update post purchase transaction kardex
                $purchase_line_after = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    ->where('purchase_lines.variation_id', $purchase_line->variation_id)
                    ->where('transactions.transaction_date', '>=', $transaction_date)
                    ->where('purchase_lines.id', $purchase_line->id)
                    ->first();

                $transaction_sell_lines = TransactionSellLine::join('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    ->whereIn('transactions.type', ['sell', 'sell_transfer', 'stock_adjustment'])
                    ->where('transaction_sell_lines.variation_id', $purchase_line->variation_id);

                if (empty($purchase_line_after)) {
                    $transaction_sell_lines = $transaction_sell_lines->where('transactions.transaction_date', '>=', $transaction_date);

                } else {
                    $transaction_date_after = Transaction::find($purchase_line_after->transaction_id)->transaction_date;

                    $transaction_sell_lines = $transaction_sell_lines->where('transactions.transaction_date', '>=', $transaction_date)
                        ->where('transactions.transaction_date', '<', $transaction_date_after);
                }

                $transaction_sell_lines = $transaction_sell_lines->select('transaction_sell_lines.*')->get();

                if (! empty($transaction_sell_lines)) {
                    foreach ($transaction_sell_lines as $tsl) {
                        $tsl->unit_cost_exc_tax = $purchase_line->purchase_price_inc_tax;
                        $tsl->unit_cost_inc_tax = $purchase_line->purchase_price_inc_tax;
                        $tsl->save();

                        $kardex_after = Kardex::where('line_reference', $tsl->id)
                            ->where('transaction_id', $tsl->transaction_id)
                            ->where('variation_id', $purchase_line->variation_id)
                            ->first();

                        if (! empty($kardex_after)) {
                            $kardex_after->unit_cost_outputs = $purchase_line->purchase_price_inc_tax;
                            $kardex_after->total_cost_outputs = $purchase_line->purchase_price_inc_tax * $kardex_after->outputs_quantity;
                            $kardex_after->save();
                        }
                    }
                }
            }
        }
    }
}
