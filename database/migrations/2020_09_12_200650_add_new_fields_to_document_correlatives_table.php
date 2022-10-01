<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToDocumentCorrelativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_correlatives', function(Blueprint $table){
            $table->enum('status', ['active', 'inactive'])->default('active')->after('business_id');
            $table->integer('location_id')->unsigned()->nullable()->after('final');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('document_type_id')->nullable()->after('id');
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('cascade')->onUpdate('cascade');
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
