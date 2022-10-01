<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerVehicleIdToQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedInteger('customer_vehicle_id')->nullable()->after('warehouse_id');
            $table->foreign('customer_vehicle_id')->references('id')->on('customer_vehicles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['customer_vehicle_id']);
            $table->dropColumn('customer_vehicle_id');
        });
    }
}
