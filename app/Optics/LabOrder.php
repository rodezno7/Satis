<?php

namespace App\Optics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabOrder extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $fillable = [
    	'no_order',
        'customer_id',
        'contact_id',
        'patient_id',
        'graduation_card_id',
        'status_lab_order_id',
        'is_reparation',
        'hoop',
        'size',
        'color',
        'vision',
        'glass',
        'ar',
        'job_type',
        'check_ext_lab',
        'external_lab_id',
        'is_urgent',
        'is_own_hoop',
        'hoop_type',
        'delivery',
        'business_id',
        'employee_id',
        'reason',
        'return_stock',
        'transaction_id',
        'hoop_name',
        'glass_os',
        'glass_od',
        'business_location_id',
        'correlative',
        'transfer_date',
        'is_annulled'
    ];

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }
}
