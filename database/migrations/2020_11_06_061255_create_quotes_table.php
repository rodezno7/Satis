<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->default(null)->nullable();
            $table->foreign('customer_id')->references('id')->on('contacts');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('document_type_id');
            $table->foreign('document_type_id')->references('id')->on('document_types');
            $table->dateTime('quote_date');
            $table->string('quote_ref_no');
            $table->string('customer_name');
            $table->string('contact_name')->default(null)->nullable();
            $table->string('email')->default(null)->nullable();
            $table->string('mobile')->default(null)->nullable();
            $table->string('address')->default(null)->nullable();
            $table->enum('payment_condition', ['cash', 'credit'])->default('cash')->nullable();
            $table->boolean('tax_detail')->default(false);
            $table->string('validity');
            $table->string('delivery_time')->default(null)->nullable();
            $table->text('note')->default(null)->nullable();
            $table->text('legend')->default(null)->nullable();
            $table->text('terms_conditions')->default(null)->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_amount', 10, 4)->default(0.0000)->nullable();
            $table->decimal('total_before_tax', 10, 4);
            $table->decimal('tax_amount', 10, 4)->default(0.0000)->nullable();
            $table->decimal('total_final', 10, 4);
            $table->unsignedInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->softDeletes();
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
        Schema::dropIfExists('quotes');
    }
}
