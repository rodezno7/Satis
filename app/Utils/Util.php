<?php

namespace App\Utils;

use App\Binnacle;
use App\Business;
use App\BusinessLocation;
use App\Cashier;
use App\TaxGroup;
use App\CustomerPortfolio;
use App\Optics\LabOrder;
use App\Optics\Patient;
use App\ReferenceCount;
use App\Optics\StatusLabOrder;
use App\User;
use App\Warehouse;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Carbon\Carbon;

class Util
{
    /**
     * This function unformats a number and returns them in plain eng format
     *
     * @param int $input_number
     *
     * @return float
     */
    public function num_uf($input_number, $currency_details = [])
    {
          $thousand_separator  = '';
          $decimal_separator  = '';

        if (!empty($currency_details)) {
            $thousand_separator = $currency_details->thousand_separator;
            $decimal_separator = $currency_details->decimal_separator;
        } else {
            $thousand_separator = session()->has('currency') ? session('currency')['thousand_separator'] : '';
            $decimal_separator = session()->has('currency') ? session('currency')['decimal_separator'] : '';
        }

        $num = str_replace($thousand_separator, '', $input_number);
        $num = str_replace($decimal_separator, '.', $num);

        return (float)$num;
    }

    /**
     * This function formats a number and returns them in specified format
     *
     * @param int $input_number
     * @param boolean $add_symbol = false
     *
     * @return string
     */
    public function num_f($input_number, $add_symbol = false, $precision = 2)
    {
          $formatted = number_format($input_number, $precision, session('currency')['decimal_separator'], session('currency')['thousand_separator']);

        if ($add_symbol) {
            if (session('business.currency_symbol_placement') == 'after') {
                $formatted = $formatted . ' ' . session('currency')['symbol'];
            } else {
                $formatted = session('currency')['symbol'] . ' ' . $formatted;
            }
        }

          return $formatted;
    }

     /**
     * Calculates percentage for a given number
     *
     * @param int $number
     * @param int $percent
     * @param int $addition default = 0
     *
     * @return float
     */
    public function calc_percentage($number, $percent, $addition = 0)
    {
        return ($addition + ($number * ($percent / 100)));
    }

    /**
     * Calculates base value on which percentage is calculated
     *
     * @param int $number
     * @param int $percent
     *
     * @return float
     */
    public function calc_percentage_base($number, $percent)
    {

        return ($number * 100) / (100 + $percent);
    }

    /**
     * Calculates percentage
     *
     * @param int $base
     * @param int $number
     *
     * @return float
     */
    public function get_percent($base, $number)
    {
        $diff = $number - $base;
        if ($base <= 0) {
            return 0;
        } else {
            return ($diff / $base) * 100;
        }
    }

    //Returns all avilable purchase statuses
    public function orderStatuses()
    {
        return [ 'received' => __('lang_v1.received'), 'pending' => __('lang_v1.pending'), 'ordered' => __('lang_v1.ordered')];
    }

    /**
     * Get total percent from tax groups given
     * @param int $tax_group_id
     * @param float $amount
     * @return float
     */
    public function getTaxPercent($tax_group_id) {
        if(is_null($tax_group_id)) {
            return null;
        }

        $tax_rates = TaxGroup::find($tax_group_id)->tax_rates;

        $percent = 0;
        if(!empty($tax_rates)) {
            foreach ($tax_rates as $tax_rate) {
                $percent += $tax_rate->percent;
            }
        }

        return round($percent, 4);
    }

    /**
     * Returns printing formats | array index represent file name format
     * @return array
     */
    public function print_formats(){
        $print_formats = [
            "invoice" => __("document_type.invoice"),
            "fiscal_credit" => __("document_type.fiscal_credit"),
            "export_invoice" => __("document_type.export_invoice"),
            "ticket" => __('document_type.ticket'),
            "proof_fiscal_credit" => __("document_type.proof_fiscal_credit"),
            "referral_note" => __("document_type.referral_note"),
            "fiscal_credit_return" => __("document_type.fiscal_credit_return"),
            "excluded_subject" => __("document_type.excluded_subject"),
            "commercial_invoice" => __("document_type.commercial_invoice"),
        ];

        return $print_formats;
    }

