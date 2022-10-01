<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactModeIdToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedInteger('contact_mode_id')->nullable()->after('customer_group_id');
            $table->foreign('contact_mode_id')->references('id')->on('crm_contact_modes')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('first_purchase_location')->nullable()->after('contact_mode_id');
            $table->foreign('first_purchase_location')->references('id')->on('business_locations')->onDelete('cascade')->onUpdate('cascade');
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
