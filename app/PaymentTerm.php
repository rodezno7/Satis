<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
      protected $fillable = [
            'name',
            'description',
            'business_id'
      ];

      public static function forDropdown($business_id, $prepend_none = true, $preprend_all = false)
      {
            $all_terms = PaymentTerm::where('business_id', $business_id);
            $all_terms = $all_terms->pluck('name', 'id');

            if ($prepend_none) {
                  $all_terms = $all_terms->prepend(__('purchase.none_payment_terms'), '');
            }

            if ($preprend_all) {
                  $all_terms = $all_terms->prepend(__('report.all'), '');
            }

            return $all_terms;
      }
}
