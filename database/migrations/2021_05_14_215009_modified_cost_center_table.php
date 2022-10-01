<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifiedCostCenterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cost_centers', function (Blueprint $table) {
            $table->unsignedInteger("created_by")
                ->after("description")
                ->nullable()
                ->default(null);
            $table->unsignedInteger("updated_by")
                ->after("created_by")
                ->nullable()
                ->default(null);
            $table->softDeletes();

            $table->foreign("created_by")
                ->on("users")
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
        Schema::table('cost_centers', function (Blueprint $table) {
            //
        });
    }
}
