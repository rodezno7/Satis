<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
	use SoftDeletes;

	protected $guarded = ['id'];
	protected $dates = ['deleted_at'];

	protected $fillable = [
		'customer_id',
		'employee_id',
		'user_id',
		'business_id',
		'document_type_id',
		'quote_date',
		'due_date',
		'type',
		'status',
		'quote_ref_no',
		'customer_name',
		'contact_name',
		'email',
		'mobile',
		'address',
		'payment_condition',
		'tax_detail',
		'validity',
		'delivery_time',
		'note',
		'terms_conditions',
		'discount_type',
		'discount_amount',
		'total_before_tax',
		'tax_amount',
		'total_final',
		'created_by',
		'selling_price_group_id',
		'cashier_id',
		'location_id',
		'warehouse_id',
		'customer_vehicle_id'
	];

	public function quote_lines()
	{
		return $this->hasMany(\App\QuoteLine::class);
	}

	public function transaction(){
		return $this->belongsTo(\App\Transaction::class);
	}

	public function payment_lines()
    {
        return $this->hasMany(\App\TransactionPayment::class);
    }

	public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

	public function customer()
    {
        return $this->belongsTo(\App\Customer::class, 'customer_id');
    }

	public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

	public function employee(){
		return $this->belongsTo(\App\Employees::class, 'employee_id');
	}
}
