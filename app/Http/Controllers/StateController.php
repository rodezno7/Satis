<?php

namespace App\Http\Controllers;

use App\State;
use App\City;
use Illuminate\Http\Request;
use DataTables;

class StateController extends Controller
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
                'name' => 'required|unique:states',
                'country_id' => 'required',
                'zone_id' => 'required',
            ]
        );
        if($request->ajax())
        {
            try {

                $state_details = $request->only(['name', 'zip_code', 'country_id', 'zone_id']);
                $state_details['business_id'] = request()->session()->get('user.business_id');

                $state = State::create($state_details);
                $output = [
                    'success' => true,
                    'msg' => __('geography.state_added')
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
     * @param  \App\State  $state
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $state = State::findOrFail($id);
        return response()->json($state);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\State  $state
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $state = State::findOrFail($id);
        return response()->json($state);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\State  $state
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $state = State::findOrFail($id);

        $validateData = $request->validate(
            [
                'name' => 'required|unique:states,name,'.$state->id,
                'country_id' => 'required',
                'zone_id' => 'required',
            ]
        );
        if($request->ajax())
        {
            try {

                $state->name = $request->input('name');
                $state->zip_code = $request->input('zip_code');
                $state->country_id = $request->input('country_id');
                $state->zone_id = $request->input('zone_id');
                $state->save();

                $output = [
                    'success' => true,
                    'msg' => __("geography.state_updated")
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
     * @param  \App\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try{

                $state = State::findOrFail($id);
                $cities = City::where('state_id', $state->id)->count();

                if ($cities > 0) {
                    $output = [
                        'success' => false,
                        'msg' =>  __('geography.state_has_cities')
                    ];
                }

                $employees = DB::table('human_resource_employees')
                ->where('state_id', $id)               
                ->count();

                if ($employees > 0) {

                    $output = [
                        'success' => false,
                        'msg' => __('rrhh.item_has_childs')
                    ];

                }


                $state->forceDelete();
                $output = [
                    'success' => true,
                    'msg' => __('geography.state_deleted')
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

    public function getStatesData()
    {
        $business_id = request()->session()->get('user.business_id');
        $states = State::where('business_id', $business_id)->with('country', 'zone');
        return DataTables::of($states)->toJson();
    }

    public function getStates()
    {
        $business_id = request()->session()->get('user.business_id');
        $states = State::where('business_id', $business_id)->get();
        return response()->json($states);
    }

    public function getStatesByCountry($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $states = State::where('business_id', $business_id)
        ->where('country_id', $id)
        ->get();
        return response()->json($states);
    }
}
