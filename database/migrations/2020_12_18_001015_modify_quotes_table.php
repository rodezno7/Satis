<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedInteger("state_id")
                ->nullable()
                ->default(null)
                ->after("mobile");
            $table->unsignedInteger("city_id")
                ->nullable()
                ->default(null)
                ->after("state_id");
            $table->string("landmark")
                ->nullable()
                ->default("")
                ->after("address");

            $table->foreign("state_id")
                ->references("id")
                ->on("states");
            $table->foreign("city_id")
                ->references("id")
                ->on("cities");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotes', function (Blueprint $table) {
            //
        });
    }
}
