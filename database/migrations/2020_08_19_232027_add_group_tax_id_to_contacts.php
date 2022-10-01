<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupTaxIdToContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedInteger('tax_group_id')
                ->nullable()
                ->after('type');
            $table->foreign('tax_group_id')
                ->references('id')
                ->on('tax_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign('tax_group_id');
            $table->dropColumn('tax_group_id');
        });
    }
}
