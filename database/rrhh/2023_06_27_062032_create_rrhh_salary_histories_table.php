<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhSalaryHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_salary_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('salary', 10, 2);
            $table->boolean('current')->default(false);
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('rrhh_personnel_action_id')->unsigned()->nullable();
            $table->foreign('rrhh_personnel_action_id', 'rrhh_pa_id_foreign')->references('id')->on('rrhh_personnel_actions')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('rrhh_salary_histories');
    }
}
