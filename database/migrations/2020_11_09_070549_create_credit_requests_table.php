<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type_person', ['natural', 'legal']);
            $table->string('business_name')->nullable();
            $table->string('trade_name')->nullable();
            $table->string('nrc')->nullable();
            $table->string('nit_business')->nullable();
            $table->string('business_type')->nullable();
            $table->string('address')->nullable();
            $table->string('category_business')->nullable();
            $table->string('phone_business')->nullable();
            $table->string('fax_business')->nullable();
            $table->string('legal_representative')->nullable();
            $table->string('dui_legal_representative')->nullable();
            $table->string('purchasing_agent')->nullable();
            $table->string('phone_purchasing_agent')->nullable();
            $table->string('fax_purchasing_agent')->nullable();
            $table->string('email_purchasing_agent')->nullable();
            $table->decimal('amount_request_business', 10, 2)->nullable();
            $table->string('term_business')->nullable();
            $table->string('warranty_business')->nullable();
            $table->string('name_natural')->nullable();
            $table->string('dui_natural')->nullable();
            $table->integer('age')->nullable();
            $table->date('birthday')->nullable();
            $table->string('phone_natural')->nullable();
            $table->string('category_natural')->nullable();
            $table->string('nit_natural')->nullable();
            $table->string('address_natural')->nullable();
            $table->decimal('amount_request_natural', 10, 2)->nullable();
            $table->string('term_natural')->nullable();
            $table->string('warranty_natural')->nullable();
            $table->string('own_business_name')->nullable();
            $table->string('own_business_address')->nullable();
            $table->string('own_business_time')->nullable();
            $table->string('own_business_phone')->nullable();
            $table->string('own_business_fax')->nullable();
            $table->string('own_business_email')->nullable();
            $table->decimal('average_monthly_income', 10, 2)->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_dui')->nullable();
            $table->string('spouse_work_address')->nullable();
            $table->string('spouse_phone')->nullable();
            $table->date('spouse_income_date')->nullable();
            $table->string('spouse_position')->nullable();
            $table->decimal('spouse_salary', 10, 2)->nullable();
            $table->boolean('order_purchase')->nullable();
            $table->boolean('order_via_fax')->nullable();
            $table->date('date_request')->nullable();
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
        Schema::dropIfExists('credit_requests');
    }
}
