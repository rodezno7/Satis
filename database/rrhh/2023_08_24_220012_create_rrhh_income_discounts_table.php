<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhIncomeDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_income_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('total_value', 10, 2);
            $table->integer('quota');
            $table->decimal('quota_value', 10, 2);
            $table->integer('quotas_applied')->nullable();
            $table->decimal('balance_to_date', 10, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('payment_period_id')->unsigned();
            $table->foreign('payment_period_id')->references('id')->on('payment_periods')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('rrhh_type_income_discount_id')->unsigned()->nullable();
            $table->foreign('rrhh_type_income_discount_id')->references('id')->on('rrhh_type_income_discounts')->onDelete('cascade');
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
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
        Schema::dropIfExists('rrhh_income_discounts');
    }
}
