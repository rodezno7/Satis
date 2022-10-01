<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenedAtColumnToRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->dateTime('opened_at')
                ->nullable()
                ->after('total_amount_check');
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
            $table->dropColumn('opened_at');
        });
    }
}
