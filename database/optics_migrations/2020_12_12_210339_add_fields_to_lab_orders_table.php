<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_orders', function (Blueprint $table) {
            $table->string('correlative')->nullable()->after('id');
            $table->unsignedInteger('customer_id')->nullable()->after('correlative');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('graduation_card_id')->nullable()->after('customer_id');
            $table->foreign('graduation_card_id')->references('id')->on('graduation_cards')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('status_lab_order_id')->nullable()->after('graduation_card_id');
            $table->foreign('status_lab_order_id')->references('id')->on('status_lab_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->string('color_base_os')->nullable()->after('status_lab_order_id');
            $table->string('color_base_od')->nullable()->after('color_base_os');
            $table->boolean('is_own_hoop')->default(0)->after('color_base_od');
            $table->enum('hoop_type', ['full', 'semi_air', 'air'])->default('full')->after('is_own_hoop');
            $table->dateTime('delivery')->nullable()->after('hoop_type');
            $table->unsignedInteger('business_id')->nullable()->after('delivery');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade')->onUpdate('cascade');
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
