<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditHasFamilyMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_has_family_members', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('credit_id')->nullable();
            $table->foreign('credit_id')->references('id')->on('credit_requests')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->enum('relationship', ['Padre', 'Madre', 'Hijo/a', 'Hermano/a', 'Tío/a', 'Primo/a', 'Abuelo/a']);
            $table->string('phone');
            $table->string('address');
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
        Schema::dropIfExists('credit_has_family_members');
    }
}
