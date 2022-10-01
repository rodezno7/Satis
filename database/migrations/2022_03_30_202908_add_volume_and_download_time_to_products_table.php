<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVolumeAndDownloadTimeToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('volume', 12, 2)
                ->nullable()
                ->default(null)
                ->after('weight');
            
            $table->time('download_time')
                ->nullable()
                ->default(null)
                ->after('volume');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('volume');
            $table->dropColumn('download_time');
        });
    }
}
