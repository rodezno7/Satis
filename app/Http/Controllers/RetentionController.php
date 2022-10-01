<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Transaction;
use App\Utils\TransactionUtil;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RetentionController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    /**
     * Constructor.
     *
     * @param  \App\TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('retentions.view') && ! auth()->user()->can('retentions.create')) {
            abort(403, "Unauthorized action.");
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $retentions = Transaction::leftJoin('customers', 'customers.id', 'transactions.customer_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'retention')
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.retention_type',
                    'transactions.ref_no',
                    'customers.name',
                    'transactions.additional_notes',
                    'transactions.final_total'
                );

            return Datatables::of($retentions)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '&nbsp;<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            @can("retentions.update")
                            <li>
                                <a href="#" data-href="{{ action(\'RetentionController@edit\', [$id]) }}"
                                    class="update_retention_button" data-container=".retention_modal">
                                    <i class="fa fa-edit"></i> @lang("messages.edit")
                                </a>
                            </li>
                            @endcan
                            @can("retentions.delete")
                            <li>
                                <a href="#" data-href="{{ action(\'RetentionController@destroy\', [$id]) }}"
                                    class="delete_retention_button">
                                    <i class="fa fa-trash"></i> @lang("messages.delete")
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>'
                )
                ->editColumn('transaction_date', '{{ @format_date($transaction_date) }}')
                ->editColumn('retention_type', '{{ __("retention." . $retention_type) }}')
                ->editColumn('final_total', function($row) {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->final_total . '</span>';
                    }
                )
                ->rawColumns(['action', 'final_total'])
                ->toJson();
        }

        return view('retention.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('retentions.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('retention.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('retentions.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input = $request->only([
                'transaction_date',
                'document_date',
                'customer_id',
                'retention_type',
                'ref_no',
                'serie',
                'additional_notes',
                'final_total'
            ]);

            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            $input['business_id'] = $business_id;
            $input['transaction_date'] = $this->transactionUtil->uf_date($input['transaction_date']);
            $input['document_date'] = $this->transactionUtil->uf_date($input['document_date']);
            $input['type'] = 'retention';
            $input['document_types_id'] = 0;
            $input['created_by'] = $user_id;

            $retention = Transaction::create($input);

            DB::commit();

            $output = [
                'success' => true,
                'data' => $retention,
                'msg' => __('retention.added_success')
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
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
        if (! auth()->user()->can('retentions.update')) {
            abort(403, 'Unauthorized action.');
        }

        $retention = Transaction::find($id);

        $select_customer = Customer::find($retention->customer_id)
            ->pluck('name', 'id');

        return view('retention.edit')->with(compact('retention', 'select_customer'));
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
        if (! auth()->user()->can('retentions.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $input = $request->only([
                    'transaction_date',
                    'document_date',
                    'customer_id',
                    'retention_type',
                    'ref_no',
                    'serie',
                    'additional_notes',
                    'final_total'
                ]);

                $input['transaction_date'] = $this->transactionUtil->uf_date($input['transaction_date']);
                $input['document_date'] = $this->transactionUtil->uf_date($input['document_date']);

                $retention = Transaction::find($id);
                $retention->fill($input);
                $retention->save();

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('retention.updated_success')
                ];

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
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
        if (! auth()->user()->can('retentions.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $retention = Transaction::find($id);

                $retention->delete();

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('retention.deleted_success')
                ];

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }
}
