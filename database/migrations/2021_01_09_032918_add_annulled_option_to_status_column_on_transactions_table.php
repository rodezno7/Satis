<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnnulledOptionToStatusColumnOnTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `transactions` CHANGE `status` `status` ENUM('received','pending','ordered','draft','final','annulled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
}
