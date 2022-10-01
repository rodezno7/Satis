<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixedAssetTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_asset_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description')->nullable();
            $table->decimal('percentage', 4, 2);
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('accounting_account_id');
            $table->timestamps();

            $table->foreign('business_id')
                ->references('id')
                ->on('business');
            $table->foreign('accounting_account_id')
                ->references('id')
                ->on('catalogues');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixed_asset_types');
    }
}
