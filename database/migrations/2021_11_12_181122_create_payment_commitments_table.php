<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentCommitmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_commitments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference', 50);
            $table->date('date');
            $table->enum("type", ["automatic", "manual"]);
            $table->unsignedInteger('supplier_id');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('location_id');
            $table->decimal('total', 12, 4);
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('contacts');
            $table->foreign('business_id')
                ->references('id')
                ->on('business');
            $table->foreign('location_id')
                ->references('id')
                ->on('business_locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_commitments');
    }
}
