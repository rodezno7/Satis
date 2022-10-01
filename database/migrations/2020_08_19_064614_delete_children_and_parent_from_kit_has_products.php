<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteChildrenAndParentFromKitHasProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kit_has_products', function (Blueprint $table) {
            $table->dropForeign('kit_has_products_parent_id_foreign');
            $table->dropColumn('parent_id');

            $table->dropForeign('kit_has_products_children_id_foreign');
            $table->dropColumn('children_id');
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
