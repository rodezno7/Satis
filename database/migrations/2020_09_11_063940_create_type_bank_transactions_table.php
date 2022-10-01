<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_bank_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('type', ['debit', 'credit']);

            $table->unsignedInteger('type_entrie_id')->nullable();
            $table->foreign('type_entrie_id')->references('id')->on('type_entries')->onDelete('cascade');

            $table->boolean('enable_checkbook')->default(0);
            $table->boolean('enable_headline')->default(0);

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
        Schema::dropIfExists('type_bank_transactions');
    }
}
