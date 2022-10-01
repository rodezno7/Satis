@extends('layouts.app')
@section('title', __('accounting.iva_books'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('accounting.iva_books')
	</h1>
</section>
<!-- Main content -->
<section class="content">
	<div class="box">
		<div class="box-body">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">	
				{!! Form::open(['id'=>'form_iva', 'action' => 'ReporterController@getIvaBooksReport', 'method' => 'post', 'target' => '_blank']) !!}
				<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">	
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none;">
						<div id="msj-errors" class="alert alert-danger alert-dismissible" role="alert">
							<strong id="msj"></strong>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.type')</label>
						<select name="type" id="type" class="select2" style="width: 100%;">
							<option value="0" disabled selected>@lang('messages.please_select')</option>
							<option value="sells">@lang('accounting.sells_final')</option>
							<option value="sells_taxpayer">@lang('accounting.sells_taxpayer')</option>
							<option value="sells_exports">@lang('accounting.sells_exports')</option>
							<option value="purchases">@lang('accounting.purchases')</option>
						</select>

					</div>
					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.from')</label>
						<div class="wrap-inputform">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('from', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'from', 'class'=>'inputform2']) !!}
						</div>
					</div>
					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.to')</label>
						<div class="wrap-inputform">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('to', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'to', 'class'=>'inputform2']) !!}
						</div>
					</div>

					<div id="div_location" class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12" style="display: none;">
						<div class="form-group">

							<label>@lang('accounting.location')</label>
							<select name="business-location-id" id="business-location-id" class="form-control select2" style="width: 100%;">
								<option value="0" selected>@lang('accounting.all')</option>
								@foreach($business_locations as $location)
								<option value="{{$location->id}}">{{$location->name}}</option>
								@endforeach
							</select>

						</div>
					</div>
				</div>

				<div class="row">



					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.format')</label>
						<select name="type-format" id="type-format" class="form-control select2">
							<option value="pdf">PDF</option>
							<option value="excel" selected>Excel</option>
						</select>						
					</div>

					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<label>@lang('accounting.size_font')</label>
						<select name="size" id="size" class="form-control select2">
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6" selected>6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
						</select>						
					</div>



					<div class="form-group float-left col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<input type="submit" class="btn btn-primary" value="@lang('accounting.generate')" style="margin-top: 20px;">
					</div>
				</div>


				{!! Form::close() !!}
			</div>



		</div>
	</div>


</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">

	$("#type").change(function(event) {
		type = $("#type").val();
		if(type != null)  {
			if(type == 'purchases') {
				$("#div_location").hide();
				$("#business-location-id").val(0).change();
			} else {
				$("#div_location").show();
			}
		}
	});


</script>
@endsection