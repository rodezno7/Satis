<?php

namespace App\Http\Controllers;

use App\Bank;
use App\State;
use App\City;
use App\Country;
use App\RrhhData;
use App\RrhhPositionHistory;
use App\RrhhSalaryHistory;
use App\RrhhTypeWage;
use App\Employees;
use Illuminate\Http\Request;
use App\Utils\EmployeeUtil;
use App\Utils\ModuleUtil;
use Excel;
use DB;
use Carbon\Carbon;

class RrhhImportEmployeesController extends Controller
{

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
    
    public function create()
    {
        if (!auth()->user()->can('rrhh_import_employees.create')) {
            abort(403, 'Unauthorized action.');
        }
    
        return view('rrhh.import_employees.create');
    }

    /**
     * Check file to importer.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkFile(Request $request)
    {
        if (! auth()->user()->can('rrhh_import_employees.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            // Errors list
            $error_msg = [];

            // Employee lines
            $employees = [];

            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');
            $exception = 0;

            if ($request->hasFile('employees_xlsx')) {
                $file = $request->file('employees_xlsx');

                /**
                 * ------------------------------------------------------------
                 * EMPLOYEE SHEET
                 * ------------------------------------------------------------
                 */

                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[1];

                // Removing the header
                unset($imported_data[0]);
                unset($imported_data[1]);
                unset($imported_data[2]);
                unset($imported_data[3]);
                unset($imported_data[4]);

                // Columns number
                $col_no = 29;

