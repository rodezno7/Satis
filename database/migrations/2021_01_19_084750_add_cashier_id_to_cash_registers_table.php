<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCashierIdToCashRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->unsignedInteger("cashier_id")
                ->nullable()
                ->default(null)
                ->after("user_id");
            $table->foreign("cashier_id")
                ->references("id")
                ->on("cashiers");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            //
        });
    }
}
