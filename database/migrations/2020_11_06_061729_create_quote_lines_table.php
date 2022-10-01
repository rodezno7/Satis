<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuoteLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quote_id');
            $table->foreign('quote_id')->references('id')->on('quotes')->onDelete('cascade');
            $table->unsignedInteger('variation_id');
            $table->foreign('variation_id')->references('id')->on('variations');
            $table->decimal('quantity', 10, 4);
            $table->decimal('unit_price_exc_tax', 10, 4);
            $table->decimal('unit_price_inc_tax', 10, 4);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_amount', 10, 4)->default(0.0000)->nullable();
            $table->decimal('tax_amount', 10, 4)->default(0.0000)->nullable();
            $table->string('warranty')->default(null)->nullable();
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
        Schema::dropIfExists('quote_lines');
    }
}
