<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyQuantityColumnOnQuoteLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `quote_lines` CHANGE `quantity` `quantity` DECIMAL(8,2) NOT NULL");
        Schema::table('quote_lines', function (Blueprint $table) {
            //$table->decimal("quantity", 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            //
        });
    }
}
