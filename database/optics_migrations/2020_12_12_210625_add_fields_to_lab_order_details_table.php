<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToLabOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_order_details', function (Blueprint $table) {
            $table->unsignedInteger('lab_order_id')->nullable()->after('id');
            $table->foreign('lab_order_id')->references('id')->on('lab_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('material_id')->nullable()->after('lab_order_id'); # Product
            $table->foreign('material_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity')->default(1)->after('material_id');
            $table->softDeletes();
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
