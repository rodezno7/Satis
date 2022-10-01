<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceParentIdAndNoteColumnsToQuoteLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->unsignedInteger('service_parent_id')->nullable()->after('quote_id');
            $table->foreign('service_parent_id')->references('id')->on('variations');

            $table->text('note')->nullable()->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->dropForeign(['service_parent_id']);
            $table->dropColumn('service_parent_id');

            $table->dropColumn('note');
        });
    }
}
