<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowCustomersHasProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_customers_has_products', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('follow_customer_id')->nullable();
            $table->foreign('follow_customer_id')->references('id')->on('follow_customers')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('variation_id')->nullable();
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('quantity');

            $table->integer('required_quantity');

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
        Schema::dropIfExists('follow_customers_has_products');
    }
}
