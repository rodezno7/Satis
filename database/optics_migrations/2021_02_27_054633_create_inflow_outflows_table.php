<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInflowOutflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inflow_outflows', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('type', ['input', 'output']);

            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('contacts')->onDelete('set null')->onUpdate('cascade');

            $table->integer('document_type_id')->nullable();
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('set null')->onUpdate('cascade');

            $table->integer('document_no')->nullable();

            $table->string('reason')->nullable();

            $table->decimal('amount', 20, 2)->default(0);

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
        Schema::dropIfExists('inflow_outflows');
    }
}
