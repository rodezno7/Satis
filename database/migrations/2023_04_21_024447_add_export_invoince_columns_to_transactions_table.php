<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExportInvoinceColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('fob_amount', 10, 6)
                ->nullable()
                ->after('customs_procedure_amount')
                ->comment('FOB for export invoice');
            $table->decimal('insurance_amount', 10, 6)
                ->nullable()
                ->after('fob_amount')
                ->comment('Insurance fr export invoice');
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
            $table->dropColumn('fob_amount');
            $table->dropColumn('insurance_amount');
        });
    }
}
