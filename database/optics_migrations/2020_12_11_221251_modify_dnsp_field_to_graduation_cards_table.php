<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDnspFieldToGraduationCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('graduation_cards', function (Blueprint $table) {
            DB::statement("ALTER TABLE graduation_cards MODIFY COLUMN dnsp_os VARCHAR(191)");
            DB::statement("ALTER TABLE graduation_cards MODIFY COLUMN dnsp_od VARCHAR(191)");
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
