<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'business_id',
        'business_location_id',
        'is_active',
    ];

    /**
     * Gets the business location to which the cashier belongs.
     *
     * @return \Illuminate\Database\Eloquent\Concerns\HasRelationships
     */
    public function business_location()
    {
        return $this->belongsTo('App\BusinessLocation');
    }

    /**
     * Get cashier closure what casier has
     * 
     * @return \Illuminate\Database\Eloquent\Concerns\HasRelationships
     */
    public function cashier_closure(){
        return $this->hasMany(App\CashierClosure::class);
    }

    /**
     * Return list of cashiers
     *
     * @param int $business_id
     * @param boolean $show_all = false
     * @param array $receipt_printer_type_attribute
     *
     * @return array
     */
    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {
        $query = Cashier::where('business_id', $business_id)->where('is_active', '1');
        
        $permitted_cashiers = Cashier::permittedCashiers();
        if ($permitted_cashiers != 'all') {
            $query->whereIn('id', $permitted_cashiers);
        }

        $cashiers = $query->pluck('name', 'id');

        //Prepend none
        if ($prepend_none) {
            $cashiers = $cashiers->prepend(__("lang_v1.none"), '');
        }

        //Prepend none
        if ($prepend_all) {
            $cashiers = $cashiers->prepend(__("report.all"), '');
        }
        
        return $cashiers;
    }

    /**
     * Gives cashiers permitted for the logged in user
     *
     * @return string or array
     */
    public static function permittedCashiers()
    {
        if (auth()->user()->can('access_all_cashiers')) {
            return 'all';
        } else {
            $business_id = request()->session()->get('user.business_id');
            $permitted_cashiers = [];
            $all_cashiers = Cashier::where('business_id', $business_id)->get();
            
            foreach ($all_cashiers as $cashier) {
                if (auth()->user()->can('cashier.' . $cashier->id)) {
                    $permitted_cashiers[] = $cashier->id;
                }
            }
    
            return $permitted_cashiers;
        }
    }
}
