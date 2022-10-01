<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of tax rate dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @param $include_attributes = false (boolean)
     *
     * @return array['tax_rates', 'attributes']
     */
    public static function forBusinessDropdown(
        $business_id,
        $prepend_none = true,
        $include_attributes = false
    ) {
    
        //$all_taxes = TaxRates::where('business_id', $business_id);
        //$tax_rates = $all_taxes->pluck('name', 'id');

        $all_taxes = TaxGroup::where('business_id', $business_id);
        $tax_rates = $all_taxes->pluck('description', 'id');

        //Prepend none
        if ($prepend_none) {
            $tax_rates = $tax_rates->prepend(__('lang_v1.none'), '');
        }

        //Add tax attributes
        $tax_attributes = null;
        if ($include_attributes) {
            $tax_attributes = collect($all_taxes->get())->mapWithKeys(function ($item) {
                return [$item->id => ['data-rate' => $item->percent]];
            })->all();
        }

        $output = ['tax_rates' => $tax_rates, 'attributes' => $tax_attributes];
        return $output;
    }

    /**
     * Return list of tax rate for a business
     *
     * @return array
     */
    public static function forBusiness($business_id)
    {
        $tax_rates = TaxRate::where('business_id', $business_id)
                        ->select(['id', 'name', 'percent'])
                        ->get()
                        ->toArray();

        return $tax_rates;
    }

    /**
     * Return list of tax rates associated with the group_tax
     *
     * @return object
     */
    public function tax_groups() {
        return $this->belongsToMany('App\TaxGroup', 'tax_rate_tax_group', 'tax_rate_id', 'tax_group_id');
    }
}