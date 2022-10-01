<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToFollowCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('follow_customers', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->nullable()->after('id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade')->onUpdate('cascade');

            $table->enum('contact_type', ['entrante', 'saliente', 'no_aplica'])->default('entrante')->after('customer_id');

            $table->unsignedInteger('contact_reason_id')->nullable()->after('contact_type');
            $table->foreign('contact_reason_id')->references('id')->on('crm_contact_reasons')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('contact_mode_id')->nullable()->after('contact_reason_id');
            $table->foreign('contact_mode_id')->references('id')->on('crm_contact_modes')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('product_cat_id')->nullable()->after('contact_mode_id');
            $table->foreign('product_cat_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');

            $table->boolean('product_not_found')->nullable()->default(0)->after('product_cat_id');

            $table->boolean('product_not_stock')->nullable()->default(0)->after('product_not_found');

            $table->string('products_not_found_desc')->nullable()->after('product_not_stock');

            $table->string('notes')->nullable()->after('products_not_found_desc');

            $table->date('date')->nullable()->after('notes');
            
            $table->unsignedInteger('register_by')->nullable()->after('date');
            $table->foreign('register_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
