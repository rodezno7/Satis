<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveParentIdColumnToStatusLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status_lab_orders', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('status_lab_orders', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->nullable()->after('business_id');
            $table->foreign('parent_id')->references('id')->on('status_lab_orders')->onDelete('set null')->onUpdate('cascade');
        });
    }
}
