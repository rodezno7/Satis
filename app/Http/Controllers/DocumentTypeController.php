<?php

namespace App\Http\Controllers;

use App\DocumentClass;
use App\DocumentType;
use App\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Utils\TransactionUtil;

use DB;

class DocumentTypeController extends Controller
{
    protected $transactionUtil;
    /**
     * Constructor
     * @param TransactionUtil $transactionUtil;
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    public function index()
    {
        if (!auth()->user()->can('document_type.view') && !auth()->user()->can('document_type.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $category = DocumentType::where('business_id', $business_id)
                ->select([
                    'document_name', 'short_name', 'print_format', 'id',
                    DB::raw('IF(print_format != "", print_format, "none") as print_format'),
                    DB::raw('IF(is_active = 1, "yes", "no") as is_active'),
                    DB::raw('IF(tax_inc = 1, "yes", "no") as tax_inc'),
                    DB::raw('IF(tax_exempt = 1, "yes", "no") as tax_exempt'),
                    DB::raw('IF(is_document_sale = 1, "yes", "no") as is_document_sale'),
                    DB::raw('IF(is_document_purchase = 1, "yes", "no") as is_document_purchase'),
                    DB::raw('IF(is_return_document = 1, "yes", "no") as is_return_document'),
                    DB::raw('IF(is_default = 1, "yes", "no") as is_default')
                ]);

            return Datatables::of($category)
                ->addColumn(
                    'action',
                    '@can("document_type.update")
                    <button data-href="{{action(\'DocumentTypeController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_documents_button" title=@lang("messages.edit")><i class="glyphicon glyphicon-edit"></i></button>
                        &nbsp;
                    @endcan
                    @can("document_type.delete")
                        <button data-href="{{action(\'DocumentTypeController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_documents_button" title=@lang("messages.delete")><i class="glyphicon glyphicon-trash"></i></button>
                    @endcan'
                )
                ->editColumn('print_format', '{{ __("document_type.".$print_format) }}')
                ->editColumn('is_active', '{{ __("messages.".$is_active) }}')
                ->editColumn('tax_inc', '{{ __("messages.".$tax_inc) }}')
                ->editColumn('tax_exempt', '{{ __("messages.".$tax_exempt) }}')
                ->editColumn('is_document_sale', '{{ __("messages.".$is_document_sale) }}')
                ->editColumn('is_document_purchase', '{{ __("messages.".$is_document_purchase) }}')
                ->editColumn('is_return_document', '{{ __("messages.".$is_return_document) }}')
                ->editColumn('is_default', '{{ __("messages.".$is_default) }}')
                ->removeColumn('id')
                ->rawColumns([10])
                ->make(false);
        }

        return view('document_types.index');
    }


    public function verifyDefault()
    {
        $default = DocumentType::where('is_default', 1)
            ->where('is_document_sale', 1)
            ->select('id', 'document_name')->first();
        return response()->json($default);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('document_type.create')) {
            abort(403, 'Unauthorized action.');
        }

        $print_formats = $this->transactionUtil->print_formats();
        $document_classes = DocumentClass::pluck('name', 'id');

        return view('document_types.create')
            ->with(compact("print_formats", "document_classes"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('document_type.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['document_name', 'short_name', 'max_operation', 'document_class_id', 'document_type_number']);
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['print_format'] = $request->input('print_format');
            $input['is_active'] = !empty($request->input('is_active')) ? 1 : 0;
            $input['tax_inc'] = !empty($request->input('tax_inc')) ? 1 : 0;
            $input['tax_exempt'] = !empty($request->input('tax_exempt')) ? 1 : 0;
            $input['is_document_sale'] = !empty($request->input('is_document_sale')) ? 1 : 0;
            $input['is_document_purchase'] = !empty($request->input('is_document_purchase')) ? 1 : 0;
            $input['is_return_document'] = !empty($request->input('is_return_document')) ? 1 : 0;
            $input['is_default'] = !empty($request->input('is_default')) ? 1 : 0;

            if ($request->get('document_id') != 0) {
                $doc =  DocumentType::find($request->get('document_id'));
                $doc->is_default = 0;
                $doc->update();
            }


            $documents = DocumentType::create($input);
            $output = [
                'success' => true,
                'data' => $documents,
                'msg' => __("document_type.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
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
        if (!auth()->user()->can('document_type.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $documents = DocumentType::where('business_id', $business_id)->find($id);
            $print_formats = $this->transactionUtil->print_formats();
            $document_classes = DocumentClass::pluck('name', 'id');

            return view('document_types.edit')->with(compact('documents', 'print_formats', 'document_classes'));
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
        if (!auth()->user()->can('document_type.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['document_name', 'short_name', 'print_format', 'max_operation', 'document_class_id', 'document_type_number']);
                $business_id = $request->session()->get('user.business_id');

                $doc_type = DocumentType::where('business_id', $business_id)->findOrFail($id);
                $doc_type->document_name  = $input['document_name'];
                $doc_type->short_name   = $input['short_name'];
                $doc_type->print_format   = $input['print_format'];
                $doc_type->is_active = !empty($request->input('is_active')) ? 1 : 0;
                $doc_type->tax_inc = !empty($request->input('tax_inc')) ? 1 : 0;
                $doc_type->tax_exempt = !empty($request->input('tax_exempt')) ? 1 : 0;
                $doc_type->is_document_purchase = !empty($request->input('is_document_purchase')) ? 1 : 0;
                $doc_type->is_document_sale = !empty($request->input('is_document_sale')) ? 1 : 0;
                $doc_type->is_return_document = !empty($request->input('is_return_document')) ? 1 : 0;
                $doc_type->is_default = !empty($request->input('is_default')) ? 1 : 0;
                $doc_type->max_operation = $input['max_operation'];
                $doc_type->document_class_id = $input['document_class_id'];
                $doc_type->document_type_number = $input['document_type_number'];

                if ($request->get('document_id') != 0) {
                    $doc =  DocumentType::find($request->get('document_id'));
                    $doc->is_default = 0;
                    $doc->update();
                }

                $doc_type->save();

                $output = [
                    'success' => true,
                    'msg' => __("document_type.updated_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('document_type.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {

                $business_id = request()->user()->business_id;

                $trans = Transaction::where('business_id', $business_id)
                    ->where('document_types_id', $id)
                    ->get();

                DB::beginTransaction();

                if (!count($trans)) {
                    $documenttype = DocumentType::where('business_id', $business_id)->findOrFail($id);
                    $documenttype->delete();

                    $output = [
                        'success' => true,
                        'msg' => __("document_type.deleted_success")
                    ];

                    DB::commit();
                } else {
                    $output = [
                        'success' => false,
                        'msg' => __("document_type.document_type_has_assoc_trans")
                    ];

                    DB::rollBack();
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }
}
