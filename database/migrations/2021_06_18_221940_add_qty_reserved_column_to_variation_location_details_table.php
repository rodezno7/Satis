<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQtyReservedColumnToVariationLocationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->decimal('qty_reserved', 20, 4)->nullable()->default(0)->after('qty_available');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->dropColumn('qty_reserved');
        });
    }
}
