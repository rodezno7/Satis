<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGraduationCardHasDiagnosticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graduation_card_has_diagnostics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('graduation_card_id');
            $table->foreign('graduation_card_id')->references('id')->on('graduation_cards')->onDelete('cascade');
            $table->unsignedInteger('diagnostic_id');
            $table->foreign('diagnostic_id')->references('id')->on('diagnostics')->onDelete('cascade');
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
        Schema::dropIfExists('graduation_card_has_diagnostics');
    }
}
