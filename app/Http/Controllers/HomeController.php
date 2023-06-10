<?php

namespace App\Http\Controllers;

use App\Business;
use Illuminate\Http\Request;

use App\Product;
use App\Transaction;
use App\VariationLocationDetails;
use App\Currency;
use App\PurchaseLine;
use App\BusinessLocation;
use App\Image;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;

use Datatables;
use Charts;
use DB;

class HomeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $transactionUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil
    ) {
    
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!auth()->user()->can('dashboard.data')) {
            return view('home.index');
        }

        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();

        $business = Business::find($business_id);

        // Dashboard settings
        if (empty($business->dashboard_settings)) {
            $dashboard_settings = $this->businessUtil->defaultDashboardSettings();
        } else {
            $dashboard_settings = json_decode($business->dashboard_settings, true);
        }
        
        $labels = [];
        // Sells last 30 days
        if (isset($dashboard_settings['sales_month']) && $dashboard_settings['sales_month'] == 1) {
            $sells_last_30_days = $this->transactionUtil->getSellsLast30Days($business_id);
            $sell_values = [];
        }

        // Purchases last 30 days
        if (isset($dashboard_settings['purchases_month']) && $dashboard_settings['purchases_month'] == 1) {
            $purchases_last_30_days = $this->transactionUtil->getPurchasesLast30Days($business_id);
            $purchase_values = [];
        }

        // Stock last 30 days
        if (isset($dashboard_settings['stock_month']) && $dashboard_settings['stock_month'] == 1) {
            $stock_last_30_days = $this->transactionUtil->getStockLast30Days($business_id);
            $stock_values = [];
        }

        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');

            $labels[] = date('j M Y', strtotime($date));

            if (isset($dashboard_settings['sales_month']) && $dashboard_settings['sales_month'] == 1) {
                if (!empty($sells_last_30_days[$date])) {
                    $sell_values[] = $sells_last_30_days[$date];
                } else {
                    $sell_values[] = 0;
                }
            }

            if (isset($dashboard_settings['purchases_month']) && $dashboard_settings['purchases_month'] == 1) {
                if (! empty($purchases_last_30_days[$date])) {
                    $purchase_values[] = $purchases_last_30_days[$date];
                } else {
                    $purchase_values[] = 0;
                }
            }

            if (isset($dashboard_settings['stock_month']) && $dashboard_settings['stock_month'] == 1) {
                if (!empty($stock_last_30_days[$date])) {
                    $stock_values[] = $stock_last_30_days[$date];
                } else {
                    $stock_values[] = 0;
                }
            }
        }

        // Chart sales last 30 days
        $sells_chart_1 = null;
        if (isset($dashboard_settings['sales_month']) && $dashboard_settings['sales_month'] == 1) {
            $sells_chart_1 = Charts::create('bar', 'highcharts')
                ->title(' ')
                ->template("material")
                ->values($sell_values)
                ->labels($labels)
                ->elementLabel(__('home.total_sells', ['currency' => $currency->code]));
        }

        // Chart purchases last 30 days
        $purchases_chart_1 = null;
        if (isset($dashboard_settings['purchases_month']) && $dashboard_settings['purchases_month'] == 1) {
            $purchases_chart_1 = Charts::create('bar', 'highcharts')
                ->title(' ')
                ->template('material')
                ->values($purchase_values)
                ->labels($labels)
                ->elementLabel(__('home.total_purchases', ['currency' => $currency->code]));
        }

        // Chart for stock last 30 days
        $stocks_chart_1 = null;
        if (isset($dashboard_settings['stock_month']) && $dashboard_settings['stock_month'] == 1) {
            $stocks_chart_1 = Charts::create('bar', 'highcharts')
                ->title(' ')
                ->template('material')
                ->values($stock_values)
                ->labels($labels)
                ->elementLabel(__('home.total_stocks', ['currency' => $currency->code]));
        }

        $labels = [];

        // Chart for sells this financial year
        if (isset($dashboard_settings['sales_year']) && $dashboard_settings['sales_year'] == 1) {
            $sells_this_fy = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end']);
            $sell_values = [];
        }

        // Purchases current financial year
        if (isset($dashboard_settings['purchases_year']) && $dashboard_settings['purchases_year'] == 1) {
            $purchases_this_fy = $this->transactionUtil->getPurchasesCurrentFy($business_id, $fy['start'], $fy['end']);
            $purchase_values = [];
        }

        // Stock current financial year
        if (isset($dashboard_settings['stock_year']) && $dashboard_settings['stock_year'] == 1) {
            $stocks_this_fy = $this->transactionUtil->getStockCurrentFy($business_id, $fy['start']);
            $stock_values = [];
        }

        $months = [];
        $date = strtotime($fy['start']);
        $last   = date('m-Y', strtotime($fy['end']));

        do {
            $month_year = date('m-Y', $date);

            $month_number = date('m', $date);

            $labels[] = \Carbon::createFromFormat('m-Y', $month_year)
                            ->format('M-Y');
            $date = strtotime('+1 month', $date);

            if (isset($dashboard_settings['sales_year']) && $dashboard_settings['sales_year'] == 1) {
                if (!empty($sells_this_fy[$month_year])) {
                    $sell_values[] = $sells_this_fy[$month_year];
                } else {
                    $sell_values[] = 0;
                }
            }

            if (isset($dashboard_settings['purchases_year']) && $dashboard_settings['purchases_year'] == 1) {
                if (!empty($purchases_this_fy[$month_year])) {
                    $purchase_values[] = $purchases_this_fy[$month_year];
                } else {
                    $purchase_values[] = 0;
                }
            }

            if (isset($dashboard_settings['stock_year']) && $dashboard_settings['stock_year'] == 1) {
                if (!empty($stocks_this_fy[$month_year])) {
                    $stock_values[] = $stocks_this_fy[$month_year];
                } else {
                    $stock_values[] = 0;
                }
            }
        } while ($month_year != $last);

        // Chart for sells this financial year
        $sells_chart_2 = null;
        if (isset($dashboard_settings['sales_year']) && $dashboard_settings['sales_year'] == 1) {
            $sells_chart_2 = Charts::create('bar', 'highcharts')
                ->title(__(' '))
                ->template("material")
                ->values($sell_values)
                ->labels($labels)
                ->elementLabel(__(
                    'home.total_sells',
                    ['currency' => $currency->code]
                ));
        }

        // Chart purchases current financial year
        $purchases_chart_2 = null;
        if (isset($dashboard_settings['purchases_year']) && $dashboard_settings['purchases_year'] == 1) {
            $purchases_chart_2 = Charts::create('bar', 'highcharts')
                ->title(__(' '))
                ->template('material')
                ->values($purchase_values)
                ->labels($labels)
                ->elementLabel(__(
                    'home.total_purchases',
                    ['currency' => $currency->code]
                ));
        }

        // Chart stock current financial year
        $stocks_chart_2 = null;
        if (isset($dashboard_settings['stock_year']) && $dashboard_settings['stock_year'] == 1) {
            $stocks_chart_2 = Charts::create('bar', 'highcharts')
                ->title(__(' '))
                ->template('material')
                ->values($stock_values)
                ->labels($labels)
                ->elementLabel(__(
                    'home.total_stocks',
                    ['currency' => $currency->code]
                ));
        }

        $months = array(
            '01' => __('accounting.january'),
            '02' => __('accounting.february'),
            '03' => __('accounting.march'),
            '04' => __('accounting.april'),
            '05' => __('accounting.may'),
            '06' => __('accounting.june'),
            '07' => __('accounting.july'),
            '08' => __('accounting.august'),
            '09' => __('accounting.september'),
            '10' => __('accounting.october'),
            '11' => __('accounting.november'),
            '12' => __('accounting.december')
        );
        
        $business_locations = BusinessLocation::where('business_id', $business_id)->get();

        // Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);

        $first_location = $locations->first();

        $default_location = null;

        # Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
                $first_location = $id;
            }

        # Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__("kardex.all_2"), 'all');
            $first_location = 'all';
        
        } else {
            foreach ($locations as $id => $name) {
                $first_location = $id;
            }
        }

        $images = Image::where('business_id', $business_id)->where('is_active', true)->get();

        return view('home.index', compact(
            'date_filters',
            'sells_chart_1',
            'sells_chart_2',
            'purchases_chart_1',
            'purchases_chart_2',
            'stocks_chart_1',
            'stocks_chart_2',
            'months',
            'business_locations',
            'locations',
            'default_location',
            'first_location',
            'business',
            'dashboard_settings',
            'images'
        ));
    }

    /**
     * Get totals for dashboard cards according to selected month.
     * 
     * @return array
     */
    public function chooseMonth()
    {
        try {
            $year = request()->get('year_modal');
            $month = request()->get('month_modal');

            $start = date($year . '-' . $month . '-01');
            $last_day = date('t', strtotime($start));
            $end = date($year . '-' . $month . '-' . $last_day);

            $output = [
                'success' => true,
                'start' => $start,
                'end' => $end
            ];

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Retrieves purchase details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseDetails()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end, $location_id);

            return $purchase_details;
        }
    }

    /**
     * Retrieves sell details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSellDetails()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');

            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start, $end, $location_id);
            
            return $sell_details;
        }
    }

    /**
     * Retrieves sell products whose available quntity is less than alert quntity.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductStockAlert()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $location_id = request()->location_id;

            $query = VariationLocationDetails::join(
                'product_variations as pv',
                'variation_location_details.product_variation_id',
                '=',
                'pv.id'
            )
                    ->join(
                        'variations as v',
                        'variation_location_details.variation_id',
                        '=',
                        'v.id'
                    )
                    ->join(
                        'products as p',
                        'variation_location_details.product_id',
                        '=',
                        'p.id'
                    )
                    ->leftjoin(
                        'business_locations as l',
                        'variation_location_details.location_id',
                        '=',
                        'l.id'
                    )
                    ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                    ->where('p.business_id', $business_id)
                    ->where('p.enable_stock', 1)
                    ->whereRaw('variation_location_details.qty_available <= p.alert_quantity');

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('variation_location_details.location_id', $permitted_locations);
            }

            //Filter by the location
            if (!empty($location_id)) {
                $query->where('variation_location_details.location_id', $location_id);
            }
            
            $products =  $query->count('p.id');
            return $products;

        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesPaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");

            $location_id = request()->location_id;

            $query = Transaction::join('customers as c', 'transactions.customer_id', 'c.id')
                    ->join('payment_terms as pt', 'c.payment_terms_id', '=', 'pt.id')   
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereRaw("DATEDIFF('$today', DATE_ADD( transactions.transaction_date, INTERVAL pt.days DAY)) > 0");  

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            //Filter by the location
            if (!empty($location_id)) {
                $query->where('transactions.location_id', $location_id);
            }

            $dues =  $query->count('transactions.id');
            return $dues;
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");

            $location_id = request()->location_id;

            $query = Transaction::join('contacts as c','transactions.contact_id','c.id')
                ->join('payment_terms as pt', 'c.payment_term_id', '=', 'pt.id')         
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->where('transactions.payment_status', '!=', 'paid')
                ->whereRaw("DATEDIFF('$today', DATE_ADD( transactions.transaction_date, INTERVAL pt.days DAY)) > 0");                

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            //Filter by the location
            if (!empty($location_id)) {
                $query->where('transactions.location_id', $location_id);
            }

            $dues =  $query->count('transactions.id');            
            return $dues;
        }
    }

    /**
     * Shows amount of products close to expire and expired ones
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockExpiryProducts(Request $request)
    {
        if (!auth()->user()->can('stock_expiry_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $location_id = request()->location_id;

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = PurchaseLine::leftjoin(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                ->leftjoin(
                    'products as p',
                    'purchase_lines.product_id',
                    '=',
                    'p.id'
                )
                ->leftjoin(
                    'variations as v',
                    'purchase_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->leftjoin(
                    'product_variations as pv',
                    'v.product_variation_id',
                    '=',
                    'pv.id'
                )
                ->leftjoin('business_locations as l', 't.location_id', '=', 'l.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)                
                ->whereNotNull('exp_date')
                ->where('p.enable_stock', 1)
                ->whereRaw('purchase_lines.quantity > purchase_lines.quantity_sold + quantity_adjusted + quantity_returned');

            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            //Filter by the location
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $products = $query->count('p.id');

            return $products;
        }
    }

    /**
     * Get the monetary value of the total stock.
     *
     * @param  float  $request
     * @return \Illuminate\Http\Response
     */
    public function getTotalStock(Request $request)
    {
        // Params
        $business_id = $request->session()->get('user.business_id');
        $location_id = request()->location_id;
        $date = request()->end;

        // Get data
        $result = DB::select(
            'SELECT monetary_total_stock(?, ?, ?) AS result',
            [$business_id, $location_id, $date]
        );

        return $result[0]->result;
    }

    /**
     * Get peak sales hours by month chart.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getPeakSalesHoursByMonthChart()
    {
        $business_id = request()->session()->get('user.business_id');

        $date_initial = \Carbon::now()->subDays(30);
        $date_final = \Carbon::now();

        $location = request()->get('location_month');

        # Peak sales hours
        $sales = $this->transactionUtil->getPeakSalesHours($business_id, $location, $date_initial, $date_final);

        $labels = [];
        $values = [];

        foreach ($sales as $hour => $sale) {
            $labels[] = $this->transactionUtil->format_time($hour . ':00:00');
            $values[] = $sale;
        }

        $sells_chart_4 = Charts::create('bar', 'highcharts')
        ->title(' ')
            ->template('material')
            ->values($values)
            ->labels($labels)
            ->elementLabel(__('accounting.total_sales'));

        return view('home.peak_sales_hours_month_chart', compact('sells_chart_4'));
    }

    /**
     * Get peak sales hours chart.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getPeakSalesHoursChart()
    {
        $business_id = request()->session()->get('user.business_id');

        $fiscal_year = $this->businessUtil->getCurrentFinancialYear($business_id);

        $location = request()->get('location');

        # Peak sales hours
        $sales = $this->transactionUtil->getPeakSalesHours($business_id, $location, $fiscal_year['start'], $fiscal_year['end']);

        $labels = [];
        $values = [];

        foreach ($sales as $hour => $sale) {
            $labels[] = $this->transactionUtil->format_time($hour . ':00:00');
            $values[] = $sale;
        }

        $sells_chart_3 = Charts::create('bar', 'highcharts')
        ->title(' ')
            ->template('material')
            ->values($values)
            ->labels($labels)
            ->elementLabel(__('accounting.total_sales'));

        return view('home.peak_sales_hours_chart', compact('sells_chart_3'));
    }
}
