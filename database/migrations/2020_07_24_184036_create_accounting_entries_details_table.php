<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountingEntriesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_entries_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('entrie_id');
            $table->unsignedBigInteger('account_id');
            $table->foreign('entrie_id')->references('id')->on('accounting_entries')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('catalogues')->onDelete('cascade');
            $table->decimal('debit', 11, 2);
            $table->decimal('credit', 11, 2);
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
        Schema::dropIfExists('accounting_entries_details');
    }
}
