<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountingPeriodIdToEntrie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->unsignedInteger('accounting_period_id')->after('description')->nullable();          

            $table->foreign('accounting_period_id')->references('id')->on('accounting_periods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            //
        });
    }
}