                // Process file
                foreach ($imported_data as $key => $value) {
                    // Check columns number
                    if (count($value) != $col_no) {
                        $error_line = [
                            'row' => 'N/A',
                            'sheet' => __('rrhh.employees'),
                            'msg' => __('product.number_of_columns_mismatch', ['number' => $col_no - 1])
                        ];

                        array_push($error_msg, $error_line);
                    }

                    // Row number
                    $row_no = $key + 1;

                    // Row
                    $row = [
                        'first_name' => trim($value[0]),
                        'last_name' => trim($value[1]),
                        'gender' => trim($value[2]),
                        'nationality' => trim($value[3]),
                        'birth_date' => trim($value[4]),
                        'dni' => trim($value[5]),
                        'tax_number' => trim($value[6]),
                        'civil_status' => trim($value[7]),
                        'phone' => trim($value[8]),
                        'mobile' => trim($value[9]),
                        'email' => trim($value[10]),
                        'institutional_email' => trim($value[11]),
                        'address' => trim($value[12]),
                        'country' => trim($value[13]),
                        'state' => trim($value[14]),
                        'city' => trim($value[15]),
                        'social_security_number' => trim($value[16]),
                        'afp' => trim($value[17]),
                        'afp_number' => trim($value[18]),
                        'date_admission' => trim($value[19]),
                        'department' => trim($value[20]),
                        'position' => trim($value[21]),
                        'type' => trim($value[22]),
                        'salary' => trim($value[23]),
                        'profession' => trim($value[24]),
                        'payment' => trim($value[25]),
                        'bank' => trim($value[26]),
                        'bank_acount' => trim($value[27]),
                    ];

                    $result = $this->checkRow($row, $row_no);

                    // Employee result
                    array_push($employees, $result['employees']);

                    // Error messages result
                    foreach ($result['error_msg'] as $item) {
                        $item['sheet'] = __('rrhh.employees');
                        array_push($error_msg, $item);
                    }
                }
            }

            $status = [
                'success' => 1,
                'msg' => __('customer.successful_verified_file')
            ];

        } catch (\Exception $e) {
            $exception = 1;

            $error_line = [
                'row' => 'N/A',
                'msg' => $e->getMessage()
            ];

            array_push($error_msg, $error_line);

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $status = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        // Session variables 
        session(['employees' => $employees]);

        $errors = $error_msg;

        if (count($error_msg) == 0 && $exception == 0) {
            //Archivo validado
            $flag = true;
        } else {
            //Errores en al check columns
            $flag = false;
        }

        return view('rrhh.import_employees.create')
            ->with(compact(
                'errors',
                'status',
                'flag',
                'exception'
            ));

        return redirect('rrhh-import-employees')->with('status', $status);
    }

    /**
     * Check row data.
     * 
     * @param  array  $row
     * @param  int  $row_no
     * @param  array  $default_data
     * @return array
     */
    public function checkRow($row, $row_no)
    {
        $employee = [
            'first_name' => null,
            'last_name' => null,
            'gender' => null,
            'nationality_id' => null,
            'birth_date' => null,
            'dni' => null,
            'approved' => null,
            'tax_number' => null,
            'civil_status_id' => null,
            'phone' => null,
            'mobile' => null,
            'email' => null,
            'institutional_email' => null,
            'address' => null,
            'country_id' => null,
            'state_id' => null,
            'city_id' => null,
            'social_security_number' => null,
            'afp_id' => null,
            'afp_number' => null,
            'date_admission' => null,
            'department_id' => null,
            'position_id' => null,
            'type_id' => null,
            'salary' => null,
            'profession_id' => null,
            'payment_id' => null,
            'bank_id' => null,
            'bank_acount' => null,
        ];

        // Errors list
        $error_msg = [];

        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');

        // ---------- FIRST NAME ----------
        // Check empty
        if (empty($row['first_name'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.first_name_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['first_name']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.first_name_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                $employee['first_name'] = $row['first_name'];
            }
        }


        // ---------- LAST NAME ----------
        // Check empty
        if (empty($row['last_name'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.last_name_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['last_name']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.last_name_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                $employee['last_name'] = $row['last_name'];
            }
        }


        // ---------- GENDER ----------
        // Check empty
        if (empty($row['gender'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.gender_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            $gender = mb_strtolower($row['gender']);

            // Check invalid value
            if (in_array($gender, ['f', 'F', 'm', 'M'])) {
                if (in_array($gender, ['f', 'F'])) {
                    $employee['gender'] = 'F';
                } else if (in_array($gender, ['m', 'M'])) {
                    $employee['gender'] = 'M';
                } 
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.gender_invalid')
                ];

                array_push($error_msg, $error_line);
            }
        }


        // ---------- NATIONALITY ----------
        // Check empty
        if (empty($row['nationality'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.nationality_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['nationality']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.nationality_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $nationality = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 6)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['nationality']]);
                    })->first();

                if (empty($nationality)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.nationality_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['nationality'] = $nationality->id;
                }
            }   
        }


        // ---------- BIRTH DATE ----------
        // Check empty
        if (empty($row['birth_date'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.birth_date_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format date
            $row['birth_date'] = str_replace("-", "/", $row['birth_date']);
            if (date('d/m/Y', strtotime($row['birth_date'])) != $row['birth_date'] || strlen($row['birth_date']) > 11) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.birth_date_valid')
                ];

                array_push($error_msg, $error_line);
            }else{
                $employee['birth_date'] = $this->moduleUtil->uf_date($row['birth_date']);
            }
        }


        // ---------- DNI ----------
        // Check empty
        if (empty($row['dni'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.dni_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format dni
            $formatDni = "/^\d{8}-\d$/";
            if(!preg_match($formatDni, $row['dni'])){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.dni_valid')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                // Check unique
                $is_exist = Employees::where('business_id', $business_id)
                ->where('dni', [$row['dni']])
                ->exists();

                if ($is_exist) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.dni_unique')
                    ];

                    array_push($error_msg, $error_line);
                }else{
                    $employee['dni'] = $row['dni'];
                }
            }
        }


        // ---------- TAX NUMBER ----------
        // Check empty
        if (empty($row['tax_number'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.tax_number_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format tax number
            if(strlen($row['tax_number']) > 18){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.tax_number_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $format = "/^[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9]$/";
                if (!preg_match($format, $row['tax_number'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.tax_number_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['tax_number'] = $row['tax_number'];
                }
            }
        }


        // ---------- CIVIL STATUS ----------
        // Check empty
        if (empty($row['civil_status'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.civil_status_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['civil_status']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.civil_status_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $civil_status = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 1)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['civil_status']]);
                    })->first();

                if (empty($civil_status)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.civil_status_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else{
                    $employee['civil_status_id'] = $civil_status->id;
                }
            }
        }
      

        // ---------- PHONE ----------
        // Check is not empty
        if (!empty($row['phone'])) {
            // Check format tax number
            if(strlen($row['phone']) > 9){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.phone_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $format = "/^[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9]$/";
                if (!preg_match($format, $row['phone'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.phone_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['phone'] = $row['phone'];
                }
            }   
        }


        // ---------- MOBILE ----------
        // Check is not empty
        if (!empty($row['mobile'])) {
            // Check format tax number
            if(strlen($row['mobile']) > 9){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.mobile_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $format = "/^[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9]$/";
                if (!preg_match($format, $row['mobile'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.mobile_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['mobile'] = $row['mobile'];
                }
            }   
        }
        

        // ---------- EMAIL ----------
        // Check empty
        if (empty($row['email'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.email_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format tax number
            if(strlen($row['email']) > 191){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.email_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $employee['email'] = $row['email'];
            }
        }


        // ---------- INSTITUTIONAL EMAIL ----------
        // Check is not empty
        if (!empty($row['institutional_email'])) {
            // Check length
            if(strlen($row['institutional_email']) > 191){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.institutional_email_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $employee['institutional_email'] = $row['institutional_email'];
            }
        }


        // ---------- ADDRESS ----------
        // Check empty
        if (empty($row['address'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.address_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if(strlen($row['address']) > 191){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.address_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $employee['address'] = $row['address'];
            }
        }


        // ---------- COUNTRY ----------
        // Check is not empty
        if (!empty($row['country'])) {
            // Check length
            if (strlen($row['country']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.country_length')
                ];

                array_push($error_msg, $error_line);
            }else{

                // Check exist
                $country = Country::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(name) = UPPER(?)', [$row['country']]);
                    })->first();

                if (empty($country)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.country_exist')
                    ];

                    array_push($error_msg, $error_line);

                    $country_error = true;
                }else {
                    $employee['country_id'] = $country->id;
                }
            }
        }


        // ---------- STATE ----------
        // Check is not empty
        if (!empty($row['state'])) {
            // Check length
            if (strlen($row['state']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.state_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $state = State::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(name) = UPPER(?)', [$row['state']]);
                    })->first();

                if (empty($state)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.state_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['state_id'] = $state->id;
                }
            }
        }


        // ---------- CITY ----------
        // Check is not empty
        if (!empty($row['city'])) {
            // Check length
            if (strlen($row['city']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.city_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $city = City::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(name) = UPPER(?)', [$row['city']]);
                    })->first();

                if (empty($city)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.city_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['city_id'] = $city->id;
                }
            }
        }


        // ---------- ISSS ----------
        // Check is not empty
        if (!empty($row['isss'])) {
            // Check length
            if(strlen($row['isss']) > 21){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.isss_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                //Check format
                $format = "/^[0-9]+$/";
                if (!preg_match($format, $row['isss'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.isss_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['social_security_number'] = $row['isss'];
                }
            }

        }


        // ---------- AFP ----------
        // Check is not empty
        if (!empty($row['afp'])) {
            // Check length
            if (strlen($row['afp']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.afp_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $afp = RrhhData::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['afp']]);
                    })->first();

                if (empty($afp)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.afp_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['afp_id'] = $afp->id;
                }
            }            
        }


        // ---------- AFP NUMBER ----------
        // Check is not empty
        if (!empty($row['afp_number'])) {
            // Check length
            if(strlen($row['afp_number']) > 26){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.afp_number_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                //Check format
                $format = "/^[0-9]+$/";
                if (!preg_match($format, $row['afp_number'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.afp_number_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['afp_number'] = $row['afp_number'];
                }
            }
        }


        // ---------- DATE ADMISSION ----------
        // Check empty
        if (empty($row['date_admission'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.date_admission_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format date
            $row['date_admission'] = str_replace("-", "/", $row['date_admission']);
            if (date('d/m/Y', strtotime($row['date_admission'])) != $row['date_admission'] || strlen($row['date_admission']) > 11) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.date_admission_valid')
                ];

                array_push($error_msg, $error_line);
            }else{
                $employee['date_admission'] = $this->moduleUtil->uf_date($row['date_admission']);
            }
        }


        // ---------- DEPARTMENT ----------
        // Check empty
        if (empty($row['department'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.department_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['department']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.department_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $department = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 2)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['department']]);
                    })->first();

                if (empty($department)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.department_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['department_id'] = $department->id;
                }
            }
        }


        // ---------- POSITION ----------
        // Check empty
        if (empty($row['position'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.position_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['position']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.position_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $position = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 3)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['position']]);
                    })->first();

                if (empty($position)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.position_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['position_id'] = $position->id;
                }
            }
        }


        // ---------- TYPE ----------
        // Check is not empty
        if (!empty($row['type'])) {
            // Check length
            if (strlen($row['type']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.type_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $type = RrhhTypeWage::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(name) = UPPER(?)', [$row['type']]);
                    })->first();

                if (empty($type)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.type_exist')
                    ];

                    array_push($error_msg, $error_line);

                    $type_error = true;
                }else {
                    $employee['type_id'] = $type->id;
                }
            } 
        }


        // ---------- SALARY ----------
        // Check empty
        if (empty($row['salary'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.salary_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check is numeric
            if (! is_numeric($row['salary'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.salary_numeric')
                ];
    
                array_push($error_msg, $error_line);
            } else {
                // Check zero
                if ($row['salary'] < 0) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.salary_zero')
                    ];
        
                    array_push($error_msg, $error_line);

                } else {
                    $employee['salary'] = $this->employeeUtil->num_uf($row['salary']);
                }
            }
        }


        // ---------- PROFESSION ----------
        // Check is not empty
        if (!empty($row['profession'])) {
            // Check length
            if (strlen($row['profession']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.profession_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $profession = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 7)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['profession']]);
                    })->first();

                if (empty($profession)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.profession_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['profession_id'] = $profession->id;
                }
            }
        }


        // ---------- PAYMENT ----------
        // Check empty
        if (empty($row['payment'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.payment_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['payment']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.payment_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $payment = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 8)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['payment']]);
                    })->first();

                if (empty($payment)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.payment_exist')
                    ];

                    array_push($error_msg, $error_line);

                    $payment_error = true;
                }else {
                    $employee['payment_id'] = $payment->id;
                }
            } 
        }


        // ---------- BANK ----------
        // Check empty
        if (empty($row['bank'])) {
            if(mb_strtolower($row['payment']) == mb_strtolower('Transferencia bancaria')){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.bank_empty')
                ];
    
                array_push($error_msg, $error_line);
            }
        }else{
            // Check length
            if (strlen($row['bank']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.bank_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $bank = Bank::where('business_id', $business_id)
                ->where(function ($query) use ($row) {
                    $query->whereRaw('UPPER(name) = UPPER(?)', [$row['bank']]);
                })->first();

                if (empty($bank)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.bank_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else{
                    $employee['bank_id'] = $bank->id;
                }
            }
        }
       
        
        // ---------- BANK ACCOUNT ----------
        // Check empty
        if (empty($row['bank_account'])) {
            if(mb_strtolower($row['payment']) == mb_strtolower('Transferencia bancaria')){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.bank_account_empty')
                ];
    
                array_push($error_msg, $error_line);
            }
        } else {
            // Check length
            if(strlen($row['bank_account']) > 191){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.bank_account_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                $format = "/^[0-9]+$/";
                if (!preg_match($format, $row['bank_account'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.bank_account_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['bank_account'] = $row['bank_account'];
                }
            }
        }


        // ----- BUSINESS ID -----
        $employee['business_id'] = $business_id;

        // ----- CREATED BY -----
        $employee['created_by'] = $user_id;


        $result = [
            'employees' => $employee,
            'error_msg' => $error_msg,
        ];

        return $result;
    }

    /**
     * Imports the uploaded file to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            // Session variables
            $employees = session('employees');

            DB::beginTransaction();

            if (! empty($employees)) {
                foreach ($employees as $data) {
                    
                    $new_employee = [
                        'agent_code' => $this->employeeUtil->generateCorrelative($data['date_admission'], $data['business_id']),
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'gender' => $data['gender'],
                        'nationality_id' => $data['nationality_id'],
                        'birth_date' => $data['birth_date'],
                        'dni' => $data['dni'],
                        'approved' => ($data['dni'] == $data['tax_number'])? 1 : 0,
                        'tax_number' => $data['tax_number'],
                        'civil_status_id' => $data['civil_status_id'],
                        'phone' => $data['phone'],
                        'mobile' => $data['mobile'],
                        'email' => $data['email'],
                        'institutional_email' => $data['institutional_email'],
                        'address' => $data['address'],
                        'country_id' => $data['country_id'],
                        'state_id' => $data['state_id'],
                        'city_id' => $data['city_id'],
                        'social_security_number' => $data['social_security_number'],
                        'afp_id' => $data['afp_id'],
                        'afp_number' => $data['afp_number'],
                        'date_admission' => $data['date_admission'],
                        'type_id' => $data['type_id'],
                        'profession_id' => $data['profession_id'],
                        'payment_id' => $data['payment_id'],
                        'bank_id' => $data['bank_id'],
                        'bank_acount' => $data['bank_acount'],
                        'business_id' => $data['business_id'],
                        'created_by' => $data['created_by'],
                    ];

                    //Create new employee
                    $employee = Employees::create($new_employee);

                    RrhhPositionHistory::insert([
                        'new_department_id' => $data['department_id'], 
                        'new_position1_id' => $data['position_id'], 
                        'employee_id' => $employee->id, 
                        'current' => 1
                    ]);
        
                    RrhhSalaryHistory::insert([
                        'employee_id' => $employee->id, 
                        'new_salary' => $data['salary'], 
                        'current' => 1
                    ]);
                }
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('product.file_imported_successfully')
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('rrhh-import-employees')->with('status', $output);
    }

    /**
     * Display edit employees screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        if (! auth()->user()->can('rrhh_import_employees.update')) {
            abort(403, 'Unauthorized action.');
        }
  
        return view('rrhh.import_employees.edit');
    }


    /**
     * Check file to importer.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkEditFile(Request $request)
    {
        if (! auth()->user()->can('rrhh_import_employees.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            // Errors list
            $error_msg = [];

            // Employee lines
            $employees = [];

            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');
            $exception = 0;

            if ($request->hasFile('employees_xlsx')) {
                $file = $request->file('employees_xlsx');

                /**
                 * ------------------------------------------------------------
                 * EMPLOYEE SHEET
                 * ------------------------------------------------------------
                 */

                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[1];

                // Removing the header
                unset($imported_data[0]);
                unset($imported_data[1]);
                unset($imported_data[2]);
                unset($imported_data[3]);
                unset($imported_data[4]);

                // Columns number
                $col_no = 29;

                // Process file
                foreach ($imported_data as $key => $value) {
                    // Check columns number
                    if (count($value) != $col_no) {
                        $error_line = [
                            'row' => 'N/A',
                            'sheet' => __('rrhh.employees'),
                            'msg' => __('product.number_of_columns_mismatch', ['number' => $col_no - 1])
                        ];

                        array_push($error_msg, $error_line);
                    }

                    // Row number
                    $row_no = $key + 1;

                    // Row
                    $row = [
                        'first_name' => trim($value[0]),
                        'last_name' => trim($value[1]),
                        'gender' => trim($value[2]),
                        'nationality' => trim($value[3]),
                        'birth_date' => trim($value[4]),
                        'dni' => trim($value[5]),
                        'tax_number' => trim($value[6]),
                        'civil_status' => trim($value[7]),
                        'phone' => trim($value[8]),
                        'mobile' => trim($value[9]),
                        'email' => trim($value[10]),
                        'institutional_email' => trim($value[11]),
                        'address' => trim($value[12]),
                        'country' => trim($value[13]),
                        'state' => trim($value[14]),
                        'city' => trim($value[15]),
                        'social_security_number' => trim($value[16]),
                        'afp' => trim($value[17]),
                        'afp_number' => trim($value[18]),
                        'date_admission' => trim($value[19]),
                        'department' => trim($value[20]),
                        'position' => trim($value[21]),
                        'type' => trim($value[22]),
                        'salary' => trim($value[23]),
                        'profession' => trim($value[24]),
                        'payment' => trim($value[25]),
                        'bank' => trim($value[26]),
                        'bank_acount' => trim($value[27]),
                    ];

                    $result = $this->checkEditRow($row, $row_no);

                    // Employee result
                    array_push($employees, $result['employees']);

                    // Error messages result
                    foreach ($result['error_msg'] as $item) {
                        $item['sheet'] = __('rrhh.employees');
                        array_push($error_msg, $item);
                    }
                }
            }

            $status = [
                'success' => 1,
                'msg' => __('customer.successful_verified_file')
            ];

        } catch (\Exception $e) {
            $exception = 1;

            $error_line = [
                'row' => 'N/A',
                'msg' => $e->getMessage()
            ];

            array_push($error_msg, $error_line);

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $status = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        // Session variables 
        session(['employees' => $employees]);

        $errors = $error_msg;

        if (count($error_msg) == 0 && $exception == 0) {
            //Archivo validado
            $flag = true;
        } else {
            //Errores en al check columns
            $flag = false;
        }

        return view('rrhh.import_employees.edit')
            ->with(compact(
                'errors',
                'status',
                'flag',
                'exception'
            ));

        return redirect('rrhh-import-employees')->with('status', $status);
    }



