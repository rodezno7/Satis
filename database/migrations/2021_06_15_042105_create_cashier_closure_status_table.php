<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashierClosureStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashier_closure_status', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cashier_closure_id');
            $table->unsignedInteger('status_id')->nullable();
            $table->timestamps();

            $table->foreign('cashier_closure_id')->references('id')->on('cashier_closures');
            $table->foreign('status_id')->references('id')->on('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashier_closure_status');
    }
}
