<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaterialDownloadColumnToStatusLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status_lab_orders', function (Blueprint $table) {
            $table->boolean('material_download')->default(0)->after('second_time');
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
            $table->dropColumn('material_download');
        });
    }
}
