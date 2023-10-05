<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhSalarialConstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_salarial_constances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('template');
            $table->decimal('margin_bottom', 4, 2);
            $table->decimal('margin_left', 4, 2);
            $table->decimal('margin_right', 4, 2);
            $table->decimal('margin_top', 4, 2);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('rrhh_salarial_constances');
    }
}
