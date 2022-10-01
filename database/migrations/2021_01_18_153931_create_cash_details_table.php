<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cash_register_id');
            $table->unsignedInteger('cashier_id');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('location_id');
            $table->integer('one_cent')->nullable()->default(null);
            $table->integer('five_cents')->nullable()->default(null);
            $table->integer('ten_cents')->nullable()->default(null);
            $table->integer('twenty_five_cents')->nullable()->default(null);
            $table->integer('one_dollar')->nullable()->default(null);
            $table->integer('five_dollars')->nullable()->default(null);
            $table->integer('ten_dollars')->nullable()->default(null);
            $table->integer('twenty_dollars')->nullable()->default(null);
            $table->integer('fifty_dollars')->nullable()->default(null);
            $table->integer('one_hundred_dollars')->nullable()->default(null);
            $table->timestamps();

            $table->foreign("cash_register_id")
                ->references("id")
                ->on("cash_registers");
            $table->foreign("cashier_id")
                ->references("id")
                ->on("cashiers");
            $table->foreign("business_id")
                ->references("id")
                ->on("business");
            $table->foreign("location_id")
                ->references("id")
                ->on("business_locations");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_details');
    }
}
