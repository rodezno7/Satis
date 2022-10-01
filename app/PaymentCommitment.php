<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentCommitment extends Model
{
    //

    public function payment_commitment_lines()
    {
        return $this->hasMany(\App\PaymentCommitmentLine::class);
    }
}
