<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhPersonnelActionAuthorizerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_personnel_action_authorizer', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('authorized')->default(false);
            $table->integer('personnel_action_id')->unsigned();
            $table->foreign('personnel_action_id')->references('id')->on('personnel_actions')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @r
     */
    public function down()
    {
        Schema::dropIfExists('rrhh_personnel_action_authorizer');
    }
}
