<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add4DecimalsInPurchaseLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_lines', function (Blueprint $table) {
            DB::statement("ALTER TABLE `purchase_lines` 
                CHANGE `pp_without_discount` `pp_without_discount` 
                    DECIMAL(20,4) NOT NULL DEFAULT '0.0000' 
                        COMMENT 'Purchase price before inline discounts'");
            DB::statement("ALTER TABLE `purchase_lines` 
                CHANGE `discount_percent` `discount_percent` 
                    DECIMAL(8,4) NOT NULL DEFAULT '0.0000' 
                        COMMENT 'Inline discount percentage'");
            DB::statement("ALTER TABLE `purchase_lines` 
                CHANGE `purchase_price` `purchase_price` 
                    DECIMAL(20,4) NULL DEFAULT NULL");
            DB::statement("ALTER TABLE `purchase_lines` 
                CHANGE `purchase_price_inc_tax` `purchase_price_inc_tax` 
                    DECIMAL(20,4) NOT NULL DEFAULT '0.0000'");
            DB::statement("ALTER TABLE `purchase_lines` 
                CHANGE `item_tax` `item_tax` 
                    DECIMAL(20,4) NULL DEFAULT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_lines', function (Blueprint $table) {
            //
        });
    }
}
