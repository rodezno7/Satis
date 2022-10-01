<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    //

    public function cashier(){
        return belongsTo(App\Cashier::class);
    }
}
