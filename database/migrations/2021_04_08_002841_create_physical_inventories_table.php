<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhysicalInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physical_inventories', function (Blueprint $table) {
            $table->increments('id');

            $table->string('code');

            $table->string('name');

            $table->date('start_date');

            $table->unsignedInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->onUpdate('cascade');

            $table->enum('status', ['new', 'process', 'review', 'authorized', 'finalized']);

            $table->unsignedInteger('responsible');
            $table->foreign('responsible')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedInteger('processed_by')->nullable();
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedInteger('reviewed_by')->nullable();
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedInteger('authorized_by')->nullable();
            $table->foreign('authorized_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedInteger('finished_by')->nullable();
            $table->foreign('finished_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('physical_inventories');
    }
}
