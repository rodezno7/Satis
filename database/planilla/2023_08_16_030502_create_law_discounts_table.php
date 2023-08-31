<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLawDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('law_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('from', 10, 2);
            $table->decimal('until', 12, 2);
            $table->decimal('base', 10, 2);
            $table->decimal('fixed_fee', 10, 2);
            $table->decimal('employee_percentage', 10, 2);
            $table->decimal('employer_value', 10, 2);
            $table->boolean('status')->default(1);
            
            $table->integer('institution_law_id')->unsigned()->nullable();
            $table->foreign('institution_law_id')->references('id')->on('institution_laws')->onDelete('cascade')->onUpdate('cascade');
            
            $table->integer('calculation_type_id')->unsigned()->nullable();
            $table->foreign('calculation_type_id')->references('id')->on('calculation_types')->onDelete('cascade')->onUpdate('cascade');
            
            $table->integer('business_id')->unsigned()->nullable();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
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
        Schema::dropIfExists('law_discounts');
    }
}
