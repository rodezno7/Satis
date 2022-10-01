<?php

use App\DocumentClass;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->timestamps();
        });

        DocumentClass::create([
            'code' => 1,
            'name' => 'IMPRESO POR IMPRENTA O TIQUETES'
        ]);

        DocumentClass::create([
            'code' => 2,
            'name' => 'FORMULARIO ÚNICO'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_classes');
    }
}
