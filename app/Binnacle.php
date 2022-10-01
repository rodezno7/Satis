<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Binnacle extends Model
{
    protected $fillable = [
        'user_id',
        'reference',
        'module',
        'action',
        'old_record',
        'new_record'
        // 'entrie_number',
        // 'entrie_correlative',
        // 'account_code',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
