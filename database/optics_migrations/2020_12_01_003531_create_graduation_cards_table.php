<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGraduationCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graduation_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->integer('business_id')->unsigned()->nullable();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade')->onUpdate('cascade');
            $table->float('sphere_le', 8, 2)->nullable();
            $table->float('sphere_re', 8, 2)->nullable();
            $table->float('cylindir_le', 8, 2)->nullable();
            $table->float('cylindir_re', 8, 2)->nullable();
            $table->float('axis_le', 8, 2)->nullable();
            $table->float('axis_re', 8, 2)->nullable();
            $table->float('base_le', 8, 2)->nullable();
            $table->float('base_re', 8, 2)->nullable();
            $table->float('addition_le', 8, 2)->nullable();
            $table->float('addition_re', 8, 2)->nullable();
            $table->float('di', 8, 2)->nullable();
            $table->float('ao', 8, 2)->nullable();
            $table->string('lens_color')->nullable();
            $table->float('bif', 8, 2)->nullable();
            $table->string('ring')->nullable();
            $table->float('size', 8, 2)->nullable();
            $table->string('color')->nullable();
            $table->string('invoice')->nullable();
            $table->integer('attended_by')->unsigned()->nullable();
            $table->foreign('attended_by')->references('id')->on('employees')->onDelete('set null');
            $table->integer('optometrist')->unsigned()->nullable();
            $table->foreign('optometrist')->references('id')->on('employees')->onDelete('set null');
            $table->text('observations')->nullable();
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
        Schema::dropIfExists('graduation_cards');
    }
}
