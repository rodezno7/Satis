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
            $table->string('employee_name');
            $table->integer('employee_age')->nullable();
            $table->string('employee_dni')->nullable();
            $table->string('employee_tax_number')->nullable();
            $table->string('employee_state')->nullable();
            $table->string('employee_city')->nullable();
            $table->string('employee_salary')->nullable();
            $table->string('employee_department')->nullable();
            $table->string('employee_position')->nullable();
            $table->string('business_name');
            $table->string('business_tax_number')->nullable();
            $table->string('business_state')->nullable();
            $table->string('current_date_letters');
            $table->text('template');
            
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
