@extends('layouts.app')
@section('title', __('report.sale_cost_product'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.sale_cost_product')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-body">
            {!! Form::open(['action' => 'ReportController@getSaleCostProductReport', 'method' => 'post', 'target' => '_blank']) !!}
            <div class="row">
                {{-- location --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!}
                        {!! Form::select('location', $locations, null, ['class' => 'form-control', 'id' => 'location',
                            'placeholder' => __('business.select_location')]) !!}
                    </div>
                </div>

                {{-- brand --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('brand', __('brand.brand')) !!}
                        {!! Form::select('brand', $brands, null, ['class' => 'form-control select2',
                            'style' => 'width: 100%;', 'placeholder' => __('brand.all_brands')]) !!}
                    </div>
                </div>

                {{-- Date range --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="input-group">
                        <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                            <span>
                            <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                            </span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                        {!! Form::hidden("start_date", date('Y-m-d'), ['id' => 'start_date']) !!}
                        {!! Form::hidden("end_date", date('Y-m-d'), ['id' => 'end_date']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-file-excel-o"></i> @lang('accounting.generate')
                        </button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="table-reponsive">
                        <table class="table table-bordered" id="sale_cost_product">
                            <thead>
                                <tr>
                                    <th class="text-center">SKU</th>
                                    <th class="text-center">{{ mb_strtoupper(__('product.product')) }}</th>
                                    <th class="text-center">{{ mb_strtoupper(__('sale.sells')) }}</th>
                                    <th class="text-center">{{ mb_strtoupper(__('sale.unit_cost')) }}</th>
                                    <th class="text-center">{{ mb_strtoupper(__('sale.total_cost')) }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-center">{{ mb_strtoupper(__('sale.total')) }}</th>
                                    <th class="text-right"><span class="display_currency"id="total_sale" data-currency_symbol="false" data-precision="0"></span></th>
                                    <th>&nbsp;</th>
                                    <th class="text-right"><span class="display_currency"id="total_cost" data-currency_symbol="true"></span></th>
                                </tr>
                            </tfoot>
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
            if ($('button#date_filter').length == 1) {
                dateRangeSettings['startDate'] = moment();
                dateRangeSettings['endDate'] = moment();
                //Date range as a button
                $('button#date_filter').daterangepicker(
                    dateRangeSettings,
                    function (start, end) {
                        $('button#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                        let start_date = $('button#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        let end_date = $('button#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        $("input#start_date").val(start_date);
                        $("input#end_date").val(end_date);

                        sale_cost_product_report.ajax.reload();
                    }
                );
                $('button#date_filter').on('cancel.daterangepicker', function (ev, picker) {
                    $('button#date_filter span').html('<i class="fa fa-calendar"></i> '+ LANG.filter_by_day);

                    $("input#start_date").val(moment().format('YYYY-MM-DD'));
                    $("input#end_date").val(moment().format('YYYY-MM-DD'));

                    sale_cost_product_report.ajax.reload();
                });
            }

            var sale_cost_product_report = $("table#sale_cost_product").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    method: "get",
                    url: "/reports/sale-cost-product-report",
                    data: function (d) {
                        d.start_date = $('input#start_date').val();
                        d.end_date = $('input#end_date').val();
                        d.location = $('select#location').val();
                        d.brand = $('select#brand').val();
                    },
                }, columns: [
                    { data: 'sku', name: 'sku' },
                    { data: 'product', name: 'product' },
                    { data: 'quantity', name: 'quantity', class: 'text-right' },
                    { data: 'cost', name: 'cost', class: 'text-right' },
                    { data: 'total', name: 'total_cost', class: 'text-right' }
                ],
                "fnDrawCallback": function (oSettings) {
                    $('span#total_sale').text(sum_table_col($('table#sale_cost_product'), 'quantity'));
                    $('span#total_cost').text(sum_table_col($('table#sale_cost_product'), 'total'));
                    __currency_convert_recursively($('table#sale_cost_product'));
                }
            });

            $('select#location, select#brand').on('change', function () {
                sale_cost_product_report.ajax.reload();
            });
        });
    </script>
@endsection