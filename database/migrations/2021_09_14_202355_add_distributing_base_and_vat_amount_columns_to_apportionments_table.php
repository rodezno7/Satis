<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDistributingBaseAndVatAmountColumnsToApportionmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apportionments', function (Blueprint $table) {
            $table->enum('distributing_base', ['weight', 'value'])->nullable()->after('reference');
            $table->decimal('vat_amount', 20, 4)->nullable()->after('distributing_base');
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
            $table->dropColumn('distributing_base');
            $table->dropColumn('vat_amount');
        });
    }
}
