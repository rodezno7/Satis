<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHumanResourceEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('human_resource_employees', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('last_name');            
            $table->string('gender');

            $table->integer('nationality_id')->unsigned();
            $table->foreign('nationality_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');
            
            $table->date('birthdate');
            $table->string('dni');
            $table->string('tax_number');
            $table->string('social_security_number')->nullable();

            $table->integer('afp_id')->unsigned()->nullable();
            $table->foreign('afp_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->string('afp_number')->nullable();

            $table->integer('civil_status_id')->unsigned();
            $table->foreign('civil_status_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('address');

            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('city_id')->unsigned()->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');

            $table->string('photo')->nullable();

            $table->integer('profession_id')->unsigned()->nullable();
            $table->foreign('profession_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->date('date_admission')->nullable();

            $table->integer('department_id')->unsigned()->nullable();
            $table->foreign('department_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('position_id')->unsigned()->nullable();
            $table->foreign('position_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->decimal('salary', 10, 2)->nullable();

            $table->integer('type_id')->unsigned()->nullable();
            $table->foreign('type_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('payment_id')->unsigned()->nullable();
            $table->foreign('payment_id')->references('id')->on('human_resources_datas')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('bank_id')->unsigned()->nullable();
            $table->foreign('bank_id')->references('id')->on('human_resource_banks')->onDelete('cascade')->onUpdate('cascade');

            $table->string('bank_account')->nullable();
            $table->boolean('extra_hours')->default(0);
            $table->boolean('foreign_tax')->default(0);
            $table->integer('fee')->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('human_resource_employees');
    }
}
