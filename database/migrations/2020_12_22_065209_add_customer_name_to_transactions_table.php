<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerNameToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger("customer_id")
                ->nullable()
                ->default(null)
                ->after("contact_id");
            $table->string("customer_name")
                ->nullable()
                ->default(null)
                ->after("customer_id");

            $table->foreign("customer_id")
                ->on("customers")
                ->references("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign("customer_id");
            $table->dropColumn("customer_id");
            $table->dropColumn("customer_name");
        });
    }
}
