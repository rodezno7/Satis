<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusLabOrderStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_lab_order_steps', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('status_id');
            $table->foreign('status_id')->references('id')->on('status_lab_orders')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('step_id');
            $table->foreign('step_id')->references('id')->on('status_lab_orders')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('status_lab_order_steps');
    }
}
