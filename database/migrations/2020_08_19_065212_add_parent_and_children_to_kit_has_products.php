<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentAndChildrenToKitHasProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kit_has_products', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->after('id');
            $table->unsignedInteger('children_id')->after('parent_id');
            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('children_id')->references('id')->on('variations')->onDelete('cascade');
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
            //
        });
    }
}
