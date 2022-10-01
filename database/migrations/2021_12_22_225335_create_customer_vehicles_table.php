<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_vehicles', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->string('license_plate')->nullable();

            $table->unsignedInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null')->onUpdate('cascade');

            $table->string('model')->nullable();

            $table->unsignedInteger('year')->nullable();

            $table->string('color')->nullable();

            $table->string('responsible')->nullable();

            $table->string('engine_number')->nullable();

            $table->string('vin_chassis')->nullable();

            $table->string('mi_km')->nullable();

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
        Schema::dropIfExists('customer_vehicles');
    }
}
