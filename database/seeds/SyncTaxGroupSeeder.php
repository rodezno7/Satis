<?php

use App\Business;
use App\TaxGroup;
use App\Utils\ProductUtil;
use Illuminate\Database\Seeder;

class SyncTaxGroupSeeder extends Seeder
{
    private $productUtil;

    /**
     * Constructor
     * 
     * @param App\Utils\ProductUtil
     * @return void
     */
    public function __construct(ProductUtil $productUtil) {
        $this->productUtil = $productUtil;
    }

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

        $business = Business::where('id', '!=', $business_id)->get();
        
        foreach ($business as $b) {
            /** Get brands from other business */
            $tax_groups = TaxGroup::where('business_id', $business_id)->get();

            foreach ($tax_groups as $tg) {
                $this->productUtil->syncTaxGroup($tg->id, $tg->description);
            }
        }
    }
}
