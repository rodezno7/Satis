<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImplementOpeningCashInCashierClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE cashier_closures CHANGE correlative close_correlative varchar(191)
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' NOT NULL;");

        Schema::table('cashier_closures', function (Blueprint $table) {
            $table->string('open_correlative')
                ->after('total_return_amount')
                ->default('')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashier_closures', function (Blueprint $table) {
            //
        });
    }
}
