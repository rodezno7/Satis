<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentCommitmentLine extends Model
{
    protected $fillable = ['payment_commitment_id', 'transaction_id', 'document_name', 'reference', 'total'];

    public function payment_commitment()
    {
        return $this->belongsTo(\App\PaymentCommitment::class);
    }
}
