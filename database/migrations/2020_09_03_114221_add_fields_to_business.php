<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToBusiness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->boolean('enable_sub_accounts_in_bank_transactions')->default(1)->after('business_full_name');
            
            $table->unsignedBigInteger('accounting_supplier_id')->after('enable_sub_accounts_in_bank_transactions')->nullable();
            $table->unsignedBigInteger('accounting_customer_id')->after('accounting_supplier_id')->nullable();

            $table->foreign('accounting_supplier_id')->references('id')->on('catalogues');
            $table->foreign('accounting_customer_id')->references('id')->on('catalogues');
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
