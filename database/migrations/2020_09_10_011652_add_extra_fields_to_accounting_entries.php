<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToAccountingEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->unsignedInteger('type_entrie_id')->after('accounting_period_id')->nullable();
            $table->foreign('type_entrie_id')->references('id')->on('type_entries')->onDelete('cascade');

            $table->unsignedInteger('business_location_id')->after('type_entrie_id')->nullable();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            //
        });
    }
}
