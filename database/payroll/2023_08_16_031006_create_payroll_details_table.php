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
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('days');
            $table->boolean('proportional')->nullable();
            $table->integer('hours')->nullable();
            
            $table->decimal('montly_salary', 10, 2);
            $table->decimal('regular_salary', 10, 2)->nullable();
            $table->decimal('commissions', 10, 2)->nullable();
            $table->decimal('extra_hours', 10, 2)->nullable();
            $table->decimal('vacation', 10, 2)->nullable();
            $table->decimal('bonus', 10, 2)->nullable();
            $table->decimal('other_income', 10, 2)->nullable();
            $table->decimal('total_income', 10, 2)->nullable();

            $table->decimal('isss', 10, 2)->nullable();
            $table->decimal('afp', 10, 2)->nullable();
            $table->decimal('rent', 10, 2)->nullable();
            
            $table->decimal('other_deductions', 10, 2)->nullable();
            $table->decimal('total_deductions', 10, 2)->nullable();
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
