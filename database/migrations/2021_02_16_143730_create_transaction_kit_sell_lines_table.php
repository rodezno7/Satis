<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionKitSellLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_kit_sell_lines', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            
            $table->unsignedInteger('variation_id');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');

            $table->decimal('quantity', 10, 4);
            
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
        Schema::dropIfExists('transaction_kit_sell_lines');
    }
}
