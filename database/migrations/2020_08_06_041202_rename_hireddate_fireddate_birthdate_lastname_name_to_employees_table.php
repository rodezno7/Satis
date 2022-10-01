<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameHireddateFireddateBirthdateLastnameNameToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function(Blueprint $table){
            $table->renameColumn('name',  'first_name');
            $table->renameColumn('lastname',  'last_name');
            $table->renameColumn('hireddate',  'hired_date');
            $table->renameColumn('fireddate',  'fired_date');
            $table->renameColumn('birthdate',  'birth_date');
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
