<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhEconomicDependencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_economic_dependences', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->date('birthdate');
            $table->string('phone')->nullable();
            $table->boolean('status');
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('type_relationship_id')->unsigned();
            $table->foreign('type_relationship_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('rrhh_economic_dependences');
    }
}
