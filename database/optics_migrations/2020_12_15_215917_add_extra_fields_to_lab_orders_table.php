<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToLabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_orders', function (Blueprint $table) {
            $table->unsignedInteger('patient_id')->nullable()->after('customer_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade')->onUpdate('cascade');

            $table->boolean('is_reparation')->default(0)->after('color_base_od');

            $table->unsignedInteger('hoop')->nullable()->after('is_reparation');
            $table->foreign('hoop')->references('id')->on('variations')->onDelete('cascade')->onUpdate('cascade');

            $table->string('size')->nullable()->after('hoop');

            $table->unsignedInteger('color')->nullable()->after('size');
            $table->foreign('color')->references('id')->on('variation_value_templates')->onDelete('cascade')->onUpdate('cascade');

            $table->string('vision')->nullable()->after('color');

            $table->unsignedInteger('glass')->nullable()->after('vision');
            $table->foreign('glass')->references('id')->on('variations')->onDelete('cascade')->onUpdate('cascade');

            $table->enum('ar', ['green', 'blue', 'premium'])->nullable()->after('glass');

            $table->text('job_type')->nullable()->after('ar');

            $table->boolean('check_ext_lab')->default(0)->after('job_type');

            $table->unsignedInteger('external_lab_id')->nullable()->after('check_ext_lab');
            $table->foreign('external_lab_id')->references('id')->on('external_labs')->onDelete('cascade')->onUpdate('cascade');

            $table->boolean('is_urgent')->default(0)->after('external_lab_id');

            DB::statement("ALTER TABLE lab_orders CHANGE correlative no_order VARCHAR(191)");
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
