@extends('layouts.app')
@section('title', __('home.home'))

@section('css')
    {!! Charts::styles(['highcharts']) !!}
    <style>
        /* Slider */
        .carousel-fade .carousel-inner .item {
            opacity: 0;
            transition-property: opacity;
        }

        .carousel-fade .carousel-inner .active {
            opacity: 1;
        }

        .carousel-fade .carousel-inner .active.left,
        .carousel-fade .carousel-inner .active.right {
            left: 0;
            opacity: 0;
            z-index: 1;
        }

        .carousel-fade .carousel-inner .next.left,
        .carousel-fade .carousel-inner .prev.right {
            opacity: 1;
        }

        .carousel-fade .carousel-control {
            z-index: 2;
        }

        /*
          WHAT IS NEW IN 3.3: "Added transforms to improve carousel performance in modern browsers."
          now override the 3.3 new styles for modern browsers & apply opacity
          */
        @media all and (transform-3d),
        (-webkit-transform-3d) {

            .carousel-fade .carousel-inner>.item.next,
            .carousel-fade .carousel-inner>.item.active.right {
                opacity: 0;
                -webkit-transform: translate3d(0, 0, 0);
                transform: translate3d(0, 0, 0);
            }

            .carousel-fade .carousel-inner>.item.prev,
            .carousel-fade .carousel-inner>.item.active.left {
                opacity: 0;
                -webkit-transform: translate3d(0, 0, 0);
                transform: translate3d(0, 0, 0);
            }

            .carousel-fade .carousel-inner>.item.next.left,
            .carousel-fade .carousel-inner>.item.prev.right,
            .carousel-fade .carousel-inner>.item.active {
                opacity: 1;
                -webkit-transform: translate3d(0, 0, 0);
                transform: translate3d(0, 0, 0);
            }
        }

        /* just for demo purpose */
        html,
        body,
        .carousel,
        .carousel-inner,
        .carousel-inner .item {
            height: 100%;
        }

        .item:nth-child(1) {
            background: blue;
        }

        .item:nth-child(2) {
            background: red;
        }

        .item:nth-child(3) {
            background: orange;
        }
    </style>
    <!-- <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css"> -->

