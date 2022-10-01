<?php

use App\PhysicalInventory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndDateColumnToPhysicalInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('physical_inventories', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('start_date');
        });

        $physical_inventories = PhysicalInventory::all();

        foreach ($physical_inventories as $physical_inventory) {
            $end_date = \Carbon::createFromFormat('Y-m-d H:i:s', $physical_inventory->updated_at);
            $physical_inventory->end_date = $end_date->format('Y-m-d');
            $physical_inventory->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('physical_inventories', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
}
