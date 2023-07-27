@extends('layouts.app')

@section('title', __('cxc.collections'))

@section('css')
    <style>
        .text-right { text-align: right; }
    </style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('cxc.collections')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        {!! Form::open(['id' => 'collections_report_form', 'action' => 'ReportController@postCollections', 'method' => 'post', 'target' => '_blank']) !!}
                        <div class="row">
                            @if (count($locations) > 2)
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('location', __('business.location') . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-map-marker"></i>
                                        </span>
                                        {!! Form::select('location', $locations, null, ['class' => 'form-control select2 location', 'style' => 'width: 100%;']) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if (count($sellers) > 1)
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label(__("sale.seller")) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user-circle"></i>
                                        </span>
                                        {!! Form::select("seller", $sellers, null, ["class" => "form-control select2",
                                            'id' => 'seller', 'placeholder' => __('sale.all_sellers')]) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                                        <span>
                                        <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                        </span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                    {!! Form::hidden("start_date", date('Y-m-d', strtotime('- 6 days')), ['id' => 'start_date']) !!}
                                    {!! Form::hidden("end_date", date('Y-m-d'), ['id' => 'end_date']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-file-excel-o"></i> @lang('accounting.generate')</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="collections_table">
                                <thead>
                                    <tr>
                                        <th>FECHA</th>
                                        <th>DOC.</th>
                                        <th style="width: 27%; text-align: center;">{{ strtoupper(__('customer.customer')) }}</th>
                                        <th>PRODUCTO</th>
                                        <th style="min-width: 10%;">CANTIDAD</th>
                                        <th style="min-width: 10%;">PRECIO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('javascript')
    <script>
    $(function () {
        //Stock Adjustment Report
        if($('button#date_filter').length == 1){
            dateRangeSettings['startDate'] = moment().subtract(6, 'days');
            dateRangeSettings['endDate'] = moment();

            $('button#date_filter').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('button#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    collections.ajax.reload();

                    let start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    let end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    $("input#start_date").val(start_date);
                    $("input#end_date").val(end_date);
                }
            );
            $('button#date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('button#date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
                collections.ajax.reload();

                let start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                let end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                $("input#start_date").val(start_date);
                $("input#end_date").val(end_date);
            });
        }

        let collections = $('table#collections_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[2, 'asc']],
            ajax: {
                url: "/collections",
                data: function (d) {
                    d.start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.location = $("select#location").val();
                    d.seller = $("select#seller").val();
                }
            },
            columns: [
                { data: 'transaction_date' },
                { data: 'correlative' },
                { data: 'customer' },
                { data: 'product' },
                { data: 'quantity', class: 'text-right' },
                { data: 'price_inc_tax', class: 'text-right' }
            ],
            fnDrawCallback: function (oSettings) {
                __currency_convert_recursively($('table#collections_table'));
            }
        });

        $('select#seller').on('change', function() {
            collections.ajax.reload();
        });
    });
    </script>
@endsection