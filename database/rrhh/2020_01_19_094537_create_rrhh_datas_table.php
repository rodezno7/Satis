<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_datas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code')->nullable();
            $table->string('short_name')->nullable();
            $table->string('value');
            $table->boolean('status');
            $table->boolean('date_required')->nullable();

            $table->integer('rrhh_header_id')->unsigned();
            $table->foreign('rrhh_header_id')->references('id')->on('rrhh_headers')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('business_id')->unsigned()->nullable();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rrhh_datas');
    }
}
