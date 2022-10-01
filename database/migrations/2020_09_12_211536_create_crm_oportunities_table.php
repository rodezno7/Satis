<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmOportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_oportunities', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('contact_type', ['entrante', 'saliente'])->default('entrante');

            $table->integer('contact_reason_id')->unsigned()->nullable();
            $table->foreign('contact_reason_id')->references('id')->on('crm_contact_reasons')->onDelete('cascade')->onUpdate('cascade');

            $table->string('name')->nullable();
            $table->string('company')->nullable();
            $table->string('charge')->nullable();
            $table->string('email')->nullable();
            $table->string('contacts')->nullable();

            $table->integer('contact_mode_id')->unsigned()->nullable();
            $table->foreign('contact_mode_id')->references('id')->on('crm_contact_modes')->onDelete('cascade')->onUpdate('cascade');

            $table->string('refered_by')->nullable();

            $table->integer('product_cat_id')->unsigned()->nullable();
            $table->foreign('product_cat_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('employee_id')->unsigned()->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');

            $table->enum('status', ['Oportunidad', 'Cliente'])->default('Oportunidad');

            $table->integer('business_id')->unsigned()->nullable();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('crm_oportunities');
    }
}
