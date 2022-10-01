<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToClaimTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('claim_types', function (Blueprint $table) {
            $table->string('correlative')->nullable()->after('id');
            $table->integer('resolution_time')->nullable()->after('description');
            $table->boolean('required_customer')->default(0)->after('resolution_time');
            $table->boolean('required_invoice')->default(0)->after('required_customer');
            $table->boolean('required_product')->default(0)->after('required_customer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('claim_types', function (Blueprint $table) {
            //
        });
    }
}
