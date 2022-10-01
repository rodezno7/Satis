<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentCommitmentLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_commitment_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_commitment_id');
            $table->unsignedInteger('transaction_id')->nullable()->default(null);
            $table->string('document_name');
            $table->string('reference', 50);
            $table->decimal('total', 12, 4);
            $table->timestamps();

            $table->foreign('payment_commitment_id')
                ->references('id')
                ->on('payment_commitments');
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_commitment_lines');
    }
}
