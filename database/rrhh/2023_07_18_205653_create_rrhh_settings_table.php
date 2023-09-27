<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('automatic_closing');
            $table->time('exit_time')->nullable();
            $table->decimal('exempt_bonus', 10, 2)->nullable();
            $table->decimal('vacation_percentage', 10, 2)->nullable();
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('rrhh_settings');
    }
}
