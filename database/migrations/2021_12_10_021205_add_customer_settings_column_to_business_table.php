<?php

use App\Business;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerSettingsColumnToBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->text('customer_settings')->after('dashboard_settings')->nullable();
        });

        $default = [
            'nit_in_general_info' => 0,
        ];

        $business = Business::get();

        foreach ($business as $item) {
            $item->customer_settings = json_encode($default);
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
            $table->dropColumn('customer_settings');
        });
    }
}
