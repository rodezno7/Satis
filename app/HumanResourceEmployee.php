<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HumanResourceEmployee extends Model {

    protected $fillable = [
        'code',
        'name',
        'last_name',
        'gender',
        'nationality_id',
        'birthdate',
        'dni',
        'tax_number',
        'social_security_number',
        'afp_id',
        'afp_number',
        'civil_status_id',
        'phone',
        'whatsapp',
        'email',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'photo',
        'profession_id',
        'date_admission',
        'department_id',
        'position_id',
        'salary',
        'type_id',
        'payment_id',
        'bank_id',
        'bank_account',
        'extra_hours',
        'foreign_tax',
        'fee',
        'status'
    ];

    public function afp() {

        return $this->belongsTo('App\HumanResourcesData');
    }

    public function civilStatus() {

        return $this->belongsTo('App\HumanResourcesData');
    }

    public function department() {

        return $this->belongsTo('App\HumanResourcesData');
    }

    public function nationality() {

        return $this->belongsTo('App\HumanResourcesData');
    }

    public function position() {

        return $this->belongsTo('App\HumanResourcesData');
    }

    public function profession() {

        return $this->belongsTo('App\HumanResourcesData');
    }

    public function type() {

        return $this->belongsTo('App\HumanResourcesData');
    }

    public function payment() {

        return $this->belongsTo('App\HumanResourcesData');
    }

    public function bank() {

        return $this->belongsTo('App\HumanResourceBanks');
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
    
}