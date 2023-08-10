<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsToRrhhContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rrhh_contracts', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('current_date_letters');
            $table->string('employee_gender')->nullable()->after('employee_age');
            $table->string('employee_address')->nullable()->after('employee_gender');
            $table->string('employee_nationality')->nullable()->after('employee_address');
            $table->string('employee_civil_status')->nullable()->after('employee_nationality');
            $table->string('employee_profession')->nullable()->after('employee_civil_status');
            $table->string('employee_dni_expedition_date')->nullable()->after('employee_dni');
            $table->string('employee_dni_expedition_place')->nullable()->after('employee_dni_expedition_date');
            $table->string('employee_tax_number_approved')->nullable()->after('employee_tax_number');
            $table->string('contract_status')->default('Vigente')->after('contract_end_date');
            $table->string('business_legal_representative')->nullable()->after('business_name');
            $table->string('business_address')->nullable()->after('business_state');
            $table->string('line_of_business')->nullable()->after('business_address');
            $table->date('current_date')->nullable()->after('template');
            $table->string('file')->nullable()->after('current_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rrhh_contracts', function (Blueprint $table) {
            $table->dropColumn('employee_gender');
            $table->dropColumn('employee_address');
            $table->dropColumn('employee_nationality');
            $table->dropColumn('employee_civil_status');
            $table->dropColumn('employee_profession');
            $table->dropColumn('employee_dni_expedition_date');
            $table->dropColumn('employee_dni_expedition_place');
            $table->dropColumn('employee_tax_number_approved');
            $table->dropColumn('contract_status');
            $table->dropColumn('business_legal_representative');
            $table->dropColumn('business_address');
            $table->dropColumn('line_of_business');
            $table->dropColumn('current_date');
            $table->dropColumn('file');
        });
    }
}
