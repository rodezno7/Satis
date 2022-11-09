<?php

use App\Zone;
use App\City;
use App\State;
use App\Country;
use App\Business;
use App\Customer;
use App\TaxGroup;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** Business id to sync with */
        $business_id = null;

        /** If business_id not setted, exit */
        if (is_null($business_id)) {
            return true;
        }

        $customers = Customer::where('business_id', $business_id)
            ->select('id', 'code')->get();

        foreach ($customers as $c) {
            $this->syncCustomer($c->id, $c->code, $business_id);
        }
    }

        /**
     * Sync customers
     * 
     * @param int $id
     * @param string $code
     * @param int $business_id
     * 
     * @return void
     * @author Arquímides Martínez
     */
    public function syncCustomer($id, $code, $business_id) {
        /** If business_id not setted, exit */
        if (is_null($business_id)) {
            return true;
        }

        $business = Business::where('id', '!=', $business_id)
            ->select('id')->get();

        /** If there is not more than one business, exit */
        if (empty($business)) {
            return true;
        }
    
        foreach ($business as $b) {
            $customer = Customer::findOrFail($id)->toArray();
            $customer['business_id'] = $b->id;

            /** Remove needless columns */
            unset(
                $customer['id'],
                $customer['created_at'],
                $customer['updated_at']
            );

            /** Country */
            if (!is_null($customer['country_id'])) {
                $country = Country::findOrFail($customer['country_id']);

                if (!empty($country)) {
                    $country = Country::where('short_name', $country->short_name)
                        ->where('business_id', $b->id)
                        ->first();

                    $customer['country_id'] = !empty($country) ? $country->id : null;

                    /** Zone */
                    if (!empty($customer['zone_id'])) {
                        $zone = Zone::findOrFail($customer['zone_id']);

                        if (!empty($zone)) {
                            $zone = Zone::where('name', $zone->name)
                                ->where('business_id', $b->id)
                                ->first();

                            $customer['zone_id'] = $zone->id;
                        }
                    }

                    /** State */
                    if (!is_null($customer['state_id'])) {
                        $state = State::findOrFail($customer['state_id']);

                        if (!empty($state)) {
                            $state = State::where('name', $state->name)
                                ->where('business_id', $b->id)
                                ->first();

                            $customer['state_id'] = !empty($state) ? $state->id : null;

                            /** City */
                            if (!is_null($customer['city_id'])) {
                                $city = City::findOrFail($customer['city_id']);

                                if (!empty($city)) {
                                    $city = City::where('name', $city->name)
                                        ->where('state_id', $state->id)
                                        ->where('business_id', $b->id)
                                        ->first();

                                    $customer['city_id'] = !empty($city) ? $city->id : null;
                                }
                            }
                        }
                    }
                }
            }

            /** Tax Group */
            if (!is_null($customer['tax_group_id'])) {
                $tax = TaxGroup::findOrFail($customer['tax_group_id']);

                if (!empty($tax)) {
                    $tax = TaxGroup::where('description', $tax->description)
                        ->where('business_id', $b->id)
                        ->first();

                    $customer['tax_group_id'] = !empty($tax) ? $tax->id : null;
                }
            }
        
            /** Create supplier */
            Customer::updateOrCreate(
                [
                    'code' => $code,
                    'business_id' => $b->id
                ],
                $customer
            );
        }
    }
}
