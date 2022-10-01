@extends('layouts.app')
@section('title', __('home.home'))

@section('css')
    {!! Charts::styles(['highcharts']) !!}
@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header" style="margin-top: -20px;">
	<div class="row">
		<div class="col-md-12 col-md-offset-0 ">
			<img src="{{asset('img/envex-erp-banner.png')}}" class="img-responsive display-block" alt="EnvexERPBanner" style="height: 100%; width: 100%;">
		</div>
	</div>
</section>

@if(auth()->user()->can('dashboard.data'))
<!-- Main content -->
<section class="content no-print">
	{{-- Business start date --}}
	<input type="hidden" id="business-start-date" value="{{ $business->start_date }}">

	<div class="row">
		<div class="col-md-12 col-xs-12">
			<div class="btn-group pull-right" data-toggle="buttons">
				{{-- Location --}}
				<select name="business_location_id" class="btn btn-info" id="business_location_id" style="height: 34px;">			
					<option value="0" selected>{{ __('home.all_locations') }}</option>
					@foreach ($business_locations as $bl)							
						<option value="{{ $bl->id }}">{{ $bl->name }}</option>
					@endforeach
				</select>

				{{-- Today --}}
				<label class="btn btn-info active">
    				<input type="radio" name="date-filter"
    				data-start="{{ date('Y-m-d') }}" 
    				data-end="{{ date('Y-m-d') }}"
    				checked> {{ __('home.today') }}
  				</label>

				{{-- This week --}}
  				<label class="btn btn-info">
    				<input type="radio" name="date-filter"
    				data-start="{{ $date_filters['this_week']['start']}}" 
    				data-end="{{ $date_filters['this_week']['end']}}"
    				> {{ __('home.this_week') }}
  				</label>

				{{-- This month --}}
  				<label class="btn btn-info">
    				<input type="radio" name="date-filter"
    				data-start="{{ $date_filters['this_month']['start']}}" 
    				data-end="{{ $date_filters['this_month']['end']}}"
    				> {{ __('home.this_month') }}
  				</label>

				{{-- This fiscal year --}}
  				<label class="btn btn-info">
    				<input type="radio" name="date-filter" 
    				data-start="{{ $date_filters['this_fy']['start']}}" 
    				data-end="{{ $date_filters['this_fy']['end']}}" 
    				> {{ __('home.this_fy') }}
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
		{{-- Total purchase --}}
		<div class="col-md-3 col-sm-6 col-xs-12">
			<!-- info box -->
			<a href="{{action('PurchaseController@index')}}" class="small-box-footer" style="color: white" title="{{ __('lang_v1.more_information') }}">
				<div class="info-box bg-aqua">
					<div class="info-box-icon" style="width: 65px;">
						<i class="ion ion-bag"></i>
					</div>

					<div class="info-box-content" style="margin-left: 60px;">
						<span class="info-box-number total_purchase"></span>
						<span class="info-box-text">
							<h5>{{ __('home.total_purchase') }} <i class="fa fa-arrow-circle-right"></i></h5>
						</span>										
					</div>
				</div>
			</a>
		</div>
	    <!-- /.col -->

		{{-- Total sales --}}
	    <div class="col-md-3 col-sm-6 col-xs-12">
			<a href="{{action('SellController@index')}}" class="small-box-footer" style="color: white" title="{{ __('lang_v1.more_information') }}">
				<div class="info-box bg-green">
					<div class="info-box-icon" style="width: 65px;">
					<i class="ion ion-ios-cart-outline"></i>
					</div>

					<div class="info-box-content" style="margin-left: 60px;">
						<span class="info-box-number total_sell"></span>
						<span class="info-box-text">
							<h5>{{ __('home.total_sell') }} <i class="fa fa-arrow-circle-right"></i></h5>
						</span>										
					</div>
				</div>
			</a>
	    </div>
	    <!-- /.col -->

		{{-- Purchase due --}}
	    <div class="col-md-3 col-sm-6 col-xs-12">
			<div class="info-box bg-yellow">
				<div class="info-box-icon" style="width: 65px;">
				  <i class="fa fa-dollar"></i>
				</div>

				<div class="info-box-content" style="margin-left: 60px;">
					<span class="info-box-number purchase_due"></span>
					<span class="info-box-text">
						<h5>{{ __('home.purchase_due') }}</h5>	
						<i class="fa fa-arrow-circle-right"></i>					
					</span>										
				</div>				
			</div>
	    </div>

		{{-- Invoice due --}}
	    <div class="col-md-3 col-sm-6 col-xs-12">
			<div class="info-box bg-red">
				<div class="info-box-icon" style="width: 65px;">
				  	<i class="ion ion-ios-paper-outline"></i>
				</div>

				<div class="info-box-content" style="margin-left: 60px;">
					<span class="info-box-number invoice_due"></span>
					<span class="info-box-text">
						<h5>{{ __('home.invoice_due') }}</h5>
						<i class="fa fa-arrow-circle-right"></i>
					</span>										
				</div>			
			</div>
	      <!-- /.info-box -->
	    </div>
	    <!-- /.col -->
  	</div>
  	<br>
	

	  <div class="row">
		@if(session('business.enable_product_expiry') == 1)
		{{-- Expire alerts --}}
		<div class="col-md-3 col-sm-6 col-xs-12">	
			<a href="{{ config('app.business') == 'optics' ? action('Optics\ProductController@index') : action('ProductController@index') }}"
				class="small-box-footer" style="color: white"
				title="{{ __('lang_v1.more_information') }}">			
				<div class="info-box bg-olive">
					
					<div class="info-box-icon" style="width: 65px;">
						<i class="fa fa-calendar-times-o"></i>					
					</div>
					
					<div class="info-box-content" style="margin-left: 60px;">
						<span class="info-box-number expire_products"></span>
						<span class="info-box-text">
							<h6>{{ __('home.expire_products') }} <i class="fa fa-arrow-circle-right"></i></h6>
						</span>										
					</div>
				</div>
			</a>
		</div>
		  @endif
    	<!-- /.col -->

		{{-- Stock alerts --}}
	    <div class="col-md-3 col-sm-6 col-xs-12">
			<a href="{{ config('app.business') == 'optics' ? action('Optics\ProductController@index') : action('ProductController@index') }}"
				class="small-box-footer"
				style="color: white"
				title="{{ __('lang_v1.more_information') }}">
				<div class="info-box bg-blue">
					<div class="info-box-icon" style="width: 65px;">
						<i class="fa fa-dropbox"></i>					
					</div>
					
					<div class="info-box-content" style="margin-left: 60px;">
						<span class="info-box-number low_stock_products"></span>
						<span class="info-box-text">
							<h5>{{ __('home.stock_products') }} <i class="fa fa-arrow-circle-right"></i></h5>							
						</span>										
					</div>
				</div>
			</a>
	    </div>
	    <!-- /.col -->

		{{-- Total stock --}}
		<div class="col-md-3 col-sm-6 col-xs-12">	
			<a href="{{ config('app.business') == 'optics' ? action('Optics\ProductController@index') : action('ProductController@index') }}"
				class="small-box-footer"
				style="color: white"
				title="{{ __('lang_v1.more_information') }}">
				<div class="info-box bg-olive">
					<div class="info-box-icon" style="width: 65px;">
						<i class="fa fa-cube"></i>
					</div>

					<div class="info-box-content" style="margin-left: 60px;">
						<span class="info-box-number total_stock"></span>
						<span class="info-box-text">
							<h5>{{ __('home.total_stock') }} <i class="fa fa-arrow-circle-right"></i></h5>
						</span>
					</div>
				</div>
			</a>
		</div>

		{{-- Accounts receivable --}}
	    <div class="col-md-3 col-sm-6 col-xs-12">
			<a href="{{action('CustomerController@indexBalancesCustomer')}}" class="small-box-footer" style="color: white" title="{{ __('lang_v1.more_information') }}">	
				<div class="info-box bg-orange">

					<div class="info-box-icon" style="width: 65px; height: 95px;">
						<i class="fa fa-money"></i>
					</div>
					
					<div class="info-box-content" style="margin-left: 60px;">
						<span class="info-box-number sales_payment_dues"></span>
						<span class="info-box-text">
							<h6>{{ __('lang_v1.accounts_receivable') }}</h6>
							<h6>{{ __('lang_v1.close_to_expire') }} <i class="fa fa-arrow-circle-right"></i></h6>																							
						</span>																
					</div>
				</div>
			</a>
	    </div>

		{{-- Accounts payable --}}
	    <div class="col-md-3 col-sm-6 col-xs-12">
			<a href="{{action('CustomerController@indexBalancesCustomer')}}" class="small-box-footer" style="color: white" title="{{ __('lang_v1.more_information') }}">
				<div class="info-box bg-maroon">
					<div class="info-box-icon" style="width: 65px; height: 95px;">
						<i class="fa fa-money"></i>
					</div>
					
					<div class="info-box-content" style="margin-left: 60px;">
						<span class="info-box-number purchase_payment_dues"></span>
						<span class="info-box-text">
							<h6>{{ __('lang_v1.debs_to_pay') }}</h6>
							<h6>{{ __('lang_v1.close_to_expire') }} <i class="fa fa-arrow-circle-right"></i></h6>
						</span>																																																	
					</div>
				</div>	
			</a>						
	    </div>
	</div>

	<br>
	
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
								{!! Form::select("location_month", $locations, null,
									["class" => "form-control select2", "id" => "location_month"]) !!}
							@else
								{!! Form::select("location_month", $locations, null,
									["class" => "form-control select2", "id" => "location_month", 'disabled']) !!}
							@endif
						</div>
					</div>
					<div class="box-body">
						<div id="peak_sales_hours_chart">
							<iframe id="frame_chart_month" src="{{ action('HomeController@getPeakSalesHoursByMonthChart', ['location_month' => $first_location]) }}" width="100%" height="400"></iframe>
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
							{!! Form::select("location", $locations, null,
								["class" => "form-control select2", "id" => "location"]) !!}
							@else
							{!! Form::select("location", $locations, null,
								["class" => "form-control select2", "id" => "location", 'disabled']) !!}
							@endif
						</div>
					</div>
					<div class="box-body">
						<div id="peak_sales_hours_chart">
							<iframe id="frame_chart" src="{{ action('HomeController@getPeakSalesHoursChart', ['location' => $first_location]) }}" width="100%" height="400"></iframe>
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
				let route = "{{ action('HomeController@getPeakSalesHoursByMonthChart') }}" + "?location_month=" + param;
				$('#frame_chart_month').attr('src', route);
                return false;
			});

			// Date range picker
			$('#range-date-filter').daterangepicker(
				dateRangeSettings,
				function (startDate, endDate) {
					$('#range-date-filter span').html(startDate.format(moment_date_format) + ' ~ ' + endDate.format(moment_date_format));

					let start = $('#range-date-filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
					let end = $('#range-date-filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

					$('input[name="date-filter"]:checked').parent('label').removeClass('active');
					$('input[name="date-filter"]:checked').attr('checked', false);
				}
			);

			// On cancel of range-date-filter label
			$('#range-date-filter').on('cancel.daterangepicker', function (ev, picker) {
				$('#range-date-filter').html('<span>' + LANG.date_range + '</span>');

				let start = $('#range-date-filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
				let end = $('#range-date-filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

				$('input[name="date-filter"]:checked').parent('label').removeClass('active');
				$('input[name="date-filter"]:checked').attr('checked', false);
			});

			// On apply of range-date-filter label
			$('#range-date-filter').on('apply.daterangepicker', function (ev, picker) {
				$('#range-date-filter').html('<span>' + LANG.date_range + '</span>');

				let start = $('#range-date-filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
				let end = $('#range-date-filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
				let location_id = $("#business_location_id").val();

				$('input[name="date-filter"]:checked').parent('label').removeClass('active');
				$('input[name="date-filter"]:checked').attr('checked', false);

				$('#range-date-filter').addClass('active');

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

