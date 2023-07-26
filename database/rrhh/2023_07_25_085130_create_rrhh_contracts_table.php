<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->boolean('status')->default(1);
            $table->string('name_employee');
            $table->integer('age_employee')->nullable();
            $table->string('dni_employee')->nullable();
            $table->string('tax_number_employee')->nullable();
            $table->string('state_employee')->nullable();
            $table->string('city_employee')->nullable();
            $table->string('salary_employee')->nullable();
            $table->string('department_employee')->nullable();
            $table->string('position_employee')->nullable();
            $table->string('name_business');
            $table->string('tax_number_business')->nullable();
            $table->string('state_business')->nullable();
            //$table->string('city_business');
            $table->string('current_date_letters');
            
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('rrhh_type_contract_id')->unsigned();
            $table->foreign('rrhh_type_contract_id')->references('id')->on('rrhh_type_contracts')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('rrhh_contracts');
    }
}
