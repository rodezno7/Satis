<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnIsTaxGroupFromRateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_rates', function (Blueprint $table) {
            $table->dropColumn("is_tax_group");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tax_rates', function (Blueprint $table) {
            $table->smallInteger("is_tax_group")
                ->default(0);
        });
    }
}
