<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToFollowOportunities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('follow_oportunities', function (Blueprint $table) {
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
        Schema::table('follow_oportunities', function (Blueprint $table) {
            //
        });
    }
}
