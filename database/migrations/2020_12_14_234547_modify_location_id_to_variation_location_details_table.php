<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyLocationIdToVariationLocationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });

        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->nullable()->change();
            $table->foreign('location_id')->references('id')->on('business_locations');
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
