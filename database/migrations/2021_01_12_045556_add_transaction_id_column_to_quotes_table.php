<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionIdColumnToQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->integer("transaction_id")
                ->nullable()
                ->default(null)
                ->after("document_type_id")
                ->unsigned();

            $table->foreign("transaction_id")
                ->on("transactions")
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
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign("transaction_id");
            $table->dropColumn("transaction_id");
        });
    }
}
