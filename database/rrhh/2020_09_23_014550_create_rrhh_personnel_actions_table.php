<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhPersonnelActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_personnel_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('effective_date')->nullable();
            
            $table->integer('payment_id')->nullable();
            $table->integer('bank_id')->nullable();
            $table->string('bank_account')->nullable();

            $table->boolean('authorized')->nullable();
            $table->date('authorization_date')->nullable();

            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('rrhh_type_personnel_action_id')->unsigned();
            $table->foreign('rrhh_type_personnel_action_id')->references('id')->on('rrhh_type_personnel_actions')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('rrhh_personnel_actions');
    }
}
