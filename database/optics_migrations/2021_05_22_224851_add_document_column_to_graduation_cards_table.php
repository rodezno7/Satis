<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocumentColumnToGraduationCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('graduation_cards', function (Blueprint $table) {
            $table->string('document')->nullable()->after('observations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('graduation_cards', function (Blueprint $table) {
            $table->dropColumn('document');
        });
    }
}
