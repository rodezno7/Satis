<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountBusinessLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_business_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('location_id');
            $table->unsignedBigInteger('general_cash_id')->nullable()->default(null);
            $table->unsignedBigInteger('inventory_account_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_receivable_id')->nullable()->default(null);
            $table->unsignedBigInteger('vat_final_customer_id')->nullable()->default(null);
            $table->unsignedBigInteger('vat_taxpayer_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_payable_id')->nullable()->default(null);
            $table->unsignedBigInteger('sale_cost_id')->nullable()->default(null);
            $table->unsignedBigInteger('sale_expense_id')->nullable()->default(null);
            $table->unsignedBigInteger('admin_expense_id')->nullable()->default(null);
            $table->unsignedBigInteger('financial_expense_id')->nullable()->default(null);
            $table->unsignedBigInteger('local_sale_id')->nullable()->default(null);
            $table->unsignedBigInteger('exports_id')->nullable()->default(null);
            $table->timestamps();

            /** Relationships */
            $table->foreign('location_id')->references('id')->on('business_locations');
            $table->foreign('general_cash_id')->references('id')->on('catalogues');
            $table->foreign('inventory_account_id')->references('id')->on('catalogues');
            $table->foreign('account_receivable_id')->references('id')->on('catalogues');
            $table->foreign('vat_final_customer_id')->references('id')->on('catalogues');
            $table->foreign('vat_taxpayer_id')->references('id')->on('catalogues');
            $table->foreign('account_payable_id')->references('id')->on('catalogues');
            $table->foreign('sale_cost_id')->references('id')->on('catalogues');
            $table->foreign('sale_expense_id')->references('id')->on('catalogues');
            $table->foreign('admin_expense_id')->references('id')->on('catalogues');
            $table->foreign('financial_expense_id')->references('id')->on('catalogues');
            $table->foreign('local_sale_id')->references('id')->on('catalogues');
            $table->foreign('exports_id')->references('id')->on('catalogues');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_business_locations');
    }
}
