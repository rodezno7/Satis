<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('import_type', ['maritime', 'aerial'])->nullable()->after('staff_note');
            $table->enum('freight', ['included', 'excluded'])->nullable()->after('import_type');
            $table->float('freight_amount', 10, 4)->nullable()->default(0.0000)->after('freight');
            $table->float('deconsolidation_amount', 10, 4)->nullable()->default(0.0000)->after('freight_amount');
            $table->float('dai_amount', 10, 4)->nullable()->default(0.0000)->after('deconsolidation_amount');
            $table->float('external_storage', 10, 4)->nullable()->default(0.0000)->after('dai_amount');
            $table->float('internal_storage', 10, 4)->nullable()->default(0.0000)->after('external_storage');
            $table->float('local_freight_amount', 10, 4)->nullable()->default(0.0000)->after('internal_storage');
            $table->float('customs_procedure_amount', 10, 4)->nullable()->default(0.0000)->after('local_freight_amount');
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
            $table->dropColumn([
                'import_type',
                'freight',
                'freight_amount',
                'deconsolidation_amount',
                'dai_amount',
                'external_storage',
                'internal_storage',
                'local_freight_amount',
                'customs_procedure_amount'
            ]);
        });
    }
}
