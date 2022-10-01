<?php

namespace App\Http\Controllers;

use App\City;
use App\Zone;
use App\State;
use App\Contact;
use App\Country;
use App\Category;
use App\Customer;
use App\Variation;
use App\Oportunity;
use App\PaymentTerm;
use App\BusinessType;
use App\CustomerGroup;
use App\Utils\TaxUtil;
use App\CRMContactMode;
use App\FollowCustomer;
use App\CustomerContact;
use App\BusinessLocation;
use App\CRMContactReason;
use App\CustomerPortfolio;
use App\SellingPriceGroup;
use App\FollowOportunities;
use Illuminate\Http\Request;
use App\FollowCustomersHasProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\FollowOportunitiesHasProduct;
use Yajra\DataTables\Facades\DataTables;

class OportunityController extends Controller
{
    public function __construct(TaxUtil $taxUtil)
    {
        $this->taxUtil = $taxUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('oportunities.view') && !auth()->user()->can('oportunities.create')) {
            abort(403, 'Unauthorized action.');
        }

        $type = request()->get('type');

        if ($type != 'all_oportunities' && $type != 'my_oportunities') {
            die("Not Found");
        }

        $business_id = request()->session()->get('user.business_id');
        //Llenar select de Contact Reason
        $contactreason = CRMContactReason::forDropdown($business_id);
        //Llenar select de Known By
        $known_by = CRMContactMode::forDropdown($business_id);
        //Llenar select de Contact Mode
        $contactmode = CRMContactMode::forDropdown($business_id);
        //Llenar select de Category
        $categories = Category::forDropdown($business_id);
        $countries = Country::forDropdown($business_id);
        $locations = BusinessLocation::select('name', 'id')->where('business_id', $business_id)->get();
        $products = Variation::join('products', 'products.id', '=', 'variations.product_id')
            ->select('products.name as name_product', 'variations.name as name_variation', 'variations.id', 'variations.sub_sku', 'products.sku')
            ->where('business_id', $business_id)
            ->where('products.clasification', '<>', 'kits')
            ->where('products.clasification', '<>', 'service')
            ->where('products.status', 'active')
            ->get();

