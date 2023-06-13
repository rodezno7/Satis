<?php

namespace App\Http\Controllers;

use App\TaxRate;
use App\TaxGroup;
use App\Transaction;
use App\TaxRateTaxGroup;

use DB;
use Datatables;
use App\Utils\TaxUtil;
use App\Utils\ProductUtil;
use Illuminate\Http\Request;


class TaxGroupController extends Controller
{
    /**
     * All utils instance
     */
    protected $types;
    protected $taxUtil;
    private $productUtil;
    private $clone_product;

    /**
     * Constructor
     * @param TaxUtils $taxUtil;
     * @return void
     */

    public function __construct(TaxUtil $taxUtil, ProductUtil $productUtil) {
        $this->taxUtil = $taxUtil;
        $this->productUtil = $productUtil;

        /** types */
        $this->types = ['products', 'contacts'];

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
        if (!auth()->user()->can('tax_group.view') && !auth()->user()->can('tax_group.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $tax_group = TaxGroup::where('business_id', $business_id)
                ->with('tax_rates');

                return Datatables::of($tax_group)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'TaxGroupController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".tax_group_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        <button data-href="{{action(\'TaxGroupController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_tax_group_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->editColumn('rates', function ($row) {
                    $rates = [];
                    foreach ($row->tax_rates as $tr) {
                        $rates[] = $tr->percent;
                    }
                    return implode(', ', $rates);
                })
                ->editColumn('type', '{{ __("lang_v1." . $type) }}')
                ->editColumn('taxes', function ($row) {
                    $taxes = [];
                    foreach ($row->tax_rates as $tr) {
                        $taxes[] = $tr->name;
                    }
                    return implode(' + ', $taxes);
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
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
        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
            ->pluck('name', 'id');

        $types = $this->types;

        return view('tax_group.create')
                ->with(compact('taxes', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('tax_group.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $tax_group = new TaxGroup();
            $tax_group->type = $request->input('types');
            $tax_group->description = $request->input('name');
            $tax_group->business_id = $request->session()->get('user.business_id');
            $tax_group->created_by = $request->session()->get('user.id');

            DB::beginTransaction();

            $tax_group->save();

            foreach($request->input('taxes') as $tax){
                $tax_group->tax_rates()->attach($tax);
            }

            /** Sync tax group */
            if ($this->clone_product) {
                $this->productUtil->syncTaxGroup($tax_group->id, $tax_group->description);
            }

            DB::commit();
            
            $output = ['success' => true,
                            'msg' => __("tax_rate.tax_group_added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            DB::rollback();

            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('tax_group.edit')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $tax_group = TaxGroup::find($id);

            $tax_rate_tax_group = TaxRateTaxGroup::where('tax_group_id', $id)->pluck('tax_rate_id');

            $taxes = TaxRate::where('business_id', $business_id)->pluck('name', 'id');

            $types = $this->types;

            return view('tax_group.edit')
                ->with(compact('tax_group', 'taxes', 'tax_rate_tax_group', 'types'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('tax_group.edit')) {
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            try {
                $tax_group = TaxGroup::findOrFail($id);
                $description = $tax_group->description;

                $tax_group->type = $request->input('types');
                $tax_group->description = $request->input('name');

                DB::beginTransaction();
                
                $tax_group->save();

                /** Get transactions for tax groups */
                $trans = Transaction::where('tax_id', $id)->get();

                /** Has no transactions? */
                if(!$trans->count()){

                    /** Sync records on pivot table */
                    $tax_group->tax_rates()->sync($request->input('taxes'));

                    /** Sync tax group */
                    if ($this->clone_product) {
                        $this->productUtil->syncTaxGroup($tax_group->id, $description);
                    }

                    $output = ['success' => true,
                                'msg' => __("tax_rate.tax_group_updated_success")
                            ];
                    
                    DB::commit();

                } else {
                    $output = ['success' => false,
                        'msg' => __("tax_rate.tax_group_has_assoc_trans")
                    ];

                    DB::rollBack();
                }

            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                DB::rollback();

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
     * @param  Interger  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('tax_group.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if(request()->ajax()){
            try {
                $tax_group = TaxGroup::findOrFail($id);

                DB::beginTransaction();

                /** Get transactions for tax groups */
                $trans = Transaction::where('tax_id', $id);

                /** Has no transactions? */
                if(!$trans->count()){

                    /** Delete all records on pivot table */
                    $tax_group->tax_rates()->detach();

                    $old_tax_group = clone $tax_group;

                    /** Delete tax group */
                    $tax_group->delete();

                    /** Sync tax group */
                    if ($this->clone_product) {
                        $this->productUtil->syncTaxGroup($tax_group->id, "", $old_tax_group);
                    }

                    $output = ['success' => true,
                                'msg' => __("tax_rate.tax_group_deleted_success")
                            ];
                    
                    DB::commit();

                } else {
                    $output = ['success' => false,
                        'msg' => __("tax_rate.tax_group_has_assoc_trans")
                    ];

                    DB::rollBack();
                }

            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                DB::rollback();

                $output = ['success' => false,
                                'msg' => __("messages.something_went_wrong")
                            ];
            }

            return $output;
        }
    }

    /**
     * Return tax groups for current business
     */

    public function getTaxGroups() {
        if(request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'products');

            return json_encode($tax_groups);
        }
    }

    /**
     * get tax group percent
     * 
     * @param int $tax_group_id
     * @return float 
     */
    public function getTaxPercent(){
        if(request()->ajax()){
            $tax_group_id = request()->input('tax_group_id', null);

            $tax_percent = $this->taxUtil->getTaxPercent($tax_group_id);

            return $tax_percent;
        }
    }

    public function getTaxes() {
        if(request()->ajax()) {
            $tax_group_id = request()->input('tax_group_id', null);
            
            $percent = $this->taxUtil->getTaxes($tax_group_id);

            return $percent;
        }
    }
}
