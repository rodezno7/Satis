<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsToPurchaseLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN pp_without_discount DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN purchase_price DECIMAL(20, 6) NULL");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN purchase_price_inc_tax DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN item_tax DECIMAL(20, 6) NULL");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN tax_amount DECIMAL(20, 6) NULL");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN dai_amount DECIMAL(20, 6) NULL DEFAULT 0");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN initial_purchase_price DECIMAL(20, 6) NULL");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN import_expense_amount DECIMAL(20, 6) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN pp_without_discount DECIMAL(20, 4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN purchase_price DECIMAL(20, 4) NULL");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN purchase_price_inc_tax DECIMAL(20, 4) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN item_tax DECIMAL(20, 4) NULL");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN tax_amount DECIMAL(8, 4) NULL");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN dai_amount DECIMAL(20, 4) NULL DEFAULT 0");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN initial_purchase_price DECIMAL(20, 4) NULL");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN import_expense_amount DECIMAL(20, 4) NULL");
    }
}
