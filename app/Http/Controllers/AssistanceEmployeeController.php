<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\AssistanceEmployee;
use App\Employees;
use App\Business;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Http;
use App\Exports\AssistanceEmployeeReportExport;
use Illuminate\Support\Facades\Crypt;

class AssistanceEmployeeController extends Controller
{
    private $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil){
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

    
    public function getAssistances(Request $request){
        if ( !auth()->user()->can('rrhh_assistance.view') ) {
            abort(403, 'Unauthorized action.');
        }


            $business_id = request()->session()->get('user.business_id');
            $data = AssistanceEmployee::select(DB::raw('id, date, time, type, employee_id, business_id'))
                ->where('business_id', $business_id)
                ->where('type', 'Entrada')
                ->orderBy('id', 'DESC')
                ->groupBy(['employee_id', 'date'])
                ->get();
            
            return DataTables::of($data)->editColumn('employee', function ($data) {
                $employee = Employees::where('id', $data->employee_id)->first();
                return $employee->first_name.' '.$employee->last_name;
            })->editColumn('date', function ($data) {
                return $this->transactionUtil->format_date($data->date);
            })->editColumn('schedule', function($data){
                $firstTime = Carbon::now()->timezone($data->business->time_zone)->format('H:i:s');
                $lastTime = Carbon::now()->timezone($data->business->time_zone)->format('H:i:s');
                $assistances = AssistanceEmployee::where('employee_id', $data->employee_id)
                    ->where('date', $data->date)
                    ->where('business_id', $data->business_id)
                    ->orderBy('id', 'ASC')
                    ->get();
                foreach($assistances as $key => $assistance){
                    if ($key === 0) {
                        $firstTime = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time);
                        $lastTime = Carbon::now()
                            ->timezone($data->business->time_zone)
                            ->format('H:i:s');
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
                $assistances = AssistanceEmployee::where('employee_id', $data->employee_id)
                    ->where('date', $data->date)
                    ->where('business_id', $data->business_id)
                    ->orderBy('id', 'ASC')
                    ->get();
                $time = 0;
                $seconds = 0;
                $minutes = 0;

                foreach($assistances as $key => $assistance){
                    if ($key === 0) {
                        $firstTime = Carbon::createFromFormat('Y-m-d H:i:s', $data->date.' '.$data->time);
                        $lastTime = Carbon::now()
                            ->timezone($data->business->time_zone)
                            ->format('Y-m-d H:i:s');
                    }

                    if($key > 0){
                        if ($key < count($assistances)-1) {
                            if($assistances[$key-1]->type == 'Salida' && $assistance->type == 'Entrada'){
                                $time = Carbon::createFromFormat('Y-m-d H:i:s', $assistances[$key-1]->date.' '.$assistances[$key-1]->time)->diffInHours(Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time));
                                $minutes = Carbon::createFromFormat('Y-m-d H:i:s', $assistances[$key-1]->date.' '.$assistances[$key-1]->time)->diffInMinutes(Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time));
                                $seconds = Carbon::createFromFormat('Y-m-d H:i:s', $assistances[$key-1]->date.' '.$assistances[$key-1]->time)->diffInSeconds(Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time));
                            }
                        }
                        if ($key === count($assistances)-1) {
                            $lastTime = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time);
                        }

                        if($assistance->type == 'Entrada'){
                            $time += $time;         
                            $minutes += $minutes;
                        }
                    }
                }
                $timeTotal = $firstTime->diffInHours($lastTime);
                $minutesTotal = $firstTime->diffInMinutes($lastTime);
                
                $minutesTotal = $minutesTotal - ($timeTotal*60);
                $timeTotal = $timeTotal - $time;
                $minutes = $minutes - ($time*60); 

                $minutesTotal = abs($minutesTotal - $minutes);
                $minutesTotal = round($minutesTotal, 0);
                return $timeTotal.' horas con '.$minutesTotal.' minutos';

            })->editColumn('status', function($data){
                $assistance = AssistanceEmployee::where('employee_id', $data->employee_id)
                    ->where('date', $data->date)
                    ->where('business_id', $data->business_id)
                    ->orderBy('id', 'DESC')
                    ->first();
                return $assistance->status;
            })->toJson();
        
    }

    public function postAssistancesReport(Request $request)
    {
        if (! auth()->user()->can('rrhh_assistance.view')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'select_employee' => 'required',
            'start_date'      => 'required',
            'end_date'        => 'required',
        ]);

        try {
            $business_id = $request->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            $assistances = [];
            $assistanceSummary = [];
            if($request->select_employee == '0'){
                $assistances = AssistanceEmployee::where('business_id', $business_id)
                    ->where('date', '>=', $request->start_date)
                    ->where('date', '<=', $request->end_date)
                    ->orderBy('date', 'ASC')
                    ->orderBy('employee_id', 'ASC')
                    ->get();

                $assistancesEmployee = AssistanceEmployee::where('business_id', $business_id)
                    ->where('date', '>=', $request->start_date)
                    ->where('date', '<=', $request->end_date)
                    ->where('type', 'Entrada')
                    ->orderBy('date', 'ASC')
                    ->orderBy('employee_id', 'ASC')
                    ->groupBy(['employee_id', 'date'])
                    ->get();

                foreach($assistancesEmployee as $data){
                    $assistancesEmployeeData = AssistanceEmployee::where('employee_id', $data->employee_id)
                        ->where('date', '>=', $request->start_date)
                        ->where('date', '<=', $request->end_date)
                        ->where('business_id', $business_id)
                        ->orderBy('id', 'ASC')
                        ->get();

                    $currrentDate = Carbon::now()->timezone($data->business->time_zone)->format('Y-m-d H:i:s');
                    $time = 0;
                    $minutes = 0;

                    foreach($assistancesEmployeeData as $key => $assistance){
                        if($data->date == $assistance->date){
                            $currentDateAssistanceEmployee = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time);
                            if ($key === 0) {
                                $firstTime = $currentDateAssistanceEmployee;
                                $lastTime = $currrentDate;
                            }
        
                            if($key > 0){
                                if ($key < count($assistancesEmployeeData)-1) {
                                    if($assistancesEmployeeData[$key-1]->type == 'Salida' && $assistance->type == 'Entrada'){
                                        $previousDateAssistanceEmployee = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key-1]->date.' '.$assistancesEmployeeData[$key-1]->time);
                                        
                                        $time = $previousDateAssistanceEmployee->diffInHours($currentDateAssistanceEmployee);
                                        $minutes = $previousDateAssistanceEmployee->diffInMinutes($currentDateAssistanceEmployee);
                                    }
                                }
                                if ($key === count($assistancesEmployeeData)-1) {
                                    $lastTime = $currentDateAssistanceEmployee;
                                }
        
                                if($assistance->type == 'Entrada'){
                                    $time += $time;         
                                    $minutes += $minutes;
                                }
                            }
                        }
                    }
                    $timeTotal = $firstTime->diffInHours($lastTime);
                    $minutesTotal = $firstTime->diffInMinutes($lastTime);
                    
                    $minutesTotal = $minutesTotal - ($timeTotal*60);
                    $timeTotal = $timeTotal - $time;
                    $minutes = $minutes - ($time*60); 
    
                    $minutesTotal = abs($minutesTotal - $minutes);
                    $minutesTotal = round($minutesTotal);

                    
                    $assistanceSummary[$data->id] = (object) [
                        'employee' => $data->employee->first_name.' '.$data->employee->last_name, 
                        'date' => $data->date, 
                        'time_worked' => $timeTotal.' horas con '.$minutesTotal.' minutos'
                    ];
                }
            }
            else{
                $assistances = AssistanceEmployee::where('business_id', $business_id)
                    ->where('employee_id', $request->select_employee)
                    ->where('date', '>=', $request->start_date)
                    ->where('date', '<=', $request->end_date)
                    ->orderBy('date', 'ASC')
                    ->orderBy('employee_id', 'ASC')
                    ->get();
                $assistancesEmployee = AssistanceEmployee::where('business_id', $business_id)
                    ->where('employee_id', $request->select_employee)
                    ->where('date', '>=', $request->start_date)
                    ->where('date', '<=', $request->end_date)
                    ->where('type', 'Entrada')
                    ->orderBy('date', 'ASC')
                    ->orderBy('employee_id', 'ASC')
                    ->groupBy('date')
                    ->get();
                $assistancesEmployeeData = AssistanceEmployee::where('employee_id', $request->select_employee)
                    ->where('date', '>=', $request->start_date)
                    ->where('date', '<=', $request->end_date)
                    ->where('business_id', $business_id)
                    ->orderBy('id', 'ASC')
                    ->get();
                
                foreach($assistancesEmployee as $data){
                    $currrentDate = Carbon::now()->timezone($data->business->time_zone)->format('Y-m-d H:i:s');
                    $time = 0;
                    $seconds = 0;
                    $minutes = 0;
                    foreach($assistancesEmployeeData as $key => $assistance){
                        if($data->date == $assistance->date){
                            $currentDateAssistanceEmployee = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date.' '.$assistance->time);
                            if ($key === 0) {
                                $firstTime = $currentDateAssistanceEmployee;
                                $lastTime = $currrentDate;
                            }
        
                            if($key > 0){
                                if ($key < count($assistancesEmployeeData)-1) {
                                    if($assistancesEmployeeData[$key-1]->type == 'Salida' && $assistance->type == 'Entrada'){
                                        $previousDateAssistanceEmployee = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key-1]->date.' '.$assistancesEmployeeData[$key-1]->time);
                                        
                                        $time = $previousDateAssistanceEmployee->diffInHours($currentDateAssistanceEmployee);
                                        $minutes = $previousDateAssistanceEmployee->diffInMinutes($currentDateAssistanceEmployee);
                                        $seconds = $previousDateAssistanceEmployee->diffInSeconds($currentDateAssistanceEmployee);
                                    }
                                }
                                if ($key === count($assistancesEmployeeData)-1) {
                                    $lastTime = $currentDateAssistanceEmployee;
                                }
        
                                if($assistance->type == 'Entrada'){
                                    $time += $time;         
                                    $minutes += $minutes;
                                }
                            }
                        }
                    }
                    $timeTotal = $firstTime->diffInHours($lastTime);
                    $minutesTotal = $firstTime->diffInMinutes($lastTime);
                    
                    $minutesTotal = $minutesTotal - ($timeTotal*60);
                    $timeTotal = $timeTotal - $time;
                    $minutes = $minutes - ($time*60); 
    
                    $minutesTotal = abs($minutesTotal - $minutes);
                    $minutesTotal = round($minutesTotal);

                    
                    $assistanceSummary[$data->id] = (object) [
                        'employee' => $data->employee->first_name.' '.$data->employee->last_name, 
                        'date' => $data->date, 
                        'time_worked' => $timeTotal.' horas con '.$minutesTotal.' minutos'
                    ];
                }
            }
            if($request->report_type == 'pdf'){
                $pdf = \PDF::loadView('rrhh.assistance.report_pdf', compact(['assistances', 'business', 'assistancesEmployee', 'assistanceSummary']));
        
                $pdf->setPaper('letter', 'portrait');
                return $pdf->stream(__('rrhh.assistance') . '.pdf');

            }else{
                return Excel::download(new AssistanceEmployeeReportExport($assistances, $assistanceSummary, $business, $this->transactionUtil), __('report.all_sales_with_utility_report') . '.xlsx');
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

    public function getByAssistances($id) 
    {
        $business_id = request()->session()->get('user.business_id');
        $assistance = AssistanceEmployee::where('id', $id)
            ->where('business_id', $business_id)
            ->first();

        $employee = Employees::findOrFail($assistance->employee_id);
        $assistances = AssistanceEmployee::where('employee_id', $assistance->employee_id)
            ->where('date', $assistance->date)
            ->get();
    
        return view('rrhh.assistance.show', compact('assistances', 'employee'));
    }
}
