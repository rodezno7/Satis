<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyAmountColumnToApportionmentHasImportExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE apportionment_has_import_expenses MODIFY COLUMN amount DECIMAL(20, 6) NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE apportionment_has_import_expenses MODIFY COLUMN amount DECIMAL(20, 4) NOT NULL DEFAULT 0");
    }
}
