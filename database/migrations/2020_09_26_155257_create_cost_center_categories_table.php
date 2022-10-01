<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostCenterCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cost_center_categories', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('cost_center_id');
            $table->foreign('cost_center_id')->references('id')->on('cost_centers')->onDelete('cascade');

            $table->string('name');
            $table->string('description')->nullable();
            
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
        Schema::dropIfExists('cost_center_categories');
    }
}
