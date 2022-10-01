<?php

use App\Business;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDashboardSettingsColumnToBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->text('dashboard_settings')->after('sms_settings')->nullable();
        });

        $default = [
            'subtract_sell_return' => 0,
            'box_exc_tax' => 0,
        ];

        $business = Business::get();

        foreach ($business as $item) {
            $item->dashboard_settings = json_encode($default);
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
            $table->dropColumn('dashboard_settings');
        });
    }
}
