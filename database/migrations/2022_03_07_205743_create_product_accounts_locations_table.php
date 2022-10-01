<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductAccountsLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_accounts_locations', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('type', ['inventory', 'cost', 'input']);
            $table->unsignedInteger("product_id");
            $table->unsignedInteger("location_id");
            $table->unsignedBigInteger("catalogue_id");

            $table->foreign("product_id")->references("id")->on("products");
            $table->foreign("location_id")->references("id")->on("business_locations");
            $table->foreign("catalogue_id")->references("id")->on("catalogues");

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
        Schema::dropIfExists('product_accounts_locations');
    }
}
