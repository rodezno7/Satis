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

    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    public function index()
    {
        if (!auth()->user()->can('rrhh_assistance.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $employees = Employees::where('business_id', $business_id)->get();

        if(request()->ajax()){
            $params = [
                // Filters
                'employee_id' => request()->input('employee_id'),
                'start_date' => request()->input('start_date'),
                'end_date' => request()->input('end_date'),

                // Datatable parameters
                'start_record' => request()->get('start'),
                'page_size' => request()->get('length'),
                'search' => request()->get('search'),
                'order' => request()->get('order')
            ];

            $assistancesData = collect($this->getAssitancesData($params));

            $datatable = DataTables::of($assistancesData['data'])
                ->addColumn('schedule', function ($row) {
                    $currrentDate = Carbon::now()
                        ->timezone($row->time_zone)
                        ->format('Y-m-d H:i:s');
                    $assistances = AssistanceEmployee::where('employee_id', $row->employee_id)
                        ->where('date', $row->date)
                        ->where('business_id', $row->business_id)
                        ->orderBy('id', 'ASC')
                        ->get();
        
                    $keyAssitance = count($assistances) - 1;
                    if ($assistances[$keyAssitance]->type == 'Entrada') {
                        $lastAssistanceEmployee = AssistanceEmployee::where('employee_id', $row->employee_id)
                            ->where('id', '>', $assistances[$keyAssitance]->id)
                            ->where('business_id', $row->business_id)
                            ->orderBy('id', 'ASC')
                            ->first();
                        if ($lastAssistanceEmployee) {
                            $assistances->add($lastAssistanceEmployee);
                        }
                    }

                    $firstTime = '';
        
                    foreach ($assistances as $key => $assistance) {
                        $currentDateAssistanceEmployee = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date . ' ' . $assistance->time);
                        if ($assistances[0]->type == 'Entrada') {
                            if ($key === 0) {
                                $firstTime = $currentDateAssistanceEmployee;
                                $lastTime = $currrentDate;
                            }
                        }
        
        
                        if ($assistances[0]->type == 'Salida') {
                            if ($key === 1 && $assistances[1]->type == 'Entrada') {
                                $firstTime = $currentDateAssistanceEmployee;
                                $lastTime = $currrentDate;
                            }
                        }
        
                        if ($assistances[count($assistances) - 1]->type == 'Entrada') {
                            $lastTime = $currrentDate;
                        } else {
                            $lastTime = $currentDateAssistanceEmployee;
                        }
                    }
                    return $this->transactionUtil->format_date($firstTime, true) . ' - ' . $this->transactionUtil->format_date($lastTime, true);
                })->addColumn('number_of_hours', function ($row) {
                    $assistancesEmployeeData = AssistanceEmployee::where('employee_id', $row->employee_id)
                        ->where('date', $row->date)
                        ->where('business_id', $row->business_id)
                        ->orderBy('id', 'ASC')
                        ->get();
        
                    $keyAssitance = count($assistancesEmployeeData) - 1;
                    if ($assistancesEmployeeData[$keyAssitance]->type == 'Entrada') {
                        $lastAssistanceEmployee = AssistanceEmployee::where('employee_id', $row->employee_id)
                            ->where('id', '>', $assistancesEmployeeData[$keyAssitance]->id)
                            ->where('business_id', $row->business_id)
                            ->orderBy('id', 'ASC')
                            ->first();
                        if ($lastAssistanceEmployee) {
                            $assistancesEmployeeData->add($lastAssistanceEmployee);
                        }
                    }
        
                    $seconds = 0;
                    $secondsFuera = 0;
                    $currrentDate = Carbon::now()->timezone($row->time_zone)->format('Y-m-d H:i:s');
                    foreach ($assistancesEmployeeData as $key => $assistance) {
                        $assistanceCurrentDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date . ' ' . $assistance->time);
                        if ($assistancesEmployeeData[0]->type == 'Entrada') {
                            if ($key == 0) {
                                $firstDate = $assistanceCurrentDate;
                            }
        
                            if ($key > 0) {
                                $assistancePreviousDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key - 1]->date . ' ' . $assistancesEmployeeData[$key - 1]->time);
                                if ($key < count($assistancesEmployeeData) - 1) {
                                    //Calcular el tiempo en que la persona estuvo afuera
                                    if ($assistancesEmployeeData[$key - 1]->type == 'Salida' && $assistance->type == 'Entrada') {
                                        $entradaDate = $assistanceCurrentDate;
                                        $salidaDate = $assistancePreviousDate;
        
                                        $secondsEntrada = strtotime($entradaDate);
                                        $secondsSalida = strtotime($salidaDate);
                                        $secondsFuera += $secondsEntrada - $secondsSalida;
                                    }
                                }
        
                                if ($key == count($assistancesEmployeeData) - 1) {
                                    //Calculando el tiempo de la primer entrada y la salida o con la fecha actual 
                                    //si no hay registro de la ultima salida
                                    if ($assistance->type == 'Salida') { //Obtener la fecha de la ultima salida
                                        $lastDate = $assistanceCurrentDate;
                                    }
        
                                    if ($assistance->type == 'Entrada') { //Obtener la fecha actual
                                        $lastDate = $currrentDate;
                                    }
        
                                    $secondsFirst = strtotime($firstDate);
                                    $secondsLast = strtotime($lastDate);
                                    $seconds = $secondsLast - $secondsFirst;
                                    $seconds = $seconds - $secondsFuera;
                                }
                            }
                        } else { //Empieza con salida
                            if ($key > 0) { //Entrada
                                if ($key == 1) {
                                    $firstDate = $assistanceCurrentDate;
                                }
        
                                if ($key > 1) {
                                    $assistancePreviousDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key - 1]->date . ' ' . $assistancesEmployeeData[$key - 1]->time);
        
                                    if ($key < (count($assistancesEmployeeData) - 1)) {
                                        //Calcular el tiempo en que la persona estuvo afuera
                                        if ($assistancesEmployeeData[$key - 1]->type == 'Salida' && $assistance->type == 'Entrada') {
                                            $entradaDate = $assistanceCurrentDate;
                                            $salidaDate = $assistancePreviousDate;
        
                                            $secondsEntrada = strtotime($entradaDate);
                                            $secondsSalida = strtotime($salidaDate);
                                            $secondsFuera += $secondsEntrada - $secondsSalida;
                                        }
                                    }
        
                                    if ($key == (count($assistancesEmployeeData) - 1)) {
                                        //Calculando el tiempo de la primer entrada y la salida o con la fecha actual 
                                        //si no hay registro de la ultima salida
                                        if ($assistance->type == 'Salida') { //Obtener la fecha de la ultima salida
                                            $lastDate = $assistanceCurrentDate;
                                        }
        
                                        if ($assistance->type == 'Entrada') { //Obtener la fecha actual
                                            $lastDate = $currrentDate;
                                        }
        
                                        $secondsFirst = strtotime($firstDate);
                                        $secondsLast = strtotime($lastDate);
                                        $seconds = $secondsLast - $secondsFirst;
                                        $seconds = $seconds - $secondsFuera;
                                    }
                                }
                            }
                        }
                    }
                    $time_worked = $this->convertSecondsToHours($seconds);
        
                    return $time_worked;
                })->editColumn('status', function ($row) {
                    $assistance = AssistanceEmployee::where('employee_id', $row->employee_id)
                        ->where('date', $row->date)
                        ->where('business_id', $row->business_id)
                        ->orderBy('id', 'DESC')
                        ->first();
        
                    if ($assistance->type == 'Salida') {
                        return $assistance->status;
                    } else {
                        $lastAssistanceEmployee = AssistanceEmployee::where('employee_id', $row->employee_id)
                            ->where('id', '>', $assistance->id)
                            ->where('business_id', $row->business_id)
                            ->orderBy('id', 'ASC')
                            ->first();
                        if ($lastAssistanceEmployee) {
                            return $lastAssistanceEmployee->status;
                        } else {
                            return $assistance->status;
                        }
                    }
                })->addColumn('actions', function ($row) {
                    $html = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.__("messages.actions").' <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    $html .= '<li> <a href="#" onClick="viewDetail('.$row->id.')"><i class="fa fa-eye"></i>'.__("messages.view").' </a></li>';
                    $html .= '</ul></div>';

                    return $html;                
                });
        
                $datatable = $datatable->rawColumns(['employee', 'schedule', 'number_of_hours', 'status', 'actions'])
                    ->setTotalRecords($assistancesData['count'])
                    ->setFilteredRecords($assistancesData['count'])
                    ->skipPaging()
                    ->toJson();

                return $datatable;
        
        }

        
        return view('rrhh.assistance.index', compact('employees'));
    }


    /**
     * Get assistances data.
     * 
     * @param  array  $params
     * @return array
     */
    public function getAssitancesData($params)
    {
        // Business filter
        $business_id = request()->session()->get('user.business_id');

        //Employee by filter
        if (! empty($params['employee_id'])) {
            $employee_id = $params['employee_id'];
        } else {
            $employee_id = 0;
        }

        // Start & end date by filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
        } else {
            $start_date = '';
            $end_date =  '';
        }

        // Datatable parameters
        $start_record = $params['start_record'];
        $page_size = $params['page_size'];
        $search_array = $params['search'];
        $search = ! is_null($search_array['value']) ? $search_array['value'] : '';
        $order = $params['order'];

        // Count payrolls
        $count = DB::select(
            'CALL count_all_assistance(?, ?, ?, ?, ?)',
            array(
                $business_id,
                $employee_id,
                $start_date,
                $end_date,
                $search
            )
        );

        // Assistances
        $parameters = [
            $business_id,
            $employee_id,
            $start_date,
            $end_date,
            $search,
            $start_record,
            $page_size,
            $order[0]['column'],
            $order[0]['dir']
        ];

        $assistancesData = DB::select(
            'CALL get_all_assistances(?, ?, ?, ?, ?, ?, ?, ?, ?)',
            $parameters
        );

    
        
        $result = [
            'data' => $assistancesData,
            'count' => $count[0]->count
        ];

        return $result;
    }


    public function getAssistances(Request $request)
    {
        if (!auth()->user()->can('rrhh_assistance.view')) {
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
            return $employee->first_name . ' ' . $employee->last_name;
        })->editColumn('date', function ($data) {
            return $this->transactionUtil->format_date($data->date);
        })->editColumn('schedule', function ($data) {
            $currrentDate = Carbon::now()
                ->timezone($data->business->time_zone)
                ->format('Y-m-d H:i:s');
            $assistances = AssistanceEmployee::where('employee_id', $data->employee_id)
                ->where('date', $data->date)
                ->where('business_id', $data->business_id)
                ->orderBy('id', 'ASC')
                ->get();

            $keyAssitance = count($assistances) - 1;
            if ($assistances[$keyAssitance]->type == 'Entrada') {
                $lastAssistanceEmployee = AssistanceEmployee::where('employee_id', $data->employee_id)
                    ->where('id', '>', $assistances[$keyAssitance]->id)
                    ->where('business_id', $data->business_id)
                    ->orderBy('id', 'ASC')
                    ->first();
                if ($lastAssistanceEmployee) {
                    $assistances->add($lastAssistanceEmployee);
                }
            }

            foreach ($assistances as $key => $assistance) {
                $currentDateAssistanceEmployee = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date . ' ' . $assistance->time);
                if ($assistances[0]->type == 'Entrada') {
                    if ($key === 0) {
                        $firstTime = $currentDateAssistanceEmployee;
                        $lastTime = $currrentDate;
                    }
                }


                if ($assistances[0]->type == 'Salida') {
                    if ($key === 1 && $assistances[1]->type == 'Entrada') {
                        $firstTime = $currentDateAssistanceEmployee;
                        $lastTime = $currrentDate;
                    }
                }

                if ($assistances[count($assistances) - 1]->type == 'Entrada') {
                    $lastTime = $currrentDate;
                } else {
                    $lastTime = $currentDateAssistanceEmployee;
                }
            }
            return $this->transactionUtil->format_date($firstTime, true) . ' - ' . $this->transactionUtil->format_date($lastTime, true);
        })->editColumn('number_of_hours', function ($data) {

            $assistancesEmployeeData = AssistanceEmployee::where('employee_id', $data->employee_id)
                ->where('date', $data->date)
                ->where('business_id', $data->business_id)
                ->orderBy('id', 'ASC')
                ->get();

            $keyAssitance = count($assistancesEmployeeData) - 1;
            if ($assistancesEmployeeData[$keyAssitance]->type == 'Entrada') {
                $lastAssistanceEmployee = AssistanceEmployee::where('employee_id', $data->employee_id)
                    ->where('id', '>', $assistancesEmployeeData[$keyAssitance]->id)
                    ->where('business_id', $data->business_id)
                    ->orderBy('id', 'ASC')
                    ->first();
                if ($lastAssistanceEmployee) {
                    $assistancesEmployeeData->add($lastAssistanceEmployee);
                }
            }

            $seconds = 0;
            $secondsFuera = 0;
            $currrentDate = Carbon::now()->timezone($data->business->time_zone)->format('Y-m-d H:i:s');
            foreach ($assistancesEmployeeData as $key => $assistance) {
                $assistanceCurrentDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date . ' ' . $assistance->time);
                if ($assistancesEmployeeData[0]->type == 'Entrada') {
                    if ($key == 0) {
                        $firstDate = $assistanceCurrentDate;
                    }

                    if ($key > 0) {
                        $assistancePreviousDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key - 1]->date . ' ' . $assistancesEmployeeData[$key - 1]->time);
                        if ($key < count($assistancesEmployeeData) - 1) {
                            //Calcular el tiempo en que la persona estuvo afuera
                            if ($assistancesEmployeeData[$key - 1]->type == 'Salida' && $assistance->type == 'Entrada') {
                                $entradaDate = $assistanceCurrentDate;
                                $salidaDate = $assistancePreviousDate;

                                $secondsEntrada = strtotime($entradaDate);
                                $secondsSalida = strtotime($salidaDate);
                                $secondsFuera += $secondsEntrada - $secondsSalida;
                            }
                        }

                        if ($key == count($assistancesEmployeeData) - 1) {
                            //Calculando el tiempo de la primer entrada y la salida o con la fecha actual 
                            //si no hay registro de la ultima salida
                            if ($assistance->type == 'Salida') { //Obtener la fecha de la ultima salida
                                $lastDate = $assistanceCurrentDate;
                            }

                            if ($assistance->type == 'Entrada') { //Obtener la fecha actual
                                $lastDate = $currrentDate;
                            }

                            $secondsFirst = strtotime($firstDate);
                            $secondsLast = strtotime($lastDate);
                            $seconds = $secondsLast - $secondsFirst;
                            $seconds = $seconds - $secondsFuera;
                        }
                    }
                } else { //Empieza con salida
                    if ($key > 0) { //Entrada
                        if ($key == 1) {
                            $firstDate = $assistanceCurrentDate;
                        }

                        if ($key > 1) {
                            $assistancePreviousDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key - 1]->date . ' ' . $assistancesEmployeeData[$key - 1]->time);

                            if ($key < (count($assistancesEmployeeData) - 1)) {
                                //Calcular el tiempo en que la persona estuvo afuera
                                if ($assistancesEmployeeData[$key - 1]->type == 'Salida' && $assistance->type == 'Entrada') {
                                    $entradaDate = $assistanceCurrentDate;
                                    $salidaDate = $assistancePreviousDate;

                                    $secondsEntrada = strtotime($entradaDate);
                                    $secondsSalida = strtotime($salidaDate);
                                    $secondsFuera += $secondsEntrada - $secondsSalida;
                                }
                            }

                            if ($key == (count($assistancesEmployeeData) - 1)) {
                                //Calculando el tiempo de la primer entrada y la salida o con la fecha actual 
                                //si no hay registro de la ultima salida
                                if ($assistance->type == 'Salida') { //Obtener la fecha de la ultima salida
                                    $lastDate = $assistanceCurrentDate;
                                }

                                if ($assistance->type == 'Entrada') { //Obtener la fecha actual
                                    $lastDate = $currrentDate;
                                }

                                $secondsFirst = strtotime($firstDate);
                                $secondsLast = strtotime($lastDate);
                                $seconds = $secondsLast - $secondsFirst;
                                $seconds = $seconds - $secondsFuera;
                            }
                        }
                    }
                }
            }
            $time_worked = $this->convertSecondsToHours($seconds);

            return $time_worked;
        })->editColumn('status', function ($data) {
            $assistance = AssistanceEmployee::where('employee_id', $data->employee_id)
                ->where('date', $data->date)
                ->where('business_id', $data->business_id)
                ->orderBy('id', 'DESC')
                ->first();

            if ($assistance->type == 'Salida') {
                return $assistance->status;
            } else {
                $lastAssistanceEmployee = AssistanceEmployee::where('employee_id', $data->employee_id)
                    ->where('id', '>', $assistance->id)
                    ->where('business_id', $data->business_id)
                    ->orderBy('id', 'ASC')
                    ->first();
                if ($lastAssistanceEmployee) {
                    return $lastAssistanceEmployee->status;
                } else {
                    return $assistance->status;
                }
            }
        })->toJson();
    }

    public function postAssistancesReport(Request $request)
    {
        if (!auth()->user()->can('rrhh_assistance.generate')) {
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
            if ($request->select_employee == '0') {
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

                foreach ($assistancesEmployee as $data) {
                    $assistancesEmployeeData = AssistanceEmployee::where('employee_id', $data->employee_id)
                        ->where('date', $data->date)
                        ->where('business_id', $business_id)
                        ->orderBy('id', 'ASC')
                        ->get();

                    $keyAssitance = count($assistancesEmployeeData) - 1;
                    if ($assistancesEmployeeData[$keyAssitance]->type == 'Entrada') {
                        $lastAssistanceEmployee = AssistanceEmployee::where('employee_id', $data->employee_id)
                            ->where('id', '>', $assistancesEmployeeData[$keyAssitance]->id)
                            ->where('date', '>', $data->date)
                            ->where('business_id', $business_id)
                            ->orderBy('id', 'ASC')
                            ->first();
                        if ($lastAssistanceEmployee) {
                            $assistancesEmployeeData->add($lastAssistanceEmployee);
                        }
                    }

                    $seconds = 0;
                    $secondsFuera = 0;
                    $currrentDate = Carbon::now()->timezone($data->business->time_zone)->format('Y-m-d H:i:s');
                    $firstTime = $currrentDate;
                    $lastTime = $currrentDate;

                    foreach ($assistancesEmployeeData as $key => $assistance) {
                        $assistanceCurrentDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date . ' ' . $assistance->time);
                        if ($assistancesEmployeeData[0]->type == 'Entrada') {
                            if ($key == 0) {
                                $firstDate = $assistanceCurrentDate;
                            }

                            if ($key > 0) {
                                $assistancePreviousDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key - 1]->date . ' ' . $assistancesEmployeeData[$key - 1]->time);
                                if ($key < count($assistancesEmployeeData) - 1) {
                                    //Calcular el tiempo en que la persona estuvo afuera
                                    if ($assistancesEmployeeData[$key - 1]->type == 'Salida' && $assistance->type == 'Entrada') {
                                        $entradaDate = $assistanceCurrentDate;
                                        $salidaDate = $assistancePreviousDate;

                                        $secondsEntrada = strtotime($entradaDate);
                                        $secondsSalida = strtotime($salidaDate);
                                        $secondsFuera += $secondsEntrada - $secondsSalida;
                                    }
                                }

                                if ($key == count($assistancesEmployeeData) - 1) {
                                    //Calculando el tiempo de la primer entrada y la salida o con la fecha actual 
                                    //si no hay registro de la ultima salida
                                    if ($assistance->type == 'Salida') { //Obtener la fecha de la ultima salida
                                        $lastDate = $assistanceCurrentDate;
                                    }

                                    if ($assistance->type == 'Entrada') { //Obtener la fecha actual
                                        $lastDate = $currrentDate;
                                    }

                                    $secondsFirst = strtotime($firstDate);
                                    $secondsLast = strtotime($lastDate);
                                    $seconds = $secondsLast - $secondsFirst;
                                    $seconds = $seconds - $secondsFuera;
                                }
                            }
                        } else { //Empieza con salida
                            if ($key > 0) { //Entrada
                                if ($key == 1) {
                                    $firstDate = $assistanceCurrentDate;
                                }

                                if ($key > 1) {
                                    $assistancePreviousDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key - 1]->date . ' ' . $assistancesEmployeeData[$key - 1]->time);

                                    if ($key < (count($assistancesEmployeeData) - 1)) {
                                        //Calcular el tiempo en que la persona estuvo afuera
                                        if ($assistancesEmployeeData[$key - 1]->type == 'Salida' && $assistance->type == 'Entrada') {
                                            $entradaDate = $assistanceCurrentDate;
                                            $salidaDate = $assistancePreviousDate;

                                            $secondsEntrada = strtotime($entradaDate);
                                            $secondsSalida = strtotime($salidaDate);
                                            $secondsFuera += $secondsEntrada - $secondsSalida;
                                        }
                                    }

                                    if ($key == (count($assistancesEmployeeData) - 1)) {
                                        //Calculando el tiempo de la primer entrada y la salida o con la fecha actual 
                                        //si no hay registro de la ultima salida
                                        if ($assistance->type == 'Salida') { //Obtener la fecha de la ultima salida
                                            $lastDate = $assistanceCurrentDate;
                                        }

                                        if ($assistance->type == 'Entrada') { //Obtener la fecha actual
                                            $lastDate = $currrentDate;
                                        }

                                        $secondsFirst = strtotime($firstDate);
                                        $secondsLast = strtotime($lastDate);
                                        $seconds = $secondsLast - $secondsFirst;
                                        $seconds = $seconds - $secondsFuera;
                                    }
                                }
                            }
                        }
                    }
                    $time_worked = $this->convertSecondsToHours($seconds);


                    $assistanceSummary[$data->id] = (object) [
                        'employee' => $data->employee->first_name . ' ' . $data->employee->last_name,
                        'start_date' => $this->transactionUtil->format_date($firstTime, true),
                        'end_date' => $this->transactionUtil->format_date($lastTime, true),
                        'time_worked' => $time_worked
                    ];
                }
            } else {
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

                foreach ($assistancesEmployee as $data) {
                    $assistancesEmployeeData = AssistanceEmployee::where('employee_id', $data->employee_id)
                        ->where('date', $data->date)
                        ->where('business_id', $business_id)
                        ->orderBy('id', 'ASC')
                        ->get();

                    $keyAssitance = count($assistancesEmployeeData) - 1;
                    if ($assistancesEmployeeData[$keyAssitance]->type == 'Entrada') {
                        $lastAssistanceEmployee = AssistanceEmployee::where('employee_id', $data->employee_id)
                            ->where('id', '>', $assistancesEmployeeData[$keyAssitance]->id)
                            ->where('date', '>', $data->date)
                            ->where('business_id', $business_id)
                            ->orderBy('id', 'ASC')
                            ->first();
                        if ($lastAssistanceEmployee) {
                            $assistancesEmployeeData->add($lastAssistanceEmployee);
                        }
                    }

                    $seconds = 0;
                    $secondsFuera = 0;
                    $currrentDate = Carbon::now()->timezone($data->business->time_zone)->format('Y-m-d H:i:s');
                    $firstTime = $currrentDate;
                    $lastTime = $currrentDate;

                    foreach ($assistancesEmployeeData as $key => $assistance) {
                        $assistanceCurrentDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistance->date . ' ' . $assistance->time);
                        if ($assistancesEmployeeData[0]->type == 'Entrada') {
                            if ($key == 0) {
                                $firstDate = $assistanceCurrentDate;
                            }

                            if ($key > 0) {
                                $assistancePreviousDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key - 1]->date . ' ' . $assistancesEmployeeData[$key - 1]->time);
                                if ($key < count($assistancesEmployeeData) - 1) {
                                    //Calcular el tiempo en que la persona estuvo afuera
                                    if ($assistancesEmployeeData[$key - 1]->type == 'Salida' && $assistance->type == 'Entrada') {
                                        $entradaDate = $assistanceCurrentDate;
                                        $salidaDate = $assistancePreviousDate;

                                        $secondsEntrada = strtotime($entradaDate);
                                        $secondsSalida = strtotime($salidaDate);
                                        $secondsFuera += $secondsEntrada - $secondsSalida;
                                    }
                                }

                                if ($key == count($assistancesEmployeeData) - 1) {
                                    //Calculando el tiempo de la primer entrada y la salida o con la fecha actual 
                                    //si no hay registro de la ultima salida
                                    if ($assistance->type == 'Salida') { //Obtener la fecha de la ultima salida
                                        $lastDate = $assistanceCurrentDate;
                                    }

                                    if ($assistance->type == 'Entrada') { //Obtener la fecha actual
                                        $lastDate = $currrentDate;
                                    }

                                    $secondsFirst = strtotime($firstDate);
                                    $secondsLast = strtotime($lastDate);
                                    $seconds = $secondsLast - $secondsFirst;
                                    $seconds = $seconds - $secondsFuera;
                                }
                            }
                        } else { //Empieza con salida
                            if ($key > 0) { //Entrada
                                if ($key == 1) {
                                    $firstDate = $assistanceCurrentDate;
                                }

                                if ($key > 1) {
                                    $assistancePreviousDate = Carbon::createFromFormat('Y-m-d H:i:s', $assistancesEmployeeData[$key - 1]->date . ' ' . $assistancesEmployeeData[$key - 1]->time);

                                    if ($key < (count($assistancesEmployeeData) - 1)) {
                                        //Calcular el tiempo en que la persona estuvo afuera
                                        if ($assistancesEmployeeData[$key - 1]->type == 'Salida' && $assistance->type == 'Entrada') {
                                            $entradaDate = $assistanceCurrentDate;
                                            $salidaDate = $assistancePreviousDate;

                                            $secondsEntrada = strtotime($entradaDate);
                                            $secondsSalida = strtotime($salidaDate);
                                            $secondsFuera += $secondsEntrada - $secondsSalida;
                                        }
                                    }

                                    if ($key == (count($assistancesEmployeeData) - 1)) {
                                        //Calculando el tiempo de la primer entrada y la salida o con la fecha actual 
                                        //si no hay registro de la ultima salida
                                        if ($assistance->type == 'Salida') { //Obtener la fecha de la ultima salida
                                            $lastDate = $assistanceCurrentDate;
                                        }

                                        if ($assistance->type == 'Entrada') { //Obtener la fecha actual
                                            $lastDate = $currrentDate;
                                        }

                                        $secondsFirst = strtotime($firstDate);
                                        $secondsLast = strtotime($lastDate);
                                        $seconds = $secondsLast - $secondsFirst;
                                        $seconds = $seconds - $secondsFuera;
                                    }
                                }
                            }
                        }
                    }
                    $time_worked = $this->convertSecondsToHours($seconds);

                    $assistanceSummary[$data->id] = (object) [
                        'employee' => $data->employee->first_name . ' ' . $data->employee->last_name,
                        'start_date' => $this->transactionUtil->format_date($firstTime, true),
                        'end_date' => $this->transactionUtil->format_date($lastTime, true),
                        'time_worked' => $time_worked
                    ];
                }
            }
            if ($request->report_type == 'pdf') {
                $pdf = \PDF::loadView(
                    'rrhh.assistance.report_pdf',
                    compact([
                        'assistances',
                        'business',
                        'assistancesEmployee',
                        'assistanceSummary'
                    ])
                );

                $pdf->setPaper('letter', 'portrait');
                return $pdf->download(__('rrhh.assistance') . '.pdf');
            } else {
                return Excel::download(
                    new AssistanceEmployeeReportExport($assistances, $assistanceSummary, $business, $this->transactionUtil),
                    __('rrhh.employee_assistance_report') . '.xlsx'
                );
            }
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }

    //Show employee assistance detail
    public function show($id)
    {
        if (!auth()->user()->can('rrhh_assistance.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $assistance = AssistanceEmployee::where('id', $id)
            ->where('business_id', $business_id)
            ->first();

        $employee = Employees::findOrFail($assistance->employee_id);
        $assistances = AssistanceEmployee::where('employee_id', $assistance->employee_id)
            ->where('date', $assistance->date)
            ->orderBy('id', 'ASC')
            ->get();

        $keyAssitance = count($assistances) - 1;
        if ($assistances[$keyAssitance]->type == 'Entrada') {
            $lastAssistanceEmployee = AssistanceEmployee::where('employee_id', $assistance->employee_id)
                ->where('id', '>', $assistances[$keyAssitance]->id)
                ->where('business_id', $business_id)
                ->orderBy('id', 'ASC')
                ->first();
            if ($lastAssistanceEmployee) {
                $assistances->add($lastAssistanceEmployee);
            }
        }

        $assistancesIds = [];
        foreach ($assistances as $key => $assistance) {
            $assistancesIds[$key] = (object) [
                'id' => Crypt::encrypt($assistance->id)
            ];
        }

        $routeApi = config('app.assistance_employee_url');

        return view('rrhh.assistance.show', compact('assistances', 'employee', 'assistancesIds', 'routeApi'));
    }


    //Show the employee's photo in a larger way in the assitance detail
    public function viewImage($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $assistance = AssistanceEmployee::where('id', $id)
            ->where('business_id', $business_id)
            ->first();
        $employee = Employees::where('id', $assistance->employee_id)->where('business_id', $business_id)->first();
        $idAssistance = Crypt::encrypt($assistance->id);

        $routeApi = config('app.assistance_employee_url');
        return view('rrhh.assistance.photo', compact('idAssistance', 'routeApi', 'employee'));
    }

    private function convertSecondsToHours($time_in_seconds)
    {
        $days = floor($time_in_seconds / 86400);
        $hours = floor($time_in_seconds / 3600);
        $minutes = floor(($time_in_seconds - ($hours * 3600)) / 60);
        $seconds = $time_in_seconds - ($hours * 3600) - ($minutes * 60);

        if ($hours > 24) {
            $hours = floor($time_in_seconds / 3600) - ($days * 24);
        }

        $numberHour = '';
        if ($hours == 1) {
            $numberHour = $hours . ' hora con';
        }
        if ($hours != 1) {
            $numberHour = $hours . ' horas con';
        }

        $numberMinute = '';
        if ($minutes == 1) {
            $numberMinute = $minutes . ' minuto con';
        }
        if ($minutes != 1) {
            $numberMinute = $minutes . ' minutos con';
        }

        $numberSecond = '';
        if ($hours == 1) {
            $numberSecond = $seconds . ' segundo';
        }
        if ($hours != 1) {
            $numberSecond = $seconds . ' segundos';
        }

        if ($days >= 1) {
            if (variant_int($days) == 1) {
                return variant_int($days) . ' día con ' . $numberHour . ' ' . $numberMinute . ' ' . $numberSecond;
            } else {
                return variant_int($days) . ' días con ' . $numberHour . ' ' . $numberMinute . ' ' . $numberSecond;
            }
        } else {
            return $numberHour . ' ' . $numberMinute . ' ' . $numberSecond;
        }
    }
}