<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitIdToKitHasProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kit_has_products', function (Blueprint $table) {
            $table->unsignedInteger('unit_id')->nullable()->after('quantity');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->unsignedInteger('unit_group_id_line')->nullable()->after('unit_id');
            $table->foreign('unit_group_id_line')->references('id')->on('unit_group_lines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kit_has_products', function (Blueprint $table) {
        });
    }
}
