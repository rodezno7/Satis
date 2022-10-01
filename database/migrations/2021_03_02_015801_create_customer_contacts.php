<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->char('phone', 12)->nullable();
            $table->char('landline', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('cargo')->nullable();
            $table->unsignedBigInteger('customer_id');
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
        Schema::dropIfExists('customer_contacts');
    }
}
