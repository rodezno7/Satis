<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnBankTransactionIdToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer("bank_transaction_id", false, true)
                ->nullable()
                ->default(null)
                ->after("location_id");

            $table->foreign("bank_transaction_id")
                ->references("id")
                ->on("bank_transactions");
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
            $table->dropForeign(["bank_transaction_id"]);
            $table->dropColumn("bank_transaction_id");
        });
    }
}
