<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('days');
            $table->integer('hours');
            
            $table->decimal('montly_salary', 10, 2);
            $table->decimal('regular_salary', 10, 2);
            $table->decimal('commissions', 10, 2)->nullable();
            $table->integer('number_daytime_overtime')->nullable();
            $table->decimal('daytime_overtime', 10, 2)->nullable();
            $table->integer('number_night_overtime_hours')->nullable();
            $table->decimal('night_overtime_hours', 10, 2)->nullable();
            $table->decimal('total_hours', 10, 2)->nullable();
            $table->decimal('vacation', 10, 2)->nullable();
            $table->decimal('bonus', 10, 2)->nullable();
            $table->decimal('other_income', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();

            $table->decimal('isss', 10, 2)->nullable();
            $table->decimal('afp', 10, 2)->nullable();
            $table->decimal('rent', 10, 2)->nullable();
            
            $table->decimal('other_deductions', 10, 2)->nullable();
            $table->decimal('total_deductionsp', 10, 2)->nullable();
            $table->decimal('total_to_pay', 10, 2)->nullable();

            $table->integer('employee_id')->unsigned()->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->integer('payroll_id')->unsigned()->nullable();
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('cascade');
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
        Schema::dropIfExists('payroll_details');
    }
}