    /**
     * Defines available Payment Types
     *
     * @return array
     */
    public function payment_types()
    {
        $payment_types = [
            'cash' => __('lang_v1.cash'),
            'card' => __('lang_v1.card'),
            'check' => __('lang_v1.check'),
            'bank_transfer' => __('lang_v1.bank_transfer')
        ];

        return $payment_types;
    }

    /**
     * Returns the list of modules enabled
     *
     * @return array
     */
    public function allModulesEnabled()
    {
        $enabled_modules = session()->has('business') ? session('business')['enabled_modules'] : null;
        $enabled_modules = (!empty($enabled_modules) && $enabled_modules != 'null') ? $enabled_modules : [];

        return $enabled_modules;
        //Module::has('Restaurant');
    }

    /**
     * Returns the list of modules enabled
     *
     * @return array
     */
    public function isModuleEnabled($module)
    {
        $enabled_modules = $this->allModulesEnabled();

        if (in_array($module, $enabled_modules)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Converts date in business format to mysql format
     *
     * @param string $date
     * @param bool $time (default = false)
     * @return strin
     */
    public function uf_date($date, $time = false)
    {
        $date_format = session('business.date_format');
        $mysql_format = 'Y-m-d';
        if ($time) {
            if (session('business.time_format') == 12) {
                $date_format = $date_format . ' h:i A';
            } else {
                $date_format = $date_format . ' H:i';
            }
            $mysql_format = 'Y-m-d H:i:s';
        }

        return \Carbon::createFromFormat($date_format, $date)->format($mysql_format);
    }

    /**
     * Converts time in business format to mysql format
     *
     * @param string $time
     * @return strin
     */
    public function uf_time($time)
    {
        $time_format = 'H:i';
        if (session('business.time_format') == 12) {
            $time_format = 'h:i A';
        }
        return \Carbon::createFromFormat($time_format, $time)->format('H:i');
    }

    /**
     * Converts time in business format to mysql format
     *
     * @param string $time
     * @return strin
     */
    public function format_time($time)
    {
        $time_format = 'H:i';
        if (session('business.time_format') == 12) {
            $time_format = 'h:i A';
        }
        return \Carbon::createFromFormat('H:i:s', $time)->format($time_format);
    }

    /**
     * Converts date in business format to mysql format
     *
     * @param string $date
     * @param bool $time (default = false)
     * @return strin
     */
    public function format_date($date, $show_time = false)
    {
        $format = session('business.date_format');
        if (!empty($show_time)) {
            if (session('business.time_format') == 12) {
                $format .= ' h:i A';
            } else {
                $format .= ' H:i';
            }
        }
        
        return \Carbon::createFromTimestamp(strtotime($date))->format($format);
    }

    /**
     * Increments reference count for a given type and given business
     * and gives the updated reference count
     *
     * @param string $type
     * @param int $business_id
     * @param bool $setter
     *
     * @return int
     */
    public function setAndGetReferenceCount($type, $business_id = null, $setter = true)
    {
        if (empty($business_id)) {
            $business_id = request()->session()->get('user.business_id');
        }

        $ref = ReferenceCount::where('ref_type', $type)
                          ->where('business_id', $business_id)
                          ->first();
        if (!empty($ref)) {
            $ref->ref_count += 1;

            if ($setter) {
                $ref->save();
            }

            return $ref->ref_count;

        } else {
            $new_ref = ReferenceCount::create([
                'ref_type' => $type,
                'business_id' => $business_id,
                'ref_count' => 1
            ]);

            return $new_ref->ref_count;
        }
    }

    /**
     * Generates reference number
     *
     * @param string $type
     * @param int $business_id
     *
     * @return int
     */
    public function generateReferenceNumber($type, $ref_count, $business_id = null)
    {

        $prefix = '';

        if (session()->has('business') && !empty(request()->session()->get('business.ref_no_prefixes')[$type])) {
            $prefix = request()->session()->get('business.ref_no_prefixes')[$type];
        }
        if (!empty($business_id)) {
            $business = Business::find($business_id);
            $prefixes = $business->ref_no_prefixes;
            $prefix = $prefixes[$type];
        }

        $ref_digits =  str_pad($ref_count, 4, 0, STR_PAD_LEFT);

        if (!in_array($type, ['contacts', 'business_location', 'username'])) {
            $ref_year = \Carbon::now()->year;
            $ref_number = $prefix . $ref_year . '/' . $ref_digits;
        } else {
            $ref_number = $prefix . $ref_digits;
        }

        return $ref_number;
    }

    public function generatePortfolioCode()
    {
        $business_id = request()->session()->get('user.business_id');
        $portfolios_prefix = Business::where('id', $business_id)->value('portfolio_prefix');
        $last_id = CustomerPortfolio::where('business_id', $business_id);
        if(!empty($last_id)){
            $last_id = $last_id->max('id');
        }
        if(!empty($last_id)){
            $new_id = $last_id + 1;
        }else{
            $new_id = 1;
        }
        
        return $portfolios_prefix . str_pad($new_id, 4, '0', STR_PAD_LEFT);
    }

    public function generateCashierCode()
    {
        $business_id = request()->session()->get('user.business_id');
        $cashier_prefix = Business::where('id', $business_id)->value('cashier_prefix');
        $last_id = Cashier::where('business_id', $business_id);
        if(!empty($last_id)){
            $last_id = $last_id->max('id');
        }
        if(!empty($last_id)){
            $new_id = $last_id + 1;
        }else{
            $new_id = 1;
        }
        
        return $cashier_prefix . str_pad($new_id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get fixed asset prefix
     * @param int $business_id
     * @return string
     */
    public function generateFixedAssetPrefix($business_id, $last_id){
        $business = Business::find($business_id);

        $prefix = "";
        if(!empty($business->fixed_asset_prefix)){
            $prefix = $business->fixed_asset_prefix;
        }

        $suffix = 1;
        if(!empty($business->fixed_asset_prefix)){
            $fixed_asset_prefix = $business->fixed_asset_prefix;
        }

        if(!is_null($last_id)){
            $suffix = $last_id + 1;
        }

        return $prefix . str_pad($suffix, 4, 0, STR_PAD_LEFT);
    }

     /**
     * Checks if the given user is admin
     *
     * @param obj $user
     * @param int $business_id
     *
     * @return bool
     */
    public function is_admin($user, $business_id)
    {
        return $user->hasRole('Admin#' . $business_id) ? true : false;
    }

     /**
     * Checks if the feature is allowed in demo
     *
     * @return mixed
     */
    public function notAllowedInDemo()
    {
        //Disable in demo
        if (config('app.env') == 'demo') {
            $output = ['success' => 0,
                    'msg' => __('lang_v1.disabled_in_demo')
                ];
            if (request()->ajax()) {
                return $output;
            } else {
                return back()->with('status', $output);
            }
        }
    }

    /**
     * Sends SMS notification.
     *
     * @param  array $data
     * @return void
     */
    public function sendSms($data)
    {
        $sms_settings = $data['sms_settings'];
        $request_data = [
            $sms_settings['send_to_param_name'] => $data['mobile_number'],
            $sms_settings['msg_param_name'] => $data['sms_body'],
        ];

        if (!empty($sms_settings['param_1']) && !empty($sms_settings['param_val_1'])) {
            $request_data[$sms_settings['param_1']] = $sms_settings['param_val_1'];
        }
        if (!empty($sms_settings['param_2']) && !empty($sms_settings['param_val_2'])) {
            $request_data[$sms_settings['param_2']] = $sms_settings['param_val_2'];
        }
        if (!empty($sms_settings['param_3']) && !empty($sms_settings['param_val_3'])) {
            $request_data[$sms_settings['param_3']] = $sms_settings['param_val_3'];
        }
        if (!empty($sms_settings['param_4']) && !empty($sms_settings['param_val_4'])) {
            $request_data[$sms_settings['param_4']] = $sms_settings['param_val_4'];
        }
        if (!empty($sms_settings['param_5']) && !empty($sms_settings['param_val_5'])) {
            $request_data[$sms_settings['param_5']] = $sms_settings['param_val_5'];
        }

        $client = new Client();

        if ($sms_settings['request_method'] == 'get') {
            $response = $client->get($sms_settings['url'] . '?'. http_build_query($request_data));
        } else {
            $response = $client->post($sms_settings['url'], [
                'form_params' => $request_data
            ]);
        }
    }

    /**
     * Uploads document to the server if present in the request
     * @param obj $request, string $file_name, string dir_name
     *
     * @return string
     */
    public function uploadFile($request, $file_name, $dir_name)
    {
        //If app environment is demo return null
        if (config('app.env') == 'demo') {
            return null;
        }
        
        $uploaded_file_name = null;
        if ($request->hasFile($file_name) && $request->file($file_name)->isValid()) {
            if ($request->$file_name->getSize() <= config('constants.document_size_limit')) {
                $new_file_name = time() . '_' . $request->$file_name->getClientOriginalName();
                if($request->$file_name->storeAs($dir_name, $new_file_name)) {
                    $uploaded_file_name = $new_file_name;
                }
            }
        }
        return $uploaded_file_name;
    }

    public function generateWarehouseCode()
    {
        $business_id = request()->session()->get('user.business_id');
        $warehouse_prefix = Business::where('id', $business_id)->value('warehouse_prefix');
        $count = Warehouse::where('business_id', $business_id)->count();
        $count ++;

        return $warehouse_prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get banks list.
     * 
     * @return array
     */
    public function checkbook_formats() {
        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        
        switch ($business->check_format_kit) {
            case 1:
                $print_formats = [
                    'default' => __('lang_v1.default'),
                    'agricola' => 'Banco Agrícola',
                    'azul' => 'Banco Azul',
                    'credomatic' => 'Banco de América Central',
                    'promerica' => 'Banco Promérica',
                ];
                break;

            case 2:
                $print_formats = [
                    'default' => __('lang_v1.default'),
                    'agricola' => 'Banco Agrícola',
                    'azul' => 'Banco Azul',
                    'credomatic' => 'Banco de América Central',
                    'cuscatlan' => 'Banco Cuscatlán',
                    'davivienda' => 'Banco Davivienda',
                    'hipotecario' => 'Banco Hipotecario',
                    'promerica' => 'Banco Promérica',
                    'constelacion' => 'S.A.C. Constelación',
                ];
                break;
        }
        
        return $print_formats;
    }

    /**
     * First and last day of the month of the date set as parameter.
     * 
     * @param  mixed  $actual_date,
     * @return array
     */
    public function first_last_month_day($actual_date)
    {
        // Actual month first day
        $month = date('m', strtotime($actual_date));
        $year = date('Y', strtotime($actual_date));
        $first_day = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));

        // Actual month last day
        $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));
        $last_day = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));

