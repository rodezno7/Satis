<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPriceIncTaxDecimalsOnVariationGroupPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `variation_group_prices` CHANGE `price_inc_tax` `price_inc_tax` DECIMAL(20,4) NOT NULL;");
        Schema::table('variation_group_prices', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variation_group_prices', function (Blueprint $table) {
            //
        });
    }
}
