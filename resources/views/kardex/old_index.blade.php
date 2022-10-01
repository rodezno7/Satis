@extends('layouts.app')
@section('title', __('kardex.kardex'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('kardex.kardex')
	</h1>
</section>
<!-- Main content -->
<section class="content">
	<div class="box">
		<div class="box-body">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">	
				{!! Form::open(['id'=>'form_kardex', 'action' => 'ReporterController@getKardexReport', 'method' => 'post', 'target' => '_blank']) !!}
				<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">	
				
				<div class="row">

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group">

							<label>@lang('product.clasification_product')</label>

							<select name="products" id="products" class="select2" style="width: 100%;">
								<option value="0">@lang('messages.please_select')</option>
								@foreach($products as $product)
								@if($product->product_type == 'variable')
								<option value="{{ $product->id }}">{{ $product->name_product }} {{ $product->name_variation }}</option>
								@else
								<option value="{{ $product->id }}">{{ $product->name_product }}</option>
								@endif
								@endforeach
							</select>
						</div>
					</div>

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group">
							<label>@lang('accounting.from')</label>
							<div class="wrap-inputform">
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
								{!! Form::date('from', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'from', 'class'=>'inputform2']) !!}
							</div>
						</div>
					</div>

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group">
							<label>@lang('accounting.to')</label>
							<div class="wrap-inputform">
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
								{!! Form::date('to', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'to', 'class'=>'inputform2']) !!}
							</div>
						</div>
					</div>


					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group">
							<label>@lang('kardex.warehouse')</label>
							<select name="warehouses" id="warehouses" class="select2" style="width: 100%;">
								<option value="0" selected>@lang('kardex.all')</option>
								@foreach($warehouses as $item)
								<option value="{{ $item->id }}">{{ $item->name }}</option>
								@endforeach
							</select>
						</div>
					</div>

					
				</div>

				<div class="row">

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group">
							<label>@lang('accounting.format')</label>
							<select name="type-format" id="type-format" class="select2" style="width: 100%;">
								<option value="pdf" selected>PDF</option>
								<option value="excel">Excel</option>
							</select>						
						</div>
					</div>



					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id="div_size">
						<div class="form-group">

							<label>@lang('kardex.font_size')</label>
							<select name="size" id="size" class="select2" style="width: 100%;">			
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10" selected>10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
							</select>
						</div>
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

	$("#type-format").change(function(){
		type = $("#type-format").val();
		if (type == 'pdf') {
			$("#div_size").show();
		} else {
			$("#div_size").hide();
		}
	});
	


</script>
@endsection