<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderStatusToQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `quotes` ADD `order_status`
            ENUM('pending','prepared') NULL DEFAULT NULL AFTER `type`");

        Schema::table('quotes', function (Blueprint $table) {
            /*$table->enum("order_status", ["pending", "prepared"])
                ->default(null)
                ->nullable()
                ->after("type");*/
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
            $table->dropColumn("order_status");
        });
    }
}
