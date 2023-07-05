<?php

namespace App\Http\Controllers;

use App\RrhhHeader;
use Illuminate\Http\Request;

class RrhhHeaderController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if ( !auth()->user()->can('rrhh_catalogues.view') ) {
            abort(403, 'Unauthorized action.');
        }

        return view('rrhh.catalogues.index');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RrhhHeader  $rrhhHeader
     * @return \Illuminate\Http\Response
     */
    public function show(RrhhHeader $rrhhHeader)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RrhhHeader  $rrhhHeader
     * @return \Illuminate\Http\Response
     */
    public function edit(RrhhHeader $rrhhHeader)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RrhhHeader  $rrhhHeader
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RrhhHeader $rrhhHeader)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RrhhHeader  $rrhhHeader
     * @return \Illuminate\Http\Response
     */
    public function destroy(RrhhHeader $rrhhHeader)
    {
        //
    }
}
