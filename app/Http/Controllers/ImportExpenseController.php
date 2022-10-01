<?php

namespace App\Http\Controllers;

use App\Business;
use App\ImportExpense;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ImportExpenseController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param  \App\Utils\TransactionUtil  $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('import_expense.view') && ! auth()->user()->can('import_expense.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $business_id = $business_id = request()->session()->get('user.business_id');
            
            $import_expenses = ImportExpense::where('business_id', $business_id)
                ->select('name', 'type', 'id');

            return Datatables::of($import_expenses)
                ->addColumn(
                    'action',
                    '@can("import_expenses.update")
                    <button data-href="{{ action(\'ImportExpenseController@edit\', [$id]) }}"
                        class="btn btn-xs btn-primary edit_import_expenses_button">
                        <i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")
                    </button>
                    &nbsp;
                    @endcan
                    @can("import_expenses.delete")
                    <button data-href="{{ action(\'ImportExpenseController@destroy\', [$id]) }}"
                        class="btn btn-xs btn-danger delete_import_expenses_button">
                        <i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")
                    </button>
                    @endcan'
                )
                ->editColumn(
                    'type',
                    '{{ __("import_expense." . $type) }}'
                )
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('import_expense.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('import_expense.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('import_expense.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('import_expense.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'type']);

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $import_expense = ImportExpense::create($input);

            $output = [
                'success' => true,
                'data' => $import_expense,
                'msg' => __("import_expense.added_success")
            ];

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ImportExpense  $importExpense
     * @return \Illuminate\Http\Response
     */
    public function show(ImportExpense $importExpense)
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
        if (! auth()->user()->can('import_expense.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $import_expense = ImportExpense::findOrFail($id);

            return view('import_expense.edit')->with(compact('import_expense'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ImportExpense  $importExpense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('import_expense.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'type']);

                $import_expense = ImportExpense::findOrFail($id);
                $import_expense->fill($input);
                $import_expense->save();

                $output = [
                    'success' => true,
                    'msg' => __("import_expense.updated_success")
                ];

            } catch (\Exception $e) {
                \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('import_expense.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $import_expense = ImportExpense::findOrFail($id);

                $import_expense->delete();

                $output = [
                    'success' => true,
                    'msg' => __("import_expense.deleted_success")
                ];

            } catch (\Exception $e) {
                \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Retrieves import expenses.
     *
     * @return json
     */
    public function getImportExpenses()
    {
        if (request()->ajax()) {
            $term = request()->term;
            $type = request()->type;

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = auth()->user()->business_id;

            $import_expenses = ImportExpense::where('business_id', $business_id)
                ->where('type', $type)
                ->where('name', 'like', '%' . $term . '%')
                ->select('id', 'name as text')
                ->get();

            return json_encode($import_expenses);
        }
    }

    /**
     * Retrieves import expesnse row.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImportExpenseRow()
    {
        if (request()->ajax()) {
            $id = request()->input('id');

            if (! empty($id)) {
                $row_count = request()->input('row_count_ie');

                $import_expense = ImportExpense::find($id);

                $business_id = auth()->user()->business_id;
                $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

                // Number of decimals in purchases
                $business = Business::find($business_id);
                $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);
                $decimals_in_purchases = $product_settings['decimals_in_purchases'];

                return view('import_expense.partials.import_expense_row', compact(
                    'import_expense',
                    'row_count',
                    'currency_details',
                    'decimals_in_purchases'
                ));
            }
        }
    }
}
