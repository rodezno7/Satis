<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToInflowOutflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inflow_outflows', function (Blueprint $table) {
            $table->unsignedInteger('employee_id')->nullable()->after('supplier_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedInteger('flow_reason_id')->nullable()->after('employee_id');
            $table->foreign('flow_reason_id')->references('id')->on('flow_reasons')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedInteger('created_by')->nullable()->after('amount');
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
        Schema::table('inflow_outflows', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');

            $table->dropForeign(['flow_reason_id']);
            $table->dropColumn('flow_reason_id');

            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');

            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });
    }
}
