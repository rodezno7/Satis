<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDocumentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->unsignedInteger('document_class_id')->nullable();
            $table->foreign('document_class_id')->references('id')->on('document_classes');

            $table->string('document_type_number')->nullable();
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
            $table->dropForeign(['document_class_id']);
            $table->dropColumn('document_class_id');

            $table->dropColumn('document_type_number');
        });
    }
}
