<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToClaims extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->unsignedInteger('status_claim_id')->nullable()->after('claim_type');
            $table->foreign('status_claim_id')->references('id')->on('status_claims')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('equipment_reception')->default(0)->after('invoice');
            $table->text('equipment_reception_desc')->nullable()->after('equipment_reception');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            //
        });
    }
}
