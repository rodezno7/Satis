<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyStatusColumnToQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `quotes` DROP COLUMN `order_status`");
        DB::statement("ALTER TABLE `quotes` ADD `status`
           ENUM('opened', 'in_preparation', 'prepared', 'on_route', 'expired',
          'closed', 'returned') NULL DEFAULT NULL AFTER `type`");
        DB::statement("ALTER TABLE `quotes` ADD `delivery_type`
            ENUM('at_home','route','caex','location','other')
            NOT NULL DEFAULT 'location' AFTER `delivery_time`");

        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedInteger("selling_price_group_id")
                ->nullable()
                ->default(null)
                ->after("tax_detail");

            $table->foreign("selling_price_group_id")
                ->references("id")
                ->on("selling_price_groups");

            $table->date("delivery_date")
                ->nullable()
                ->default(null)
                ->after("delivery_time");

            $table->string("other_delivery_type")
                ->nullable()
                ->default("")
                ->after("delivery_type");
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
