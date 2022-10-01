<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oportunities', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('contact_type', ['entrante', 'saliente', 'no_aplica'])->default('entrante');
            $table->integer('contact_reason_id')->unsigned()->nullable();
            $table->foreign('contact_reason_id')->references('id')->on('crm_contact_reasons')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name')->nullable();
            $table->string('company')->nullable();
            $table->string('charge')->nullable();
            $table->string('email')->nullable();
            $table->string('contacts')->nullable();
            $table->integer('known_by')->unsigned()->nullable();
            $table->foreign('known_by')->references('id')->on('crm_contact_modes')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('contact_mode_id')->unsigned()->nullable();
            $table->foreign('contact_mode_id')->references('id')->on('crm_contact_modes')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('city_id')->unsigned()->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('refered_id')->unsigned()->nullable();
            $table->foreign('refered_id')->references('id')->on('contacts')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('product_cat_id')->unsigned()->nullable();
            $table->foreign('product_cat_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->string('notes')->nullable();
            $table->string('social_user')->nullable();
            $table->boolean('product_not_found')->nullable()->default(0);
            $table->string('products_not_found_desc')->nullable();
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('contacts')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['oportunity', 'customer'])->default('oportunity');
            $table->integer('business_id')->unsigned()->nullable();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('oportunities');
    }
}
