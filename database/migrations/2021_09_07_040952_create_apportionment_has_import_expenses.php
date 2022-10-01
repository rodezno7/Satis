<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApportionmentHasImportExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apportionment_has_import_expenses', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('apportionment_id');
            $table->foreign('apportionment_id')->references('id')->on('apportionments')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('import_expense_id');
            $table->foreign('import_expense_id')->references('id')->on('import_expenses')->onDelete('cascade')->onUpdate('cascade');

            $table->decimal('amount', 20, 4)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apportionment_has_import_expenses');
    }
}
