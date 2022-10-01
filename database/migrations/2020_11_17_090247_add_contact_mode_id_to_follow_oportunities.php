<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactModeIdToFollowOportunities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('follow_oportunities', function (Blueprint $table) {
            $table->unsignedInteger('contact_mode_id')->nullable()->after('contact_reason_id');
            $table->foreign('contact_mode_id')->references('id')->on('crm_contact_modes')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('follow_oportunities', function (Blueprint $table) {
            //
        });
    }
}
