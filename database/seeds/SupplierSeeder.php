<?php

use App\City;
use App\State;
use App\Contact;
use App\Country;
use App\Business;
use App\TaxGroup;
use App\Utils\ContactUtil;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
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

        $suppliers = Contact::where('type', 'supplier')
            ->where('business_id', $business_id)
            ->select('id', 'contact_id')->get();

        foreach ($suppliers as $s) {
            $this->syncSupplier($s->id, $s->contact_id, $business_id);
        }
    }

    /**
     * Sync Suppliers
     * 
     * @param int $id
     * @param string $code
     * @param int $business_id
     * 
     * @return void
     * @author Arquímides Martínez
     */
    public function syncSupplier($id, $code, $business_id) {
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
            $supplier = Contact::findOrFail($id)->toArray();
            $supplier['business_id'] = $b->id;
            $supplier['supplier_catalogue_id'] = null;

            /** Remove needless columns */
            unset(
                $supplier['id'],
                $supplier['created_at'],
                $supplier['updated_at']
            );

            /** Country */
            if (!is_null($supplier['country_id'])) {
                $country = Country::findOrFail($supplier['country_id']);

                if (!empty($country)) {
                    $country = Country::where('short_name', $country->short_name)
                        ->where('business_id', $b->id)
                        ->first();

                    $supplier['country_id'] = !empty($country) ? $country->id : null;

                    /** State */
                    if (!is_null($supplier['state_id'])) {
                        $state = State::findOrFail($supplier['state_id']);

                        if (!empty($state)) {
                            $state = State::where('name', $state->name)
                                ->where('business_id', $b->id)
                                ->first();

                            $supplier['state_id'] = !empty($state) ? $state->id : null;

                            /** City */
                            if (!is_null($supplier['city_id'])) {
                                $city = City::findOrFail($supplier['city_id']);

                                if (!empty($city)) {
                                    $city = City::where('name', $city->name)
                                        ->where('state_id', $state->id)
                                        ->where('business_id', $b->id)
                                        ->first();

                                    $supplier['city_id'] = !empty($city) ? $city->id : null;
                                }
                            }
                        }
                    }
                }
            }

            /** Tax Group */
            if (!is_null($supplier['tax_group_id'])) {
                $tax = TaxGroup::findOrFail($supplier['tax_group_id']);

                if (!empty($tax)) {
                    $tax = TaxGroup::where('description', $tax->description)
                        ->where('business_id', $b->id)
                        ->first();

                    $supplier['tax_group_id'] = !empty($tax) ? $tax->id : null;
                }
            }

            /** Create supplier */
            Contact::updateOrCreate(
                [
                    'contact_id' => $code,
                    'business_id' => $b->id
                ],
                $supplier
            );
        }
    }
}
