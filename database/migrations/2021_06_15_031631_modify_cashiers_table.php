<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCashiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashiers', function (Blueprint $table) {
            $table->boolean("is_active")->after("status")->default(1);
            $table->unsignedInteger("last_open_by")->after("is_active")->nullable()->default(null);
            $table->unsignedInteger("last_close_by")->after("last_open_by")->nullable()->default(null);
            $table->dateTime("last_open")->after("last_close_by")->nullable()->default(null);
            $table->dateTime("last_close")->after("last_open")->nullable()->default(null);
            $table->unsignedInteger("last_cashier_closure")->after("last_close")->nullable()->default(null);
            DB::statement("ALTER TABLE cashiers MODIFY COLUMN status enum('open','close')
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;");

            $table->foreign("last_open_by")->references("id")->on("users");
            $table->foreign("last_close_by")->references("id")->on("users");
            $table->foreign("last_cashier_closure")->references("id")->on("cashier_closures");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashiers', function (Blueprint $table) {
            //
        });
    }
}
