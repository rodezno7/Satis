<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('full_name');
            $table->integer('age');
            $table->enum('sex', ['female', 'male', 'other']);
            $table->string('address')->nullable();
            $table->string('contacts')->nullable();
            $table->string('email')->nullable();
            $table->boolean('glasses')->default(false);
            $table->string('glasses_graduation')->nullable();
            $table->integer('business_id')->unsigned()->nullable();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('location_id')->unsigned()->nullable();
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('register_by')->unsigned()->nullable();
            $table->foreign('register_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('patients');
    }
}
