<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpenseCategoryIdColumnToInflowOutflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inflow_outflows', function (Blueprint $table) {
            $table->unsignedInteger('expense_category_id')->nullable()->after('flow_reason_id');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('set null')->onUpdate('cascade');

            $table->text('description')->nullable()->after('amount');
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
            $table->dropForeign(['expense_category_id']);
            $table->dropColumn('expense_category_id');

            $table->dropColumn('description');
        });
    }
}
