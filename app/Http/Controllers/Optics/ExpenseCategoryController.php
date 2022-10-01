<?php

namespace App\Http\Controllers\Optics;

use App\ExpenseCategory;
use App\Utils\Util;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class ExpenseCategoryController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $util;

    /**
     * Constructor
     *
     * @param \App\Utils\Util $util
     * @return void
     */
    public function __construct(Util $util)
    {
        $this->util = $util;
        $this->module_name = 'expense_category';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $expense_category = ExpenseCategory::where('expense_categories.business_id', $business_id)
                ->leftjoin('catalogues', 'expense_categories.account_id', '=', 'catalogues.id')
                ->whereNull('expense_categories.deleted_at')
                ->select(
                    'expense_categories.id',
                    'expense_categories.name',
                    DB::raw("CONCAT(COALESCE(catalogues.code,''), ' ', COALESCE(catalogues.name, '')) as account_name")
                );

            return Datatables::of($expense_category)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'Optics\ExpenseCategoryController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".expense_category_modal"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                        &nbsp;
                        <button data-href="{{action(\'Optics\ExpenseCategoryController@destroy\', [$id])}}" class="btn btn-xs btn-danger optics_delete_expense_category"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('optics.expense_category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        return view('optics.expense_category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'code']);
            $input['business_id'] = $request->session()->get('user.business_id');

            $expense_category = ExpenseCategory::create($input);

            # Store binnacle
            $user_id = $request->session()->get('user.id');

            $this->util->registerBinnacle($user_id, $this->module_name, 'create', $expense_category);

            $output = ['success' => true,
                            'msg' => __("expense.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
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
        if (!auth()->user()->can('expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $expense_category = ExpenseCategory::where('business_id', $business_id)->find($id);

            return view('optics.expense_category.edit')
                    ->with(compact('expense_category'));
        }
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
        if (!auth()->user()->can('expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'code']);
                $business_id = $request->session()->get('user.business_id');

                $expense_category = ExpenseCategory::where('business_id', $business_id)->findOrFail($id);

                # Clone record before action
                $expense_category_old = clone $expense_category;

                $expense_category->name = $input['name'];
                // $expense_category->code = $input['code'];
                $expense_category->save();

                # Store binnacle
                $user_id = $request->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'update', $expense_category_old, $expense_category);

                $output = ['success' => true,
                            'msg' => __("expense.updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
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
        if (!auth()->user()->can('expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $expense_category = ExpenseCategory::where('business_id', $business_id)->findOrFail($id);

                # Clone record before action
                $expense_category_old = clone $expense_category;

                $expense_category->delete();

                # Store binnacle
                $user_id = request()->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'delete', $expense_category_old);

                $output = ['success' => true,
                            'msg' => __("expense.deleted_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }
}
