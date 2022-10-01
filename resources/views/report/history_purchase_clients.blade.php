@extends('layouts.app')

@section('title', __('report.history_purchase'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('Reporte de productos comprados por clientes')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-report" data-toggle="tab">@lang('accounting.report')</a></li>
                </ul>
            </div>

            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab-report">
                        {{-- <h4>@lang('accounting.report')</h4> --}}
                        {!! Form::open(['id' => 'form_taxpayer', 'action' => 'ReporterController@getHistoryPurchaseClientsReport', 'method' => 'post', 'target' => '_blank']) !!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="msj-errors" class="alert alert-danger alert-dismissible" role="alert"
                                    style="display: none;">
                                    <strong id="msj"></strong>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('transaction_date', __('accounting.from') . ':') !!} <span style="color: red;"><small>*</small></span>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" value="{{ @format_date('now') }}" name="initial_date" readonly
                                            required id="initial_date" required class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('transaction_date', __('accounting.to') . ':') !!} <span style="color: red;"><small>*</small></span>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" value="{{ @format_date('now') }}" name="final_date" readonly
                                            required id="final_date" required class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('Customer', __('Cliente') . ':') !!} 
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user-circle"></i>
                                        </span>
                                        {!! Form::select('customer_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width:100%', 'id' => 'customer_id']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('Products', __('Producto') . ':') !!} <span style="color: red;"><small>*</small></span>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user-circle"></i>
                                        </span>
                                        {!! Form::select('product_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width:100%', 'id' => 'product_id', 'required']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('accounting.format')</label>
                                    <select name="report_type" id="report_type" class="form-control select2"
                                        style="width: 100%" required>
                                        <option value="pdf" selected>PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4" style="margin-top: 22px;">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-success" value="@lang('accounting.generate')"
                                        id="button_report">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('input#initial_date, input#final_date').datetimepicker({
                format: moment_date_format,
                ignoreReadonly: true
            });

            $('select#customer_id').select2();

            $('#customer_id').select2({
                ajax: {
                    url: '/customers/get_only_customers',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 2,
                escapeMarkup: function(m) {
                    return m;
                },
                templateResult: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    var html = (data.text || data.business_name) +
                        ` (<b>${LANG.code}: </b> ${data.id})`;
                    return html;
                },
                templateSelection: function(data) {
                    if (!data.id) {
                        // $('#supplier_name').val('');
                        return data.text;
                    }
                    // If it's a new supplier
                    if (!data.id) {
                        return data.text;
                        // If a provider has been selected
                    } else {
                        return data.business_name || data.text;
                    }
                },
            });
            $('#product_id').select2({
                ajax: {
                    url: '/products/get_only_products',
                    dataType: 'json',
                    delay: 500,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 2,
                escapeMarkup: function(m) {
                    return m;
                },
                templateResult: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    var html = data.text + ` (<b>SKU: </b> ${data.sku})`;
                    return html;
                },
                templateSelection: function(data) {
                    if (!data.id) {
                        // $('#supplier_name').val('');
                        return data.text;
                    }
                    // If it's a new supplier
                    if (!data.id) {
                        return data.text;
                        // If a provider has been selected
                    } else {
                        return data.text || data.sku;
                    }
                },
            });
            $('#button_report').attr('disabled', true);
        });
        $('select#product_id').on('change', function () {
            if($(this).val() != null){
                $('#button_report').attr('disabled', false);
            }
        })
    </script>
@endsection
