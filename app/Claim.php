<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = ['correlative', 'claim_type', 'status_claim_id', 'description', 'claim_date', 'suggested_closing_date', 'review_description', 'proceed', 'not_proceed', 'justification', 'closed', 'resolution', 'authorized_by', 'close_date', 'register_by', 'customer_id', 'variation_id', 'invoice', 'equipment_reception', 'equipment_reception_desc'];

    public function claimType()
    {
    	return $this->belongsTo('App\ClaimType');
    }
}
