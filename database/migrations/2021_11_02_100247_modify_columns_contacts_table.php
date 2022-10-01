<?php

use App\Contact;
use App\Country;
use App\State;
use App\City;
use App\PaymentTerm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $contacts = Contact::withTrashed()->get();

        foreach ($contacts as $contact) {

            $country = Country::whereRaw('upper(name) = upper("' . $contact->country . '")')->first();
            //Check if country exist to set the state and city
            if (!empty($country)) {
                $contact->country = $country->id;

                $state = State::whereRaw('upper(name) = upper("' . $contact->state . '")')
                    ->where('country_id', $country->id)->first();

                if (!empty($state)) {
                    $contact->state = $state->id;

                    $city = City::whereRaw('upper(name) = upper("' . $contact->city . '")')->where('state_id', $state->id)->first();

                    if (!empty($city)) {
                        $contact->city = $city->id;
                    } else {
                        $contact->city = null;
                    }
                } else {
                    $contact->state = null;
                    $contact->city = null;
                }
            } else {
                //Check if state exist to set the country and city
                $state = State::whereRaw('upper(name) = upper("' . $contact->state . '")')->first();

                if (!empty($state)) {
                    $contact->country = $state->country_id;
                    $contact->state = $state->id;

                    $city = City::whereRaw('upper(name) = upper("' . $contact->city . '")')->where('state_id', $state->id)->first();

                    if (!empty($city)) {
                        $contact->city = $city->id;
                    } else {
                        $contact->city = null;
                    }
                } else {
                    //Check if city exist to set the state and country
                    $city = City::whereRaw('upper(name) = upper("' . $contact->city . '")')->first();

                    if (!empty($city)) {
                        $state = State::where('id', $city->state_id)->first();

                        $contact->city = $city->id;
                        $contact->state = $city->state_id;
                        $contact->country = $state->country_id;
                    } else {
                        $contact->country = null;
                        $contact->state = null;
                        $contact->city = null;
                    }
                }
            }

            //Check and set the payment_term_id properly to pay_term_type (we use that field later)
            if (empty($contact->pay_term_type)) {
                $contact->pay_term_number = null;
            } else {
                if ($contact->pay_term_type == "months") {
                    if (!empty($contact->pay_term_number)) {
                        $contact->pay_term_number = $contact->pay_term_number * 30;
                    }
                }

                $payment_term = PaymentTerm::where('days', $contact->pay_term_number)->first();
                if (!empty($payment_term)) {
                    $contact->pay_term_number = $payment_term->id;
                } else {
                    $contact->pay_term_number = null;
                }
            }

            $contact->save();
        }


        Schema::table('contacts', function (Blueprint $table) {
            $table->integer('country_id')->unsigned()->nullable()->default(null)->after('provider_catalogue_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('state_id')->unsigned()->nullable()->default(null)->after('provider_catalogue_id');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('city_id')->unsigned()->nullable()->default(null)->after('provider_catalogue_id');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('zone_id')->unsigned()->nullable()->default(null)->after('provider_catalogue_id');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('payment_term_id')->unsigned()->nullable()->default(null)->after('alternate_number');
            $table->foreign('payment_term_id')->references('id')->on('payment_terms')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('business_type_id')->unsigned()->nullable()->default(null)->after('alternate_number');
            $table->foreign('business_type_id')->references('id')->on('business_types')->onDelete('cascade')->onUpdate('cascade');
        });

        //Set all the foreign keys to each field
        DB::statement('UPDATE contacts SET country_id = country');
        DB::statement('UPDATE contacts SET state_id = state');
        DB::statement('UPDATE contacts SET city_id = city');
        DB::statement('UPDATE contacts SET payment_term_id = pay_term_number');
        DB::statement('UPDATE contacts SET business_type_id = case
                when business_type = 1 then 2
                when business_type = 2 then 3
                when business_type = 3 then 4
                else null
                end
            ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
}
