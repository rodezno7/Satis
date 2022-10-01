<?php

namespace App\Http\Controllers;

use App\DocumentCorrelative;
use App\System;
use App\DocumentType;
use App\BusinessLocation;
use App\Notifications\NewNotification;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;

use DB;

class DocumentCorrelativeController extends Controller
{

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;

        // Status
        $this->status_list = [
            'all' => __('kardex.all'),
            'active' => __('cashier.active'),
            'inactive' => __('cashier.inactive')
        ];
    }

    /**
     * Invocar la vista de listado de Correlativos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('correlatives.view') && !auth()->user()->can('correlatives.create')) {
            abort(403, "Unauthorized action.");
        }

        $business_id = request()->session()->get('user.business_id');

        // Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);

        $default_location = null;

        // Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
            }
            
        // Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__("kardex.all_2"), 'all');
        }

        // Document types
        $document_types = DocumentType::forDropdown($business_id, false, false);
        $document_types = $document_types->prepend(__("kardex.all"), 'all');

        // Payment status
        $status_list = $this->status_list;

        return view('document_correlatives.index')
            ->with(compact(
                'locations',
                'default_location',
                'document_types',
                'status_list'
            ));
    }

    // Mostrar lista de Correlativos
    public function getCorrelativesData()
    {
        if (!auth()->user()->can('correlatives.view') && !auth()->user()->can('correlatives.create')) {
            abort(403, "Unauthorized action.");
        }

        $business_id = request()->session()->get('user.business_id');
        $correlatives = DB::table('document_correlatives')
            ->select(
                'document_correlatives.id',
                'business_locations.name',
                'document_types.document_name',
                'document_correlatives.serie',
                'document_correlatives.resolution',
                'document_correlatives.initial',
                'document_correlatives.actual',
                'document_correlatives.final',
                'document_correlatives.status'
            )
            ->join('business_locations', 'document_correlatives.location_id', '=', 'business_locations.id')
            ->join('document_types', 'document_correlatives.document_type_id', '=', 'document_types.id')
            ->where('document_correlatives.business_id', $business_id);

        // Location filter
        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations != 'all') {
            $correlatives->whereIn('business_locations.id', $permitted_locations);
        }

        if (request()->has('location_id')) {
            $location_id = request()->get('location_id');

            if (! empty($location_id) && $location_id != 'all') {
                $correlatives->where('business_locations.id', $location_id);
            }
        }

        // Document type filter
        if (request()->has('document_type_id')) {
            $document_type_id = request()->get('document_type_id');

            if (! empty($document_type_id) && $document_type_id != 'all') {
                $correlatives->where('document_types.id', $document_type_id);
            }
        }

        // Status filter
        if (request()->has('status')) {
            $status = request()->get('status');

            if (! empty($status) && $status != 'all') {
                $correlatives->where('document_correlatives.status', $status);
            }
        }

        return DataTables::of($correlatives)
            ->addColumn(
                'action', function ($row) {
                    $html = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                '<span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                    if (auth()->user()->can('correlatives.update')) {
                        $html .= '<li><a href="#" data-href="' . action('DocumentCorrelativeController@edit', [$row->id]) . '" class="edit_correlatives_button"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                    }

                    if (auth()->user()->can('correlatives.delete')) {
                        $html .= '<li><a href="#" data-href="' . action('DocumentCorrelativeController@destroy', [$row->id]) . '" class="delete_correlatives_button"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '</ul></div>';

                    return $html;
                }
            )
            ->editColumn(
                'status',
                '@if ($status == "active")
                    <span class="badge" style="background-color: #5cb85c;">{{ __("cashier." . $status) }}</span>
                @else
                    <span class="badge" style="background-color: #d9534f;">{{ __("cashier." . $status) }}</span>
                @endif'
            )
            ->removeColumn('id')
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    /**
     * Mostrar formulario para registrar nuevo correlativo.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('correlatives.create')) {
            abort(403, "Unauthorized action.");
        }

        $business_id = request()->session()->get('user.business_id');

        $documentstypes = DocumentType::forDropdown($business_id);
        $locations = BusinessLocation::forDropdown($business_id);

        return view('document_correlatives.create')
            ->with(compact('documentstypes', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('correlatives.create')) {
            abort(403, "Unauthorized action.");
        }

        try {
            $correlatives = $request->only(['location_id', 'document_type_id', 'serie', 'resolution', 'initial', 'actual', 'final']);
            $correlatives['business_id'] = $request->session()->get('user.business_id');
            $correlatives['status'] = 'active';

            $correlatives = DocumentCorrelative::create($correlatives);
            $outpout = [
                'success' => true,
                'data' => $correlatives,
                'msg' => __("correlatives.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $outpout = ['success' => false, 'msg' => $e->getMessage()];
        }
        return $outpout;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('correlatives.update')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $correlatives = DocumentCorrelative::where('business_id', $business_id)->find($id);
            $documentstypes = DocumentType::forDropdown($business_id);
            $locations = BusinessLocation::forDropdown($business_id);

            return view('document_correlatives.edit')
                ->with(compact('correlatives', 'documentstypes', 'locations'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('correlatives.update')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['location_id', 'document_type_id', 'serie', 'resolution', 'initial', 'actual', 'final']);
                $business_id = request()->session()->get('user.business_id');

                $correlatives = DocumentCorrelative::where('business_id', $business_id)->findOrFail($id);
                $correlatives->location_id = $input['location_id'];
                $correlatives->document_type_id = $input['document_type_id'];
                $correlatives->serie = $input['serie'];
                $correlatives->resolution = $input['resolution'];
                $correlatives->initial = $input['initial'];
                $correlatives->actual = $input['actual'];
                $correlatives->final = $input['final'];
                $correlatives->save();

                $outpout = ['success' => true, 'data' => $correlatives, 'msg' => __('correlatives.updated_success')];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $outpout = ['success' => false, 'msg' => $e->getMessage()];
            }

            return $outpout;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('correlatives.delete')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $correlatives = DocumentCorrelative::where('business_id', $business_id)->find($id);
                $correlatives->delete();
                $outpout = ['success' => true, 'data' => $correlatives, 'msg' => __('correlatives.deleted_success')];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $outpout = ['success' => false, 'msg' => $e->getMessage()];
            }

            return $outpout;
        }
    }
}
