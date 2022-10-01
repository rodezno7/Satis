<?php

use App\Business;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDashboardSettingsColumnFromBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $business = Business::get();

        foreach ($business as $item) {
            $dashboard_settings = json_decode($item->dashboard_settings, true);

            $default = [
                'subtract_sell_return' => isset($dashboard_settings['subtract_sell_return']) ? $dashboard_settings['subtract_sell_return'] : 0,
                'box_exc_tax' => isset($dashboard_settings['box_exc_tax']) ? $dashboard_settings['box_exc_tax'] : 0,
                'sales_month' => 1,
                'sales_year' => 1,
                'peak_sales_hours_month' => 1,
                'peak_sales_hours_year' => 1,
                'purchases_month' => 0,
                'purchases_year' => 0,
                'stock_month' => 0,
                'stock_year' => 0
            ];

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
        //
    }
}
