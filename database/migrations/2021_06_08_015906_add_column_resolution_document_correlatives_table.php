<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnResolutionDocumentCorrelativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_correlatives', function (Blueprint $table) {
            $table->string('resolution')->nullable()->after('serie');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_correlatives', function (Blueprint $table) {
            $table->dropColumn('resolution');
        });
    }
}
