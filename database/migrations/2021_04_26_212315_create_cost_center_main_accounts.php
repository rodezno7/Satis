<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostCenterMainAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cost_center_main_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("cost_center_id");
            $table->unsignedBigInteger("expense_account_id")
                ->nullable()
                ->default(null);
            $table->unsignedInteger("updated_by")
                ->nullable()
                ->default(null);
            $table->timestamps();
            
            $table->foreign("cost_center_id")
                ->on("cost_centers")
                ->references("id");
            $table->foreign("expense_account_id")
                ->on("catalogues")
                ->references("id");
            $table->foreign("updated_by")
                ->on("users")
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
        Schema::dropIfExists('cost_center_main_accounts');
    }
}
