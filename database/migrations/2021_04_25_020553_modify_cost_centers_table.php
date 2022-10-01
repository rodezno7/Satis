<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCostCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cost_centers', function (Blueprint $table) {
            $table->unsignedInteger("business_id")
                ->after("id");
            $table->unsignedInteger("location_id")
                ->after("business_id");

            $table->foreign("business_id")
                ->on("business")
                ->references("id");
            $table->foreign("location_id")
                ->on("business_locations")
                ->references("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cost_centers', function (Blueprint $table) {
            $table->dropForeign("business_id");
            $table->dropForeign("location_id");

            $table->dropColumn("business_id");
            $table->dropColumn("location_id");
        });
    }
}
