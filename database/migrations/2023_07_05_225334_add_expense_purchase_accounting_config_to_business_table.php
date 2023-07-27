<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpensePurchaseAccountingConfigToBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->enum('purchase_accounting_entry_mode', ['daily', 'transaction'])
                ->after('sale_accounting_entry_mode')
                ->default('transaction');
            $table->enum('expense_accounting_entry_mode', ['daily', 'transaction'])
                ->after('purchase_accounting_entry_mode')
                ->default('transaction');
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
            $table->dropColumn('purchase_accounting_entry_mode');
            $table->dropColumn('expense_accounting_entry_mode');
        });
    }
}
