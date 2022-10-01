<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeftAndRightGlassToLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_orders', function (Blueprint $table) {
            $table->unsignedInteger('glass_os')->nullable()->after('glass');
            $table->foreign('glass_os')->references('id')->on('variations')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('glass_od')->nullable()->after('glass_os');
            $table->foreign('glass_od')->references('id')->on('variations')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('graduation_cards', function (Blueprint $table) {
            $table->boolean('balance_os')->default(0)->after('is_prescription');
            $table->boolean('balance_od')->default(0)->after('is_prescription');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
