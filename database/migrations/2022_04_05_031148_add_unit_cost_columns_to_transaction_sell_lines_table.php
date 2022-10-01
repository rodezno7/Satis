<?php

use App\TransactionSellLine;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitCostColumnsToTransactionSellLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->decimal('unit_cost_exc_tax', 20, 6)->after('service_parent_id')->default(0);
            $table->decimal('unit_cost_inc_tax', 20, 6)->after('unit_cost_exc_tax')->default(0);
        });

        $transaction_sell_lines = TransactionSellLine::all();

        if (! empty($transaction_sell_lines)) {
            foreach ($transaction_sell_lines as $tsl) {
                $tsl->unit_cost_exc_tax = $tsl->unit_price_before_discount;
                $tsl->unit_cost_inc_tax = $tsl->unit_price;
                $tsl->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->dropColumn('unit_cost_exc_tax');
            $table->dropColumn('unit_cost_inc_tax');
        });
    }
}
