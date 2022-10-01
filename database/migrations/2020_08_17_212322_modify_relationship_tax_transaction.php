<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyRelationshipTaxTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_tax_id_foreign');

            $table->foreign('tax_id')
                ->references('id')
                ->on('tax_groups')
                ->onDelete('cascade');
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
            $table->dropForeign(['tax_id']);

            $table->foreign('tax_id')
                ->references('id')
                ->on('tax_rates')
                ->onDelete('cascade');
        });
    }
}
