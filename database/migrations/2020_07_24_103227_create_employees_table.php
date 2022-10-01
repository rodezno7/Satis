<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id', 10);
            $table->string('name');
            $table->string('lastname')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->date('hireddate')->nullable();
            $table->date('fireddate')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('agentcode')->unique();
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
        Schema::dropIfExists('employees');
    }
}
