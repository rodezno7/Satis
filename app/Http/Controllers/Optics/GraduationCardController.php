<?php

namespace App\Http\Controllers\Optics;

use App\Employees;
use App\Optics\Diagnostic;
use App\Optics\GraduationCard;
use App\Optics\GraduationCardHasDiagnostic;
use App\Optics\Patient;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use DB;

class GraduationCardController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $util;

    /**
     * Constructor
     *
     * @param \App\Utils\Util $util
     * @return void
     */
    public function __construct(Util $util)
    {
        $this->util = $util;
        $this->module_name = 'graduation_card';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('graduation_card.view') && !auth()->user()->can('graduation_card.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $graduation_cards = GraduationCard::join('patients as p', 'graduation_cards.patient_id', 'p.id')
                ->select('p.full_name', 'graduation_cards.id');
            
            return Datatables::of($graduation_cards)
                ->addColumn(
                    'action',
                    '@can("graduation_cards.update")
                    <button data-href="{{ action(\'Optics\GraduationCardController@edit\', [$id]) }}" class="btn btn-xs btn-primary edit_graduation_cards_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                    &nbsp;
                    @endcan
                    @can("graduation_cards.delete")
                    <button data-href="{{ action(\'Optics\GraduationCardController@destroy\', [$id]) }}" class="btn btn-xs btn-danger delete_graduation_cards_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->removeColumn('id')
                ->rawColumns([1])
                ->make(false);
        }

        return view('optics.graduation_card.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('graduation_card.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Data for form
        $business_id = request()->session()->get('user.business_id');
        $patients = Patient::forDropdown($business_id);
        $employees = Employees::forDropdown($business_id);
        $diagnostics = Diagnostic::where('business_id', $business_id)
            ->orderBy('name')
            ->get();

        return view('optics.graduation_card.create')
            ->with(compact('patients', 'employees', 'diagnostics'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('graduation_card.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                'patient_id',
                'sphere_os', 'sphere_od',
                'cylindir_os', 'cylindir_od',
                'axis_os', 'axis_od',
                'base_os', 'base_od',
                'addition_os', 'addition_od',
                'di', 'ao',
                'invoice',
                'attended_by',
                'optometrist',
                'observations',
                'dnsp_os', 'dnsp_od', 'ap'
            ]);

            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $diagnostics = $request->input('diagnostics');

            $input['is_prescription'] = !empty($request->input('is_prescription')) ? 1 : 0;

            DB::beginTransaction();
            // Saves graduation card
            $graduation_card = GraduationCard::create($input);

            # Store binnacle
            $user_id = $request->session()->get('user.id');

            $this->util->registerBinnacle($user_id, $this->module_name, 'create', $graduation_card);

            // Saves diagnostics
            if (!empty($diagnostics)) {
                foreach ($diagnostics as $diag) {
                    $diagnostic = new GraduationCardHasDiagnostic;
                    $diagnostic->graduation_card_id = $graduation_card->id;
                    $diagnostic->diagnostic_id = $diag;
                    $diagnostic->save();
                }
            }
            DB::commit();
    
            $output = ['success' => true,
                'data' => $graduation_card,
                'msg' => __("graduation_card.added_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GraduationCard  $graduationCard
     * @return \Illuminate\Http\Response
     */
    public function show(GraduationCard $graduationCard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GraduationCard  $graduationCard
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('graduation_card.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            // Data for form
            $business_id = request()->session()->get('user.business_id');
            $patients = Patient::forDropdown($business_id);
            $employees = Employees::forDropdown($business_id);
            $graduation_card = GraduationCard::find($id);
            $diagnostics = Diagnostic::where('business_id', $business_id)
                ->orderBy('name')
                ->get();
            $my_diagnostics = GraduationCardHasDiagnostic::where('graduation_card_id', $id)
                ->pluck('diagnostic_id')
                ->toArray();

            $status = [];
            foreach ($diagnostics as $diag) {
                if (in_array($diag->id, $my_diagnostics)) {
                    array_push($status, 1);
                } else {
                    array_push($status, 0);
                }
            }

            return view('optics.graduation_card.edit')
                ->with(compact('patients', 'employees', 'graduation_card', 'diagnostics', 'status'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GraduationCard  $graduationCard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('graduation_card.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only([
                    'patient_id',
                    'sphere_os', 'sphere_od',
                    'cylindir_os', 'cylindir_od',
                    'axis_os', 'axis_od',
                    'base_os', 'base_od',
                    'addition_os', 'addition_od',
                    'di', 'ao',
                    'invoice',
                    'attended_by',
                    'optometrist',
                    'observations',
                    'dnsp_os', 'dnsp_od', 'ap'
                ]);

                $diagnostics = $request->input('diagnostics');

                $input['is_prescription'] = !empty($request->input('is_prescription')) ? 1 : 0;

                $graduation_card = GraduationCard::findOrFail($id);

                # Clone record before action
                $graduation_card_old = clone $graduation_card;

                $graduation_card->fill($input);

                if ($graduation_card->is_prescription == 1) {
                    $graduation_card->optometrist = null;
                }

                DB::beginTransaction();
                // Saves graduation card
                $graduation_card->save();

                # Store binnacle
                $user_id = $request->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'update', $graduation_card_old, $graduation_card);

                // Saves diagnostics
                if (!empty($diagnostics)) {
                    GraduationCardHasDiagnostic::where('graduation_card_id', $id)->forceDelete();

                    foreach ($diagnostics as $diag) {
                        $diagnostic = new GraduationCardHasDiagnostic;
                        $diagnostic->graduation_card_id = $graduation_card->id;
                        $diagnostic->diagnostic_id = $diag;
                        $diagnostic->save();
                    }
                } else {
                    GraduationCardHasDiagnostic::where('graduation_card_id', $id)->forceDelete();
                }
                DB::commit();

                $output = ['success' => true,
                    'msg' => __("graduation_card.updated_success")
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GraduationCard  $graduationCard
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('graduation_card.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $graduation_card = GraduationCard::findOrFail($id);

                # Clone record before action
                $graduation_card_old = clone $graduation_card;

                $graduation_card->delete();

                # Store binnacle
                $user_id = request()->session()->get('user.id');

                $this->util->registerBinnacle($user_id, $this->module_name, 'delete', $graduation_card_old);

                $output = ['success' => true,
                    'msg' => __("graduation_card.deleted_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }


    public function createOrder()
    {
        if (!auth()->user()->can('graduation_card.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Data for form
        $business_id = request()->session()->get('user.business_id');
        $patients = Patient::forDropdown($business_id);
        $employees = Employees::forDropdown($business_id);
        $diagnostics = Diagnostic::where('business_id', $business_id)
            ->orderBy('name')
            ->get();

        return view('orders.create')
            ->with(compact('patients', 'employees', 'diagnostics'));
    }
}
