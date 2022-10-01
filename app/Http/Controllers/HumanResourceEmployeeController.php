<?php

namespace App\Http\Controllers;

use App\HumanResourceEmployee;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Carbon\Carbon;
use Storage;

class HumanResourceEmployeeController extends Controller
{
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
    public function store(Request $request) {

        if ( !auth()->user()->can('rrhh_overall_payroll.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'                  => 'required',
            'last_name'             => 'required',
            'gender'                => 'required',
            'birthdate'             => 'required|date',
            'dni'                   => 'required|regex:/^\d{8}-\d$/',
            'tax_number'            => 'required|regex:/^\d{4}-\d{6}-\d{3}-\d$/',
            'nationality_id'        => 'required',
            'civil_status_id'       => 'required',
            //'phone'                 => 'required|regex:/^\d{4}-\d{4}$/',
            //'whatsapp'              => 'required|regex:/^\d{4}-\d{4}$/',
            //'email'                 => 'required|email',
            'address'               => 'required',
            //'date_admission'        => 'required',
            //'salary'                => 'required|numeric',
            //'department_id'         => 'required',
            //'position_id'           => 'required',
            //'afp_id'                => 'required',
            //'afp_number'            => 'required|numeric',
            //'social_security_number'=> 'required|numeric',
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
        return $output;
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
            
            $route = 'rrhh_photos/blank.jpg';
        } else {

            $route = 'flags/'.$employee->photo;

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
            'tax_number'            => 'required|regex:/^\d{4}-\d{6}-\d{3}-\d$/',
            'nationality_id'        => 'required',
            'civil_status_id'       => 'required',
            //'phone'                 => 'required|regex:/^\d{4}-\d{4}$/',
            //'whatsapp'              => 'required|regex:/^\d{4}-\d{4}$/',
            //'email'                 => 'required|email',
            'address'               => 'required',
            //'date_admission'        => 'required',
            //'salary'                => 'required|numeric',
            //'department_id'         => 'required',
            //'position_id'           => 'required',
            //'afp_id'                => 'required',
            //'afp_number'            => 'required|numeric',
            //'social_security_number'=> 'required|numeric',
        ]);

        try {

            $input_details = $request->all();
            if ($request->input('status')) {
                $input_details['status'] = 1;
            } else {
                $input_details['status'] = 0;
            }

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
        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HumanResourceEmployee  $humanResourceEmployee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

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

    public function getData() {

        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }

        return view('rrhh.employees.data');
    }

    public function getEmployees() {

        if ( !auth()->user()->can('rrhh_overall_payroll.view') ) {
            abort(403, 'Unauthorized action.');
        }
        


        $employees = DB::table('human_resource_employees as e')
        ->select('e.id', 'e.code', 'e.name', 'e.status', DB::raw("CONCAT(e.name, ' ', e.last_name) as full_name"));
        
        return DataTables::of($employees)->filterColumn('full_name', function($query, $keyword) {
            $sql = "CONCAT(e.name, ' ', e.last_name)  like ?";
            $query->whereRaw($sql, ["%{$keyword}%"]);
        })->toJson();
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
                    Storage::disk('flags')->put($name,  \File::get($file));
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
            
            $route = 'rrhh_photos/blank.jpg';
        } else {

            $route = 'flags/'.$employee->photo;

        }

        return view('rrhh.employees.photo', compact('route'));

    }
}
