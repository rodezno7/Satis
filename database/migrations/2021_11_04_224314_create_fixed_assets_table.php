<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixedAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fixed_asset_type_id');
            $table->string('code', 25);
            $table->string('name', 50);
            $table->string('description')->nullable();
            $table->enum('type', ['new', 'used']);
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('location_id')->default(null)->nullable();
            $table->unsignedInteger('brand_id')->default(null)->nullable();
            $table->string('model', 50)->default(null)->nullable();
            $table->unsignedInteger('year')->default(null)->nullable();
            $table->decimal('initial_value', 12, 4);
            $table->decimal('current_value', 12, 4);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('fixed_asset_type_id')
                ->references('id')
                ->on('fixed_asset_types');
            $table->foreign('business_id')
                ->references('id')
                ->on('business');
            $table->foreign('location_id')
                ->references('id')
                ->on('business_locations');
            $table->foreign('brand_id')
                ->references('id')
                ->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixed_assets');
    }
}
