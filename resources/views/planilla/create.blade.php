{!! Form::open(['method' => 'post', 'id' => 'form_add_planilla' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('planilla.planilla')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>

<div class="modal-body">
  <div class="row">

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
			<label>@lang('planilla.type_planilla')</label> <span class="text-danger">*</span>
			<select name="type_planilla_id" id="type_planilla_id" class="form-control form-control-sm select2" 
				placeholder="{{ __('planilla.type_planillas') }}" style="width: 100%;">
				<option value="">{{ __('planilla.type_planilla') }}</option>
				@foreach ($typePlanillas as $typePlanilla)
					<option value="{{ $typePlanilla->id }}">{{ $typePlanilla->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('planilla.year')</label> <span class="text-danger">*</span>
		  {!! Form::text("year", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.year'), 'id' => 'year', 'required'])!!}
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('planilla.month')</label> <span class="text-danger">*</span>
		  <select name="month" id="month" class="form-control form-control-sm select2" 
				placeholder="{{ __('planilla.payment_periods') }}" style="width: 100%;">
				<option value="1">{{ __('planilla.january') }}</option>
				<option value="2">{{ __('planilla.febrary') }}</option>
				<option value="3">{{ __('planilla.march') }}</option>
				<option value="4">{{ __('planilla.april') }}</option>
				<option value="5">{{ __('planilla.may') }}</option>
				<option value="6">{{ __('planilla.june') }}</option>
				<option value="7">{{ __('planilla.july') }}</option>
				<option value="8">{{ __('planilla.august') }}</option>
				<option value="9">{{ __('planilla.september') }}</option>
				<option value="10">{{ __('planilla.october') }}</option>
				<option value="11">{{ __('planilla.november') }}</option>
				<option value="12">{{ __('planilla.december') }}</option>
			</select>
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
			<label>@lang('planilla.payment_period')</label> <span class="text-danger">*</span>
			<select name="payment_period_id" id="payment_period_id" class="form-control form-control-sm select2" 
				placeholder="{{ __('planilla.payment_periods') }}" style="width: 100%;">
				<option value="">{{ __('planilla.payment_period') }}</option>
				@foreach ($paymentPeriods as $paymentPeriod)
					<option value="{{ $paymentPeriod->id }}">{{ $paymentPeriod->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('planilla.start_date')</label> <span class="text-danger">*</span>
        {!! Form::text("start_date", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.start_date'), 
        'id' => 'start_date', 'readonly' => 'readonly'])!!}
      </div>
    </div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('planilla.end_date')</label> <span class="text-danger">*</span>
		  {!! Form::text("end_date", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.end_date'), 
		  'id' => 'end_date', 'readonly' => 'readonly'])!!}
		</div>
	</div>

  </div>
</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <button type="button" class="btn btn-info" id="btn_add_calculate_planilla">@lang('planilla.generate')</button>
  <button type="button" class="btn btn-primary" id="btn_add_planilla">@lang('planilla.create')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel'
    )</button>
</div>
{!! Form::close() !!}
<script src="{{ asset('js/payroll/payroll.js?v=' . $asset_v) }}"></script>