<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsToQuoteLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE quote_lines MODIFY COLUMN unit_price_exc_tax DECIMAL(20, 6) NOT NULL");
        DB::statement("ALTER TABLE quote_lines MODIFY COLUMN unit_price_inc_tax DECIMAL(20, 6) NOT NULL");
        DB::statement("ALTER TABLE quote_lines MODIFY COLUMN discount_amount DECIMAL(20, 6) NULL DEFAULT 0");
        DB::statement("ALTER TABLE quote_lines MODIFY COLUMN tax_amount DECIMAL(20, 6) NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE quote_lines MODIFY COLUMN unit_price_exc_tax DECIMAL(10, 4) NOT NULL");
        DB::statement("ALTER TABLE quote_lines MODIFY COLUMN unit_price_inc_tax DECIMAL(20, 4) NOT NULL");
        DB::statement("ALTER TABLE quote_lines MODIFY COLUMN discount_amount DECIMAL(10, 4) NULL DEFAULT 0");
        DB::statement("ALTER TABLE quote_lines MODIFY COLUMN tax_amount DECIMAL(10, 4) NULL DEFAULT 0");
    }
}
