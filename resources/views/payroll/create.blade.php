{!! Form::open(['method' => 'post', 'id' => 'form_add_payroll' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('payroll.payroll')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>

<div class="modal-body">
  <div class="row">

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
			<label>@lang('payroll.payroll_type')</label> <span class="text-danger">*</span>
			<select name="payroll_type_id" id="payroll_type_id" class="form-control form-control-sm select2" 
				placeholder="{{ __('payroll.payroll_types') }}" style="width: 100%;">
				<option value="">{{ __('payroll.payroll_type') }}</option>
				@foreach ($payrollTypes as $payrollType)
					<option value="{{ $payrollType->id }}">{{ $payrollType->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('payroll.year')</label> <span class="text-danger">*</span>
		  {!! Form::text("year", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('payroll.year'), 'id' => 'year', 'required'])!!}
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_month">
		<div class="form-group">
		  <label>@lang('payroll.month')</label> <span class="text-danger">*</span>
		  <select name="month" id="month" class="form-control form-control-sm select2" 
				placeholder="{{ __('payroll.payment_periods') }}" style="width: 100%;">
				<option value="1">{{ __('payroll.january') }}</option>
				<option value="2">{{ __('payroll.febrary') }}</option>
				<option value="3">{{ __('payroll.march') }}</option>
				<option value="4">{{ __('payroll.april') }}</option>
				<option value="5">{{ __('payroll.may') }}</option>
				<option value="6">{{ __('payroll.june') }}</option>
				<option value="7">{{ __('payroll.july') }}</option>
				<option value="8">{{ __('payroll.august') }}</option>
				<option value="9">{{ __('payroll.september') }}</option>
				<option value="10">{{ __('payroll.october') }}</option>
				<option value="11">{{ __('payroll.november') }}</option>
				<option value="12">{{ __('payroll.december') }}</option>
			</select>
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_payment_period">
		<div class="form-group">
			<label>@lang('payroll.payment_period')</label> <span class="text-danger">*</span>
			<select name="payment_period_id" id="payment_period_id" class="form-control form-control-sm select2" 
				placeholder="{{ __('payroll.payment_periods') }}" style="width: 100%;">
				<option value="">{{ __('payroll.payment_period') }}</option>
				@foreach ($paymentPeriods as $paymentPeriod)
					<option value="{{ $paymentPeriod->id }}">{{ $paymentPeriod->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_start_date">
      <div class="form-group">
        <label>@lang('payroll.start_date')</label> <span class="text-danger">*</span>
        {!! Form::text("start_date", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('payroll.start_date'), 
        'id' => 'start_date', 'readonly' => true])!!}
      </div>
    </div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('payroll.end_date')</label> <span class="text-danger">*</span>
		  {!! Form::text("end_date", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('payroll.end_date'), 
		  'id' => 'end_date', 'readonly' => true])!!}
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_day" style="display: none">
		<div class="form-group">
			<label>@lang('payroll.days')</label> <span class="text-danger">*</span> @show_tooltip('Cantidad de días a calcular en la planilla.')
			{!! Form::number("days", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('payroll.days'), 
		  'id' => 'days'])!!}
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_isr" style="display: none">
		<div class="form-group">
			<label>@lang('payroll.ISR_apply')</label> <span class="text-danger">*</span> @show_tooltip('Debe seleccionar el periodo del descuento de renta a aplicar a la planilla')
			<select name="isr_id" id="isr_id" class="form-control form-control-sm select2" style="width: 100%;">
				<option value="">@lang('payroll.period_to_apply')</option>
				@foreach ($isrTables as $isrTable)
					<option value="{{ $isrTable->id }}">{{ $isrTable->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

  </div>
</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <button type="button" class="btn btn-info" id="btn_add_calculate_payroll">@lang('payroll.generate')</button>
  <button type="button" class="btn btn-primary" id="btn_add_payroll">@lang('payroll.create')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel'
    )</button>
</div>
{!! Form::close() !!}
<script src="{{ asset('js/payroll/payroll.js?v=' . $asset_v) }}"></script>