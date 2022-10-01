<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tax rate
            $table->decimal('tax_group_rate', 20, 4)->nullable()->after('tax_amount');

            // Tax amount
            $table->decimal('tax_group_amount', 20, 4)->nullable()->after('tax_group_rate');

            // Amount paid
            $table->decimal('payment_balance', 20, 4)->nullable()->after('customs_procedure_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('tax_group_rate');

            $table->dropColumn('tax_group_amount');

            $table->dropColumn('payment_balance');
        });
    }
}
