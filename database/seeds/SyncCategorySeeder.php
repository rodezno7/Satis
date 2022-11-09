<?php

use App\Business;
use App\Category;
use App\Utils\ProductUtil;
use Illuminate\Database\Seeder;

class SyncCategorySeeder extends Seeder
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
            /** Get categories from other business */
            $categories = Category::where('business_id', $business_id)
                ->where('parent_id', '0')->get();

            foreach ($categories as $c) {
                $this->productUtil->syncCategory($c->id, $c->name);
            }

            /** Get sub categories from other business */
            $sub_categories = Category::where('business_id', $business_id)
                ->where('parent_id', '!=', '0')->get();

            foreach ($sub_categories as $sc) {
                $this->productUtil->syncCategory($sc->id, $sc->name);
            }
        }
    }
}
