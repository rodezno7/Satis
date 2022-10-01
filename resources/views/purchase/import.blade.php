@extends('layouts.app')
@section('title', __('purchase.import_purchases'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('purchase.import_purchases')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-sm-12">
                <div class="box box-solid box-default">

                    <div class="box-body">
                        {!! Form::open(['url' => action('PurchaseController@postImportPurchases'), 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'send_form']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
                                    <select name="tax_id" id="tax_id" class="form-control select2"
                                        placeholder="{{ __('messages.please_select') }}" required>
                                        <option value="0" selected>@lang('lang_v1.none')</option>
                                        @foreach ($tax_groups as $tg)
                                            <option value="{{ $tg->id }}"> {{ $tg->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __('purchase.discount_type') . ':') !!}
                                    {!! Form::select('discount_type', ['' => __('lang_v1.none'), 'fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], '', ['class' => 'form-control select2', 'id' => 'discount_type']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_amount', __('purchase.discount_amount') . ':') !!}
                                    <input type="text" name="discount_amount" value="0" id="discount_amount"
                                        class="form-control input_number">
                                </div>
                            </div>
                        </div>
                        <div class="row"></div>
                        <div class="row">
                            {{-- Input --}}
                            {{-- <div class="col-sm-6" style="margin-bottom: 5px;"> --}}
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('name', __('product.file_to_import') . ':') !!}
                                    {!! Form::file('purchases_csv', ['accept' => '.csv', 'required']) !!}
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <br>
                                {{-- Button --}}
                                <button type="submit" class="btn btn-primary"
                                    id="submit_send">@lang('messages.submit')</button>

                                {{-- TODO: Loader --}}
                                <div id="loading" style="display: none; margin-left: 5px;">
                                    <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                                    @lang('accounting.wait_please')
                                </div>
                            </div>
                            {{-- </div> --}}
                        </div>
                        {!! Form::close() !!}

                        {{-- Download template --}}
                        <div class="row" style="margin-top: 5%;">
                            <div class="col-sm-4">
                                <a href=" @if (auth()->user()->language == 'es' ||
                                    auth()->user()->language == 'en') {{ asset('uploads/files/import_purchase_csv_template_' . auth()->user()->language . '.csv') }}
                                @else
                                    {{ asset('uploads/files/import_purchase_csv_template_en.csv') }} @endif
                                    " class="btn btn-success" download>
                                    <i class="fa fa-download"></i> @lang('product.download_csv_file_template')
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Errors --}}
        @if (isset($flag) && isset($exception))
            @if (!$flag)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-solid box-danger">
                            <div class="box-header">
                                <h3 class="box-title">@lang('accounting.errors')</h3>
                            </div>

                            <div class="box-body">
                                <table class="table table-striped" id="errors_table">
                                    <tr>
                                        <th>@lang('purchase.row_number')</th>
                                        <th>@lang('accounting.description')</th>
                                    </tr>
                                    @foreach ($errors as $error)
                                        <tr>
                                            <td>{{ $error['row'] }}</td>
                                            <td>{{ $error['msg'] }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Process purchase --}}
            @else
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-solid box-success">
                            <div class="box-header">
                                <h3 class="box-title">@lang('purchase.process_purchase')</h3>
                            </div>

                            <div class="box-body">
                                {!! Form::open(['url' => action('PurchaseController@importPurchases'), 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'import_form']) !!}

                                <div class="col-sm-12">
                                    <p><strong>@lang('purchase.net_total_amount'):</strong>
                                        ${{ @num_format($total_before_tax) }}</p>
                                    <p><strong>@lang('purchase.discount'):</strong> ${{ @num_format($discount_general) }}
                                    </p>
                                    <p><strong>@lang('purchase.purchase_tax'):</strong> ${{ @num_format($tax_amount) }}
                                    </p>
                                    {{-- <p><strong>@lang('purchase.additional_shipping_charges'):</strong>
                                        ${{ @num_format($shipping_charges) }}</p> --}}
                                    <p><strong>@lang('purchase.purchase_total'):</strong>
                                        ${{ @num_format($final_total) }}
                                    </p>
                                </div>

                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success"
                                            id="submit_import">@lang('purchase.import')</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Instructions --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-solid box-default">
                    <div class="box-header">
                        <h3 class="box-title">@lang('lang_v1.instructions')</h3>
                    </div>

                    <div class="box-body">
                        <strong>@lang('lang_v1.instruction_line1')</strong><br><br>
                        <ol style="margin-left: 20px;">
                            <li style="list-style-type: decimal;">@lang('purchase.instruction_line_1').</li>
                            <li style="list-style-type: decimal;">@lang('purchase.instruction_line_2').</li>
                            <li style="list-style-type: decimal;">@lang('purchase.instruction_line_3').</li>
                            <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line2'):</li>
                        </ol>
                        <br>

                        <table class="table table-striped">
                            <tr>
                                <th style="width: 15%;">@lang('lang_v1.col_no')</th>
                                <th style="width: 25%;">@lang('lang_v1.col_name')</th>
                                <th style="width: 60%;">@lang('lang_v1.instruction')</th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>
                                    SKU<br><small class="text-muted">(@lang('lang_v1.required'))</small></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>@lang('business.product')<br><small
                                        class="text-muted">(@lang('lang_v1.optional'))</small></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>@lang('purchase.purchase_quantity')<br><small
                                        class="text-muted">(@lang('lang_v1.required'))</small></td>
                                <td>@lang('purchase.purchase_quantity_greater_than_zero')</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>@lang('Costo unitario')<br><small class="text-muted">(@lang('lang_v1.required'))</small>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>@lang('purchase.supplier')<br><small
                                        class="text-muted">(@lang('lang_v1.required'))</small></td>
                                <td>@lang('purchase.supplier_instruction')</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>@lang('document_type.title')<br><small
                                        class="text-muted">(@lang('lang_v1.required'))</small></td>
                                <td>@lang('purchase.document_type_instruction')</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>@lang('purchase.ref_no')<br><small
                                        class="text-muted">(@lang('lang_v1.optional'))</small></td>
                                <td>@lang('purchase.ref_no_instruction')</td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>@lang('purchase.purchase_date')<br><small
                                        class="text-muted">(@lang('lang_v1.required'))</small></td>
                                <td>@lang('purchase.purchase_date_instruction')</td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>@lang('purchase.purchase_status')<br><small
                                        class="text-muted">(@lang('lang_v1.required'))</small></td>
                                <td>@lang('purchase.purchase_status_instruction')</td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td>@lang('warehouse.warehouse')<br><small
                                        class="text-muted">(@lang('lang_v1.required'))</small></td>
                                <td>@lang('purchase.warehouse_instruction')</td>
                            </tr>
                            <tr>
                                <td>11</td>
                                <td>@lang('purchase.additional_notes')<br><small
                                        class="text-muted">(@lang('lang_v1.optional'))</small></td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->

@endsection

{{-- @section('javascript')
<script>
  $(document).ready(function() {
    $("#import_form").on("submit", function(e) {
    //$(document).on('submit', 'form#import_form', function(e) {
        e.preventDefault();
        //var form = document.getElementById("import_form");

        //var data = new FormData(document.getElementById("import_form"));
        //var data = new FormData($(this));
        var data = $(this).serialize();

        $('#submit_import').attr('disabled', true);

        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            dataType: "html",
            data: data,
            success: function(result) {
                if (result.success === true) {
                    if (result.status === true) {
                        alert('exito');
                    } else {
                        $('#errors_div').show();
                        result.error_msg.forEach(item => {
                            var new_tr = '<tr>' +
                                '<td>' + item['row'] + '</td>' +
                                '<td>' + item['msg'] + '</td>' +  
                                '<tr>'; 
                            
                            $('#list').append(new_tr);
                        });
                    }
                } else {
                    Swal.fire
                    ({
                        title: ""+result.msg+"",
                        icon: "error",
                    });
                }
                $('#submit_import').attr('disabled', false);
            }
        });
    });
  });
</script>
@endsection --}}
