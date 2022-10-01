<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePrintFormatToBankCheckbooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_checkbooks', function (Blueprint $table) {
            $table->dropColumn('print_format');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_checkbooks', function (Blueprint $table) {
            $table->string('print_format')->nullable()->after('actual_correlative');
        });
    }
}
