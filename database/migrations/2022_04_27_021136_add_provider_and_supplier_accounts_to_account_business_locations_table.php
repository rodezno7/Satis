<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddProviderAndSupplierAccountsToAccountBusinessLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE account_business_locations DROP FOREIGN KEY account_business_locations_account_payable_id_foreign');
        DB::statement('ALTER TABLE account_business_locations DROP COLUMN account_payable_id');
    
        Schema::table('account_business_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_account_id')
                ->nullable()
                ->default(null)
                ->after('vat_taxpayer_id');
            $table->unsignedBigInteger('provider_account_id')
                ->nullable()
                ->default(null)
                ->after('supplier_account_id');

            $table->foreign('supplier_account_id')
                ->references('id')
                ->on('catalogues');
            $table->foreign('provider_account_id')
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
        Schema::table('account_business_locations', function (Blueprint $table) {
            //
        });
    }
}
