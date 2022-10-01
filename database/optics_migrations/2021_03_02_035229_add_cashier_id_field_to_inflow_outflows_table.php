<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCashierIdFieldToInflowOutflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inflow_outflows', function (Blueprint $table) {
            $table->unsignedInteger('cashier_id')->nullable()->default(null)->after('business_id');
            $table->foreign('cashier_id')->on('cashiers')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inflow_outflows', function (Blueprint $table) {
            $table->dropForeign(['cashier_id']);
            $table->dropColumn('cashier_id');
        });
    }
}
