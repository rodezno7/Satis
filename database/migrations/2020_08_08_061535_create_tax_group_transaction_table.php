<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxGroupTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_group_transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tax_group_id');
            $table->unsignedInteger('transaction_id');
            
            $table->timestamps();
        });

        Schema::table('tax_group_transaction', function (Blueprint $table) {
            $table->foreign('tax_group_id')->references('id')->on('tax_groups')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_group_transaction');
    }
}
