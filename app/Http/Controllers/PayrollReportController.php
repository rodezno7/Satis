<?php

namespace App\Http\Controllers;

use App\Business;
use App\Exports\AnnualPayrollSummaryExport;
use Illuminate\Http\Request;
use App\Payroll;
use App\Utils\EmployeeUtil;
use App\Utils\ModuleUtil;
use DB;
use DataTables;
use Excel;

class PayrollReportController extends Controller
{
    protected $moduleUtil;
    protected $employeeUtil;

    /**
     * Constructor
     *
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, EmployeeUtil $employeeUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->employeeUtil = $employeeUtil;
    }

    public function annualSummary(){
        if (! auth()->user()->can('payroll.report-annual-summary')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $year= Payroll::select('id', 'year')->distinct('year')->get();
        $years = $year->unique('year');

        if(request()->ajax()){
            // Set maximum php execution time
            if (request()->get('length') == -1) {
                ini_set('max_execution_time', 0);
            }
            
            // Parameters
            $params = [
                // Filters
                'year' => request()->input('year'),

                // Datatable parameters
                'start_record' => request()->get('start'),
                'page_size' => request()->get('length'),
                'search' => request()->get('search'),
                'order' => request()->get('order')
            ];

            // Payrolls
            $payrolls = collect($this->getAnnualSummaryData($params));

            $datatable = DataTables::of($payrolls['data'])
                ->addColumn('period', function ($row) { 
                    if($row->start_date != null){
                        return $this->moduleUtil->format_date($row->start_date).' - '.$this->moduleUtil->format_date($row->end_date);
                    }else{
                        return 'Fecha de ingreso - '.$this->moduleUtil->format_date($row->end_date);
                    }
                })->editColumn('status', function ($row) {
                    $html = '';
                    if ($row->status == 'Aprobada') {
                        $html = '<span class="badge" style="background: #449D44">' . $row->status . '</span>';
                    }
                    if ($row->status == 'Calculada') {
                        $html = '<span class="badge" style="background: #00A6DC">' . $row->status . '</span>';
                    }
                    if ($row->status == 'Pagada') {
                        $html = '<span class="badge" style="background: #367FA9">' . $row->status . '</span>';
                    }
                    if ($row->status == 'Iniciada') {
                        $html = '<span class="badge">' . $row->status . '</span>';
                    }
                    return $html;             
                })->editColumn('isr', function ($row) {
                    if($row->isr_id != null){
                        return $row->isr;
                    }              
                })->editColumn('payment_period', function ($row) {
                    if($row->payment_period_id != null){
                        return $row->payment_period;
                    }else{
                        return "N/A";
                    }                
                });

            $datatable = $datatable->rawColumns(['payrollType', 'payrollName', 'payment_period', 'period', 'isr', 'status'])
                ->setTotalRecords($payrolls['count'])
                ->setFilteredRecords($payrolls['count'])
                ->skipPaging()
                ->toJson();
            
            return $datatable;
        }

        return view('payroll.report.annual_summary', compact('years'));
    }

    /**
     * Get annual summary data.
     * 
     * @param  array  $params
     * @return array
     */
    public function getAnnualSummaryData($params)
    {
        // Business filter
        $business_id = request()->session()->get('user.business_id');

        // Created by filter
        if (! empty($params['year'])) {
            $year = $params['year'];
        } else {
            $year = 0;
        }

        // Datatable parameters
        $start_record = $params['start_record'];
        $page_size = $params['page_size'];
        $search_array = $params['search'];
        $search = ! is_null($search_array['value']) ? $search_array['value'] : '';
        $order = $params['order'];

        // Count payrolls
        $count = DB::select(
            'CALL count_all_payroll(?, ?, ?)',
            array(
                $business_id,
                $year,
                $search
            )
        );

        // Payrolls
        $parameters = [
            $business_id,
            $year,
            $search,
            $start_record,
            $page_size,
            $order[0]['column'],
            $order[0]['dir']
        ];

        $payrolls = DB::select(
            'CALL get_all_payrolls(?, ?, ?, ?, ?, ?, ?)',
            $parameters
        );
        
        $result = [
            'data' => $payrolls,
            'count' => $count[0]->count
        ];

        return $result;
    }

    public function generateAnnualSummary(Request $request)
    {
        if (! auth()->user()->can('payroll.report-annual-summary')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Business filter
            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();

            if (! empty($request->year)) {
                $year = $request->year;
            } else {
                $year = 0;
            }
            
            if($year != 0){
                $fileName = 'Resumen anual - ' . $year . '.xlsx';

                // Payrolls
                $parameters = [
                    $business_id,
                    $year
                ];

                $summaries = DB::select(
                    'CALL get_annual_payroll_summary_by_employee(?, ?)',
                    $parameters
                );

                return Excel::download(
                    new AnnualPayrollSummaryExport($summaries, $business, $year, $this->moduleUtil),
                    $fileName
                );
            }else{
                $year= Payroll::select('id', 'year')->distinct('year')->get();
                $years = $year->unique('year');

                // Definir el nombre del archivo zip y crear una nueva instancia de ZipArchive
                $zip_file = 'Resumen_anual.zip';
                $zip = new \ZipArchive();

                //Crear archivo zip y abrirlo
                \Storage::disk('local')->put($zip_file,  $zip);
                $zip->open(public_path('uploads/'.$zip_file),\ZipArchive::CREATE);
                    
                // Recorrer el array con un foreach
                foreach ($years as $year) {
                    $fileName = 'Resumen anual - ' . $year->year . '.xlsx';
                    
                    // Payrolls
                    $parameters = [
                        $business_id,
                        $year->year
                    ];

                    $summaries = DB::select(
                        'CALL get_annual_payroll_summary_by_employee(?, ?)',
                        $parameters
                    );

                    // Guardar el archivo excel en el disco local
                    Excel::store(
                        new AnnualPayrollSummaryExport($summaries, $business, $year->year, $this->moduleUtil), $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX
                    );

                    // Añadir el archivo excel al archivo zip
                    $zip->addFile(public_path('uploads/'.$fileName), $fileName);                
                }

                // Cerrar el archivo zip
                $zip->close();

                // Devolver el archivo zip para descargarlo
                $response = response()->download(public_path('uploads/'.$zip_file));

                // Retornar la respuesta con el archivo zip
                return $response;
            }

            
        
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('rrhh.error')
            ];
        }

    }
}
