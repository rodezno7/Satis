<?php

use App\Barcode;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameWithLogoColumnToBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barcodes', function (Blueprint $table) {
            $table->integer('name_with_logo')->nullable()->after('name');
        });

        $barcodes = Barcode::all();

        foreach ($barcodes as $barcode) {
            switch ($barcode->id) {
                case 1:
                    $barcode->name_with_logo = __('barcode.barcode_name_with_logo_' . $barcode->id);
                    break;
                
                case 2:
                    $barcode->name_with_logo = __('barcode.barcode_name_with_logo_' . $barcode->id);
                    break;

                case 3:
                    $barcode->name_with_logo = __('barcode.barcode_name_with_logo_' . $barcode->id);
                    break;

                case 4:
                    $barcode->name_with_logo = __('barcode.barcode_name_with_logo_' . $barcode->id);
                    break;

                case 5:
                    $barcode->name_with_logo = __('barcode.barcode_name_with_logo_' . $barcode->id);
                    break;

                case 6:
                    $barcode->name_with_logo = __('barcode.barcode_name_' . $barcode->id);
                    break;
            }

            $barcode->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barcodes', function (Blueprint $table) {
            $table->dropColumn('name_with_logo');
        });
    }
}
