<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyQuotesCustomersRelationshipOnQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign("quotes_customer_id_foreign");

            $table->foreign("customer_id")
                ->references("id")
                ->on("customers");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign("customer_id");

            $table->foreign("customer_id")
                ->references("id")
                ->on("contacts");
        });
    }
}
