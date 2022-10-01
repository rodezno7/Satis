<?php

namespace App\Http\Controllers;

use App\FiscalYear;
use App\AccountingPeriod;
use Illuminate\Http\Request;
use DataTables;

class FiscalYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                'year' => 'required|unique:fiscal_years',
            ],
            [
                'year.required' => __('accounting.year_required'),
                'year.unique' => __('accounting.year_unique'),
            ]
        );
        if($request->ajax())
        {
            $year = FiscalYear::create($request->all());
            return response()->json([
                "msj" => 'Created'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FiscalYear  $fiscalYear
     * @return \Illuminate\Http\Response
     */
    public function show(FiscalYear $fiscalYear)
    {
        return response()->json($fiscalYear);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FiscalYear  $fiscalYear
     * @return \Illuminate\Http\Response
     */
    public function edit(FiscalYear $fiscalYear)
    {
        return response()->json($fiscalYear);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FiscalYear  $fiscalYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FiscalYear $fiscalYear)
    {
        $validateData = $request->validate(
            [
                'year' => 'required|unique:fiscal_years,year,'.$fiscalYear->id,
            ],
            [
                'year.required' => __('accounting.year_required'),
                'year.unique' => __('accounting.year_unique'),
            ]
        );
        if($request->ajax())
        {
            $fiscalYear->update($request->all());
            return response()->json([
                "msj" => 'Updated'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FiscalYear  $fiscalYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(FiscalYear $fiscalYear)
    {
        if (request()->ajax()) {
            try{

                $periods = AccountingPeriod::where('fiscal_year_id', $fiscalYear->id)->count();

                if($periods > 0){
                    $output = [
                        'success' => false,
                        'msg' =>  __('accounting.year_has_periods')
                    ];
                }
                else{
                    $fiscalYear->forceDelete();
                    $output = [
                        'success' => true,
                        'msg' => __('accounting.year_deleted')
                    ];
                }
            }
            catch (\Exception $e){
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    public function getFiscalYearsData()
    {
        $years = FiscalYear::select('id', 'year')->get();
        return DataTables::of($years)->toJson();
    }

    public function getYears()
    {
        $years = FiscalYear::select('id', 'year')->get();
        return response()->json($years);
    }
}
