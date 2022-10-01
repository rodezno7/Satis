<?php

namespace App\Http\Controllers;

use App\Brands;
use App\Customer;
use App\CustomerVehicle;
use DB;
use Excel;
use Illuminate\Http\Request;

class CustomerVehicleController extends Controller
{
    /**
     * Show import option for customer vehicles.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImporter()
    {
        if (! auth()->user()->can('customer.create') || config('app.business') != 'workshop') {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        $errors = [];

        // Check if zip extension it loaded or not
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => __('messages.install_enable_zip')
            ];

            return view('customer.import_vehicles')->with([
                'notification' => $output,
                'errors' => $errors
            ]);

        } else {
            return view('customer.import_vehicles', compact('errors'));
        }
    }

    /**
     * Process data before import.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postImporter(Request $request)
    {
        if (! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set maximum php execution time
            ini_set('max_execution_time', 0);

            // Errors list
            $error_msg = [];

            // Vehicles lines
            $vehicles = [];

            $business_id = auth()->user()->business_id;
            $user_id = auth()->user()->id;
            $exception = 0;

            if ($request->hasFile('file_xlsx')) {
                $file = $request->file('file_xlsx');

                $imported_data = Excel::toArray('', $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX)[1];

                // Remove header
                unset($imported_data[0]);
                unset($imported_data[1]);
                unset($imported_data[2]);
                unset($imported_data[3]);

                // Process file
                foreach ($imported_data as $key => $value) {
                    // Check if 11 no. of columns exists
                    if (count($value) != 12) {
                        $error_line = [
                            'row' => 'N/A',
                            'msg' => __('purchase.number_of_columns_mismatch') . ' (' . count($value) . ')'
                        ];

                        array_push($error_msg, $error_line);
                    }

                    // Row number
                    $row_no = $key + 1;

                    // Columns
                    $dni = trim($value[0]);
                    $nit = trim($value[1]);
                    $license_plate = trim($value[2]);
                    $brand = trim($value[3]);
                    $model = trim($value[4]);
                    $year = trim($value[5]);
                    $color = trim($value[6]);
                    $responsible = trim($value[7]);
                    $engine_number = trim($value[8]);
                    $vin_chassis = trim($value[9]);
                    $mi_km = trim($value[10]);
                    $id = trim($value[11]);

                    // Data
                    $vehicle = [
                        'customer_id' => null,
                        'license_plate' => null,
                        'brand_id' => $brand,
                        'model' => $model,
                        'year' => null,
                        'color' => $color,
                        'responsible' => $responsible,
                        'engine_number' => $engine_number,
                        'vin_chassis' => $vin_chassis,
                        'mi_km' => $mi_km
                    ];

                    // COLUMN A, B AND L

                    // Check empty
                    if (empty($dni) && empty($nit) && empty($id)) {
                        $error_line = [
                            'row' => $row_no,
                            'msg' => __('customer.dni_nit_empty')
                        ];

                        array_push($error_msg, $error_line);
                    }

                    // Check existence
                    if (! empty($dni)) {
                        $customer = Customer::where('dni', $dni)
                            ->where('business_id', $business_id)
                            ->first();

                        if (! empty($customer)) {
                            $vehicle['customer_id'] = $customer->id;
                        }
                    }

                    if (! empty($nit) && is_null($vehicle['customer_id'])) {
                        $customer = Customer::where('tax_number', $nit)
                            ->where('business_id', $business_id)
                            ->first();

                        if (! empty($customer)) {
                            $vehicle['customer_id'] = $customer->id;
                        }
                    }

                    if (! empty($id) && is_null($vehicle['customer_id'])) {
                        $customer = Customer::find($id);

                        if (! empty($customer)) {
                            $vehicle['customer_id'] = $customer->id;
                        }
                    }

                    if (is_null($vehicle['customer_id'])) {
                        $error_line = [
                            'row' => $row_no,
                            'msg' => __('customer.customer_does_not_exist')
                        ];

                        array_push($error_msg, $error_line);
                    }

                    // COLUMN C

                    // Check empty
                    if (empty($license_plate)) {
                        $error_line = [
                            'row' => $row_no,
                            'msg' => __('customer.license_plate_empty')
                        ];

                        array_push($error_msg, $error_line);

                    } else {
                        $vehicle['license_plate'] = $license_plate;
                    }

                    // COLUMN F

                    // Check greater than or equal to zero
                    if (! empty($year)) {
                        if ($year <= 0) {
                            $error_line = [
                                'row' => $row_no,
                                'msg' => __('customer.year_greater_equal_zero')
                            ];
    
                            array_push($error_msg, $error_line);

                        } else {
                            $vehicle['year'] = $year;
                        }
                    }

                    array_push($vehicles, $vehicle);
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

        session(['vehicles' => $vehicles]);

        $errors = $error_msg;

        if (count($error_msg) == 0 && $exception == 0) {
            $flag = true;
        } else {
            $flag = false;
        }

        return view('customer.import_vehicles')
            ->with(compact(
                'errors',
                'status',
                'flag',
                'exception'
            ));
    }

    /**
     * Import customer vehicles.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        if (! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = auth()->user()->business_id;
            $user_id = auth()->user()->id;

            // Vehicles
            $vehicles = session('vehicles');

            DB::beginTransaction();

            if (! empty($vehicles)) {
                foreach ($vehicles as $vehicle) {
                    if (! empty($vehicle['brand_id'])) {
                        $brand = Brands::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $vehicle['brand_id']],
                            ['created_by' => $user_id]
                        );

                        $vehicle['brand_id'] = $brand->id;
                    }
                    
                    CustomerVehicle::create($vehicle);
                }
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('customer.vehicles_add_success')
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('customers')->with('status', $output);
    }
}
