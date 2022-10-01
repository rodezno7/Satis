<?php

namespace App\Http\Controllers;

use App\Business;
use App\Catalogue;
use App\ExpenseCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('expense_category.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {        
            $expense_category = DB::table('expense_categories')
                ->select('expense_categories.id', 'expense_categories.name', DB::raw("CONCAT(COALESCE(catalogues.code,''),' ',COALESCE(catalogues.name,'')) as account_name"))
                ->leftjoin('catalogues', 'expense_categories.account_id', '=', 'catalogues.id')
                ->whereNull('expense_categories.deleted_at')
                ->where('expense_categories.business_id', $business_id);

            return Datatables::of($expense_category)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'ExpenseCategoryController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".expense_category_modal"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                        &nbsp;
                        <button data-href="{{action(\'ExpenseCategoryController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_expense_category"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }
        $accountBusiness = Business::where('id', $business_id)->first();
        $verifiedAccount = $accountBusiness->accounting_expense_id != null || !empty($accountBusiness->accounting_expense_id) ? true : false;

        return view('expense_category.index', compact('verifiedAccount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('expense_category.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $expense_account_id = Business::where('id', $business_id)->first();
        $expense_prefix = Catalogue::where('id', $expense_account_id->accounting_expense_id)->first();

        $expenses_accounts = Catalogue::where('code', 'like', $expense_prefix->code . '%')
            ->select('id', DB::raw("CONCAT(COALESCE(code,''),' ',COALESCE(name,'')) as account_name"))
            ->pluck('account_name', 'id');

        return view('expense_category.create')
            ->with(compact('expenses_accounts', 'expense_account_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('expense_category.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'account_id']);
            $input['business_id'] = $request->session()->get('user.business_id');

            ExpenseCategory::create($input);
            $output = [
                'success' => true,
                'msg' => __("expense.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ExpenseCategory  $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseCategory $expenseCategory)
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
        if (!auth()->user()->can('expense_category.update')) {
            abort(403, 'Unauthorized action.');
        }

        // if (request()->ajax()) {
        $business_id = request()->session()->get('user.business_id');
        $expense_category = ExpenseCategory::where('business_id', $business_id)->find($id);
        $expense_account_id = Business::where('id', $business_id)->first();
        $expense_prefix = Catalogue::where('id', $expense_account_id->accounting_expense_id)->first();

        if (!is_object($expense_prefix)) {
            $output = [
                'success' => false,
                'msg' => 'Aun no has configurado la cuenta principal de gastos de la empresa'
            ];
            return $output;
        } else {
            $expenses_accounts = Catalogue::where('code', 'like', $expense_prefix->code . '%')
                ->select('id', DB::raw("CONCAT(COALESCE(code,''),' ',COALESCE(name,'')) as account_name"))
                ->pluck('account_name', 'id');

            $expense_account_id = Business::where('id', $business_id)->first();
            $expense_prefix = Catalogue::where('id', $expense_account_id->accounting_expense_id)->first();

            $expenses_accounts = Catalogue::where('code', 'like', $expense_prefix->code . '%')
                ->select('id', DB::raw("CONCAT(COALESCE(code,''),' ',COALESCE(name,'')) as account_name"))
                ->pluck('account_name', 'id');

            return view('expense_category.edit')
                ->with(compact('expense_category', 'expenses_accounts'));
        }
        // }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('expense_category.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'account_id']);
                $business_id = $request->session()->get('user.business_id');

                $expense_category = ExpenseCategory::where('business_id', $business_id)->findOrFail($id);
                $expense_category->name = $input['name'];
                $expense_category->account_id = $input['account_id'];
                $expense_category->save();

                $output = [
                    'success' => true,
                    'msg' => __("expense.updated_success")
                ];
            } catch (\Exception $e) {
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('expense_category.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $expense_category = ExpenseCategory::where('business_id', $business_id)->findOrFail($id);
                $expense_category->delete();

                $output = [
                    'success' => true,
                    'msg' => __("expense.deleted_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
