<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsFromGraduationCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('graduation_cards', function (Blueprint $table) {
            $table->dropColumn('lens_color');
            $table->dropColumn('bif');
            $table->dropColumn('ring');
            $table->dropColumn('size');
            $table->dropColumn('color');
            DB::statement("ALTER TABLE graduation_cards MODIFY COLUMN di VARCHAR(191)");
            $table->boolean('is_prescription')->after('business_id')->default(0);
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
