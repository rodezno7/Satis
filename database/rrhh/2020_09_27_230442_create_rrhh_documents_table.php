<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_documents', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('number');
            $table->string('file')->nullable();
            $table->date('date_expedition')->nullable();
            $table->date('date_expiration')->nullable();

            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('document_type_id')->unsigned();
            $table->foreign('document_type_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('city_id')->unsigned()->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('rrhh_documents');
    }
}
