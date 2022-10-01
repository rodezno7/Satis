<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMOportunity extends Model
{
    protected $table = "crm_oportunities";
    protected $fillable = ['contact_type', 'contact_reason_id', 'name', 'company', 'charge', 'email', 'contacts', 'contact_mode_id', 'refered_id', 'product_cat_id', 'employee_id', 'status', 'business_id'];
}
