<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateConstraintToTypeBankTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('type_bank_transactions', function (Blueprint $table) {
            $table->boolean('enable_date_constraint')->default(0)->after('enable_headline');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('type_bank_transactions', function (Blueprint $table) {
            //
        });
    }
}
