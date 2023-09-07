<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\AssignOp\Concat;
Use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
class Employees extends Model
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of customer group for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @param $prepend_all = false (boolean)
     *
     * @return array
     */

    protected $fillable = [
        'agent_code',
        'first_name', 
        'last_name',
        'gender',
        'nationality_id',
        'hired_date', 
        'fired_date', 
        'birth_date',
        'dni',
        'approved',
        'tax_number',
        'social_security_number',
        'afp_id',
        'afp_number',
        'civil_status_id',
        'phone',
        'mobile',
        'email',
        'institutional_email',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'photo',
        'profession_id',
        'date_admission',
        'department_id',
        'position1_id',
        'salary',
        'type_id',
        'payment_id',
        'bank_id',
        'bank_account',
        'extra_hours',
        'foreign_tax',
        'fee',
        'status',
        'curriculum_vitae',
        'created_by',
        'business_id',
        'user_id', 
        'short_name',
        'deleted_at'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function afp() {
        return $this->belongsTo('App\RrhhData');
    }

    public function civilStatus() {
        return $this->belongsTo('App\RrhhData');
    }

    public function nationality() {
        return $this->belongsTo('App\RrhhData');
    }

    public function profession() {
        return $this->belongsTo('App\RrhhData');
    }

    public function rrhhTypeWage() {
        return $this->belongsTo('App\RrhhTypeWage', 'type_id');
    }

    public function payment() {
        return $this->belongsTo('App\RrhhData');
    }

    public function bank() {
        return $this->belongsTo('App\Bank');
    }
    
    public function country() {
        return $this->belongsTo('App\Country');
    }

    public function state() {
        return $this->belongsTo('App\State');
    }

    public function city() {
        return $this->belongsTo('App\City');
    }

    public function positionHistories(){
        return $this->hasMany('App\RrhhPositionHistory', 'employee_id');
    }

    public function salaryHistories(){
        return $this->hasMany('App\RrhhSalaryHistory', 'employee_id');
    }
    
    public function rrhhContracts(){
        return $this->hasMany('App\RrhhContract');
    }

    public function rrhhDocuments(){
        return $this->hasMany('App\RrhhDocuments');
    }

    public function rrhhIncomeDiscounts(){
        return $this->hasMany('App\RrhhIncomeDiscount', 'employee_id');
    }

    public function payrollDetails(){
        return $this->hasMany('App\PayrollDetail', 'employee_id');
    }

    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {

        $query = Employees::where('business_id', $business_id);
        $all_emp = $query->select('id', DB::raw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) as full_name"));
        $all_emp = $all_emp->pluck('full_name', 'id');

        //Prepend none
        if ($prepend_none) {
            $all_emp = $all_emp->prepend(__("lang_v1.none"), '');
        }

        //Prepend none
        if ($prepend_all) {
            $all_emp = $all_emp->prepend(__("report.all"), '');
        }
        
        return $all_emp;
    }

    public static function SellersDropdown($business_id, $prepend_none = true)
     {

        $all_cmmsn_agnts = Employees::where('business_id', $business_id)
        ->whereNotNull('agent_code')
        ->select('id', DB::raw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) as full_name"));

        $users = $all_cmmsn_agnts->pluck('full_name', 'id');

        //Prepend none
        if ($prepend_none) {
            $users = $users->prepend(__('lang_v1.none'), '');
        }

        return $users;
    }

    public function newNotification()
    {
        $this->notify(new Notification);
    }
}
