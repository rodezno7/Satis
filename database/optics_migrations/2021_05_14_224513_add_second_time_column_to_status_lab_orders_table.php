<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecondTimeColumnToStatusLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status_lab_orders', function (Blueprint $table) {
            $table->boolean('second_time')->default(0)->after('transfer_sheet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('status_lab_orders', function (Blueprint $table) {
            $table->dropColumn('second_time');
        });
    }
}
