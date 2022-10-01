<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApportionmentHasTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apportionment_has_transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('apportionment_id');
            $table->foreign('apportionment_id')->references('id')->on('apportionments')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apportionment_has_transactions');
    }
}
