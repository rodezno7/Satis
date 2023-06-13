<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('last_name');
            $table->integer('nationality_id')->unsigned()->nullable()->after('gender');
            $table->foreign('nationality_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('dni')->nullable()->after('nationality_id');
            $table->string('tax_number')->nullable()->after('dni');
            $table->integer('civil_status_id')->unsigned()->nullable()->after('tax_number');
            $table->foreign('civil_status_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('phone')->nullable()->after('civil_status_id');
            $table->string('address')->nullable()->after('email');
            $table->string('social_security_number')->nullable()->after('address');
            $table->integer('afp_id')->unsigned()->nullable()->after('social_security_number');
            $table->foreign('afp_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('afp_number')->nullable()->after('afp_id');
            $table->date('date_admission')->nullable()->after('afp_number');
            $table->decimal('salary', 10, 2)->nullable()->after('date_admission');
            $table->integer('department_id')->unsigned()->nullable()->after('salary');
            $table->foreign('department_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');
            // $table->integer('position_id')->unsigned()->nullable()->after('department_id');
            // $table->foreign('position_id')->references('id')->on('human_resources_datas');
            $table->string('photo')->nullable()->after('position_id');
            $table->boolean('status')->default(1)->after('photo');

            $table->integer('country_id')->unsigned()->nullable()->after('status');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('state_id')->unsigned()->nullable()->after('country_id');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('city_id')->unsigned()->nullable()->after('state_id');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('profession_id')->unsigned()->nullable()->after('city_id');
            $table->foreign('profession_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('type_id')->unsigned()->nullable()->after('profession_id');
            $table->foreign('type_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('payment_id')->unsigned()->nullable()->after('type_id');
            $table->foreign('payment_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('bank_id')->unsigned()->nullable()->after('payment_id');
            $table->foreign('bank_id')->references('id')->on('human_resource_banks')->onDelete('cascade')->onUpdate('cascade');

            $table->string('bank_account')->nullable()->after('bank_id');
            $table->boolean('extra_hours')->default(0)->after('bank_account');
            $table->boolean('foreign_tax')->default(0)->after('extra_hours');
            $table->integer('fee')->nullable()->after('foreign_tax');
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
            $table->dropColumn('gender');
            $table->dropForeign(['nationality_id']);
            $table->dropColumn('nationality_id');
            $table->dropColumn('dni');
            $table->dropColumn('tax_number');
            $table->dropForeign(['civil_status_id']);
            $table->dropColumn('civil_status_id');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('social_security_number');
            $table->dropForeign(['afp_id']);
            $table->dropColumn('afp_id');
            $table->dropColumn('afp_number');
            $table->dropColumn('date_admission');
            $table->dropColumn('salary', 10, 2);
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            // $table->integer('position_id')->unsigned()->nullable()->after('department_id');
            // $table->foreign('position_id')->references('id')->on('human_resources_datas');
            $table->dropColumn('photo');
            $table->dropColumn('status');

            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');

            $table->dropForeign(['state_id']);
            $table->dropColumn('state_id');

            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');

            $table->dropForeign(['profession_id']);
            $table->dropColumn('profession_id');

            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');

            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');

            $table->dropForeign(['bank_id']);
            $table->dropColumn('bank_id');

            $table->dropColumn('bank_account');
            $table->dropColumn('extra_hours');
            $table->dropColumn('foreign_tax');
            $table->dropColumn('fee');


        });
    }
}
