<?php

namespace App\Http\Controllers;

use App\HumanResourceEmployee;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;
use Illuminate\Validation\Rule;
use App\Utils\ProductUtil;

class HumanResourceEmployeeController extends Controller
{
    protected $productUtil;

    /**
     * Constructor
     *
     * @param ProductUtil $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }

        return view('rrhh.employees.index');
    }

    public function getEmployees() 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $employees = DB::table('human_resource_employees as e')
        ->select('e.id', 'e.code', 'e.name', 'e.phone', 'e.email', 'e.status', DB::raw("CONCAT(e.name, ' ', e.last_name) as full_name"))
        ->where('business_id', $business_id);
        
        return DataTables::of($employees)->filterColumn('full_name', function($query, $keyword) {
            $sql = "CONCAT(e.name, ' ', e.last_name)  like ?";
            $query->whereRaw($sql, ["%{$keyword}%"]);
        })->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        if ( !auth()->user()->can('rrhh_overall_payroll.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $nationalities = DB::table('human_resources_datas')->where('human_resources_header_id', 6)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $civil_statuses = DB::table('human_resources_datas')->where('human_resources_header_id', 1)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $professions = DB::table('human_resources_datas')->where('human_resources_header_id', 7)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');

        $departments = DB::table('human_resources_datas')->where('human_resources_header_id', 2)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $positions = DB::table('human_resources_datas')->where('human_resources_header_id', 3)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $afps = DB::table('human_resources_datas')->where('human_resources_header_id', 4)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $types = DB::table('human_resources_datas')->where('human_resources_header_id', 5)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $banks = DB::table('human_resource_banks')->orderBy('name', 'ASC')->pluck('name', 'id');

        $countries = DB::table('countries')->pluck('name', 'id');
        
        return view('rrhh.employees.create', compact(
            'nationalities',
            'civil_statuses',
            'countries', 
            'professions',
            'departments',
            'positions',
            'afps',
            'types',
            'banks'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        if ( !auth()->user()->can('rrhh_overall_payroll.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'                  => 'required',
            'last_name'             => 'required',
            'gender'                => 'required',
            'birthdate'             => 'required|date',
            'dni'                   => 'required',
            'tax_number'            => 'required',
            'address'               => 'required',
            'email'                 => 'required|email',
            'date_admission'        => 'nullable|date',
            'nationality_id'        => 'required', 
            'civil_status_id'       => 'required', 
            'department_id'         => 'nullable',
            'position_id'           => 'nullable', 
        ]);

        try {
            $input_details = $request->all();
            $date_admission = Carbon::parse($request->input('date_admission'));
            $mdate = $date_admission->month;
            $ydate = $date_admission->year;
            $last_correlative = DB::table('human_resource_employees')
            ->select(DB::raw('MAX(id) as max'))
            ->first();

            if ($last_correlative->max != null) {
                $correlative = $last_correlative->max + 1;

            } else {
                $correlative = 1;
            }

            $input_details['code'] = 'E'.$mdate.$ydate.str_pad($correlative, 3, '0', STR_PAD_LEFT);
            $input_details['photo'] = $this->productUtil->uploadFile($request, 'photo', config('constants.product_img_path'));
            $input_details['created_by'] = $request->session()->get('user.id');
            $input_details['business_id'] = $request->session()->get('user.business_id');
            $employee = HumanResourceEmployee::create($input_details);

            $output = [
                'success' => 1,
                'id' => $employee->id,
                'msg' => __('rrhh.added_successfully')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('rrhh.error')
            ];
        }

        if ($request->input('submit_type') == 'complete') {
            return redirect()->action('HumanResourceEmployeeController@edit', [$employee->id]);
        } else if ($request->input('submit_type') == 'other') {
            return redirect()->action('HumanResourceEmployeeController@create')->with('status', $output);
        } else {
            return redirect('rrhh-employees')->with('status', $output);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HumanResourceEmployee  $humanResourceEmployee
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $employee = HumanResourceEmployee::where('id', $id)->with(
            'afp',
            'civilStatus',
            'department',
            'nationality',
            'position',
            'profession',
            'type',
            'bank',
            'city',
            'state'
        )
        ->first();

        if ($employee->photo == '') {
            
            $route = 'uploads/img/defualt.png';
        } else {

            $route = 'uploads/img/'.$employee->photo;

        }

        return view('rrhh.employees.show', compact('employee', 'route'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HumanResourceEmployee  $humanResourceEmployee
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        if ( !auth()->user()->can('rrhh_overall_payroll.update') ) {
            abort(403, 'Unauthorized action.');
        }

        $employee = HumanResourceEmployee::findOrFail($id);

        $nationalities = DB::table('human_resources_datas')->where('human_resources_header_id', 6)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $civil_statuses = DB::table('human_resources_datas')->where('human_resources_header_id', 1)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $professions = DB::table('human_resources_datas')->where('human_resources_header_id', 7)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');

        $departments = DB::table('human_resources_datas')->where('human_resources_header_id', 2)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $positions = DB::table('human_resources_datas')->where('human_resources_header_id', 3)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $afps = DB::table('human_resources_datas')->where('human_resources_header_id', 4)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $types = DB::table('human_resources_datas')->where('human_resources_header_id', 5)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');

        $payments = DB::table('human_resources_datas')->where('human_resources_header_id', 8)->where('status', 1)->orderBy('value', 'ASC')->pluck('value', 'id');
        $banks = DB::table('human_resource_banks')->orderBy('name', 'ASC')->pluck('name', 'id');
        $countries = DB::table('countries')->pluck('name', 'id');
        $states = DB::table('states')->where('country_id', $employee->country_id)->pluck('name', 'id');
        $cities = DB::table('cities')->where('state_id', $employee->state_id)->pluck('name', 'id');
        
        return view('rrhh.employees.edit', compact(
            'employee',
            'nationalities',
            'civil_statuses',
            'states', 
            'professions',
            'departments',
            'positions',
            'afps',
            'types',
            'banks',
            'cities',
            'payments',
            'countries'
        ));        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HumanResourceEmployee  $humanResourceEmployee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if ( !auth()->user()->can('rrhh_overall_payroll.update') ) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name'                  => 'required',
            'last_name'             => 'required',
            'gender'                => 'required',
            'birthdate'             => 'required|date',
            'dni'                   => 'required|regex:/^\d{8}-\d$/',
            'tax_number'            => 'nullable|regex:/^\d{4}-\d{6}-\d{3}-\d$/',
            'address'               => 'required',
            'email'                 => 'required|email',
            'date_admission'        => 'nullable|date',
            'nationality_id'        => 'required', 
            'civil_status_id'       => 'required', 
            'department_id'         => 'nullable',
            'position_id'           => 'nullable', 
            'salary'                => 'nullable|numeric',
            'afp_number'            => 'nullable|integer',
            'social_security_number'=> 'nullable|integer',
        ]);

        try {
            $input_details = $request->all();
            if ($request->input('status')) {
                $input_details['status'] = 1;
            } else {
                $input_details['status'] = 0;
            }

            $input_details['photo'] = $this->productUtil->uploadFile($request, 'photo', config('constants.product_img_path'));
            
            // $file_name = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'));
            // if (!empty($file_name)) {
            //     $input_details['photo'] = $file_name;
            // }
            
            $employee = HumanResourceEmployee::findOrFail($id);
            $employee->update($input_details);


            $output = [
                'success' => 1,
                'msg' => __('rrhh.updated_successfully')
            ];


        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('rrhh.error')
            ];
        }
        return redirect('rrhh-employees')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HumanResourceEmployee  $humanResourceEmployee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) 
    {
        if (!auth()->user()->can('rrhh_overall_payroll.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $item = HumanResourceEmployee::findOrFail($id);

                $item->forceDelete();
                $output = [
                    'success' => true,
                    'msg' => __('rrhh.deleted_successfully')
                ];
            }
            catch (\Exception $e){
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __('rrhh.error')
                ];
            }
            return $output;
        }
    }


    public function verifiedIfExistsDocument($type, $value, $id = null)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            //verifica si hay registtos en la base de datos
            if ($type == 'dni') {
                if(is_null($id)){
                    $employee = HumanResourceEmployee::where('dni', $value)->where('business_id', $business_id)->exists();
                }else{
                    $employee = HumanResourceEmployee::where('id', '<>', $id)->where('dni', $value)->where('business_id', $business_id)->exists();
                }
                if ($employee) {
                    $output = [
                        'success' => true,
                        'msg' => trans('customer.DNI_invalid')
                    ];
                    return  $output;
                } else {
                    $output = [
                        'success' => false,
                        'msg' => trans('customer.DNI_valid')
                    ];
                    return  $output;
                }
            } else if ($type == 'tax_number') {
                if(is_null($id)){
                    $employee = HumanResourceEmployee::where('tax_number', $value)->where('business_id', $business_id)->exists();
                }else{
                    $employee = HumanResourceEmployee::where('id', '<>', $id)->where('tax_number', $value)->where('business_id', $business_id)->exists();
                }
                if ($employee) {
                    $output2 = [
                        'success' => true,
                        'msg' => trans('customer.validate_tax_number_error')
                    ];
                    return  $output2;
                } else {
                    $output2 = [
                        'success' => false,
                        'msg' => trans('customer.validate_tax_number_success'),
                    ];
                    return  $output2;
                }
            }
        }
    }

    public function uploadPhoto(Request $request) {

        if (!auth()->user()->can('rrhh_overall_payroll.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            try {

                DB::beginTransaction();

                if ($request->hasFile('img')) {
                    $file = $request->file('img');
                    $name = time().$file->getClientOriginalName();
                    Storage::disk('uploads/img')->put($name,  \File::get($file));
                    $input_details['photo'] = $name;
                }

                $employee = HumanResourceEmployee::findOrFail($request->input('employee_id'));
                $employee->update($input_details);

                DB::commit();
                
                $output = [
                    'success' => true,
                    'msg' => __('rrhh.upload_successfully')
                ];


            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __('rrhh.error')
                ];
            }

            return $output;
        }
    }

    public function getPhoto($id) {

        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $employee = HumanResourceEmployee::findOrFail($id);
        if ($employee->photo == '') {
            $route = 'uploads/img/defualt.png';
        } else {
            $route = 'uploads/img/'.$employee->photo;
        }

        return view('rrhh.employees.photo', compact('route'));
    }
}
