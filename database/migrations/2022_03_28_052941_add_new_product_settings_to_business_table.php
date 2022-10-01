<?php

use App\Business;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewProductSettingsToBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $business = Business::get();

        foreach ($business as $item) {
            $product_settings = json_decode($item->product_settings, true);

            $default = [
                'show_stock_without_decimals' => isset($product_settings['show_stock_without_decimals']) ? $product_settings['show_stock_without_decimals'] : 0,
                'decimals_in_sales' => isset($product_settings['decimals_in_sales']) ? $product_settings['decimals_in_sales'] : 4,
                'decimals_in_purchases' => isset($product_settings['decimals_in_purchases']) ? $product_settings['decimals_in_purchases'] : 4,
                'decimals_in_inventories' => isset($product_settings['decimals_in_inventories']) ? $product_settings['decimals_in_inventories'] : 4,
                'decimals_in_fiscal_documents' => isset($product_settings['decimals_in_fiscal_documents']) ? $product_settings['decimals_in_fiscal_documents'] : 2,
                'product_rotation' => null
            ];

            $item->product_settings = json_encode($default);
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
