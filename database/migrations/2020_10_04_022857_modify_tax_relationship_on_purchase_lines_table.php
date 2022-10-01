<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTaxRelationshipOnPurchaseLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->dropForeign("purchase_lines_tax_id_foreign");

            $table->foreign("tax_id")
                ->references("id")
                ->on("tax_groups")
                ->onDelete("cascade");
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
            $table->dropForeign(['tax_id']);

            $table->foreign('tax_id')
                ->references('id')
                ->on('tax_rates')
                ->onDelete('cascade');
        });
    }
}
