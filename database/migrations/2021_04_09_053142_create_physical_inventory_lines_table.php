<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhysicalInventoryLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physical_inventory_lines', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('physical_inventory_id');
            $table->foreign('physical_inventory_id')->references('id')->on('physical_inventories')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('variation_id');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade')->onUpdate('cascade');

            $table->decimal('quantity', 20, 4)->default(0);

            $table->decimal('stock', 20, 4)->default(0);

            $table->decimal('difference', 20, 4)->default(0);

            $table->decimal('price', 20, 4)->default(0);

            $table->unsignedInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            
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
        Schema::dropIfExists('physical_inventory_lines');
    }
}
