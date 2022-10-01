<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN total_before_tax DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN tax_amount DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN tax_group_amount DECIMAL(20, 6) NULL");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN shipping_charges DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN dai_amount DOUBLE(20, 6) NULL DEFAULT 0");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN final_total DECIMAL(20, 6) NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN total_before_tax DECIMAL(20, 4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN tax_amount DECIMAL(20, 4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN tax_group_amount DECIMAL(20, 4) NULL");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN shipping_charges DECIMAL(20, 4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN dai_amount DOUBLE(10, 4) NULL DEFAULT 0");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN final_total DECIMAL(20, 4) NOT NULL DEFAULT 0");
    }
}
