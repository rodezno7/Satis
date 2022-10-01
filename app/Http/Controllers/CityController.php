<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;
use DataTables;
use DB;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('geography.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('geography.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate(
            [
                'name' => 'required',
                'state_id' => 'required',
            ]
        );
        if($request->ajax())
        {
            try {

                $city_details = $request->only(['name', 'state_id']);
                $city_details['business_id'] = request()->session()->get('user.business_id');

                $city = City::create($city_details);
                $output = [
                    'success' => true,
                    'msg' => __('geography.city_added')
                ];

            } catch(\Exception $e){
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
     * Display the specified resource.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $city = City::findOrFail($id);
        return response()->json($city);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $city = City::findOrFail($id);
        return response()->json($city);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);

        $validateData = $request->validate(
            [
                'name' => 'required|unique:cities,name,'.$city->id,
                'state_id' => 'required',
            ]
        );
        if($request->ajax())
        {
            try {

                $city->name = $request->input('name');
                $city->state_id = $request->input('state_id');
                $city->save();

                $output = [
                    'success' => true,
                    'msg' => __("geography.city_updated")
                ];

            } catch(\Exception $e){
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
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try{
                $city = City::findOrFail($id);

                $employees = DB::table('human_resource_employees')
                ->where('city_id', $id)               
                ->count();

                if ($employees > 0) {

                    $output = [
                        'success' => false,
                        'msg' => __('rrhh.item_has_childs')
                    ];

                }

                $city->forceDelete();
                $output = [
                    'success' => true,
                    'msg' => __('geography.city_deleted')
                ];
                
            }
            catch (\Exception $e){
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    public function getCitiesData()
    {
        $business_id = request()->session()->get('user.business_id');
        $cities = City::where('business_id', $business_id)->with('state');
        return DataTables::of($cities)->toJson();
    }

    public function changeStatus($id)
    {
        try{
            $city = City::findOrFail($id);
            if ($city->status == 1) {
                $city->status = 0;
            }
            else {
                $city->status = 1;
            }
            $city->save();
            
            $output = [
                'success' => true,
                'msg' => __('geography.status_changed')
            ];

        }
        catch (\Exception $e){
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return $output;
    }

    public function getCitiesByState($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $cities = City::where('business_id', $business_id)
        ->where('state_id', $id)
        ->get();
        return response()->json($cities);
    }
}
