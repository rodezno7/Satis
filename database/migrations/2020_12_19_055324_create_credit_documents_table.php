<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->unsignedInteger('reason_id');
            $table->foreign('reason_id')->references('id')->on('support_documents')->onDelete('cascade');
            $table->date('register_date');
            $table->unsignedInteger('courier_id');
            $table->foreign('courier_id')->references('id')->on('employees');
            $table->unsignedInteger('reception_user_id')->nullable();
            $table->foreign('reception_user_id')->references('id')->on('users');
            $table->date('reception_date')->nullable();
            $table->unsignedInteger('document_type_received')->nullable();
            $table->foreign('document_type_received')->references('id')->on('support_documents')->onDelete('cascade');
            $table->integer('document_number')->nullable();
            $table->unsignedInteger('custodian_id')->nullable();
            $table->foreign('custodian_id')->references('id')->on('users');
            $table->date('custodian_receive_date')->nullable();
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
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
        Schema::dropIfExists('credit_documents');
    }
}
