<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCreditPaymentTermColumnFromTransactionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `transaction_payments` CHANGE `method` `method`
            ENUM('cash','card','check','bank_transfer') CHARACTER SET utf8mb4
            COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL; ");

        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->dropColumn('credit_payment_term');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            //
        });
    }
}
