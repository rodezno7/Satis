<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedInteger('bank_id');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('location_id');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('bank_id')
                ->references('id')
                ->on('banks');

            $table->foreign('business_id')
                ->references('id')
                ->on('business');

            $table->foreign('location_id')
                ->references('id')
                ->on('business_locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos');
    }
}
