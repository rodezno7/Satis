<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyGraduationCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('graduation_cards', function (Blueprint $table) {
            $table->renameColumn('sphere_le', 'sphere_os');
            $table->renameColumn('sphere_re', 'sphere_od');
            $table->renameColumn('cylindir_le', 'cylindir_os');
            $table->renameColumn('cylindir_re', 'cylindir_od');
            $table->renameColumn('axis_le', 'axis_os');
            $table->renameColumn('axis_re', 'axis_od');
            $table->renameColumn('base_le', 'base_os');
            $table->renameColumn('base_re', 'base_od');
            $table->renameColumn('addition_le', 'addition_os');
            $table->renameColumn('addition_re', 'addition_od');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
