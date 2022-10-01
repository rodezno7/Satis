<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationAndWarehouseToLabOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_order_details', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->nullable()->after('variation_id');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('warehouse_id')->nullable()->after('location_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
