<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AssistanceEmployee;
use App\Employees;
use App\Business;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use DateTime;

class AssistanceEmployeeController extends Controller
{
    private $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    public function index(){
        if ( !auth()->user()->can('rrhh_assistance.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $employees = Employees::where('business_id', $business_id)->get();
        return view('rrhh.assistance.index', compact('employees'));
    }

    
    public function getAssistances(){
        if ( !auth()->user()->can('rrhh_assistance.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $data = AssistanceEmployee::select(DB::raw('id, date, time, type, employee_id, business_id'))->where('business_id', $business_id)->orderBy('id', 'DESC')->groupBy(['employee_id', 'date'])->get();
        
        return DataTables::of($data)->editColumn('employee', function ($data) {
            $employee = Employees::where('id', $data->employee_id)->first();
            return $employee->first_name.' '.$employee->last_name;
        })->editColumn('date', function ($data) {
            return $this->transactionUtil->format_date($data->date);
        })->editColumn('schedule', function($data){
            $firstTime = Carbon::now()->timezone($data->business->time_zone)->format('H:i:s');
            $lastTime = Carbon::now()->timezone($data->business->time_zone)->format('H:i:s');
            $assistances = AssistanceEmployee::where('employee_id', $data->employee_id)->where('date', $data->date)->where('business_id', $data->business_id)->orderBy('id', 'ASC')->get();
            foreach($assistances as $key => $assistance){
                if ($key === 0) {
                    $firstTime = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time);
                    $lastTime = Carbon::now()->timezone($data->business->time_zone)->format('H:i:s');
                }
            
                if(count($assistances) > 1){
                    if ($key === count($assistances)-1) {
                        $lastTime = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time);
                    }
                }
            }
            return $this->transactionUtil->format_date($firstTime, true).' - '. $this->transactionUtil->format_date($lastTime, true);
        })->editColumn('number_of_hours', function($data){
            $firstTime = Carbon::now()->timezone($data->business->time_zone)->format('Y-m-d H:i:s');
            $lastTime = Carbon::now()->timezone($data->business->time_zone)->format('Y-m-d H:i:s');
            $assistances = AssistanceEmployee::where('employee_id', $data->employee_id)->where('date', $data->date)->where('business_id', $data->business_id)->orderBy('id', 'ASC')->get();
            $time = 0;
            $newTime = 0;

            $minutes = 0;
            $newMinutes = 0;

            foreach($assistances as $key => $assistance){
                if ($key === 0 && $assistance->type == 'Entrada') {
                    $firstTime = Carbon::createFromFormat('Y-m-d H:i:s', $data->date.' '.$data->time);
                    $lastTime = Carbon::now()->timezone($data->business->time_zone)->format('Y-m-d H:i:s');
                    $time = $firstTime;
                }
            
                if(count($assistances) > 1){
                    if ($key === count($assistances)-1) {
                        $lastTime = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time);
                    }
                }
                if($assistance->type == 'Entrada' && $key != 0){

                }
                if($assistance->type == 'Entrada' && $key != 0){

                }
                $newTime = $time->diffInHours(Carbon::createFromFormat('Y-m-d H:i:s', $data->date.' '.$data->time));
                $time = $time + $newTime;

                $newMinutes = $minutes->diffInHours(Carbon::createFromFormat('Y-m-d H:i:s', $data->date.' '.$data->time));
                $minutes = $minutes + $newMinutes;
            }
            //$time = $firstTime->diffInHours($lastTime);
            //$minutes = $firstTime->diffInMinutes($lastTime);
            $minutes = $minutes - ($time*60);
            $minutes = number_format($minutes, 0, ',', '.');
            return $time.' horas con '.$minutes.' minutos';

        })->editColumn('status', function($data){
            $assistance = AssistanceEmployee::where('employee_id', $data->employee_id)->where('date', $data->date)->where('business_id', $data->business_id)->orderBy('id', 'DESC')->first();
            return $assistance->status;
        })->toJson();
    }

    public function postAssistancesReport(Request $request)
    {
        if (! auth()->user()->can('rrhh_assistance.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->get();
            $assistances = [];
            if($request->select_employee == '0'){
                $assistances = AssistanceEmployee::where('business_id', $business_id)->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)->orderBy('id', 'DESC')->groupBy(['employee_id', 'date'])->get();
                $assistancesEmployee = AssistanceEmployee::where('business_id', $business_id)->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)->orderBy('id', 'DESC')->groupBy(['employee_id', 'date'])->get();
            }
            else{
                $assistances = AssistanceEmployee::where('business_id', $business_id)->where('employee_id', $request->select_employee)->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)->orderBy('id', 'DESC')->groupBy('date')->get();
                $assistancesEmployee = AssistanceEmployee::where('business_id', $business_id)->where('employee_id', $request->select_employee)->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)->orderBy('id', 'DESC')->groupBy('date')->get();
            }

            if($request->report_type == 'pdf'){
                return view('rrhh.assistance.report_pdf', compact(['assistances', 'business']));
                $pdf = \PDF::loadView('rrhh.assistance.report_pdf', compact(['assistances', 'business', 'assistancesEmployee']));
        
                $pdf->setPaper('letter', 'portrait');
                return $pdf->download(__('rrhh.assistance') . '.pdf');

            }else{
                $output = [
                    'success' => true,
                    'msg' => 'Exitoooo 123',
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }
    
}
