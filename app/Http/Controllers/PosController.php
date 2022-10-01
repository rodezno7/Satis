<?php

namespace App\Http\Controllers;

use App\Pos;
use App\Bank;
use App\BankAccount;
use Datatables;
use App\Employees;
use App\BusinessLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('pos.view')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $pos = Pos::join('bank_accounts as ba', 'pos.bank_account_id', 'ba.id',)
                ->join('business', 'business.id', '=', 'pos.business_id')
                ->leftJoin('employees', 'employees.id', '=', 'pos.employee_id')
                ->join('business_locations as bl', 'bl.id', '=', 'pos.location_id')
                ->select([
                    'pos.id',
                    'pos.name',
                    'pos.description',
                    'pos.brand',
                    'employees.first_name as firstname',
                    'ba.name as bank_name',
                    'bl.name as business_name',
                    'pos.status'
                ]);

            return DataTables::of($pos)
                ->addColumn(
                    'actions',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        if (auth()->user()->can('pos.update')) {
                            $html .= '<li><a href="#" data-href="' . action('PosController@edit', [$row->id]) . '" class="edit_pos_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if ($row->status == 'active') {
                            $html .= '<li><a href="#" onclick="anulPos(' . $row->id . ')"><i class="fa fa-lock" aria-hidden="true"></i> ' . __("payment.pos_anull") . '</a></li>';
                        } else {
                            $html .= '<li><a href="#" onclick="activePos(' . $row->id . ')"><i class="fa fa-check-square-o" aria-hidden="true"></i> ' . __("payment.pos_active") . '</a></li>';
                        }

                        if (auth()->user()->can('pos.delete')) {
                            $html .= '<li><a href="#" onclick="deletePos(' . $row->id . ')"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->editColumn(
                    'status',
                    '<i class="fa fa-circle aria-hidden="true" style="color:{{$status == \'inactive\' ? \'red\'  : \'rgb(50, 243, 50)\'}};"><span></span></i>'
                )
                ->removeColumn('id')
                ->rawColumns(['actions', 'status'])
                ->toJson();
        } else {
            return view('pos.index');
        }
    }

    public function create()
    {
        $business_id = Auth::user()->business_id;
        $bank_accounts = BankAccount::pluck('name', 'id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $status = ['active' => 'activo', 'inactive' => 'inactivo'];
        $employees = Employees::where('business_id', $business_id)->pluck('first_name', 'id');
        return view('pos.create', compact('bank_accounts', 'business_locations', 'status', 'employees'));
    }

    public function show($id)
    {
        $pos = Pos::findOrFail($id);
        return view('pos.show', $pos);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('pos.create')) {
            abort(403, 'Unauthorized action.');
        }

        $auth_key = $request->input('authorization_key');

        $request->validate(
            [
                'name' => 'required',
                'bank_account_id' => 'required',
                'location_id' => 'required',
                'status' => 'required',
                'authorization_key' => 'required|between:4,4',
                'confirm_authorization_key' => 'required|between:4,4|in:' . $auth_key,
            ],
            [
                'name.required' => trans('paymet.pos_name_required'),
                'bank_account_id.required' => trans('payment.pos_bank_required'),
                'location_id.required' => trans('payment.pos_location_required'),
                'status.required' => trans('payment.pos_status_required'),
                'authorization_key.required' => trans('card_pos.required_authorization_key'),
                'authorization_key.between' => trans('card_pos.characters_authorization_key'),
                'confirm_authorization_key.required' => trans('card_pos.required_confirm_authorization_key'),
                'confirm_authorization_key.between' => trans('card_pos.characters_required_confirm_authorization_key'),
                'confirm_authorization_key.in' => trans('card_pos.in_required_confirm_authorization_key'),
            ]
        );

        try {

            $pos = new Pos();
            $pos->name = trim($request->name);
            $pos->model = trim($request->model);
            $pos->brand = trim($request->brand);
            $pos->description = trim($request->description);
            $pos->bank_account_id = trim($request->bank_account_id);
            $pos->business_id = auth()->user()->business_id;
            $pos->location_id = trim($request->location_id);
            $pos->employee_id = trim($request->employee_id);
            $pos->status = trim($request->status);
            $pos->authorization_key = bcrypt(trim($request->authorization_key));
            $pos->save();

            $output = [
                'success' => true,
                'msg' => __("card_pos.success"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }

    public function edit($id)
    {
        $pos = Pos::find($id);
        $business_id = Auth::user()->business_id;
        $bank_accounts = BankAccount::pluck('name', 'id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $status = ['active' => 'activo', 'inactive' => 'inactivo'];
        $employees = Employees::where('business_id', $business_id)->pluck('first_name', 'id');
        return view('pos.edit', compact('pos', 'bank_accounts', 'business_locations', 'status', 'employees'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('pos.update')) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'name' => 'required',
            'bank_account_id' => 'required',
            'location_id' => 'required',
            'status' => 'required',
        ];

        $rules_msg = [
            'name.required' => trans('paymet.pos_name_required'),
            'bank_account_id.required' => trans('payment.pos_bank_required'),
            'location_id.required' => trans('payment.pos_location_required'),
            'status.required' => trans('payment.pos_status_required'),
        ];

        if ($request->input('authorization_key')) {
            $auth_key = $request->input('authorization_key');

            $rules['authorization_key'] = 'between:4,4';
            $rules['confirm_authorization_key'] = 'between:4,4|in:' . $auth_key;

            $rules_msg['authorization_key.between'] = trans('card_pos.characters_authorization_key');
            $rules_msg['confirm_authorization_key.between'] = trans('card_pos.characters_required_confirm_authorization_key');
            $rules_msg['confirm_authorization_key.in'] = trans('card_pos.in_required_confirm_authorization_key');
        }

        $request->validate($rules, $rules_msg);

        try {

            $pos = Pos::findOrFail($id);
            $pos->name = trim($request->name);
            $pos->model = trim($request->model);
            $pos->brand = trim($request->brand);
            $pos->description = trim($request->description);
            $pos->bank_account_id = trim($request->bank_account_id);
            $pos->business_id = auth()->user()->business_id;
            $pos->location_id = trim($request->location_id);
            $pos->employee_id = trim($request->employee_id);
            $pos->status = trim($request->status);

            if (! empty($request->authorization_key)) {
                $pos->authorization_key = bcrypt(trim($request->authorization_key));
            }

            $pos->update();

            $output = [
                'success' => true,
                'msg' => __("card_pos.update"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('pos.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $pos = Pos::findOrFail($id);
            $pos->delete();
            $output = [
                'success' => true,
                'msg' => __("payment.pos_delete"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }

    public function cancel($id)
    {
        if (!auth()->user()->can('pos.cancel')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $pos = Pos::findOrFail($id);
            $pos->status = 'inactive';
            $pos->update();
            $output = [
                'success' => true,
                'msg' => __("card_pos.cancel"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }

    public function activate($id)
    {
        if (!auth()->user()->can('pos.activate')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $pos = Pos::findOrFail($id);
            $pos->status = 'active';
            $pos->update();
            $output = [
                'success' => true,
                'msg' => __("card_pos.activate"),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }
}
