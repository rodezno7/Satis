<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportEmployeesController extends Controller
{
    public function index(){
        if (!auth()->user()->can('import_employees.create')) {
            abort(403, 'Unauthorized action.');
        }
    
        return view('rrhh.import_employees.index');
    }
}
