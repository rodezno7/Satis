<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsToRrhhPositionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rrhh_position_histories', function (Blueprint $table) {
            $table->dropForeign('rrhh_position_histories_department_id_foreign');
            $table->dropColumn('department_id');

            $table->dropForeign('rrhh_position_histories_position1_id_foreign');
            $table->dropColumn('position1_id');

            $table->integer('previous_department_id')->unsigned()->nullable();
            $table->foreign('previous_department_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('previous_position1_id')->unsigned()->nullable();
            $table->foreign('previous_position1_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('new_department_id')->unsigned();
            $table->foreign('new_department_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('new_position1_id')->unsigned()->nullable();
            $table->foreign('new_position1_id')->references('id')->on('rrhh_datas')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('rrhh_position_histories', function (Blueprint $table) {
            $table->dropForeign('previous_department_id');
            $table->dropColumn('previous_department_id');
            
            $table->dropForeign('previous_position1_id');
            $table->dropColumn('previous_position1_id');

            $table->dropForeign('new_department_id');
            $table->dropColumn('new_department_id');
            
            $table->dropForeign('new_position1_id');
            $table->dropColumn('new_position1_id');
            $table->dropColumn('deleted_at');
        });
    }
}
