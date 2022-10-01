<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApportionmentDateColumnToApportionmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apportionments', function (Blueprint $table) {
            $table->date('apportionment_date')->nullable()->after('business_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apportionments', function (Blueprint $table) {
            $table->dropColumn('apportionment_date');
        });
    }
}
