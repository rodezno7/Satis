<?php

namespace App\Http\Controllers;

use App\Zone;
use App\State;
use Illuminate\Http\Request;
use DataTables;

class ZoneController extends Controller
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
                'name' => 'required|unique:zones',
            ]
        );
        if($request->ajax())
        {
            try {

                $zone_details = $request->only(['name']);
                $zone_details['business_id'] = request()->session()->get('user.business_id');

                $zone = Zone::create($zone_details);
                $output = [
                    'success' => true,
                    'msg' => __('geography.zone_added')
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
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $zone = Zone::findOrFail($id);
        return response()->json($zone);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $zone = Zone::findOrFail($id);
        return response()->json($zone);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $validateData = $request->validate(
            [
                'name' => 'required|unique:zones,name,'.$zone->id,
            ]
        );
        if($request->ajax())
        {
            try {

                $zone->name = $request->input('name');
                $zone->save();

                $output = [
                    'success' => true,
                    'msg' => __("geography.zone_updated")
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
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try{

                $zone = Zone::findOrFail($id);
                $states = State::where('zone_id', $zone->id)->count();

                if($states > 0){
                    $output = [
                        'success' => false,
                        'msg' =>  __('geography.zone_has_states')
                    ];
                }
                else{
                    $zone->forceDelete();
                    $output = [
                        'success' => true,
                        'msg' => __('geography.zone_deleted')
                    ];
                }
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

    public function getZonesData()
    {
        $business_id = request()->session()->get('user.business_id');
        $zones = Zone::where('business_id', $business_id);
        return DataTables::of($zones)->toJson();
    }

    public function getZones()
    {
        $business_id = request()->session()->get('user.business_id');
        $zones = Zone::where('business_id', $business_id)->get();
        return response()->json($zones);
    }
}
