<?php

namespace App\Http\Controllers;

use App\Binnacle;
use App\User;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class BinnacleController extends Controller
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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('binnacle.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $binnacle = Binnacle::leftJoin('users', 'users.id', 'binnacles.user_id')
                ->where('business_id', $business_id)
                ->select(
                    'binnacles.created_at',
                    DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as user"),
                    'binnacles.module',
                    'binnacles.action',
                    'binnacles.old_record',
                    'binnacles.new_record',
                    // 'binnacles.entrie_number',
                    // 'binnacles.entrie_correlative',
                    // 'binnacles.account_code',
                    'binnacles.id'
                );
    
            # User filter
            if (request()->has('user_id')) {
                $user_id = request()->get('user_id');
    
                if (! empty($user_id)) {
                    if ($user_id != 'all') {
                        $binnacle->where('binnacles.user_id', $user_id);
                    }   
                }
            }
    
            # Date filter
            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $binnacle->whereDate('binnacles.created_at', '>=', $start)
                    ->whereDate('binnacles.created_at', '<=', $end);
            }

            return Datatables::of($binnacle)
                ->filterColumn('user', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->editColumn(
                    'module',
                    function ($row) {
                        if (is_null($row->module)) {
                            return '';
                        } else {
                            return __('binnacle.' . $row->module);
                        }
                    }
                )
                ->editColumn(
                    'action',
                    function ($row) {
                        if (is_null($row->action)) {
                            return '';
                        } else {
                            return __('binnacle.' . $row->action);
                        }
                    }
                )
                ->rawColumns(['module', 'action'])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return action('BinnacleController@show', [$row->id]) ;
                    }
                ])
                ->toJson();
        }

        # Users
        $users = User::allUsersDropdown($business_id, false);
        $users = $users->prepend(__('kardex.all'), 'all');

        return view('binnacle.index')
            ->with(compact('users'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('binnacle.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $binnacle = Binnacle::find($id);

            # Old record data
            $old_record = [];

            if (! empty($binnacle->old_record)) {
                foreach (json_decode($binnacle->old_record, true) as $key => $value) {
                    $old_record[$key] = $value;
                }
            }

            # New record data
            $new_record = [];

            if (! empty($binnacle->new_record)) {
                foreach (json_decode($binnacle->new_record, true) as $key => $value) {
                    $new_record[$key] = $value;
                }
            }

            return view('binnacle.show')->with(compact('binnacle', 'old_record', 'new_record'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Binnacle  $binnacle
     * @return \Illuminate\Http\Response
     */
    public function edit(Binnacle $binnacle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Binnacle  $binnacle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Binnacle $binnacle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Binnacle  $binnacle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Binnacle $binnacle)
    {
        //
    }
}
