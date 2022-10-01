<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsReturnDocumentToDocumentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->boolean('is_return_document')
                ->default(false)
                ->after('is_document_sale');
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
            $table->dropColumn('is_return_document');
        });
    }
}