/**
     * Check row data.
     * 
     * @param  array  $row
     * @param  int  $row_no
     * @param  array  $default_data
     * @return array
     */
    public function checkEditRow($row, $row_no)
    {
        $employee = [
            'id' => null,
            'first_name' => null,
            'last_name' => null,
            'gender' => null,
            'nationality_id' => null,
            'birth_date' => null,
            'dni' => null,
            'approved' => null,
            'tax_number' => null,
            'civil_status_id' => null,
            'phone' => null,
            'mobile' => null,
            'email' => null,
            'institutional_email' => null,
            'address' => null,
            'country_id' => null,
            'state_id' => null,
            'city_id' => null,
            'social_security_number' => null,
            'afp_id' => null,
            'afp_number' => null,
            'date_admission' => null,
            'department_id' => null,
            'position_id' => null,
            'type_id' => null,
            'salary' => null,
            'profession_id' => null,
            'payment_id' => null,
            'bank_id' => null,
            'bank_acount' => null,
        ];

        // Errors list
        $error_msg = [];

        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');



        // ---------- FIRST NAME ----------
        // Check empty
        if (empty($row['first_name'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.first_name_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['first_name']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.first_name_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                $employee['first_name'] = $row['first_name'];
            }
        }


        // ---------- LAST NAME ----------
        // Check empty
        if (empty($row['last_name'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.last_name_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['last_name']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.last_name_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                $employee['last_name'] = $row['last_name'];
            }
        }


        // ---------- GENDER ----------
        // Check empty
        if (empty($row['gender'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.gender_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            $gender = mb_strtolower($row['gender']);

            // Check invalid value
            if (in_array($gender, ['f', 'F', 'm', 'M'])) {
                if (in_array($gender, ['f', 'F'])) {
                    $employee['gender'] = 'F';
                } else if (in_array($gender, ['m', 'M'])) {
                    $employee['gender'] = 'M';
                } 
            } else {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.gender_invalid')
                ];

                array_push($error_msg, $error_line);
            }
        }


        // ---------- NATIONALITY ----------
        // Check empty
        if (empty($row['nationality'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.nationality_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['nationality']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.nationality_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $nationality = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 6)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['nationality']]);
                    })->first();

                if (empty($nationality)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.nationality_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['nationality'] = $nationality->id;
                }
            }   
        }


        // ---------- BIRTH DATE ----------
        // Check empty
        if (empty($row['birth_date'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.birth_date_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format date
            $row['birth_date'] = str_replace("-", "/", $row['birth_date']);
            if (date('d/m/Y', strtotime($row['birth_date'])) != $row['birth_date'] || strlen($row['birth_date']) > 11) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.birth_date_valid')
                ];

                array_push($error_msg, $error_line);
            }else{
                $employee['birth_date'] = $this->moduleUtil->uf_date($row['birth_date']);
            }
        }


        // ---------- DNI ----------
        // Check empty
        if (empty($row['dni'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.dni_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format dni
            $formatDni = "/^\d{8}-\d$/";
            if(!preg_match($formatDni, $row['dni'])){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.dni_valid')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                // Check unique
                $is_exist = Employees::where('business_id', $business_id)
                ->where('dni', [$row['dni']])
                ->exists();

                if ($is_exist) {
                    // $error_line = [
                    //     'row' => $row_no,
                    //     'msg' => __('rrhh.dni_unique')
                    // ];

                    // array_push($error_msg, $error_line);
                    $employee['dni'] = $row['dni'];
                    $employee['id'] = $is_exist->id;
                }else{
                    $employee['dni'] = $row['dni'];
                }
            }
        }


        // ---------- TAX NUMBER ----------
        // Check empty
        if (empty($row['tax_number'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.tax_number_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format tax number
            if(strlen($row['tax_number']) > 18){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.tax_number_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $format = "/^[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9]$/";
                if (!preg_match($format, $row['tax_number'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.tax_number_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['tax_number'] = $row['tax_number'];
                }
            }
        }


        // ---------- CIVIL STATUS ----------
        // Check empty
        if (empty($row['civil_status'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.civil_status_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['civil_status']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.civil_status_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $civil_status = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 1)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['civil_status']]);
                    })->first();

                if (empty($civil_status)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.civil_status_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else{
                    $employee['civil_status_id'] = $civil_status->id;
                }
            }
        }
      

        // ---------- PHONE ----------
        // Check is not empty
        if (!empty($row['phone'])) {
            // Check format tax number
            if(strlen($row['phone']) > 9){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.phone_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $format = "/^[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9]$/";
                if (!preg_match($format, $row['phone'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.phone_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['phone'] = $row['phone'];
                }
            }   
        }


        // ---------- MOBILE ----------
        // Check is not empty
        if (!empty($row['mobile'])) {
            // Check format tax number
            if(strlen($row['mobile']) > 9){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.mobile_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $format = "/^[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9\+_\-{1}]+[0-9]$/";
                if (!preg_match($format, $row['mobile'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.mobile_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['mobile'] = $row['mobile'];
                }
            }   
        }
        

        // ---------- EMAIL ----------
        // Check empty
        if (empty($row['email'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.email_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format tax number
            if(strlen($row['email']) > 191){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.email_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $employee['email'] = $row['email'];
            }
        }


        // ---------- INSTITUTIONAL EMAIL ----------
        // Check is not empty
        if (!empty($row['institutional_email'])) {
            // Check length
            if(strlen($row['institutional_email']) > 191){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.institutional_email_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $employee['institutional_email'] = $row['institutional_email'];
            }
        }


        // ---------- ADDRESS ----------
        // Check empty
        if (empty($row['address'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.address_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if(strlen($row['address']) > 191){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.address_length')
                ];
    
                array_push($error_msg, $error_line);
            }else{
                $employee['address'] = $row['address'];
            }
        }


        // ---------- COUNTRY ----------
        // Check is not empty
        if (!empty($row['country'])) {
            // Check length
            if (strlen($row['country']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.country_length')
                ];

                array_push($error_msg, $error_line);
            }else{

                // Check exist
                $country = Country::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(name) = UPPER(?)', [$row['country']]);
                    })->first();

                if (empty($country)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.country_exist')
                    ];

                    array_push($error_msg, $error_line);

                    $country_error = true;
                }else {
                    $employee['country_id'] = $country->id;
                }
            }
        }


        // ---------- STATE ----------
        // Check is not empty
        if (!empty($row['state'])) {
            // Check length
            if (strlen($row['state']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.state_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $state = State::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(name) = UPPER(?)', [$row['state']]);
                    })->first();

                if (empty($state)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.state_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['state_id'] = $state->id;
                }
            }
        }


        // ---------- CITY ----------
        // Check is not empty
        if (!empty($row['city'])) {
            // Check length
            if (strlen($row['city']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.city_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $city = City::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(name) = UPPER(?)', [$row['city']]);
                    })->first();

                if (empty($city)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.city_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['city_id'] = $city->id;
                }
            }
        }


        // ---------- ISSS ----------
        // Check is not empty
        if (!empty($row['isss'])) {
            // Check length
            if(strlen($row['isss']) > 21){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.isss_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                //Check format
                $format = "/^[0-9]+$/";
                if (!preg_match($format, $row['isss'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.isss_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['social_security_number'] = $row['isss'];
                }
            }

        }


        // ---------- AFP ----------
        // Check is not empty
        if (!empty($row['afp'])) {
            // Check length
            if (strlen($row['afp']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.afp_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $afp = RrhhData::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['afp']]);
                    })->first();

                if (empty($afp)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.afp_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['afp_id'] = $afp->id;
                }
            }            
        }


        // ---------- AFP NUMBER ----------
        // Check is not empty
        if (!empty($row['afp_number'])) {
            // Check length
            if(strlen($row['afp_number']) > 26){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.afp_number_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                //Check format
                $format = "/^[0-9]+$/";
                if (!preg_match($format, $row['afp_number'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.afp_number_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['afp_number'] = $row['afp_number'];
                }
            }
        }


        // ---------- DATE ADMISSION ----------
        // Check empty
        if (empty($row['date_admission'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.date_admission_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check format date
            $row['date_admission'] = str_replace("-", "/", $row['date_admission']);
            if (date('d/m/Y', strtotime($row['date_admission'])) != $row['date_admission'] || strlen($row['date_admission']) > 11) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.date_admission_valid')
                ];

                array_push($error_msg, $error_line);
            }else{
                $employee['date_admission'] = $this->moduleUtil->uf_date($row['date_admission']);
            }
        }


        // ---------- DEPARTMENT ----------
        // Check empty
        if (empty($row['department'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.department_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['department']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.department_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $department = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 2)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['department']]);
                    })->first();

                if (empty($department)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.department_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['department_id'] = $department->id;
                }
            }
        }


        // ---------- POSITION ----------
        // Check empty
        if (empty($row['position'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.position_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['position']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.position_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $position = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 3)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['position']]);
                    })->first();

                if (empty($position)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.position_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['position_id'] = $position->id;
                }
            }
        }


        // ---------- TYPE ----------
        // Check is not empty
        if (!empty($row['type'])) {
            // Check length
            if (strlen($row['type']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.type_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $type = RrhhTypeWage::where('business_id', $business_id)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(name) = UPPER(?)', [$row['type']]);
                    })->first();

                if (empty($type)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.type_exist')
                    ];

                    array_push($error_msg, $error_line);

                    $type_error = true;
                }else {
                    $employee['type_id'] = $type->id;
                }
            } 
        }


        // ---------- SALARY ----------
        // Check empty
        if (empty($row['salary'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.salary_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check is numeric
            if (! is_numeric($row['salary'])) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.salary_numeric')
                ];
    
                array_push($error_msg, $error_line);
            } else {
                // Check zero
                if ($row['salary'] < 0) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.salary_zero')
                    ];
        
                    array_push($error_msg, $error_line);

                } else {
                    $employee['salary'] = $this->employeeUtil->num_uf($row['salary']);
                }
            }
        }


        // ---------- PROFESSION ----------
        // Check is not empty
        if (!empty($row['profession'])) {
            // Check length
            if (strlen($row['profession']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.profession_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $profession = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 7)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['profession']]);
                    })->first();

                if (empty($profession)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.profession_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else {
                    $employee['profession_id'] = $profession->id;
                }
            }
        }


        // ---------- PAYMENT ----------
        // Check empty
        if (empty($row['payment'])) {
            $error_line = [
                'row' => $row_no,
                'msg' => __('rrhh.payment_empty')
            ];

            array_push($error_msg, $error_line);
        } else {
            // Check length
            if (strlen($row['payment']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.payment_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $payment = RrhhData::where('business_id', $business_id)
                    ->where('rrhh_header_id', 8)
                    ->where('status', 1)
                    ->where(function ($query) use ($row) {
                        $query->whereRaw('UPPER(value) = UPPER(?)', [$row['payment']]);
                    })->first();

                if (empty($payment)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.payment_exist')
                    ];

                    array_push($error_msg, $error_line);

                    $payment_error = true;
                }else {
                    $employee['payment_id'] = $payment->id;
                }
            } 
        }


        // ---------- BANK ----------
        // Check empty
        if (empty($row['bank'])) {
            if(mb_strtolower($row['payment']) == mb_strtolower('Transferencia bancaria')){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.bank_empty')
                ];
    
                array_push($error_msg, $error_line);
            }
        }else{
            // Check length
            if (strlen($row['bank']) > 191) {
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.bank_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                // Check exist
                $bank = Bank::where('business_id', $business_id)
                ->where(function ($query) use ($row) {
                    $query->whereRaw('UPPER(name) = UPPER(?)', [$row['bank']]);
                })->first();

                if (empty($bank)) {
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.bank_exist')
                    ];

                    array_push($error_msg, $error_line);
                }else{
                    $employee['bank_id'] = $bank->id;
                }
            }
        }
       
        
        // ---------- BANK ACCOUNT ----------
        // Check empty
        if (empty($row['bank_account'])) {
            if(mb_strtolower($row['payment']) == mb_strtolower('Transferencia bancaria')){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.bank_account_empty')
                ];
    
                array_push($error_msg, $error_line);
            }
        } else {
            // Check length
            if(strlen($row['bank_account']) > 191){
                $error_line = [
                    'row' => $row_no,
                    'msg' => __('rrhh.bank_account_length')
                ];

                array_push($error_msg, $error_line);
            }else{
                $format = "/^[0-9]+$/";
                if (!preg_match($format, $row['bank_account'])){
                    $error_line = [
                        'row' => $row_no,
                        'msg' => __('rrhh.bank_account_valid')
                    ];
        
                    array_push($error_msg, $error_line);
                }else{
                    $employee['bank_account'] = $row['bank_account'];
                }
            }
        }


        // ----- BUSINESS ID -----
        $employee['business_id'] = $business_id;

        // ----- CREATED BY -----
        $employee['created_by'] = $user_id;


        $result = [
            'employees' => $employee,
            'error_msg' => $error_msg,
        ];

        return $result;
    }



    /**
     * Imports the uploaded file to database.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        if (! auth()->user()->can('rrhh_import_employee.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            // Session variables
            $employees = session('employees');

            DB::beginTransaction();

            if (! empty($employees)) {
                foreach ($employees as $data) {
                    //Employee
                    $employee = Employees::where('id', $data['id'])->where('business_id', $business_id)->first();

                    $dataEmployee = [
                        'agent_code' => $this->employeeUtil->generateCorrelative($data['date_admission'], $data['business_id']),
                        'first_name' => is_null($data['first_name']) ? $employee->first_name : $data['first_name'],
                        'last_name' => is_null($data['last_name']) ? $employee->last_name : $data['last_name'],
                        'gender' => is_null($data['gender']) ? $employee->gender : $data['gender'],
                        'nationality_id' => is_null($data['nationality_id']) ? $employee->nationality_id : $data['nationality_id'],
                        'birth_date' => is_null($data['birth_date']) ? $employee->birth_date : $data['birth_date'],
                        'dni' => is_null($data['dni']) ? $employee->dni : $data['dni'],
                        'approved' =>  ($data['dni'] == $data['tax_number'])? 1 : 0, //Falta

                        'tax_number' => is_null($data['tax_number']) ? $employee->tax_number : $data['tax_number'],
                        'civil_status_id' => is_null($data['civil_status_id']) ? $employee->civil_status_id : $data['civil_status_id'],
                        'phone' => is_null($data['phone']) ? $employee->phone : $data['phone'],
                        'mobile' => is_null($data['mobile']) ? $employee->mobile : $data['mobile'],
                        'email' => is_null($data['email']) ? $employee->email : $data['email'],
                        'institutional_email' => is_null($data['institutional_email']) ? $employee->institutional_email : $data['institutional_email'],
                        'address' => is_null($data['address']) ? $employee->address : $data['address'],
                        'country_id' => is_null($data['country_id']) ? $employee->country_id : $data['country_id'],
                        'state_id' => is_null($data['state_id']) ? $employee->state_id : $data['state_id'],
                        'city_id' => is_null($data['city_id']) ? $employee->city_id : $data['city_id'],
                        'social_security_number' => is_null($data['social_security_number']) ? $employee->social_security_number : $data['social_security_number'],
                        'afp_id' => is_null($data['afp_id']) ? $employee->afp_id : $data['afp_id'],

                        'afp_number' => is_null($data['afp_number']) ? $employee->afp_number : $data['afp_number'],
                        'date_admission' => is_null($data['date_admission']) ? $employee->date_admission : $data['date_admission'],
                        'type_id' => is_null($data['type_id']) ? $employee->type_id : $data['type_id'],
                        'profession_id' => is_null($data['profession_id']) ? $employee->profession_id : $data['profession_id'],
                        'payment_id' => is_null($data['payment_id']) ? $employee->payment_id : $data['payment_id'],
                        'bank_id' => is_null($data['bank_id']) ? $employee->bank_id : $data['bank_id'],
                        'bank_acount' => is_null($data['bank_acount']) ? $employee->bank_acount : $data['bank_acount'],
                    ];

                    if($data['id'] == null){
                        //Create new employee
                        $employee = Employees::create($new_employee);
                    }else{
                        //Update employee
                        $employee->update($dataEmployee);
                    }
                    $position = RrhhPositionHistory::where('employee_id', $employee->id)->where('current', 1)->count();
                    $salary = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->count();


                    if($position != 0){
                        $position = RrhhPositionHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
                        $position->delete();
                    }
                    
                    RrhhPositionHistory::insert([
                        'new_department_id' => $data['department_id'], 
                        'new_position1_id' => $data['position_id'], 
                        'employee_id' => $employee->id, 
                        'current' => 1
                    ]);
        
                    if($salary != 0){
                        $salary = RrhhSalaryHistory::where('employee_id', $employee->id)->where('current', 1)->orderBy('id', 'DESC')->first();
                        $salary->delete();
                    }

                    RrhhSalaryHistory::insert([
                        'employee_id' => $employee->id, 
                        'new_salary' => $data['salary'], 
                        'current' => 1
                    ]);
                }
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('product.file_imported_successfully')
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('import-products')->with('status', $output);
    }
}
