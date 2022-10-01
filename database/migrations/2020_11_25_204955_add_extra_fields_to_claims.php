<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToClaims extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->date('claim_date')->nullable()->after('description');
            $table->date('suggested_closing_date')->nullable()->after('claim_date');
            
            $table->unsignedInteger('customer_id')->nullable()->after('register_by');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('variation_id')->nullable()->after('customer_id');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade')->onUpdate('cascade');

            $table->decimal('invoice', 10, 2)->nullable()->after('variation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            //
        });
    }
}
