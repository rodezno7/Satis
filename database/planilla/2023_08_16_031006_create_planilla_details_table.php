<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanillaDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planilla_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('days');
            $table->integer('hours');

            $table->decimal('commissions', 10, 2)->nullable();
            $table->integer('number_daytime_overtime')->nullable();
            $table->decimal('daytime_overtime', 10, 2)->nullable();
            $table->integer('number_night_overtime_hours')->nullable();
            $table->decimal('night_overtime_hours', 10, 2)->nullable();

            $table->decimal('isss', 10, 2)->nullable();
            $table->decimal('afp', 10, 2)->nullable();
            $table->decimal('renta', 10, 2)->nullable();
            $table->decimal('other_deductions', 10, 2)->nullable();

            $table->integer('employee_id')->unsigned()->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->integer('planilla_id')->unsigned()->nullable();
            $table->foreign('planilla_id')->references('id')->on('planillas')->onDelete('cascade');
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
        Schema::dropIfExists('planilla_details');
    }
}