@endsection

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header" style="margin-top: -20px;">
        <!-- <div class="row">
          <div class="col-md-12 col-md-offset-0 ">
           <img src="{{ asset('img/envex-erp-banner.png') }}" class="img-responsive display-block" alt="EnvexERPBanner" style="height: 100%; width: 100%;">
          </div>
         </div> -->
        <div class="row" style="@if ($images->isEmpty()) {{ 'display:none !important;' }} @endif">
            <div class="col-md-12">
                <div id="carousel1" class="carousel slide" data-ride="carousel">
                    <!-- <!– Indicatodores –> -->
                    <ol class="carousel-indicators">
                        @foreach ($images as $key => $image)
                            @if ($loop->first)
                                <li data-target="#carousel{{ $key }}" data-slide-to="{{ $key }}"
                                    class="active"></li>
                            @else
                                <li data-target="#carousel{{ $key }}" data-slide-to="{{ $key }}"
                                    class=""></li>
                            @endif
                        @endforeach
                    </ol>

                    <!-- <!– Contenedor de las imagenes –> -->
                    <div class="carousel-inner" role="listbox">
                        @foreach ($images as $image)
                            @if ($loop->first)
                                <div class="item active">
                                    <a href="{{ !is_null($image->link) ? $image->link : '#' }}"
                                        target="{{ !is_null($image->link) ? '_blank' : '' }}"><img
                                            src="{{ asset('uploads/slides/' . $image->path) }}"
                                            class="img-responsive display-block" alt="EnvexERPBanner"
                                            style="width: 100%;"></a>
                                </div>
                            @else
                                <div class="item">
                                    <a href="{{ !is_null($image->link) ? $image->link : '#' }}"
                                        target="{{ !is_null($image->link) ? '_blank' : '' }}"><img
                                            src="{{ asset('uploads/slides/' . $image->path) }}"
                                            class="img-responsive display-block" alt="EnvexERPBanner"
                                            style="width: 100%;"></a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <!-- <!– Controls –> -->
                    <a class="left carousel-control" href="#carousel1" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Anterior</span>
                    </a>
                    <a class="right carousel-control" href="#carousel1" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Siguiente</span>
                    </a>
                </div>
            </div>
        </div>

    </section>



    @if (auth()->user()->can('dashboard.data'))
        <!-- Main content -->
        <section class="content no-print">
            {{-- Business start date --}}
            <input type="hidden" id="business-start-date" value="{{ $business->start_date }}">

            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="btn-group pull-right" data-toggle="buttons">
                        {{-- Location --}}
                        <select name="business_location_id" class="btn btn-info" id="business_location_id"
                            style="height: 34px;">
                            <option value="0" selected>{{ __('home.all_locations') }}</option>
                            @foreach ($business_locations as $bl)
                                <option value="{{ $bl->id }}">{{ $bl->name }}</option>
                            @endforeach
                        </select>

                        {{-- Today --}}
                        <label class="btn btn-info active">
                            <input type="radio" name="date-filter" data-start="{{ date('Y-m-d') }}"
                                data-end="{{ date('Y-m-d') }}" checked> {{ __('home.today') }}
                        </label>

                        {{-- This week --}}
                        <label class="btn btn-info">
                            <input type="radio" name="date-filter" data-start="{{ $date_filters['this_week']['start'] }}"
                                data-end="{{ $date_filters['this_week']['end'] }}"> {{ __('home.this_week') }}
                        </label>

                        {{-- This month --}}
                        <label class="btn btn-info">
                            <input type="radio" name="date-filter"
                                data-start="{{ $date_filters['this_month']['start'] }}"
                                data-end="{{ $date_filters['this_month']['end'] }}"> {{ __('home.this_month') }}
                        </label>

                        {{-- This fiscal year --}}
                        <label class="btn btn-info">
                            <input type="radio" name="date-filter" data-start="{{ $date_filters['this_fy']['start'] }}"
                                data-end="{{ $date_filters['this_fy']['end'] }}"> {{ __('home.this_fy') }}
                        </label>

                        {{-- Choose month --}}
                        <label class="btn btn-info" data-toggle="modal" data-target="#choose_month_modal">
                            {{ __('home.choose_month') }}
                        </label>

                        {{-- Date range --}}
                        <label class="btn btn-info" id="range-date-filter">
                            <span>
                                {{ __('report.date_range') }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <br>

			<div class="row">
                {{-- Total sales --}}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                    <div class="small-box bg-blue">
                        <div class="inner">
                            <h3 class="info-box-number total_sell"><sup style="font-size: 20px"></sup></h3>
                            <p class="info-box-text" style="color: white">{{ __('home.total_sell') }}</p><br>
                        </div>
                        <div class="icon">
                            <i class="ion ion-ios-cart-outline"></i>
                        </div>
                        <a href="{{ action('SellController@index') }}" class="small-box-footer">
                            @lang('home.more_information') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <!-- /.col -->


                {{-- Total purchase --}}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                    <div class="small-box bg-light-blue">
                        <div class="inner">
                            <h3 class="total_purchase"></h3>
                            <p class="info-box-text" style="color: white">{{ __('home.total_purchase') }}</p><br>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ action('SellController@index') }}" class="small-box-footer">
                            @lang('home.more_information') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <!-- /.col -->

				{{-- Total expense --}}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3 class="info-box-number total_expense"></h3>
                            <p class="info-box-text" style="color: white">{{ __('home.total_expense') }}</p><br>
                        </div>
                        <div class="icon">
                            <i class="fa fa-minus-circle"></i>
                        </div>
						<a href="{{ action('ExpenseController@index') }}" class="small-box-footer">
                            @lang('home.more_information') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

				{{-- Gross Profit --}}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3 class="info-box-number gross_profit"></h3>
                            <p class="info-box-text" style="color: white; margin-bottom: 12px !important">{{ __('home.gross_profit') }}</p>
							<h3 class="info-box-number net_earnings"></h3>
                            <p class="info-box-text" style="color: white; margin: 0px !important">{{ __('home.net_earnings') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-money"></i>
                        </div>
                    </div>
                </div>

				{{-- Invoice due --}}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                    <div class="small-box bg-olive">
                        <div class="inner">
                            <h3 class="info-box-number invoice_due"></h3>
                            <p class="info-box-text" style="color: white">{{ __('home.invoice_due') }}</p><br>
                        </div>
                        <div class="icon">
                            <i class="fa fa-dollar"></i>
                        </div>
                        <a href="{{ action('CustomerController@indexBalancesCustomer') }}" class="small-box-footer">
                            @lang('home.more_information') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <!-- /.col -->

                {{-- Purchase due --}}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3 class="info-box-number purchase_due"></h3>
                            <p class="info-box-text" style="color: white">{{ __('home.purchase_due') }}</p><br>
                        </div>
                        <div class="icon">
                            <i class="ion ion-ios-paper-outline"></i>
                        </div>
                        <a href="{{ action('PurchaseController@debtsToPayReport') }}" class="small-box-footer">
                            @lang('home.more_information') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                {{-- Total stock --}}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                    <div class="small-box bg-orange">
                        <div class="inner">
                            <h3 class="info-box-number total_stock"></h3>
                            <p class="info-box-text" style="color: white">{{ __('home.total_stock') }}</p><br>
                        </div>
                        <div class="icon">
							<i class="fa fa-cube"></i>
						</div>
                    </div>
                </div>
                <!-- /.col -->

                {{-- Average sales --}}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 class="info-box-number average_sales"></h3>
                            <p class="info-box-text" style="color: white">{{ __('home.average_sales') }}</p><br>
                        </div>
                        <div class="icon">
                            <i class="ion ion-ios-cart-outline"></i>
                        </div>
                    </div>
                </div>
            </div>
            
			<br>
			@if (isset($dashboard_settings['sell_and_product']) && $dashboard_settings['sell_and_product'] == 1)
			<div class="row">
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">
                                {{ __('home.trending_products') }}
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('home.products') }}</th>
                                        <th>{{ __('home.amount') }}</th>
                                        <th>{{ __('home.total_sells1') }}</th>
                                        <th>{{ __('home.last_sale') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="trendingProducts-table"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">
								{{ __('home.sells') }}
							</h3>
						</div>
						<div class="box-body">
							{!! $sells_chart_line_1->html() !!}
						</div>
					</div>
                </div>
            </div>
			@endif


            {{-- Sales last 30 days --}}
            @if (isset($dashboard_settings['sales_month']) && $dashboard_settings['sales_month'] == 1)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">
                                    {{ __('home.sells_last_30_days') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                {!! $sells_chart_1->html() !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Sales current financial year --}}
            @if (isset($dashboard_settings['sales_year']) && $dashboard_settings['sales_year'] == 1)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">
                                    {{ __('home.sells_current_fy') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                {!! $sells_chart_2->html() !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Peak sales hours in the last 30 days --}}
            @if (isset($dashboard_settings['peak_sales_hours_month']) && $dashboard_settings['peak_sales_hours_month'] == 1)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">{{ __('home.peak_sales_hours_month') }}</h3>
                                <div class="box-tools">
                                    <strong>@lang('accounting.location'): &nbsp;</strong>
                                    @if (is_null($default_location))
                                        {!! Form::select('location_month', $locations, null, [
                                            'class' => 'form-control select2',
                                            'id' => 'location_month',
                                        ]) !!}
                                    @else
                                        {!! Form::select('location_month', $locations, null, [
                                            'class' => 'form-control select2',
                                            'id' => 'location_month',
                                            'disabled',
                                        ]) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="box-body">
                                <div id="peak_sales_hours_chart">
                                    <iframe id="frame_chart_month"
                                        src="{{ action('HomeController@getPeakSalesHoursByMonthChart', ['location_month' => $first_location]) }}"
                                        width="100%" height="400"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Peak sales hours in the fiscal year --}}
            @if (isset($dashboard_settings['peak_sales_hours_year']) && $dashboard_settings['peak_sales_hours_year'] == 1)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">{{ __('home.peak_sales_hours_year') }}</h3>
                                <div class="box-tools">
                                    <strong>@lang('accounting.location'): &nbsp;</strong>
                                    @if (is_null($default_location))
                                        {!! Form::select('location', $locations, null, ['class' => 'form-control select2', 'id' => 'location']) !!}
                                    @else
                                        {!! Form::select('location', $locations, null, [
                                            'class' => 'form-control select2',
                                            'id' => 'location',
                                            'disabled',
                                        ]) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="box-body">
                                <div id="peak_sales_hours_chart">
                                    <iframe id="frame_chart"
                                        src="{{ action('HomeController@getPeakSalesHoursChart', ['location' => $first_location]) }}"
                                        width="100%" height="400"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Purchases last 30 days --}}
            @if (isset($dashboard_settings['purchases_month']) && $dashboard_settings['purchases_month'] == 1)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">
                                    {{ __('home.purchases_last_30_days') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                {!! $purchases_chart_1->html() !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Purchases current financial year --}}
            @if (isset($dashboard_settings['purchases_year']) && $dashboard_settings['purchases_year'] == 1)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">
                                    {{ __('home.purchases_current_fy') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                {!! $purchases_chart_2->html() !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Stock last 30 days --}}
            @if (isset($dashboard_settings['stock_month']) && $dashboard_settings['stock_month'] == 1)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">
                                    {{ __('home.stock_last_30_days') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                {!! $stocks_chart_1->html() !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Stock current financial year --}}
            @if (isset($dashboard_settings['stock_year']) && $dashboard_settings['stock_year'] == 1)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">
                                    {{ __('home.stock_current_fy') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                {!! $stocks_chart_2->html() !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Choose month modal --}}
            @include('home.partials.choose_month_modal', ['months' => $months])
        </section>
        <!-- /.content -->

    @stop

    @section('javascript')
        <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
        <script src="{{ asset('plugins\chart\highchart\highcharts.js?v=' . $asset_v) }}"></script>
        <!-- {!! Charts::assets(['highcharts']) !!} -->

		@if (isset($dashboard_settings['sell_and_product']) && $dashboard_settings['sell_and_product'] == 1)
			{!! $sells_chart_line_1->script() !!}
        @endif

        @if (isset($dashboard_settings['sales_month']) && $dashboard_settings['sales_month'] == 1)
            {!! $sells_chart_1->script() !!}
        @endif

        @if (isset($dashboard_settings['sales_year']) && $dashboard_settings['sales_year'] == 1)
            {!! $sells_chart_2->script() !!}
        @endif

        @if (isset($dashboard_settings['purchases_month']) && $dashboard_settings['purchases_month'] == 1)
            {!! $purchases_chart_1->script() !!}
        @endif

        @if (isset($dashboard_settings['purchases_year']) && $dashboard_settings['purchases_year'] == 1)
            {!! $purchases_chart_2->script() !!}
        @endif

        @if (isset($dashboard_settings['stock_month']) && $dashboard_settings['stock_month'] == 1)
            {!! $stocks_chart_1->script() !!}
        @endif

        @if (isset($dashboard_settings['stock_year']) && $dashboard_settings['stock_year'] == 1)
            {!! $stocks_chart_2->script() !!}
        @endif
        <!-- <script src="jquery.min.js"></script> -->
        <!-- <script src="bootstrap/js/bootstrap.min.js"></script> -->
        <script>
            $(document).ready(function() {
                $('#location').change(function() {
                    let param = $(this).val();
                    let route = "{{ action('HomeController@getPeakSalesHoursChart') }}" + "?location=" + param;
                    $('#frame_chart').attr('src', route);
                    return false;
                });

                $('#location_month').change(function() {
                    let param = $(this).val();
                    let route = "{{ action('HomeController@getPeakSalesHoursByMonthChart') }}" +
                        "?location_month=" + param;
                    $('#frame_chart_month').attr('src', route);
                    return false;
                });

                // Date range picker
                $('#range-date-filter').daterangepicker(
                    dateRangeSettings,
                    function(startDate, endDate) {
                        $('#range-date-filter span').html(startDate.format(moment_date_format) + ' ~ ' + endDate
                            .format(moment_date_format));

                        let start = $('#range-date-filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        let end = $('#range-date-filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                        $('input[name="date-filter"]:checked').parent('label').removeClass('active');
                        $('input[name="date-filter"]:checked').attr('checked', false);
                    }
                );

                // On cancel of range-date-filter label
                $('#range-date-filter').on('cancel.daterangepicker', function(ev, picker) {
                    $('#range-date-filter').html('<span>' + LANG.date_range + '</span>');

                    let start = $('#range-date-filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    let end = $('#range-date-filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    $('input[name="date-filter"]:checked').parent('label').removeClass('active');
                    $('input[name="date-filter"]:checked').attr('checked', false);
                });

                // On apply of range-date-filter label
                $('#range-date-filter').on('apply.daterangepicker', function(ev, picker) {
                    $('#range-date-filter').html('<span>' + LANG.date_range + '</span>');

                    let start = $('#range-date-filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    let end = $('#range-date-filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    let location_id = $("#business_location_id").val();

                    $('input[name="date-filter"]:checked').parent('label').removeClass('active');
                    $('input[name="date-filter"]:checked').attr('checked', false);

                    $('#range-date-filter').addClass('active');
                    console.log('datepicker')

                    update_statistics(start, end, location_id);
                });

                let current_time = new Date();

                let range_picker_start = $('input#business-start-date').val();
                let range_picker_end = current_time.getFullYear() + '-12-31';

                $('#range-date-filter').data('daterangepicker').setStartDate(moment(range_picker_start, 'YYYY-MM-DD'));
                $('#range-date-filter').data('daterangepicker').setEndDate(moment(range_picker_end, 'YYYY-MM-DD'));
            });
        </script>
    @endif
@endsection
