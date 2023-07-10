<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhPositionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_position_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('previous_department_id')->unsigned()->nullable();
            $table->foreign('previous_department_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('previous_position1_id')->unsigned()->nullable();
            $table->foreign('previous_position1_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('new_department_id')->unsigned();
            $table->foreign('new_department_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('new_position1_id')->unsigned()->nullable();
            $table->foreign('new_position1_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('rrhh_personnel_action_id')->unsigned()->nullable();
            $table->foreign('rrhh_personnel_action_id', 'rrhh_pa_id_foreign')->references('id')->on('rrhh_personnel_actions')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('current')->default(false);
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
        Schema::dropIfExists('rrhh_position_histories');
    }
}
