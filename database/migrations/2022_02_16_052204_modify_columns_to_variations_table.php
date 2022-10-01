<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsToVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE variations MODIFY COLUMN default_purchase_price DECIMAL(20, 6) NULL");
        DB::statement("ALTER TABLE variations MODIFY COLUMN dpp_inc_tax DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE variations MODIFY COLUMN profit_percent DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE variations MODIFY COLUMN default_sell_price DECIMAL(20, 6) NULL");
        DB::statement("ALTER TABLE variations MODIFY COLUMN sell_price_inc_tax DECIMAL(20, 6) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE variations MODIFY COLUMN default_purchase_price DECIMAL(20, 4) NULL");
        DB::statement("ALTER TABLE variations MODIFY COLUMN dpp_inc_tax DECIMAL(20, 4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE variations MODIFY COLUMN profit_percent DECIMAL(20, 4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE variations MODIFY COLUMN default_sell_price DECIMAL(20, 4) NULL");
        DB::statement("ALTER TABLE variations MODIFY COLUMN sell_price_inc_tax DECIMAL(20, 4) NULL");
    }
}
