@extends('layouts.app')

@section('title', __('kardex.kardex'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('kardex.kardex')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    {{-- View costs --}}
    <input type="hidden" id="view_costs" value="{{ auth()->user()->can('kardex.view_costs') }}">

    <div class="box">
        {{-- Business start date --}}
        <input type="hidden" id="business-start-date" value="{{ $business->start_date }}">

        <div class="box-header">
            <h3 class="box-title">@lang('kardex.inputs_and_outputs')</h3>
        </div>
        
        <div class="box-body">
            {{-- Form --}}
            {!! Form::open(['url' => action('KardexController@generateReport'),
                'method' => 'post', 'id' => 'generate_report_form', 'target' => '_blank']) !!}

            <div class="row">
                {{-- warehouse --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("warehouse", __("messages.location") . ":") !!}
                        {!! Form::select("warehouse", $warehouses, null,
                            ["class" => "form-control select2", "id" => "warehouse", "placeholder" => __("messages.please_select"), "required"]) !!}
                    </div>
                </div>

                {{-- product --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label("product", __("kardex.product") . ":") !!}
                        {!! Form::select("product", [], null,
                            ["class" => "form-control select2", "id" => "product", "placeholder" => __("messages.please_select"), "required", 'disabled']) !!}
                    </div>
                </div>

                {{-- start and end --}}
                <div class="col-sm-3">
                    <div class="form-group" style="display: inline-block;">
                        <div class="input-group" style="display: inline-block;">
                            <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                                <span>
                                    <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                </span>
                                <i class="fa fa-caret-down" style="margin-left: 3px;"></i>
                            </button>
                        </div>
                    </div>

                    {!! Form::hidden('start_date', null, ['id' => 'start_date']) !!}
                    {!! Form::hidden('end_date', null, ['id' => 'end_date']) !!}
                </div>
            </div>

            <div class="row">
                {{-- size --}}
                <div class="col-sm-3">
                    <label>@lang('accounting.size_font')</label>
                    <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                        <option value="7">7</option>
                        <option value="8" selected>8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>

                {{-- format --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>@lang('accounting.format')</label>
                        <select name="report_type" id="report_type" class="form-control select2" style="width: 100%" required>
                            <option value="pdf" selected>PDF</option>
                            <option value="excel">Excel</option>
                        </select>                       
                    </div>
                </div>

                {{-- report button --}}
                <div class="col-sm-3" style="margin-top: 24px;">
                    <div class="form-group">
                        <button type="submit" id="btn_report" class="btn btn-success">
                            @lang('accounting.generate')
                        </button>
                    </div>
                </div>

                {{-- action buttons --}}
                <div class="col-sm-3">
                    {{-- refresh button --}}
                    @can('kardex.update_balance')
                        <div class="form-group" style="display: inline-block;">
                            <button type="button" id="btn-refresh" title data-toggle="tooltip" data-original-title="{{ __('kardex.refresh_balance') }}"
                                class="btn btn-success" style="margin-top: 25px; margin-left: 10px;">
                                <i class="fa fa-refresh"></i>
                            </button>
                        </div>
                    @endcan

                    {{-- generate button --}}
                    @can('kardex.generate_product_kardex')
                        <div class="form-group" style="display: inline-block;">
                            <button type="button" id="btn-generate" title data-toggle="tooltip" data-original-title="{{ __('kardex.generate_product_kardex') }}"
                                class="btn btn-success" style="margin-top: 25px; margin-left: 10px;">
                                <i class="fa fa-exchange"></i>
                            </button>
                        </div>
                    @endcan
                </div>
            </div>
            {!! Form::close() !!}

            @can('kardex.view')
            <div class="table-responsive" style="margin-top: 10px;">
            <table class="table table-bordered table-striped table-text-center ajax_view" id="kardex_table" width="100%">
                <thead>
                    <tr>
                        <th>@lang('kardex.date')</th>
                        <th>@lang('kardex.transaction')</th>
                        <th>@lang('kardex.type')</th>
                        <th>@lang('kardex.reference')</th>
                        <th>@lang('kardex.initial_stock')</th>
                        <th>@lang('kardex.input')</th>
                        <th>@lang('kardex.output')</th>
                        <th>@lang('kardex.final_stock')</th>
                        @if (auth()->user()->can('kardex.view_costs'))
                            <th>@lang('kardex.input_cost')</th>
                            <th>@lang('kardex.output_cost')</th>
                            <th>@lang('kardex.balance')</th>
                        @endif
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        let initial_time = $('input#business-start-date').val()
        let current_time = new Date();

        let initial_start_date = initial_time.substr(0, 4) + '-01-01';
        let initial_end_date = current_time.getFullYear() + '-12-31';

        // Date filter
        $('#date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                kardex_table.ajax.reload();

                var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);
            }
        );

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            kardex_table.ajax.reload();
        });

        $('#date_filter').data('daterangepicker').setStartDate(moment(initial_start_date, 'YYYY-MM-DD'));
		$('#date_filter').data('daterangepicker').setEndDate(moment(initial_end_date, 'YYYY-MM-DD'));

        $('#start_date').val(initial_start_date);
        $('#end_date').val(initial_end_date);

        // Datatable
        let view_costs = $('#view_costs').val();

        let table_columns = [];

        // console.log('aaa ' + columns);
        if (view_costs == 1) {
            table_columns = [
                { data: 'date_time', name: 'date_time' },
                { data: 'movement_type', name: 'movement_types.name' },
                { data: 'type', name: 'movement_types.type' },
                { data: 'reference', name: 'reference' },
                { data: 'initial_stock', name: 'balance' },
                { data: 'inputs_quantity', name: 'inputs_quantity' },
                { data: 'outputs_quantity', name: 'outputs_quantity' },
                { data: 'balance', name: 'balance' },
                { data: 'total_cost_inputs', name: 'total_cost_inputs' },
                { data: 'total_cost_outputs', name: 'total_cost_outputs' },
                { data: 'balance_cost', name: 'balance' },
                { data: 'action', name: 'action', searchable: false, orderable: false }
            ];

        } else {
            table_columns = [
                { data: 'date_time', name: 'date_time' },
                { data: 'movement_type', name: 'movement_types.name' },
                { data: 'type', name: 'movement_types.type' },
                { data: 'reference', name: 'reference' },
                { data: 'initial_stock', name: 'balance' },
                { data: 'inputs_quantity', name: 'inputs_quantity' },
                { data: 'outputs_quantity', name: 'outputs_quantity' },
                { data: 'balance', name: 'balance' },
                { data: 'action', name: 'action', searchable: false, orderable: false }
            ];
        }

        kardex_table = $('#kardex_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'asc']],
            'ajax': {
                'url': '/kardex',
                'data': function (d) {
                    var start = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.warehouse_id = $("select#warehouse").val();
                    d.variation_id = $("select#product").val();
                    d.start_date = start;
                    d.end_date = end;
                }
            },
            columns: table_columns
        });

        // Warehouse filter
        $('select#warehouse').on('change', function() {
            kardex_table.ajax.reload();
            $('select#product').attr('disabled', false);
        });

        // Product filter
        $('select#product').on('change', function() {
            kardex_table.ajax.reload();
        });

        // Search product
        $('#product').select2({
            ajax: {
                url: '/kardex/products/list',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function(m) {
                return m;
            },
            templateResult: function(data) {
                var string;
                if (data.variation != null) {
                    string = data.text;
                    if (data.type == 'variable') {
                        string += ' - ' + data.variation;
                        string += ' (' + data.sub_sku + ')';
                    } else {
                        string += ' (' + data.sku + ')';
                    }
                } else {
                    string = data.text;
                }
                return string;
            },
            templateSelection: function(data) {
                var string;
                if (data.variation != null) {
                    string = data.text;
                    if (data.type == 'variable') {
                        string += ' - ' + data.variation;
                        string += ' (' + data.sub_sku + ')';
                    } else {
                        string += ' (' + data.sku + ')';
                    }
                } else {
                    string = data.text;
                }
                return string;
            },
        });

        // Refresh balance and datatable
        $(document).on('click', 'button#btn-refresh', function() {
            $.ajax({
                method: 'GET',
                url: '/kardex/refresh-balance/' + $('select#warehouse').val() + '/' + $('select#product').val(),
                success: function(res) {
                    if (res.success === true) {
                        kardex_table.ajax.reload();
                    } else {
                        Swal.fire({
                            title: res.msg,
                            icon: 'error'
                        });
                    }
                }
            });
        });

        // On click of btn-generate button
        $(document).on('click', 'button#btn-generate', function () {
            let icon = $(this).find('i');

            Swal.fire({
                title: LANG.sure,
                text: LANG.generate_product_kardex_alert,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelmButtonText: LANG.cancel,
                confirmButtonText: LANG.yes
            }).then((result) => {
                if (result.isConfirmed) {
                    icon.removeClass('fa-exchange').addClass('fa-spinner fa-pulse');

                    $.ajax({
                        type: 'post',
                        url: '/kardex/generate-product-kardex',
                        data: {
                            variation_id : $('select#product').val(),
                            warehouse_id : $('select#warehouse').val()
                        },
                        dataType: 'json',
                        success: function (res) {
                            icon.removeClass('fa-spinner fa-pulse').addClass('fa-exchange');

                            if (res.success === 1) {
                                kardex_table.ajax.reload();

                                Swal.fire({
                                    title: res.msg,
                                    icon: 'success',
                                });

                            } else {
                                Swal.fire({
                                    title: res.msg,
                                    icon: 'error',
                                });
                            }
                        }
                    })
                }
            })
        });
    });
</script>
@endsection