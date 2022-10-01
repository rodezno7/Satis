<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountsResultStateToBusiness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {

            $table->unsignedBigInteger('accounting_ordinary_incomes_id')->after('accounting_profit_and_loss_id')->nullable();
            $table->foreign('accounting_ordinary_incomes_id')->references('id')->on('catalogues');

            $table->unsignedBigInteger('accounting_return_sells_id')->after('accounting_ordinary_incomes_id')->nullable();
            $table->foreign('accounting_return_sells_id')->references('id')->on('catalogues');

            $table->unsignedBigInteger('accounting_sells_cost_id')->after('accounting_return_sells_id')->nullable();
            $table->foreign('accounting_sells_cost_id')->references('id')->on('catalogues');

            $table->unsignedBigInteger('accounting_ordinary_expenses_id')->after('accounting_sells_cost_id')->nullable();
            $table->foreign('accounting_ordinary_expenses_id')->references('id')->on('catalogues');

            $table->unsignedBigInteger('accounting_extra_incomes_id')->after('accounting_ordinary_expenses_id')->nullable();
            $table->foreign('accounting_extra_incomes_id')->references('id')->on('catalogues');

            $table->unsignedBigInteger('accounting_extra_expenses_id')->after('accounting_extra_incomes_id')->nullable();
            $table->foreign('accounting_extra_expenses_id')->references('id')->on('catalogues');

            $table->integer('level_childrens_ordynary_incomes')->nullable();
            $table->integer('level_childrens_ordynary_expenses')->nullable();
            $table->integer('level_childrens_extra_incomes')->nullable();
            $table->integer('level_childrens_extra_expenses')->nullable();

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
