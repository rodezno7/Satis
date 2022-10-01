<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCashRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->decimal('total_amount_transfer', 10, 4)
                ->nullable()
                ->default(00.0000)
                ->after('total_amount_check');
            $table->decimal('total_amount_credit', 10, 4)
                ->nullable()
                ->default(00.0000)
                ->after('total_amount_transfer');
            $table->date('date')
                ->nullable()
                ->default(null)
                ->after('status');

            $table->dropColumn('opened_at');
            $table->dropColumn('closed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            //
        });
    }
}