        return [
            'first_day' => $first_day,
            'last_day' => $last_day,
        ];
    }

    /**
     * Generate lab order code.
     * 
     * @param  int  $location_id
     * @return string
     */
    public function generateLabOrderCode($location_id = null)
    {
        $business_id = request()->session()->get('user.business_id');
        $laborder_prefix = Business::where('id', $business_id)->value('laborder_prefix');
        $last_id = LabOrder::where('business_id', $business_id);

        if ($location_id != null) {
            $location = BusinessLocation::find($location_id);
            $location_id = $location->location_id;
        }

        if (! empty($last_id)) {
            $last_id = $last_id->max('id');
        }

        if (! empty($last_id)) {
            $new_id = $last_id + 1;

        } else {
            $new_id = 1;
        }

        return $laborder_prefix . $location_id . str_pad($new_id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate status lab order code.
     * 
     * @return string
     */
    public function generateStatusLabOrderCode()
    {
        $business_id = request()->session()->get('user.business_id');
        $slo_prefix = Business::where('id', $business_id)->value('status_laborder_prefix');
        $last_id = StatusLabOrder::where('business_id', $business_id);

        if (! empty($last_id)) {
            $last_id = $last_id->max('id');
        }

        if (! empty($last_id)) {
            $new_id = $last_id + 1;

        } else {
            $new_id = 1;
        }

        return $slo_prefix . str_pad($new_id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate patient code.
     * 
     * @return string
     */
    public function generatePatientsCode()
    {
        $business_id = request()->session()->get('user.business_id');
        $patients_prefix = Business::where('id', $business_id)->value('patient_prefix');
        $last_id = Patient::where('business_id', $business_id);

        if (! empty($last_id)) {
            $last_id = $last_id->max('id');
        }
        
        if (! empty($last_id)) {
            $new_id = $last_id + 1;

        } else {
            $new_id = 1;
        }

        return $patients_prefix . str_pad($new_id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Returns all avilable sexs.
     * 
     * @return array
     */
    public function Sexs()
    {
        return [
            'female' => __('lang_v1.female'),
            'male' => __('lang_v1.male'), 'other' => __('lang_v1.other')
        ];
    }

    /**
     * Register action in binnacle.
     * 
     * @param  string  $module
     * @param  string  $action
     * @param  string  $reference
     * @param  mixed  $old_record
     * @param  mixed  $new_record
     * @return void
     */
    public function registerBinnacle($module, $action, $reference = null, $old_record = null, $new_record = null)
    {
        $business = Business::find(request()->session()->get('user.business_id'));

        if($business){
            //Bitacoras
            if ($business->enable_binnacle) {
                $user = User::find(request()->session()->get('user.id'));
    
                $params = ['user' => $user->first_name . ' ' . $user->last_name];
        
                if (! is_null($reference)) {
                    $params['reference'] = $reference;
                }
                $globalUtil = new GlobalUtil;
                $ip = $globalUtil->getUserIP();
                $infoClient = $this->getDataClient($ip);
                
                $binnacle['user_id'] = $user->id;
                $binnacle['module'] = $module;
                $binnacle['reference'] = $reference;
                $binnacle['action'] = __('binnacle.' . $module . '_' . $action, $params);
                $binnacle['realized_in'] = Carbon::now()->timezone('America/El_Salvador')->format('Y-m-d H:i:s');
                $binnacle['machine_name'] = php_uname();
                $binnacle['ip'] = $ip;
                $binnacle['city'] = $infoClient['geoplugin_city'];
                $binnacle['country'] = $infoClient['geoplugin_countryName'];
                $binnacle['latitude'] = $infoClient['geoplugin_longitude'];
                $binnacle['longitude'] = $infoClient['geoplugin_latitude'];
                $binnacle['domain'] = $request->getHttpHost();
        
                if (! is_null($old_record)) {
                    $binnacle['old_record'] = json_encode($old_record);
                }
        
                if (! is_null($new_record)) {
                    $binnacle['new_record'] = json_encode($new_record);
                }
        
                Binnacle::create($binnacle);
            }
        }else{
            //Bitacora para inicio de sesion
            if($action == 'login'){
                $globalUtil = new GlobalUtil;
                $ip = $globalUtil->getUserIP();
                $infoClient = $this->getDataClient($ip);
                
                $binnacle['user_id'] = $module;
                $binnacle['module'] = null;
                $binnacle['reference'] = null;
                $binnacle['action'] = $action;
                $binnacle['realized_in'] = Carbon::now()->timezone('America/El_Salvador')->format('Y-m-d H:i:s');
                $binnacle['machine_name'] = php_uname();
                $binnacle['ip'] = $ip;
                $binnacle['city'] = $infoClient['geoplugin_city'];
                $binnacle['country'] = $infoClient['geoplugin_countryName'];
                $binnacle['latitude'] = $infoClient['geoplugin_longitude'];
                $binnacle['longitude'] = $infoClient['geoplugin_latitude'];
                $binnacle['domain'] = request()->getHttpHost();
                $binnacle['old_record'] = null;
                $binnacle['new_record'] = null;
        
                Binnacle::create($binnacle);
            }
        }
    }

    /**
     * Generate reference for quote.
     * 
     * @return  string
     */
    public function generateQuoteReference()
    {
        $business_id = request()->session()->get('user.business_id');

        $business = Business::where('id', $business_id)->first();

        $last_correlative = DB::table('quotes')
            ->select(DB::raw('MAX(id) as max'))
            ->first();

        if ($last_correlative->max != null) {
            $correlative = $last_correlative->max + 1;

        } else {
            $correlative = 1;
        }

        $cont = str_pad($correlative, 5, '0', STR_PAD_LEFT);
        
        return $business->quote_prefix . $cont;
    }

    public function getDataClient($inClient){
        $information = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$inClient));
            return $information;
    }
}