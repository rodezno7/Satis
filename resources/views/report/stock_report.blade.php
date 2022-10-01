@extends('layouts.app')
@section('title', __('report.stock_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.stock_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" id="accordion">
              <div class="box-header with-border">
                <h3 class="box-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                    <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                  </a>
                </h3>
              </div>
              <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                <div class="box-body">
                  {!! Form::open(['id'=>'form_stock_report', 'action' => 'ReportController@postStockReport', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        {{-- location_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                            </div>
                        </div>

                        {{-- warehouse_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('warehouse_id', __('warehouse.warehouse') . ':') !!}
                                {!! Form::select('warehouse_id', $warehouses, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'warehouse_id']); !!}
                            </div>
                        </div>

                        {{-- contact_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('contact_id', __('purchase.supplier') . ':') !!}
                                {!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('accounting.all'), 'id' => 'contact_id']); !!}
                            </div>
                        </div>

                        {{-- start and end --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                                        <span>
                                            <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                        </span>
                                        <i class="fa fa-caret-down" style="margin-left: 3px;"></i>
                                    </button>
                                </div>
                                {!! Form::hidden('start_date', null, ['id' => 'start_date']) !!}
                                {!! Form::hidden('end_date', null, ['id' => 'end_date']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- brand --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('brand_id', __('product.brand') . ':') !!}
                                {!! Form::select('brand_id', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'brand_id']); !!}
                            </div>
                        </div>

                        {{-- category --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('category_id', __('category.category') . ':') !!}
                                {!! Form::select('category_id', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                            </div>
                        </div>

                        {{-- sub_category --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                                {!! Form::select('sub_category_id', array(), null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_category_id']); !!}
                            </div>
                        </div>  

                        {{-- unit --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('unit_id',__('product.unit') . ':') !!}
                                {!! Form::select('unit_id', $units, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>@lang('accounting.format')</label>
                                <select name="report_type" id="report_type" class="form-control select2" style="width: 100%" required>
                                    <option value="pdf" selected>PDF</option>
                                    <option value="excel">Excel</option>
                                </select>                       
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>@lang('accounting.size_font')</label>
                            <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                                <option value="7">7</option>
                                <option value="8" selected>8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div class="col-sm-3" style="margin-top: 25px;">
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="@lang('accounting.generate')" id="button_report">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>
                                    {!! Form::checkbox('stock_filter', 1, null, ['class' => 'input-icheck', 'id' => 'stock_filter']); !!}
                                    <strong>@lang('report.show_produts_out_stock')</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                    <table id="show_stock_report_table" class="table table-striped table-text-center" width="100%">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>@lang('business.product')</th>
                                <th>@lang('category.category')</th>
                                <th>@lang('product.sub_category')</th>
                                <th>@lang('brand.brand')</th>
                                <th>@lang('report.unit_cost')</th>
                                <th>@lang('sale.unit_price')</th>
                                <th>@lang('report.current_stock')</th>
                                <th>@lang('report.vld_stock')</th>
                                <th>@lang('report.value_total')</th>
                                <th>@lang('report.total_unit_sold')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray text-center footer-total">
                                <td colspan="7"><strong>@lang('sale.total')</strong></td>
                                <td id="footer_total_stock" style="font-weight: bold;"></td>
                                <td id="footer_total_vld_stock" style="font-weight: bold;"></td>
                                <td id="footer_total_value" style="font-weight: bold;"></td>
                                <td id="footer_total_sold" style="font-weight: bold;"></td>
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
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready(function() {
        // Date filter
        $('#date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);

                show_stock_report_table.ajax.reload();
            }
        );

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            show_stock_report_table.ajax.reload();
        });

        var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

        $('#start_date').val(start_date);
        $('#end_date').val(end_date);
    });
</script>
@endsection