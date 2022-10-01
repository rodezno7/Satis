<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountingPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->unsignedInteger('fiscal_year_id');
            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years');
            
            $table->integer('month');
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
        Schema::dropIfExists('accounting_periods');
    }
}
