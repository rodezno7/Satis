<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByAndUpdatedByToKardexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kardexes', function (Blueprint $table) {
            $table->unsignedInteger('created_by')->nullable()->after('business_id');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedInteger('updated_by')->nullable()->after('created_by');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kardexes', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');

            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });
    }
}
