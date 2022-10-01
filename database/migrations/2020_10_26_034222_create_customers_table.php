<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('business_name')->nullable();
            $table->string('email')->nullable();
            $table->string('telphone')->nullable();
            $table->string('dni')->nullable();
            $table->boolean('is_taxpayer')->nullable()->default(0);
            $table->string('reg_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('business_line')->nullable();
            $table->integer('business_type_id')->unsigned()->nullable();
            $table->foreign('business_type_id')->references('id')->on('business_types')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer_portfolio_id')->unsigned()->nullable();
            $table->foreign('customer_portfolio_id')->references('id')->on('customer_portfolios')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer_group_id')->unsigned()->nullable();
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade')->onUpdate('cascade');
            $table->string('address')->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('city_id')->unsigned()->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('zone_id')->unsigned()->nullable();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('allowed_credit')->nullable()->default(0);
            $table->decimal('opening_balance', 8, 2);
            $table->decimal('credit_limit', 8, 2);
            $table->decimal('credit_balance', 8, 2);
            $table->integer('payment_terms_id')->unsigned()->nullable();
            $table->foreign('payment_terms_id')->references('id')->on('payment_terms')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('customers');
    }
}
