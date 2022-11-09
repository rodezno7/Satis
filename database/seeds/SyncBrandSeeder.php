<?php

use App\Brands;
use App\Business;
use App\Utils\ProductUtil;
use Illuminate\Database\Seeder;

class SyncBrandSeeder extends Seeder
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
            $brands = Brands::where('business_id', $business_id)->get();

            foreach ($brands as $br) {
                $this->productUtil->syncBrand($br->id, $br->name);
            }
        }
    }
}
