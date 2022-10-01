<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add4DecimalsInTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE `transactions` 
                CHANGE `total_before_tax` `total_before_tax` 
                    DECIMAL(20,4) NOT NULL DEFAULT '0.0000'");
            DB::statement("ALTER TABLE `transactions` 
                CHANGE `tax_amount` `tax_amount` 
                    DECIMAL(20,4) NOT NULL DEFAULT '0.0000'");
            DB::statement("ALTER TABLE `transactions` 
                CHANGE `shipping_charges` `shipping_charges` 
                    DECIMAL(20,4) NOT NULL DEFAULT '0.0000'");
            DB::statement("ALTER TABLE `transactions` 
                CHANGE `final_total` `final_total` 
                    DECIMAL(20,4) NOT NULL DEFAULT '0.0000'");
            DB::statement("ALTER TABLE `transactions` 
                CHANGE `exchange_rate` `exchange_rate` 
                    DECIMAL(20,4) NOT NULL DEFAULT '1.0000'");
            DB::statement("ALTER TABLE `transactions` 
                CHANGE `total_amount_recovered` `total_amount_recovered` 
                    DECIMAL(20,4) NULL DEFAULT NULL 
                        COMMENT 'Used for stock adjustment.'");
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
}
