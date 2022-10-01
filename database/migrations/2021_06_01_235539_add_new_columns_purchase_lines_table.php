<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsPurchaseLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->float('weight_kg', 10, 4)->nullable()->after('tax_amount');
            $table->float('transfer_fee', 10, 4)->nullable()->after('weight_kg');
            $table->enum('freight', ['yes', 'no'])->nullable()->after('transfer_fee');
            $table->float('freight_amount', 20, 4)->nullable()->default(0.0000)->after('freight');
            $table->float('deconsolidation_amount', 20, 4)->nullable()->default(0.0000)->after('freight_amount');
            $table->float('dai_amount', 20, 4)->nullable()->default(0.0000)->after('deconsolidation_amount');
            $table->float('external_storage', 20, 4)->nullable()->default(0.0000)->after('dai_amount');
            $table->float('local_freight_amount', 20, 4)->nullable()->default(0.0000)->after('external_storage');
            $table->float('customs_procedure_amount', 20, 4)->nullable()->default(0.0000)->after('local_freight_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->dropColumn([
                'weight_kg',
                'transfer_fee',
                'freight',
                'freight_amount',
                'deconsolidation_amount',
                'dai_amount',
                'external_storage',
                'local_freight_amount',
                'customs_procedure_amount'
            ]);
        });
    }
}
