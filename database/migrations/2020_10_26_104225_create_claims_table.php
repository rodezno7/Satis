<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->increments('id');
            $table->string('correlative');
            $table->integer('claim_type')->unsigned()->nullable();
            $table->foreign('claim_type')->references('id')->on('claim_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('description');
            $table->string('review_description')->nullable();
            $table->boolean('proceed')->nullable()->default(false);
            $table->string('resolution')->nullable();
            $table->integer('authorized_by')->unsigned()->nullable();
            $table->foreign('authorized_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->date('close_date')->nullable();
            $table->integer('register_by')->unsigned()->nullable();
            $table->foreign('register_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('claims');
    }
}
