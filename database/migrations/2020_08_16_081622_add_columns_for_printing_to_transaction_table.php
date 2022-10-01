<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForPrintingToTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('operating_conditions')->after('document')->nullable();
            $table->string('authorized_by')->after('operating_conditions')->nullable();
            $table->string('order_number')->after('authorized_by')->nullable();
            $table->string('declaration_number')->after('order_number')->nullable();
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
            $table->dropColumn(
                [
                    'operating_conditions',
                    'authorized_by',
                    'order_number',
                    'declaration_number'
                ]
            );
        });
    }
}
