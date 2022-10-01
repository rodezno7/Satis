<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHumanResourcesDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('human_resources_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->string('short_name')->nullable();
            $table->string('value');
            $table->boolean('status');

            $table->integer('human_resources_header_id')->unsigned();
            $table->foreign('human_resources_header_id')->references('id')->on('human_resources_headers')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('human_resources_datas');
    }
}
