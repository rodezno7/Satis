<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowOportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_oportunities', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('oportunity_id')->nullable();
            $table->foreign('oportunity_id')->references('id')->on('oportunities')->onDelete('cascade')->onUpdate('cascade');

            $table->enum('contact_type', ['entrante', 'saliente', 'no_aplica'])->default('entrante');

            $table->unsignedInteger('contact_reason_id')->nullable();
            $table->foreign('contact_reason_id')->references('id')->on('crm_contact_reasons')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('product_cat_id')->nullable();
            $table->foreign('product_cat_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');

            $table->boolean('product_not_found')->nullable()->default(0);
            $table->boolean('product_not_stock')->nullable()->default(0);
            $table->string('products_not_found_desc')->nullable();

            $table->string('notes')->nullable();

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
        Schema::dropIfExists('follow_oportunities');
    }
}
