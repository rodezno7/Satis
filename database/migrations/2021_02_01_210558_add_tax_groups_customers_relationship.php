<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxGroupsCustomersRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedInteger("tax_group_id")
                ->nullable()
                ->after("dni");

            $table->foreign("tax_group_id")
                ->on("tax_groups")
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
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign("tax_group_id");
            $table->dropColumn("tax_group_id");
        });
    }
}
