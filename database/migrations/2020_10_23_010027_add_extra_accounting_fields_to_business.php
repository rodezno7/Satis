<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraAccountingFieldsToBusiness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {

            $table->unsignedBigInteger('accounting_utility_id')->after('accounting_bank_id')->nullable();
            $table->foreign('accounting_utility_id')->references('id')->on('catalogues');

            $table->unsignedBigInteger('accounting_deficit_id')->after('accounting_utility_id')->nullable();
            $table->foreign('accounting_deficit_id')->references('id')->on('catalogues');

            $table->unsignedBigInteger('accounting_cost_id')->after('accounting_deficit_id')->nullable();
            $table->foreign('accounting_cost_id')->references('id')->on('catalogues');

            $table->unsignedBigInteger('accounting_debtor_result_id')->after('accounting_cost_id')->nullable();
            $table->foreign('accounting_debtor_result_id')->references('id')->on('catalogues');

            $table->unsignedBigInteger('accounting_creditor_result_id')->after('accounting_debtor_result_id')->nullable();
            $table->foreign('accounting_creditor_result_id')->references('id')->on('catalogues');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            //
        });
    }
}
