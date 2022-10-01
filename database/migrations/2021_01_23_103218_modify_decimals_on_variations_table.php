<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDecimalsOnVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variations', function (Blueprint $table) {
            $table->decimal('default_purchase_price', 20, 4)->change();
            $table->decimal('dpp_inc_tax', 20, 4)->change();
            $table->decimal('profit_percent', 20, 4)->change();
            $table->decimal('default_sell_price', 20, 4)->change();
            $table->decimal('sell_price_inc_tax', 20, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variations', function (Blueprint $table) {
            //
        });
    }
}
