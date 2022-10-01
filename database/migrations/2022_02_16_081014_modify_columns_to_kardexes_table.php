<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsToKardexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE kardexes MODIFY COLUMN unit_cost_inputs DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE kardexes MODIFY COLUMN total_cost_inputs DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE kardexes MODIFY COLUMN unit_cost_outputs DECIMAL(20, 6) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE kardexes MODIFY COLUMN total_cost_outputs DECIMAL(20, 6) NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE kardexes MODIFY COLUMN unit_cost_inputs DECIMAL(8, 2) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE kardexes MODIFY COLUMN total_cost_inputs DECIMAL(8, 2) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE kardexes MODIFY COLUMN unit_cost_outputs DECIMAL(8, 2) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE kardexes MODIFY COLUMN total_cost_outputs DECIMAL(8, 2) NOT NULL DEFAULT 0");
    }
}
