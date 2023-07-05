<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhActionTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_action_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rrhh_type_personnel_action_id')->unsigned()->nullable();
            $table->foreign('rrhh_type_personnel_action_id', 'rrhh_tpa_id_foreign')->references('id')->on('rrhh_type_personnel_actions')->onDelete('cascade');
            $table->integer('rrhh_required_action_id')->unsigned()->nullable();
            $table->foreign('rrhh_required_action_id')->references('id')->on('rrhh_required_actions')->onDelete('cascade');
            $table->integer('rrhh_class_personnel_action_id')->unsigned()->nullable();
            $table->foreign('rrhh_class_personnel_action_id', 'rrhh_cpa_at_id_foreign')->references('id')->on('rrhh_class_personnel_actions')->onDelete('cascade');
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
        Schema::dropIfExists('rrhh_action_type');
    }
}
