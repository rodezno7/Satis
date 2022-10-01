<?php

namespace App\Http\Controllers;

use App\CashDetail;
use Illuminate\Http\Request;

class CashDetailController extends Controller
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
        return view("cash_detail.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CashDetail  $cashDetail
     * @return \Illuminate\Http\Response
     */
    public function show(CashDetail $cashDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CashDetail  $cashDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(CashDetail $cashDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CashDetail  $cashDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CashDetail $cashDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CashDetail  $cashDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(CashDetail $cashDetail)
    {
        //
    }
}
