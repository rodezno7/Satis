<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraOptionsToBusiness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            
            $table->boolean('edition_in_approved_entries')->default(1)->after('enable_validation_entries');
            $table->boolean('deletion_in_approved_entries')->default(1)->after('edition_in_approved_entries');
            $table->boolean('edition_in_number_entries')->default(1)->after('deletion_in_approved_entries');
            $table->boolean('allow_uneven_totals_entries')->default(1)->after('edition_in_number_entries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            //
        });
    }
}
