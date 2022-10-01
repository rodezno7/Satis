<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecondTimeFieldsToLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_orders', function (Blueprint $table) {
            $table->unsignedInteger('employee_id')->nullable()->after('status_lab_order_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null')->onUpdate('cascade');

            $table->text('reason')->nullable()->after('employee_id');

            $table->boolean('return_stock')->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
