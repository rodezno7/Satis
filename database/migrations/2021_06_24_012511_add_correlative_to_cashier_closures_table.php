<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCorrelativeToCashierClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashier_closures', function (Blueprint $table) {
            $table->string('correlative')
                ->after('total_return_amount')
                ->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashier_closures', function (Blueprint $table) {
            $table->dropColumn('correlative');
        });
    }
}