        return view('oportunity.index', compact(
            'contactreason',
            'known_by',
            'categories',
            'contactmode',
            'countries',
            'products',
            'locations',
            'type'
        ));
    }

    public function getOportunityData()
    {
        if (!auth()->user()->can('oportunities.view') && !auth()->user()->can('oportunities.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = auth()->user()->business_id;
        $user_id = auth()->user()->id;
        $type = request()->get('type');
        $oportunity = Oportunity::join('crm_contact_reasons', 'oportunities.contact_reason_id', 'crm_contact_reasons.id')
            ->join('users as user', 'user.id', 'oportunities.created_by')
            ->where('oportunities.business_id', $business_id)
            ->where('oportunities.status', 'oportunity')
            ->select(
                DB::raw("upper(oportunities.contact_type) as contact_type"),
                DB::raw("crm_contact_reasons.name as reason"),
                'oportunities.name',
                'oportunities.company',
                'oportunities.id',
                'oportunities.contact_date',
                'oportunities.created_by',
                DB::raw('CONCAT(user.first_name, " ", user.last_name) as full_name_user')
            );

        if ($type != "all_oportunities") {
            $oportunity->where('oportunities.created_by', $user_id);
        }

        // Date filter
        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = trim(request()->start_date);
            $end =  trim(request()->end_date);
            $oportunity->whereBetween('oportunities.contact_date', [$start, $end]);
        }

        return DataTables::of($oportunity)
            ->addColumn(
                'actions',
                function ($row) {
                    $html = '<div class="btn-group">
                <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                    if (auth()->user()->can('oportunities.view')) {
                        $html .= '<li><a href="/follow-oportunities/' . $row->id . '" class="edit_pos_button"><i class="glyphicon glyphicon-eye-open edit-glyphicon"></i> ' . __("Ver") . '</a></li>';
                    }

                    if (auth()->user()->can('oportunities.update')) {
                        $html .= '<li><a href="#" data-href="' . action('OportunityController@edit', [$row->id]) . '" class="edit_oportunity_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }

                    if (auth()->user()->can('follow_oportunities.create')) {
                        $html .= '<li><a data-href="' . action('FollowOportunitiesController@create', [$row->id]) . '" data-container=".oportunities_modal" class="btn-modal" style="cursor:pointer;"><i class="glyphicon probar glyphicon-comment"></i> ' . __("crm.tracing") . '</a></li>';
                    }

                    if (auth()->user()->can('customer.create')) {
                        $html .= '<li><a href="#" data-href="' . action('OportunityController@createCustomer', [$row->id]) . '" class="convert_customer_button"><i class="fa fa-star"></i> ' . __("crm.convert_to_customer") . '</a></li>';
                    }

                    if (auth()->user()->can('oportunities.delete')) {
                        $html .= '<li><a href="#" onClick="deleteOportunity(' . $row->id . ')"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '</ul></div>';
                    return $html;
                }
            )

            ->rawColumns(['actions'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('oportunities.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Llenar select de Contact Reason
        $contactreason = CRMContactReason::forDropdown($business_id);

        //Llenar select de Known By
        $known_by = CRMContactMode::forDropdown($business_id);

        //Llenar select de Contact Mode
        $contactmode = CRMContactMode::where('name', 'not like', "%cliente%")->pluck('name', 'id');

        //Llenar select de Category
        $categories = Category::forDropdown($business_id);

        $countries = Country::forDropdown($business_id);

        return view('oportunity.create')
            ->with(compact('contactreason', 'known_by', 'categories', 'contactmode', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('oportunities.create')) {
            abort(403, 'Unauthorized action.');
        }

        $chk_not_found = $request->input('chk_not_found');

        try {

            $oportunity = $request->only(['contact_type', 'contact_date', 'contact_reason_id', 'name', 'company', 'charge', 'email', 'contacts', 'known_by', 'refered_id', 'contact_mode_id', 'social_user', 'country_id', 'state_id', 'city_id']);
            $oportunity['business_id'] = $request->session()->get('user.business_id');
            $oportunity['created_by'] = $request->session()->get('user.id');
            $oportunity['status'] = "oportunity";

            if ($chk_not_found) {
                $oportunity['product_not_found'] = 1;
                $oportunity['products_not_found_desc'] = $request->input('products_not_found_desc');
            } else {
                $oportunity['product_not_found'] = 0;
                $oportunity['product_cat_id'] = $request->input('product_cat_id');
            }

            $oportunity = Oportunity::create($oportunity);
            $outpout = [
                'success' => true,
                'data' => $oportunity,
                'msg' => __("crm.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $outpout;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Oportunity  $oportunity
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('oportunities.view')) {
            abort(403, 'Unauthorized action.');
        }

        $oportunity = DB::table('oportunities as oportunity')
            ->leftJoin('crm_contact_reasons as reason', 'reason.id', '=', 'oportunity.contact_reason_id')
            ->leftJoin('crm_contact_modes as mode', 'mode.id', '=', 'oportunity.contact_mode_id')
            ->leftJoin('crm_contact_modes as known', 'known.id', '=', 'oportunity.known_by')
            ->leftJoin('contacts as customer', 'customer.id', '=', 'oportunity.refered_id')
            ->leftJoin('countries as country', 'country.id', '=', 'oportunity.country_id')
            ->leftJoin('states as state', 'state.id', '=', 'oportunity.state_id')
            ->leftJoin('cities as city', 'city.id', '=', 'oportunity.city_id')
            ->leftJoin('categories as category', 'category.id', '=', 'oportunity.product_cat_id')
            ->select('oportunity.*', 'reason.name as reason', 'mode.name as mode', 'country.name as country', 'state.name as state', 'city.name as city', 'known.name as knowned', 'customer.name as customer', 'category.name as category')
            ->where('oportunity.id', $id)
            ->first();

        return response()->json($oportunity);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Oportunity  $oportunity
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('oportunities.update')) {
            abort(403, 'Unauthorized action.');
        }

        //if (request()->ajax()) {

        $business_id = request()->session()->get('user.business_id');

        //Llenar select de Known By
        $known_by = CRMContactMode::forDropdown($business_id);

        $oportunity = Oportunity::where('business_id', $business_id)->find($id);

        //Llenar select de Contact Reason
        $contactreason = CRMContactReason::forDropdown($business_id);

        //Llenar select de Contact Mode
        $contactmode = CRMContactMode::forDropdown($business_id);

        //Llenar select de Category
        $categories = Category::forDropdown($business_id);

        $countries = Country::forDropdown($business_id);

        $refered_by = Customer::where('id', $oportunity->refered_id)
            ->pluck('name', 'id');

        $states = State::select('id', 'name')
            ->where('country_id', $oportunity->country_id)
            ->pluck('name', 'id');

        $cities = State::select('id', 'name')
            ->where('country_id', $oportunity->state_id)
            ->pluck('name', 'id');

        return view('oportunity.edit')
            ->with(compact(
                'contactreason',
                'contactmode',
                'categories',
                'refered_by',
                'oportunity',
                'known_by',
                'states',
                'countries',
                'cities'
            ));
        //}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Oportunity  $oportunity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('oportunities.update')) {
            abort(403, 'Unauthorized action.');
        }

        $chk_not_found = $request->input('echk_not_found');
        $customer = $request->input('refered_id');

        try {

            $oportunity = Oportunity::findOrFail($id);

            $oportunity_details = $request->only(['contact_type', 'contact_date', 'known_by', 'contact_reason_id', 'name', 'company', 'charge', 'email', 'contacts', 'contact_mode_id', 'social_user', 'country_id', 'state_id', 'city_id']);

            if ($chk_not_found) {
                $oportunity_details['product_not_found'] = 1;
                $oportunity_details['products_not_found_desc'] = $request->input('eproducts_not_found_desc');
                $oportunity_details['product_cat_id'] = null;
            } else {
                $oportunity_details['product_not_found'] = 0;
                $oportunity_details['product_cat_id'] = $request->input('eproduct_cat_id');
                $oportunity_details['products_not_found_desc'] = null;
            }
            if ($customer) {
                $oportunity_details['refered_id'] = $request->input('refered_id');
            } else {
                $oportunity_details['refered_id'] = null;
            }

            $oportunity = $oportunity->update($oportunity_details);
            $output = [
                'success' => true,
                'data' => $oportunity,
                'msg' => __("crm.updated_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Oportunity  $oportunity
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('oportunities.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $oportunity = Oportunity::findOrFail($id);
            $follow_oportunities = FollowOportunities::where('oportunity_id', $id)->count();
            if ($follow_oportunities > 0) {
                $output = [
                    'success' => false,
                    'msg' => __("crm.oportunity_has_follows")
                ];
            } else {
                $oportunity->delete();
                $output = [
                    'success' => true,
                    'msg' => __("crm.deleted_success")
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCustomer($id)
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $oportunity = Oportunity::findOrFail($id);
        $business_id = request()->session()->get('user.business_id');

        $business_types = BusinessType::select('id', 'name')
            ->pluck('name', 'id');

        $customer_portfolios = CustomerPortfolio::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $customer_groups = CustomerGroup::all();

        $countries = Country::select('id', 'name')
            ->where('business_id', $business_id)
            ->pluck('name', 'id');

        $payment_terms = PaymentTerm::select('id', 'name')
            ->pluck('name', 'id');

        // Contact modes
        $contact_modes = CRMContactMode::where('business_id', $business_id)
            ->pluck('name', 'id');

        $cities = City::select('id', 'name')
            ->where('business_id', $business_id)
            ->where('state_id', $oportunity->state_id)
            ->pluck('name', 'id');

        $states = State::select('id', 'name')
            ->where('business_id', $business_id)
            ->where('country_id', $oportunity->country_id)
            ->pluck('name', 'id');

        // Business locations
        $business_locations = BusinessLocation::forDropdown($business_id);

        /** Tax groups */
        $tax_groups = $this->taxUtil->getTaxGroups($business_id, 'contacts');

        /**Prices list */
        $prices_group = SellingPriceGroup::all();
        return view('oportunity.create_customer', compact(
            'business_types',
            'customer_portfolios',
            'customer_groups',
            'countries',
            'payment_terms',
            'contact_modes',
            'business_locations',
            'tax_groups',
            'prices_group',
            'oportunity',
            'cities',
            'states'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCustomer(Request $request)
    {
        // dd($request);
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $is_taxpayer = $request->input('is_taxpayer');
        $allowed_credit = $request->input('allowed_credit');
        if (empty($request->input('email'))) {
            $validateData = $request->validate(
                [
                    'name' => 'required',
                    'telphone' => 'required',
                ]
            );
        } elseif (empty($request->input('telphone'))) {
            $validateData = $request->validate(
                [
                    'name' => 'required',
                    'email' => 'required|email',
                ]
            );
        } else {
            $validateData = $request->validate(
                [
                    'name' => 'required',
                    'telphone' => 'required',
                ]
            );
        }

        try {
            $customer_details = $request->only([
                'name',
                'business_name',
                'email',
                'telphone',
                'dni',
                'business_type_id',
                'customer_portfolio_id',
                'customer_group_id',
                'address',
                'country_id',
                'state_id',
                'city_id',
                'contact_mode_id',
                'first_purchase_location',
                'latitude',
                'length',
                'selling_price_group_id',
            ]);

            $customer_details['tax_group_id'] = $request->input('tax_group_id') != 0 ? $request->input('tax_group_id') : null;

            $business_id = request()->session()->get('user.business_id');

            $customer_details['business_id'] = $business_id;
            $customer_details['created_by'] = $request->session()->get('user.id');
            $customer_details['latitude'] = $request->latitude;
            $customer_details['length'] = $request->length;

            if ($request->input('state_id')) {
                $state = State::findOrFail($request->input('state_id'));
                $zone = Zone::where('id', $state->zone_id)->first();
                $customer_details['zone_id'] = $zone->id;
            }

            if ($is_taxpayer) {
                $customer_details['is_taxpayer'] = 1;
                $customer_details['reg_number'] = $request->input('reg_number');
                $customer_details['tax_number'] = $request->input('tax_number');
                $customer_details['business_line'] = $request->input('business_line');
            } else {
                $customer_details['is_taxpayer'] = 0;
            }


            if ($allowed_credit) {
                $customer_details['allowed_credit'] = 1;
                $customer_details['opening_balance'] = $request->input('opening_balance');
                $customer_details['credit_limit'] = $request->input('credit_limit');
                $customer_details['credit_balance'] = $request->input('opening_balance');
                $customer_details['payment_terms_id'] = $request->input('payment_terms_id');
            } else {
                $customer_details['allowed_credit'] = 0;
            }
            DB::beginTransaction();

            // dd($customer_details);
            $customer = Customer::create($customer_details);
            // dd($customer);
            $customer_id = $customer->id;

            if (!empty($request->input("contactname"))) {
                //se recorre cada uno de los campos de contactos y se agregan a un array

                foreach ($request->input("contactname") as $contactname) {
                    $contactnames[] = $contactname;
                }

                foreach ($request->input("contactphone") as $contactmobil) {
                    $contactmobile[] = $contactmobil;
                }

                foreach ($request->input("contactlandline") as $contactlandline) {
                    $contactlandlines[] = $contactlandline;
                }

                foreach ($request->input("contactemail") as $contactemail) {
                    $contactmails[] = $contactemail;
                }
                foreach ($request->input("contactcargo") as $contactcargo) {
                    $contactcargos[] = $contactcargo;
                }

                if (!empty($contactnames)) {

                    for ($i = 0; $i < count($contactnames); $i++) {
                        //se crea un nuevo contacto acorde a la cantidad de datos mandados en el array $contactnames
                        CustomerContact::create([
                            'name'      => $contactnames[$i],
                            'phone'     => $contactmobile[$i],
                            'landline'  => $contactlandlines[$i],
                            'email'     => $contactmails[$i],
                            'cargo'     => $contactcargos[$i],
                            'customer_id' => $customer_id
                        ]);
                    }
                }
            } //fin de contactos miltples



            $oportunity = Oportunity::findOrFail($request->input('oportunity_id'));
            $oportunity->customer_id = $customer->id;
            $oportunity->status = 'customer';
            $oportunity->save();

            $follow_oportunities = FollowOportunities::where('oportunity_id', $oportunity->id)->get();

            if (!empty($follow_oportunities)) {
                foreach ($follow_oportunities as $fo) {
                    $follow_customer = new FollowCustomer;
                    $follow_customer->customer_id = $customer->id;
                    $follow_customer->contact_type = $fo->contact_type;
                    $follow_customer->contact_reason_id = $fo->contact_reason_id;
                    $follow_customer->product_cat_id = $fo->product_cat_id;
                    $follow_customer->product_not_found = $fo->product_not_found;
                    $follow_customer->product_not_stock = $fo->product_not_stock;
                    $follow_customer->products_not_found_desc = $fo->products_not_found_desc;
                    $follow_customer->notes = $fo->notes;
                    $follow_customer->contact_mode_id = $fo->contact_mode_id;
                    $follow_customer->date = $fo->date;
                    $follow_customer->register_by = $fo->register_by;
                    $follow_customer->save();

                    $details = FollowOportunitiesHasProduct::where('follow_oportunitie_id', $fo->id)->get();

                    if (!empty($details)) {
                        foreach ($details as $d) {
                            $detail = new FollowCustomersHasProduct;
                            $detail->follow_customer_id = $follow_customer->id;
                            $detail->variation_id = $d->variation_id;
                            $detail->quantity = $d->quantity;
                            $detail->required_quantity = $d->required_quantity;
                            $detail->save();
                        }
                    }
                }
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("customer.added_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }
}
