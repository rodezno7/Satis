<?php

namespace App\Http\Controllers;

use DB;
use App\Catalogue;
use App\InvoiceLayout;
use App\InvoiceScheme;
use App\BusinessLocation;
use App\AccountBusinessLocation;
    
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

use App\Utils\Util;
use App\Utils\ModuleUtil;

class BusinessLocationController extends Controller
{
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('location.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $locations = BusinessLocation::where('business_locations.business_id', $business_id)
                ->select(['location_id', 'business_locations.name', 'city', 'state',
                    'business_locations.id']);

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $locations->whereIn('business_locations.id', $permitted_locations);
            }

            return Datatables::of($locations)
                ->addColumn(
                    'action',
                    '<button type="button" data-href="{{action(\'BusinessLocationController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".location_edit_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button type="button" data-href="{{action(\'BusinessLocationController@getAccountingAccountByLocation\', [$id])}}" class="btn btn-xs btn-info btn-modal" data-container=".add_update_accounting_account_modal"><i class="glyphicon glyphicon-edit"></i> @lang("accounting.accounts")</button>
                    '
                )
                ->removeColumn('id')
                ->rawColumns([4])
                ->make(false);
        }

        return view('business_location.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('location.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for location quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('locations', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('locations', $business_id);
        }

        $invoice_layouts = InvoiceLayout::where('business_id', $business_id)
                            ->get()
                            ->pluck('name', 'id');

        $invoice_schemes = InvoiceScheme::where('business_id', $business_id)
                            ->get()
                            ->pluck('name', 'id');
        return view('business_location.create')
                    ->with(compact('invoice_layouts', 'invoice_schemes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('location.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            } elseif (!$this->moduleUtil->isQuotaAvailable('locations', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('locations', $business_id);
            }

            $input = $request->only(['name', 'landmark', 'city', 'state', 'country', 'zip_code', 'invoice_scheme_id',
                'invoice_layout_id', 'mobile', 'alternate_number', 'email', 'website', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'location_id']);

            $input['business_id'] = $business_id;

            //Update reference count
            $ref_count = $this->commonUtil->setAndGetReferenceCount('business_location');

            if (empty($input['location_id'])) {
                $input['location_id'] = $this->commonUtil->generateReferenceNumber('business_location', $ref_count);
            }

            $location = BusinessLocation::create($input);

            //Create a new permission related to the created location
            Permission::create(['name' => 'location.' . $location->id ]);

            $output = ['success' => true,
                            'msg' => __("business.business_location_added_success")
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
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('location.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location = BusinessLocation::where('business_id', $business_id)
                                    ->find($id);
        $invoice_layouts = InvoiceLayout::where('business_id', $business_id)
                            ->get()
                            ->pluck('name', 'id');
        $invoice_schemes = InvoiceScheme::where('business_id', $business_id)
                            ->get()
                            ->pluck('name', 'id');

        return view('business_location.edit')
                ->with(compact('location', 'invoice_layouts', 'invoice_schemes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('location.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'location_id', 'landmark', 'city', 'state',
                'country', 'zip_code', 'invoice_scheme_id', 'invoice_layout_id', 'mobile',
                'alternate_number', 'email', 'website', 'custom_field1', 'custom_field2',
                'custom_field3', 'custom_field4']);
            
            $business_id = $request->session()->get('user.business_id');

            BusinessLocation::where('business_id', $business_id)
                            ->where('id', $id)
                            ->update($input);

            $output = ['success' => true,
                            'msg' => __('business.business_location_updated_success')
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
     * Remove the specified resource from storage.
     *
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * get add/update accounting accounts by location
     * @param int $location_id
     */
    public function getAccountingAccountByLocation($location_id){
        $location =
            BusinessLocation::where('id', $location_id)
                ->select('id', 'name')
                ->first();

        $account_location =
            AccountBusinessLocation::where('location_id', $location_id)
                ->first();

        $account_location_names = [];
        if($account_location){
            $al = $account_location;

            $account_location_names =
                Catalogue::whereIn('id',
                    [
                        $al->general_cash_id,
                        $al->inventory_account_id,
                        $al->account_receivable_id,
                        $al->vat_final_customer_id,
                        $al->vat_taxpayer_id,
                        $al->supplier_account_id,
                        $al->provider_account_id,
                        $al->sale_cost_id,
                        $al->sale_expense_id,
                        $al->admin_expense_id,
                        $al->financial_expense_id,
                        $al->local_sale_id,
                        $al->exports_id,
                    ]
                )->select(DB::raw('CONCAT(code, " ", name) as account_name'), 'id')
                ->get()
                ->pluck('account_name', 'id');
        }
        
        return view('business_location.partials.account_location',
            compact('location', 'account_location', 'account_location_names'));
    }

        /**
     * post add/Update accounting accounts by location
     * @param int $location_id
     */
    public function postAccountingAccountByLocation(Request $request){
        try{
            AccountBusinessLocation::updateOrCreate(
                ['location_id' => $request->location_id],
                [
                    'general_cash_id' => $request->general_cash_id,
                    'inventory_account_id' => $request->inventory_account_id,
                    'account_receivable_id' => $request->account_receivable_id,
                    'vat_final_customer_id' => $request->vat_final_customer_id,
                    'vat_taxpayer_id' => $request->vat_taxpayer_id,
                    'supplier_account_id' => $request->supplier_account_id,
                    'provider_account_id' => $request->provider_account_id,
                    'sale_cost_id' => $request->sale_cost_id,
                    'sale_expense_id' => $request->sale_expense_id,
                    'admin_expense_id' => $request->admin_expense_id,
                    'financial_expense_id' => $request->financial_expense_id,
                    'local_sale_id' => $request->local_sale_id,
                    'exports_id' => $request->exports_id
                ]
            );

            $output = ['success' => true,
                'msg' => __("business.account_location_saved_successfully")];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;   
    }

     /**
     * Checks if the given location id already exist for the current business.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkLocationId(Request $request)
    {
        $location_id = $request->input('location_id');

        $valid = 'true';
        if (!empty($location_id)) {
            $business_id = $request->session()->get('user.business_id');
            $hidden_id = $request->input('hidden_id');

            $query = BusinessLocation::where('business_id', $business_id)
                            ->where('location_id', $location_id);
            if (!empty($hidden_id)) {
                $query->where('id', '!=', $hidden_id);
            }
            $count = $query->count();
            if ($count > 0) {
                $valid = 'false';
            }
        }
        echo $valid;
        exit;
    }
}
