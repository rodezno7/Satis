<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Business;
use App\Setting;
use DB;

class SettingController extends Controller
{
    public function index(){
        if ( !auth()->user()->can('rrhh_assistance.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $setting = Setting::where('business_id', $business_id)->first();
        return view('rrhh.settings.index', compact('setting'));
    }

    public function store(Request $request){
        if ( !auth()->user()->can('rrhh_employees.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'exit_time'         => 'required_if:automatic_closing,1',
            //'automatic_closing' => 'required',
        ]);
        try{
            //dd($request);
            DB::commit();
            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();

            if($request->automatic_closing == 1){
                $exit_time = $request->exit_time;
            }else{
                $exit_time = null;
                $request->automatic_closing = 0;
            }

            $setting = Setting::where('business_id', $business_id)->first();
            if($setting) {
                $setting->update(['exit_time' => $exit_time, 'automatic_closing' => $request->automatic_closing]);
                $output = [
                    'success' => true,
                    'msg' => __('rrhh.settings_updated_successfully')
                ];

            } else {
                Setting::create(['business_id' => $business_id, 'exit_time' => $exit_time, 'automatic_closing' => $request->automatic_closing]);

                $output = [
                    'success' => true,
                    'msg' => __('rrhh.settings_added_successfully')
                ];
            }

            
            DB::beginTransaction();
            
        }catch(Exception $e){
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('rrhh.error')
            ];
        }

        return redirect('rrhh-setting')->with('status', $output);
    }
}
