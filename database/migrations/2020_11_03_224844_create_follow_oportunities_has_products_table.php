<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowOportunitiesHasProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_oportunities_has_products', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('follow_oportunitie_id')->nullable();
            $table->foreign('follow_oportunitie_id')->references('id')->on('follow_oportunities')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('follow_oportunities_has_products');
    }
}
