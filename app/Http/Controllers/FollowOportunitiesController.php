<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use DataTables;

use App\Contact;
use App\Country;
use App\Category;
use App\Oportunity;
use App\CRMContactMode;
use App\BusinessLocation;

use App\CRMContactReason;
use App\FollowOportunities;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use App\FollowOportunitiesHasProduct;

class FollowOportunitiesController extends Controller
{
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }
    public function index()
    {
        // return view()
    }

    public function show($id)
    {
        if (!auth()->user()->can('follow_oportunities.view')) {
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
        $this->id = $id;

        return view('oportunity.follow_oportunities.index', compact('oportunity'));
    }

    public function create($id)
    {
        if (!auth()->user()->can('follow_oportunities.create')) {
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

        //Llenar select de Clientes
        $clients = Contact::where('business_id', $business_id)->whereIn('type', ['customer', 'both'])->pluck('name', 'id');

        $countries = Country::forDropdown($business_id);
        $locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $products = DB::table('variations')
            ->join('products', 'products.id', '=', 'variations.product_id')
            ->select('products.name as name_product', 'variations.name as name_variation', 'variations.id', 'variations.sub_sku', 'products.sku')
            ->where('business_id', $business_id)
            ->where('products.clasification', '<>', 'kits')
            ->where('products.clasification', '<>', 'service')
            ->where('products.status', 'active')
            ->get();

        // dd($id);
        $oportunity = Oportunity::findOrFail($id);
        return view('oportunity.follow_oportunities.create')
            ->with(compact('contactreason', 'known_by', 'oportunity', 'categories', 'clients', 'contactmode', 'countries', 'products', 'locations', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('follow_oportunities.create')) {
            abort(403, 'Unauthorized action.');
        }

        $chk_not_found = $request->input('chk_not_found');
        $chk_not_stock = $request->input('chk_not_stock');

        try {

            $follow_oportunity_details = $request->only(['oportunity_id', 'contact_type', 'contact_reason_id', 'notes', 'contact_mode_id', 'date']);
            $follow_oportunity_details['date'] = $this->transactionUtil->uf_date($request->input('date'));
            $follow_oportunity_details['register_by'] = $request->session()->get('user.id');

            if ($chk_not_found) {
                $follow_oportunity_details['products_not_found_desc'] = $request->input('products_not_found_desc');
                $follow_oportunity_details['product_not_found'] = 1;
            } else {
                $follow_oportunity_details['product_not_found'] = 0;
                $follow_oportunity_details['product_cat_id'] = $request->input('product_cat_id');
            }

            if ($chk_not_stock) {
                $follow_oportunity_details['product_not_stock'] = 1;
            } else {
                $follow_oportunity_details['product_not_stock'] = 0;
            }

            DB::beginTransaction();

            $follow_oportunity = FollowOportunities::create($follow_oportunity_details);
            if (!empty($request->input("variation_id"))) {

                foreach ($request->input("variation_id") as $variation_id) {
                    $variation_ids[] = $variation_id;
                }

                foreach ($request->input("quantity") as $quantity) {
                    $quantitys[] = $quantity;
                }

                foreach ($request->input("required_quantity") as $required_quantity) {
                    $required_quantitys[] = $required_quantity;
                }

                if (!empty($variation_ids)) {

                    for ($i = 0; $i < count($variation_ids); $i++) {
                        //se crea un nuevo contacto acorde a la cantidad de datos mandados en el array $contactnames

                        FollowOportunitiesHasProduct::create([
                            'follow_oportunitie_id'=> $follow_oportunity->id,
                            'variation_id'     => $variation_ids[$i],
                            'quantity'  => $quantitys[$i],
                            'required_quantity'     => $required_quantitys[$i]
                        ]);
                    }
                }
            }

            DB::commit();
            $outpout = [
                'success' => true,
                'msg' => __("crm.added_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $outpout;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FollowOportunities  $followOportunities
     * @return \Illuminate\Http\Response
     */
    public function showOportunities($id)
    {
        if (!auth()->user()->can('follow_oportunities.view')) {
            abort(403, 'Unauthorized action.');
        }
        $followOportunitie = DB::table('follow_oportunities as follow')
            ->leftJoin('oportunities as oportunity', 'oportunity.id', '=', 'follow.oportunity_id')
            ->leftJoin('crm_contact_reasons as reason', 'reason.id', '=', 'follow.contact_reason_id')
            ->leftJoin('crm_contact_modes as mode', 'mode.id', '=', 'follow.contact_mode_id')
            ->leftJoin('categories as category', 'category.id', '=', 'follow.product_cat_id')
            ->select('follow.*', 'oportunity.name as oportunity', 'reason.name as reason', 'mode.name as mode', 'category.name as category')
            ->where('follow.id', $id)
            ->first();
        // return response()->json($followOportunitie);
        return view('oportunity.follow_oportunities.index', compact('followOportunitie'));
    }


    public function edit($id)
    {
        if (!auth()->user()->can('follow_oportunities.update')) {
            abort(403, 'Unauthorized action.');
        }
        $followOportunitie = FollowOportunities::findOrFail($id);
        $business_id = request()->session()->get('user.business_id');

        //Llenar select de Contact Reason
        $contactreason = CRMContactReason::forDropdown($business_id);

        //Llenar select de Known By
        $known_by = CRMContactMode::forDropdown($business_id);

        //Llenar select de Contact Mode
        $contactmode = CRMContactMode::where('name', 'not like', "%cliente%")->pluck('name', 'id');

        //Llenar select de Category
        $categories = Category::forDropdown($business_id);

        //Llenar select de Clientes
        $clients = Contact::where('business_id', $business_id)->whereIn('type', ['customer', 'both'])->pluck('name', 'id');

        $countries = Country::forDropdown($business_id);
        $locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $followOportunitie_product = FollowOportunitiesHasProduct::where('follow_oportunitie_id', $id)
            ->leftJoin('variations', 'variations.id', '=', 'follow_oportunities_has_products.variation_id')
            ->leftJoin('products', 'products.id', '=', 'variations.product_id')
            ->leftJoin('variation_location_details as stock', 'variations.id', '=', 'stock.variation_id')
            ->select([
                'variations.id', 'follow_oportunities_has_products.id as idf', 'variations.name as name',
                'products.sku', 'follow_oportunities_has_products.quantity as quantity',
                'follow_oportunities_has_products.required_quantity'
            ])
            ->get();
        // dd($followOportunitie->product_not_stock);

        $products = DB::table('variations')
            ->join('products', 'products.id', '=', 'variations.product_id')
            ->select('products.name as name_product', 'variations.name as name_variation', 'variations.id', 'variations.sub_sku', 'products.sku')
            ->where('business_id', $business_id)
            ->where('products.clasification', '<>', 'kits')
            ->where('products.clasification', '<>', 'service')
            ->where('products.status', 'active')
            ->get();

        // foreach($products as $p){
        //     echo $p->name_product . "<br>";
        // }
        return view('oportunity.follow_oportunities.edit')
            ->with(compact(
                'contactreason',
                'followOportunitie',
                'known_by',
                'categories',
                'clients',
                'contactmode',
                'countries',
                'products',
                'locations',
                'followOportunitie_product',
            ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FollowOportunities  $followOportunities
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request);
        if (!auth()->user()->can('follow_oportunities.update')) {
            abort(403, 'Unauthorized action.');
        }

        $chk_not_found = $request->input('eechk_not_found');
        $chk_not_stock = $request->input('eechk_not_stock');

        try {

            $followOportunities = FollowOportunities::findOrFail($id);

            $follow_oportunity_details['contact_type'] = $request->input('eecontact_type');
            $follow_oportunity_details['contact_reason_id'] = $request->input('eecontact_reason_id');
            $follow_oportunity_details['notes'] = $request->input('eenotes');
            $follow_oportunity_details['contact_mode_id'] = $request->input('econtact_mode_id');

            if ($chk_not_found) {
                $follow_oportunity_details['products_not_found_desc'] = $request->input('eeproducts_not_found_desc');
                $follow_oportunity_details['product_not_found'] = 1;
            } else {
                $follow_oportunity_details['product_not_found'] = 0;
                $follow_oportunity_details['product_cat_id'] = $request->input('eeproduct_cat_id');
            }

            if ($chk_not_stock) {
                $follow_oportunity_details['product_not_stock'] = 1;
            } else {
                $follow_oportunity_details['product_not_stock'] = 0;
            }

            DB::beginTransaction();

            $followOportunities->update($follow_oportunity_details);
            $oldContact = FollowOportunitiesHasProduct::where('follow_oportunitie_id', $followOportunities->id)->pluck('id');
            $newContact = [];

            if (!empty($request->input("variation_id"))) {
                foreach ($request->input('oporid') as $oporid) {
                    $oporids[] = $oporid;
                }

                foreach ($request->input("variation_id") as $variation_id) {
                    $variation_ids[] = $variation_id;
                }

                foreach ($request->input("quantity") as $quantity) {
                    $quantitys[] = $quantity;
                }

                foreach ($request->input("required_quantity") as $required_quantity) {
                    $required_quantitys[] = $required_quantity;
                }

                if (!empty($variation_ids)) {

                    for ($i = 0; $i < count($variation_ids); $i++) {
                        //se crea un nuevo contacto acorde a la cantidad de datos mandados en el array $contactnames
                        if ($oporids[$i] == "0") {
                            FollowOportunitiesHasProduct::create([
                                'follow_oportunitie_id'=> $followOportunities->id,
                                'variation_id'     => $variation_ids[$i],
                                'quantity'  => $quantitys[$i],
                                'required_quantity'     => $required_quantitys[$i]
                            ]);
                        } else {
                                FollowOportunitiesHasProduct::find($oporids[$i])
                                ->update([
                                    'follow_oportunitie_id'=> $followOportunities->id,
                                    'variation_id'     => $variation_ids[$i],
                                    'quantity'  => $quantitys[$i],
                                    'required_quantity'     => $required_quantitys[$i]
                                ]);
                            $newContact[] = $oporids[$i];
                        }
                    }
                }
            }

            foreach ($oldContact as $o) {
                $delete = true;

                foreach ($newContact as $n) {
                    if ($o == $n) {
                        $delete = false;
                    }
                }

                if ($delete) {
                    FollowOportunitiesHasProduct::findOrFail($o)->delete();
                }
            }
            DB::commit();
            $outpout = [
                'success' => true,
                'msg' => __("crm.updated_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $outpout;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FollowOportunities  $followOportunities
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('follow_oportunities.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $follow = FollowOportunities::findOrFail($id);

            $follow->delete();
            $output = [
                'success' => true,
                'msg' => __("crm.deleted_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }

    public function getFollowsbyOportunity($id)
    {
        $follow_oportunities = DB::table('follow_oportunities as follow')
            ->join('oportunities as oportunity', 'follow.oportunity_id', '=', 'oportunity.id')
            ->leftJoin('crm_contact_reasons as reason', 'follow.contact_reason_id', '=', 'reason.id')
            ->leftJoin('crm_contact_modes as mode', 'mode.id', '=', 'follow.contact_mode_id')
            ->leftJoin('users as user', 'user.id', '=', 'follow.register_by')
            ->select('follow.id', 'follow.contact_type as contact_type', 'reason.name as reason', 'oportunity.name as oportunity', 'mode.name as mode', 'follow.date', DB::raw('CONCAT(user.first_name, " ", user.last_name) as name_register'), 'follow.register_by')
            ->where('follow.oportunity_id', $id)
            ->orderBy('follow.date', 'desc')
            ->get();
        return DataTables::of($follow_oportunities)
            ->addColumn(
                'actions',
                function ($row) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-xs btn-primary dropdown-toggle" 
                            data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") . '<span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                    $html .= '<li><a href="#" data-href="' . action('FollowOportunitiesController@edit', [$row->id]) . '" class="edit_oportunities_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    $html .= '<li><a href="#" onclick="deleteOport(' . $row->id . ')"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';


                    $html .= '</ul></div>';
                    return $html;
                }
            )
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function getProductsByFollowOportunity($id)
    {
        $items = DB::table('follow_oportunities_has_products as follow')
            ->join('variations as variation', 'variation.id', '=', 'follow.variation_id')
            ->join('products as product', 'product.id', '=', 'variation.product_id')
            ->select('follow.*', 'variation.name as name_variation', 'variation.sub_sku', 'product.sku', 'product.name as name_product')
            ->where('follow.follow_oportunitie_id', $id)
            ->get();
        return response()->json($items);
    }
}
