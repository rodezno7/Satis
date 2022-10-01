<?php

namespace App\Http\Controllers;

use App\FollowCustomer;
use App\FollowCustomersHasProduct;
use Illuminate\Http\Request;

use App\CRMContactMode;
use App\CRMContactReason;
use App\Category;
use App\Contact;
use App\Country;

use DataTables;
use DB;

class FollowCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('follow_customer.create')) {
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

        $products = DB::table('variations')
        ->join('products', 'products.id', '=', 'variations.product_id')
        ->select('products.name as name_product', 'variations.name as name_variation', 'variations.id', 'variations.sub_sku', 'products.sku')
        ->where('business_id', $business_id)
        ->where('products.clasification', '<>', 'kits')
        ->where('products.clasification', '<>', 'service')
        ->where('products.status', 'active')
        ->get();

        return view('customer.follow_customers.create')
            ->with(compact('contactreason', 'known_by', 'categories', 'clients', 'contactmode', 'countries', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('follow_customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $chk_not_found = $request->input('chk_not_found');
        $chk_not_stock = $request->input('chk_not_stock');

        try {
            $follow_customer_details = $request->only(['contact_type', 'contact_reason_id', 'notes', 'contact_mode_id', 'date']);

            $follow_customer_details['customer_id'] = $request->input('customer_id_follow');

            $follow_customer_details['register_by'] = $request->session()->get('user.id');

            if($chk_not_found){
                $follow_customer_details['products_not_found_desc'] = $request->input('products_not_found_desc');
                $follow_customer_details['product_not_found'] = 1;
            }else{
                $follow_customer_details['product_not_found'] = 0;
                $follow_customer_details['product_cat_id'] = $request->input('product_cat_id');
            }

            if($chk_not_stock){
                $follow_customer_details['product_not_stock'] = 1;
                
            }else{
                $follow_customer_details['product_not_stock'] = 0;
                
            }

            DB::beginTransaction();

            $follow_customer = FollowCustomer::create($follow_customer_details);
            $variation_ids = $request->input('variation_id');
            $quantity = $request->input('quantity');
            $required_quantity = $request->input('required_quantity');

            if (!empty($variation_ids))
            {
                $cont = 0;                
                while($cont < count($variation_ids))
                {
                    $detail = new FollowCustomersHasProduct;
                    $detail->follow_customer_id = $follow_customer->id;
                    $detail->variation_id = $variation_ids[$cont];
                    $detail->quantity = $quantity[$cont];
                    $detail->required_quantity = $required_quantity[$cont];
                    $detail->save();
                    $cont = $cont + 1;
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
     * @param  \App\FollowCustomer  $followCustomer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('follow_customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $followCustomer = DB::table('follow_customers as follow')
            ->leftJoin('customers as c', 'c.id', '=', 'follow.customer_id')
            ->leftJoin('crm_contact_reasons as reason', 'reason.id', '=', 'follow.contact_reason_id')
            ->leftJoin('crm_contact_modes as mode', 'mode.id', '=', 'follow.contact_mode_id')
            ->leftJoin('categories as category', 'category.id', '=', 'follow.product_cat_id')
            ->select('follow.*', 'c.name as customer', 'reason.name as reason', 'mode.name as mode', 'category.name as category')
            ->where('follow.id', $id)
            ->first();

        return response()->json($followCustomer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FollowCustomer  $followCustomer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('follow_customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        $followCustomer = FollowCustomer::findOrFail($id);

        return response()->json($followCustomer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FollowCustomer  $followCustomer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('follow_customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        $chk_not_found = $request->input('eechk_not_found');
        $chk_not_stock = $request->input('eechk_not_stock');

        try {

            $followCustomer = FollowCustomer::findOrFail($id);

            $follow_customer_details['contact_type'] = $request->input('eecontact_type');
            $follow_customer_details['contact_reason_id'] = $request->input('eecontact_reason_id');
            $follow_customer_details['notes'] = $request->input('eenotes');
            $follow_customer_details['contact_mode_id'] = $request->input('econtact_mode_id');
            $follow_customer_details['date'] = $request->input('edate');

            if($chk_not_found){
                $follow_customer_details['products_not_found_desc'] = $request->input('eeproducts_not_found_desc');
                $follow_customer_details['product_not_found'] = 1;
            }else{
                $follow_customer_details['product_not_found'] = 0;
                $follow_customer_details['product_cat_id'] = $request->input('eeproduct_cat_id');
            }

            if($chk_not_stock){
                $follow_customer_details['product_not_stock'] = 1;
                
            }else{
                $follow_customer_details['product_not_stock'] = 0;
                
            }

            DB::beginTransaction();

            $followCustomer->update($follow_customer_details);

            $variation_ids = $request->input('variation_id');
            $quantity = $request->input('quantity');
            $required_quantity = $request->input('required_quantity');

            FollowCustomersHasProduct::where('follow_customer_id', $id)->forceDelete();
            
            if (!empty($variation_ids))
            {
                $cont = 0;                
                while($cont < count($variation_ids))
                {
                    $detail = new FollowCustomersHasProduct;
                    $detail->follow_customer_id = $followCustomer->id;
                    $detail->variation_id = $variation_ids[$cont];
                    $detail->quantity = $quantity[$cont];
                    $detail->required_quantity = $required_quantity[$cont];
                    $detail->save();
                    $cont = $cont + 1;
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
     * @param  \App\FollowCustomer  $followCustomer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('follow_customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $follow = FollowCustomer::findOrFail($id);
            
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

    public function getFollowsbyCustomer($id){
        $follow_customers = DB::table('follow_customers as follow')
            ->join('customers as c', 'follow.customer_id', '=', 'c.id')
            ->leftJoin('crm_contact_reasons as reason', 'follow.contact_reason_id', '=', 'reason.id')
            ->leftJoin('crm_contact_modes as mode', 'mode.id', '=', 'follow.contact_mode_id')
            ->leftJoin('users as user', 'user.id', '=', 'follow.register_by')
            ->select('follow.id', 'follow.contact_type as contact_type', 'reason.name as reason', 'c.name as customer', 'mode.name as mode', 'follow.date', DB::raw('CONCAT(user.first_name, " ", user.last_name) as name_register'), 'follow.register_by')
            ->where('follow.customer_id', $id)
            ->orderBy('follow.date', 'desc')
            ->get();

        return DataTables::of($follow_customers)->toJson();
    }

    public function getProductsByFollowCustomer($id)
    {
        $items = DB::table('follow_customers_has_products as follow')
            ->join('variations as variation', 'variation.id', '=', 'follow.variation_id')
            ->join('products as product', 'product.id', '=', 'variation.product_id')
            ->select('follow.*', 'variation.name as name_variation', 'variation.sub_sku', 'product.sku', 'product.name as name_product')
            ->where('follow.follow_customer_id', $id)
            ->get();
        
        return response()->json($items);
    }
}
