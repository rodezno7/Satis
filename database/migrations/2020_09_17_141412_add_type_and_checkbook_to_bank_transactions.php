<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeAndCheckbookToBankTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_transactions', function (Blueprint $table) {

            $table->dropColumn('type');

            $table->unsignedInteger('type_bank_transaction_id')->after('accounting_entrie_id')->nullable();
            $table->foreign('type_bank_transaction_id')->references('id')->on('type_bank_transactions')->onDelete('cascade');

            $table->unsignedInteger('bank_checkbook_id')->after('type_bank_transaction_id')->nullable();
            $table->foreign('bank_checkbook_id')->references('id')->on('bank_checkbooks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_transactions', function (Blueprint $table) {
            //
        });
    }
}
