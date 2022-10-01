<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostCenterCategorieHasAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cost_center_categorie_has_accounts', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('categorie_id');
            $table->foreign('categorie_id')->references('id')->on('cost_center_categories')->onDelete('cascade');

            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('catalogues')->onDelete('cascade');

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
        Schema::dropIfExists('cost_center_categorie_has_accounts');
    }
}
