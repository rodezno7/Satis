<?php

namespace App\Optics;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class MaterialType extends Model
{
    use SoftDeletes;

    protected $table = 'material_types';

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
        'business_id',
        'name',
        'description',
        'created_by'
    ];

    /**
     * Gets the business location to which the material type belongs.
     *
     * @return \Illuminate\Database\Eloquent\Concerns\HasRelationships
     */
    public function business_location()
    {
        return $this->belongsTo('App\BusinessLocation');
    }

    /**
     * Return list of material types
     *
     * @param int $business_id
     * @param boolean $show_all = false
     * @param array $receipt_printer_type_attribute
     *
     * @return array
     */
    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {
        $query = MaterialType::where('business_id', $business_id);
        
        /*
        $permitted_material_types = MaterialType::permittedMaterialTypes();
        if ($permitted_material_types != 'all') {
            $query->whereIn('id', $permitted_material_types);
        }
        */

        $material_types = $query->pluck('name', 'id');

        //Prepend none
        if ($prepend_none) {
            $material_types = $material_types->prepend(__("lang_v1.none"), '');
        }

        //Prepend none
        if ($prepend_all) {
            $material_types = $material_types->prepend(__("report.all"), '');
        }
        
        return $material_types;
    }

    /**
     * Gives cashiers permitted for the logged in user
     *
     * @return string or array
     */
    /*
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
    */
}
