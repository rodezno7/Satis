<?php

use App\Product;
use App\Business;
use App\Utils\ProductUtil;
use Illuminate\Database\Seeder;

class SyncProductSeeder extends Seeder
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
            /** Get products from other business */
            $products = Product::where('business_id', $business_id)
                ->where('clasification', 'product')
                ->select('id', 'sku')->get();

            foreach ($products as $p) {
                $this->productUtil->syncProduct($p->id, $p->sku);
            }

            /** Get kits and service from other business */
            $products = Product::where('business_id', $business_id)
                ->where('clasification', '!=', 'product')
                ->select('id', 'sku')->get();

            foreach ($products as $p) {
                $this->productUtil->syncProduct($p->id, $p->sku);
            }
        }
    }
}
