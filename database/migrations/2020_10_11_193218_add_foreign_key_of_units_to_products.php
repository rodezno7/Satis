<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOfUnitsToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('unit_id')->nullable()->after('type');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');


            $table->unsignedInteger('unit_group_id')->nullable()->after('unit_id');
            $table->foreign('unit_group_id')->references('id')->on('unit_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
