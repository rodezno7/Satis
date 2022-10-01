<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsFromTransactionSellLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->dropColumn('item_tax');
            $table->dropForeign('transaction_sell_lines_tax_id_foreign');
            $table->foreign('tax_id')
                ->references('id')
                ->on('tax_groups');
            $table->decimal('unit_price_exc_tax', 20, 4)
                ->after('unit_price_inc_tax')
                ->default(null);
            $table->decimal('tax_amount', 10, 4)
                ->after('unit_price_exc_tax')
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            //
        });
    }
}
