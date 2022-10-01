<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerPortfolio;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Utils\Util;
use App\Employees;
use DB;
use Exception;

class CustomerPortfolioController extends Controller
{

    public function __construct(Util $util)
    {
        $this->util = $util;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('portfolios.view') && !auth()->user()->can('portfolios.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        return view('customer_portfolios.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('portfolios.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $sellers = Employees::SellersDropdown($business_id);
        $code = $this->util->generatePortfolioCode();

        return view('customer_portfolios.create')
            ->with(compact('sellers', 'code'));
    }


    public function getPortfoliosData()
    {
        if (!auth()->user()->can('portfolios.view') && !auth()->user()->can('portfolios.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $portfolios = DB::table('customer_portfolios')
            ->select('customer_portfolios.id', 'customer_portfolios.code', 'customer_portfolios.name', 'customer_portfolios.description', DB::raw("CONCAT(COALESCE(employees.first_name, ''), ' ', COALESCE(employees.last_name, '')) as full_name"))
            ->join('employees', 'employees.id', '=', 'customer_portfolios.seller_id')
            ->where('customer_portfolios.status', 'active')
            ->where('customer_portfolios.business_id', $business_id);
        return DataTables::of($portfolios)
            ->addColumn(
                'action',
                '@can("portfolios.update")
            <button data-href="{{action(\'CustomerPortfolioController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_portfolios_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                &nbsp;
            @endcan
            @can("portfolios.delete")
                <button data-href="{{action(\'CustomerPortfolioController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_portfolios_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
            @endcan'
            )
            ->removeColumn('id')
            ->rawColumns([4])
            ->make(false);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('portfolios.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $portfolio = $request->only(['code', 'name', 'description', 'seller_id']);
            $portfolio['business_id'] = $request->session()->get('user.business_id');
            $portfolio['status'] = 'active';

            $portfolio = CustomerPortfolio::create($portfolio);
            $outpout = [
                'success' => true,
                'data' => $portfolio,
                'msg' => __("crm.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => $e->getMessage()];
        }

        return $outpout;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerPortfolio  $customerPortfolio
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerPortfolio $customerPortfolio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerPortfolio  $customerPortfolio
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('portfolios.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $portfolios = CustomerPortfolio::where('business_id', $business_id)->find($id);
            $sellers = Employees::SellersDropdown($business_id);

            return view('customer_portfolios.edit')
                ->with(compact('portfolios', 'sellers'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerPortfolio  $customerPortfolio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('portfolios.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'description', 'seller_id']);
                $business_id = $request->session()->get('user.business_id');

                $portfolios = CustomerPortfolio::where('business_id', $business_id)->findOrFail($id);
                $portfolios->name = $input['name'];
                $portfolios->description = $input['description'];
                $portfolios->seller_id = $input['seller_id'];
                $portfolios->save();

                $outpout = [
                    'success' => true,
                    'data' => $portfolios,
                    'msg' => __("customer.updated_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $outpout = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $outpout;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerPortfolio  $customerPortfolio
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('portfolios.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $customers = Customer::where('business_id', $business_id)
                    ->where('customer_portfolio_id', $id)
                    ->get();

                DB::beginTransaction();

                if (!count($customers)) {
                    $portfolios = CustomerPortfolio::where('business_id', $business_id)->findOrFail($id);
                    $portfolios->delete();

                    $outpout = [
                        'success' => true,
                        'msg' => __("customer.deleted_success")
                    ];

                    DB::commit();
                } else {
                    $outpout = [
                        'success' => false,
                        'msg' => __("customer.portfolios_has_assoc_customers")
                    ];
                    DB::rollBack();
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $outpout = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $outpout;
        }
    }
}
