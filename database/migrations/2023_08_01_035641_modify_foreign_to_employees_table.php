<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyForeignToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_nationality_id_foreign');
            $table->foreign('nationality_id')->references('id')->on('rrhh_datas')->onDelete('cascade');

            $table->dropForeign('employees_civil_status_id_foreign');
            $table->foreign('civil_status_id')->references('id')->on('rrhh_datas')->onDelete('cascade');

            $table->dropForeign('employees_afp_id_foreign');
            $table->foreign('afp_id')->references('id')->on('rrhh_datas')->onDelete('cascade');

            $table->dropForeign('employees_profession_id_foreign');
            $table->foreign('profession_id')->references('id')->on('rrhh_datas')->onDelete('cascade');

            $table->dropForeign('employees_type_id_foreign');
            $table->foreign('type_id')->references('id')->on('rrhh_type_wages')->onDelete('cascade');

            $table->dropForeign('employees_payment_id_foreign');
            $table->foreign('payment_id')->references('id')->on('rrhh_datas')->onDelete('cascade');

            $table->dropForeign('employees_bank_id_foreign');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_nationality_id_foreign');
            $table->dropForeign('employees_civil_status_id_foreign');
            $table->dropForeign('employees_afp_id_foreign');
            $table->dropForeign('employees_profession_id_foreign');
            $table->dropForeign('employees_type_id_foreign');
            $table->dropForeign('employees_payment_id_foreign');
            $table->dropForeign('employees_bank_id_foreign');
        });
    }
}
