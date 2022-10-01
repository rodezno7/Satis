<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTaxDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_tax_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sell_line_id');
            $table->foreign('sell_line_id')
                ->references('id')
                ->on('transaction_sell_lines')
                ->onDelete('cascade');
            $table->unsignedInteger('tax_group_id');
            $table->foreign('tax_group_id')
                ->references('id')
                ->on('tax_groups');
            $table->unsignedInteger('tax_rate_id');
            $table->foreign('tax_rate_id')
                ->references('id')
                ->on('tax_rates');
            $table->enum('transaction_type', ['purchase', 'sell'])
                ->default('sell');
            $table->decimal('tax_amount', 10, 4);
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
        Schema::dropIfExists('transaction_tax_details');
    }
}
