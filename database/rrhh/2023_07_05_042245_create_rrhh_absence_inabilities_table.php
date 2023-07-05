<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhAbsenceInabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_absence_inabilities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('description');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('type_inability_id')->unsigned()->nullable();
            $table->foreign('type_inability_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('type_absence_id')->unsigned()->nullable();
            $table->foreign('type_absence_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('rrhh_absence_inabilities');
    }
}
