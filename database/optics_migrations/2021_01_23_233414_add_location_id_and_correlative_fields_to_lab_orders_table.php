<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationIdAndCorrelativeFieldsToLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_orders', function (Blueprint $table) {
            $table->unsignedInteger('business_location_id')->nullable()->after('transaction_id');
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade')->onUpdate('cascade');

            $table->string('correlative')->nullable()->after('business_location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lab_orders', function (Blueprint $table) {
            //
        });
    }
}
