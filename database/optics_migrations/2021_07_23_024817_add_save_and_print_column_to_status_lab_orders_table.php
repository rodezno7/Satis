<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSaveAndPrintColumnToStatusLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status_lab_orders', function (Blueprint $table) {
            $table->boolean('save_and_print')->default(0)->after('material_download');
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
            $table->dropColumn('save_and_print');
        });
    }
}
