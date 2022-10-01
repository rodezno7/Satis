<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettingsColumnsToStatusLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status_lab_orders', function (Blueprint $table) {
            $table->boolean('is_default')->default(0)->after('status');
            $table->boolean('print_order')->default(0)->after('is_default');
            $table->boolean('transfer_sheet')->default(0)->after('print_order');
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
            $table->dropColumn('is_default');
            $table->dropColumn('print_order');
            $table->dropColumn('transfer_sheet');
        });
    }
}
