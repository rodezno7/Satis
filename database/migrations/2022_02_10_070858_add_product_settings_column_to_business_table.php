<?php

use App\Business;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductSettingsColumnToBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->text('product_settings')->after('customer_settings')->nullable();
        });

        $default = [
            'show_stock_without_decimals' => 0,
        ];

        $business = Business::get();

        foreach ($business as $item) {
            $item->product_settings = json_encode($default);
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('product_settings');
        });
    }
}
