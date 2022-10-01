<?php

namespace App\Http\Controllers;

use App\TaxRate;
use App\TaxGroup;
use App\Transaction;
use DB;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Utils\TaxUtil;

class TaxRateController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $taxUtil;

    /**
     * Constructor
     *
     * @param TaxUtil $taxUtil
     * @return void
     */
    public function __construct(TaxUtil $taxUtil)
    {
        $this->taxUtil = $taxUtil;
        $this->type = ["purchase", "sell"];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('tax_rate.view') && !auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $tax_rates = TaxRate::where('business_id', $business_id)
                ->select('name', 'type', 'percent',
                    DB::raw("IFNULL(min_amount, 'N/A')"),
                    DB::raw("IFNULL(max_amount, 'N/A')"), 'id');

            return Datatables::of($tax_rates)
                ->addColumn(
                    'action',
                    '@can("tax_rate.update")
                    <button data-href="{{action(\'TaxRateController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_tax_rate_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("tax_rate.delete")
                        <button data-href="{{action(\'TaxRateController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_tax_rate_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->editColumn('type', '{{ __("tax_rate." . $type) }}')
                ->removeColumn('id')
                ->rawColumns([5])
                ->make(false);
        }

        return view('tax_rate.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        $type = $this->type;

        return view('tax_rate.create', compact("type"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'type', 'percent', 'min_amount', 'max_amount']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['percent'] = $this->taxUtil->num_uf($input['percent']);

            $tax_rate = TaxRate::create($input);

            $output = ['success' => true,
                            'data' => $tax_rate,
                            'msg' => __("tax_rate.added_success")
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
        if (!auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $tax_rate = TaxRate::where('business_id', $business_id)->find($id);

            $type = $this->type;

            return view('tax_rate.edit')
                ->with(compact('tax_rate', 'type'));
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
        if (!auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'type', 'percent', 'min_amount', 'max_amount']);
                $business_id = $request->session()->get('user.business_id');

                $tax_rate = TaxRate::where('business_id', $business_id)->findOrFail($id);

                /** Get group ids associated to tax rate */
                $tax_group_id = [];
                foreach ($tax_rate->tax_groups as $tg) {
                    $tax_group_id [] = $tg->pivot->tax_group_id;
                }

                $trans = Transaction::whereIn('tax_id', $tax_group_id)->get();

                if(!$trans->count()) {
                    $tax_rate->name = $input['name'];
                    $tax_rate->type = $input['type'];
                    $tax_rate->percent = $this->taxUtil->num_uf($input['percent']);
                    $tax_rate->min_amount = !empty($input['min_amount']) ? $input['min_amount'] : null;
                    $tax_rate->max_amount = !empty($input['max_amount']) ? $input['max_amount'] : null;
                    
                    $tax_rate->save();

                    $output = ['success' => true,
                            'msg' => __("tax_rate.updated_success")
                            ];

                } else {
                    DB::rollBack();

                    $output = ['success' => false,
                        'msg' => __("tax_rate.tax_group_has_assoc_trans")
                    ];
                }

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
        if (!auth()->user()->can('tax_rate.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $tax_rate = TaxRate::where('business_id', $business_id)->findOrFail($id);

                /** Has no associated groups? */
                if(!$tax_rate->tax_groups->count()) {
                    $tax_rate->delete();

                    $output = ['success' => true,
                            'msg' => __("tax_rate.updated_success")
                            ];
                } else {
                    DB::rollBack();

                    $output = ['success' => false,
                        'msg' => __("tax_rate.tax_rate_has_assoc_group")
                    ];
                }
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
