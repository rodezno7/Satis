<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentCorrelativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_correlatives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serie')->nullable();
            $table->integer('initial')->default('0');
            $table->integer('actual')->default('0');
            $table->integer('final')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_correlatives');
    }
}
