<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsToTransactionSellLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN unit_price_before_discount DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN unit_price DECIMAL(20, 6) NULL");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN line_discount_amount DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN unit_price_inc_tax DECIMAL(20, 6) NULL");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN unit_price_exc_tax DECIMAL(20, 6) NOT NULL");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN tax_amount DECIMAL(20, 6) NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN unit_price_before_discount DECIMAL(20, 4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN unit_price DECIMAL(20, 4) NULL");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN line_discount_amount DECIMAL(20, 2) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN unit_price_inc_tax DECIMAL(20, 4) NULL");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN unit_price_exc_tax DECIMAL(20, 4) NOT NULL");
        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN tax_amount DECIMAL(10, 4) NOT NULL");
    }
}
