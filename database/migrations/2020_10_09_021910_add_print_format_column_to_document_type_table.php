<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrintFormatColumnToDocumentTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->string("print_format", 30)
                ->after("short_name")
                ->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn("print_format");
        });
    }
}
