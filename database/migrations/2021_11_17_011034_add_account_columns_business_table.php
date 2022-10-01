<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountColumnsBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->enum('debt_to_pay_type', ['supplier', 'bag_account', 'cost_center'])->after('accounting_customer_id')->nullable()->default(null);
            $table->enum('receivable_type', ['customer', 'bag_account', 'cost_center'])->after('accounting_customer_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            //
        });
    }
}
