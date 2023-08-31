<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanillasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planillas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_planilla_id')->unsigned();
            $table->foreign('type_planilla_id')->references('id')->on('type_planillas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('approval_date')->nullable();
            
            $table->integer('planilla_status_id')->unsigned();
            $table->foreign('planilla_status_id')->references('id')->on('planilla_statuses')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('payment_period_id')->unsigned();
            $table->foreign('payment_period_id')->references('id')->on('payment_periods')->onDelete('cascade')->onUpdate('cascade');
            
            $table->integer('calculation_type_id')->unsigned();
            $table->foreign('calculation_type_id')->references('id')->on('calculation_types')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('business_id')->unsigned()->nullable();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('planillas');
    }
}
