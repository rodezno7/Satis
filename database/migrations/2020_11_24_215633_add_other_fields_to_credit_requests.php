<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOtherFieldsToCreditRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_requests', function (Blueprint $table) {
            $table->string('payment_manager')->nullable()->after('email_purchasing_agent');
            $table->string('phone_payment_manager')->nullable()->after('payment_manager');
            $table->string('email_payment_manager')->nullable()->after('phone_payment_manager');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_requests', function (Blueprint $table) {
            //
        });
    }
}
