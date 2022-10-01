<?php

namespace App\Http\Controllers\Optics;

use App\BusinessLocation;
use App\Employees;
use App\Optics\LabOrder;
use App\Optics\Patient;
use App\Utils\Util;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\DataTables;

class PatientController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $util;

    /**
     * Constructor
     *
     * @param \App\Utils\Util $util
     * @return void
     */
    public function __construct(Util $util)
    {
        $this->util = $util;
        $this->module_name = 'patient';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('patients.view') && !auth()->user()->can('patients.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        return view('optics.patients.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($patient_name = null)
    {
        if (!auth()->user()->can('patients.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $code = $this->util->generatePatientsCode();
        $sexs = $this->util->Sexs();
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('optics.patients.create')
        ->with(compact('code', 'sexs', 'business_locations', 'patient_name'));
    }

    public function getPatientsData()
    {
        if (!auth()->user()->can('patients.view') && !auth()->user()->can('patients.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $patients = DB::table('patients')
            ->leftJoin('business_locations', 'patients.location_id', 'business_locations.id')
            ->leftJoin('employees', 'patients.employee_id', 'employees.id')
            ->select('patients.id', 'patients.code', 'patients.full_name', 'patients.age', 'business_locations.name as location', DB::raw("CONCAT(COALESCE(employees.first_name,''),' ',COALESCE(employees.last_name,'')) as employee"), 'business_locations.name as location')
            ->where('patients.business_id', $business_id);
        return DataTables::of($patients)
            ->addColumn(
                'action',
                '@can("patients.update")
            <button data-href="{{action(\'Optics\PatientController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_patients_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                &nbsp;
            @endcan
            @can("patients.delete")
                <button data-href="{{action(\'Optics\PatientController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_patients_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
            @endcan'
            )
            ->removeColumn('id')
            ->rawColumns([5])
            ->make(false);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('patients.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                'code', 'full_name', 'age',
                'sex', 'email', 'contacts',
                'address', 'glasses_graduation', 'location_id'
            ]);

            $input['business_id'] = request()->session()->get('user.business_id');
            $input['register_by'] = $request->session()->get('user.id');
            $input['glasses'] = !empty($request->input('chkhas_glasses')) ? 1 : 0;
            $input['notes'] = $request->input('txt-notes');

            $employee_code = $request->input('employee_code');
            if (!empty($employee_code)) {
                $employee = Employees::where('agent_code', $request->input('employee_code'))->first();
                $input['employee_id'] = !empty($employee) ? $employee->id : null;
            }

            $patient = Patient::create($input);

            # Store binnacle
            $user_id = $request->session()->get('user.id');

            $this->util->registerBinnacle($user_id, $this->module_name, 'create', $patient);
    
            $output = ['success' => true,
                'data' => $patient,
                'full_name' => $patient->full_name,
                'pat_id' => $patient->id,
                'msg' => __("patient.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('patients.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $sexs = $this->util->Sexs();
            $business_locations = BusinessLocation::forDropdown($business_id);

            $patient = Patient::where('business_id', $business_id)->find($id);

            $permission = true;

            // Add missing location
            if (!empty($patient->location_id)) {
                $location = BusinessLocation::where('business_id', $business_id)->find($patient->location_id);
    
                if (empty($business_locations[$patient->location_id])) {
                    $business_locations->prepend($location->name, $location->id);
                    $permission = false;
                }
            }

            return view('optics.patients.edit')
                ->with(compact('sexs', 'business_locations', 'patient', 'permission'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('patients.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only([
                    'code', 'full_name', 'age',
                    'sex', 'email', 'contacts',
                    'address', 'glasses_graduation', 'location_id'
                ]);

                $input['glasses'] = !empty($request->input('chkhas_glasses')) ? 1 : 0;
                $input['notes'] = $request->input('txt-notes');

                $employee_code = $request->input('employee_code');
                if (!empty($employee_code)) {
                    $employee = Employees::where('agent_code', $request->input('employee_code'))->first();
                    $input['employee_id'] = !empty($employee) ? $employee->id : null;
                } else {
                    $input['employee_id'] = null;
                }

                $business_id = $request->session()->get('user.business_id');

                $patient = Patient::where('business_id', $business_id)->findOrFail($id);

                # Clone record before action
                $patient_old = clone $patient;

                $patient->fill($input);
                $patient->save();

                # Store binnacle
                $user_id = $request->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'update', $patient_old, $patient);

                $output = [
                    'success' => true,
                    'msg' => __("patient.updated_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('patients.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $patient = Patient::where('business_id', $business_id)->findOrFail($id);

                $lab_orders = LabOrder::where('patient_id', $id)->get();
                $allow_delete = count($lab_orders) > 0 ? false : true;

                if ($allow_delete) {
                    # Clone record before action
                    $patient_old = clone $patient;

                    $patient->delete();

                    # Store binnacle
                    $user_id = request()->session()->get('user.id');

                    $this->util->registerBinnacle($user_id, $this->module_name, 'delete', $patient_old);

                    $output = [
                        'success' => true,
                        'msg' => __('patient.deleted_success')
                    ];

                } else {
                    $output = [
                        'success' => false,
                        'msg' => __('patient.lab_orders_already_exist')
                    ];
                }

            } catch (\Exception $e) {
                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    public function getEmployeeByCode($code) {

        if (request()->ajax()) {
            try {

                $employee = Employees::where('agent_code', $code)->first();

                if (! empty($employee)) {
                    $output = [
                        'success' => true,
                        'emp' => true,
                        'msg' => $employee->first_name . ' ' . $employee->last_name,
                        'user_id' => $employee->user_id,
                        'emp_id' => $employee->id
                    ];
                } else {
                    $output = [
                        'success' => true,
                        'emp' => false,
                        'msg' => __('patient.employee_does_not_exist')
                    ];
                }

            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'emp' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Retrieves list of patients, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getPatients()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $patients = Patient::where('patients.business_id', $business_id);

            if (!empty($term)) {
                $patients->where(function ($query) use ($term) {
                    $query->where('patients.full_name', 'like', '%' . $term . '%');
                });
            }

            $patients = $patients->select(
                'patients.id',
                'patients.full_name as text'
            )
            ->get();

            return json_encode($patients);
        }
    }
}
