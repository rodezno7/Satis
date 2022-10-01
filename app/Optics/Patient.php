<?php

namespace App\Optics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Patient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'code', 'full_name', 'age',
        'sex', 'email', 'contacts',
        'address', 'glasses', 'glasses_graduation',
        'location_id', 'business_id', 'register_by',
        'notes', 'employee_id'
    ];

    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {
        $query = Patient::where('business_id', $business_id);
        $patients = $query->select('id', 'full_name');
        $patients = $patients->pluck('full_name', 'id');

        // Prepend none
        if ($prepend_none) {
            $patients = $patients->prepend(__("lang_v1.none"), '');
        }

        // Prepend all
        if ($prepend_all) {
            $patients = $patients->prepend(__("report.all"), '');
        }
        
        return $patients;
    }

    /**
     * Gets the employee to which the patient belongs.
     *
     * @return \Illuminate\Database\Eloquent\Concerns\HasRelationships
     */
    public function employee()
    {
        return $this->belongsTo('App\Employees');
    }
}
