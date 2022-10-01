<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsToPhysicalInventoryLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE physical_inventory_lines MODIFY COLUMN price DECIMAL(20, 6) NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE physical_inventory_lines MODIFY COLUMN price DECIMAL(20, 4) NOT NULL DEFAULT 0");
    }
}
