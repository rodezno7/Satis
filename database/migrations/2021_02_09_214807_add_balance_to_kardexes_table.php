<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBalanceToKardexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kardexes', function (Blueprint $table) {
            $table->decimal('balance', 20, 4)->default(0)->after('total_cost_outputs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kardexes', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
}
