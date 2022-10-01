<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForApportionmentInTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('total_before_expense', 20, 6)->default(0)->after('distributing_base');
            $table->decimal('purchase_expense_amount', 20, 6)->default(0)->after('total_before_expense');
            $table->decimal('total_after_expense', 20, 6)->default(0)->after('purchase_expense_amount');
            $table->decimal('apportionment_expense_amount', 20, 6)->default(0)->after('total_after_expense');
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
            $table->dropColumn('total_before_expense');
            $table->dropColumn('purchase_expense_amount');
            $table->dropColumn('total_after_expense');
            $table->dropColumn('apportionment_expense_amount');
        });
    }
}
