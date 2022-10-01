<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashierClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashier_closures', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cashier_id');
            $table->decimal('total_system_amount', 10, 4)->default(0.0000);
            $table->decimal('total_physical_amount', 10, 4)->default(0.0000);
            $table->decimal('differences', 8, 4)->default(0.0000);
            $table->decimal('initial_cash_amount', 10, 4)->default(0.0000);
            $table->decimal('total_cash_amount', 10, 4)->default(0.0000);
            $table->decimal('total_card_amount', 10, 4)->default(0.0000);
            $table->decimal('total_credit_amount', 10, 4)->default(0.0000);
            $table->decimal('total_check_amount', 10, 4)->default(0.0000);
            $table->decimal('total_bank_transfer_amount', 10, 4)->default(0.0000);
            $table->decimal('total_return_amount', 10, 4)->default(0.0000);
            $table->text('closing_note')->nullable();
            $table->unsignedInteger('opened_by');
            $table->unsignedInteger('closed_by')->nullable();
            $table->dateTime('open_date')->nullable();
            $table->dateTime('close_date')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('cashier_id')
                ->references('id')
                ->on('cashiers');
            $table->foreign('opened_by')
                ->references('id')
                ->on('users');
            $table->foreign('closed_by')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashier_closures');
    }
}
