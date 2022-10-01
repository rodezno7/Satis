<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKitHasProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kit_has_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('parent_id');
            $table->unsignedInteger('children_id');
            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('children_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('quantity');
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
        Schema::dropIfExists('kit_has_products');
    }
}
