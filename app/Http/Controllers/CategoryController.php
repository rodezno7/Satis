<?php

namespace App\Http\Controllers;

use App\Business;
use App\Category;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;

use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;
    private $productUtil;

    /**
     * Constructor
     *
     * @param \App\Utils\TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;

        // Binnacle data
        $this->module_name = 'category';

        /** clone product config */
        $this->clone_product = config('app.clone_product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $category = Category::where('business_id', $business_id)
            ->select(['name', 'short_code', 'id', 'parent_id']);

            return Datatables::of($category)
            ->addColumn(
                'action',
                '@can("category.update")
                <button data-href="{{action(\'CategoryController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                &nbsp;
                @endcan
                @can("category.delete")
                <button data-href="{{action(\'CategoryController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                @endcan'
            )
            ->editColumn('name', function ($row) {
                if ($row->parent_id != 0) {
                    return '--' . $row->name;
                } else {
                    return $row->name;
                }
            })
            ->removeColumn('id')
            ->removeColumn('parent_id')
            ->rawColumns([2])
            ->make(false);
        }

        return view('category.index');
    }

    public function getCategoriesData()
    {
        $categories = DB::table('categories as categorie')
        ->leftJoin('catalogues as catalogue', 'catalogue.id', '=', 'categorie.catalogue_id')
        ->select('categorie.id', 'categorie.name as name', DB::raw('CONCAT(catalogue.code, " ", catalogue.name) as account'))
        ->where('parent_id', 0)
        ->whereNull('categorie.deleted_at')
        ->get();
        return DataTables::of($categories)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->select(['name', 'short_code', 'id'])
            ->get();

        $parent_categories = [];

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $parent_categories[$category->id] = $category->name;
            }
        }

        $quick_add = false;

        if (! empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $type = null;

        if (! empty(request()->input('type'))) {
            $type = request()->input('type');
        }

        return view('category.create')
            ->with(compact('parent_categories', 'quick_add', 'type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'short_code']);
            if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                $input['parent_id'] = $request->input('parent_id');
            } else {
                $input['parent_id'] = 0;
            }
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            DB::beginTransaction();

            $category = Category::create($input);

            // Store binnacle
            $this->transactionUtil->registerBinnacle(
                $this->module_name,
                'create',
                $category->name,
                $category
            );

            /** Clone category */
            if ($this->clone_product) {
                $this->productUtil->syncCategory($category->id, $category->name);
            }

            DB::commit();

            $output = [
                'success' => true,
                'data' => $category,
                'msg' => __("category.added_success")
            ];
            
        } catch (\Exception $e) {
            DB::rollback();
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
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category = Category::where('id', $category->id)
            ->with('catalogue')
            ->first();
        return response()->json($category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $category = Category::where('business_id', $business_id)->find($id);
            
            $parent_categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->where('id', '!=', $id)
            ->pluck('name', 'id');
            
            $is_parent = false;
            
            if ($category->parent_id == 0) {
                $is_parent = true;
                $selected_parent = null;
            } else {
                $selected_parent = $category->parent_id ;
            }

            return view('category.edit')
            ->with(compact('category', 'parent_categories', 'is_parent', 'selected_parent'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    public function updateCatalogueId(Request $request)
    {
        if (request()->ajax()) {
            try {
                $id = $request->input('id');
                $name = $request->input('name');
                $catalogue_id = $request->input('catalogue_id');

                $category = Category::findOrFail($id);

                // Clone record before action
                $category_old = clone $category;

                $category->name = $name;
                $category->catalogue_id = $catalogue_id;
                $category->save();

                // Store binnacle
                $this->transactionUtil->registerBinnacle(
                    $this->module_name,
                    'update',
                    $category->name,
                    $category_old,
                    $category
                );

                $output = [
                    'success' => true,
                    'msg' => __("category.updated_success")
                ];
            }
            catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'short_code']);
                $business_id = $request->session()->get('user.business_id');

                DB::beginTransaction();

                $category = Category::where('business_id', $business_id)->findOrFail($id);
                $name = $category->name;

                // Clone record before action
                $category_old = clone $category;

                $category->name = $input['name'];
                $category->short_code = $input['short_code'];
                if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                    $category->parent_id = $request->input('parent_id');
                } else {
                    $category->parent_id = 0;
                }
                $category->save();

                // Store binnacle
                $this->transactionUtil->registerBinnacle(
                    $this->module_name,
                    'update',
                    $category->name,
                    $category_old,
                    $category
                );

                /** Sync category */
                if ($this->clone_product) {
                    $this->productUtil->syncCategory($category->id, $name);
                }

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __("category.updated_success")
                ];
            } catch (\Exception $e) {
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('category.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                DB::beginTransaction();

                $category = Category::where('business_id', $business_id)->findOrFail($id);

                // Clone record before action
                $category_old = clone $category;

                $category->delete();

                // Store binnacle
                $this->transactionUtil->registerBinnacle(
                    $this->module_name,
                    'delete',
                    $category_old->name,
                    $category_old
                );

                /** Sync category */
                if ($this->clone_product) {
                    $this->productUtil->syncCategory($id, "", $category_old, $this->transactionUtil, $this->module_name);
                }

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __("category.deleted_success")
                ];

            } catch (\Exception $e) {
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
}
