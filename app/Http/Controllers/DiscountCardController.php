<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DiscountCard;
class DiscountCardController extends Controller
{
    //

     public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $category = DiscountCard::where('business_id', $business_id)
                        ->select(['discounts_name', 'value_', 'id',]);

            return Datatables::of($category);
        }

        return view('category.index');
    }
    
}
